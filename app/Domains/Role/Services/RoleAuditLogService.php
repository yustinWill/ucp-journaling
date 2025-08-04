<?php

namespace App\Domains\Role\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RoleAuditLogService
{
    private $table_name = 'm_role_audit_logs';

    public function create(array $data, $role_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_role_id' => $role_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function findAllBasedOnId($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.f_role_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name'
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_role_id', $id)
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
