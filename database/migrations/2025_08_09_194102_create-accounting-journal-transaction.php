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
        Schema::create('t_accounting_journals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('f_transaction_code')->nullable();
            $table->string('reference_no');
            $table->string('f_company_code')->nullable();
            $table->date('date');
            $table->string('f_accounting_feature_code')->nullable();
            $table->string('f_accounting_transaction_code')->nullable();
            $table->string('f_coa_code');
            $table->string('description');
            $table->double('amount');
            $table->string('debit_credit');
            $table->json('f_tag_codes')->nullable();
            $table->string('status');
            $table->boolean('is_locked')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();

            $table->index('reference_no');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('modified_by')->references('id')->on('users');
            $table->foreign('f_accounting_feature_code')->references('code')->on('m_accounting_features');
            $table->foreign('f_coa_code')->references('code')->on('m_coas');
            $table->foreign('f_company_code')->references('code')->on('m_companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
