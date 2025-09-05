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
        // Update existing data to uppercase for standardization
        DB::table('m_payment_terms')->update([
            'type' => DB::raw('UPPER(type)'),
            'status' => DB::raw('UPPER(status)'),
        ]);

        // Update ENUM columns to uppercase values for consistency
        DB::statement("ALTER TABLE m_payment_terms MODIFY COLUMN type ENUM('NET', 'IMMEDIATE', 'COD', 'CUSTOM') NOT NULL DEFAULT 'NET'");
        DB::statement("ALTER TABLE m_payment_terms MODIFY COLUMN status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data to lowercase
        DB::table('m_payment_terms')->update([
            'type' => DB::raw('LOWER(type)'),
            'status' => DB::raw('LOWER(status)'),
        ]);

        // Restore original enum with lowercase values
        DB::statement("ALTER TABLE m_payment_terms MODIFY COLUMN type ENUM('net', 'immediate', 'cod', 'custom') NOT NULL DEFAULT 'net'");
        DB::statement("ALTER TABLE m_payment_terms MODIFY COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'");
    }
};
