<?php

namespace App\Domains\StockAdjustment\Actions;

use App\Domains\StockAdjustment\Services\StockAdjustmentAuditLogService;
use App\Domains\StockAdjustment\Services\StockAdjustmentService;
use App\Domains\StockAdjustment\Services\StockMovementLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateStockAdjustmentAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // Create main stock adjustment record
            $stockAdjustmentId = app(StockAdjustmentService::class)->create([
                'adjustment_date' => $data['adjustment_date'],
                'adjustment_type' => $data['adjustment_type'],
                'warehouse_id' => $data['warehouse_id'],
                'pic_id' => $data['pic_id'],
                'status' => 'APPROVED',
                'description' => $data['description'],
                'memo' => $data['memo'] ?? null,
                'f_tag_codes' => $data['tags'] ?? [],
                'reference_number' => $data['reference_number'] ?? null,
                'total_items' => count($data['products'] ?? []),
                'total_adjustment_qty' => $this->calculateTotalQty($data['products'] ?? []),
                'total_adjustment_value' => $this->calculateTotalValue($data['products'] ?? []),
            ]);

            // Create adjustment items and stock movement logs
            if (! empty($data['products'])) {
                $this->createAdjustmentItems($stockAdjustmentId, $data['products'], $data);
                $this->createStockMovementLogs($stockAdjustmentId, $data['products'], $data);
            }

            // Create audit log
            app(StockAdjustmentAuditLogService::class)->create(
                $stockAdjustmentId,
                'CREATE',
                'Stock adjustment created',
                [
                    'adjustment_type' => $data['adjustment_type'],
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
                $movementType = $product['adjustment_qty'] > 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';

                $movements[] = [
                    'movement_date' => $mainData['adjustment_date'],
                    'movement_type' => $movementType,
                    'reference_type' => 'STOCK_ADJUSTMENT',
                    'reference_number' => $adjustment->adjustment_number,
                    'warehouse_id' => $mainData['warehouse_id'],
                    'warehouse_code' => $warehouse->code,
                    'warehouse_name' => $warehouse->name,
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
}
