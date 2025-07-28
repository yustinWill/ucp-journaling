<?php

namespace App\Domains\Company\Services;

use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatuses;

class CompanyService
{
    private $table_name = 'm_companies';

    public function findAll()
    {
        return DB::table($this->table_name . ' as c')
            ->select([
                'c.code AS company_code',
                'c.name AS company_name',
                'c.status AS status'
            ])
            ->where('c.status', GeneralStatuses::ACTIVE)
            ->orderBy('c.code', 'ASC')
            ->get();
    }
}
