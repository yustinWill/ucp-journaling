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
        Schema::table('m_product_compositions', function (Blueprint $table) {
            $table->unsignedBigInteger('f_unit_id')->after('qty');
            $table->foreign('f_unit_id')->references('id')->on('m_units');
            $table->dropColumn('price');
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
