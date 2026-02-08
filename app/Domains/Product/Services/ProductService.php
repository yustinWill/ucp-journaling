<?php

namespace App\Domains\Product\Services;

use App\Enums\StockStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class ProductService
{
    private $table_name = 'm_products';

    public function findAll()
    {
        return DB::table($this->table_name.' as m_products')
            ->select('m_products.id', 'm_products.name', 'm_products.code', 'm_units.name as unit_name')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->orderBy('m_products.name', 'asc')
            ->get();
    }

    public function findAllAllowPurchase()
    {
        return DB::table($this->table_name.' as m_products')
            ->select('m_products.id', 'm_products.name', 'm_products.code', 'm_products.unit_purchase_price', 'm_units.name as unit_name')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->where('m_products.is_item_purchased', '=', 1)
            ->orderBy('m_products.name', 'asc')
            ->get();
    }

    public function findAllBundle()
    {
        return DB::table($this->table_name.' as m_products')
            ->select('m_products.id', 'm_products.name', 'm_products.code', 'm_units.name as unit_name','m_products.bundle_batch_qty')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->where('m_products.type', '=', 'BUNDLE')
            ->orderBy('m_products.name', 'asc')
            ->get();
    }


    public function findAllBundlePacking()
    {
        return DB::table($this->table_name.' as m_products')
            ->select('m_products.id', 'm_products.name', 'm_products.code', 'm_units.name as unit_name','m_products.bundle_batch_qty')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->where('m_products.type', '=', 'BUNDLE')
            ->where('m_products.is_packaging', '!=', 1)
            ->orderBy('m_products.name', 'asc')
            ->get();
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as m_products')
            ->select([
                'm_products.*',
                'mu.name as unit_name',
                'mc.name as category_name',
                'coa_purchase.name as purchase_coa_name',
                'tax_purchase.name as purchase_tax_name',
                'coa_sell.name as sell_coa_name',
                'tax_sell.name as sell_tax_name',
                'coa_inventory.name as inventory_coa_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('m_categories as mc', 'm_products.f_category_id', '=', 'mc.id')
            ->join('m_units as mu', 'm_products.f_unit_id', '=', 'mu.id')

            ->leftJoin('m_coas as coa_purchase', 'm_products.f_purchase_coa_code', '=', 'coa_purchase.id')
            ->leftJoin('m_taxes as tax_purchase', 'm_products.f_purchase_tax_id', '=', 'tax_purchase.id')
            ->leftJoin('m_coas as coa_sell', 'm_products.f_sell_coa_code', '=', 'coa_sell.id')
            ->leftJoin('m_taxes as tax_sell', 'm_products.f_sell_tax_id', '=', 'tax_sell.id')
            ->leftJoin('m_coas as coa_inventory', 'm_products.f_inventory_coa_code', '=', 'coa_inventory.id')
            ->leftJoin(DB::raw("JSON_TABLE(m_products.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('m_products.id', $id)
            ->groupBy('m_products.id')
            ->first();

        // Return null if product not found
        if (!$data) {
            return null;
        }

        if ($data->tags) {
            $data->tags = json_decode($data->tags);
        }
        if ($data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes, true);
        }
        if ($data->main_image) {
            $data->main_image = Storage::disk('public')->url($data->main_image);
        }

        return $data;
    }

    public function getValueWithBundlePrefix($data, $key)
    {
        return $data["bundle_{$key}"] ?? $data[$key] ?? null;
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'f_unit_id' => $data['unit_id'],
            'f_category_id' => $data['category_id'],
            'main_image' => $data['image'] ?? null,
            'type' => $data['type'],
            'is_packaging' => $data['type'] === 'bundle' && isset($data['is_packaging']) && $data['is_packaging'] === 'true' ? true : false,
            'is_item_purchased' => $this->getValueWithBundlePrefix($data, 'purchase_enabled') === 'true' ? true : false,
            'unit_purchase_price' => $this->getValueWithBundlePrefix($data, 'purchase_price'),
            'f_purchase_coa_code' => $this->getValueWithBundlePrefix($data, 'purchase_account'),
            'f_purchase_tax_id' => $this->getValueWithBundlePrefix($data, 'purchase_tax'),

            'is_item_sold' => $this->getValueWithBundlePrefix($data, 'sales_enabled') === 'true' ? true : false,
            'unit_sell_price' => $this->getValueWithBundlePrefix($data, 'sales_price'),
            'f_sell_coa_code' => $this->getValueWithBundlePrefix($data, 'sales_account'),
            'f_sell_tax_id' => $this->getValueWithBundlePrefix($data, 'sales_tax'),

            'monitor_stock' => $this->getValueWithBundlePrefix($data, 'inventory_enabled') === 'true' ? true : false,
            'minimum_stock' => $this->getValueWithBundlePrefix($data, 'minimum_stock'),
            'f_inventory_coa_code' => $this->getValueWithBundlePrefix($data, 'inventory_account'),
            'bundle_batch_qty' => $data['type'] === 'bundle' ? ($data['bundle_batch_qty'] ?? null) : null,
            'track_qty_only' => isset($data['track_quantity']) && $data['track_quantity'] === 'true' ? true : false,
            'track_batch' => isset($data['track_batch']) && $data['track_batch'] === 'true' ? true : false,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            'total_stock' => 0,
            'qty_available' => 0,
            'average_price' => 0,
            'f_stock_status_code' => StockStatuses::OUT_OF_STOCK,
            'status' => $data['status'],
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        $update_data = [
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'f_unit_id' => $data['unit_id'],
            'f_category_id' => $data['category_id'],
            'type' => $data['type'],
            'is_packaging' => $data['type'] === 'bundle' && isset($data['is_packaging']) && $data['is_packaging'] === 'true' ? true : false,
            'is_item_purchased' => $this->getValueWithBundlePrefix($data, 'purchase_enabled') === 'true' ? true : false,
            'unit_purchase_price' => $this->getValueWithBundlePrefix($data, 'purchase_price'),
            'f_purchase_coa_code' => $this->getValueWithBundlePrefix($data, 'purchase_account'),
            'f_purchase_tax_id' => $this->getValueWithBundlePrefix($data, 'purchase_tax'),

            'is_item_sold' => $this->getValueWithBundlePrefix($data, 'sales_enabled') === 'true' ? true : false,
            'unit_sell_price' => $this->getValueWithBundlePrefix($data, 'sales_price'),
            'f_sell_coa_code' => $this->getValueWithBundlePrefix($data, 'sales_account'),
            'f_sell_tax_id' => $this->getValueWithBundlePrefix($data, 'sales_tax'),
            'bundle_batch_qty' => $data['type'] === 'bundle' ? ($data['bundle_batch_qty'] ?? null) : null,
            'monitor_stock' => $this->getValueWithBundlePrefix($data, 'inventory_enabled') === 'true' ? true : false,
            'minimum_stock' => $this->getValueWithBundlePrefix($data, 'minimum_stock'),
            'f_inventory_coa_code' => $this->getValueWithBundlePrefix($data, 'inventory_account'),

            'track_qty_only' => isset($data['track_quantity']) && $data['track_quantity'] === 'true' ? true : false,
            'track_batch' => isset($data['track_batch']) && $data['track_batch'] === 'true' ? true : false,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            'status' => $data['status'],
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ];

        if (isset($data['image'])) {
            $update_data['main_image'] = $data['image'];
        }

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($update_data);
    }

    public function updateStock(int $id): int
    {
        $totalStock = DB::table('t_stock_movement_logs as sml1')
            ->where('sml1.product_id', $id)
            ->where('sml1.company_code', session('user_company_code'))
            ->whereRaw('sml1.id = (
                SELECT MAX(sml2.id)
                FROM t_stock_movement_logs sml2
                WHERE sml2.product_id = sml1.product_id
                  AND sml2.warehouse_id = sml1.warehouse_id
                  AND sml2.company_code = sml1.company_code
            )')
            ->sum('sml1.stock_after');

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'total_stock' => $totalStock,
            ]);
    }

    /**
     * Update product average price using weighted average formula
     * Formula: ((purchase_price × qty_purchased) + (current_avg_price × current_stock)) / (qty_purchased + current_stock)
     *
     * @param int $productId
     * @param float $purchasePrice - Price per unit of the new purchase
     * @param float $qtyPurchased - Quantity being purchased
     * @return bool
     */
    public function updateAveragePriceWeighted(int $productId, float $purchasePrice, float $qtyPurchased): bool
    {
        // Get current product data
        $product = DB::table($this->table_name)
            ->where('id', $productId)
            ->first();

        if (!$product) {
            return false;
        }

        $currentStock = (float) $product->total_stock;
        $currentAvgPrice = (float) ($product->average_price ?? 0);

        // Calculate weighted average HPP
        // Formula: ((harga beli × qty beli) + (harga stock sebelumnya × qty sisa stock sebelumnya)) / (stock beli + stock sisa)
        $totalValue = ($purchasePrice * $qtyPurchased) + ($currentAvgPrice * $currentStock);
        $totalQty = $qtyPurchased + $currentStock;

        $newAveragePrice = $totalQty > 0 ? $totalValue / $totalQty : $purchasePrice;

        // Update average price
        return DB::table($this->table_name)
            ->where('id', $productId)
            ->update([
                'average_price' => $newAveragePrice,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]) > 0;
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                't.f_category_id',
                'mc.name AS category_name',
                't.total_stock',
                // Calculate qty_available from stock movement logs (same logic as Persediaan tab)
                DB::raw('(SELECT COALESCE(SUM(
                    CASE
                        WHEN sml.movement_type LIKE "%_IN" THEN sml.movement_qty
                        WHEN sml.movement_type LIKE "%_OUT" THEN -sml.movement_qty
                        ELSE 0
                    END
                ), 0) FROM t_stock_movement_logs sml WHERE sml.product_id = t.id AND sml.company_code = "'.session('user_company_code').'") AS qty_available'),
                't.minimum_stock',
                't.f_unit_id',
                'mu.name AS unit_name',
                't.average_price',
                't.unit_purchase_price',
                't.unit_sell_price',
                't.status',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('m_categories as mc', 't.f_category_id', '=', 'mc.id')
            ->leftJoin('m_units as mu', 't.f_unit_id', '=', 'mu.id')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');

        // Filter by status
        $query->where('t.company_code', session('user_company_code'));

        if ($category_filter = request()->input('category_filter')) {
            $query->where('t.f_category_id', $category_filter);
        }

        if ($stock_status_filter = request()->input('stock_status_filter')) {
            $query->where('t.f_stock_status_code', $stock_status_filter);
        }

        if ($search = request()->input('search')) {
            $query->where('t.name', 'like', "%{$search}%");
        }
        $query->groupBy('t.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('t.id', 'ASC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
