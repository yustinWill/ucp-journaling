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
        Schema::table('t_purchase_order_products', function (Blueprint $table) {
            $table->decimal('delivered_qty', 15, 2)->after('qty')->default(0);
            $table->decimal('remaining_qty', 15, 2)->after('delivered_qty')->default(0);
            $table->string('status')->after('total');
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
