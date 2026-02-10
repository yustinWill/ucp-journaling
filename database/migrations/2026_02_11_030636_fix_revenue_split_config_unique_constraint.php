<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the constraint exists before trying to drop it
        $constraintExists = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.statistics
            WHERE table_schema = DATABASE()
            AND table_name = 'm_product_revenue_split_configs'
            AND index_name = 'unq_active_product_config'
        ");

        if ($constraintExists[0]->count > 0) {
            Schema::table('m_product_revenue_split_configs', function (Blueprint $table) {
                // Drop the old unique constraint that incorrectly includes status
                // The old constraint prevented multiple INACTIVE records
                $table->dropUnique('unq_active_product_config');
            });
        }

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
