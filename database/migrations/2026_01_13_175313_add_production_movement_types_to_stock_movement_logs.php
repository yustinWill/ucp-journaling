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
        DB::statement("ALTER TABLE t_stock_movement_logs
            MODIFY COLUMN movement_type ENUM(
                'ADJUSTMENT_IN',
                'ADJUSTMENT_OUT',
                'TRANSFER_IN',
                'TRANSFER_OUT',
                'PURCHASE_IN',
                'SALES_OUT',
                'RETURN_IN',
                'RETURN_OUT',
                'DAMAGE_OUT',
                'EXPIRY_OUT',
                'PRODUCTION_IN',
                'PRODUCTION_OUT'
            ) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE t_stock_movement_logs
            MODIFY COLUMN movement_type ENUM(
                'ADJUSTMENT_IN',
                'ADJUSTMENT_OUT',
                'TRANSFER_IN',
                'TRANSFER_OUT',
                'PURCHASE_IN',
                'SALES_OUT',
                'RETURN_IN',
                'RETURN_OUT',
                'DAMAGE_OUT',
                'EXPIRY_OUT'
            ) NOT NULL");
    }
};
