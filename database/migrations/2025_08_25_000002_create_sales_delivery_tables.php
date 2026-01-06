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
        // Main Sales Delivery Table
        Schema::create('t_sales_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_number')->unique();
            $table->unsignedBigInteger('sales_order_id')->nullable(); // Can be null for direct delivery
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->date('delivery_date');
            $table->string('status')->default('PENDING'); // PENDING, IN_TRANSIT, DELIVERED, CANCELLED, RETURNED
            $table->text('delivery_address')->nullable();
            $table->string('customer_reference')->nullable();
            $table->text('delivery_notes')->nullable(); // Customer-facing notes
            $table->text('memo')->nullable(); // Internal memo
            $table->text('tags')->nullable(); // JSON array of tag IDs
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('sales_order_id')->references('id')->on('t_sales_orders');
            $table->foreign('customer_id')->references('id')->on('m_contacts');
            $table->foreign('warehouse_id')->references('id')->on('m_warehouses');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Indexes
            $table->index(['delivery_date', 'status']);
            $table->index(['customer_id', 'warehouse_id']);
        });

        // Sales Delivery Products Table
        Schema::create('t_sales_delivery_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_delivery_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sales_order_product_id')->nullable(); // Link to original order line
            $table->decimal('quantity_ordered', 10, 2)->default(0);
            $table->decimal('quantity_delivered', 10, 2);
            $table->string('delivery_status')->default('PENDING'); // PENDING, DELIVERED, PARTIAL, CANCELLED
            $table->text('description')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('sales_delivery_id')->references('id')->on('t_sales_deliveries')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('m_products');
            $table->foreign('sales_order_product_id')->references('id')->on('t_sales_order_products');
        });

        // Sales Delivery Audit Log Table
        Schema::create('t_sales_delivery_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_sales_delivery_id');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_sales_delivery_id')->references('id')->on('t_sales_deliveries')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sales_delivery_audit_logs');
        Schema::dropIfExists('t_sales_delivery_products');
        Schema::dropIfExists('t_sales_deliveries');
    }
};
