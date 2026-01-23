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
            $table->decimal('delivered_qty', 15, 4)->default(0)->after('quantity')->comment('Quantity already delivered');
            $table->decimal('remaining_qty', 15, 4)->default(0)->after('delivered_qty')->comment('Remaining quantity to be delivered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            $table->dropColumn(['delivered_qty', 'remaining_qty']);
        });
    }
};
