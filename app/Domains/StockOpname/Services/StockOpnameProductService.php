<?php

namespace App\Domains\StockOpname\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOpnameProductService
{
    private $table_name = 't_stock_opname_products';

    public function findAllBasedOnId($id)
    {
        return DB::table($this->table_name . ' AS t')
            ->select('t.*', 'p.name as product_name', 'p.code as product_code', 'u.name as unit_name')
            ->join('m_products as p', 'p.id', '=', 't.f_product_id')
            ->join('m_units as u', 'u.id', '=', 'p.f_unit_id')
            ->where('f_stock_opname_id', $id)
            ->orderBy('id', 'ASC')
            ->get();
    }

    // public function findOne($id)
    // {
    //     $data = DB::table($this->table_name . ' as t')
    //         ->select('t.*', 'w.name as warehouse_name', 'u.name as pic_name')
    //         ->leftJoin('m_warehouses as w', 't.warehouse_id', '=', 'w.id')
    //         ->leftJoin('users as u', 't.pic_id', '=', 'u.id')
    //         ->where('t.id', $id)
    //         ->where('t.company_code', session('user_company_code'))
    //         ->first();

    //     if ($data && $data->f_tag_codes) {
    //         $data->f_tag_codes = json_decode($data->f_tag_codes); // Returns objects for view compatibility
    //     }

    //     return $data;
    // }

    public function createMany(array $data): void
    {
        $insert_data = [];
        foreach ($data as $row) {
            $insert_data = [
                'f_stock_opname_id' => $row['f_stock_opname_id'],
                'f_product_id' => $row['f_product_id'],
                'system_stock' => $row['system_stock'],
                'physical_qty' => $row['physical_qty'],
                'variance' => $row['variance'],
                'note' => $row['note'],
                'created_by' => Auth::id(),
                'created_at' => now()
            ];
        }
        DB::table($this->table_name)->insert($insert_data);
    }

    // public function update(int $id, array $data): int
    // {
    //     $updateData = [
    //         'adjustment_date' => $data['adjustment_date'],
    //         'adjustment_type' => $data['adjustment_type'],
    //         'warehouse_id' => $data['warehouse_id'],
    //         'pic_id' => $data['pic_id'],
    //         'status' => $data['status'],
    //         'description' => $data['description'],
    //         'memo' => $data['memo'] ?? null,
    //         'f_tag_codes' => json_encode($data['tags'] ?? []),
    //         'reference_number' => $data['reference_number'] ?? null,
    //         'total_items' => $data['total_items'] ?? 0,
    //         'total_adjustment_qty' => $data['total_adjustment_qty'] ?? 0,
    //         'total_adjustment_value' => $data['total_adjustment_value'] ?? 0,
    //         'updated_by' => Auth::id(),
    //         'updated_at' => now()
    //     ];

    //     return DB::table($this->table_name)
    //         ->where('id', $id)
    //         ->where('company_code', session('user_company_code'))
    //         ->update($updateData);
    // }
}
