<?php

namespace App\Domains\StockOpname\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOpnameService
{
    private $table_name = 't_stock_opnames';

    // public function findAll()
    // {
    //     return DB::table($this->table_name)
    //         ->where('company_code', session('user_company_code'))
    //         ->orderBy('created_at', 'DESC')
    //         ->get();
    // }

    // public function findOne($id)
    // {
    //     $data = DB::table($this->table_name . ' as t')
    //         ->select('t.*', 'w.name as warehouse_name', 'u.name as pic_name')
    //         ->leftJoin('m_warehouses as w', 't.warehouse_id', '=', 'w.id')
    //         ->leftJoin('users as u', 't.pic_id', '=', 'u.id')
    //         ->where('t.id', $id)
    //         ->where('t.company_code', session('user_company_code'))
    //         ->first();

    //     if ($data && $data->f_tag_codes) {
    //         $data->f_tag_codes = json_decode($data->f_tag_codes); // Returns objects for view compatibility
    //     }

    //     return $data;
    // }

    public function create(array $data): int
    {
        $data = [
            'company_code' => session('user_company_code'),
            'stock_opname_no' => $data['stock_opname_no'],
            'transaction_date' => $data['transaction_date'],
            'f_warehouse_id' => $data['f_warehouse_id'],
            'f_pic_id' => $data['f_pic_id'],
            'status' => $data['status'],
            'description' => $data['description'],
            'memo' => $data['memo'] ?? null,
            'f_tag_codes' => json_encode($data['f_tag_codes'] ?? []),
            'created_at' => now(),
            'created_by' => Auth::id(),
        ];

        return DB::table($this->table_name)->insertGetId($data);
    }

    // public function update(int $id, array $data): int
    // {
    //     $updateData = [
    //         'adjustment_date' => $data['adjustment_date'],
    //         'adjustment_type' => $data['adjustment_type'],
    //         'warehouse_id' => $data['warehouse_id'],
    //         'pic_id' => $data['pic_id'],
    //         'status' => $data['status'],
    //         'description' => $data['description'],
    //         'memo' => $data['memo'] ?? null,
    //         'f_tag_codes' => json_encode($data['tags'] ?? []),
    //         'reference_number' => $data['reference_number'] ?? null,
    //         'total_items' => $data['total_items'] ?? 0,
    //         'total_adjustment_qty' => $data['total_adjustment_qty'] ?? 0,
    //         'total_adjustment_value' => $data['total_adjustment_value'] ?? 0,
    //         'updated_by' => Auth::id(),
    //         'updated_at' => now()
    //     ];

    //     return DB::table($this->table_name)
    //         ->where('id', $id)
    //         ->where('company_code', session('user_company_code'))
    //         ->update($updateData);
    // }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as so')
            ->select([
                'so.id',
                'so.stock_opname_no',
                'so.transaction_date',
                'w.name as warehouse_name',
                'u.name as pic_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
                'so.count_product',
                'so.total_system_stock',
                'so.total_physical_stock',
                'so.total_variance',
                'so.status',
            ])
            ->leftJoin('m_warehouses as w', 'so.f_warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'so.f_pic_id', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(so.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('so.company_code', session('user_company_code'));

        if ($date = request()->input('date')) {
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('so.transaction_date', [$dateFrom, $dateTo]);
            }
        }
        if ($warehouse_filter = request()->input('warehouse')) {
            $query->where('so.f_warehouse_id', $warehouse_filter);
        }

        if ($status_filter = request()->input('status')) {
            $query->where('so.status', $status_filter);
        }

        if ($no_opname = request()->input('no_opname')) {
            $query->where('so.stock_opname_no', 'like', "%{$no_opname}%");
        }


        $query->groupBy('so.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('so.created_at', 'DESC');
        $data = $query->get();

        foreach ($data as $row) {
            if ($row->tags) {
                $tags = json_decode($row->tags, true);
                $row->tags = collect($tags)->unique('code')->values()->all();
            }
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    // public function getProductsInWarehouse($warehouse_id, $category_filter = null, $search_filter = null)
    // {
    //     // For now, return products with zero stock since stock movement tables don't exist yet
    //     // This allows the stock adjustment form to work for testing purposes
    //     $query = DB::table('m_products as p')
    //         ->select([
    //             'p.id',
    //             'p.code',
    //             'p.name',
    //             'u.code as unit', // Get unit from units table
    //             'pc.name as category_name',
    //             DB::raw('COALESCE(p.total_stock, 0) as current_stock'), // Use existing total_stock field
    //             DB::raw('COALESCE(p.average_price, p.unit_purchase_price, 0) as unit_cost')
    //         ])
    //         ->leftJoin('m_categories as pc', 'p.f_category_id', '=', 'pc.id')
    //         ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
    //         ->where('p.company_code', session('user_company_code'))
    //         ->where('p.status', 'ACTIVE');

    //     if ($category_filter) {
    //         $query->where('p.f_category_id', $category_filter);
    //     }

    //     if ($search_filter) {
    //         $query->where(function ($q) use ($search_filter) {
    //             $q->where('p.code', 'like', "%{$search_filter}%")
    //                 ->orWhere('p.name', 'like', "%{$search_filter}%");
    //         });
    //     }

    //     return $query->orderBy('p.name')->get();
    // }

    // private function generateAdjustmentNumber()
    // {
    //     $today = now()->format('Ymd');
    //     $prefix = 'SA-' . $today . '-';

    //     $lastNumber = DB::table($this->table_name)
    //         ->where('company_code', session('user_company_code'))
    //         ->where('adjustment_number', 'like', $prefix . '%')
    //         ->orderBy('id', 'desc')
    //         ->value('adjustment_number');

    //     if ($lastNumber) {
    //         $lastSequence = (int) substr($lastNumber, -3);
    //         $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
    //     } else {
    //         $newSequence = '001';
    //     }

    //     return $prefix . $newSequence;
    // }

    // public function getAdjustmentItems($stockAdjustmentId)
    // {
    //     return DB::table('t_stock_adjustment_items as sai')
    //         ->select([
    //             'sai.id',
    //             'sai.product_id',
    //             'sai.product_code',
    //             'sai.product_name',
    //             'sai.unit',
    //             'sai.stock_before',
    //             'sai.adjustment_qty',
    //             'sai.stock_after',
    //             'sai.unit_cost',
    //             'sai.total_value',
    //             'sai.reason',
    //         ])
    //         ->where('sai.stock_adjustment_id', $stockAdjustmentId)
    //         ->where('sai.company_code', session('user_company_code'))
    //         ->orderBy('sai.id')
    //         ->get();
    // }

    // public function getLatestByMonth($year, $month)
    // {
    //     return DB::table($this->table_name)
    //         ->where('company_code', session('user_company_code'))
    //         ->whereYear('adjustment_date', $year)
    //         ->whereMonth('adjustment_date', $month)
    //         ->orderBy('created_at', 'desc')
    //         ->first(['id', 'adjustment_number']);
    // }
}
