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
        Schema::table('t_storage_stock_movements', function (Blueprint $table) {
            // Drop the unnecessary columns for simplified stock movement tracking
            $table->dropColumn(['unit_hpp', 'total_value', 'f_consumed_batch_ids']);
        });
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