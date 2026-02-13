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
        Schema::table('t_return_managements', function (Blueprint $table) {
            // Add warehouse_id column after return_type
            $table->unsignedBigInteger('f_warehouse_id')->after('return_type')->nullable();

            // Add foreign key constraint
            $table->foreign('f_warehouse_id')->references('id')->on('m_warehouses')->onDelete('cascade');

            // Add index for better query performance
            $table->index('f_warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_return_managements', function (Blueprint $table) {
            // Drop foreign key and index first
            $table->dropForeign(['f_warehouse_id']);
            $table->dropIndex(['f_warehouse_id']);

            // Drop the column
            $table->dropColumn('f_warehouse_id');
        });
    }
};
