<?php

namespace App\Domains\UserCompany\Services;

use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatuses;

class UserCompanyService
{
    private $table_name = 'm_user_companies';

    public function findAllBasedOnUserId($userId)
    {
        return DB::table($this->table_name . ' as c')
            ->select([
                'm.code AS company_code',
                'm.name AS company_name',
                'c.status AS status',
                'c.default_company AS default_company'
            ])
            ->join('m_companies as m', 'c.f_company_code', '=', 'm.code')
            ->where('c.f_user_id', $userId)
            ->where('c.status', GeneralStatuses::ACTIVE)
            ->orderBy('m.code', 'ASC')
            ->get();
    }

    public function findByUserIdAndCompanyCode($userId, $companyCode)
    {
        return DB::table($this->table_name . ' as c')
            ->select([
                'm.code AS company_code',
                'm.name AS company_name',
                'c.status AS status',
                'c.default_company AS default_company'
            ])
            ->join('m_companies as m', 'c.f_company_code', '=', 'm.code')
            ->where('c.f_user_id', $userId)
            ->where('c.f_company_code', $companyCode)
            ->where('c.status', GeneralStatuses::ACTIVE)
            ->first();
    }
}
