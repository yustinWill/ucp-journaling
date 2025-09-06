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
        // Create t_purchase_invoices table
        Schema::create('t_purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->string('transaction_code', 50)->unique();
            $table->unsignedBigInteger('f_purchase_order_id');
            $table->date('invoice_date');
            $table->date('transaction_date');
            $table->date('due_date');
            $table->string('payment_terms', 50)->nullable();
            $table->text('description')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->text('internal_memo')->nullable();
            $table->string('attachment')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('status', ['DRAFT', 'UNPAID', 'PAID', 'PARTIAL_PAID', 'OVERDUE', 'CANCELLED'])->default('UNPAID');
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('f_purchase_order_id')->references('id')->on('t_purchase_orders')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['company_code', 'status']);
            $table->index(['company_code', 'invoice_date']);
            $table->index(['company_code', 'due_date']);
            $table->index('f_purchase_order_id');
        });

        // Create t_purchase_invoice_products table
        Schema::create('t_purchase_invoice_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_purchase_invoice_id');
            $table->unsignedBigInteger('f_product_order_product_id');
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('invoiced_qty_before', 10, 3)->default(0);
            $table->decimal('remaining_qty_before', 10, 3)->default(0);
            $table->decimal('qty', 10, 3);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->decimal('invoiced_qty_after', 10, 3)->default(0);
            $table->decimal('remaining_qty_after', 10, 3)->default(0);
            $table->text('note')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('f_purchase_invoice_id')->references('id')->on('t_purchase_invoices')->onDelete('cascade');
            $table->foreign('f_product_order_product_id')->references('id')->on('t_purchase_order_products')->onDelete('cascade');
            $table->foreign('f_product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('f_purchase_invoice_id');
            $table->index('f_product_order_product_id');
            $table->index('f_product_id');
        });

        // Create t_purchase_invoice_audit_logs table
        Schema::create('t_purchase_invoice_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('f_purchase_invoice_id');
            $table->enum('action_type', ['CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'REJECT', 'CANCEL']);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at');

            // Foreign key constraints
            $table->foreign('f_purchase_invoice_id')->references('id')->on('t_purchase_invoices')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['company_code', 'f_purchase_invoice_id'], 'pi_audit_company_invoice_idx');
            $table->index('f_purchase_invoice_id', 'pi_audit_invoice_idx');
            $table->index('user_id', 'pi_audit_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_purchase_invoice_audit_logs');
        Schema::dropIfExists('t_purchase_invoice_products');
        Schema::dropIfExists('t_purchase_invoices');
    }
};
