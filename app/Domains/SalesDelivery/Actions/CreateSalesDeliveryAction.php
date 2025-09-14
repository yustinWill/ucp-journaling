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

class CreateSalesDeliveryAction
{
    public function execute(array $data)
    {
        // Handle file upload
        if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
            $path = $data['attachment']->store('sales_delivery', 'public');
            $data['attachment'] = $path;
        }
        return DB::transaction(function () use ($data) {
            $sales_delivery_id = app(SalesDeliveryService::class)->create($data);

            app(SalesDeliveryService::class)->createDeliveryProducts($sales_delivery_id, $data['products']);

            // Process products for stock movement
            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $product) {
                    if (! isset($product['product_id']) || ! isset($product['delivery_qty']) || $product['delivery_qty'] <= 0) {
                        continue;
                    }

                    $productId = $product['product_id'];
                    $deliveryQty = $product['delivery_qty'];
                    $warehouseId = $data['warehouse_id'];

                    // Update product stock (decrease)
                    app(ProductService::class)->updateStock($productId, '-', $deliveryQty);

                    // Record stock movement (OUT)
                    app(StockMovementLogService::class)->recordMovement(
                        $warehouseId,
                        $productId,
                        'SALES_OUT',
                        'SALES_DELIVERY',
                        $data['delivery_number'] ?? 'Unknown',
                        $deliveryQty,
                        'Sales Delivery: ' . ($data['delivery_number'] ?? 'Unknown'),
                        $data['delivery_date'] ?? now()
                    );

                    app(SalesOrderService::class)->updateDeliveredQuantities($data['sales_order_id'], $product['sales_order_product_id'], $deliveryQty);

                    // Consume stock batches (FIFO)
                    $this->consumeStockBatches($productId, $deliveryQty, $sales_delivery_id);
                }
            }

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan Sales Delivery',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(SalesDeliveryAuditLogService::class)->create($log_data, $sales_delivery_id);

            return $sales_delivery_id;
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
            if (! isset($batch->id) || ! isset($batch->remaining_qty)) {
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
                'notes' => 'Stock consumed by Sales Delivery',
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
