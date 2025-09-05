<?php

namespace App\Domains\Product\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCompositionService
{
    private $table_name = 'm_product_compositions';

    public function create(array $data)
    {
        $insert_data = [];
        foreach ($data as $row) {
            $insert_data[] = [
                'f_product_id' => $row['f_product_id'],
                'f_product_composition_product_id' => $row['f_product_composition_product_id'],
                'qty' => $row['qty'],
                'f_unit_id' => $row['f_unit_id'],
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];
        }

        return DB::table($this->table_name)->insert($insert_data);
    }

    public function update($id, array $data)
    {
        return DB::table($this->table_name)->where('id', $id)->update([
            'f_product_composition_product_id' => $data['f_product_composition_product_id'],
            'qty' => $data['qty'],
            'f_unit_id' => $data['f_unit_id'],
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    public function findAllBasedOnProductId($product_id)
    {
        return DB::table($this->table_name.' as t')
            ->select('t.*', 'prd.total_stock', 'prd.name as product_name', 'unt.name as unit_name')
            ->join('m_products as prd', 'prd.id', '=', 't.f_product_composition_product_id')
            ->join('m_units as unt', 'unt.id', '=', 't.f_unit_id')
            ->where('t.f_product_id', $product_id)
            ->get();
    }

    public function delete($product_id, $id)
    {
        return DB::table($this->table_name)->where('id', $id)->where('f_product_id', $product_id)->delete();
    }
}
