<?php

namespace App\Domains\User\Actions;

use App\Domains\User\Services\UserAuditLogService;
use App\Domains\User\Services\UserService;
use App\Enums\ActionTypes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UpdateUserAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(UserService::class)->findOne($id);
        $oldRole = Role::find($old_data->role_id ?? null);
        $newRole = Role::find($data['role'] ?? null);

        return DB::transaction(function () use ($id, $data, $old_data, $oldRole, $newRole) {
            app(UserService::class)->update($id, $data);

            // Sync Spatie role for user
            $user = User::find($id);
            if (isset($data['role']) && $data['role']) {
                $role = Role::find($data['role']);
                if ($role) {
                    $user->syncRoles([$role->name]);
                }
            }

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan User',
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'name' => [
                            'label' => 'Nama User',
                            'before' => $old_data->name,
                            'after' => $data['name'],
                        ],
                        'email' => [
                            'label' => 'Email User',
                            'before' => $old_data->email,
                            'after' => $data['email'],
                        ],
                        'phone' => [
                            'label' => 'Nomor Telepon',
                            'before' => $old_data->phone,
                            'after' => $data['phone'],
                        ],
                        'role' => [
                            'label' => 'Role',
                            'before' => $oldRole ? $oldRole->id.' - '.$oldRole->name : 'N/A',
                            'after' => $newRole ? $newRole->id.' - '.$newRole->name : 'N/A',
                        ],
                    ],
                ]),
            ];
            app(UserAuditLogService::class)->create($log_data, $id);
        });
    }
}
