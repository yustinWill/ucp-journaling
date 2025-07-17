<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create modules
        $modules = [
            ['name' => 'User Management', 'slug' => 'user', 'description' => 'User management and administration'],
            ['name' => 'Role Management', 'slug' => 'role', 'description' => 'Role and permission management'],
            ['name' => 'Contact Management', 'slug' => 'contact', 'description' => 'Customer and supplier management'],
            ['name' => 'Product Management', 'slug' => 'product', 'description' => 'Product and inventory management'],
            ['name' => 'Sales Management', 'slug' => 'sales', 'description' => 'Sales transactions and orders'],
            ['name' => 'Purchase Management', 'slug' => 'purchase', 'description' => 'Purchase orders and supplier management'],
            ['name' => 'Journal Management', 'slug' => 'journal', 'description' => 'Journal entries and accounting'],
            ['name' => 'Expense Management', 'slug' => 'expense', 'description' => 'Expense tracking and management'],
            ['name' => 'COA Management', 'slug' => 'coa', 'description' => 'Chart of accounts management'],
        ];

        foreach ($modules as $module) {
            $moduleId = DB::table('modules')->insertGetId([
                'name' => $module['name'],
                'slug' => $module['slug'],
                'description' => $module['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create permissions for each module
            $permissions = ['read', 'write', 'delete', 'approval'];
            foreach ($permissions as $permission) {
                DB::table('permissions')->insert([
                    'module_id' => $moduleId,
                    'permission_type' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Create default roles
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'is_active' => true,
                'permissions' => 'all' // Special case for all permissions
            ],
            [
                'name' => 'Admin',
                'description' => 'Administrative access with limited permissions',
                'is_active' => true,
                'permissions' => ['user.read', 'user.write', 'contact.read', 'contact.write', 'product.read', 'product.write', 'sales.read', 'sales.write']
            ],
            [
                'name' => 'Accounting',
                'description' => 'Financial and accounting module access',
                'is_active' => true,
                'permissions' => ['journal.read', 'journal.write', 'expense.read', 'expense.write', 'coa.read', 'coa.write']
            ],
            [
                'name' => 'Sales',
                'description' => 'Sales and customer management access',
                'is_active' => true,
                'permissions' => ['contact.read', 'contact.write', 'product.read', 'sales.read', 'sales.write']
            ],
            [
                'name' => 'Purchase',
                'description' => 'Purchase and supplier management access',
                'is_active' => true,
                'permissions' => ['contact.read', 'contact.write', 'product.read', 'purchase.read', 'purchase.write']
            ],
        ];

        foreach ($roles as $roleData) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => $roleData['name'],
                'description' => $roleData['description'],
                'is_active' => $roleData['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign permissions to role
            if ($roleData['permissions'] === 'all') {
                // Super Admin gets all permissions
                $allPermissions = DB::table('permissions')->get();
                foreach ($allPermissions as $permission) {
                    DB::table('role_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Assign specific permissions
                foreach ($roleData['permissions'] as $permissionSlug) {
                    [$moduleSlug, $permissionType] = explode('.', $permissionSlug);
                    
                    $permission = DB::table('permissions')
                        ->join('modules', 'permissions.module_id', '=', 'modules.id')
                        ->where('modules.slug', $moduleSlug)
                        ->where('permissions.permission_type', $permissionType)
                        ->select('permissions.id')
                        ->first();
                    
                    if ($permission) {
                        DB::table('role_permissions')->insert([
                            'role_id' => $roleId,
                            'permission_id' => $permission->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
