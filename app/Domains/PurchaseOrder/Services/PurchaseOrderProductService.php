<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderProductService
{
    private $table_name = 't_purchase_order_products';

    public function create($po_id, array $data)
    {
        return DB::table($this->table_name)->insert([
            "f_purchase_order_id" => $po_id,
            "f_product_id" => $data['product_id'],
            "qty" => $data['qty'],
            "f_unit_id" => $data['unit_id'],
            "price_per_qty" => $data['price_per_qty'],
            "f_tax_id" => $data['tax_id'],
            "total" => $data['total'],
            "created_at" => now(),
            "created_by" => Auth::id(),
        ]);
    }
}
