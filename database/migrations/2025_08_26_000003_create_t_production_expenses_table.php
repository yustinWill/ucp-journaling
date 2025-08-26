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
        Schema::create('t_production_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id'); // References t_productions
            $table->string('expense_type'); // From product expenses (LABOR, OVERHEAD, MATERIAL, etc.)
            $table->string('expense_name'); // Description of the expense
            $table->decimal('default_amount', 15, 2)->default(0); // Default from product expenses
            $table->decimal('actual_amount', 15, 2); // Editable amount entered by user
            $table->string('unit')->nullable(); // Per unit, per batch, etc.
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('production_id')->references('id')->on('t_productions')->onDelete('cascade');
            
            // Indexes
            $table->index(['production_id', 'expense_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_production_expenses');
    }
};