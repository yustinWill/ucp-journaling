<?php

namespace App\Domains\StockOpname\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as t')
            ->select('t.*', 'w.id as warehouse_id', 'w.code as warehouse_code', 'w.name as warehouse_name', 'u.name as pic_name')
            ->leftJoin('m_warehouses as w', 't.f_warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 't.f_pic_id', '=', 'u.id')
            ->where('t.id', $id)
            ->where('t.company_code', session('user_company_code'))
            ->first();

        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes); // Returns objects for view compatibility
        }

        return $data;
    }

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
            'count_product' => $data['count_product'],
            'count_variance' => $data['count_variance'],
            'total_system_stock' => $data['total_system_stock'],
            'total_physical_stock' => $data['total_physical_stock'],
            'total_variance' => $data['total_variance'],
        ];

        return DB::table($this->table_name)->insertGetId($data);
    }

    public function updateStatus(int $id, string $status): int
    {
        $updateData = [
            'status' => $status,
            // 'updated_by' => Auth::id(),
            // 'updated_at' => now()
        ];

        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update($updateData);
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
        $query = DB::table($this->table_name.' as so')
            ->select([
                'so.id',
                'so.stock_opname_no',
                'so.transaction_date',
                'w.name as warehouse_name',
                'u.name as pic_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
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
                $row->tags = collect($tags)->unique('name')->values()->all();
            }
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}
