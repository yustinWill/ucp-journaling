<?php

namespace App\Domains\PurchaseInvoice\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceAuditLogService
{
    private $table_name = 't_purchase_invoice_audit_logs';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_purchase_invoice_id' => $data['invoice_id'],
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'] ?? null,
            'user_id' => $data['user_id'] ?? Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function findAllBasedOnInvoiceId($invoiceId)
    {
        return DB::table($this->table_name.' as pial')
            ->select([
                'pial.*',
                'u.name as user_name',
            ])
            ->leftJoin('users as u', 'pial.user_id', '=', 'u.id')
            ->where('pial.f_purchase_invoice_id', $invoiceId)
            ->where('pial.company_code', session('user_company_code'))
            ->orderBy('pial.created_at', 'desc')
            ->get();
    }
}
