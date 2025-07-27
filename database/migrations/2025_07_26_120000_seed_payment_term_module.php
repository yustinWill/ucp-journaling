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
        $module = [
            'name' => 'master_data_payment_term',
            'slug' => 'master-data-payment-term',
            'description' => 'Master Data Payment Terms',
            'is_active' => true,
            'created_at' => now(),
        ];

        DB::table('modules')->insert($module);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('modules')->where('name', 'master_data_payment_term')->delete();
    }
};