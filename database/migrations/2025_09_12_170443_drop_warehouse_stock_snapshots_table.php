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
        // Drop t_warehouse_stock_snapshots table as we centralize all stock tracking
        // in t_stock_movement_logs for quantity tracking only (no pricing/values)
        Schema::dropIfExists('t_warehouse_stock_snapshots');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table structure if needed to rollback
        Schema::create('t_warehouse_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_warehouse_id');
            $table->unsignedBigInteger('f_product_id');
            $table->date('snapshot_date');
            $table->decimal('opening_stock', 15, 4)->default(0);
            $table->decimal('stock_in', 15, 4)->default(0);
            $table->decimal('stock_out', 15, 4)->default(0);
            $table->decimal('closing_stock', 15, 4)->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->unique(['f_warehouse_id', 'f_product_id', 'snapshot_date'], 'unq_warehouse_product_snapshot');
            $table->index(['f_warehouse_id', 'snapshot_date'], 'idx_warehouse_snapshot_date');
        });
    }
};
