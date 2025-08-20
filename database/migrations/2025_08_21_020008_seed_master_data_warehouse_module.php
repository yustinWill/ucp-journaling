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
        // Check if master_data_warehouse module already exists
        $existingModule = DB::table('modules')
            ->where('name', 'master_data_warehouse')
            ->first();

        if (!$existingModule) {
            $modules = [
                [
                    'name' => 'master_data_warehouse',
                    'slug' => 'master-data-warehouse',
                    'description' => 'Master Data Warehouse',
                    'is_active' => true,
                    'created_at' => now(),
                ]
            ];

            DB::table('modules')->insert($modules);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('modules')->where('name', 'master_data_warehouse')->delete();
    }
};