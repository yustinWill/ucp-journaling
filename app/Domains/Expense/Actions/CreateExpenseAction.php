<?php

namespace App\Domains\Expense\Actions;

use App\Domains\Budged\Services\BudgedService;
use App\Domains\Expense\Services\ExpenseAuditLogService;
use App\Domains\Expense\Services\ExpenseCoaDetailService;
use App\Domains\Expense\Services\ExpenseService;
use App\Enums\ActionTypes;
use App\Services\TransactionNumberService;
use Illuminate\Support\Facades\DB;

class CreateExpenseAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // The form only previewed the number; take it now, so a save that fails takes none.
            if (! empty($data['auto_number'])) {
                $data['transaction_number'] = app(TransactionNumberService::class)->generateNumber('expense');
            }

            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                $data['attachment'] = $data['attachment']->store('expenses', 'public');
            }

            $expense_id = app(ExpenseService::class)->create($data);

            $expense_coa_details = [];
            foreach ($data['expense_lines'] as $line) {
                $expense_coa_details[] = [
                    'f_expense_id' => $expense_id,
                    'f_coa_code' => $line['expense_account'],
                    'f_tax_id' => $line['tax_id'],
                    'amount' => $line['amount'],
                ];
            }
            app(ExpenseCoaDetailService::class)->create($expense_coa_details);

            // Save budget mapping if provided and update budget remaining value
            if (! empty($data['budged_history_id'])) {
                DB::table('t_budged_expenses')->insert([
                    'budged_history_id' => $data['budged_history_id'],
                    'expense_id' => $expense_id,
                ]);

                // Update budget remaining value
                app(BudgedService::class)->updateBudgetRemaining(
                    $data['budged_history_id'],
                    $data['final_total']
                );
            }

            if ($data['pay_later'] == false) {
                app(PaymentExpenseAction::class)->execute([
                    'expense_id' => $expense_id,
                    'transaction_number' => $data['transaction_number'],
                    'transaction_date' => $data['transaction_date'],
                    'recipient_id' => $data['recipient_id'],
                    'payment_coa' => null,
                    'description' => $data['description'] ?? null,
                    'memo' => $data['memo'] ?? null,
                    'tags' => $data['tags'] ?? [],
                    'attachment' => $data['attachment'] ?? null,
                    'remaining_amount' => $data['final_total'],
                    'payment_amount' => $data['final_total'],
                ]);
            }
            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembentukan Biaya',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(ExpenseAuditLogService::class)->create($log_data, $expense_id);
        });
    }
}
