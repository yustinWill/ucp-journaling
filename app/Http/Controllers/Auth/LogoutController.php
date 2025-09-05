<?php

namespace App\Http\Controllers\Auth;

use App\Domains\User\Actions\AuthenticateUserAction;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;

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
