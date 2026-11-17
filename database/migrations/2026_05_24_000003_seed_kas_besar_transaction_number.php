<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('m_transaction_numbers')->insert([
            'module_name'         => 'kas_besar',
            'module_display_name' => 'Kas Besar',
            'module_initial'      => 'KB',
            'format_template'     => 'KB-{YEAR}-{MM}-{COUNTER}',
            'current_counter'     => 0,
            'counter_padding'     => 4,
            'reset_frequency'     => 'monthly',
            'last_reset_date'     => now()->toDateString(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('m_transaction_numbers')->where('module_name', 'kas_besar')->delete();
    }
};
