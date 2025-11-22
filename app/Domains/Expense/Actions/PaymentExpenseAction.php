<?php

namespace App\Domains\Expense\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;
use App\Domains\Expense\Services\ExpenseService;
use App\Domains\Expense\Services\ExpensePaymentService;
use App\Domains\Expense\Services\ExpenseAuditLogService;


class PaymentExpenseAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                $data['attachment'] = $data['attachment']->store('expense_payments', 'public');
            }

            app(ExpensePaymentService::class)->create($data);

            app(ExpenseService::class)->updateHeaderAmount($data['expense_id'], $data['payment_amount']);

            return true;
        });
    }
}
