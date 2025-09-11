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
        // Drop the redundant t_storage_stock_movements table
        // We are consolidating to use t_stock_movement_logs instead
        Schema::dropIfExists('t_storage_stock_movements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the table if needed to rollback
        Schema::create('t_storage_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_storage_id');
            $table->unsignedBigInteger('f_product_id');
            $table->datetime('movement_date');
            $table->enum('movement_type', ['IN', 'OUT', 'TRANSFER_IN', 'TRANSFER_OUT', 'ADJUSTMENT']);
            $table->enum('source_type', ['PRODUCTION', 'PURCHASE', 'SALES', 'TRANSFER', 'ADJUSTMENT', 'DISCARD']);
            $table->unsignedBigInteger('f_source_transaction_id');
            $table->decimal('qty_moved', 15, 4);
            $table->decimal('qty_before', 15, 4);
            $table->decimal('qty_after', 15, 4);
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('f_storage_id')->references('id')->on('m_storages');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->index(['f_storage_id', 'f_product_id', 'movement_date'], 'idx_storage_product_movement');
            $table->index(['company_code', 'movement_type'], 'idx_company_movement_type');
            $table->index(['source_type', 'f_source_transaction_id'], 'idx_source_transaction');
            $table->index('movement_date', 'idx_movement_date');
        });
    }
};
