<?php

namespace App\Domains\Expense\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;
use App\Domains\Expense\Services\ExpenseService;
use App\Domains\Expense\Services\ExpenseCoaDetailService;
use App\Domains\Expense\Services\ExpenseAuditLogService;


class CreateExpenseAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
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
