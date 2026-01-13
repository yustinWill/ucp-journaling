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
        Schema::table('t_productions', function (Blueprint $table) {
            // Add missing columns after attachment
            $table->decimal('bundle_price', 15, 2)->default(0)->after('attachment')->comment('Bundle selling price');
            $table->decimal('bundle_price_unit', 15, 2)->default(0)->after('bundle_price')->comment('Bundle price per unit');
            $table->decimal('total_component_cost_unit', 15, 2)->default(0)->after('bundle_price_unit')->comment('Total component cost per unit');
            $table->decimal('hpp_production', 15, 2)->default(0)->after('total_component_cost_unit')->comment('HPP per unit including production costs');
            $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

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
        Schema::table('t_productions', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop columns
            $table->dropColumn([
                'bundle_price',
                'bundle_price_unit',
                'total_component_cost_unit',
                'hpp_production',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
