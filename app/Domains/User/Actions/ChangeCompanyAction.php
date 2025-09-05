<?php

namespace App\Domains\User\Actions;

use App\Domains\UserCompany\Services\UserCompanyService;
use Illuminate\Support\Facades\Auth;

class ChangeCompanyAction
{
    public function changeCompany($companyCode)
    {
        $userCompany = app(UserCompanyService::class)->findByUserIdAndCompanyCode(Auth::id(), $companyCode);

        session(['user_company_code' => $userCompany->company_code, 'user_company_name' => $userCompany->company_name]);

        return true;
    }
}
