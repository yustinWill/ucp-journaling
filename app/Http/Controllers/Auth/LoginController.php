<?php

namespace App\Http\Controllers\Auth;

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
    }
}
