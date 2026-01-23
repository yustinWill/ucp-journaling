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
        // Add f_unit_id to sales order products table
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            if (!Schema::hasColumn('t_sales_order_products', 'f_unit_id')) {
                $table->unsignedBigInteger('f_unit_id')->nullable()->after('f_product_id');

                // Add foreign key
                $table->foreign('f_unit_id')->references('id')->on('m_units')->onDelete('set null');
            }
        });

        // Add f_unit_id to sales delivery products table
        Schema::table('t_sales_delivery_products', function (Blueprint $table) {
            if (!Schema::hasColumn('t_sales_delivery_products', 'f_unit_id')) {
                $table->unsignedBigInteger('f_unit_id')->nullable()->after('product_id');

                // Add foreign key
                $table->foreign('f_unit_id')->references('id')->on('m_units')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            $table->dropForeign(['f_unit_id']);
            $table->dropColumn('f_unit_id');
        });

        Schema::table('t_sales_delivery_products', function (Blueprint $table) {
            $table->dropForeign(['f_unit_id']);
            $table->dropColumn('f_unit_id');
        });
    }
};
