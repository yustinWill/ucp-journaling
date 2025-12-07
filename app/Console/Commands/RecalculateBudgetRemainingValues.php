<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateBudgetRemainingValues extends Command
{
    protected $signature = 'budget:recalculate';
    protected $description = 'Recalculate all budget remaining values based on existing expenses';

    public function handle()
    {
        $this->info('Starting budget recalculation...');

        // Get all budgets
        $budgets = DB::table('m_budgeds')->get();

        foreach ($budgets as $budget) {
            // Get total expenses for this budget
            $totalExpenses = DB::table('t_budged_expenses as be')
                ->join('t_expenses as e', 'be.expense_id', '=', 'e.id')
                ->where('be.budged_history_id', $budget->id)
                ->sum('e.total_amount');

            // Calculate new remaining value
            $newRemainingValue = $budget->budged_limit_value - $totalExpenses;

            // Update budget
            DB::table('m_budgeds')
                ->where('id', $budget->id)
                ->update(['budged_remaining_value' => $newRemainingValue]);

            $this->info("Budget #{$budget->id} ({$budget->budged_name}): Limit = {$budget->budged_limit_value}, Expenses = {$totalExpenses}, New Remaining = {$newRemainingValue}");
        }

        $this->info('Budget recalculation completed!');
    }
}
