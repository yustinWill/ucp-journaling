<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// The expense payment form numbers its payments from this row. Staging got it by hand; production never had it.
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('m_transaction_numbers')->where('module_name', 'expense_payment')->exists()) {
            return;
        }

        DB::table('m_transaction_numbers')->insert([
            'company_code' => DB::table('m_transaction_numbers')->where('module_name', 'expense')->value('company_code'),
            'module_name' => 'expense_payment',
            'module_display_name' => 'Expense Payment',
            'module_initial' => 'EP',
            'format_template' => 'EP-{YY}{MM}{DD}-{COUNTER}',
            'current_counter' => 0,
            'counter_padding' => 5,
            'reset_frequency' => 'daily',
            'last_reset_date' => now()->toDateString(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Left in place: staging's row predates this migration, and payments may already carry its numbers.
    }
};
