<?php

namespace App\Domains\Module\Services;

use Illuminate\Support\Facades\DB;

class ModuleService
{
    private $table_name = 'modules';

    public function findAll()
    {
        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.name',
                't.description',
            ])
            ->where('t.is_active', true)
            ->orderBy('t.name', 'ASC')
            ->get();
    }
}
