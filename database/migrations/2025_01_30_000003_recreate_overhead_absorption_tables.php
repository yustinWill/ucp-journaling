<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables if they exist
        Schema::dropIfExists('t_overhead_absorption_expenses');
        Schema::dropIfExists('t_overhead_absorption_details');
        Schema::dropIfExists('t_overhead_absorption_productions');
        Schema::dropIfExists('t_overhead_absorptions');

        // Create new tables with proper structure
        Schema::create('t_overhead_absorptions', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 50);
            $table->string('transaction_no', 100)->unique();
            $table->string('period', 7); // Format: YYYY-MM
            $table->date('settlement_date')->nullable();
            $table->decimal('total_production_qty', 15, 2)->default(0);
            $table->decimal('total_overhead_cost', 15, 2)->default(0);
            $table->decimal('overhead_per_unit', 15, 2)->default(0);
            $table->boolean('is_settled')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->index(['company_code', 'period']);
            $table->index('is_settled');
        });

        Schema::create('t_overhead_absorption_productions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_overhead_absorption_id');
            $table->unsignedBigInteger('f_production_id');
            $table->decimal('production_qty', 15, 2);
            $table->decimal('direct_cost', 15, 2);
            $table->decimal('overhead_allocated', 15, 2);
            $table->decimal('hpp_per_unit', 15, 2);
            $table->timestamps();

            $table->foreign('f_overhead_absorption_id', 'fk_oa_prod_absorption')
                  ->references('id')->on('t_overhead_absorptions')->onDelete('cascade');
            $table->foreign('f_production_id')->references('id')->on('t_productions');
        });

        Schema::create('t_overhead_absorption_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_overhead_absorption_id');
            $table->unsignedBigInteger('f_expense_id');
            $table->decimal('expense_amount', 15, 2);
            $table->timestamps();

            $table->foreign('f_overhead_absorption_id', 'fk_oa_exp_absorption')
                  ->references('id')->on('t_overhead_absorptions')->onDelete('cascade');
            $table->foreign('f_expense_id')->references('id')->on('t_expenses');
        });

        // Add fields to production table if they don't exist
        if (!Schema::hasColumn('t_productions', 'is_overhead_absorbed')) {
            Schema::table('t_productions', function (Blueprint $table) {
                $table->boolean('is_overhead_absorbed')->default(false)->after('status');
                $table->unsignedBigInteger('f_overhead_absorption_id')->nullable()->after('is_overhead_absorbed');
                $table->decimal('allocated_overhead', 15, 2)->default(0)->after('f_overhead_absorption_id');
                $table->decimal('hpp_with_overhead', 15, 2)->default(0)->after('allocated_overhead');

                $table->foreign('f_overhead_absorption_id')->references('id')->on('t_overhead_absorptions');
            });
        }

        // Add fields to expense table if they don't exist
        if (!Schema::hasColumn('t_expenses', 'is_overhead_absorbed')) {
            Schema::table('t_expenses', function (Blueprint $table) {
                $table->boolean('is_overhead_absorbed')->default(false)->after('category_type');
                $table->unsignedBigInteger('f_overhead_absorption_id')->nullable()->after('is_overhead_absorbed');

                $table->foreign('f_overhead_absorption_id')->references('id')->on('t_overhead_absorptions');
            });
        }
    }

    public function down(): void
    {
        // Remove fields from expense table
        if (Schema::hasColumn('t_expenses', 'f_overhead_absorption_id')) {
            Schema::table('t_expenses', function (Blueprint $table) {
                $table->dropForeign(['f_overhead_absorption_id']);
                $table->dropColumn(['is_overhead_absorbed', 'f_overhead_absorption_id']);
            });
        }

        // Remove fields from production table
        if (Schema::hasColumn('t_productions', 'f_overhead_absorption_id')) {
            Schema::table('t_productions', function (Blueprint $table) {
                $table->dropForeign(['f_overhead_absorption_id']);
                $table->dropColumn(['is_overhead_absorbed', 'f_overhead_absorption_id', 'allocated_overhead', 'hpp_with_overhead']);
            });
        }

        Schema::dropIfExists('t_overhead_absorption_expenses');
        Schema::dropIfExists('t_overhead_absorption_productions');
        Schema::dropIfExists('t_overhead_absorptions');
    }
};
