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
        Schema::table('m_product_units', function (Blueprint $table) {
            $table->dropColumn('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_product_units', function (Blueprint $table) {
            $table->decimal('barcode', 50, 0)->nullable()->comment('Barcode for this specific unit')->after('conversion_factor');
        });
    }
};
