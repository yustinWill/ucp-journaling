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
        Schema::create('t_return_managements', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->string('transaction_code', 50)->unique();
            $table->enum('return_type', ['PURCHASE_RETURN', 'SALES_RETURN']);
            $table->unsignedBigInteger('f_source_invoice_id'); // PI or SI ID
            $table->string('source_invoice_code', 50); // PI or SI code for reference
            $table->date('return_date');
            $table->date('transaction_date');
            $table->enum('return_reason', ['DEFECTIVE', 'WRONG_ITEM', 'DAMAGED', 'EXPIRED', 'OVERSTOCKED', 'CUSTOMER_REQUEST', 'OTHER'])->default('OTHER');
            $table->text('return_reason_description')->nullable();
            $table->text('description')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->text('internal_memo')->nullable();
            $table->string('attachment')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->enum('status', ['DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'PROCESSED', 'REJECTED', 'CANCELLED'])->default('DRAFT');
            $table->enum('processing_status', ['NOT_PROCESSED', 'PARTIAL', 'COMPLETED'])->default('NOT_PROCESSED');
            $table->text('approval_notes')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign key constraints
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['company_code', 'status']);
            $table->index(['company_code', 'return_date']);
            $table->index(['company_code', 'return_type']);
            $table->index('f_source_invoice_id');
            $table->index('source_invoice_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_return_managements');
    }
};