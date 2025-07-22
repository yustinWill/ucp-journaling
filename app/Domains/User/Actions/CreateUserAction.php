<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserService;
use App\Domains\User\Services\UserAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateUserAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(UserService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan User",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(UserAuditLogService::class)->create($log_data, $tag_id);
        });
    }
}
