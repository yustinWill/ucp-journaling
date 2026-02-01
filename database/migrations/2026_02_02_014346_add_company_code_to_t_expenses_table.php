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
            $table->string('company_code')->after('id');
            $table->foreign('company_code')->references('code')->on('m_companies');
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->dropForeign(['company_code']);
            $table->dropIndex(['company_code']);
            $table->dropColumn('company_code');
        });
    }
};
