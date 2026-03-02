<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_stock_movement_logs', function (Blueprint $table) {
            $table->decimal('movement_qty', 15, 4)->nullable(false)->change();
            $table->decimal('stock_before', 15, 4)->nullable(false)->change();
            $table->decimal('stock_after', 15, 4)->nullable(false)->change();
            $table->decimal('unit_cost', 15, 4)->nullable(false)->default(0.00)->change();
            $table->decimal('total_value', 15, 4)->nullable(false)->default(0.00)->change();
        });
    }

    public function down(): void
    {
        Schema::table('t_stock_movement_logs', function (Blueprint $table) {
            $table->decimal('movement_qty', 15, 2)->nullable(false)->change();
            $table->decimal('stock_before', 15, 2)->nullable(false)->change();
            $table->decimal('stock_after', 15, 2)->nullable(false)->change();
            $table->decimal('unit_cost', 15, 2)->nullable(false)->default(0.00)->change();
            $table->decimal('total_value', 15, 2)->nullable(false)->default(0.00)->change();
        });
    }
};
