<?php

namespace App\Domains\Unit\Services;

use Illuminate\Support\Facades\DB;

class UnitTypeService
{
    private $table_name = 'm_unit_types';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->orderBy('id', 'asc')
            ->get();
    }

    public function findOne(int $id)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->where('id', $id)
            ->first();
    }
}
