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

            // Add indexes for filtering
            $table->index(['is_paid_perusahaan', 'company_code']);
            $table->index(['is_paid_owner', 'company_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_sales_order_revenue_splits', function (Blueprint $table) {
            $table->dropIndex(['is_paid_perusahaan', 'company_code']);
            $table->dropIndex(['is_paid_owner', 'company_code']);

            $table->dropColumn([
                'is_paid_perusahaan',
                'is_paid_owner',
                'is_paid_pabrik',
            ]);
        });
    }
};
