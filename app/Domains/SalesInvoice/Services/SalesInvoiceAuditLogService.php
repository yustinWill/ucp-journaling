<?php
namespace App\Domains\SalesInvoice\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesInvoiceAuditLogService
{
    private $table_name = 't_sales_invoice_audit_logs';

    public function create(array $data, $sales_invoice_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'f_sales_invoice_id' => $sales_invoice_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function findAllBasedOnSalesInvoiceId($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.f_sales_invoice_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name'
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_sales_invoice_id', $id)
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }
}