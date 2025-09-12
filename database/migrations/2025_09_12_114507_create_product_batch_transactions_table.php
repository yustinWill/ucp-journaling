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
        Schema::create('t_product_batch_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_product_batch_id');
            $table->unsignedBigInteger('f_product_id');
            $table->enum('transaction_type', ['IN', 'OUT']);
            $table->enum('source_type', ['PURCHASE_DELIVERY', 'SALES_DELIVERY', 'ADJUSTMENT', 'TRANSFER', 'PRODUCTION']);
            $table->unsignedBigInteger('f_source_transaction_id');
            $table->datetime('transaction_date');
            $table->decimal('qty_moved', 15, 4);
            $table->string('reference_code');
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('f_product_batch_id')->references('id')->on('t_product_stock_batches');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->index(['f_product_batch_id', 'transaction_date'], 'idx_batch_transaction_date');
            $table->index(['f_product_id', 'transaction_type'], 'idx_product_transaction_type');
            $table->index(['source_type', 'f_source_transaction_id'], 'idx_source_transaction');
            $table->index(['company_code', 'transaction_date'], 'idx_company_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_product_batch_transactions');
    }
};