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
        // Insert Top Up module
        DB::table('modules')->insert([
            'name' => 'main_transaction_topup',
            'slug' => 'main-transaction-topup',
            'description' => 'Main Transaction Top Up Payment',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Get the module ID
        $topupModule = DB::table('modules')
            ->where('name', 'main_transaction_topup')
            ->first();

        if (!$topupModule) {
            throw new \Exception('Failed to create Top Up module');
        }

        // Define permission types
        $permissionTypes = [
            'read',
            'write',
            'delete',
            'approval',
        ];

        // Insert permissions for Top Up module
        $permissions = [];
        foreach ($permissionTypes as $type) {
            $permissions[] = [
                'module_id' => $topupModule->id,
                'permission_type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('permissions')->insert($permissions);

        // Optional: Auto-grant permissions to Super Admin role (role_id = 1)
        // Uncomment this section if you want to automatically grant permissions to Super Admin
        /*
        $superAdminRole = DB::table('roles')->where('id', 1)->first();

        if ($superAdminRole) {
            $topupPermissions = DB::table('permissions')
                ->where('module_id', $topupModule->id)
                ->get();

            $rolePermissions = [];
            foreach ($topupPermissions as $permission) {
                $rolePermissions[] = [
                    'role_id' => $superAdminRole->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('role_permissions')->insert($rolePermissions);
        }
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the Top Up module
        $topupModule = DB::table('modules')
            ->where('name', 'main_transaction_topup')
            ->first();

        if ($topupModule) {
            // Delete role permissions
            DB::table('role_permissions')
                ->whereIn('permission_id', function ($query) use ($topupModule) {
                    $query->select('id')
                        ->from('permissions')
                        ->where('module_id', $topupModule->id);
                })
                ->delete();

            // Delete permissions
            DB::table('permissions')
                ->where('module_id', $topupModule->id)
                ->delete();

            // Delete module
            DB::table('modules')
                ->where('id', $topupModule->id)
                ->delete();
        }
    }
};
