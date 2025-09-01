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
        Schema::table('t_stock_opnames', function (Blueprint $table) {
            $table->unsignedInteger('count_product')->after('f_tag_codes')->default(0);
            $table->decimal('total_system_stock', 15, 2)->after('count_product')->default(0);
            $table->decimal('total_physical_stock', 15, 2)->after('total_system_stock')->default(0);
            $table->decimal('total_variance', 15, 2)->after('total_physical_stock')->default(0);
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
