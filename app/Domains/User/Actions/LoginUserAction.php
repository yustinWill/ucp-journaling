<?php

namespace App\Domains\User\Actions;

// use App\Domains\User\Services\UserService;
use Illuminate\Support\Facades\Auth;

class LoginUserAction
{
    public function execute($req)
    {
        if (Auth::attempt($req)) {
            $req->session()->regenerate();
            // $user_data = $this->get_user_data();
            // $this->set_user_session($user_data, $laba_rugi_coa);
            // $this->insert_login_log($req);

            return true;
        } else {
            // throw new Exception($this->exception_codes['general']['internal_server_error']);
        }


        // return app(UserService::class)->createUser($name, $emailObject, $password);
    }
}
