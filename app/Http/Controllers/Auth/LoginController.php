<?php

namespace App\Http\Controllers\Auth;

use App\Domains\User\Actions\LoginUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login()
    {
        return view('pages.public.login', []);
    }

    public function authenticate(Request $req)
    {
        return redirect()->route('home.view')->withSuccess('OK');
        // try {
        //     $req->validate([
        //         'email' => 'required|email',
        //         'password' => 'required',
        //     ]);

        //     app(LoginUserAction::class)->execute($req);

        //     return redirect()->route('home.view')->withSuccess('OK');
        // } catch (\Throwable $th) {
        //     dd($th);
        //     switch ($th->getMessage()) {
        //         case 'USER_NOT_FOUND':
        //             return back()->withErrors([
        //                 'email' => 'User Tidak Aktif',
        //             ])->onlyInput('email');
        //             break;
        //         default:
        //             // code...
        //             break;
        //     }
        // }
    }
}
