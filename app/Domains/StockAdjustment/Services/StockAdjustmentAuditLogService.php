<?php

namespace App\Domains\StockAdjustment\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentAuditLogService
{
    private $table_name = 't_stock_adjustment_audit_logs';

    public function create(int $stock_adjustment_id, string $action_type, string $description, array $metadata = []): int
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'stock_adjustment_id' => $stock_adjustment_id,
            'action_type' => $action_type,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'created_by' => Auth::id(),
            'created_at' => now()
        ]);
    }

    public function findByStockAdjustmentId(int $stock_adjustment_id)
    {
        return DB::table($this->table_name . ' as sal')
            ->select([
                'sal.id',
                'sal.action_type',
                'sal.description',
                'sal.metadata',
                'sal.created_at',
                'u.name as user_name'
            ])
            ->leftJoin('users as u', 'sal.created_by', '=', 'u.id')
            ->where('sal.stock_adjustment_id', $stock_adjustment_id)
            ->where('sal.company_code', session('user_company_code'))
            ->orderBy('sal.created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $log->metadata = $log->metadata ? json_decode($log->metadata) : null;
                return $log;
            });
    }

    public function findAll()
    {
        return DB::table($this->table_name . ' as sal')
            ->select([
                'sal.*',
                'sa.adjustment_number',
                'u.name as user_name'
            ])
            ->leftJoin('t_stock_adjustments as sa', 'sal.stock_adjustment_id', '=', 'sa.id')
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