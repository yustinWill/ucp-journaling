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
            // Add production mode field
            $table->enum('production_mode', ['QUANTITY_PLANNED', 'MATERIAL_BASED'])
                ->default('QUANTITY_PLANNED')
                ->after('status')
                ->comment('Production calculation mode: QUANTITY_PLANNED (default) or MATERIAL_BASED');

            // Add leftover materials JSON field for Material-Based mode
            $table->json('leftover_materials')->nullable()->after('production_mode')
                ->comment('Leftover materials data with 4-decimal precision for stock opname (Material-Based mode only)');
        });

        Schema::table('t_production_items', function (Blueprint $table) {
            // Add material input field to track original input in Material-Based mode
            $table->decimal('material_input', 15, 4)->nullable()->after('quantity_consumed')
                ->comment('Original material input amount (Material-Based mode only)');

            // Add leftover field to track remaining material per item
            $table->decimal('leftover_amount', 15, 4)->nullable()->after('material_input')
                ->comment('Leftover material amount with 4-decimal precision (Material-Based mode only)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_productions', function (Blueprint $table) {
            $table->dropColumn(['production_mode', 'leftover_materials']);
        });

        Schema::table('t_production_items', function (Blueprint $table) {
            $table->dropColumn(['material_input', 'leftover_amount']);
        });
    }
};
