<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_sales_order_revert_logs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 50);
            $table->unsignedBigInteger('sales_order_id');
            $table->string('transaction_code', 100);
            $table->string('customer_name')->nullable();
            $table->decimal('grand_price', 20, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('actor_name')->nullable();
            $table->string('actor_email')->nullable();
            $table->json('order_snapshot');
            $table->json('summary');
            $table->timestamp('created_at')->useCurrent();

            // No Sales Order foreign key: the audit record must outlive the deleted SO.
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['company_code', 'created_at'], 'so_revert_log_company_date_idx');
            $table->index(['company_code', 'transaction_code'], 'so_revert_log_company_code_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_sales_order_revert_logs');
    }
};
