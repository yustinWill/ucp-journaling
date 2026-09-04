<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserAuditLogService;
use App\Domains\User\Services\UserService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DeleteUserAction
{
    public function execute(int $id): void
    {
        $oldData = app(UserService::class)->findOne($id);
        $auditBefore = [
            'id' => $oldData->id,
            'name' => $oldData->name,
            'email' => $oldData->email,
            'phone' => $oldData->phone,
            'status' => $oldData->status,
            'role_id' => $oldData->role_id,
        ];

        DB::transaction(function () use ($id, $auditBefore) {
            if (app(UserService::class)->softDelete($id) !== 1) {
                throw new RuntimeException('User sudah dihapus atau tidak ditemukan.');
            }

            app(UserAuditLogService::class)->create([
                'action_type' => ActionTypes::DELETE,
                'description' => 'Penghapusan User (nonaktif)',
                'metadata' => json_encode([
                    'before' => $auditBefore,
                    'after' => [
                        'status' => 'INACTIVE',
                        'deleted' => true,
                    ],
                ]),
            ], $id);
        });
    }
}
