<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_expense_coa_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_expense_id');
            $table->string('f_coa_code');
            $table->unsignedBigInteger('f_tax_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_expense_id')->references('id')->on('t_expenses');
            $table->foreign('f_coa_code')->references('code')->on('m_coas');
            $table->foreign('f_tax_id')->references('id')->on('m_taxes');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_expense_coa_details');
    }
};
