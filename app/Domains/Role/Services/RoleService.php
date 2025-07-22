<?php

namespace App\Domains\Role\Services;

use Illuminate\Support\Facades\DB;

class RoleService
{
    private $table_name = 'roles';

    public function findAll()
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.id',
                't.name',
                't.description',
            ])
            ->orderBy('t.name', 'ASC')
            ->get();
    }
}
