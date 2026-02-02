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
        // Check if top_up module already exists
        $exists = DB::table('m_transaction_numbers')
            ->where('module_name', 'top_up')
            ->exists();

        if (!$exists) {
            DB::table('m_transaction_numbers')->insert([
                'module_name' => 'top_up',
                'module_display_name' => 'Top Up',
                'module_initial' => 'TU',
                'format_template' => 'TU-{YY}{MM}{DD}-{COUNTER}',
                'current_counter' => 0,
                'counter_padding' => 4,
                'reset_frequency' => 'daily',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('m_transaction_numbers')
            ->where('module_name', 'top_up')
            ->delete();
    }
};
