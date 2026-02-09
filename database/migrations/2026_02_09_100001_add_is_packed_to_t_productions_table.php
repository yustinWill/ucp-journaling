<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_productions', function (Blueprint $table) {
            $table->boolean('is_packed')->default(false)->after('is_packaging')
                ->comment('Flag to indicate if this production has been packed');
        });
    }

    public function down(): void
    {
        Schema::table('t_productions', function (Blueprint $table) {
            $table->dropColumn('is_packed');
        });
    }
};
