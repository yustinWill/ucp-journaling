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
        Schema::table('t_sales_delivery_products', function (Blueprint $table) {
            // Add tracking columns for quantity changes
            $table->decimal('qty', 15, 4)->default(0)->after('sales_order_product_id')->comment('Quantity being delivered');
            $table->decimal('delivered_qty_before', 15, 4)->default(0)->after('qty')->comment('Delivered qty before this delivery');
            $table->decimal('remaining_qty_before', 15, 4)->default(0)->after('delivered_qty_before')->comment('Remaining qty before this delivery');
            $table->decimal('delivered_qty_after', 15, 4)->default(0)->after('remaining_qty_before')->comment('Delivered qty after this delivery');
            $table->decimal('remaining_qty_after', 15, 4)->default(0)->after('delivered_qty_after')->comment('Remaining qty after this delivery');

            // Add audit columns
            $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');

            // Add foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_delivery_products', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop columns
            $table->dropColumn([
                'qty',
                'delivered_qty_before',
                'remaining_qty_before',
                'delivered_qty_after',
                'remaining_qty_after',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
