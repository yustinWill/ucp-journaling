<?php

namespace App\Domains\RolePermission\Services;

use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    private $table_name = 'role_permissions';

    public function findAllBasedOnRoleId($id)
    {
        return DB::table($this->table_name . ' as rp')
            ->select([
                'm.name AS module_name',
                'p.permission_type AS permission_type',
                'rp.status AS status'
            ])
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->join('modules as m', 'm.id', '=', 'p.module_id')
            ->where('rp.role_id', $id)
            ->orderBy('m.id', 'ASC')
            ->orderBy('p.id', 'ASC')
            ->orderBy('rp.id', 'ASC')
            ->get();
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'role_id' => $data['role_id'],
            'permission_id' => $data['permission_id'],
            'status' => $data['status'],
            'created_at' => now()
        ]);
    }
}
