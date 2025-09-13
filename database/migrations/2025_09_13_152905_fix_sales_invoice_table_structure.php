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
        // Drop the existing table and recreate with correct structure
        Schema::dropIfExists('t_sales_invoice_audit_logs');
        Schema::dropIfExists('t_sales_invoice_payments');
        Schema::dropIfExists('t_sales_invoice_items');
        Schema::dropIfExists('t_sales_invoices');

        // Recreate with correct structure matching the service expectations
        Schema::create('t_sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('transaction_code')->unique();
            $table->unsignedBigInteger('f_sales_order_id');
            $table->date('invoice_date');
            $table->date('transaction_date');
            $table->date('due_date');
            $table->string('payment_terms')->nullable();
            $table->text('description')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->text('internal_memo')->nullable();
            $table->string('attachment')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('status')->default('UNPAID'); // UNPAID, PAID, PARTIAL_PAID, OVERDUE, CANCELLED
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_sales_order_id')->references('id')->on('t_sales_orders');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Indexes
            $table->index(['company_code', 'invoice_date']);
            $table->index(['company_code', 'status']);
            $table->index('due_date');
        });

        // Sales Invoice Products Table
        Schema::create('t_sales_invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_sales_invoice_id');
            $table->unsignedBigInteger('f_sales_order_product_id');
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('invoiced_qty_before', 10, 2)->default(0);
            $table->decimal('remaining_qty_before', 10, 2)->default(0);
            $table->decimal('qty', 10, 2);
            $table->decimal('invoiced_qty_after', 10, 2)->default(0);
            $table->decimal('remaining_qty_after', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('line_total', 15, 2);
            $table->text('note')->nullable();
            $table->string('status')->default('INVOICED');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();

            // Foreign keys
            $table->foreign('f_sales_invoice_id')->references('id')->on('t_sales_invoices')->onDelete('cascade');
            $table->foreign('f_sales_order_product_id')->references('id')->on('t_sales_order_products');
            $table->foreign('f_product_id')->references('id')->on('m_products');
            $table->foreign('created_by')->references('id')->on('users');
        });

        // Sales Invoice Audit Log Table
        Schema::create('t_sales_invoice_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_sales_invoice_id');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_sales_invoice_id')->references('id')->on('t_sales_invoices')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sales_invoice_audit_logs');
        Schema::dropIfExists('t_sales_invoice_products');
        Schema::dropIfExists('t_sales_invoices');
    }
};