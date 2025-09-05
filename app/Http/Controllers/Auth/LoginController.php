<?php

namespace App\Http\Controllers\Auth;

use App\Domains\User\Actions\AuthenticateUserAction;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login()
    {
        return view('pages.public.login', []);
    }

    public function authenticate(Request $req, AuthenticateUserAction $authenticateUserAction)
    {
        try {
            $credentials = $req->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            $authenticateUserAction->login($credentials);

            return redirect()->route('home.view');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (AppException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
