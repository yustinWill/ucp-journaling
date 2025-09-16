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
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign('t_sales_order_products_tax_id_foreign');
            $table->dropColumn(['f_tax_id', 'tax_amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('f_tax_id')->nullable()->after('unit_price');
            $table->decimal('tax_amount', 20, 2)->nullable()->after('f_tax_id');
            // Restore foreign key constraint
            $table->foreign('f_tax_id')->references('id')->on('m_taxes')->onDelete('set null');
        });
    }
};
