<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Enums\ActionTypes;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteSalesOrderAction
{
    /**
     * Delete sales order and revert all stock movements
     *
     * @param int $salesOrderId
     * @return void
     */
    public function execute(int $salesOrderId)
    {
        return DB::transaction(function () use ($salesOrderId) {
            // Get sales order data
            $salesOrder = app(SalesOrderService::class)->findOne($salesOrderId);

            if (!$salesOrder) {
                throw new \Exception("Sales Order dengan ID {$salesOrderId} tidak ditemukan.");
            }

            // Check if sales order has invoices
            $invoiceCount = DB::table('t_sales_invoices')
                ->where('f_sales_order_id', $salesOrderId)
                ->count();

            if ($invoiceCount > 0) {
                throw new \Exception("Sales Order tidak dapat dihapus karena sudah memiliki invoice. Hapus invoice terlebih dahulu.");
            }

            // Get all sales deliveries for this sales order
            $salesDeliveries = DB::table('t_sales_deliveries')
                ->where('f_sales_order_id', $salesOrderId)
                ->get();

            // Process each sales delivery
            foreach ($salesDeliveries as $delivery) {
                // If delivery was confirmed, revert stock movements
                if ($delivery->is_confirmed == '1') {
                    $this->revertSalesDeliveryStockMovements($delivery->id, $delivery->delivery_number);
                }

                // Delete sales delivery products
                DB::table('t_sales_delivery_products')
                    ->where('f_sales_delivery_id', $delivery->id)
                    ->delete();

                // Delete sales delivery audit logs
                DB::table('t_sales_delivery_audit_logs')
                    ->where('f_sales_delivery_id', $delivery->id)
                    ->delete();

                // Delete sales delivery
                DB::table('t_sales_deliveries')
                    ->where('id', $delivery->id)
                    ->delete();
            }

            // Create audit log before deletion
            $log_data = [
                'action_type' => ActionTypes::DELETE,
                'description' => 'Sales Order Deleted - All stock movements reverted',
                'metadata' => json_encode([
                    'sales_order' => $salesOrder,
                    'deleted_at' => now(),
                    'deleted_by' => Auth::id(),
                ]),
            ];
            app(SalesOrderAuditLogService::class)->create($salesOrderId, $log_data['action_type'], [
                'description' => $log_data['description'],
                'before' => $salesOrder,
                'after' => null,
            ]);

            // Delete sales order products
            DB::table('t_sales_order_products')
                ->where('f_sales_order_id', $salesOrderId)
                ->delete();

            // Delete sales order audit logs
            DB::table('t_sales_order_audit_logs')
                ->where('f_sales_order_id', $salesOrderId)
                ->delete();

            // Delete sales order
            DB::table('t_sales_orders')
                ->where('id', $salesOrderId)
                ->delete();

            Log::info("Sales Order {$salesOrderId} deleted successfully with stock movements reverted.");

            return true;
        });
    }

    /**
     * Revert stock movements from confirmed sales delivery
     *
     * @param int $salesDeliveryId
     * @param string $deliveryNumber
     * @return void
     */
    private function revertSalesDeliveryStockMovements(int $salesDeliveryId, string $deliveryNumber)
    {
        // Get delivery details
        $delivery = DB::table('t_sales_deliveries')
            ->where('id', $salesDeliveryId)
            ->first();

        if (!$delivery) {
            return;
        }

        // Get delivery products
        $deliveryProducts = DB::table('t_sales_delivery_products')
            ->where('f_sales_delivery_id', $salesDeliveryId)
            ->get();

        foreach ($deliveryProducts as $product) {
            if (!isset($product->f_product_id) || !isset($product->quantity_delivered) || $product->quantity_delivered <= 0) {
                continue;
            }

            $productId = $product->f_product_id;
            $deliveryQty = $product->quantity_delivered;
            $warehouseId = $delivery->f_warehouse_id;

            // Revert product stock (increase back)
            app(ProductService::class)->updateStock($productId, '+', $deliveryQty);

            // Record reversal stock movement (IN to cancel the OUT)
            app(StockMovementLogService::class)->recordMovement(
                $warehouseId,
                $productId,
                'RETURN_IN',
                'SALES_DELIVERY_REVERSAL',
                $deliveryNumber,
                $deliveryQty,
                'Sales Delivery Reversal: Stock returned due to Sales Order deletion',
                $delivery->delivery_date ?? now()
            );

            // Revert stock batch consumption
            $this->revertStockBatchConsumption($productId, $deliveryQty, $salesDeliveryId);
        }

        // Delete original stock movement logs for this delivery
        DB::table('t_stock_movement_logs')
            ->where('reference_type', 'SALES_DELIVERY')
            ->where('reference_number', $deliveryNumber)
            ->where('movement_type', 'SALES_OUT')
            ->delete();
    }

    /**
     * Revert stock batch consumption (restore consumed batches)
     *
     * @param int $productId
     * @param float $quantityToRestore
     * @param int $salesDeliveryId
     * @return void
     */
    private function revertStockBatchConsumption(int $productId, float $quantityToRestore, int $salesDeliveryId)
    {
        // Get batch consumption transactions for this delivery (in reverse order - LIFO for reversal)
        $batchTransactions = DB::table('t_product_batch_transactions')
            ->where('f_product_id', $productId)
            ->where('source_type', 'SALES_DELIVERY')
            ->where('f_source_transaction_id', $salesDeliveryId)
            ->where('transaction_type', 'OUT')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($batchTransactions as $transaction) {
            $batchId = $transaction->f_product_batch_id;
            $qtyToRestore = $transaction->qty_moved;

            // Restore the batch quantity
            DB::table('t_product_stock_batches')
                ->where('id', $batchId)
                ->increment('remaining_qty', $qtyToRestore, [
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            // Create reversal transaction record (IN)
            DB::table('t_product_batch_transactions')->insert([
                'company_code' => session('user_company_code'),
                'f_product_batch_id' => $batchId,
                'f_product_id' => $productId,
                'transaction_type' => 'IN',
                'source_type' => 'SALES_DELIVERY_REVERSAL',
                'f_source_transaction_id' => $salesDeliveryId,
                'transaction_date' => now(),
                'qty_moved' => $qtyToRestore,
                'reference_code' => 'SD-REV-' . $salesDeliveryId,
                'notes' => 'Stock batch restored due to Sales Order deletion',
                'created_at' => now(),
                'created_by' => Auth::id(),
            ]);
        }

        // Delete original consumption transactions
        DB::table('t_product_batch_transactions')
            ->where('f_product_id', $productId)
            ->where('source_type', 'SALES_DELIVERY')
            ->where('f_source_transaction_id', $salesDeliveryId)
            ->where('transaction_type', 'OUT')
            ->delete();
    }
}
