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
        return DB::table($this->table_name . ' as m_products')
            ->select('m_products.id', 'm_products.name', 'm_products.code', 'm_units.name as unit_name')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->orderBy('m_products.name', 'asc')
            ->get();
    }

    public function findOne($id)
    {
        return DB::table($this->table_name . ' as m_products')
            ->select('m_products.*', 'm_units.name as unit_name')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->where('m_products.id', $id)
            ->first();
    }

    function getValueWithBundlePrefix($data, $key)
    {
        return $data["bundle_{$key}"] ?? $data[$key] ?? null;
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
            "is_item_purchased" => $this->getValueWithBundlePrefix($data, 'purchase_enabled')  === "true" ? true : false,
            "unit_purchase_price" => $this->getValueWithBundlePrefix($data, 'purchase_price'),
            "f_purchase_coa_code" => $this->getValueWithBundlePrefix($data, 'purchase_account'),
            "f_purchase_tax_id" => $this->getValueWithBundlePrefix($data, 'purchase_tax'),

            "is_item_sold" => $this->getValueWithBundlePrefix($data, 'sales_enabled')  === "true" ? true : false,
            "unit_sell_price" => $this->getValueWithBundlePrefix($data, 'sales_price'),
            "f_sell_coa_code" => $this->getValueWithBundlePrefix($data, 'sales_account'),
            "f_sell_tax_id" => $this->getValueWithBundlePrefix($data, 'sales_tax'),

            "monitor_stock" => $this->getValueWithBundlePrefix($data, 'inventory_enabled') === "true" ? true : false,
            "minimum_stock" => $this->getValueWithBundlePrefix($data, 'minimum_stock'),
            "f_inventory_coa_code" => $this->getValueWithBundlePrefix($data, 'inventory_account'),

            "track_qty_only" => isset($data['track_quantity']) && $data['track_quantity'] === "true" ? true : false,
            "track_batch" => isset($data['track_batch']) && $data['track_batch'] === "true" ? true : false,
            "f_tag_codes" => isset($data['tags']) ? json_encode($data['tags']) : null,
            "internal_memo" => $data['memo'] ?? '',
            "total_stock" => 0,
            "qty_available" => 0,
            "average_price" => 0,
            "f_stock_status_code" => StockStatuses::OUT_OF_STOCK,
            "status" => GeneralStatuses::ACTIVE,
            "created_by" => Auth::id(),
            "created_at" => now()
        ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.code',
                't.f_category_id',
                'mc.name AS category_name',
                't.total_stock',
                't.qty_available',
                't.minimum_stock',
                't.f_unit_id',
                'mu.name AS unit_name',
                't.average_price',
                't.unit_purchase_price',
                't.unit_sell_price',
                't.status',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            ])
            ->join('m_categories as mc', 't.f_category_id', '=', 'mc.id')
            ->join('m_units as mu', 't.f_unit_id', '=', 'mu.id')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');

        // Filter by status
        $query->where('mc.company_code', session('user_company_code'));
        $query->where('mc.status', GeneralStatuses::ACTIVE);

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
