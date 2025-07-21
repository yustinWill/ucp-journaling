<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Domains\User\Actions\AuthenticateUserAction;
use App\Exceptions\AppException;
use App\Enums\SuccessCodes;

class LogoutController extends Controller
{
    public function logout(AuthenticateUserAction $authenticateUserAction)
    {
        try {
            $authenticateUserAction->logout();
            return redirect()->route('login')->withSuccess(SuccessCodes::LOGOUT_SUCCESS->message());
        } catch (AppException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
