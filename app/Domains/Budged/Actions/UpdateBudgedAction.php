<?php

namespace App\Domains\Budged\Actions;

use App\Domains\Budged\Services\BudgedService;
use Illuminate\Support\Facades\DB;

class UpdateBudgedAction
{
    public function execute(int $periodId, array $budgets): array
    {
        return DB::transaction(function () use ($periodId, $budgets) {
            $budgedService = app(BudgedService::class);

            $updatedCount = 0;
            $createdCount = 0;

            foreach ($budgets as $budget) {
                if (isset($budget['id']) && $budget['id']) {
                    // Update existing budget
                    $oldBudget = $budgedService->findById($budget['id']);

                    $updateData = [
                        'f_budget_category_id' => $budget['f_budget_category_id'],
                        'budged_limit_value' => $budget['budged_limit_value'],
                    ];

                    // Smart update: If old limit equals old remaining, update remaining to match new limit
                    if ($oldBudget && $oldBudget->budged_limit_value == $oldBudget->budged_remaining_value) {
                        $updateData['budged_remaining_value'] = $budget['budged_limit_value'];
                    } elseif (isset($budget['budged_remaining_value'])) {
                        $updateData['budged_remaining_value'] = $budget['budged_remaining_value'];
                    }

                    $budgedService->update($budget['id'], $updateData);
                    $updatedCount++;
                } else {
                    // Create new budget
                    $budgedService->create([
                        'f_budget_period_id' => $periodId,
                        'f_budget_category_id' => $budget['f_budget_category_id'],
                        'budged_limit_value' => $budget['budged_limit_value'],
                        'budged_remaining_value' => $budget['budged_limit_value'],
                    ]);
                    $createdCount++;
                }
            }

            return [
                'period_id' => $periodId,
                'updated_count' => $updatedCount,
                'created_count' => $createdCount,
                'total_budgets' => $updatedCount + $createdCount,
            ];
        });
    }
}
