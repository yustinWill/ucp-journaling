<?php

namespace App\Domains\StockOpname\Actions;

use App\Enums\ErrorCodes;
use App\Exceptions\AppException;
use App\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                $this->updateAdjustmentItems($id, $data['products']);
                $this->updateStockMovementLogs($id, $data['products'], $data, $originalData);
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
}
