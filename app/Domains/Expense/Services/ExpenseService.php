<?php

namespace App\Domains\Expense\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    private $table_name = 't_expenses';

    public function create($data)
    {
        if ($data['pay_later'] == false) {
            $data['due_date'] = null;
            $data['payment_terms'] = null;
            $data['payment_method'] = $data['payment_method'];
        } else {
            $data['payment_method'] = null;
        }

        $insert_data = [
            'company_code' => session('user_company_code'),
            'type' => 'AA',
            'transaction_no' => $data['transaction_number'],
            'transaction_date' => $data['transaction_date'],
            'to_be_paid_later' => $data['pay_later'],
            'f_payment_term_id' => $data['payment_terms'],
            'f_payment_method_id' => $data['payment_method'],
            'due_date' => $data['due_date'],
            'f_receiver_customer_id' => $data['recipient_id'],
            'billing_address' => $data['billing_address'],
            'description' => $data['description'] ?? null,
            'memo' => $data['memo'] ?? null,
            'f_tag_codes' => json_encode($data['tags'] ?? []),
            'attachment' => $data['attachment'] ?? null,
            'subtotal' => $data['subtotal'],
            'total_tax' => $data['tax_amount'],
            'total_before_discount' => $data['total_before_discount'],
            'total_discount' => $data['discount_amount'],
            'total_amount' => $data['final_total'],
            'category_type' => $data['category_type'] ?? null,
            'total_remaining_amount' => $data['final_total'],
            'total_paid_amount' => 0,
            'entity_type' => $data['entity_type'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ];

        return DB::table($this->table_name)->insertGetId($insert_data);
    }

    public function update($data, $id)
    {
        if ($data['pay_later'] == false) {
            $data['due_date'] = null;
            $data['payment_terms'] = null;
            $data['payment_method'] = $data['payment_method'];
        } else {
            $data['payment_method'] = null;
        }
        $update_data = [
            'transaction_date' => $data['transaction_date'],
            'to_be_paid_later' => $data['pay_later'],
            'f_payment_term_id' => $data['payment_terms'],
            'f_payment_method_id' => $data['payment_method'],
            'due_date' => $data['due_date'],
            'f_receiver_customer_id' => $data['recipient_id'],
            'billing_address' => $data['billing_address'],
            'description' => $data['description'] ?? null,
            'memo' => $data['memo'] ?? null,
            'f_tag_codes' => json_encode($data['tags'] ?? []),
            'subtotal' => $data['subtotal'],
            'total_tax' => $data['tax_amount'],
            'total_before_discount' => $data['total_before_discount'],
            'total_discount' => $data['discount_amount'],
            'total_amount' => $data['final_total'],
            'category_type' => $data['category_type'] ?? null,
            'total_remaining_amount' => $data['final_total'],
            'total_paid_amount' => 0,
            'entity_type' => $data['entity_type'],
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ];

        if (isset($data['attachment']) && $data['attachment'] != null && $data['attachment'] != '') {
            $update_data['attachment'] = $data['attachment'];
        }

        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->update($update_data);
    }

    public function updateHeaderAmount($id, $total_amount)
    {
        DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->decrement('total_remaining_amount', $total_amount);

        DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->increment('total_paid_amount', $total_amount);
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.type',
                't.transaction_no',
                't.transaction_date',
                't.to_be_paid_later',
                't.f_payment_term_id',
                'pt.name AS payment_term_name',
                't.due_date',
                't.f_receiver_customer_id AS supplier_id',
                's.name AS supplier_name',
                't.billing_address',
                't.description',
                't.memo',
                't.f_tag_codes',
                't.attachment',
                't.subtotal',
                't.total_tax',
                't.total_before_discount',
                't.total_discount',
                't.total_amount',
                't.total_remaining_amount',
                't.total_paid_amount',
                't.created_at',
                't.updated_at',
                't.created_by',
                't.updated_by',
                't.company_code',
                't.f_payment_method_id',
                'category_type',
                'pm.name AS payment_method_name',
                'entity_type',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('m_contacts as s', 't.f_receiver_customer_id', '=', 's.id')
            ->leftJoin('m_payment_methods as pm', 't.f_payment_method_id', '=', 'pm.id')
            ->leftJoin('m_payment_terms as pt', 't.f_payment_term_id', '=', 'pt.id')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('t.id', $id)
            ->where('t.company_code', session('user_company_code'))
            ->groupBy('t.id')
            ->first();

        if ($data && $data->tags) {
            $data->tags = json_decode($data->tags); // Returns objects for view compatibility
        }

        $data_detail = DB::table('t_expense_coa_details as d')
            ->select([
                'd.id',
                'a.code as coa_code',
                'a.name as coa_name',
                'd.amount',
                'mt.id as tax_id',
                'mt.code as tax_code',
                'mt.name as tax_name',
            ])
            ->leftJoin('m_coas as a', 'd.f_coa_code', '=', 'a.code')
            ->leftJoin('m_taxes as mt', 'd.f_tax_id', '=', 'mt.id')
            ->where('d.f_expense_id', $id)
            ->get();

        $data_payment = DB::table('t_expense_payments as ter')
            ->select([
                '*',
            ])
            ->where('ter.f_expense_id', $id)
            ->get();

        $data->expense_lines = $data_detail;
        $data->payments = $data_payment;

        // dd($data->payments);
        return $data;
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.transaction_date',
                't.transaction_no',
                't.description',
                's.id AS supplier_id',
                's.name AS supplier_name',
                't.total_amount',
                'usr.id AS created_by_id',
                'usr.name AS created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('m_contacts as s', 't.f_receiver_customer_id', '=', 's.id')
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->leftJoin(DB::raw("JSON_TABLE(t.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id');

        $query->where('t.company_code', session('user_company_code'));

        if ($date = request()->input('date_range_filter')) {
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('t.transaction_date', [$dateFrom, $dateTo]);
            } else {
                // Single date filter
                $query->where('t.transaction_date', $date);
            }
        }

        if ($supplier_filter = request()->input('supplier_filter')) {
            $query->where('t.f_receiver_customer_id', $supplier_filter);
        }

        if ($transaction_no = request()->input('transaction_no')) {
            $query->where('t.transaction_no', 'like', "%{$transaction_no}%");
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
