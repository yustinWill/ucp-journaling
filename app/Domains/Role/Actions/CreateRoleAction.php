<?php

namespace App\Domains\Role\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;
use App\Enums\GeneralStatuses;
use App\Domains\RolePermission\Services\RolePermissionService;
use App\Domains\Permission\Services\PermissionService;
use App\Domains\Role\Services\RoleService;
use App\Domains\Role\Services\RoleAuditLogService;


class CreateRoleAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role_id = app(RoleService::class)->create($data);

            foreach ($data['permissions'] as $permission) {
                $permission = app(PermissionService::class)->findOneBasedOnModuleAndPermission($permission['module_id'], $permission['permission_type']);

                app(RolePermissionService::class)->create([
                    'role_id' => $role_id,
                    'permission_id' => $permission->id,
                    'status' => GeneralStatuses::ACTIVE
                ]);
            }

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Role",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];

            app(RoleAuditLogService::class)->create($log_data, $role_id);
        });
    }
}
