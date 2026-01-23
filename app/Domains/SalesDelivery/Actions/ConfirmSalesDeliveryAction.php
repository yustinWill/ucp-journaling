<?php

namespace App\Domains\SalesDelivery\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Enums\ActionTypes;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfirmSalesDeliveryAction
{
    /**
     * Confirm sales delivery and process stock movement
     *
     * @param int $salesDeliveryId
     * @return void
     */
    public function execute(int $salesDeliveryId)
    {
        return DB::transaction(function () use ($salesDeliveryId) {
            // Get sales delivery data
            $salesDelivery = app(SalesDeliveryService::class)->findOne($salesDeliveryId);

            if (!$salesDelivery) {
                throw new \Exception("Sales Delivery dengan ID {$salesDeliveryId} tidak ditemukan.");
            }

            // Check if already confirmed
            if ($salesDelivery->is_confirmed === '1') {
                throw new \Exception("Sales Delivery ini sudah dikonfirmasi sebelumnya.");
            }

            // Get delivery products
            $deliveryProducts = app(SalesDeliveryService::class)->findProductLines($salesDeliveryId);

            // Process products for stock movement
            foreach ($deliveryProducts as $product) {
                if (!isset($product->product_id) || !isset($product->qty) || $product->qty <= 0) {
                    continue;
                }

                $productId = $product->product_id;
                $deliveryQty = $product->qty;
                $warehouseId = $salesDelivery->warehouse_id;
                $unitId = $product->f_unit_id ?? null;

                // Convert delivery quantity to base unit for stock operations
                // If unit_id is provided, check if it's the default unit
                $deliveryQtyInBaseUnit = $deliveryQty;

                if ($unitId) {
                    $productUnit = DB::table('m_product_units as pu')
                        ->where('pu.f_product_id', $productId)
                        ->where('pu.f_unit_id', $unitId)
                        ->select('pu.conversion_factor', 'pu.is_default')
                        ->first();

                    if ($productUnit && !$productUnit->is_default) {
                        // Not default unit, convert to base unit
                        // Example: 10 BOX * 100 (conversion factor) = 1000 KG (base unit)
                        $conversionFactor = $productUnit->conversion_factor ?? 1;
                        $deliveryQtyInBaseUnit = $deliveryQty * $conversionFactor;
                    }
                }

                // Record stock movement (OUT) - use base unit quantity
                app(StockMovementLogService::class)->recordMovement(
                    $warehouseId,
                    $productId,
                    'SALES_OUT',
                    'SALES_DELIVERY',
                    $salesDelivery->delivery_number ?? 'Unknown',
                    $deliveryQtyInBaseUnit,
                    'Sales Delivery Confirmed: ' . ($salesDelivery->delivery_number ?? 'Unknown'),
                    $salesDelivery->delivery_date ?? now()
                );

                // Update product stock (decrease) - use base unit quantity
                app(ProductService::class)->updateStock($productId, '-', $deliveryQtyInBaseUnit);

                // Update delivered quantities in sales order - use original quantity (in sales unit)
                if ($salesDelivery->sales_order_id && $product->sales_order_product_id) {
                    app(SalesOrderService::class)->updateDeliveredQuantities(
                        $salesDelivery->sales_order_id,
                        $product->sales_order_product_id,
                        $deliveryQty
                    );
                }

                // Consume stock batches (FIFO) - use base unit quantity
                $this->consumeStockBatches($productId, $deliveryQtyInBaseUnit, $salesDeliveryId);
            }

            // Update sales delivery status to CONFIRMED
            DB::table('t_sales_deliveries')
                ->where('id', $salesDeliveryId)
                ->update([
                    'is_confirmed' => '1',
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                    'confirm_at' => now(),
                    'confirmed_by' => Auth::id(),
                ]);

            // Create audit log
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Konfirmasi Sales Delivery - Stock Movement Processed',
                'metadata' => json_encode([
                    'before' => ['status' => $salesDelivery->status],
                    'after' => ['status' => 'CONFIRMED'],
                    'confirm_at' => now(),
                    'confirmed_by' => Auth::id(),
                ]),
            ];
            app(SalesDeliveryAuditLogService::class)->create($log_data, $salesDeliveryId);

            return $salesDeliveryId;
        });
    }

    /**
     * Consume stock batches using FIFO method
     */
    private function consumeStockBatches(int $productId, float $quantityToConsume, int $salesDeliveryId)
    {
        // Get available batches ordered by date (FIFO)
        $batches = DB::table('t_product_stock_batches')
            ->where('f_product_id', $productId)
            ->where('remaining_qty', '>', 0)
            ->where('status', 'ACTIVE')
            ->orderBy('batch_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $remainingToConsume = $quantityToConsume;

        foreach ($batches as $batch) {
            if ($remainingToConsume <= 0) {
                break;
            }

            // Ensure batch has required properties
            if (!isset($batch->id) || !isset($batch->remaining_qty)) {
                continue;
            }

            $availableQty = $batch->remaining_qty;
            $consumeFromThisBatch = min($remainingToConsume, $availableQty);

            // Update batch remaining quantity
            DB::table('t_product_stock_batches')
                ->where('id', $batch->id)
                ->update([
                    'remaining_qty' => $availableQty - $consumeFromThisBatch,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            // Record batch consumption transaction
            DB::table('t_product_batch_transactions')->insert([
                'company_code' => session('user_company_code'),
                'f_product_batch_id' => $batch->id,
                'f_product_id' => $productId,
                'transaction_type' => 'OUT',
                'source_type' => 'SALES_DELIVERY',
                'f_source_transaction_id' => $salesDeliveryId,
                'transaction_date' => now(),
                'qty_moved' => $consumeFromThisBatch,
                'reference_code' => 'SD-' . $salesDeliveryId,
                'notes' => 'Stock consumed by Sales Delivery Confirmation',
                'created_at' => now(),
                'created_by' => Auth::id(),
            ]);

            $remainingToConsume -= $consumeFromThisBatch;
        }

        // Log warning if we couldn't consume all required quantity
        if ($remainingToConsume > 0) {
            Log::warning("Insufficient stock batches for product {$productId}. Could not consume {$remainingToConsume} units.");
        }
    }
}
