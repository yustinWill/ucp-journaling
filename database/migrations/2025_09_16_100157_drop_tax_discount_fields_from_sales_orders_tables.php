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
        // Tax and discount fields from t_sales_orders table already dropped
        // This migration serves as a record that these fields were removed
        
        // Skip - fields already removed in previous operation
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore tax and discount fields to t_sales_orders table
        Schema::table('t_sales_orders', function (Blueprint $table) {
            $table->decimal('total_price_before_discount', 20, 2)->nullable()->after('grand_price');
            $table->decimal('total_tax', 20, 2)->nullable()->after('total_price_before_discount');
            $table->decimal('discount', 20, 2)->nullable()->after('total_tax');
        });

        // Restore tax fields to t_sales_order_products table
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('f_tax_id')->nullable()->after('unit_price');
            $table->decimal('tax_amount', 20, 2)->nullable()->after('f_tax_id');
            // Restore foreign key constraint
            $table->foreign('f_tax_id')->references('id')->on('m_taxes')->onDelete('set null');
        });
    }
};
