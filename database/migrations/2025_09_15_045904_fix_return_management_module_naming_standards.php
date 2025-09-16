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
        // Update Return Management module to follow naming standards
        DB::table('modules')
            ->where('slug', 'return_management')
            ->update([
                'name' => 'main_transaction_return_management',
                'slug' => 'main-transaction-return-management',
                'description' => 'Main Transaction Return Management',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Return Management module to previous naming
        DB::table('modules')
            ->where('slug', 'main-transaction-return-management')
            ->update([
                'name' => 'Return Management',
                'slug' => 'return_management',
                'description' => 'Return management for purchase and sales invoices',
                'updated_at' => now(),
            ]);
    }
};