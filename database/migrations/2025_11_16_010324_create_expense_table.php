<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('type', 255);
            $table->string('transaction_no', 255)->unique();
            $table->date('transaction_date');
            $table->boolean('to_be_paid_later');
            $table->unsignedBigInteger('f_payment_term_id')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('f_receiver_customer_id');
            $table->text('billing_address')->nullable();
            $table->text('description')->nullable();
            $table->text('memo')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total_tax', 15, 2);
            $table->decimal('total_before_discount', 15, 2);
            $table->decimal('total_discount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_payment_term_id')->references('id')->on('m_payment_terms');
            $table->foreign('f_receiver_customer_id')->references('id')->on('m_contacts');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_expenses');
    }
};
