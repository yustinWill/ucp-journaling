<?php

namespace App\Domains\Product\Services;

use App\Enums\GeneralStatuses;
use App\Enums\StockStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private $table_name = 'm_products';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code')
            ->where('status', GeneralStatuses::ACTIVE)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            "company_code" => session('user_company_code'),
            "code" => $data['code'],
            "name" => $data['name'],
            "description" => $data['description'] ?? null,
            "f_unit_id" => $data['unit_id'],
            "f_category_id" => $data['category_id'],
            "main_image" => $data['image'] ?? null,
            "type" => $data['type'],
            "is_item_purchased" => $data['purchase_enabled'] === "true" ? true : false,
            "unit_purchase_price" => $data['purchase_price'] ?? null,
            "f_purchase_coa_code" => $data['purchase_account'] ?? null,
            "f_purchase_tax_id" => $data['purchase_tax'] ?? null,
            "is_item_sold" => $data['sales_enabled'] === "true" ? true : false,
            "unit_sell_price" => $data['sales_price'] ?? null,
            "f_sell_coa_code" => $data['sales_account'] ?? null,
            "f_sell_tax_id" => $data['sales_tax'] ?? null,
            "monitor_stock" => $data['inventory_enabled'] === "true" ? true : false,
            "minimum_stock" => $data['minimum_stock'] ?? null,
            "f_inventory_coa_code" => $data['inventory_account'] ?? null,
            "track_qty_only" => isset($data['track_quantity']) ? (bool) $data['track_quantity'] : false,
            "track_batch" => isset($data['track_batch']) ? (bool) $data['track_batch'] : false,
            "f_tag_codes" => isset($data['tags']) ? json_encode($data['tags']) : null,
            "internal_memo" => $data['memo'] ?? '',
            "total_stock" => 0,
            "qty_available" => 0,
            "average_price" => 0,
            "f_stock_status_code" => StockStatuses::OUT_OF_STOCK,
            "created_by" => Auth::id(),
            "created_at" => now()
        ]);
    }
}
