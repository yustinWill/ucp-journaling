<?php

namespace App\Domains\SalesOrder\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderAuditLogService
{
    private $table_name = 't_sales_order_audit_logs';

    public function create(int $salesOrderId, string $action, array $metadata = [], string $description = null): int
    {
        return DB::table($this->table_name)->insertGetId([
            'f_sales_order_id' => $salesOrderId,
            'action_type' => $action,
            'description' => $description ?? ucfirst(strtolower($action)),
            'metadata' => json_encode($metadata),
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findAllBasedOnSalesOrderId(int $salesOrderId): array
    {
        return DB::table($this->table_name . ' as sal')
            ->leftJoin('users as u', 'sal.created_by', '=', 'u.id')
            ->where('sal.f_sales_order_id', $salesOrderId)
            ->select([
                'sal.id',
                'sal.action_type as action',
                'sal.description',
                'sal.metadata',
                'sal.created_at as date',
                'u.name as user_name',
                'u.email as user_email'
            ])
            ->orderBy('sal.created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->metadata = json_decode($log->metadata, true) ?? [];
                return $log;
            })
            ->toArray();
    }

    public function findById(int $id)
    {
        $log = DB::table($this->table_name . ' as sal')
            ->leftJoin('users as u', 'sal.created_by', '=', 'u.id')
            ->where('sal.id', $id)
            ->select([
                'sal.*',
                'u.name as user_name',
                'u.email as user_email'
            ])
            ->first();

        if ($log) {
            $log->metadata = json_decode($log->metadata, true) ?? [];
        }

        return $log;
    }
}