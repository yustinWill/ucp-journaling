<?php

namespace App\Domains\Warehouse\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\StockAdjustment\Services\StockAdjustmentAuditLogService;
use App\Domains\StockAdjustment\Services\StockAdjustmentService;
use App\Domains\StockOpname\Services\StockOpnameService;
use App\Services\BatchManagementService;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateStockTransferAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // Create main stock adjustment record
            $adjustment_type = 'WAREHOUSE_TRANSFER';

            // Use authenticated user if pic_id is not provided or invalid
            $picId = $data['pic_id'] ?? Auth::id();

            $stockAdjustmentId = app(StockAdjustmentService::class)->create([
                'adjustment_date' => $data['adjustment_date'],
                'adjustment_type' => $adjustment_type,
                'warehouse_id' => $data['warehouse_id'],
                'pic_id' => $picId,
                'status' => 'APPROVED',
                'description' => $data['description'],
                'memo' => $data['memo'] ?? null,
                'f_tag_codes' => $data['tags'] ?? [],
                'reference_number' => $data['reference_number'] ?? null,
                'total_items' => count($data['products'] ?? []),
                'total_adjustment_qty' => $this->calculateTotalQty($data['products'] ?? []),
                'total_adjustment_value' => $this->calculateTotalValue($data['products'] ?? []),
                'source_reference_id' => null,
            ]);

            // Create adjustment items, update stock, and handle batches
            if (! empty($data['products'])) {
                $temp_warehouse = $data['warehouse_id'];
                //Process Incremental
                $data['warehouse_id'] = $data['warehouse_dest_id'];
                $this->processStockAdjustments($stockAdjustmentId, $data['products'], $data);
                $this->createAdjustmentItems($stockAdjustmentId, $data['products'], $data);
                $this->createStockMovementLogs($stockAdjustmentId, $data['products'], $data);

                // Process Decremental

                $data['warehouse_id'] = $temp_warehouse;
                foreach ($data['products'] as &$row) {
                    $row['adjustment_qty'] = -1 * $row['adjustment_qty'];
                }

                $this->processStockAdjustments($stockAdjustmentId, $data['products'], $data);
                $this->createAdjustmentItems($stockAdjustmentId, $data['products'], $data);
                $this->createStockMovementLogs($stockAdjustmentId, $data['products'], $data);

                // Recalculate total_stock from movement logs for all affected products
                foreach ($data['products'] as $product) {
                    if ($product['adjustment_qty'] != 0) {
                        app(ProductService::class)->updateStock($product['product_id']);
                    }
                }
            }

            // Create audit log
            app(StockAdjustmentAuditLogService::class)->create(
                $stockAdjustmentId,
                'CREATE',
                'Stock adjustment created',
                [
                    'adjustment_type' => $adjustment_type,
                    'warehouse_id' => $data['warehouse_id'],
                    'total_items' => count($data['products'] ?? []),
                    'status' => $data['status'] ?? 'DRAFT',
                ]
            );

            return $stockAdjustmentId;
        });
    }

    private function calculateTotalQty(array $products): float
    {
        return array_sum(array_column($products, 'adjustment_qty'));
    }

    private function calculateTotalValue(array $products): float
    {
        $totalValue = 0;
        foreach ($products as $product) {
            $totalValue += ($product['adjustment_qty'] * ($product['unit_cost'] ?? 0));
        }

        return $totalValue;
    }

    private function processStockAdjustments(int $stockAdjustmentId, array $products, array $mainData): void
    {
        foreach ($products as $product) {
            if ($product['adjustment_qty'] != 0) {
                $productId = $product['product_id'];
                $adjustmentQty = $product['adjustment_qty'];

                if ($adjustmentQty > 0) {
                    // Positive adjustment - create new batch
                    app(BatchManagementService::class)->createBatch([
                        'f_product_id' => $productId,
                        'source_type' => 'STOCK_ADJUSTMENT',
                        'f_source_transaction_id' => $stockAdjustmentId,
                        'batch_date' => $mainData['adjustment_date'],
                        'quantity' => $adjustmentQty,
                        'purchase_price' => $product['unit_cost'] ?? 0,
                        'hpp_status' => 'SETTLED',
                    ]);
                } else {
                    // Negative adjustment - consume batches (FIFO)
                    app(BatchManagementService::class)->consumeBatches($productId, abs($adjustmentQty), [
                        'source_type' => 'STOCK_ADJUSTMENT',
                        'f_source_transaction_id' => $stockAdjustmentId,
                        'reference_code' => 'ADJ-'.$stockAdjustmentId,
                        'notes' => 'Stock consumed by Stock Adjustment',
                    ]);
                }
            }
        }
    }

    // Batch management methods removed - now using global BatchManagementService

    private function createAdjustmentItems(int $stockAdjustmentId, array $products, array $mainData): void
    {
        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'company_code' => session('user_company_code'),
                'stock_adjustment_id' => $stockAdjustmentId,
                'product_id' => $product['product_id'],
                'product_code' => $product['product_code'],
                'product_name' => $product['product_name'],
                'unit' => $product['unit'],
                'stock_before' => $product['stock_before'],
                'adjustment_qty' => $product['adjustment_qty'],
                'stock_after' => $product['stock_before'] + $product['adjustment_qty'],
                'unit_cost' => $product['unit_cost'] ?? 0,
                'total_value' => ($product['adjustment_qty'] * ($product['unit_cost'] ?? 0)),
                'reason' => $product['reason'] ?? null,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];
        }

        DB::table('t_stock_adjustment_items')->insert($items);
    }

    private function createStockMovementLogs(int $stockAdjustmentId, array $products, array $mainData): void
    {
        // Get warehouse and adjustment details
        $warehouse = DB::table('m_warehouses')->where('id', $mainData['warehouse_id'])->first();
        $adjustment = DB::table('t_stock_adjustments')->where('id', $stockAdjustmentId)->first();

        $movements = [];
        foreach ($products as $product) {
            if ($product['adjustment_qty'] != 0) {
                $rawAdjustmentQty = $product['adjustment_qty'];
                $adjustmentQty = abs($rawAdjustmentQty);

                $movementType = $rawAdjustmentQty > 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';
                if ($movementType === 'ADJUSTMENT_IN') {
                    $stockBefore = db::table('t_stock_movement_logs')
                        ->where('product_id', $product['product_id'])
                        ->where('warehouse_id', $mainData['warehouse_id'])
                        ->orderBy('created_at', 'desc')
                        ->value('stock_after') ?? 0;
                    $product['stock_before'] = $stockBefore;
                }
                $movements[] = [
                    'movement_date' => $mainData['adjustment_date'],
                    'movement_type' => $movementType,
                    'reference_type' => 'STOCK_TRANSFER',
                    'reference_number' => $adjustment->adjustment_number,
                    'warehouse_id' => $mainData['warehouse_id'],
                    'warehouse_code' => $warehouse->code,
                    'warehouse_name' => $warehouse->name,
                    'product_id' => $product['product_id'],
                    'product_code' => $product['product_code'],
                    'product_name' => $product['product_name'],
                    'unit' => $product['unit'],
                    'stock_before' => $product['stock_before'],
                    'movement_qty' => $adjustmentQty,
                    'stock_after' => $product['stock_before'] + $rawAdjustmentQty,
                    'unit_cost' => $product['unit_cost'] ?? 0,
                    'total_value' => ($adjustmentQty * ($product['unit_cost'] ?? 0)),
                    'notes' => $product['reason'] ?? $mainData['description'],
                ];
            }
        }

        if (! empty($movements)) {
            app(StockMovementLogService::class)->createBulk($movements);
        }
    }
}
