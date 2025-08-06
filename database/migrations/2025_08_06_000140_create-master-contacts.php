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
        Schema::create('m_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('type');
            $table->string('name');
            $table->string('group_contact_name')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->string('f_identity_type_code')->nullable();
            $table->string('identity_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('f_sales_pic_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_npwp')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_fax_no')->nullable();
            $table->string('f_coa_account_payable')->nullable();
            $table->string('f_coa_account_receivable')->nullable();
            $table->string('f_payment_terms_id')->nullable();
            $table->json('f_tag_codes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
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
