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
        Schema::create('m_tax_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_tax_id');
            $table->string('action_type'); // CREATE, UPDATE, DELETE
            $table->text('description');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();

            // Foreign key constraints
            $table->foreign('f_tax_id')->references('id')->on('m_taxes')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['f_tax_id']);
            $table->index(['action_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_tax_audit_logs');
    }
};