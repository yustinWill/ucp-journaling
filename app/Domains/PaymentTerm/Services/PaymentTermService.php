<?php

namespace App\Domains\PaymentTerm\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\DB;

class PaymentTermService
{
    private $table_name = 'm_payment_terms';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->select('id', 'name', 'code', 'days', 'type')
            ->where('status', GeneralStatuses::ACTIVE)
            ->where('company_code', session('user_company_code'))
            ->orderBy('name', 'asc')
            ->get();
    }
}