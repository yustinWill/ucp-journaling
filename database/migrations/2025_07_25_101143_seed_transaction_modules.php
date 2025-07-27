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
                'name' => 'main_transaction_purchase',
                'slug' => 'main-transaction-purchase',
                'description' => 'Main Transaction Purchase',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'main_transaction_sales',
                'slug' => 'main-transaction-sales',
                'description' => 'Main Transaction Sales',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'main_transaction_stock_adjustment',
                'slug' => 'main-transaction-stock-adjustment',
                'description' => 'Main Transaction Stock Adjustment',
                'is_active' => true,
                'created_at' => now(),
            ],
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