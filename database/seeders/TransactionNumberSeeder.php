<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactionNumbers = [
            [
                'module_name' => 'expense',
                'module_display_name' => 'Expense',
                'module_initial' => 'EX',
                'format_template' => 'EXP-{YEAR}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 3,
                'reset_frequency' => 'yearly',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'purchase_order',
                'module_display_name' => 'Purchase Order',
                'module_initial' => 'PO',
                'format_template' => 'PO-{YY}{MM}{DD}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 3,
                'reset_frequency' => 'daily',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'purchase_delivery',
                'module_display_name' => 'Purchase Delivery',
                'module_initial' => 'PD',
                'format_template' => 'PD-{YEAR}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 4,
                'reset_frequency' => 'yearly',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'purchase_invoice',
                'module_display_name' => 'Purchase Invoice',
                'module_initial' => 'PI',
                'format_template' => 'PI-{YEAR}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 4,
                'reset_frequency' => 'yearly',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'sales',
                'module_display_name' => 'Sales',
                'module_initial' => 'SO',
                'format_template' => 'SO-{MODULE_INITIAL}-{YY}{MM}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 4,
                'reset_frequency' => 'monthly',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'journal',
                'module_display_name' => 'Journal',
                'module_initial' => 'JR',
                'format_template' => 'JRN-{YEAR}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 5,
                'reset_frequency' => 'yearly',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'credit_memo',
                'module_display_name' => 'Credit Memo',
                'module_initial' => 'CM',
                'format_template' => 'CM-{YEAR}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 4,
                'reset_frequency' => 'yearly',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'module_name' => 'stock_adjustment',
                'module_display_name' => 'Stock Adjustment',
                'module_initial' => 'SA',
                'format_template' => 'SA-{YY}{MM}{DD}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 3,
                'reset_frequency' => 'daily',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('m_transaction_numbers')->insert($transactionNumbers);
    }
}
