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
            $table->renameColumn('modified_by', 'updated_by');
        });

        Schema::table('m_product_expenses', function (Blueprint $table) {
            $table->renameColumn('modified_by', 'updated_by');
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
