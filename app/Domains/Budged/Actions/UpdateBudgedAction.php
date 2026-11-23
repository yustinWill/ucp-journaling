<?php

namespace App\Domains\Budged\Actions;

use App\Domains\Budged\Services\BudgedService;
use App\Domains\BudgetCategory\Services\BudgetCategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateBudgedAction
{
    public function execute(int $periodId, string $type, array $budgets, ?string $periodEndDate = null): array
    {
        return DB::transaction(function () use ($periodId, $type, $budgets, $periodEndDate) {
            $budgedService = app(BudgedService::class);

            // Update period type
            $budgedService->updatePeriodType($periodId, $type);

            if ($periodEndDate) {
                $this->updateEndDate($budgedService, $periodId, $periodEndDate);
            }

            $updatedCount = 0;
            $createdCount = 0;

            foreach ($budgets as $budget) {
                if (isset($budget['id']) && $budget['id']) {
                    // Update existing budget
                    $oldBudget = $budgedService->findById($budget['id']);

                    // What was already used stays used: the remaining value moves by the change in limit.
                    $used = (float) $oldBudget->budged_limit_value - (float) $oldBudget->budged_remaining_value;

                    $budgedService->update($budget['id'], [
                        'budged_limit_value' => $budget['budged_limit_value'],
                        'budged_remaining_value' => (float) $budget['budged_limit_value'] - $used,
                    ]);
                    $updatedCount++;
                } else {
                    // Create new budget
                    $budgedService->create([
                        'f_budget_period_id' => $periodId,
                        'f_budget_category_id' => $budget['f_budget_category_id'],
                        'budged_name' => app(BudgetCategoryService::class)->findById($budget['f_budget_category_id'])->category_name,
                        'budged_limit_value' => $budget['budged_limit_value'],
                        'budged_remaining_value' => $budget['budged_limit_value'],
                    ]);
                    $createdCount++;
                }
            }

            return [
                'period_id' => $periodId,
                'period_end_date' => $periodEndDate,
                'updated_count' => $updatedCount,
                'created_count' => $createdCount,
                'total_budgets' => $updatedCount + $createdCount,
            ];
        });
    }

    /**
     * Expenses are only accepted inside their budget's period, so the end date may not move
     * before the start date or before an expense already charged to this period.
     */
    private function updateEndDate(BudgedService $budgedService, int $periodId, string $periodEndDate): void
    {
        $period = $budgedService->findPeriodById($periodId);
        $startDate = substr((string) $period->period_start_date, 0, 10);
        if ($periodEndDate === substr((string) $period->period_end_date, 0, 10)) {
            return;
        }

        if ($periodEndDate < $startDate) {
            throw ValidationException::withMessages([
                'period_end_date' => 'Tanggal akhir periode tidak boleh sebelum tanggal mulai ('.date('d/m/Y', strtotime($startDate)).').',
            ]);
        }

        $lastExpenseDate = DB::table('t_budged_expenses as be')
            ->join('m_budgeds as b', 'b.id', '=', 'be.budged_history_id')
            ->join('t_expenses as e', 'e.id', '=', 'be.expense_id')
            ->where('b.f_budget_period_id', $periodId)
            ->max('e.transaction_date');
        $lastExpenseDate = $lastExpenseDate ? substr((string) $lastExpenseDate, 0, 10) : null;

        if ($lastExpenseDate && $periodEndDate < $lastExpenseDate) {
            throw ValidationException::withMessages([
                'period_end_date' => 'Tanggal akhir periode tidak boleh sebelum '.date('d/m/Y', strtotime($lastExpenseDate)).': sudah ada pengeluaran di periode ini pada tanggal itu.',
            ]);
        }

        DB::table('m_budget_periods')->where('id', $periodId)->update([
            'period_end_date' => $periodEndDate,
            'updated_at' => now(),
        ]);
    }
}
