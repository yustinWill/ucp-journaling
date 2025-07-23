<?php

namespace App\Domains\Tax\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TaxAuditLogService
{
    private $table_name = 'm_tax_audit_logs';

    public function create(int $taxId, string $actionType, string $description): int
    {
        return DB::table($this->table_name)->insertGetId([
            'f_tax_id' => $taxId,
            'action_type' => $actionType,
            'description' => $description,
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function findAllBasedOnTagId(int $taxId)
    {
        return DB::table($this->table_name . ' as tal')
            ->select([
                'tal.created_at',
                'tal.action_type',
                'tal.description',
                'u.name as user_name'
            ])
            ->leftJoin('users as u', 'tal.created_by', '=', 'u.id')
            ->where('tal.f_tax_id', $taxId)
            ->orderBy('tal.created_at', 'desc')
            ->get();
    }
}