<?php

namespace App\Domains\Production\Services;

use App\Domains\Product\Services\ProductService;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionStockService
{
    private $movement_table = 't_stock_movement_logs';

    private $batch_table = 't_product_stock_batches';

    public function createProductionStockMovements(int $productionId, array $items, array $productionData): array
    {
        $processedItems = [];

        // OPTIMIZATION: Pre-fetch all necessary data to avoid N+1 queries
        $productIds = array_merge(
            array_column($items, 'raw_material_product_id'),
            [$productionData['bundle_product_id']]
        );

        // Fetch all products with units in ONE query
        $products = DB::table('m_products as p')
            ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->whereIn('p.id', $productIds)
            ->select('p.*', 'u.code as unit_code', 'u.name as unit_name')
            ->get()
            ->keyBy('id');

        // Fetch warehouse once
        $warehouse = DB::table('m_warehouses')->where('id', $productionData['warehouse_id'])->first();

        // 1. Consume raw materials (decrease stock) - Update all stocks at once
        $stockUpdates = [];
        foreach ($items as $item) {
            $productId = $item['raw_material_product_id'];
            $stockUpdates[] = "WHEN id = {$productId} THEN total_stock - {$item['quantity_consumed']}";
        }

        if (! empty($stockUpdates)) {
            DB::table('m_products')
                ->whereIn('id', array_column($items, 'raw_material_product_id'))
                ->update([
                    'total_stock' => DB::raw('CASE '.implode(' ', $stockUpdates).' ELSE total_stock END'),
                ]);
        }

        // 2. Process each item for FIFO and prepare stock movements
        $stockMovements = [];
        foreach ($items as $item) {
            $product = $products[$item['raw_material_product_id']];

            // Calculate stock before/after for movement log
            $stockBefore = $product->total_stock;
            $stockAfter = $stockBefore - $item['quantity_consumed'];

            // Prepare stock movement data (will be bulk inserted later)
            $stockMovements[] = [
                'company_code' => session('user_company_code'),
                'movement_date' => $productionData['production_date'],
                'movement_type' => 'PRODUCTION_OUT',
                'reference_type' => 'PRODUCTION',
                'reference_number' => $productionData['production_number'],
                'warehouse_id' => $productionData['warehouse_id'],
                'warehouse_code' => $warehouse->code,
                'warehouse_name' => $warehouse->name,
                'product_id' => $item['raw_material_product_id'],
                'product_code' => $product->code,
                'product_name' => $product->name,
                'unit' => $product->unit_code,
                'stock_before' => $stockBefore,
                'movement_qty' => $item['quantity_consumed'],
                'stock_after' => $stockAfter,
                'unit_cost' => $item['unit_cost'] ?? 0,
                'total_value' => ($item['unit_cost'] ?? 0) * $item['quantity_consumed'],
                'notes' => "Raw material consumed for production {$productionData['production_number']}",
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];

            // Consume raw material batches (FIFO) - OPTIMIZED
            $batchConsumption = $this->consumeRawMaterialFIFOOptimized(
                $item['raw_material_product_id'],
                $productionData['warehouse_id'],
                $item['quantity_consumed'],
                $productionId
            );

            $item['batch_consumption'] = $batchConsumption;
            $processedItems[] = $item;
        }

        // 3. Create bundle product (increase stock)
        app(ProductService::class)->updateStock($productionData['bundle_product_id'], '+', $productionData['quantity_produced']);

        // Prepare bundle product movement
        $bundleProduct = $products[$productionData['bundle_product_id']];
        $stockMovements[] = [
            'company_code' => session('user_company_code'),
            'movement_date' => $productionData['production_date'],
            'movement_type' => 'PRODUCTION_IN',
            'reference_type' => 'PRODUCTION',
            'reference_number' => $productionData['production_number'],
            'warehouse_id' => $productionData['warehouse_id'],
            'warehouse_code' => $warehouse->code,
            'warehouse_name' => $warehouse->name,
            'product_id' => $productionData['bundle_product_id'],
            'product_code' => $bundleProduct->code,
            'product_name' => $bundleProduct->name,
            'unit' => $bundleProduct->unit_code,
            'stock_before' => $bundleProduct->total_stock,
            'movement_qty' => $productionData['quantity_produced'],
            'stock_after' => $bundleProduct->total_stock + $productionData['quantity_produced'],
            'unit_cost' => 0,
            'total_value' => 0,
            'notes' => "Bundle product created from production {$productionData['production_number']}",
            'created_by' => Auth::id(),
            'created_at' => now(),
        ];

        // OPTIMIZATION: Bulk insert all stock movements at once
        if (! empty($stockMovements)) {
            DB::table('t_stock_movement_logs')->insert($stockMovements);
        }

        // 4. Create new batch for bundle product
        $this->createProductionBatch(
            $productionData['bundle_product_id'],
            $productionData['warehouse_id'],
            $productionData['quantity_produced'],
            $productionId,
            $productionData
        );

        return [
            'movements_created' => count($items) + 1, // Raw materials + bundle product
            'raw_material_movements' => count($items),
            'bundle_product_batch_created' => true,
            'items_with_batch_details' => $processedItems,
        ];
    }

    public function reverseProductionStockMovements(int $productionId, array $reversalData): array
    {
        $production = $reversalData['production'];
        $items = $reversalData['items'];

        // 1. Remove bundle product (decrease stock)
        app(ProductService::class)->updateStock($production['bundle_product_id'], '-', $reversalData['quantity_to_reverse']);

        // Record bundle product removal movement
        app(StockMovementLogService::class)->recordMovement(
            $production['warehouse_id'],
            $production['bundle_product_id'],
            'PRODUCTION_REVERSAL_OUT',
            'PRODUCTION_REVERSAL',
            $production['production_number'].'-REV',
            $reversalData['quantity_to_reverse'],
            "Bundle product removed for production reversal {$production['production_number']}",
            now()->format('Y-m-d')
        );

        // Remove bundle product batch
        $bundleBatch = $this->removeBundleProductFIFO(
            $production['bundle_product_id'],
            $production['warehouse_id'],
            $reversalData['quantity_to_reverse'],
            $productionId
        );

        // 2. Restore raw materials (increase stock)
        foreach ($items as $item) {
            app(ProductService::class)->updateStock($item['raw_material_product_id'], '+', $item['quantity_consumed']);

            // Record raw material restoration movement
            app(StockMovementLogService::class)->recordMovement(
                $production['warehouse_id'],
                $item['raw_material_product_id'],
                'PRODUCTION_REVERSAL_IN',
                'PRODUCTION_REVERSAL',
                $production['production_number'].'-REV',
                $item['quantity_consumed'],
                "Raw material restored from production reversal {$production['production_number']}",
                now()->format('Y-m-d')
            );

            // Restore raw material batch
            $restoredBatch = $this->restoreRawMaterialBatch(
                $item['raw_material_product_id'],
                $production['warehouse_id'],
                $item['quantity_consumed'],
                $productionId
            );
        }

        return [
            'movements_created' => count($items) + 1, // Raw materials + bundle product
            'bundle_batch_removed' => $bundleBatch,
            'raw_materials_restored' => count($items),
        ];
    }

    private function consumeRawMaterialFIFO(int $productId, int $warehouseId, float $quantityNeeded, int $productionId): array
    {
        $batches = DB::table($this->batch_table)
            ->where('f_product_id', $productId)
            // ->where('warehouse_id', $warehouseId)
            ->where('remaining_qty', '>', 0)
            ->orderBy('created_at', 'ASC') // FIFO order
            ->get();

        // dd($batches);

        $consumedBatches = [];
        $remainingToConsume = $quantityNeeded;

        foreach ($batches as $batch) {
            if ($remainingToConsume <= 0) {
                break;
            }

            $consumeFromThisBatch = min($remainingToConsume, $batch->remaining_qty);

            // Update batch remaining quantity
            DB::table($this->batch_table)
                ->where('id', $batch->id)
                ->decrement('remaining_qty', $consumeFromThisBatch);

            $consumedBatches[] = [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_reference_code,
                'quantity_consumed' => $consumeFromThisBatch,
                'original_quantity' => $batch->initial_qty,
                'remaining_after' => $batch->remaining_qty - $consumeFromThisBatch,
            ];

            $remainingToConsume -= $consumeFromThisBatch;
        }

        return $consumedBatches;
    }

    /**
     * OPTIMIZED: Consume batches using batch updates instead of individual queries
     */
    private function consumeRawMaterialFIFOOptimized(int $productId, int $warehouseId, float $quantityNeeded, int $productionId): array
    {
        // Fetch batches with remaining quantity in FIFO order
        $batches = DB::table($this->batch_table)
            ->where('f_product_id', $productId)
            ->where('remaining_qty', '>', 0)
            ->orderBy('created_at', 'ASC') // FIFO order
            ->get();

        if ($batches->isEmpty()) {
            return [];
        }

        $consumedBatches = [];
        $remainingToConsume = $quantityNeeded;
        $batchUpdates = [];

        // Calculate consumption for each batch
        foreach ($batches as $batch) {
            if ($remainingToConsume <= 0) {
                break;
            }

            $consumeFromThisBatch = min($remainingToConsume, $batch->remaining_qty);
            $newRemainingQty = $batch->remaining_qty - $consumeFromThisBatch;

            // Prepare batch update
            $batchUpdates[] = [
                'id' => $batch->id,
                'consume' => $consumeFromThisBatch,
                'new_remaining' => $newRemainingQty,
            ];

            $consumedBatches[] = [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_reference_code,
                'quantity_consumed' => $consumeFromThisBatch,
                'original_quantity' => $batch->initial_qty,
                'remaining_after' => $newRemainingQty,
            ];

            $remainingToConsume -= $consumeFromThisBatch;
        }

        // OPTIMIZATION: Execute batch updates in a single query using CASE WHEN
        if (! empty($batchUpdates)) {
            $caseStatements = [];
            $ids = [];

            foreach ($batchUpdates as $update) {
                $caseStatements[] = "WHEN id = {$update['id']} THEN {$update['new_remaining']}";
                $ids[] = $update['id'];
            }

            DB::table($this->batch_table)
                ->whereIn('id', $ids)
                ->update([
                    'remaining_qty' => DB::raw('CASE '.implode(' ', $caseStatements).' ELSE remaining_qty END'),
                    'updated_at' => now(),
                ]);
        }

        return $consumedBatches;
    }

    private function removeBundleProductFIFO(int $productId, int $warehouseId, float $quantityToRemove, int $productionId): array
    {
        // Find the specific production batch to remove from
        $batch = DB::table($this->batch_table)
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('reference_type', 'PRODUCTION')
            ->where('reference_id', $productionId)
            ->where('remaining_quantity', '>=', $quantityToRemove)
            ->first();

        if (! $batch) {
            throw new \Exception('Insufficient quantity in production batch for reversal');
        }

        // Update batch remaining quantity
        DB::table($this->batch_table)
            ->where('id', $batch->id)
            ->decrement('remaining_quantity', $quantityToRemove);

        return [
            'batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'quantity_removed' => $quantityToRemove,
            'remaining_after' => $batch->remaining_quantity - $quantityToRemove,
        ];
    }

    private function restoreRawMaterialBatch(int $productId, int $warehouseId, float $quantityToRestore, int $productionId): array
    {
        // Create new batch for restored raw material (FIFO integrity)
        $batchNumber = $this->generateBatchNumber('RESTORE');

        $batchId = DB::table($this->batch_table)->insertGetId([
            'batch_number' => $batchNumber,
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'original_quantity' => $quantityToRestore,
            'remaining_quantity' => $quantityToRestore,
            'unit_cost' => $this->getProductAveragePrice($productId),
            'reference_type' => 'PRODUCTION_REVERSAL',
            'reference_id' => $productionId,
            'batch_date' => now(),
            'expiry_date' => null,
            'notes' => 'Restored from production reversal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'batch_id' => $batchId,
            'batch_number' => $batchNumber,
            'quantity_restored' => $quantityToRestore,
        ];
    }

    private function createProductionBatch(int $productId, int $warehouseId, float $quantity, int $productionId, array $productionData): int
    {
        $batchNumber = $this->generateBatchNumber('PROD');

        return DB::table($this->batch_table)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_product_id' => $productId,
            'batch_reference_code' => $batchNumber,
            'source_type' => 'PRODUCTION',
            'f_source_transaction_id' => $productionId,
            'batch_date' => $productionData['production_date'],
            'initial_qty' => $quantity,
            'remaining_qty' => $quantity,
            'raw_material_cost' => 0.00,
            'production_cost' => $this->calculateBundleProductUnitCost($productionData),
            'purchase_price' => 0.00,
            'estimated_hpp' => $this->calculateBundleProductUnitCost($productionData),
            'final_hpp' => null,
            'hpp_status' => 'UNSETTLED',
            'status' => 'ACTIVE',
            'created_at' => now(),
            'created_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    // Removed: updateProductStockTotals - now handled by centralized ProductService::updateStock()

    // Removed: updateProductStockTotalsReversal - now handled by centralized ProductService::updateStock()

    // private function generateMovementId(): string
    // {
    //     return 'MOV-' . date('YmdHis') . '-' . substr(uniqid(), -4);
    // }

    private function generateBatchNumber(string $prefix): string
    {
        return $prefix.'-'.date('YmdHis').'-'.substr(uniqid(), -4);
    }

    private function getBundleProductUnitCode(int $productId): string
    {
        $product = DB::table('m_products as p')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.id', $productId)
            ->first();

        return $product->code;
    }

    private function getProductAveragePrice(int $productId): float
    {
        $product = DB::table('m_products')
            ->where('id', $productId)
            ->first();

        return $product->average_price ?? 0;
    }

    private function calculateBundleProductUnitCost(array $productionData): float
    {
        // Calculate unit cost based on total production cost
        if ($productionData['quantity_produced'] > 0) {
            return $productionData['total_production_cost'] / $productionData['quantity_produced'];
        }

        return 0;
    }
}
