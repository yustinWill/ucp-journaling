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
        // Check if the old column name still exists before attempting rename
        // This migration may be redundant if 2025_08_20_003200_rename_storage_to_warehouse_final.php already ran
        if (Schema::hasColumn('m_warehouse_audit_logs', 'f_storage_id')) {
            Schema::table('m_warehouse_audit_logs', function (Blueprint $table) {
                $table->renameColumn('f_storage_id', 'f_warehouse_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
