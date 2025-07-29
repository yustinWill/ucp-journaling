<?php

namespace App\Domains\User\Actions;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\AppException;
use App\Enums\ErrorCodes;
use App\Domains\RolePermission\Services\RolePermissionService;
use App\Domains\UserCompany\Services\UserCompanyService;

class ChangeCompanyAction
{
    public function changeCompany($companyCode)
    {
        $userCompany = app(UserCompanyService::class)->findByUserIdAndCompanyCode(Auth::id(), $companyCode);

        session(['user_company_code' => $userCompany->company_code, 'user_company_name' => $userCompany->company_name]);
        return true;
    }
}