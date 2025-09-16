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
        Schema::create('t_return_management_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_return_management_id');
            $table->unsignedBigInteger('f_source_invoice_product_id'); // PI or SI product ID
            $table->unsignedBigInteger('f_product_id');
            $table->decimal('original_qty', 10, 3); // Original invoiced quantity
            $table->decimal('returned_qty_before', 10, 3)->default(0); // Previously returned qty
            $table->decimal('available_qty', 10, 3); // Available quantity to return
            $table->decimal('return_qty', 10, 3); // Current return quantity
            $table->decimal('returned_qty_after', 10, 3)->default(0); // Total returned after this return
            $table->decimal('unit_price', 15, 2); // Original unit price from invoice
            $table->decimal('return_unit_price', 15, 2); // Return price (can be different)
            $table->decimal('total_return_price', 15, 2); // Total return value
            $table->enum('return_condition', ['NEW', 'USED_GOOD', 'USED_FAIR', 'DAMAGED', 'DEFECTIVE'])->default('NEW');
            $table->text('return_reason')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('f_return_management_id')->references('id')->on('t_return_managements')->onDelete('cascade');
            $table->foreign('f_product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('f_return_management_id');
            $table->index('f_source_invoice_product_id');
            $table->index('f_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_return_management_products');
    }
};