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
        Schema::create('t_production_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id'); // References t_productions
            $table->unsignedBigInteger('user_id'); // References users
            $table->enum('action_type', [
                'CREATE',
                'UPDATE',
                'DELETE',
                'COMPLETE',
                'CANCEL',
                'REVERSAL',
                'STATUS_CHANGE',
            ]);
            $table->string('description');
            $table->json('metadata')->nullable(); // Before/after data, stock movements, batch info
            $table->timestamp('created_at');

            // Foreign key constraints
            $table->foreign('production_id')->references('id')->on('t_productions')->onDelete('cascade');

            // Indexes
            $table->index(['production_id', 'created_at']);
            $table->index(['user_id', 'action_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_production_audit_logs');
    }
};
