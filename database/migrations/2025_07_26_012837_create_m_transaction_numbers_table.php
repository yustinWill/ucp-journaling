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
        Schema::create('m_transaction_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->unique()->comment('Module identifier (expense, purchase_order, sales, etc.)');
            $table->string('module_display_name')->comment('Human readable module name');
            $table->string('module_initial', 2)->comment('2-letter module code (EX, PO, SO, etc.)');
            $table->text('format_template')->comment('Flexible interpolation template (e.g., RIYO-{YEAR}-{COUNTER})');
            $table->unsignedBigInteger('current_counter')->default(1)->comment('Current counter value (user configurable)');
            $table->tinyInteger('counter_padding')->default(5)->comment('Zero-padding for counter (e.g., 5 = 00001)');
            $table->enum('reset_frequency', ['never', 'yearly', 'monthly', 'daily'])->default('never')->comment('When to reset counter');
            $table->date('last_reset_date')->nullable()->comment('Last time counter was reset');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            // Add indexes for performance
            $table->index(['module_name', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_transaction_numbers');
    }
};