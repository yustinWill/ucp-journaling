<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;

use App\Domains\User\Actions\ChangeCompanyAction;

class ChangeCompanyController extends Controller
{
    public function index(Request $request)
    {
        $companyCode = $request->input('company_code');

        if (!$companyCode) {
            throw ValidationException::withMessages(['company_code' => 'Company code is required.']);
        }

        app(ChangeCompanyAction::class)->changeCompany($companyCode);

        return redirect()->route('home.view');
    }
}
