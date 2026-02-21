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
        Schema::table('t_reimbursements', function (Blueprint $table) {
            $table->date('reimbursement_date')->nullable()->after('reimbursement_name')->comment('Date when the reimbursement expense occurred');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_reimbursements', function (Blueprint $table) {
            $table->dropColumn('reimbursement_date');
        });
    }
};
