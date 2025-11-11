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

}
