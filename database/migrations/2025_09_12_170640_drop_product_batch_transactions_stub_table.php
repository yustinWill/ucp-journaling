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
        // Drop the stub product_batch_transactions table (without t_ prefix)
        // We use t_product_batch_transactions (with t_ prefix) for actual batch tracking
        Schema::dropIfExists('product_batch_transactions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the stub table if needed for rollback
        Schema::create('product_batch_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
