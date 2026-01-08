<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration restructures the budget system to use periods
        // For fresh migrations: Creates new structure
        // For existing databases: Migrates old data to new structure

        // Step 1: Create new m_budget_periods table
        if (! Schema::hasTable('m_budget_periods')) {
            Schema::create('m_budget_periods', function (Blueprint $table) {
                $table->id();
                $table->date('period_start_date');
                $table->date('period_end_date');
                $table->bigInteger('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->bigInteger('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['period_start_date', 'period_end_date']);
            });
        }

        // Step 2 & 3: Only migrate data if old tables exist
        $hasOldBudgets = Schema::hasTable('m_budgeds');
        $hasOldHistory = Schema::hasTable('m_budgeds_history');

        $periodMap = [];

        if ($hasOldHistory) {
            // Get unique periods from m_budgeds_history
            try {
                $periods = DB::table('m_budgeds_history')
                    ->select('period_start_date', 'period_end_date', 'created_by', 'created_at')
                    ->distinct()
                    ->get();

                // Insert unique periods into m_budget_periods
                foreach ($periods as $period) {
                    $periodId = DB::table('m_budget_periods')->insertGetId([
                        'period_start_date' => $period->period_start_date,
                        'period_end_date' => $period->period_end_date,
                        'created_by' => $period->created_by,
                        'created_at' => $period->created_at,
                    ]);

                    $key = $period->period_start_date.'|'.$period->period_end_date;
                    $periodMap[$key] = $periodId;
                }
            } catch (\Exception $e) {
                // If migration fails, continue with creating new structure
            }
        }

        // Rename old m_budgeds to m_budgeds_old for backup (only if it exists and not already renamed)
        if ($hasOldBudgets && ! Schema::hasTable('m_budgeds_old')) {
            Schema::rename('m_budgeds', 'm_budgeds_old');
        }

        // Step 4: Create new m_budgeds table with period reference (if it doesn't exist)
        if (! Schema::hasTable('m_budgeds')) {
            Schema::create('m_budgeds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('f_budget_period_id');
                $table->string('budged_name');
                $table->decimal('budged_limit_value', 20, 2)->default(0);
                $table->decimal('budged_remaining_value', 20, 2)->default(0);
                $table->bigInteger('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->bigInteger('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('f_budget_period_id')->references('id')->on('m_budget_periods')->onDelete('cascade');
                $table->index('f_budget_period_id');
            });
        }

        // Step 5: Migrate data from old structure to new structure (only if old data exists)
        if ($hasOldHistory && Schema::hasTable('m_budgeds_old') && count($periodMap) > 0) {
            try {
                $histories = DB::table('m_budgeds_history')->get();

                foreach ($histories as $history) {
                    $key = $history->period_start_date.'|'.$history->period_end_date;
                    $periodId = $periodMap[$key] ?? null;

                    if ($periodId) {
                        $oldBudget = DB::table('m_budgeds_old')->where('id', $history->budged_id)->first();

                        if ($oldBudget) {
                            DB::table('m_budgeds')->insert([
                                'f_budget_period_id' => $periodId,
                                'budged_name' => $oldBudget->budged_name,
                                'budged_limit_value' => $history->budged_limit_value,
                                'budged_remaining_value' => $history->budged_remaining_value ?? $history->budged_limit_value,
                                'created_by' => $history->created_by,
                                'created_at' => $history->created_at,
                                'updated_by' => $history->updated_by ?? null,
                                'updated_at' => $history->updated_at ?? null,
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // If migration fails, tables are created but without old data
            }
        }

        // Step 6: Rename m_budgeds_history to m_budgeds_history_old for backup (only if it exists)
        if ($hasOldHistory && ! Schema::hasTable('m_budgeds_history_old')) {
            Schema::rename('m_budgeds_history', 'm_budgeds_history_old');
        }

        // Note: We keep m_budgeds_old and m_budgeds_history_old for backup
        // You can drop them manually after verifying the migration was successful
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old structure
        Schema::dropIfExists('m_budgeds');
        Schema::rename('m_budgeds_old', 'm_budgeds');
        Schema::rename('m_budgeds_history_old', 'm_budgeds_history');
        Schema::dropIfExists('m_budget_periods');
    }
};
