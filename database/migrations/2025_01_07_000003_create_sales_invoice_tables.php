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
        // Main Sales Invoice Table
        Schema::create('t_sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('sales_delivery_id')->nullable(); // Can be null for direct invoice
            $table->unsignedBigInteger('customer_id');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('payment_terms')->nullable();
            $table->string('status')->default('DRAFT'); // DRAFT, PENDING, PARTIALLY_PAID, PAID, OVERDUE, CANCELLED, REFUNDED
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->text('terms_conditions')->nullable(); // Invoice terms
            $table->text('memo')->nullable(); // Internal memo
            $table->text('tags')->nullable(); // JSON array of tag IDs
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('sales_delivery_id')->references('id')->on('t_sales_deliveries');
            $table->foreign('customer_id')->references('id')->on('m_contacts');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Indexes
            $table->index(['invoice_date', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index('due_date');
        });

        // Sales Invoice Line Items Table
        Schema::create('t_sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sales_delivery_product_id')->nullable(); // Link to delivery line
            $table->text('description');
            $table->decimal('quantity_invoiced', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('sales_invoice_id')->references('id')->on('t_sales_invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('m_products');
            $table->foreign('sales_delivery_product_id')->references('id')->on('t_sales_delivery_products');
            $table->foreign('tax_id')->references('id')->on('m_taxes');
        });

        // Sales Invoice Payments Table
        Schema::create('t_sales_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id');
            $table->date('payment_date');
            $table->string('payment_method'); // CASH, BANK_TRANSFER, CREDIT_CARD, CHECK, TRADE_CREDIT
            $table->string('payment_reference')->nullable(); // Check number, transfer reference, etc.
            $table->decimal('payment_amount', 15, 2);
            $table->text('payment_notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();

            // Foreign keys
            $table->foreign('sales_invoice_id')->references('id')->on('t_sales_invoices')->onDelete('cascade');
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
        Schema::dropIfExists('t_sales_invoice_payments');
        Schema::dropIfExists('t_sales_invoice_items');
        Schema::dropIfExists('t_sales_invoices');
    }
};
