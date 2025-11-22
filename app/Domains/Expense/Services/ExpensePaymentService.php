<?php

namespace App\Domains\Expense\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpensePaymentService
{
    private $table_name = 't_expense_payments';

    public function create($data)
    {
        $insert_data = [
            'company_code' => session('user_company_code'),
            'f_expense_id' => $data['expense_id'],
            'transaction_no' => $data['transaction_number'],
            'transaction_date' => $data['transaction_date'],
            'f_receiver_customer_id' => $data['recipient_id'],
            'f_payment_coa_code' => $data['payment_coa'],
            'description' => $data['description'] ?? null,
            'memo' => $data['memo'] ?? null,
            'f_tag_codes' => json_encode($data['tags'] ?? []),
            'attachment' => $data['attachment'] ?? null,
            'remaining_amount' => $data['remaining_amount'],
            'payment_amount' => $data['payment_amount'],
            'latest_remaining_amount' => $data['remaining_amount'] - $data['payment_amount'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ];

        return DB::table($this->table_name)->insertGetId($insert_data);
    }

    public function findOneById($id)
    {
        $data = DB::table($this->table_name.' as t_expense_payments')
            ->select([
                't_expenses.*', 't_expense_payments.*', DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->join('t_expenses', 't_expense_payments.f_expense_id', '=', 't_expenses.id')
            ->leftJoin(DB::raw("JSON_TABLE(t_expense_payments.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('t_expense_payments.id', $id)
            ->where('t_expense_payments.company_code', session('user_company_code'))
            ->groupBy('t_expense_payments.id')
            ->first();

        if ($data && $data->tags) {
            $data->tags = json_decode($data->tags); // Returns objects for view compatibility
        }

        return $data;
    }
}
