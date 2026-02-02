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
            if (!Schema::hasColumn('t_expenses', 'category_type')) {
                $table->string('category_type')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('t_expenses', 'entity_type')) {
                $table->string('entity_type')->nullable()->after('category_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('t_expenses', 'entity_type')) {
                $table->dropColumn('entity_type');
            }
            if (Schema::hasColumn('t_expenses', 'category_type')) {
                $table->dropColumn('category_type');
            }
        });
    }
};
