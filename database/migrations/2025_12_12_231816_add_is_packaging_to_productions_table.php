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
        Schema::table('t_productions', function (Blueprint $table) {
            $table->boolean('is_packaging')->default(false)->after('status')->comment('Flag to distinguish packing production from regular production');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_productions', function (Blueprint $table) {
            $table->dropColumn('is_packaging');
        });
    }
};
