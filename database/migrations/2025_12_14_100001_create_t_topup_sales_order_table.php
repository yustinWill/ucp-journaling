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
        Schema::create('t_topup_sales_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_code', 10);
            $table->unsignedBigInteger('topup_id')->comment('Reference to t_topup');
            $table->unsignedBigInteger('sales_order_id')->comment('Reference to t_sales_orders');
            $table->decimal('allocated_amount', 15, 2)->default(0)->comment('Amount yang dialokasikan untuk SO ini');
            $table->decimal('so_total_amount', 15, 2)->default(0)->comment('Total amount SO (snapshot)');
            $table->decimal('so_remaining_before', 15, 2)->default(0)->comment('Remaining SO sebelum alokasi');
            $table->decimal('so_remaining_after', 15, 2)->default(0)->comment('Remaining SO setelah alokasi');
            $table->integer('allocation_sequence')->default(0)->comment('Urutan alokasi FIFO (1, 2, 3, ...)');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('company_code');
            $table->index('topup_id');
            $table->index('sales_order_id');
            $table->index('allocation_sequence');
            $table->index(['topup_id', 'allocation_sequence']);

            // Foreign keys
            $table->foreign('topup_id')->references('id')->on('t_topup')->onDelete('cascade');
            $table->foreign('sales_order_id')->references('id')->on('t_sales_orders')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_topup_sales_order');
    }
};
