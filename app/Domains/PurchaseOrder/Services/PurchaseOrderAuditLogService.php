<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderAuditLogService
{
    private $table_name = 't_purchase_order_audit_logs';

    public function create(array $data, $purchase_order_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_purchase_order_id' => $purchase_order_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function findAllBasedOnPurchaseOrderId($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.f_purchase_order_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name'
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_contact_id', $id)
            ->where('t.company_code', session('user_company_code'))
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }

    public function findOneBasedOnId($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->first();
    }
}
