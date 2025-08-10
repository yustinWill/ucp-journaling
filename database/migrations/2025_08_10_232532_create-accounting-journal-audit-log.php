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
        Schema::create('m_accounting_journal_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_accounting_journal_id');
            $table->string('company_code');
            $table->string('action_type');
            $table->string('description');
            $table->json('metadata');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('roles');
            $table->foreign('updated_by')->references('id')->on('roles');
            $table->foreign('f_accounting_journal_id')->references('id')->on('t_accounting_journals');
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
