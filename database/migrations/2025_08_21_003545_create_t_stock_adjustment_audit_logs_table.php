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
        Schema::create('t_stock_adjustment_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('stock_adjustment_id');
            $table->enum('action_type', ['CREATE', 'UPDATE', 'DELETE', 'APPROVE', 'CANCEL', 'COMPLETE']);
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            
            $table->index(['company_code', 'stock_adjustment_id'], 'idx_audit_company_adjustment');
            $table->index('action_type', 'idx_audit_action_type');
            $table->index('created_at', 'idx_audit_created_at');
            
            $table->foreign('stock_adjustment_id')->references('id')->on('t_stock_adjustments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stock_adjustment_audit_logs');
    }
};
