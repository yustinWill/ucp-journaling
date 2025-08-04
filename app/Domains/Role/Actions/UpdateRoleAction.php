<?php

namespace App\Domains\Role\Actions;

use App\Domains\Role\Services\RoleService;
use App\Domains\Role\Services\RoleAuditLogService;
use App\Domains\Permission\Services\PermissionService;
use App\Domains\RolePermission\Services\RolePermissionService;
use Illuminate\Support\Facades\DB;
use App\Enums\GeneralStatuses;
use App\Enums\ActionTypes;

class UpdateRoleAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(RoleService::class)->findOne($id);

        // $before_role = app(RoleService::class)->findOne($old_data->role_id);
        // $after_role = app(RoleService::class)->findOne($data['role']);

        return DB::transaction(function () use ($data, $id, $old_data) {
            app(RoleService::class)->update($id, $data);

            app(RolePermissionService::class)->deleteByRoleId($id);

            foreach ($data['permissions'] as $permission) {
                $permission = app(PermissionService::class)->findOneBasedOnModuleAndPermission($permission['module_id'], $permission['permission_type']);

                app(RolePermissionService::class)->create([
                    'role_id' => $id,
                    'permission_id' => $permission->id,
                    'status' => GeneralStatuses::ACTIVE
                ]);
            }

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Role",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'name' => [
                            'label' => 'Nama Role',
                            'before' => $old_data->name,
                            'after' => $data['name'],
                        ],
                        'status' => [
                            'label' => 'Status',
                            'before' => $old_data->is_active,
                            'after' => $data['status'] == 'ACTIVE' ? true : false,
                        ],
                        'description' => [
                            'label' => 'Deskripsi',
                            'before' => $old_data->description,
                            'after' => $data['description'] ?? null,
                        ],
                        'tags' => [
                            'label' => 'Tag',
                            'before' => $old_data->f_tag_codes,
                            'after' => $data['tags'] ?? null,
                        ],
                    ]
                ])
            ];
            app(RoleAuditLogService::class)->create($log_data, $id);
        });
    }
}
