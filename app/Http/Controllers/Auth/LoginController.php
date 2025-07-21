<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;

use App\Domains\User\Actions\LoginUserAction;

class LoginController extends Controller
{
    public function login()
    {
        return view('pages.public.login', []);
    }

    public function authenticate(Request $req, LoginUserAction $loginUserAction)
    {
        try {
            $credentials = $req->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
            ]);

            $loginUserAction->execute($credentials);

            return redirect()->route('home.view')->withSuccess('OK');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (AppException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
