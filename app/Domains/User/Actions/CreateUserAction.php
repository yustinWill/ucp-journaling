<?php

namespace App\Domains\User\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;
use App\Enums\GeneralStatuses;
use App\Domains\UserCompany\Services\UserCompanyService;
use App\Domains\User\Services\UserService;
use App\Domains\User\Services\UserAuditLogService;


class CreateUserAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user_id = app(UserService::class)->create($data);

            app(UserCompanyService::class)->create([
                'user_id' => $user_id,
                'company_code' => session('user_company_code'),
                'status' => GeneralStatuses::ACTIVE,
                'default_company' => true,
            ]);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan User",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            
            app(UserAuditLogService::class)->create($log_data, $user_id);
        });
    }
}
