<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Return Management module
        $moduleId = DB::table('modules')->insertGetId([
            'name' => 'Return Management',
            'slug' => 'return_management',
            'description' => 'Return management for purchase and sales invoices',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create permissions for Return Management module
        $permissions = ['read', 'write', 'delete', 'approval'];
        $permissionIds = [];
        
        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->insertGetId([
                'module_id' => $moduleId,
                'permission_type' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $permissionIds[] = $permissionId;
        }

        // Grant permissions to Super Admin role
        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->insert([
                    'company_code' => '001',
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $permissionId,
                    'status' => 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Grant permissions to Admin role if it exists
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        if ($adminRole) {
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->insert([
                    'company_code' => '001',
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                    'status' => 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get Return Management module
        $module = DB::table('modules')->where('slug', 'return_management')->first();
        
        if ($module) {
            // Delete role permissions for this module
            $permissionIds = DB::table('permissions')
                ->where('module_id', $module->id)
                ->pluck('id');
            
            DB::table('role_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();
            
            // Delete permissions for this module
            DB::table('permissions')
                ->where('module_id', $module->id)
                ->delete();
            
            // Delete the module
            DB::table('modules')->where('id', $module->id)->delete();
        }
    }
};