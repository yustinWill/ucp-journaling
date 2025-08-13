<?php

namespace App\Domains\User\Actions;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\AppException;
use App\Enums\ErrorCodes;
use App\Domains\RolePermission\Services\RolePermissionService;
use App\Domains\UserCompany\Services\UserCompanyService;
use App\Domains\GlobalConfig\Services\GlobalConfigService;

class AuthenticateUserAction
{
    public function login($req)
    {
        if (Auth::attempt($req)) {
            $permissions = app(RolePermissionService::class)->findAllBasedOnRoleId(Auth::user()->role_id);

            if (empty($permissions)) {
                throw new AppException(ErrorCodes::USER_NO_PERMISSIONS);
            }
            $user_company = app(UserCompanyService::class)->findAllBasedOnUserId(Auth::id());

            if ($user_company->isEmpty()) {
                throw new AppException(ErrorCodes::USER_NO_COMPANY);
            }

            $user_company_code = null;
            foreach ($user_company as $company) {
                if ($company->default_company) {
                    $user_company_code = $company->company_code;
                    $user_company_name = $company->company_name;
                    break;
                }
            }


            $config_data = app(GlobalConfigService::class)->findAll();

            foreach ($config_data as $config) {
                session([$config->code => $config->value]);
            }
            
            session(['permissions' => $permissions, 'user_company' => $user_company, 'user_company_code' => $user_company_code, 'user_company_name' => $user_company_name]);
            return true;
        } else {
            throw new AppException(ErrorCodes::INVALID_LOGIN_CREDENTIALS);
        }
    }

    public function logout()
    {
        Auth::logout();
        return true;
    }
}
