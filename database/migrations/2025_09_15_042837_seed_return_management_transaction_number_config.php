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
        // Insert Purchase Return transaction number configuration
        DB::table('m_transaction_numbers')->insert([
            'company_code' => '001',
            'module_name' => 'purchase_return',
            'module_display_name' => 'Purchase Return',
            'module_initial' => 'PR',
            'format_template' => 'PR-{YEAR}-{COUNTER}',
            'current_counter' => 0,
            'counter_padding' => 4,
            'reset_frequency' => 'yearly',
            'last_reset_date' => now()->format('Y-01-01'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Sales Return transaction number configuration  
        DB::table('m_transaction_numbers')->insert([
            'company_code' => '001',
            'module_name' => 'sales_return',
            'module_display_name' => 'Sales Return',
            'module_initial' => 'SR',
            'format_template' => 'SR-{YEAR}-{COUNTER}',
            'current_counter' => 0,
            'counter_padding' => 4,
            'reset_frequency' => 'yearly',
            'last_reset_date' => now()->format('Y-01-01'),
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
            ->whereIn('module_name', ['purchase_return', 'sales_return'])
            ->delete();
    }
};