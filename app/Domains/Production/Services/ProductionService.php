<?php

namespace App\Domains\Production\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionService
{
    private $table_name = 't_productions';

    public function findAll(array $filters = [])
    {
        $query = DB::table($this->table_name . ' as p')
            ->select(
                'p.*',
                'bp.name as bundle_product_name',
                'bp.code as bundle_product_code',
                'w.name as warehouse_name',
                'w.code as warehouse_code',
                'u.name as pic_name'
            )
            ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->leftJoin('m_warehouses as w', 'p.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'p.pic_id', '=', 'u.id')
            ->where('p.company_code', session('user_company_code'))
            ->orderBy('p.created_at', 'DESC');

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('p.status', $filters['status']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('p.warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('p.production_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('p.production_date', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name . ' as p')
            ->select(
                'p.*',
                'bp.name as bundle_product_name',
                'bp.code as bundle_product_code',
                'w.name as warehouse_name',
                'w.code as warehouse_code',
                'u.name as pic_name',
                'created_user.name as created_by'
            )
            ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->leftJoin('m_warehouses as w', 'p.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'p.pic_id', '=', 'u.id')
            ->leftJoin('users as created_user', 'p.created_by', '=', 'created_user.id')
            ->where('p.id', $id)
            ->where('p.company_code', session('user_company_code'))
            ->first();

        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes); // Returns objects for view compatibility
        }

        return $data;
    }

    public function create(array $data): int
    {
        $productionData = [
            'company_code' => session('user_company_code'),
            'production_number' => $data['production_number'],
            'production_date' => $data['production_date'],
            'bundle_product_id' => $data['bundle_product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'pic_id' => $data['pic_id'],
            'quantity_planned' => $data['quantity_planned'],
            'quantity_produced' => $data['quantity_produced'] ?? 0,
            'status' => 'COMPLETED',
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'f_tag_codes' => json_encode($data['f_tag_codes'] ?? []),
            'total_raw_material_cost' => $data['total_raw_material_cost'] ?? 0,
            'total_production_expense' => $data['total_production_expense'] ?? 0,
            'total_production_cost' => $data['total_production_cost'] ?? 0,
            'total_items_consumed' => $data['total_items_consumed'] ?? 0,
            'created_by' => Auth::id(),
            // 'updated_by' => Auth::id(),
            'created_at' => now(),
            // 'updated_at' => now()
        ];

        return DB::table($this->table_name)->insertGetId($productionData);
    }

    public function update(int $id, array $data): int
    {
        $updateData = [
            'production_date' => $data['production_date'],
            'bundle_product_id' => $data['bundle_product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'pic_id' => $data['pic_id'],
            'quantity_planned' => $data['quantity_planned'],
            'quantity_produced' => $data['quantity_produced'] ?? 0,
            'status' => $data['status'],
            'description' => $data['description'] ?? null,
            'notes' => $data['notes'] ?? null,
            'reference_number' => $data['reference_number'] ?? null,
            'f_tag_codes' => json_encode($data['f_tag_codes'] ?? []),
            'total_raw_material_cost' => $data['total_raw_material_cost'] ?? 0,
            'total_production_expense' => $data['total_production_expense'] ?? 0,
            'total_production_cost' => $data['total_production_cost'] ?? 0,
            'total_items_consumed' => $data['total_items_consumed'] ?? 0,
            'updated_by_user_id' => Auth::id(),
            'updated_at' => now()
        ];

        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update($updateData);
    }

    public function delete(int $id): bool
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->delete() > 0;
    }

    public function updateStatus(int $id, string $status): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update([
                'status' => $status,
                'updated_by_user_id' => Auth::id(),
                'updated_at' => now()
            ]);
    }

    public function generateProductionNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        
        // Get next sequence number for this month
        $lastProduction = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('production_number', 'LIKE', "PROD-{$year}{$month}-%")
            ->orderBy('production_number', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastProduction) {
            $lastSequence = (int) substr($lastProduction->production_number, -4);
            $sequence = $lastSequence + 1;
        }

        return "PROD-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getBundleProductFormula(int $bundleProductId): array
    {
        // Get raw material components from m_product_elements
        return DB::table('m_product_elements as pe')
            ->select(
                'pe.*',
                'p.name as product_name',
                'p.code as product_code',
                'p.total_stock',
                'p.average_price',
                'u.name as unit_name',
                'u.code as unit_code'
            )
            ->leftJoin('m_products as p', 'pe.f_raw_material_product_id', '=', 'p.id')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('pe.f_bundle_product_id', $bundleProductId)
            ->where('pe.element_type', 'RAW_MATERIAL')
            ->get()
            ->toArray();
    }

    public function getBundleProductExpenses(int $bundleProductId): array
    {
        // Get production expenses from m_product_expenses
        return DB::table('m_product_expenses as pe')
            ->select('pe.*')
            ->where('pe.f_product_id', $bundleProductId)
            ->get()
            ->toArray();
    }

    public function canBeReversed(int $id): array
    {
        $production = $this->findOne($id);
        
        if (!$production) {
            return ['can_reverse' => false, 'reason' => 'Production not found'];
        }

        if ($production->status !== 'COMPLETED') {
            return ['can_reverse' => false, 'reason' => 'Only completed productions can be reversed'];
        }

        // Check if bundle product still has sufficient stock for reversal
        $bundleProduct = DB::table('m_products')
            ->where('id', $production->bundle_product_id)
            ->first();

        if (!$bundleProduct || $bundleProduct->total_stock < $production->quantity_produced) {
            return [
                'can_reverse' => false, 
                'reason' => 'Insufficient bundle product stock for reversal',
                'available_stock' => $bundleProduct->total_stock ?? 0,
                'required_stock' => $production->quantity_produced
            ];
        }

        return ['can_reverse' => true];
    }
}