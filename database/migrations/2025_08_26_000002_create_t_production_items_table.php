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
        Schema::create('t_production_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id'); // References t_productions
            $table->unsignedBigInteger('raw_material_product_id'); // References m_products
            $table->decimal('quantity_required', 15, 4); // From product formula
            $table->decimal('quantity_consumed', 15, 4)->default(0); // Actual consumption
            $table->string('unit_code'); // References m_units
            $table->decimal('unit_cost', 15, 2)->default(0); // Average cost per unit
            $table->decimal('total_cost', 15, 2)->default(0); // quantity_consumed * unit_cost
            $table->json('batch_consumption')->nullable(); // Track which batches were consumed for FIFO reversal
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('production_id')->references('id')->on('t_productions')->onDelete('cascade');
            
            // Indexes
            $table->index(['production_id', 'raw_material_product_id']);
            $table->index('raw_material_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_production_items');
    }
};