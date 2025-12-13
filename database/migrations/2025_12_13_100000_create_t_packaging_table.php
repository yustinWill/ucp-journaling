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
            $table->bigIncrements('id');
            $table->string('company_code', 10);
            $table->unsignedBigInteger('production_id')->comment('Reference to t_productions with is_packaging=true');
            $table->unsignedBigInteger('bundle_product_id')->comment('The bundle product being unpacked/consumed');
            $table->unsignedBigInteger('packaging_product_id')->comment('The packaging product being produced');
            $table->decimal('quantity_packed', 15, 4)->comment('Quantity of packaging product produced');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Indexes for better query performance
            $table->index('company_code', 'idx_packaging_company_code');
            $table->index('production_id', 'idx_packaging_production_id');
            $table->index('bundle_product_id', 'idx_packaging_bundle_product_id');
            $table->index('packaging_product_id', 'idx_packaging_packaging_product_id');

            // Foreign keys
            $table->foreign('production_id', 'fk_packaging_production_id')
                ->references('id')
                ->on('t_productions')
                ->onDelete('cascade');

            $table->foreign('bundle_product_id', 'fk_packaging_bundle_product_id')
                ->references('id')
                ->on('m_products')
                ->onDelete('restrict');

            $table->foreign('packaging_product_id', 'fk_packaging_packaging_product_id')
                ->references('id')
                ->on('m_products')
                ->onDelete('restrict');
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
