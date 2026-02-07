<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions
        $permissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Roles & Permissions
            'manage_roles',
            'manage_permissions',

            // Dashboard
            'view_dashboard',

            // Master Data
            'view_master_data',
            'create_master_data',
            'edit_master_data',
            'delete_master_data',

            // Transactions
            'view_transactions',
            'create_transactions',
            'edit_transactions',
            'delete_transactions',

            // Reports
            'view_reports',
            'export_reports',

            // Audit Logs
            'view_audit_logs',

            // Settings
            'manage_settings',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Define roles and assign permissions
        $roles = [
            'super_admin' => [
                'permissions' => $permissions, // All permissions
            ],
            'admin' => [
                'permissions' => [
                    'view_users',
                    'create_users',
                    'edit_users',
                    'view_dashboard',
                    'view_master_data',
                    'create_master_data',
                    'edit_master_data',
                    'view_transactions',
                    'view_reports',
                    'export_reports',
                    'view_audit_logs',
                ],
            ],
            'manager' => [
                'permissions' => [
                    'view_dashboard',
                    'view_master_data',
                    'view_transactions',
                    'create_transactions',
                    'view_reports',
                    'export_reports',
                ],
            ],
            'user' => [
                'permissions' => [
                    'view_dashboard',
                    'view_transactions',
                ],
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // Assign permissions to role
            if (isset($roleData['permissions'])) {
                $rolePermissions = Permission::whereIn('name', $roleData['permissions'])->pluck('id')->toArray();
                $role->syncPermissions($rolePermissions);
            }
        }

        $this->command->info('Roles and permissions setup completed successfully!');
    }
}
