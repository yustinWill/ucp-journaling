<?php

namespace App\Domains\Expense\Actions;

use App\Domains\Expense\Services\ExpenseAuditLogService;
use App\Domains\Expense\Services\ExpenseCoaDetailService;
use App\Domains\Expense\Services\ExpenseService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;
use App\Domains\Budged\Services\BudgedService;

class UpdateExpenseAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                $data['attachment'] = $data['attachment']->store('expenses', 'public');
            }

            $expense_id = $data['expense_id'];
            // Read before the update, so the old budget gets the old total back.
            $oldExpense = app(ExpenseService::class)->findOne($expense_id);
            app(ExpenseService::class)->update($data, $expense_id);

            $expense_coa_details = [];
            foreach ($data['expense_lines'] as $line) {
                $expense_coa_details[] = [
                    'f_expense_id' => $expense_id,
                    'f_coa_code' => $line['expense_account'],
                    'f_tax_id' => $line['tax_id'],
                    'amount' => $line['amount'],
                ];
            }

            app(ExpenseCoaDetailService::class)->deleteByExpenseId($expense_id);

            app(ExpenseCoaDetailService::class)->create($expense_coa_details);

            $oldBudgetMapping = DB::table('t_budged_expenses')
                ->where('expense_id', $expense_id)
                ->first();

            // Update budget mapping and remaining values
            DB::table('t_budged_expenses')->where('expense_id', $expense_id)->delete();

            $budgedService = app(BudgedService::class);

            // If there was an old budget, restore its remaining value
            if ($oldBudgetMapping) {
                // Add back the old expense amount to the old budget
                DB::table('m_budgeds')
                    ->where('id', $oldBudgetMapping->budged_history_id)
                    ->increment('budged_remaining_value', $oldExpense->total_amount);
            }

            // If there's a new budget, deduct the new amount
            if (!empty($data['budged_history_id'])) {
                DB::table('t_budged_expenses')->insert([
                    'budged_history_id' => $data['budged_history_id'],
                    'expense_id' => $expense_id,
                ]);

                // Deduct new expense amount from new budget
                $budgedService->updateBudgetRemaining(
                    $data['budged_history_id'],
                    $data['final_total']
                );
            }

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Biaya',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(ExpenseAuditLogService::class)->create($log_data, $expense_id);
        });
    }
}
