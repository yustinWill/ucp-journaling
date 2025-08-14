<?php

namespace App\Domains\Product\Services;

use App\Enums\StockStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCompositionService
{
    private $table_name = 'm_product_compositions';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            "f_product_id" => $data['product_id'],
            "f_product_composition_product_id" => $data['composition_product_id'],
            "qty" => $data['qty'],
            "price" => $data['price'],
            "created_by" => Auth::id(),
            "created_at" => now()
        ]);
    }
}
