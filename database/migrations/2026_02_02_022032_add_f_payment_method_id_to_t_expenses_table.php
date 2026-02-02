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
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('f_payment_method_id')->nullable()->after('f_payment_term_id');
            $table->foreign('f_payment_method_id')->references('id')->on('m_payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->dropForeign(['f_payment_method_id']);
            $table->dropColumn('f_payment_method_id');
        });
    }
};
