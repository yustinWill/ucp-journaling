<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_reimbursements', function (Blueprint $table) {
            $table->string('attachment', 500)->nullable()->after('description')->comment('File attachment path');
        });
    }

    public function down(): void
    {
        Schema::table('t_reimbursements', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
