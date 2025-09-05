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
        Schema::create('l_coa_summary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('f_accounting_closing_id');
            $table->date('date');
            $table->string('company_code')->nullable();
            $table->string('f_coa_code');
            $table->double('opening_balance');
            $table->double('debit_balance');
            $table->double('credit_balance');
            $table->double('closing_balance');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('is_active')->nullable();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('modified_by')->references('id')->on('users');
            $table->foreign('f_accounting_closing_id')->references('id')->on('l_accounting_closing');
            $table->foreign('f_coa_code')->references('code')->on('m_coas');
            $table->foreign('company_code')->references('code')->on('m_companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('l_accounting_closing');
    }
};
