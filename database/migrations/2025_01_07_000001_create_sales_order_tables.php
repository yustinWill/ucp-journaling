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
        // Main Sales Order Table
        Schema::create('t_sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sales_person_id')->nullable();
            $table->date('order_date');
            $table->string('payment_terms')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, CONFIRMED, DELIVERED, INVOICED, CANCELLED
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('memo')->nullable();
            $table->text('tags')->nullable(); // JSON array of tag IDs
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            // Foreign keys
            $table->foreign('customer_id')->references('id')->on('m_contacts');
            $table->foreign('sales_person_id')->references('id')->on('m_sales_persons');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            // Indexes
            $table->index(['order_date', 'status']);
            $table->index('customer_id');
        });

        // Sales Order Products Table
        Schema::create('t_sales_order_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('quantity_ordered', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('sales_order_id')->references('id')->on('t_sales_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('m_products');
            $table->foreign('tax_id')->references('id')->on('m_taxes');
        });

        // Sales Order Audit Log Table
        Schema::create('t_sales_order_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_sales_order_id');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            // Foreign keys
            $table->foreign('f_sales_order_id')->references('id')->on('t_sales_orders')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sales_order_audit_logs');
        Schema::dropIfExists('t_sales_order_products');
        Schema::dropIfExists('t_sales_orders');
    }
};