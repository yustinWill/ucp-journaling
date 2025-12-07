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
        Schema::create('m_budget_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->bigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index('status');
        });

        // Add foreign key to m_budgeds table
        Schema::table('m_budgeds', function (Blueprint $table) {
            $table->unsignedBigInteger('f_budget_category_id')->nullable()->after('f_budget_period_id');
            $table->foreign('f_budget_category_id')->references('id')->on('m_budget_categories')->onDelete('set null');
            $table->index('f_budget_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_budgeds', function (Blueprint $table) {
            $table->dropForeign(['f_budget_category_id']);
            $table->dropColumn('f_budget_category_id');
        });

        Schema::dropIfExists('m_budget_categories');
    }
};
