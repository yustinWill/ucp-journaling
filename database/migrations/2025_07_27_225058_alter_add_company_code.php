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
        $table_names = [
            'm_categories',
            'm_category_audit_logs',
            'm_coa_audit_logs',
            'm_coas',
            'm_payment_terms',
            'm_sales_person_audit_logs',
            'm_sales_persons',
            'm_tag_audit_logs',
            'm_tag_colors',
            'm_tag_usage_logs',
            'm_tags',
            'm_tax_audit_logs',
            'm_taxes',
            'm_transaction_numbers',
            'm_unit_audit_logs',
            'm_unit_types',
            'm_units',
            'm_user_audit_logs',
            'role_permissions',
        ];

        foreach ($table_names as $key => $value) {
            Schema::table($value, function (Blueprint $table) {
                $table->string('company_code')->after('id')->nullable();
                $table->index(['company_code']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
