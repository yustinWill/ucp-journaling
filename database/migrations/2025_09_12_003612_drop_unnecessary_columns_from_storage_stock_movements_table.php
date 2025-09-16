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
        // Check if table exists before attempting to modify it
        if (Schema::hasTable('t_storage_stock_movements')) {
            Schema::table('t_storage_stock_movements', function (Blueprint $table) {
                // Drop the unnecessary columns for simplified stock movement tracking
                if (Schema::hasColumn('t_storage_stock_movements', 'unit_hpp')) {
                    $table->dropColumn('unit_hpp');
                }
                if (Schema::hasColumn('t_storage_stock_movements', 'total_value')) {
                    $table->dropColumn('total_value');
                }
                if (Schema::hasColumn('t_storage_stock_movements', 'f_consumed_batch_ids')) {
                    $table->dropColumn('f_consumed_batch_ids');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_storage_stock_movements', function (Blueprint $table) {
            // Recreate the columns if rollback is needed
            $table->decimal('unit_hpp', 15, 2);
            $table->decimal('total_value', 15, 2);
            $table->json('f_consumed_batch_ids')->nullable(); // array of batch IDs consumed for OUT movements
        });
    }
};
