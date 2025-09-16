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
        Schema::table('t_return_managements', function (Blueprint $table) {
            $table->enum('stock_adjustment_status', ['NOT_ADJUSTED', 'ADJUSTED'])->default('NOT_ADJUSTED')->after('processing_status');
            $table->unsignedBigInteger('f_stock_adjustment_id')->nullable()->after('stock_adjustment_status');
            
            // Add foreign key to stock adjustments table
            $table->foreign('f_stock_adjustment_id')->references('id')->on('t_stock_adjustments')->onDelete('set null');
            
            // Add index for stock adjustment status
            $table->index(['company_code', 'stock_adjustment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_return_managements', function (Blueprint $table) {
            $table->dropForeign(['f_stock_adjustment_id']);
            $table->dropIndex(['company_code', 'stock_adjustment_status']);
            $table->dropColumn(['stock_adjustment_status', 'f_stock_adjustment_id']);
        });
    }
};