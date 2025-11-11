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
        Schema::table('m_contact_addresses', function (Blueprint $table) {
            if (! Schema::hasColumn('m_contact_addresses', 'area')) {
                $table->string('area', 255)->nullable()->after('postal_code');
            }
            if (! Schema::hasColumn('m_contact_addresses', 'maps_url')) {
                $table->string('maps_url', 1000)->nullable()->after('area');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_contact_addresses', function (Blueprint $table) {
            $table->dropColumn(['area', 'maps_url']);
        });
    }
};
