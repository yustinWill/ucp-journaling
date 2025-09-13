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
        // Remove the old general sales transaction number configuration
        DB::table('m_transaction_numbers')->where('module_name', 'sales')->delete();

        // Add the three separate sales module transaction numbers
        $salesTransactionNumbers = [
            [
                'module_name' => 'sales_order',
                'module_display_name' => 'Sales Order',
                'module_initial' => 'SO',
                'format_template' => 'SO-{YY}{MM}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 3,
                'reset_frequency' => 'monthly',
                'last_reset_date' => null,
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'module_name' => 'sales_delivery',
                'module_display_name' => 'Sales Delivery',
                'module_initial' => 'SD',
                'format_template' => 'SD-{YY}{MM}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 3,
                'reset_frequency' => 'monthly',
                'last_reset_date' => null,
                'status' => 'active',
                'created_at' => now(),
            ],
            [
                'module_name' => 'sales_invoice',
                'module_display_name' => 'Sales Invoice',
                'module_initial' => 'SI',
                'format_template' => 'SI-{YY}{MM}-{COUNTER}',
                'current_counter' => 1,
                'counter_padding' => 3,
                'reset_frequency' => 'monthly',
                'last_reset_date' => null,
                'status' => 'active',
                'created_at' => now(),
            ],
        ];

        DB::table('m_transaction_numbers')->insert($salesTransactionNumbers);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the separate sales transaction numbers
        $moduleNames = [
            'sales_order',
            'sales_delivery',
            'sales_invoice',
        ];

        DB::table('m_transaction_numbers')->whereIn('module_name', $moduleNames)->delete();

        // Restore the original general sales transaction number
        $originalSales = [
            'module_name' => 'sales',
            'module_display_name' => 'Sales',
            'module_initial' => 'SO',
            'format_template' => 'SO-{MODULE_INITIAL}-{YY}{MM}-{COUNTER}',
            'current_counter' => 1,
            'counter_padding' => 5,
            'reset_frequency' => 'never',
            'last_reset_date' => null,
            'status' => 'active',
            'created_at' => now(),
        ];

        DB::table('m_transaction_numbers')->insert($originalSales);
    }
};
