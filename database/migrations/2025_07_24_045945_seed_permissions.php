<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get existing role IDs
        $sales_person = DB::table('modules')->where('name', 'master_data_sales_person')->first();
        $tax = DB::table('modules')->where('name', 'master_data_tax')->first();
        $unit = DB::table('modules')->where('name', 'master_data_unit')->first();
        $category = DB::table('modules')->where('name', 'master_data_category')->first();
        $tag = DB::table('modules')->where('name', 'master_data_tag')->first();
        $role = DB::table('modules')->where('name', 'master_data_role')->first();
        $user = DB::table('modules')->where('name', 'master_data_user')->first();

        if (!$sales_person || !$tax || !$unit || !$category || !$tag || !$role || !$user) {
            throw new Exception('Required roles not found. Please ensure roles are seeded first.');
        }

        $actions = [
            'read',
            'write',
            'delete',
            'approval'
        ];

        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $sales_person->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $tax->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $unit->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $category->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $tag->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $role->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $user->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }
        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
