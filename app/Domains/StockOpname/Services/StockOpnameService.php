<?php

namespace App\Domains\StockAdjustment\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockOpnameService
{
    private $table_name = 't_stock_opnames';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name . ' as t')
            ->select('t.*', 'w.name as warehouse_name', 'u.name as pic_name')
            ->leftJoin('m_warehouses as w', 't.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 't.pic_id', '=', 'u.id')
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
        $stockAdjustmentData = [
            'company_code' => session('user_company_code'),
            'adjustment_number' => $this->generateAdjustmentNumber(),
            'adjustment_date' => $data['adjustment_date'],
            'adjustment_type' => $data['adjustment_type'],
            'warehouse_id' => $data['warehouse_id'],
            'pic_id' => $data['pic_id'],
            'status' => $data['status'],
            'description' => $data['description'],
            'memo' => $data['memo'] ?? null,
            'f_tag_codes' => json_encode($data['f_tag_codes'] ?? []),
            'reference_number' => $data['reference_number'] ?? null,
            'total_items' => $data['total_items'] ?? 0,
            'total_adjustment_qty' => $data['total_adjustment_qty'] ?? 0,
            'total_adjustment_value' => $data['total_adjustment_value'] ?? 0,
            'created_by' => Auth::id(),
            'created_at' => now()
        ];

        return DB::table($this->table_name)->insertGetId($stockAdjustmentData);
    }

    public function update(int $id, array $data): int
    {
        $updateData = [
            'adjustment_date' => $data['adjustment_date'],
            'adjustment_type' => $data['adjustment_type'],
            'warehouse_id' => $data['warehouse_id'],
            'pic_id' => $data['pic_id'],
            'status' => $data['status'],
            'description' => $data['description'],
            'memo' => $data['memo'] ?? null,
            'f_tag_codes' => json_encode($data['tags'] ?? []),
            'reference_number' => $data['reference_number'] ?? null,
            'total_items' => $data['total_items'] ?? 0,
            'total_adjustment_qty' => $data['total_adjustment_qty'] ?? 0,
            'total_adjustment_value' => $data['total_adjustment_value'] ?? 0,
            'updated_by' => Auth::id(),
            'updated_at' => now()
        ];

        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update($updateData);
    }

    public function findAllDatatable()
    {
        $tab = request()->input('tab', 'waiting_adjustment');

        $query = DB::table($this->table_name . ' as sa')
            ->select([
                'sa.id',
                'sa.adjustment_number',
                'sa.adjustment_date',
                'sa.adjustment_type',
                'sa.warehouse_id',
                'sa.status',
                'sa.description',
                'sa.total_items',
                'sa.total_adjustment_qty',
                'sa.total_adjustment_value',
                'sa.created_at as created_date',
                'w.name as warehouse_name',
                'u.name as pic_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', t.code, 'name', t.name, 'color', tc.code)) as tags")
            ])
            ->leftJoin('m_warehouses as w', 'sa.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'sa.pic_id', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(sa.f_tag_codes, '$[*]' COLUMNS(code VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })
            ->leftJoin('m_tags as t', 'tag_json.code', '=', 't.code')
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
            ->where('sa.company_code', session('user_company_code'));

        // Apply tab-based filtering
        // switch ($tab) {
        //     case 'waiting_adjustment':
        //         $query->whereIn('sa.status', ['DRAFT']);
        //         break;
        //     case 'waiting_approval':
        //         $query->whereIn('sa.status', ['WAITING_APPROVAL']);
        //         break;
        //     case 'finished':
        //         $query->whereIn('sa.status', ['APPROVED', 'COMPLETED', 'CANCELLED']);
        //         break;
        // }
        $query->whereIn('sa.status', ['APPROVED']);

        // Apply filters

        if ($date = request()->input('date_range_filter')) {
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('sa.adjustment_date', [$dateFrom, $dateTo]);
            }
        }
        if ($status_filter = request()->input('status_filter')) {
            $query->where('sa.status', $status_filter);
        }

        if ($type_filter = request()->input('type_filter')) {
            $query->where('sa.adjustment_type', $type_filter);
        }

        // if ($search = request()->input('search')) {
        //     $query->where(function ($q) use ($search) {
        //         $q->where('sa.adjustment_number', 'like', "%{$search}%")
        //             ->orWhere('sa.description', 'like', "%{$search}%")
        //             ->orWhere('w.name', 'like', "%{$search}%")
        //             ->orWhere('u.name', 'like', "%{$search}%");
        //     });
        // }

        $query->groupBy('sa.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('company_code', session('user_company_code'))->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('sa.created_at', 'DESC');
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

    public function getProductsInWarehouse($warehouse_id, $category_filter = null, $search_filter = null)
    {
        // For now, return products with zero stock since stock movement tables don't exist yet
        // This allows the stock adjustment form to work for testing purposes
        $query = DB::table('m_products as p')
            ->select([
                'p.id',
                'p.code',
                'p.name',
                'u.code as unit', // Get unit from units table
                'pc.name as category_name',
                DB::raw('COALESCE(p.total_stock, 0) as current_stock'), // Use existing total_stock field
                DB::raw('COALESCE(p.average_price, p.unit_purchase_price, 0) as unit_cost')
            ])
            ->leftJoin('m_categories as pc', 'p.f_category_id', '=', 'pc.id')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.company_code', session('user_company_code'))
            ->where('p.status', 'ACTIVE');

        if ($category_filter) {
            $query->where('p.f_category_id', $category_filter);
        }

        if ($search_filter) {
            $query->where(function ($q) use ($search_filter) {
                $q->where('p.code', 'like', "%{$search_filter}%")
                    ->orWhere('p.name', 'like', "%{$search_filter}%");
            });
        }

        return $query->orderBy('p.name')->get();
    }

    private function generateAdjustmentNumber()
    {
        $today = now()->format('Ymd');
        $prefix = 'SA-' . $today . '-';

        $lastNumber = DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('adjustment_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('adjustment_number');

        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        return $prefix . $newSequence;
    }

    public function getAdjustmentItems($stockAdjustmentId)
    {
        return DB::table('t_stock_adjustment_items as sai')
            ->select([
                'sai.id',
                'sai.product_id',
                'sai.product_code',
                'sai.product_name',
                'sai.unit',
                'sai.stock_before',
                'sai.adjustment_qty',
                'sai.stock_after',
                'sai.unit_cost',
                'sai.total_value',
                'sai.reason',
            ])
            ->where('sai.stock_adjustment_id', $stockAdjustmentId)
            ->where('sai.company_code', session('user_company_code'))
            ->orderBy('sai.id')
            ->get();
    }

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('adjustment_date', $year)
            ->whereMonth('adjustment_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id', 'adjustment_number']);
    }
}
