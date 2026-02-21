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
        // Get all permissions and update group based on permission name
        $permissions = DB::table('permissions')->get();

        foreach ($permissions as $permission) {
            // Extract group from permission name (first part before underscore)
            $group = strtok($permission->name, '_');
            
            DB::table('permissions')
                ->where('id', $permission->id)
                ->update(['group' => $group]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all groups back to null
        DB::table('permissions')->update(['group' => null]);
    }
};
