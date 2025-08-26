<?php

namespace App\Domains\Production\Services;

use Illuminate\Support\Facades\DB;

class ProductionStockService
{
    private $movement_table = 't_stock_movement_logs';
    private $batch_table = 't_product_stock_batches';

    public function createProductionStockMovements(int $productionId, array $items, array $productionData): array
    {
        $movements = [];

        // 1. Consume raw materials (negative movements)
        foreach ($items as $item) {
            $batchConsumption = $this->consumeRawMaterialFIFO(
                $item['raw_material_product_id'],
                $productionData['warehouse_id'],
                $item['quantity_consumed'],
                $productionId
            );

            $movements[] = [
                'movement_id' => $this->generateMovementId(),
                'company_code' => session('user_company_code'),
                'product_id' => $item['raw_material_product_id'],
                'warehouse_id' => $productionData['warehouse_id'],
                'movement_type' => 'PRODUCTION_CONSUME',
                'quantity' => -$item['quantity_consumed'], // Negative for consumption
                'unit_code' => $item['unit_code'],
                'reference_type' => 'PRODUCTION',
                'reference_id' => $productionId,
                'reference_number' => $productionData['production_number'],
                'movement_date' => $productionData['production_date'],
                'notes' => "Raw material consumed for production {$productionData['production_number']}",
                'batch_details' => json_encode($batchConsumption),
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Update item with batch consumption details
            $item['batch_consumption'] = $batchConsumption;
        }

        // 2. Create bundle product (positive movement)
        $bundleMovement = [
            'movement_id' => $this->generateMovementId(),
            'company_code' => session('user_company_code'),
            'product_id' => $productionData['bundle_product_id'],
            'warehouse_id' => $productionData['warehouse_id'],
            'movement_type' => 'PRODUCTION_CREATE',
            'quantity' => $productionData['quantity_produced'],
            'unit_code' => $this->getBundleProductUnitCode($productionData['bundle_product_id']),
            'reference_type' => 'PRODUCTION',
            'reference_id' => $productionId,
            'reference_number' => $productionData['production_number'],
            'movement_date' => $productionData['production_date'],
            'notes' => "Bundle product created from production {$productionData['production_number']}",
            'batch_details' => null,
            'created_at' => now(),
            'updated_at' => now()
        ];

        $movements[] = $bundleMovement;

        // Insert all movements
        DB::table($this->movement_table)->insert($movements);

        // 3. Create new batch for bundle product
        $this->createProductionBatch(
            $productionData['bundle_product_id'],
            $productionData['warehouse_id'],
            $productionData['quantity_produced'],
            $productionId,
            $productionData
        );

        // 4. Update product stock totals
        $this->updateProductStockTotals($items, $productionData);

        return [
            'movements_created' => count($movements),
            'raw_material_movements' => count($items),
            'bundle_product_batch_created' => true,
            'items_with_batch_details' => $items
        ];
    }

    public function reverseProductionStockMovements(int $productionId, array $reversalData): array
    {
        $movements = [];
        $production = $reversalData['production'];
        $items = $reversalData['items'];

        // 1. Remove bundle product (negative movement)
        $bundleBatch = $this->removeBundleProductFIFO(
            $production['bundle_product_id'],
            $production['warehouse_id'],
            $reversalData['quantity_to_reverse'],
            $productionId
        );

        $movements[] = [
            'movement_id' => $this->generateMovementId(),
            'company_code' => session('user_company_code'),
            'product_id' => $production['bundle_product_id'],
            'warehouse_id' => $production['warehouse_id'],
            'movement_type' => 'PRODUCTION_REVERSAL_REMOVE',
            'quantity' => -$reversalData['quantity_to_reverse'], // Negative for removal
            'unit_code' => $this->getBundleProductUnitCode($production['bundle_product_id']),
            'reference_type' => 'PRODUCTION_REVERSAL',
            'reference_id' => $productionId,
            'reference_number' => $production['production_number'] . '-REV',
            'movement_date' => now()->format('Y-m-d'),
            'notes' => "Bundle product removed for production reversal {$production['production_number']}",
            'batch_details' => json_encode($bundleBatch),
            'created_at' => now(),
            'updated_at' => now()
        ];

        // 2. Restore raw materials (positive movements)
        foreach ($items as $item) {
            $restoredBatch = $this->restoreRawMaterialBatch(
                $item['raw_material_product_id'],
                $production['warehouse_id'],
                $item['quantity_consumed'],
                $productionId
            );

            $movements[] = [
                'movement_id' => $this->generateMovementId(),
                'company_code' => session('user_company_code'),
                'product_id' => $item['raw_material_product_id'],
                'warehouse_id' => $production['warehouse_id'],
                'movement_type' => 'PRODUCTION_REVERSAL_RESTORE',
                'quantity' => $item['quantity_consumed'], // Positive for restoration
                'unit_code' => $item['unit_code'],
                'reference_type' => 'PRODUCTION_REVERSAL',
                'reference_id' => $productionId,
                'reference_number' => $production['production_number'] . '-REV',
                'movement_date' => now()->format('Y-m-d'),
                'notes' => "Raw material restored from production reversal {$production['production_number']}",
                'batch_details' => json_encode($restoredBatch),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insert all movements
        DB::table($this->movement_table)->insert($movements);

        // Update product stock totals
        $this->updateProductStockTotalsReversal($items, $production);

        return [
            'movements_created' => count($movements),
            'bundle_batch_removed' => $bundleBatch,
            'raw_materials_restored' => count($items)
        ];
    }

    private function consumeRawMaterialFIFO(int $productId, int $warehouseId, float $quantityNeeded, int $productionId): array
    {
        $batches = DB::table($this->batch_table)
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('created_at', 'ASC') // FIFO order
            ->get();

        $consumedBatches = [];
        $remainingToConsume = $quantityNeeded;

        foreach ($batches as $batch) {
            if ($remainingToConsume <= 0) break;

            $consumeFromThisBatch = min($remainingToConsume, $batch->remaining_quantity);
            
            // Update batch remaining quantity
            DB::table($this->batch_table)
                ->where('id', $batch->id)
                ->decrement('remaining_quantity', $consumeFromThisBatch);

            $consumedBatches[] = [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'quantity_consumed' => $consumeFromThisBatch,
                'original_quantity' => $batch->original_quantity,
                'remaining_after' => $batch->remaining_quantity - $consumeFromThisBatch
            ];

            $remainingToConsume -= $consumeFromThisBatch;
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

        if (!$batch) {
            throw new \Exception("Insufficient quantity in production batch for reversal");
        }

        // Update batch remaining quantity
        DB::table($this->batch_table)
            ->where('id', $batch->id)
            ->decrement('remaining_quantity', $quantityToRemove);

        return [
            'batch_id' => $batch->id,
            'batch_number' => $batch->batch_number,
            'quantity_removed' => $quantityToRemove,
            'remaining_after' => $batch->remaining_quantity - $quantityToRemove
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
            'notes' => "Restored from production reversal",
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return [
            'batch_id' => $batchId,
            'batch_number' => $batchNumber,
            'quantity_restored' => $quantityToRestore
        ];
    }

    private function createProductionBatch(int $productId, int $warehouseId, float $quantity, int $productionId, array $productionData): int
    {
        $batchNumber = $this->generateBatchNumber('PROD');

        return DB::table($this->batch_table)->insertGetId([
            'batch_number' => $batchNumber,
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'original_quantity' => $quantity,
            'remaining_quantity' => $quantity,
            'unit_cost' => $this->calculateBundleProductUnitCost($productionData),
            'reference_type' => 'PRODUCTION',
            'reference_id' => $productionId,
            'batch_date' => $productionData['production_date'],
            'expiry_date' => null,
            'notes' => "Produced from {$productionData['production_number']}",
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    private function updateProductStockTotals(array $items, array $productionData): void
    {
        // Decrease raw material stock
        foreach ($items as $item) {
            DB::table('m_products')
                ->where('id', $item['raw_material_product_id'])
                ->decrement('total_stock', $item['quantity_consumed']);
        }

        // Increase bundle product stock
        DB::table('m_products')
            ->where('id', $productionData['bundle_product_id'])
            ->increment('total_stock', $productionData['quantity_produced']);
    }

    private function updateProductStockTotalsReversal(array $items, array $production): void
    {
        // Restore raw material stock
        foreach ($items as $item) {
            DB::table('m_products')
                ->where('id', $item['raw_material_product_id'])
                ->increment('total_stock', $item['quantity_consumed']);
        }

        // Decrease bundle product stock
        DB::table('m_products')
            ->where('id', $production['bundle_product_id'])
            ->decrement('total_stock', $production['quantity_produced']);
    }

    private function generateMovementId(): string
    {
        return 'MOV-' . date('YmdHis') . '-' . substr(uniqid(), -4);
    }

    private function generateBatchNumber(string $prefix): string
    {
        return $prefix . '-' . date('YmdHis') . '-' . substr(uniqid(), -4);
    }

    private function getBundleProductUnitCode(int $productId): string
    {
        $product = DB::table('m_products as p')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.id', $productId)
            ->first();

        return $product->code ?? 'PCS';
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