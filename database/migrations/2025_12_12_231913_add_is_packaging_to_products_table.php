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
        Schema::table('m_products', function (Blueprint $table) {
            // Add is_packaging column
            // Note: Position after 'track_batch' since 'is_bundle' column doesn't exist in the schema
            if (! Schema::hasColumn('m_products', 'is_packaging')) {
                $table->boolean('is_packaging')->default(false)->after('track_batch')->comment('Flag to mark products that are used for packing (subset of bundle products)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_products', function (Blueprint $table) {
            $table->dropColumn('is_packaging');
        });
    }
};
