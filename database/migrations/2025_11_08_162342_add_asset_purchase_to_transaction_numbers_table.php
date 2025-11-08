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
        DB::table('m_transaction_numbers')->insert([
            'module_name' => 'asset_purchase',
            'module_display_name' => 'Asset Purchase',
            'module_initial' => 'AP',
            'format_template' => 'AP-{YEAR}-{COUNTER}',
            'current_counter' => 1,
            'counter_padding' => 4,
            'reset_frequency' => 'yearly',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('m_transaction_numbers')
            ->where('module_name', 'asset_purchase')
            ->delete();
    }
};
