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
        Schema::table('t_purchase_orders', function (Blueprint $table) {
            $table->unsignedInteger('payment_term_day_count')->after('f_payment_term_id')->nullable();
            $table->date('payment_term_due_date')->after('payment_term_day_count')->nullable();
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
