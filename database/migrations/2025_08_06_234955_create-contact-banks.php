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
        Schema::create('m_contact_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_contact_id');
            $table->string('f_bank_code')->nullable();
            $table->string('branch_office')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('f_contact_id')->references('id')->on('m_contacts');
            $table->foreign('f_bank_code')->references('code')->on('m_banks');
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
