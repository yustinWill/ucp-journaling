<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_contacts', function (Blueprint $table) {
            // Add boolean flags for contact types
            $table->boolean('is_customer')->default(false)->after('type');
            $table->boolean('is_supplier')->default(false)->after('is_customer');
            $table->boolean('is_employee')->default(false)->after('is_supplier');
            $table->boolean('is_other')->default(false)->after('is_employee');
        });

        // Data migration: Populate boolean flags from existing type column
        DB::table('m_contacts')->where('type', 'CUSTOMER')->update(['is_customer' => true]);
        DB::table('m_contacts')->where('type', 'SUPPLIER')->update(['is_supplier' => true]);
        DB::table('m_contacts')->where('type', 'EMPLOYEE')->update(['is_employee' => true]);
        DB::table('m_contacts')->where('type', 'OTHERS')->update(['is_other' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_contacts', function (Blueprint $table) {
            $table->dropColumn(['is_customer', 'is_supplier', 'is_employee', 'is_other']);
        });
    }
};
