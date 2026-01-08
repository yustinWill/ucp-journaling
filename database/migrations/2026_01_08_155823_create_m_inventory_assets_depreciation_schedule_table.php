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
        Schema::create('m_inventory_assets_depreciation_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id')->comment('Reference to m_inventory_assets.id');
            $table->date('asset_depreciation_date')->comment('Date when depreciation should occur');
            $table->decimal('asset_depreciation_value', 15, 2)->comment('Depreciation value for this period');
            $table->boolean('asset_is_depreciation')->nullable()->comment('Flag indicating if depreciation has been processed');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('asset_id')->references('id')->on('m_inventory_assets')->onDelete('cascade');

            // Indexes for better query performance
            $table->index('asset_id', 'idx_asset_id');
            $table->index('asset_depreciation_date', 'idx_depreciation_date');
            $table->index(['asset_id', 'asset_depreciation_date'], 'idx_asset_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_inventory_assets_depreciation_schedule');
    }
};
