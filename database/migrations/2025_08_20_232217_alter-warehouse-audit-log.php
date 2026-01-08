<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is redundant - the column rename is already handled by
        // 2025_08_20_003200_rename_storage_to_warehouse_final.php migration
        // Keeping this file for migration history, but doing nothing

        // Original code (now disabled):
        // Schema::table('m_warehouse_audit_logs', function (Blueprint $table) {
        //     $table->renameColumn('f_storage_id', 'f_warehouse_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
