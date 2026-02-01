<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->decimal('total_paid_amount', 15, 2)->default(0)->after('total_amount');
            $table->decimal('total_remaining_amount', 15, 2)->default(0)->after('total_paid_amount');
        });

        // Update total_remaining_amount to match total_amount for existing records
        DB::table('t_expenses')->update([
            'total_remaining_amount' => DB::raw('total_amount'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->dropColumn(['total_paid_amount', 'total_remaining_amount']);
        });
    }
};
