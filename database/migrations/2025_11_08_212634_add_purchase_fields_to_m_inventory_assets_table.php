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
            $table->string('payment_terms')->nullable()->after('vendor_name');
            $table->date('due_date')->nullable()->after('payment_terms');
            $table->string('f_purchase_coa_code')->nullable()->after('f_asset_coa_code');
            $table->integer('depreciation_period_months')->nullable()->after('depreciation_rate');
            $table->decimal('total_depreciation', 15, 2)->default(0)->after('net_book_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_inventory_assets', function (Blueprint $table) {
            $table->dropColumn([
                'payment_terms',
                'due_date',
                'f_purchase_coa_code',
                'depreciation_period_months',
                'total_depreciation'
            ]);
        });
    }
};
