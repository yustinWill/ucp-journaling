<?php

namespace App\Domains\StockStatus\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class StockStatusService
{
    private $table_name = 'm_stock_statuses';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code')
            ->where('status', GeneralStatuses::ACTIVE)
            ->orderBy('id', 'asc')
            ->get();
    }
}