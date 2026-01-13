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
        Schema::create('m_product_units', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_product_id')->comment('Reference to m_products.id');
            $table->unsignedBigInteger('f_unit_id')->comment('Reference to m_units.id');
            $table->boolean('is_default')->default(false)->comment('Flag to mark default unit for the product');
            $table->decimal('conversion_factor', 15, 4)->default(1)->comment('Conversion factor to base unit (default unit). e.g., 1 box = 10 pcs, factor = 10');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('f_unit_id')->references('id')->on('m_units')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['company_code', 'f_product_id'], 'idx_product_units_lookup');
            $table->index('status', 'idx_product_units_status');
            $table->index('is_default', 'idx_product_units_default');

            // Unique constraint - one product can only have one unit type once
            $table->unique(['company_code', 'f_product_id', 'f_unit_id'], 'unique_product_unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_product_units');
    }
};
