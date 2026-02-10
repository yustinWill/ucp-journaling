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
        Schema::table('m_product_revenue_split_configs', function (Blueprint $table) {
            // Drop the old unique constraint that incorrectly includes status
            // The old constraint prevented multiple INACTIVE records
            $table->dropUnique('unq_active_product_config');
        });

        // Don't add any unique constraint at database level
        // We'll handle "one ACTIVE config per product" in application logic
        // This allows:
        // - Multiple INACTIVE configs (historical records)
        // - Application to enforce only one ACTIVE config
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the old unique constraint
        Schema::table('m_product_revenue_split_configs', function (Blueprint $table) {
            $table->unique(['company_code', 'f_product_id', 'status'], 'unq_active_product_config');
        });
    }
};
