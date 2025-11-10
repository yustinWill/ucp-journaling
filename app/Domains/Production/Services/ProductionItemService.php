<?php

namespace App\Domains\Production\Services;

use Illuminate\Support\Facades\DB;

class ProductionItemService
{
    private $table_name = 't_production_items';

    public function findByProductionId(int $productionId): array
    {
        return DB::table($this->table_name.' as pi')
            ->select(
                'pi.*',
                'p.name as product_name',
                'p.code as product_code',
                'p.total_stock as current_stock',
                'pc.qty as quantity_per_bundle',
                'u.name as unit_name',
                'u.code as unit_code'
            )
            ->leftJoin('m_products as p', 'pi.raw_material_product_id', '=', 'p.id')
            ->leftJoin('t_productions as pro', 'pi.production_id', '=', 'pro.id')
            ->leftJoin('m_product_compositions as pc', function($join) {
                $join->on('pi.raw_material_product_id', '=', 'pc.f_product_composition_product_id')
                    ->on('pro.bundle_product_id', '=', 'pc.f_product_id');
            })
            ->leftJoin('m_units as u', 'pi.unit_code', '=', 'u.code')
            ->where('pi.production_id', $productionId)
            ->orderBy('pi.id')
            ->get()
            ->toArray();
    }

    public function createBulk(int $productionId, array $items): void
    {
        $insertData = [];

        foreach ($items as $item) {
            $insertData[] = [
                'production_id' => $productionId,
                'raw_material_product_id' => $item['raw_material_product_id'],
                'quantity_required' => $item['quantity_required'],
                'quantity_consumed' => $item['quantity_consumed'] ?? $item['quantity_required'],
                'material_input' => $item['material_input'] ?? null, // For Material-Based mode
                'leftover_amount' => $item['leftover'] ?? null, // For Material-Based mode (4 decimals)
                'unit_code' => $item['unit_code'],
                'unit_cost' => $item['unit_cost'] ?? 0,
                'total_cost' => ($item['quantity_consumed'] ?? $item['quantity_required']) * ($item['unit_cost'] ?? 0),
                'batch_consumption' => json_encode($item['batch_consumption'] ?? []),
                'notes' => $item['notes'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table($this->table_name)->insert($insertData);
    }

    public function updateBulk(int $productionId, array $items): void
    {
        // Delete existing items
        DB::table($this->table_name)->where('production_id', $productionId)->delete();

        // Insert updated items
        $this->createBulk($productionId, $items);
    }

    public function deleteByProductionId(int $productionId): int
    {
        return DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->delete();
    }

    public function calculateTotalRawMaterialCost(int $productionId): float
    {
        return DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->sum('total_cost');
    }

    public function getTotalItemsConsumed(int $productionId): int
    {
        return DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->count();
    }

    public function getItemsForReversal(int $productionId): array
    {
        return DB::table($this->table_name.' as pi')
            ->select(
                'pi.*',
                'p.name as product_name',
                'p.code as product_code'
            )
            ->leftJoin('m_products as p', 'pi.raw_material_product_id', '=', 'p.id')
            ->where('pi.production_id', $productionId)
            ->where('pi.quantity_consumed', '>', 0)
            ->get()
            ->toArray();
    }

    public function getBatchConsumptionDetails(int $productionId): array
    {
        $items = DB::table($this->table_name)
            ->where('production_id', $productionId)
            ->whereNotNull('batch_consumption')
            ->get();

        $batchDetails = [];
        foreach ($items as $item) {
            if ($item->batch_consumption) {
                $batches = json_decode($item->batch_consumption, true);
                $batchDetails[$item->raw_material_product_id] = $batches;
            }
        }

        return $batchDetails;
    }

    public function validateStockAvailability(array $items, int $warehouseId): array
    {
        $validation = [];

        foreach ($items as $item) {
            $product = DB::table('m_products')
                ->where('id', $item['raw_material_product_id'])
                ->first();
            // dd($product->total_stock);
            // Check total stock
            $isAvailable = $product && $product->total_stock >= $item['quantity_required'];
            // Get available batches for this product in the warehouse
            // $availableBatches = DB::table('t_product_stock_batches')
            //     ->where('f_product_id', $item['raw_material_product_id'])
            //     // warehouse tidak ada
            //     // ->where('warehouse_id', $warehouseId)
            //     ->where('remaining_qty', '>', 0)
            //     ->where('status', 'ACTIVE')
            //     ->orderBy('created_at', 'ASC') // FIFO order
            //     ->get();
            $availableBatches = DB::table('t_stock_movement_logs')
                ->where('product_id', $item['raw_material_product_id'])
                // warehouse tidak ada
                ->where('warehouse_id', $warehouseId)
                ->where('stock_after', '>', 0)
                ->orderBy('created_at', 'ASC') // FIFO order
                ->get();
            $totalAvailableInBatches = $availableBatches->sum('stock_after');
            $validation[] = [
                'raw_material_product_id' => $item['raw_material_product_id'],
                'product_name' => $product->name ?? 'Unknown',
                'quantity_required' => $item['quantity_required'],
                'total_stock' => $product->total_stock ?? 0,
                'available_in_warehouse' => $totalAvailableInBatches,
                'is_available' => $isAvailable && $totalAvailableInBatches >= $item['quantity_required'],
                'available_batches' => $availableBatches->toArray(),
            ];
        }

        return $validation;
    }
}
