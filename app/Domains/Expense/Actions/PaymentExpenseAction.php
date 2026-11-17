<?php

namespace App\Domains\Expense\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;
use App\Domains\Expense\Services\ExpenseService;
use App\Domains\Expense\Services\ExpensePaymentService;
use App\Domains\Expense\Services\ExpenseAuditLogService;
use App\Services\KasBesarService;


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

            // Kas/Tunai expenses take money out of Kas Besar when they are paid, not when they are created.
            $expense = DB::table('t_expenses')->where('id', $data['expense_id'])->first(['payment_method_type', 'transaction_no']);
            if ($expense->payment_method_type === 'kas_tunai') {
                $kas = app(KasBesarService::class)->record(
                    'DEDUCTION',
                    -(float) $data['payment_amount'],
                    $data['transaction_date'],
                    'Pengeluaran: '.$expense->transaction_no,
                    $data['expense_id']
                );

                DB::table('t_expenses')->where('id', $data['expense_id'])->update(['f_kas_besar_id' => $kas['id']]);
            }

            return true;
        });
    }
}
