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
        Schema::table('t_sales_order_revenue_splits', function (Blueprint $table) {
            // Add payment status fields for each entity
            $table->boolean('is_paid_perusahaan')->default(false)->after('split_notes')
                ->comment('Payment status for Kingdom (Perusahaan) portion');
            $table->boolean('is_paid_owner')->default(false)->after('is_paid_perusahaan')
                ->comment('Payment status for Owner portion');
            $table->boolean('is_paid_pabrik')->default(false)->after('is_paid_owner')
                ->comment('Payment status for Factory (Pabrik) portion');

            // Add indexes for filtering (with custom short names to avoid MySQL 64-char limit)
            $table->index(['is_paid_perusahaan', 'company_code'], 'idx_rev_split_paid_perusahaan');
            $table->index(['is_paid_owner', 'company_code'], 'idx_rev_split_paid_owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_revenue_splits', function (Blueprint $table) {
            $table->dropIndex('idx_rev_split_paid_perusahaan');
            $table->dropIndex('idx_rev_split_paid_owner');

            $table->dropColumn([
                'is_paid_perusahaan',
                'is_paid_owner',
                'is_paid_pabrik',
            ]);
        });
    }
};
