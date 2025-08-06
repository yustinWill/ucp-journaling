<?php

namespace App\Domains\Bank\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class BankService
{
    private $table_name = 'm_banks';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code')
            ->where('status', GeneralStatuses::ACTIVE)
            ->orderBy('name', 'asc')
            ->get();
    }
}