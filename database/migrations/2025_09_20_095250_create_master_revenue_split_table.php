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
        Schema::create('m_revenue_splits', function (Blueprint $table) {
            $table->id();
            $table->string('cycle_name')->unique();
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('target_pabrik', 15, 2)->default(0);
            $table->decimal('target_owner', 15, 2)->default(0);
            $table->decimal('target_perusahaan', 15, 2)->default(0);
            $table->decimal('total_target', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['DRAFT', 'ACTIVE', 'SELESAI'])->default('DRAFT');
            $table->string('company_code')->default('001');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_revenue_splits');
    }
};
