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
        Schema::create('t_product_stock_batches', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_product_id');
            $table->string('batch_reference_code');
            $table->enum('source_type', ['PRODUCTION', 'PURCHASE', 'STOCK_ADJUSTMENT', 'TRANSFER_IN', 'OTHER']);
            $table->unsignedBigInteger('f_source_transaction_id');
            $table->date('batch_date');
            $table->decimal('initial_qty', 15, 4);
            $table->decimal('remaining_qty', 15, 4);
            $table->decimal('raw_material_cost', 15, 2)->default(0);
            $table->decimal('production_cost', 15, 2)->default(0);
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('estimated_hpp', 15, 2);
            $table->decimal('final_hpp', 15, 2)->nullable();
            $table->enum('hpp_status', ['UNSETTLED', 'SETTLED'])->default('UNSETTLED');
            $table->enum('status', ['ACTIVE', 'CONSUMED', 'EXPIRED'])->default('ACTIVE');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->unique(['batch_reference_code', 'company_code']);
            $table->index(['f_product_id', 'status', 'batch_date']);
            $table->index(['company_code', 'source_type']);
            $table->index(['remaining_qty', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_product_stock_batches');
    }
};
