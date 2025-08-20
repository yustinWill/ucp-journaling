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
        Schema::create('t_stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('adjustment_number')->unique();
            $table->date('adjustment_date');
            $table->enum('adjustment_type', [
                'MANUAL_ADJUSTMENT', 
                'STOCK_OPNAME', 
                'PRODUCT_RETURN', 
                'DAMAGE_LOSS', 
                'EXPIRY_DISPOSAL',
                'WAREHOUSE_TRANSFER'
            ]);
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('pic_id');
            $table->enum('status', [
                'DRAFT',
                'WAITING_APPROVAL', 
                'APPROVED',
                'COMPLETED',
                'CANCELLED'
            ])->default('DRAFT');
            $table->text('description');
            $table->text('memo')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->string('reference_number')->nullable(); // For linking to Stock Opname, etc.
            $table->integer('total_items')->default(0);
            $table->decimal('total_adjustment_qty', 15, 2)->default(0);
            $table->decimal('total_adjustment_value', 15, 2)->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->index(['company_code', 'adjustment_date']);
            $table->index(['adjustment_type', 'status']);
            $table->index('warehouse_id');
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stock_adjustments');
    }
};
