<?php

namespace App\Domains\Expense\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{
    private $table_name = 't_expenses';

    public function create($data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'type' => 'AA',
            'transaction_no' => $data['transaction_number'],
            'transaction_date' => $data['transaction_date'],
            'to_be_paid_later' => $data['pay_later'],
            'f_payment_term_id' => $data['payment_terms'],
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
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);

    }
}
