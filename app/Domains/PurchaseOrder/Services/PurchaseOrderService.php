<?php

namespace App\Domains\PurchaseOrder\Services;

use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatuses;

class PurchaseOrderService
{
    private $table_name = 't_purchase_orders';

    public function getLatestByMonth($year, $month)
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month)
            ->orderBy('created_at', 'desc')
            ->first(['id']);
    }
}
