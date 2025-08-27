<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderService
{
    private $table_name = 't_purchase_orders';

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            "company_code" => session('user_company_code'),
            "transaction_code" => $data['reference_no'],
            "transaction_date" => $data['date'],
            "f_supplier_id" => $data['supplier_id'],
            "supplier_email" => $data['email'],
            "supplier_address" => $data['address'],
            "supplier_reference_no" => $data['supplier_reference'],
            "f_payment_term_id" => $data['payment_terms'],
            "payment_term_day_count" => $data['payment_term_day_count'] ?? null,
            "payment_term_due_date" => $data['payment_term_due_date'] ?? null,
            "f_warehouse_id" => $data['warehouse_id'],
            "description" => $data['description'],
            "f_tag_codes" => isset($data['tags']) ? json_encode($data['tags']) : null,
            "internal_memo" => $data['memo'] ?? null,
            "is_price_include_tax" => $data['include_tax'] ?? false,
            "subtotal" => $data['subtotal'] ?? null,
            "total_tax" => $data['tax_amount'] ?? null,
            "total_price_before_discount" => $data['total_before_discount'] ?? null,
            "discount" => $data['discount_amount'] ?? null,
            "grand_price" => $data['total'] ?? null,
            "attachment" => $data['attachment'] ?? null,
            "created_at" => now(),
            "created_by" => Auth::id()
        ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as po')
            ->select(
                'po.*',
                'mc.name AS supplier_name',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'po.f_supplier_id')
            ->leftJoin('users as u', 'po.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(po.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('po.company_code', session('user_company_code'));

        if ($date = request()->input('date_range_filter')) {
            // Check if date is a range (contains ' - ')
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('po.transaction_date', [$dateFrom, $dateTo]);
            }
        }

        if ($on_due_date_filter = request()->input('on_due_date_filter')) {
            $query->where('po.payment_term_due_date', $on_due_date_filter);
        }

        if ($suppllier_filter = request()->input('suppllier_filter')) {
            $query->where('po.f_supplier_id', $suppllier_filter);
        }

        if ($purchase_order_no_filter = request()->input('purchase_order_no_filter')) {
            $query->where('po.transaction_code', 'like', "%{$purchase_order_no_filter}%");
        }

        $query->groupBy('po.id');


        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('po.id', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name . ' as po')
            ->select(
                'po.*',
                'mc.name AS supplier_name',
                'pt.code AS payment_term_code',
                'pt.name AS payment_term_name',
                'pt.type AS payment_term_type',
                 'mw.code AS warehouse_code',
                'mw.name AS warehouse_name',
                'u.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags")
            )
            ->leftJoin('m_contacts as mc', 'mc.id', '=', 'po.f_supplier_id')
            ->leftJoin('users as u', 'po.created_by', '=', 'u.id')
            ->leftJoin('m_payment_terms as pt', 'pt.id', '=', 'po.f_payment_term_id')
            ->leftJoin('m_warehouses as mw', 'mw.id', '=', 'po.f_warehouse_id')
            ->leftJoin(DB::raw("JSON_TABLE(po.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('po.id', $id)
            ->where('po.company_code', session('user_company_code'))
            ->groupBy('po.id')
            ->first();

        if ($data && $data->f_tag_codes) {
            $data->f_tag_codes = json_decode($data->f_tag_codes); // Returns objects for view compatibility
        }
         if ($data->attachment) {
            $data->attachment = Storage::disk('public')->url($data->attachment);
        }

        return $data;
    }
}
