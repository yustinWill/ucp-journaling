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
        Schema::table('t_purchase_orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_price_include_tax',
                'subtotal',
                'total_tax',
                'total_price_before_discount',
                'discount',
            ]);
            $table->unsignedInteger('f_customer_id')->after('supplier_reference_no')->nullable();
            $table->foreign('f_customer_id')->references('id')->on('m_contacts');
        });

        Schema::table('t_purchase_order_products', function (Blueprint $table) {
            $table->dropForeign(['f_tax_id']);
            $table->dropColumn([
                'f_tax_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
