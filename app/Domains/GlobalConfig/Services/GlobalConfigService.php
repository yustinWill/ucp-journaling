<?php

namespace App\Domains\GlobalConfig\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class GlobalConfigService
{
    private $table_name = 'm_global_configs';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('code', 'name', 'value')
            ->where('status', GeneralStatuses::ACTIVE)
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findOne(string $code)
    {
        return DB::table($this->table_name)
            ->select('code', 'name', 'value')
            ->where('status', GeneralStatuses::ACTIVE)
            ->where('code', $code)
            ->first();
    }
}
