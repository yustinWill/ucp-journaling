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
        Schema::create('m_product_revenue_split_configs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('porsi_perusahaan', 15, 2)->default(0)->comment('Fixed amount for company portion');
            $table->decimal('porsi_owner', 15, 2)->default(0)->comment('Fixed amount for owner portion');
            $table->decimal('porsi_pabrik', 15, 2)->default(0)->comment('Fixed amount for factory portion');
            $table->enum('remaining_allocation', ['perusahaan', 'owner', 'pabrik'])->default('perusahaan')->comment('Category that gets the remaining amount');
            $table->decimal('base_price', 15, 2)->default(0)->comment('Reference price used for configuration');
            $table->text('notes')->nullable()->comment('Additional configuration notes');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index(['company_code', 'f_product_id'], 'idx_company_product');
            $table->index('f_product_id', 'idx_product_id');
            $table->index('status', 'idx_status');
            
            // Unique constraint - one active config per product per company
            $table->unique(['company_code', 'f_product_id', 'status'], 'unq_active_product_config');
            
            // Foreign key constraints
            $table->foreign('f_product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_product_revenue_split_configs');
    }
};