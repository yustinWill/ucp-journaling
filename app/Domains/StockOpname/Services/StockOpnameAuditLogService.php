<?php

namespace App\Domains\StockOpname\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOpnameAuditLogService
{
    private $table_name = 't_stock_opname_audit_logs';

    public function create(int $stock_opname_id, string $action_type, string $description, array $metadata = []): int
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_stock_opname_id' => $stock_opname_id,
            'action_type' => $action_type,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function findByStockOpnameId(int $stock_opname_id)
    {
        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by',
                'u.name as user_name',
            ])
            ->leftJoin('users as u', 't.created_by', '=', 'u.id')
            ->where('t.f_stock_opname_id', $stock_opname_id)
            ->where('t.company_code', session('user_company_code'))
            ->orderBy('t.created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->metadata = $log->metadata ? json_decode($log->metadata) : null;

                return $log;
            });
    }

    public function findAll()
    {
        return DB::table($this->table_name.' as sal')
            ->select([
                'sal.*',
                'sa.adjustment_number',
                'u.name as user_name',
            ])
            ->leftJoin('t_stock_adjustments as sa', 'sal.stock_opname_id', '=', 'sa.id')
            ->leftJoin('users as u', 'sal.created_by', '=', 'u.id')
            ->where('sal.company_code', session('user_company_code'))
            ->orderBy('sal.created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->metadata = $log->metadata ? json_decode($log->metadata) : null;

                return $log;
            });
    }
}
