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
        Schema::create('m_unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_from_unit_id')->comment('Source unit (e.g., Box)');
            $table->unsignedBigInteger('f_to_unit_id')->comment('Target unit (e.g., KG)');
            $table->decimal('conversion_factor', 15, 4)->comment('Conversion multiplier (e.g., 1 box = 10 kg, factor = 10)');
            $table->string('conversion_formula')->nullable()->comment('Optional formula description');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('f_from_unit_id')->references('id')->on('m_units')->onDelete('cascade');
            $table->foreign('f_to_unit_id')->references('id')->on('m_units')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['company_code', 'f_from_unit_id', 'f_to_unit_id'], 'idx_unit_conv_lookup');
            $table->index('status', 'idx_unit_conv_status');

            // Unique constraint to prevent duplicate conversions
            $table->unique(['company_code', 'f_from_unit_id', 'f_to_unit_id'], 'unique_unit_conversion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_unit_conversions');
    }
};
