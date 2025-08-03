<?php

namespace App\Domains\Permission\Services;

use Illuminate\Support\Facades\DB;

class PermissionService
{
    private $table_name = 'permissions';

    public function findAll()
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.module_id',
                't.permission_type AS type',
                't.permission_name AS name',
            ])
            ->orderBy('t.id', 'ASC')
            ->get();
    }

    public function findOneBasedOnModuleAndPermission($module_id, $permission_type)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.module_id',
                't.permission_type',
            ])
            ->where('t.module_id', $module_id)
            ->where('t.permission_type', $permission_type)
            ->first();
    }
}
