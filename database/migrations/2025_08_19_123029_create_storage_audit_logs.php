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
        Schema::create('m_storage_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_storage_id');
            $table->string('action_type');
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            $table->unsignedBigInteger('created_by');
            
            $table->foreign('f_storage_id')->references('id')->on('m_storages');
            $table->foreign('created_by')->references('id')->on('users');
            
            $table->index(['f_storage_id', 'created_at']);
            $table->index('company_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_storage_audit_logs');
    }
};