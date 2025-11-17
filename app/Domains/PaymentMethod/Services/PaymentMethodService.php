<?php

namespace App\Domains\PaymentMethod\Services;

use Illuminate\Support\Facades\DB;

class PaymentMethodService
{
    private $table_name = 'm_payment_methods';

    public function findAll()
    {
        return DB::table($this->table_name)
            ->where('status', 'ACTIVE')
            ->orderBy('name', 'asc')
            ->get();
    }
}
