<?php

namespace App\Domains\SalesInvoice\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalesInvoicePaymentService
{
    private $table_name = 't_sales_invoice_payment';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'customer_id' => $data['customer_id'],
            'account_code' =>  $data['account_code'],
            'payment_method' => $data['payment_method'],
            'internal_memo' => $data['memo'] ?? null,
            'f_tags_code' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'sales_invoice_id' => $data['sales_invoice_id'],
            'payment_value' =>  $data['payment_value'],
            'reference_number' =>  $data['reference_number'],
            'payment_date' =>  $data['payment_date'],
            'deduction_value' =>  $data['deduction_value'],
            'deduction_account_code' =>  $data['deduction_account_code'],
            'attachment' => $data['attachment'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    
    public function getRemainingPayment($invoiceId)
    {
        
        return DB::table($this->table_name)
            ->where('sales_invoice_id', $invoiceId)
            ->select(DB::raw('SUM(payment_value - COALESCE(deduction_value, 0)) AS total'))
            ->value('total');
    }
}
