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
        Schema::create('t_stock_movement_logs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->date('movement_date');
            $table->enum('movement_type', [
                'ADJUSTMENT_IN',
                'ADJUSTMENT_OUT', 
                'TRANSFER_IN',
                'TRANSFER_OUT',
                'PURCHASE_IN',
                'SALES_OUT',
                'RETURN_IN',
                'RETURN_OUT',
                'DAMAGE_OUT',
                'EXPIRY_OUT'
            ]);
            $table->string('reference_type'); // e.g., 'STOCK_ADJUSTMENT', 'PURCHASE_ORDER', etc.
            $table->string('reference_number');
            $table->unsignedBigInteger('warehouse_id');
            $table->string('warehouse_code');
            $table->string('warehouse_name');
            $table->unsignedBigInteger('product_id');
            $table->string('product_code');
            $table->string('product_name');
            $table->string('unit');
            $table->decimal('stock_before', 15, 2);
            $table->decimal('movement_qty', 15, 2);
            $table->decimal('stock_after', 15, 2);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->index(['company_code', 'movement_date']);
            $table->index(['warehouse_id', 'product_id']);
            $table->index(['reference_type', 'reference_number']);
            $table->index('movement_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stock_movement_logs');
    }
};
