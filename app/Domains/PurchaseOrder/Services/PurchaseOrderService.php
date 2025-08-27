<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatuses;
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
}
