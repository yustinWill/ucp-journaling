<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Rename main storage table to warehouse
        if (Schema::hasTable('m_storages')) {
            Schema::rename('m_storages', 'm_warehouses');
        }

        // Step 2: Rename audit logs table and update foreign keys
        if (Schema::hasTable('m_storage_audit_logs')) {
            Schema::rename('m_storage_audit_logs', 'm_warehouse_audit_logs');

            // Get the actual foreign key constraint name
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'm_warehouse_audit_logs' 
                AND COLUMN_NAME = 'f_storage_id'
                AND CONSTRAINT_NAME != 'PRIMARY'
            ");

            Schema::table('m_warehouse_audit_logs', function (Blueprint $table) use ($foreignKeys) {
                if (! empty($foreignKeys)) {
                    $table->dropForeign($foreignKeys[0]->CONSTRAINT_NAME);
                }
                $table->renameColumn('f_storage_id', 'f_warehouse_id');
            });

            Schema::table('m_warehouse_audit_logs', function (Blueprint $table) {
                $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
            });
        }

        // Step 3: Stock movements table was removed - using t_stock_movement_logs instead

        // Step 4: Rename stock snapshots table and update foreign keys
        if (Schema::hasTable('t_storage_stock_snapshots')) {
            Schema::rename('t_storage_stock_snapshots', 't_warehouse_stock_snapshots');

            // Get the actual foreign key constraint name
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 't_warehouse_stock_snapshots' 
                AND COLUMN_NAME = 'f_storage_id'
                AND CONSTRAINT_NAME != 'PRIMARY'
            ");

            Schema::table('t_warehouse_stock_snapshots', function (Blueprint $table) use ($foreignKeys) {
                if (! empty($foreignKeys)) {
                    $table->dropForeign($foreignKeys[0]->CONSTRAINT_NAME);
                }
                // Try to drop indexes, but continue if they don't exist
                try {
                    $table->dropIndex('unq_storage_product_snapshot');
                } catch (\Exception $e) {
                    // Index doesn't exist, continue
                }
                try {
                    $table->dropIndex('idx_storage_snapshot_date');
                } catch (\Exception $e) {
                    // Index doesn't exist, continue
                }
                $table->renameColumn('f_storage_id', 'f_warehouse_id');
            });

            Schema::table('t_warehouse_stock_snapshots', function (Blueprint $table) {
                $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
                $table->unique(['f_warehouse_id', 'f_product_id', 'snapshot_date'], 'unq_warehouse_product_snapshot');
                $table->index(['f_warehouse_id', 'snapshot_date'], 'idx_warehouse_snapshot_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse all changes
        if (Schema::hasTable('t_warehouse_stock_snapshots')) {
            Schema::table('t_warehouse_stock_snapshots', function (Blueprint $table) {
                $table->dropForeign(['f_warehouse_id']);
                $table->dropIndex('unq_warehouse_product_snapshot');
                $table->dropIndex('idx_warehouse_snapshot_date');
                $table->renameColumn('f_warehouse_id', 'f_storage_id');
            });

            Schema::table('t_warehouse_stock_snapshots', function (Blueprint $table) {
                $table->foreign('f_storage_id')->references('id')->on('m_storages');
                $table->unique(['f_storage_id', 'f_product_id', 'snapshot_date'], 'unq_storage_product_snapshot');
                $table->index(['f_storage_id', 'snapshot_date'], 'idx_storage_snapshot_date');
            });

            Schema::rename('t_warehouse_stock_snapshots', 't_storage_stock_snapshots');
        }

        // Stock movements table was removed - using t_stock_movement_logs instead

        if (Schema::hasTable('m_warehouse_audit_logs')) {
            Schema::table('m_warehouse_audit_logs', function (Blueprint $table) {
                $table->dropForeign(['f_warehouse_id']);
                $table->renameColumn('f_warehouse_id', 'f_storage_id');
            });

            Schema::table('m_warehouse_audit_logs', function (Blueprint $table) {
                $table->foreign('f_storage_id')->references('id')->on('m_storages');
            });

            Schema::rename('m_warehouse_audit_logs', 'm_storage_audit_logs');
        }

        if (Schema::hasTable('m_warehouses')) {
            Schema::rename('m_warehouses', 'm_storages');
        }
    }
};
