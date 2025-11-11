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
            // Check if columns exist before adding
            if (! Schema::hasColumn('m_inventory_assets', 'coa_depreciation_debit')) {
                $table->decimal('coa_depreciation_debit', 15, 2)->default(0)->after('status');
            }
            if (! Schema::hasColumn('m_inventory_assets', 'coa_depreciation_credit')) {
                $table->decimal('coa_depreciation_credit', 15, 2)->default(0)->after('coa_depreciation_debit');
            }
            if (! Schema::hasColumn('m_inventory_assets', 'f_sales_coa_code')) {
                $table->string('f_sales_coa_code')->nullable()->after('coa_depreciation_credit');
            }
            if (! Schema::hasColumn('m_inventory_assets', 'disposal_code')) {
                $table->string('disposal_code')->nullable()->after('disposal_date');
            }
            if (! Schema::hasColumn('m_inventory_assets', 'f_difference_coa_code')) {
                $table->string('f_difference_coa_code')->nullable()->after('disposal_code');
            }
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
