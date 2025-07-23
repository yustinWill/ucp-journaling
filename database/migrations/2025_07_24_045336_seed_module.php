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
        $modules = [
            [
                'name' => 'master_data_sales_person',
                'slug' => 'master-data-sales-person',
                'description' => 'Master Data Sales Person',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'master_data_tax',
                'slug' => 'master-data-tax',
                'description' => 'Master Data Tax',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'master_data_unit',
                'slug' => 'master-data-unit',
                'description' => 'Master Data Unit',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'master_data_category',
                'slug' => 'master-data-category',
                'description' => 'Master Data Category',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'master_data_tag',
                'slug' => 'master-data-tag',
                'description' => 'Master Data Tag',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'master_data_role',
                'slug' => 'master-data-role',
                'description' => 'Master Data Role',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'master_data_user',
                'slug' => 'master-data-user',
                'description' => 'Master Data User',
                'is_active' => true,
                'created_at' => now(),
            ]
        ];

        DB::table('modules')->insert($modules);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
