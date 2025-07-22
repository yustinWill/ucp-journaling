<?php

namespace App\Domains\Unit\Services;
use Illuminate\Support\Facades\DB;

class UnitTypeService
{
    private $table_name = 'm_unit_types';
    
    public function findAll()
    {
        return DB::table($this->table_name)->orderBy('id', 'asc')->get();
    }
}
