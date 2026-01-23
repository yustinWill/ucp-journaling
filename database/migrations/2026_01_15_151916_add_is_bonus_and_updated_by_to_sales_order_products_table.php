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
            $table->boolean('is_bonus')->default(false)->after('unit_price')->comment('Whether this is a bonus product');
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');

            // Add foreign key
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_products', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['updated_by']);

            // Drop columns
            $table->dropColumn(['is_bonus', 'updated_by']);
        });
    }
};
