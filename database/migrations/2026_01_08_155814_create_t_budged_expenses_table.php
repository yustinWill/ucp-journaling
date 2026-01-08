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
        Schema::create('t_budged_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budged_history_id')->comment('Reference to m_budgeds.id');
            $table->unsignedBigInteger('expense_id')->comment('Reference to t_expenses.id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('budged_history_id')->references('id')->on('m_budgeds')->onDelete('cascade');
            $table->foreign('expense_id')->references('id')->on('t_expenses')->onDelete('cascade');

            // Indexes for better query performance
            $table->index('budged_history_id');
            $table->index('expense_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_budged_expenses');
    }
};
