<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates a view that maps the old t_warehouse_stock_movements table name
     * to the current t_stock_movement_logs table for backward compatibility.
     */
    public function up(): void
    {
        // Create a view that maps old table name to new table
        // This provides backward compatibility for any queries still using the old name
        DB::statement("
            CREATE VIEW t_warehouse_stock_movements AS
            SELECT
                id,
                company_code,
                warehouse_id as f_storage_id,
                product_id as f_product_id,
                movement_date,
                movement_type,
                reference_type as source_type,
                reference_number as f_source_transaction_id,
                movement_qty as qty_moved,
                stock_before as qty_before,
                stock_after as qty_after,
                notes,
                created_at,
                updated_at,
                created_by,
                NULL as updated_by
            FROM t_stock_movement_logs
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS t_warehouse_stock_movements");
    }
};
