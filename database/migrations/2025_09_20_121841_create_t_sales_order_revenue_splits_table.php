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
        Schema::create('t_sales_order_revenue_splits', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('f_sales_order_id');
            $table->unsignedBigInteger('f_sales_order_product_id');
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('product_unit_price', 15, 2)->comment('Actual selling price per unit');
            $table->decimal('product_quantity', 15, 3)->comment('Quantity sold');
            $table->decimal('product_total_amount', 15, 2)->comment('Total amount for this product line');
            
            // Revenue split amounts
            $table->decimal('porsi_perusahaan', 15, 2)->default(0)->comment('Company portion amount');
            $table->decimal('porsi_owner', 15, 2)->default(0)->comment('Owner portion amount');
            $table->decimal('porsi_pabrik', 15, 2)->default(0)->comment('Factory portion amount');
            $table->enum('remaining_allocation', ['perusahaan', 'owner', 'pabrik'])->comment('Category that received the remaining amount');
            $table->decimal('remaining_amount', 15, 2)->default(0)->comment('Calculated remaining amount');
            
            // Configuration reference
            $table->unsignedBigInteger('f_revenue_split_config_id')->nullable()->comment('Reference to original config');
            $table->boolean('is_custom_split')->default(false)->comment('True if split was manually edited for this transaction');
            $table->text('split_notes')->nullable()->comment('Notes about this revenue split');
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index(['company_code', 'f_sales_order_id'], 'idx_company_so');
            $table->index('f_sales_order_id', 'idx_sales_order_id');
            $table->index('f_sales_order_product_id', 'idx_so_product_id');
            $table->index('f_product_id', 'idx_product_id');
            $table->index('f_revenue_split_config_id', 'idx_config_id');
            
            // Unique constraint - one split per sales order product
            $table->unique(['f_sales_order_id', 'f_sales_order_product_id'], 'unq_so_product_split');
            
            // Foreign key constraints
            $table->foreign('f_product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('f_revenue_split_config_id')->references('id')->on('m_product_revenue_split_configs')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sales_order_revenue_splits');
    }
};