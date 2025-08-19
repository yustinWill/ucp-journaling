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
        Schema::create('t_storage_stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_storage_id');
            $table->unsignedBigInteger('f_product_id');
            $table->date('snapshot_date'); // last day of month
            $table->decimal('snapshot_qty', 15, 4);
            $table->decimal('snapshot_value', 15, 2);
            $table->decimal('avg_hpp', 15, 2);
            $table->integer('movement_count'); // number of movements in the month
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            
            $table->foreign('f_storage_id')->references('id')->on('m_storages');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('created_by')->references('id')->on('users');
            
            $table->unique(['f_storage_id', 'f_product_id', 'snapshot_date'], 'unq_storage_product_snapshot');
            $table->index(['company_code', 'snapshot_date'], 'idx_company_snapshot_date');
            $table->index(['f_storage_id', 'snapshot_date'], 'idx_storage_snapshot_date');
            $table->index(['f_product_id', 'snapshot_date'], 'idx_product_snapshot_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_storage_stock_snapshots');
    }
};