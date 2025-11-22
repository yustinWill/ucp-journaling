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
        Schema::create('t_expense_repayments', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_expense_id');
            $table->string('transaction_no', 255)->unique();
            $table->date('transaction_date');
            $table->unsignedBigInteger('f_receiver_customer_id');
            $table->string('f_payment_coa_code', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('memo')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->decimal('remaining_amount', 15, 2);
            $table->decimal('payment_amount', 15, 2);
            $table->decimal('latest_remaining_amount', 15, 2);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_expense_id')->references('id')->on('t_expenses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_repayment');
    }
};
