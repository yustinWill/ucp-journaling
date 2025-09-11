<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementLogService
{
    private $table_name = 't_stock_movement_logs';

    public function create(array $data): int
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'movement_date' => $data['movement_date'],
            'movement_type' => $data['movement_type'],
            'reference_type' => $data['reference_type'],
            'reference_number' => $data['reference_number'],
            'warehouse_id' => $data['warehouse_id'],
            'warehouse_code' => $data['warehouse_code'],
            'warehouse_name' => $data['warehouse_name'],
            'product_id' => $data['product_id'],
            'product_code' => $data['product_code'],
            'product_name' => $data['product_name'],
            'unit' => $data['unit'],
            'stock_before' => $data['stock_before'],
            'movement_qty' => $data['movement_qty'],
            'stock_after' => $data['stock_after'],
            'unit_cost' => $data['unit_cost'] ?? 0,
            'total_value' => $data['total_value'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function createBulk(array $movements): bool
    {
        $insertData = [];
        foreach ($movements as $movement) {
            $insertData[] = [
                'company_code' => session('user_company_code'),
                'movement_date' => $movement['movement_date'],
                'movement_type' => $movement['movement_type'],
                'reference_type' => $movement['reference_type'],
                'reference_number' => $movement['reference_number'],
                'warehouse_id' => $movement['warehouse_id'],
                'warehouse_code' => $movement['warehouse_code'],
                'warehouse_name' => $movement['warehouse_name'],
                'product_id' => $movement['product_id'],
                'product_code' => $movement['product_code'],
                'product_name' => $movement['product_name'],
                'unit' => $movement['unit'],
                'stock_before' => $movement['stock_before'],
                'movement_qty' => $movement['movement_qty'],
                'stock_after' => $movement['stock_after'],
                'unit_cost' => $movement['unit_cost'] ?? 0,
                'total_value' => $movement['total_value'] ?? 0,
                'notes' => $movement['notes'] ?? null,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];
        }

        return DB::table($this->table_name)->insert($insertData);
    }

    public function findByReference(string $reference_type, string $reference_number)
    {
        return DB::table($this->table_name.' as sml')
            ->select([
                'sml.*',
                'u.name as created_by_name',
            ])
            ->leftJoin('users as u', 'sml.created_by', '=', 'u.id')
            ->where('sml.reference_type', $reference_type)
            ->where('sml.reference_number', $reference_number)
            ->where('sml.company_code', session('user_company_code'))
            ->orderBy('sml.created_at', 'asc')
            ->get();
    }

    public function findByWarehouseAndProduct(int $warehouse_id, int $product_id, $limit = 50)
    {
        return DB::table($this->table_name.' as sml')
            ->select([
                'sml.*',
                'u.name as created_by_name',
            ])
            ->leftJoin('users as u', 'sml.created_by', '=', 'u.id')
            ->where('sml.warehouse_id', $warehouse_id)
            ->where('sml.product_id', $product_id)
            ->where('sml.company_code', session('user_company_code'))
            ->orderBy('sml.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findAll($limit = 100)
    {
        return DB::table($this->table_name.' as sml')
            ->select([
                'sml.*',
                'u.name as created_by_name',
            ])
            ->leftJoin('users as u', 'sml.created_by', '=', 'u.id')
            ->where('sml.company_code', session('user_company_code'))
            ->orderBy('sml.created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Helper method to record a movement with automatic before/after stock calculation
     */
    public function recordMovement($warehouseId, $productId, $movementType, $referenceType, $referenceNumber, $movementQty, $notes = null, $movementDate = null, $unitCost = 0)
    {
        // Get current stock from product table
        $product = DB::table('m_products')->where('id', $productId)->first();
        $currentStock = $product ? $product->total_stock : 0;

        // Get warehouse and product details
        $warehouse = DB::table('m_warehouses')->where('id', $warehouseId)->first();
        $unit = DB::table('m_units as u')
            ->join('m_products as p', 'p.f_unit_id', '=', 'u.id')
            ->where('p.id', $productId)
            ->first();

        // Calculate new stock
        $isInMovement = in_array($movementType, ['PURCHASE_IN', 'ADJUSTMENT_IN', 'TRANSFER_IN', 'RETURN_IN']);
        $newStock = $isInMovement ? $currentStock + $movementQty : $currentStock - $movementQty;

        return $this->create([
            'movement_date' => $movementDate ?? now(),
            'movement_type' => $movementType,
            'reference_type' => $referenceType,
            'reference_number' => $referenceNumber,
            'warehouse_id' => $warehouseId,
            'warehouse_code' => $warehouse->code ?? '',
            'warehouse_name' => $warehouse->name ?? '',
            'product_id' => $productId,
            'product_code' => $product->code ?? '',
            'product_name' => $product->name ?? '',
            'unit' => $unit->code ?? '',
            'stock_before' => $currentStock,
            'movement_qty' => $movementQty,
            'stock_after' => $newStock,
            'unit_cost' => $unitCost,
            'total_value' => $movementQty * $unitCost,
            'notes' => $notes,
        ]);
    }
}
