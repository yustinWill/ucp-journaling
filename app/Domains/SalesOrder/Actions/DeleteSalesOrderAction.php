<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\Product\Services\ProductService;
use App\Services\StockMovementLogService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteSalesOrderAction
{
    public function execute(int $salesOrderId): array
    {
        return DB::transaction(function () use ($salesOrderId) {
            $salesOrder = $this->findSalesOrderById($salesOrderId);

            if (! $salesOrder) {
                throw new \Exception("Sales Order dengan ID {$salesOrderId} tidak ditemukan.");
            }

            return $this->deleteSalesOrder($salesOrder);
        });
    }

    public function executeByTransactionCode(string $transactionCode): array
    {
        return DB::transaction(function () use ($transactionCode) {
            $salesOrder = $this->findSalesOrderByTransactionCode($transactionCode);

            if (! $salesOrder) {
                throw new \Exception("Sales Order dengan nomor {$transactionCode} tidak ditemukan.");
            }

            return $this->deleteSalesOrder($salesOrder);
        });
    }

    public function executeManyByTransactionCodes(array $transactionCodes): array
    {
        return DB::transaction(function () use ($transactionCodes) {
            $results = [];

            foreach ($transactionCodes as $transactionCode) {
                $salesOrder = $this->findSalesOrderByTransactionCode($transactionCode);

                if (! $salesOrder) {
                    throw new \Exception("Sales Order dengan nomor {$transactionCode} tidak ditemukan.");
                }

                $results[] = $this->deleteSalesOrder($salesOrder);
            }

            return $results;
        });
    }

    private function findSalesOrderById(int $salesOrderId)
    {
        $companyCode = $this->companyCode();

        $query = DB::table('t_sales_orders')
            ->where('id', $salesOrderId)
            ->where('company_code', $companyCode);

        return $query->lockForUpdate()->first();
    }

    private function findSalesOrderByTransactionCode(string $transactionCode)
    {
        $companyCode = $this->companyCode();

        $query = DB::table('t_sales_orders')
            ->where('transaction_code', $transactionCode)
            ->where('company_code', $companyCode);

        return $query->lockForUpdate()->first();
    }

    private function companyCode(): string
    {
        $companyCode = session('user_company_code');

        if (! $companyCode) {
            throw new AuthorizationException('Company context is required to revert a Sales Order.');
        }

        return (string) $companyCode;
    }

    private function deleteSalesOrder(object $salesOrder): array
    {
        $summary = [
            'sales_order_id' => $salesOrder->id,
            'transaction_code' => $salesOrder->transaction_code,
            'sales_invoices_deleted' => 0,
            'sales_deliveries_deleted' => 0,
            'stock_movements_reversed' => 0,
            'stock_batches_restored' => 0,
            'topup_allocations_deleted' => 0,
            'revenue_splits_deleted' => 0,
        ];

        $invoiceIds = DB::table('t_sales_invoices')
            ->where('f_sales_order_id', $salesOrder->id)
            ->pluck('id');

        $this->ensureNoSalesReturns($invoiceIds, $salesOrder->transaction_code);
        $this->deleteSalesInvoices($invoiceIds, $summary);

        $salesDeliveries = DB::table('t_sales_deliveries')
            ->where('sales_order_id', $salesOrder->id)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($salesDeliveries as $delivery) {
            if ($delivery->is_confirmed === '1') {
                $this->revertConfirmedSalesDelivery($delivery, $summary);
            }

            $this->deleteSalesDelivery($delivery->id);
            $summary['sales_deliveries_deleted']++;
        }

        $this->revertTopupAllocations($salesOrder->id, $summary);
        $this->deleteRevenueSplits($salesOrder->id, $summary);

        DB::table('t_sales_order_products')
            ->where('f_sales_order_id', $salesOrder->id)
            ->delete();

        DB::table('t_sales_order_audit_logs')
            ->where('f_sales_order_id', $salesOrder->id)
            ->delete();

        DB::table('t_sales_orders')
            ->where('id', $salesOrder->id)
            ->delete();

        Log::info("Sales Order {$salesOrder->transaction_code} deleted with related SI/SD cleanup.", $summary);

        return $summary;
    }

    private function ensureNoSalesReturns(Collection $invoiceIds, string $transactionCode): void
    {
        if ($invoiceIds->isEmpty()) {
            return;
        }

        $returnCodes = DB::table('t_return_managements')
            ->where('return_type', 'SALES_RETURN')
            ->whereIn('f_source_invoice_id', $invoiceIds->all())
            ->pluck('transaction_code');

        if ($returnCodes->isNotEmpty()) {
            throw new \Exception(
                'Sales Order '.$transactionCode.' memiliki Sales Return: '.
                $returnCodes->implode(', ').
                '. Hapus atau reversal return terlebih dahulu sebelum menghapus SO.'
            );
        }
    }

    private function deleteSalesInvoices(Collection $invoiceIds, array &$summary): void
    {
        if ($invoiceIds->isEmpty()) {
            return;
        }

        $invoiceIdValues = $invoiceIds->all();

        DB::table('t_sales_invoice_payment')
            ->whereIn('sales_invoice_id', array_map('strval', $invoiceIdValues))
            ->delete();

        DB::table('t_sales_invoice_products')
            ->whereIn('f_sales_invoice_id', $invoiceIdValues)
            ->delete();

        DB::table('t_sales_invoice_audit_logs')
            ->whereIn('f_sales_invoice_id', $invoiceIdValues)
            ->delete();

        $summary['sales_invoices_deleted'] = DB::table('t_sales_invoices')
            ->whereIn('id', $invoiceIdValues)
            ->delete();
    }

    private function revertConfirmedSalesDelivery(object $delivery, array &$summary): void
    {
        $deliveryProducts = DB::table('t_sales_delivery_products')
            ->where('sales_delivery_id', $delivery->id)
            ->orderBy('id')
            ->get();

        $affectedProductIds = [];

        foreach ($deliveryProducts as $product) {
            $deliveryQty = $this->getDeliveryQuantity($product);

            if (! $product->product_id || $deliveryQty <= 0) {
                continue;
            }

            $qtyInBaseUnit = $this->convertDeliveryQtyToBaseUnit(
                (int) $product->product_id,
                $product->f_unit_id ? (int) $product->f_unit_id : null,
                $deliveryQty
            );

            app(StockMovementLogService::class)->recordMovement(
                (int) $delivery->warehouse_id,
                (int) $product->product_id,
                'RETURN_IN',
                'SALES_DELIVERY_REVERSAL',
                $delivery->delivery_number,
                $qtyInBaseUnit,
                'Sales Delivery reversal before deleting Sales Order: '.$delivery->delivery_number,
                $delivery->delivery_date ?? now()
            );

            $this->restoreStockBatchConsumption((int) $product->product_id, (int) $delivery->id, $summary);

            $affectedProductIds[] = (int) $product->product_id;
            $summary['stock_movements_reversed']++;
        }

        foreach (array_unique($affectedProductIds) as $productId) {
            app(ProductService::class)->updateStock($productId);
        }
    }

    private function getDeliveryQuantity(object $deliveryProduct): float
    {
        $qty = (float) ($deliveryProduct->qty ?? 0);

        if ($qty > 0) {
            return $qty;
        }

        return (float) ($deliveryProduct->quantity_delivered ?? 0);
    }

    private function convertDeliveryQtyToBaseUnit(int $productId, ?int $unitId, float $quantity): float
    {
        if (! $unitId) {
            return $quantity;
        }

        $productUnit = DB::table('m_product_units')
            ->where('f_product_id', $productId)
            ->where('f_unit_id', $unitId)
            ->select('conversion_factor', 'is_default')
            ->first();

        if (! $productUnit || $productUnit->is_default) {
            return $quantity;
        }

        return $quantity * (float) ($productUnit->conversion_factor ?? 1);
    }

    private function restoreStockBatchConsumption(int $productId, int $salesDeliveryId, array &$summary): void
    {
        $batchTransactions = DB::table('t_product_batch_transactions')
            ->where('f_product_id', $productId)
            ->where('source_type', 'SALES_DELIVERY')
            ->where('f_source_transaction_id', $salesDeliveryId)
            ->where('transaction_type', 'OUT')
            ->get();

        foreach ($batchTransactions->groupBy('f_product_batch_id') as $batchId => $transactions) {
            $qtyToRestore = (float) $transactions->sum('qty_moved');

            DB::table('t_product_stock_batches')
                ->where('id', $batchId)
                ->update([
                    'remaining_qty' => DB::raw('remaining_qty + '.$qtyToRestore),
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            $summary['stock_batches_restored']++;
        }

        DB::table('t_product_batch_transactions')
            ->where('f_product_id', $productId)
            ->where('source_type', 'SALES_DELIVERY')
            ->where('f_source_transaction_id', $salesDeliveryId)
            ->where('transaction_type', 'OUT')
            ->delete();
    }

    private function deleteSalesDelivery(int $salesDeliveryId): void
    {
        DB::table('t_sales_delivery_products')
            ->where('sales_delivery_id', $salesDeliveryId)
            ->delete();

        DB::table('t_sales_delivery_audit_logs')
            ->where('f_sales_delivery_id', $salesDeliveryId)
            ->delete();

        DB::table('t_sales_deliveries')
            ->where('id', $salesDeliveryId)
            ->delete();
    }

    private function revertTopupAllocations(int $salesOrderId, array &$summary): void
    {
        $allocations = DB::table('t_topup_sales_order')
            ->where('sales_order_id', $salesOrderId)
            ->get();

        if ($allocations->isEmpty()) {
            return;
        }

        foreach ($allocations->groupBy('topup_id') as $topupId => $topupAllocations) {
            $amountToRestore = (float) $topupAllocations->sum('allocated_amount');
            $topup = DB::table('t_topup')
                ->where('id', $topupId)
                ->lockForUpdate()
                ->first();

            if (! $topup) {
                continue;
            }

            $allocatedAmount = max(0, (float) $topup->allocated_amount - $amountToRestore);
            $remainingBalance = min(
                (float) $topup->topup_amount,
                (float) $topup->remaining_balance + $amountToRestore
            );

            $status = 'PARTIAL';
            if ($allocatedAmount <= 0) {
                $status = 'OPEN';
            } elseif ($remainingBalance <= 0) {
                $status = 'PAID';
            }

            DB::table('t_topup')
                ->where('id', $topupId)
                ->update([
                    'allocated_amount' => $allocatedAmount,
                    'remaining_balance' => $remainingBalance,
                    'status' => $status,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);
        }

        $summary['topup_allocations_deleted'] = DB::table('t_topup_sales_order')
            ->where('sales_order_id', $salesOrderId)
            ->delete();
    }

    private function deleteRevenueSplits(int $salesOrderId, array &$summary): void
    {
        $summary['revenue_splits_deleted'] = DB::table('t_sales_order_revenue_splits')
            ->where('f_sales_order_id', $salesOrderId)
            ->delete();
    }
}
