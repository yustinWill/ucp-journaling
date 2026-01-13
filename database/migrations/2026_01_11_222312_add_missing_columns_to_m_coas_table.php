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
        Schema::table('m_coas', function (Blueprint $table) {
            if (!Schema::hasColumn('m_coas', 'f_parent_coa_code')) {
                $table->string('f_parent_coa_code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('m_coas', 'group_type')) {
                $table->string('group_type')->nullable()->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_coas', function (Blueprint $table) {
            if (Schema::hasColumn('m_coas', 'f_parent_coa_code')) {
                $table->dropColumn('f_parent_coa_code');
            }
            if (Schema::hasColumn('m_coas', 'group_type')) {
                $table->dropColumn('group_type');
            }
        });
    }
};
