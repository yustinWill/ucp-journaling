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
         Schema::table('m_inventory_assets', function (Blueprint $table) {
            // Add purchase-related fields
            $table->decimal('coa_depreciation_debit',15,2)->default(0)->after('status');
            $table->decimal('coa_depreciation_credit',15,2)->default(0)->after('coa_depreciation_debit');
            $table->string('f_sales_coa_code')->nullable()->after('coa_depreciation_credit');
            $table->string('disposal_code')->nullable()->after('disposal_date');
            $table->string('f_difference_coa_code')->nullable->after('disposal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_inventory_assets', function (Blueprint $table) {
            $table->dropColumn([
                'f_coa_depreciation_debit',
                'f_coa_depreciation_credit',
                'f_sales_coa_code',
                'f_difference_coa_code',
                'disposal_code'
            ]);
        });
    }
};
