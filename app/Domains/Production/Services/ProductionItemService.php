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
        // Get bundle product's default unit code
        $production = DB::table('t_productions')->where('id', $productionId)->first();

        $bundleDefaultUnitCode = DB::table('m_product_units as pu')
            ->join('m_units as u', 'pu.f_unit_id', '=', 'u.id')
            ->where('pu.f_product_id', $production->bundle_product_id)
            ->where('pu.is_default', 1)
            ->value('u.code');

        // Fallback to product's default unit if no product_unit found
        if (!$bundleDefaultUnitCode) {
            $bundleDefaultUnitCode = DB::table('m_products as p')
                ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
                ->where('p.id', $production->bundle_product_id)
                ->value('u.code');
        }

        $insertData = [];

        foreach ($items as $item) {
            $insertData[] = [
                'production_id' => $productionId,
                'raw_material_product_id' => $item['raw_material_product_id'],
                'quantity_required' => $item['quantity_required'],
                'quantity_consumed' => $item['quantity_consumed'] ?? $item['quantity_required'],
                'material_input' => $item['material_input'] ?? null, // For Material-Based mode
                'leftover_amount' => $item['leftover'] ?? null, // For Material-Based mode (4 decimals)
                'unit_code' => $bundleDefaultUnitCode, // Use bundle product's default unit instead of component unit
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

            // Get unit conversion info if unit_code is provided
            $conversionFactor = 1;
            $isDefaultUnit = true;

            if (!empty($item['unit_code'])) {
                $productUnit = DB::table('m_product_units as pu')
                    ->join('m_units as u', 'pu.f_unit_id', '=', 'u.id')
                    ->where('pu.f_product_id', $item['raw_material_product_id'])
                    ->where('u.code', $item['unit_code'])
                    ->select('pu.conversion_factor', 'pu.is_default')
                    ->first();

                if ($productUnit) {
                    $conversionFactor = $productUnit->conversion_factor ?? 1;
                    $isDefaultUnit = (bool) ($productUnit->is_default ?? true);
                }
            }

            // Calculate stock available in warehouse using the same logic as page display
            // Sum movement_qty: IN adds, OUT subtracts (stock is always in base unit)
            $warehouseStock = DB::table('t_stock_movement_logs')
                ->where('product_id', $item['raw_material_product_id'])
                ->where('warehouse_id', $warehouseId)
                ->selectRaw('SUM(
                    CASE
                        WHEN movement_type LIKE "%_IN" THEN movement_qty
                        WHEN movement_type LIKE "%_OUT" THEN -movement_qty
                        ELSE 0
                    END
                ) as total_stock')
                ->value('total_stock') ?? 0;

            // Convert quantity_required to base unit if not using default unit
            // quantity_required is in component's unit, need to convert for stock comparison
            $quantityRequiredInBaseUnit = $isDefaultUnit
                ? $item['quantity_required']
                : ($item['quantity_required'] * $conversionFactor);

            // Check availability based on warehouse stock (both in base unit now)
            $isAvailable = $warehouseStock >= $quantityRequiredInBaseUnit;

            // Get available batches for FIFO consumption
            $availableBatches = DB::table('t_product_stock_batches')
                ->where('f_product_id', $item['raw_material_product_id'])
                ->where('remaining_qty', '>', 0)
                ->where('status', 'ACTIVE')
                ->orderBy('batch_date', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();

            $validation[] = [
                'raw_material_product_id' => $item['raw_material_product_id'],
                'product_name' => $product->name ?? 'Unknown',
                'quantity_required' => $item['quantity_required'],
                'quantity_required_base_unit' => $quantityRequiredInBaseUnit,
                'total_stock' => $product->total_stock ?? 0,
                'available_in_warehouse' => $warehouseStock,
                'conversion_factor' => $conversionFactor,
                'is_default_unit' => $isDefaultUnit,
                'is_available' => $isAvailable,
                'available_batches' => $availableBatches->toArray(),
            ];
        }
        return $validation;
    }
}
