<?php

namespace App\Domains\Budged\Actions;

use App\Domains\Budged\Services\BudgedService;
use App\Domains\BudgetCategory\Services\BudgetCategoryService;
use Illuminate\Support\Facades\DB;

class CreateBudgedAction
{
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $budgedService = app(BudgedService::class);
            $periodStartDate = $data['period_start_date'];
            $periodEndDate = $data['period_end_date'];
            $type = $data['type'] ?? 'kingdom';
            $budgets = $data['budgets'];

            // Step 1: Create the period first
            $periodId = $budgedService->createPeriod([
                'period_start_date' => $periodStartDate,
                'period_end_date' => $periodEndDate,
                'type' => $type,
            ]);

            $createdBudgets = [];
            $createdCount = 0;

            // Step 2: Create multiple budgets for this period
            foreach ($budgets as $budget) {
                // Get category name from category ID
                $category = app(BudgetCategoryService::class)->findById($budget['f_budget_category_id']);

                $budgedId = $budgedService->create([
                    'f_budget_period_id' => $periodId,
                    'f_budget_category_id' => $budget['f_budget_category_id'],
                    'budged_name' => $category->category_name,
                    'budged_limit_value' => $budget['budged_limit_value'],
                    'budged_remaining_value' => $budget['budged_limit_value'],
                ]);

                $createdBudgets[] = [
                    'budged_id' => $budgedId,
                    'budged_name' => $category->category_name,
                    'budged_limit_value' => $budget['budged_limit_value'],
                ];

                $createdCount++;
            }

            return [
                'period_id' => $periodId,
                'period_start_date' => $periodStartDate,
                'period_end_date' => $periodEndDate,
                'budgets_created' => $createdBudgets,
                'count' => $createdCount,
            ];
        });
    }
}
