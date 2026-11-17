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
            $number = $this->numberFor($date);

            // The saldo runs in date order: the new row follows every row dated on or before it.
            $saldoBefore = (float) DB::table('t_kas_besar')
                ->where('company_code', $companyCode)
                ->where('transaction_date', '<=', $date)
                ->orderBy('transaction_date', 'DESC')
                ->orderBy('id', 'DESC')
                ->value('saldo_after');
            $saldoAfter = $saldoBefore + $amount;

            if ($amount < 0 && $saldoAfter < 0) {
                throw ValidationException::withMessages([
                    'kas_besar' => 'Saldo Kas Besar tidak mencukupi. Saldo pada '.date('d/m/Y', strtotime($date)).': Rp '.number_format($saldoBefore, 0, ',', '.'),
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

            // A backdated row shifts the saldo of every later-dated row; carry it forward.
            $saldo = $saldoAfter;
            $later = DB::table('t_kas_besar')
                ->where('company_code', $companyCode)
                ->where('transaction_date', '>', $date)
                ->orderBy('transaction_date')
                ->orderBy('id')
                ->get(['id', 'transaction_number', 'topup_amount']);
            foreach ($later as $row) {
                $before = $saldo;
                $saldo += (float) $row->topup_amount;
                if ($saldo < 0) {
                    throw ValidationException::withMessages([
                        'kas_besar' => 'Saldo Kas Besar tidak mencukupi: transaksi '.$row->transaction_number.' sesudahnya akan menjadi minus.',
                    ]);
                }
                DB::table('t_kas_besar')->where('id', $row->id)->update(['saldo_before' => $before, 'saldo_after' => $saldo]);
            }

            return ['id' => $id, 'transaction_number' => $number];
        });
    }

    /**
     * A row dated in the current month takes the next counter value. A backdated row continues
     * its own month's sequence instead, so it neither gets this month's prefix nor reuses a number.
     */
    private function numberFor(string $date): string
    {
        $numbers = app(TransactionNumberService::class);
        $timestamp = strtotime($date);

        if (date('Y-m', $timestamp) === date('Y-m')) {
            return $numbers->generateNumber('kas_besar');
        }

        $config = DB::table('m_transaction_numbers')
            ->where('module_name', 'kas_besar')
            ->where('status', 'active')
            ->lockForUpdate()
            ->first();

        $prefix = $numbers->interpolateTemplate(
            strstr($config->format_template, '{COUNTER}', true),
            $config->module_initial,
            $config->module_name,
            0,
            0,
            $timestamp
        );

        // Numbers are global like the counter, so this is not scoped by company.
        $last = DB::table('t_kas_besar')
            ->where('transaction_number', 'like', $prefix.'%')
            ->pluck('transaction_number')
            ->map(fn ($number) => (int) substr($number, strlen($prefix)))
            ->max();

        return $numbers->interpolateTemplate(
            $config->format_template,
            $config->module_initial,
            $config->module_name,
            ($last ?? 0) + 1,
            $config->counter_padding,
            $timestamp
        );
    }
}
