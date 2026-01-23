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
            // Modify quantity_delivered to have a default value
            $table->decimal('quantity_delivered', 10, 2)->default(0)->change();
            // Also modify quantity_ordered to have consistent default
            $table->decimal('quantity_ordered', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_delivery_products', function (Blueprint $table) {
            // Revert back to no default (nullable false is maintained)
            $table->decimal('quantity_delivered', 10, 2)->nullable(false)->default(null)->change();
            $table->decimal('quantity_ordered', 10, 2)->nullable(false)->default(null)->change();
        });
    }
};
