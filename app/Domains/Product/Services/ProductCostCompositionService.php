<?php

namespace App\Domains\Product\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCostCompositionService
{
    private $table_name = 'm_product_composition_cost';

    public function create(array $data)
    {
        $insert_data = [];
        foreach ($data as $row) {
            $insert_data[] = [
                'f_product_id' => $row['f_product_id'],
                'account_code' => $row['account_code'],
                'cost_value' => $row['cost_value'],
            ];
        }

        return DB::table($this->table_name)->insert($insert_data);
    }
    
    public function update($id, array $data)
    {
        return DB::table($this->table_name)->where('id', $id)->update([
            'account_code' => $data['account_code'],
            'cost_value' => $data['cost_value']
        ]);
    }
    public function delete($product_id, $id)
    {
        return DB::table($this->table_name)->where('id', $id)->where('f_product_id', $product_id)->delete();
    }
    public function findAllBasedOnProductId($product_id)
    {
        return DB::table($this->table_name.' as t')
            ->select('t.*')
            ->where('t.f_product_id', $product_id)
            ->get();
    }
    
    public function getSumBasedOnProductId($product_id)
    {
        return DB::table($this->table_name.' as t')
            ->where('t.f_product_id', $product_id)
            ->sum('cost_value');
    }

}
