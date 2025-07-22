<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserService;
use App\Domains\User\Services\UserAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateUserAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(UserService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(UserService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan User",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(UserAuditLogService::class)->create($log_data, $id);
        });
    }
}
