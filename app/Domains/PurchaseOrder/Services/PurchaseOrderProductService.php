<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderProductService
{
    private $table_name = 't_purchase_order_products';

    public function create($po_id, array $data)
    {
        return DB::table($this->table_name)->insertGetId([
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

    public function update($po_id, $id, array $data)
    {
        return DB::table($this->table_name)
            ->where('f_purchase_order_id', $po_id)
            ->where('id', $id)
            ->update([
                "f_product_id" => $data['product_id'],
                "qty" => $data['qty'],
                "f_unit_id" => $data['unit_id'],
                "price_per_qty" => $data['price_per_qty'],
                "f_tax_id" => $data['tax_id'],
                "total" => $data['total'],
                "updated_at" => now(),
                "updated_by" => Auth::id(),
            ]);
    }

    public function delete($po_id, $id)
    {
        return DB::table($this->table_name)
            ->where('f_purchase_order_id', $po_id)
            ->where('id', $id)
            ->delete();
    }

    public function findAllBasedOnPOId($po_id)
    {
        return DB::table($this->table_name . ' as pop')
            ->select(
                'pop.*',
                'mp.code AS product_code',
                'mp.name AS product_name',
                'mu.code AS unit_code',
                'mu.name AS unit_name',
                'mt.code AS tax_code',
                'mt.name AS tax_name',
                'mt.rate AS tax_rate'
            )
            ->leftJoin('m_products as mp', 'mp.id', '=', 'pop.f_product_id')
            ->leftJoin('m_units as mu', 'mu.id', '=', 'pop.f_unit_id')
            ->leftJoin('m_taxes as mt', 'mt.id', '=', 'pop.f_tax_id')
            ->where('pop.f_purchase_order_id', $po_id)
            ->orderBy('pop.id', 'ASC')
            ->get();
    }
}
