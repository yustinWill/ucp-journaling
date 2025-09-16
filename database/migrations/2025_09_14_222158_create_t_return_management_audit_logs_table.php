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
        Schema::create('t_return_management_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->unsignedBigInteger('f_return_management_id');
            $table->enum('action_type', ['CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'REJECT', 'CANCEL', 'PROCESS', 'COMPLETE']);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at');

            // Foreign key constraints
            $table->foreign('f_return_management_id')->references('id')->on('t_return_managements')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['company_code', 'f_return_management_id'], 'rm_audit_company_return_idx');
            $table->index('f_return_management_id', 'rm_audit_return_idx');
            $table->index('user_id', 'rm_audit_user_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_return_management_audit_logs');
    }
};