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
        Schema::table('t_purchase_deliveries', function (Blueprint $table) {
            // Add status column after total_item
            // Status values: PENDING, IN_TRANSIT, DELIVERED, CANCELLED, RETURNED
            $table->string('status')->default('PENDING')->after('total_item');

            // Add index for better query performance
            $table->index(['delivery_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_purchase_deliveries', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex(['delivery_date', 'status']);

            // Drop the status column
            $table->dropColumn('status');
        });
    }
};
