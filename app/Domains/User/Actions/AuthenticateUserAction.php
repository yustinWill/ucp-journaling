<?php

namespace App\Domains\User\Actions;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\AppException;
use App\Enums\ErrorCodes;
use App\Domains\RolePermission\Services\RolePermissionService;

class AuthenticateUserAction
{
    public function login($req)
    {
        if (Auth::attempt($req)) {
            $permissions = app(RolePermissionService::class)->findAllBasedOnRoleId(Auth::user()->role_id);
            session(['permissions' => $permissions]);
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
