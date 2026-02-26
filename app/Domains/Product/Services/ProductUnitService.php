<?php

namespace App\Domains\Product\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductUnitService
{
    private $table_name = 'm_product_units';

    /**
     * Create multiple units for a product
     */
    public function createMultiple(int $productId, array $units): void
    {
        foreach ($units as $unit) {
            $this->create($productId, $unit);
        }
    }

    /**
     * Create a single product unit
     * This method is used both for creating new products and updating existing ones
     */
    public function create(int $productId, array $data): int
    {
        // If this unit is marked as default, ensure no other unit is default for this product
        if (isset($data['is_default']) && $data['is_default']) {
            DB::table($this->table_name)
                ->where('company_code', session('user_company_code'))
                ->where('f_product_id', $productId)
                ->update(['is_default' => false]);
        }

        // Check if this unit already exists for this product (handles both create and update scenarios)
        $existing = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('f_product_id', $productId)
            ->where('f_unit_id', $data['unit_id'])
            ->first();

        if ($existing) {
            // Update existing record instead of inserting duplicate
            // This handles the case where product is being updated or unit data is being refreshed
            DB::table($this->table_name)
                ->where('id', $existing->id)
                ->update([
                    'is_default' => $data['is_default'] ?? false,
                    'conversion_factor' => $data['conversion_factor'] ?? 1,
                    'status' => $data['status'] ?? GeneralStatuses::ACTIVE->value,
                    'notes' => isset($data['note']) && $data['note'] !== '' ? $data['note'] : null,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            return $existing->id;
        }

        // Insert new record only if it doesn't exist
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_product_id' => $productId,
            'f_unit_id' => $data['unit_id'],
            'is_default' => $data['is_default'] ?? false,
            'conversion_factor' => $data['conversion_factor'] ?? 1,
            'status' => $data['status'] ?? GeneralStatuses::ACTIVE->value,
            'notes' => isset($data['note']) && $data['note'] !== '' ? $data['note'] : null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Update product units - delete old ones and create new ones
     */
    public function updateProductUnits(int $productId, array $units): void
    {
        // Delete existing units for this product
        $this->deleteByProduct($productId);

        // Create new units
        $this->createMultiple($productId, $units);
    }

    /**
     * Get all units for a specific product
     * If no units in m_product_units, returns the base unit from m_products
     */
    public function findByProduct(int $productId)
    {
        $units = DB::table($this->table_name . ' as pu')
            ->select([
                'pu.*',
                'u.code as unit_code',
                'u.name as unit_name',
                DB::raw('CONCAT(u.name, " (", u.code, ")") as unit_display'),
            ])
            ->leftJoin('m_units as u', 'pu.f_unit_id', '=', 'u.id')
            ->where('pu.f_product_id', $productId)
            ->where('pu.company_code', session('user_company_code'))
            ->orderBy('pu.is_default', 'desc')
            ->orderBy('pu.created_at', 'asc')
            ->get();

        // If no units configured, return the base unit from m_products
        if ($units->isEmpty()) {
            $baseUnit = DB::table('m_products as p')
                ->select([
                    DB::raw('NULL as id'),
                    DB::raw("'" . session('user_company_code') . "' as company_code"),
                    DB::raw("$productId as f_product_id"),
                    'p.f_unit_id',
                    DB::raw('1 as is_default'),
                    DB::raw('1.0000 as conversion_factor'),
                    DB::raw("'ACTIVE' as status"),
                    DB::raw('NULL as notes'),
                    DB::raw('NULL as created_at'),
                    DB::raw('NULL as updated_at'),
                    DB::raw('NULL as created_by'),
                    DB::raw('NULL as updated_by'),
                    'u.code as unit_code',
                    'u.name as unit_name',
                    DB::raw('CONCAT(u.name, " (", u.code, ")") as unit_display'),
                ])
                ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
                ->where('p.id', $productId)
                ->where('p.company_code', session('user_company_code'))
                ->get();

            return $baseUnit;
        }

        return $units;
    }

    /**
     * Get default unit for a product
     */
    public function findDefaultUnit(int $productId)
    {
        return DB::table($this->table_name . ' as pu')
            ->select([
                'pu.*',
                'u.code as unit_code',
                'u.name as unit_name',
            ])
            ->leftJoin('m_units as u', 'pu.f_unit_id', '=', 'u.id')
            ->where('pu.f_product_id', $productId)
            ->where('pu.company_code', session('user_company_code'))
            ->where('pu.is_default', true)
            ->first();
    }

    /**
     * Convert quantity from one unit to default unit
     */
    public function convertToDefaultUnit(int $productId, int $fromUnitId, float $quantity): ?float
    {
        $unit = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('f_product_id', $productId)
            ->where('f_unit_id', $fromUnitId)
            ->where('status', GeneralStatuses::ACTIVE->value)
            ->first();

        if ($unit) {
            return $quantity * $unit->conversion_factor;
        }

        return null;
    }

    /**
     * Convert quantity from default unit to another unit
     */
    public function convertFromDefaultUnit(int $productId, int $toUnitId, float $quantity): ?float
    {
        $unit = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('f_product_id', $productId)
            ->where('f_unit_id', $toUnitId)
            ->where('status', GeneralStatuses::ACTIVE->value)
            ->first();

        if ($unit && $unit->conversion_factor > 0) {
            return $quantity / $unit->conversion_factor;
        }

        return null;
    }

    /**
     * Delete all units for a product
     */
    public function deleteByProduct(int $productId): int
    {
        return DB::table($this->table_name)
            ->where('f_product_id', $productId)
            ->where('company_code', session('user_company_code'))
            ->delete();
    }

    /**
     * Delete a specific product unit
     */
    public function delete(int $id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->delete();
    }

    /**
     * Check if a product has units configured
     */
    public function hasUnits(int $productId): bool
    {
        return DB::table($this->table_name)
            ->where('f_product_id', $productId)
            ->where('company_code', session('user_company_code'))
            ->exists();
    }

    /**
     * Get active units for a product (for dropdowns)
     */
    public function findActiveByProduct(int $productId)
    {
        return DB::table($this->table_name . ' as pu')
            ->select([
                'pu.id',
                'pu.f_unit_id',
                'pu.conversion_factor',
                'pu.is_default',
                'u.code as unit_code',
                'u.name as unit_name',
                DB::raw('CONCAT(u.name, " (", u.code, ")") as unit_display'),
            ])
            ->leftJoin('m_units as u', 'pu.f_unit_id', '=', 'u.id')
            ->where('pu.f_product_id', $productId)
            ->where('pu.company_code', session('user_company_code'))
            ->where('pu.status', GeneralStatuses::ACTIVE->value)
            ->orderBy('pu.is_default', 'desc')
            ->orderBy('pu.created_at', 'asc')
            ->get();
    }
}
