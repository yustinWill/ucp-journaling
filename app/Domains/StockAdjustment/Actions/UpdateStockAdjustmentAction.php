<?php

namespace App\Domains\StockAdjustment\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\StockAdjustment\Services\StockAdjustmentAuditLogService;
use App\Domains\StockAdjustment\Services\StockAdjustmentService;
use App\Enums\ErrorCodes;
use App\Exceptions\AppException;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateStockAdjustmentAction
{
    public function execute(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            // Get original data for audit
            $originalData = app(StockAdjustmentService::class)->findOne($id);

            if ($originalData->status !== 'DRAFT') {
                throw new AppException(ErrorCodes::STOCK_ADJUSTMENT_STATUS_MUST_BE_DRAFT);
            }

            // Update main stock adjustment record
            app(StockAdjustmentService::class)->update($id, [
                'adjustment_date' => $data['adjustment_date'],
                'adjustment_type' => $data['adjustment_type'],
                'warehouse_id' => $data['warehouse_id'],
                'pic_id' => $data['pic_id'],
                'status' => $data['status'],
                'description' => $data['description'],
                'memo' => $data['memo'] ?? null,
                'tags' => $data['tags'] ?? [],
                'reference_number' => $data['reference_number'] ?? null,
                'total_items' => count($data['products'] ?? []),
                'total_adjustment_qty' => $this->calculateTotalQty($data['products'] ?? []),
                'total_adjustment_value' => $this->calculateTotalValue($data['products'] ?? []),
            ]);

            // Update adjustment items if products are provided
            if (isset($data['products'])) {
                // Reverse previous adjustments first
                $this->reversePreviousAdjustments($id, $originalData);

                // Apply new adjustments
                $this->processStockAdjustments($id, $data['products'], $data);
                $this->updateAdjustmentItems($id, $data['products']);
                $this->updateStockMovementLogs($id, $data['products'], $data, $originalData);

                // Recalculate total_stock from movement logs for all affected products
                foreach ($data['products'] as $product) {
                    if ($product['adjustment_qty'] != 0) {
                        app(ProductService::class)->updateStock($product['product_id']);
                    }
                }
            }

            // Create audit log
            app(StockAdjustmentAuditLogService::class)->create(
                $id,
                'UPDATE',
                'Stock adjustment updated',
                [
                    'changes' => $this->getChanges($originalData, $data),
                    'updated_fields' => array_keys($data),
                ]
            );

            return true;
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

    private function updateAdjustmentItems(int $stockAdjustmentId, array $products): void
    {
        // Delete existing items
        DB::table('t_stock_adjustment_items')
            ->where('stock_adjustment_id', $stockAdjustmentId)
            ->where('company_code', session('user_company_code'))
            ->delete();

        // Insert new items
        if (! empty($products)) {
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
    }

    private function updateStockMovementLogs(int $stockAdjustmentId, array $products, array $mainData, $originalData): void
    {
        // Delete existing movement logs for this adjustment
        $adjustment = DB::table('t_stock_adjustments')->where('id', $stockAdjustmentId)->first();
        DB::table('t_stock_movement_logs')
            ->where('reference_type', 'STOCK_ADJUSTMENT')
            ->where('reference_number', $adjustment->adjustment_number)
            ->where('company_code', session('user_company_code'))
            ->delete();

        // Create new movement logs
        $warehouse = DB::table('m_warehouses')->where('id', $mainData['warehouse_id'])->first();

        $movements = [];
        foreach ($products as $product) {
            if ($product['adjustment_qty'] != 0) {
                $movementType = $product['adjustment_qty'] > 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';

                $movements[] = [
                    'movement_date' => $mainData['adjustment_date'],
                    'movement_type' => $movementType,
                    'reference_type' => 'STOCK_ADJUSTMENT',
                    'reference_number' => $adjustment->adjustment_number,
                    'warehouse_id' => $mainData['warehouse_id'],
                    'warehouse_code' => $warehouse->code ?? '',
                    'warehouse_name' => $warehouse->name ?? '',
                    'product_id' => $product['product_id'],
                    'product_code' => $product['product_code'],
                    'product_name' => $product['product_name'],
                    'unit' => $product['unit'],
                    'stock_before' => $product['stock_before'],
                    'movement_qty' => $product['adjustment_qty'],
                    'stock_after' => $product['stock_before'] + $product['adjustment_qty'],
                    'unit_cost' => $product['unit_cost'] ?? 0,
                    'total_value' => ($product['adjustment_qty'] * ($product['unit_cost'] ?? 0)),
                    'notes' => $product['reason'] ?? $mainData['description'],
                ];
            }
        }

        if (! empty($movements)) {
            app(StockMovementLogService::class)->createBulk($movements);
        }
    }

    private function getChanges($originalData, array $newData): array
    {
        $changes = [];

        $trackableFields = [
            'adjustment_date',
            'adjustment_type',
            'warehouse_id',
            'pic_id',
            'status',
            'description',
            'memo',
            'reference_number',
        ];

        foreach ($trackableFields as $field) {
            if (isset($newData[$field]) && $newData[$field] != $originalData->$field) {
                $changes[$field] = [
                    'old' => $originalData->$field,
                    'new' => $newData[$field],
                ];
            }
        }

        return $changes;
    }

    private function reversePreviousAdjustments(int $stockAdjustmentId, $originalData): void
    {
        // Get previous adjustment items
        $previousItems = DB::table('t_stock_adjustment_items')
            ->where('stock_adjustment_id', $stockAdjustmentId)
            ->where('company_code', session('user_company_code'))
            ->get();

        foreach ($previousItems as $item) {
            if ($item->adjustment_qty != 0) {
                $productId = $item->product_id;
                $previousQty = $item->adjustment_qty;

                if ($previousQty > 0) {
                    // Previous was positive - now reverse by consuming batches
                    $this->consumeStockBatches($productId, $previousQty, $stockAdjustmentId);
                } else {
                    // Previous was negative - now reverse by creating batch
                    $this->createReversalBatch($stockAdjustmentId, $item);
                }
            }
        }
    }

    private function processStockAdjustments(int $stockAdjustmentId, array $products, array $mainData): void
    {
        foreach ($products as $product) {
            if ($product['adjustment_qty'] != 0) {
                $productId = $product['product_id'];
                $adjustmentQty = $product['adjustment_qty'];

                if ($adjustmentQty > 0) {
                    // Positive adjustment - create new batch
                    $this->createAdjustmentBatch($stockAdjustmentId, $product, $mainData);
                } else {
                    // Negative adjustment - consume batches (FIFO)
                    $this->consumeStockBatches($productId, abs($adjustmentQty), $stockAdjustmentId);
                }
            }
        }
    }

    private function createAdjustmentBatch(int $stockAdjustmentId, array $product, array $mainData): int
    {
        $batchReference = 'ADJ-UPD-'.$stockAdjustmentId.'-'.$product['product_id'].'-'.time();
        $unitCost = $product['unit_cost'] ?? 0;

        return DB::table('t_product_stock_batches')->insertGetId([
            'company_code' => session('user_company_code'),
            'f_product_id' => $product['product_id'],
            'batch_reference_code' => $batchReference,
            'source_type' => 'ADJUSTMENT',
            'f_source_transaction_id' => $stockAdjustmentId,
            'batch_date' => $mainData['adjustment_date'],
            'initial_qty' => $product['adjustment_qty'],
            'remaining_qty' => $product['adjustment_qty'],
            'raw_material_cost' => 0.00,
            'production_cost' => 0.00,
            'purchase_price' => $unitCost,
            'estimated_hpp' => $unitCost,
            'final_hpp' => null,
            'hpp_status' => 'SETTLED',
            'status' => 'ACTIVE',
            'created_at' => now(),
            'created_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    private function createReversalBatch(int $stockAdjustmentId, $item): int
    {
        $batchReference = 'ADJ-REV-'.$stockAdjustmentId.'-'.$item->product_id.'-'.time();
        $unitCost = $item->unit_cost ?? 0;

        return DB::table('t_product_stock_batches')->insertGetId([
            'company_code' => session('user_company_code'),
            'f_product_id' => $item->product_id,
            'batch_reference_code' => $batchReference,
            'source_type' => 'ADJUSTMENT_REVERSAL',
            'f_source_transaction_id' => $stockAdjustmentId,
            'batch_date' => now()->format('Y-m-d'),
            'initial_qty' => abs($item->adjustment_qty),
            'remaining_qty' => abs($item->adjustment_qty),
            'raw_material_cost' => 0.00,
            'production_cost' => 0.00,
            'purchase_price' => $unitCost,
            'estimated_hpp' => $unitCost,
            'final_hpp' => null,
            'hpp_status' => 'SETTLED',
            'status' => 'ACTIVE',
            'created_at' => now(),
            'created_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    private function consumeStockBatches(int $productId, float $quantityToConsume, int $adjustmentId): void
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
                'source_type' => 'STOCK_ADJUSTMENT',
                'f_source_transaction_id' => $adjustmentId,
                'transaction_date' => now(),
                'qty_moved' => $consumeFromThisBatch,
                'reference_code' => 'ADJ-'.$adjustmentId,
                'notes' => 'Stock consumed by Stock Adjustment Update',
                'created_at' => now(),
                'created_by' => Auth::id(),
            ]);

            $remainingToConsume -= $consumeFromThisBatch;
        }

        if ($remainingToConsume > 0) {
            Log::warning("Insufficient stock batches for product {$productId}. Could not consume {$remainingToConsume} units in adjustment {$adjustmentId}.");
        }
    }
}
