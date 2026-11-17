<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasBesarService
{
    /**
     * Write one Kas Besar row: a positive amount tops up, a negative one deducts.
     * Returns the new row's id and number.
     */
    public function record(string $type, float $amount, string $date, ?string $notes = null, ?int $expenseId = null): array
    {
        return DB::transaction(function () use ($type, $amount, $date, $notes, $expenseId) {
            $companyCode = session('user_company_code');

            // Taking the number first locks the kas_besar counter row until commit,
            // so no other Kas Besar write can read the same saldo in the meantime.
            $number = app(TransactionNumberService::class)->generateNumber('kas_besar');

            $saldoBefore = (float) DB::table('t_kas_besar')
                ->where('company_code', $companyCode)
                ->orderBy('id', 'DESC')
                ->value('saldo_after');
            $saldoAfter = $saldoBefore + $amount;

            if ($amount < 0 && $saldoAfter < 0) {
                throw ValidationException::withMessages([
                    'kas_besar' => 'Saldo Kas Besar tidak mencukupi. Saldo saat ini: Rp '.number_format($saldoBefore, 0, ',', '.'),
                ]);
            }

            $id = DB::table('t_kas_besar')->insertGetId([
                'company_code' => $companyCode,
                'transaction_number' => $number,
                'transaction_date' => $date,
                'type' => $type,
                'topup_amount' => $amount,
                'saldo_before' => $saldoBefore,
                'saldo_after' => $saldoAfter,
                'f_expense_id' => $expenseId,
                'notes' => $notes,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return ['id' => $id, 'transaction_number' => $number];
        });
    }
}
