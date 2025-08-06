<?php

namespace App\Domains\Contact\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContactProductService
{
    private $table_name = 'm_contact_products';

    public function create(array $data)
    {
        DB::table($this->table_name)->insertGetId([
            'f_contact_id' => $data['contact_id'],
            'f_product_code' => $data['product_code'],
            'price_per_qty' => $data['price_per_qty'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function createMany(array $data)
    {
        foreach ($data as &$item) {
            $item['created_by'] = Auth::id();
            $item['created_at'] = now();
        }
        return DB::table($this->table_name)->insert($data);
    }
}
