<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserService;
use App\Domains\Role\Services\RoleService;
use App\Domains\User\Services\UserAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateUserAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(UserService::class)->findOne($id);

        $before_role = app(RoleService::class)->findOne($old_data->role_id);
        $after_role = app(RoleService::class)->findOne($data['role']);

        return DB::transaction(function () use ($id, $data, $old_data, $before_role, $after_role) {
            app(UserService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan User",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'name' => [
                            'label' => 'Nama User',
                            'before' => $old_data->name,
                            'after' => $data['name'],
                        ],
                        'type' => [
                            'label' => 'Email User',
                            'before' => $old_data->email,
                            'after' => $data['email'],
                        ],
                        'status' => [
                            'label' => 'Nomor Telepon',
                            'before' => $old_data->phone,
                            'after' => $data['phone'],
                        ],
                        'status' => [
                            'label' => 'Status',
                            'before' => $old_data->status,
                            'after' => $data['status'],
                        ],
                        'role' => [
                            'label' => 'Role',
                            'before' => $before_role->id . ' - ' . $before_role->name,
                            'after' => $after_role->id . ' - ' . $after_role->name,
                        ],
                        'status' => [
                            'label' => 'Status User',
                            'before' => $old_data->status,
                            'after' => $data['status'] ?? null,
                        ],
                        'tags' => [
                            'label' => 'Tag',
                            'before' => $old_data->f_tag_codes,
                            'after' => $data['tags'] ?? null,
                        ],
                    ]
                ])
            ];
            app(UserAuditLogService::class)->create($log_data, $id);
        });
    }
}
