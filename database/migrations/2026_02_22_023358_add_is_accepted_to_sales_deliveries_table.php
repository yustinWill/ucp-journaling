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
        Schema::table('t_sales_deliveries', function (Blueprint $table) {
            // Add is_accepted flag after is_confirmed column
            $table->boolean('is_accepted')->default(false)->after('is_confirmed')
                ->comment('Flag to indicate if delivery has been accepted before confirmation');

            // Add accepted_at timestamp
            $table->timestamp('accepted_at')->nullable()->after('is_accepted')
                ->comment('Timestamp when delivery was accepted');

            // Add accepted_by user reference
            $table->unsignedBigInteger('accepted_by')->nullable()->after('accepted_at')
                ->comment('User ID who accepted the delivery');

            // Foreign key for accepted_by
            $table->foreign('accepted_by')->references('id')->on('users')->onDelete('set null');

            // Add index for better query performance
            $table->index(['is_accepted', 'is_confirmed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_deliveries', function (Blueprint $table) {
            // Drop foreign key and index first
            $table->dropForeign(['accepted_by']);
            $table->dropIndex(['is_accepted', 'is_confirmed']);

            // Drop columns
            $table->dropColumn(['is_accepted', 'accepted_at', 'accepted_by']);
        });
    }
};
