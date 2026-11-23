<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasBesarService
{
    /** Stands in for {COUNTER} while the rest of the template is filled in. */
    private const COUNTER = '#COUNTER#';

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
            $number = $this->numberFor($date, true);

            // The saldo runs in date order: the new row follows every row dated on or before it.
            $saldoBefore = $this->saldoOn($date);
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

    /** The number a row dated $date would get, without taking it. */
    public function previewNumber(string $date): string
    {
        return $this->numberFor($date, false);
    }

    /** Saldo at the end of $date: the last row dated on or before it. */
    public function saldoOn(string $date): float
    {
        return (float) DB::table('t_kas_besar')
            ->where('company_code', session('user_company_code'))
            ->where('transaction_date', '<=', $date)
            ->orderBy('transaction_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->value('saldo_after');
    }

    /** The most a deduction dated $date can take without that day or any later row going below zero. */
    public function availableOn(string $date): float
    {
        $lowestLater = DB::table('t_kas_besar')
            ->where('company_code', session('user_company_code'))
            ->where('transaction_date', '>', $date)
            ->min('saldo_after');

        return $lowestLater === null ? $this->saldoOn($date) : min($this->saldoOn($date), (float) $lowestLater);
    }

    /** Opening saldo, money in, money out and closing saldo for a date range (either end may be open). */
    public function summary(?string $from, ?string $to): array
    {
        $rows = DB::table('t_kas_besar')
            ->where('company_code', session('user_company_code'))
            ->when($from, fn ($q) => $q->where('transaction_date', '>=', $from))
            ->when($to, fn ($q) => $q->where('transaction_date', '<=', $to));

        return [
            'opening' => $from ? $this->saldoOn(date('Y-m-d', strtotime($from.' -1 day'))) : 0.0,
            'in' => (float) (clone $rows)->where('topup_amount', '>', 0)->sum('topup_amount'),
            'out' => -(float) (clone $rows)->where('topup_amount', '<', 0)->sum('topup_amount'),
            'closing' => $this->saldoOn($to ?: '9999-12-31'),
            'current' => $this->saldoOn('9999-12-31'),
        ];
    }

    /**
     * A row in the counter's current period (same number prefix as today) takes the next counter value.
     * A row in an earlier period continues that period's own sequence instead, so it neither gets
     * today's prefix nor reuses a number.
     */
    private function numberFor(string $date, bool $take): string
    {
        $numbers = app(TransactionNumberService::class);

        $config = DB::table('m_transaction_numbers')
            ->where('module_name', 'kas_besar')
            ->where('status', 'active')
            ->when($take, fn ($q) => $q->lockForUpdate())
            ->first();

        if (! $config) {
            throw new \Exception('Pengaturan nomor transaksi Kas Besar tidak ditemukan atau tidak aktif.');
        }
        if (! str_contains($config->format_template, '{COUNTER}')) {
            throw new \Exception('Format nomor transaksi Kas Besar harus berisi {COUNTER}.');
        }

        $period = fn (int $timestamp) => $numbers->interpolateTemplate(
            str_replace('{COUNTER}', self::COUNTER, $config->format_template),
            $config->module_initial,
            $config->module_name,
            0,
            0,
            $timestamp
        );
        $pattern = $period(strtotime($date));

        if ($pattern === $period(time())) {
            return $take ? $numbers->generateNumber('kas_besar') : $numbers->getNextNumberPreview('kas_besar');
        }

        [$prefix, $suffix] = explode(self::COUNTER, $pattern, 2);
        $regex = '/^'.preg_quote($prefix, '/').'(\d+)'.preg_quote($suffix, '/').'$/';

        // Numbers are global like the counter, so this is not scoped by company.
        $last = DB::table('t_kas_besar')
            ->where('transaction_number', 'like', $prefix.'%'.$suffix)
            ->pluck('transaction_number')
            ->map(fn ($number) => preg_match($regex, $number, $m) ? (int) $m[1] : 0)
            ->max();

        return str_replace(self::COUNTER, str_pad(($last ?? 0) + 1, $config->counter_padding, '0', STR_PAD_LEFT), $pattern);
    }
}
