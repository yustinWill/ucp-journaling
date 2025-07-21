<?php

namespace App\Domains\User\Actions;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\AppException;
use App\Enums\ErrorCodes;

class LoginUserAction
{
    public function execute($req)
    {
        if (Auth::attempt($req)) {
            return true;
        } else {
            throw new AppException(ErrorCodes::INVALID_LOGIN_CREDENTIALS);
        }
    }
}
