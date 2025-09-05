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
        Schema::table('t_purchase_delivery_products', function (Blueprint $table) {
            $table->unsignedBigInteger('f_product_order_product_id')->after('f_purchase_delivery_id');
            $table->foreign('f_product_order_product_id')->references('id')->on('t_purchase_order_products');
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
