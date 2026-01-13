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
        Schema::create('m_product_composition_cost', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_product_id')->comment('Reference to m_products.id');
            $table->string('account_code')->comment('COA account code');
            $table->decimal('cost_value', 15, 2)->comment('Cost value for this composition');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_product_id')->references('id')->on('m_products')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('f_product_id', 'idx_product_composition_cost_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_product_composition_cost');
    }
};
