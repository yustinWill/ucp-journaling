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
        Schema::create('t_productions', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('production_number')->unique();
            $table->date('production_date');
            $table->unsignedBigInteger('bundle_product_id'); // References m_products
            $table->unsignedBigInteger('warehouse_id'); // References m_warehouses
            $table->unsignedBigInteger('pic_id'); // References users
            $table->integer('quantity_planned');
            $table->integer('quantity_produced')->default(0);
            $table->enum('status', [
                'DRAFT',
                'PLANNED',
                'IN_PROGRESS',
                'COMPLETED',
                'CANCELLED',
            ])->default('DRAFT');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('reference_number')->nullable();
            $table->json('f_tag_codes')->nullable(); // Tag references
            $table->decimal('total_raw_material_cost', 15, 2)->default(0);
            $table->decimal('total_production_expense', 15, 2)->default(0);
            $table->decimal('total_production_cost', 15, 2)->default(0);
            $table->integer('total_items_consumed')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['company_code', 'production_date']);
            $table->index(['bundle_product_id', 'warehouse_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_productions');
    }
};
