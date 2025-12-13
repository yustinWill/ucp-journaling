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
        Schema::create('t_packaging', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('production_id'); // Reference to t_productions (with is_packaging = true)
            $table->unsignedBigInteger('bundle_product_id'); // The main bundle product being packed
            $table->unsignedBigInteger('packaging_product_id'); // The packaging product used
            $table->decimal('quantity_packed', 15, 4); // Quantity of this packaging product produced
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('production_id')->references('id')->on('t_productions')->onDelete('cascade');

            // Indexes
            $table->index(['company_code', 'production_id']);
            $table->index('bundle_product_id');
            $table->index('packaging_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_packaging');
    }
};
