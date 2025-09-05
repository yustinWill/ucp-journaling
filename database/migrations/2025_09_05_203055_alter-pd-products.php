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
            $table->decimal('delivered_qty_before', 15, 2)->after('f_product_id')->default(0);
            $table->decimal('remaining_qty_before', 15, 2)->after('delivered_qty_before')->default(0);
            $table->decimal('delivered_qty_after', 15, 2)->after('qty')->default(0);
            $table->decimal('remaining_qty_after', 15, 2)->after('delivered_qty_after')->default(0);
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
