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
            $table->string('is_confirmed', 1)->default('0')->after('status')->comment('Whether delivery is confirmed (0=No, 1=Yes)');
            $table->timestamp('confirm_at')->nullable()->after('is_confirmed')->comment('Confirmation timestamp');
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirm_at')->comment('User who confirmed');

            // Add foreign key
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_deliveries', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['confirmed_by']);

            // Drop columns
            $table->dropColumn(['is_confirmed', 'confirm_at', 'confirmed_by']);
        });
    }
};
