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
            ->orderByRaw("CASE WHEN code = 'Other' THEN 1 ELSE 0 END, name ASC")
            ->get();
    }
}
