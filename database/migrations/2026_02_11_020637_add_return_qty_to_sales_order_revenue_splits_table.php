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
        Schema::table('t_sales_order_revenue_splits', function (Blueprint $table) {
            $table->decimal('return_qty', 15, 3)->default(0)->after('product_quantity')->comment('Quantity returned/canceled');
            $table->decimal('effective_qty', 15, 3)->default(0)->after('return_qty')->comment('Effective quantity (product_quantity - return_qty)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_revenue_splits', function (Blueprint $table) {
            $table->dropColumn(['return_qty', 'effective_qty']);
        });
    }
};
