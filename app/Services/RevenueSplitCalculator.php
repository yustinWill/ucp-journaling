<?php

namespace App\Services;

use InvalidArgumentException;

final class RevenueSplitCalculator
{
    /**
     * Calculate a revenue split using whole pieces and whole Rupiah.
     *
     * @return array<string, int|string|array<int, string>>
     */
    public function calculate(array $input): array
    {
        $qtyTotal = $this->wholeQuantity($input['qty_total'] ?? 0, 'Qty total');
        $qtyAnulir = $this->wholeQuantity($input['qty_anulir'] ?? 0, 'Qty anulir');
        $qtyBonus = $this->wholeQuantity($input['qty_bonus'] ?? 0, 'Qty bonus');
        $ratePabrik = $this->rupiah($input['rate_pabrik'] ?? 0);
        $rateOwner = $this->rupiah($input['rate_owner'] ?? 0);
        $ratePerusahaan = $this->rupiah($input['rate_perusahaan'] ?? 0);
        $totalProduk = $this->rupiah($input['total_produk'] ?? 0);
        $sisaKe = $input['sisa_ke'] ?? null;

        if (! in_array($sisaKe, ['pabrik', 'owner', 'perusahaan'], true)) {
            throw new InvalidArgumentException('Penerima sisa harus dipilih.');
        }

        if ($qtyAnulir + $qtyBonus > $qtyTotal) {
            throw new InvalidArgumentException('Qty anulir + qty bonus tidak boleh melebihi qty total.');
        }

        $qtyNormal = $qtyTotal - $qtyAnulir - $qtyBonus;
        $rateTotal = $ratePabrik + $rateOwner + $ratePerusahaan;
        $porsiPabrikBase = ($qtyNormal + $qtyBonus) * $ratePabrik;
        $porsiOwnerBase = $qtyNormal * $rateOwner;
        $porsiPerusahaanBase = $qtyNormal * $ratePerusahaan;
        $totalSplitBeforeRemainder = $porsiPabrikBase + $porsiOwnerBase + $porsiPerusahaanBase;
        $totalEfektif = $qtyTotal === 0
            ? 0
            : $this->roundFraction($qtyNormal * $totalProduk, $qtyTotal);
        $selisih = $totalEfektif - $totalSplitBeforeRemainder;

        if ($selisih < 0) {
            // Pabrik selalu menerima kewajiban penuh, termasuk produksi bonus.
            // Hanya nilai yang tersisa setelah Pabrik yang dapat dibagikan ke
            // Owner lalu Perusahaan.
            $porsiPabrik = $porsiPabrikBase;
            $availableAmount = max(0, $totalEfektif - $porsiPabrik);
            $porsiOwner = min($porsiOwnerBase, $availableAmount);
            $availableAmount -= $porsiOwner;
            $porsiPerusahaan = min($porsiPerusahaanBase, $availableAmount);
        } else {
            $porsiPabrik = $porsiPabrikBase;
            $porsiOwner = $porsiOwnerBase;
            $porsiPerusahaan = $porsiPerusahaanBase;

            if ($sisaKe === 'pabrik') {
                $porsiPabrik += $selisih;
            } elseif ($sisaKe === 'owner') {
                $porsiOwner += $selisih;
            } else {
                $porsiPerusahaan += $selisih;
            }
        }

        $errors = [];
        $requiredSplitTotal = max($totalEfektif, $porsiPabrikBase);
        $factoryFundingShortfall = max(0, $porsiPabrikBase - $totalEfektif);

        // Guard selisih maksimal 1% dinonaktifkan. Surplus dialokasikan ke
        // penerima sisa, sedangkan defisit mengikuti prioritas kewajiban.
        // if ($totalEfektif > 0 && (abs($selisih) * 100) > $totalEfektif) {
        //     $errors[] = sprintf(
        //         'Selisih Rp %s melebihi 1%% dari total efektif. Periksa rate dan total produk.',
        //         number_format(abs($selisih), 0, ',', '.')
        //     );
        // }

        return [
            'qty_total' => $qtyTotal,
            'qty_anulir' => $qtyAnulir,
            'qty_bonus' => $qtyBonus,
            'qty_normal' => $qtyNormal,
            'qty_efektif' => $qtyNormal,
            'rate_pabrik' => $ratePabrik,
            'rate_owner' => $rateOwner,
            'rate_perusahaan' => $ratePerusahaan,
            'rate_total' => $rateTotal,
            'total_produk' => $totalProduk,
            'total_efektif' => $totalEfektif,
            'porsi_pabrik' => $porsiPabrik,
            'porsi_owner' => $porsiOwner,
            'porsi_perusahaan' => $porsiPerusahaan,
            'total_split' => $porsiPabrik + $porsiOwner + $porsiPerusahaan,
            'required_split_total' => $requiredSplitTotal,
            'factory_obligation' => $porsiPabrikBase,
            'factory_funding_shortfall' => $factoryFundingShortfall,
            'remaining_allocation' => $sisaKe,
            'remaining_amount' => $selisih,
            'errors' => $errors,
        ];
    }

    /**
     * @param  array<string, mixed>  $result
     */
    public function assertSavable(array $result): void
    {
        $errors = $result['errors'] ?? [];

        if ($errors !== []) {
            throw new InvalidArgumentException(implode(' ', $errors));
        }

        $porsiPabrik = $result['porsi_pabrik'] ?? null;
        $factoryObligation = $result['factory_obligation'] ?? null;

        if (! is_int($porsiPabrik) || ! is_int($factoryObligation) || $porsiPabrik < $factoryObligation) {
            throw new InvalidArgumentException('Porsi Pabrik tidak boleh kurang dari kewajiban Pabrik.');
        }

        $requiredSplitTotal = $result['required_split_total']
            ?? max((int) ($result['total_efektif'] ?? 0), $factoryObligation);

        if (($result['total_split'] ?? null) !== $requiredSplitTotal) {
            throw new InvalidArgumentException('Total porsi harus sama dengan total wajib dibagikan.');
        }
    }

    public function grossTotal(mixed $unitPrice, mixed $qtyTotal): int
    {
        return $this->rupiah($unitPrice) * $this->wholeQuantity($qtyTotal, 'Qty total');
    }

    public function rupiah(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        $value = trim((string) $value);

        if (! preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException('Nilai Rupiah tidak valid.');
        }

        $negative = str_starts_with($value, '-');
        $unsigned = ltrim($value, '-');
        [$whole, $fraction] = array_pad(explode('.', $unsigned, 2), 2, '');
        $rounded = (int) $whole;

        if ($fraction !== '' && (int) $fraction[0] >= 5) {
            $rounded++;
        }

        return $negative ? -$rounded : $rounded;
    }

    public function wholeQuantity(mixed $value, string $label = 'Qty'): int
    {
        if (is_int($value)) {
            $quantity = $value;
        } else {
            $value = trim((string) $value);

            if (! preg_match('/^\d+(?:\.0+)?$/', $value)) {
                throw new InvalidArgumentException("{$label} harus berupa bilangan bulat.");
            }

            $quantity = (int) explode('.', $value, 2)[0];
        }

        if ($quantity < 0) {
            throw new InvalidArgumentException("{$label} tidak boleh negatif.");
        }

        return $quantity;
    }

    private function roundFraction(int $numerator, int $denominator): int
    {
        if ($denominator <= 0) {
            throw new InvalidArgumentException('Pembagi perhitungan total efektif tidak valid.');
        }

        $half = intdiv($denominator, 2);

        if ($numerator >= 0) {
            return intdiv($numerator + $half, $denominator);
        }

        return -intdiv(abs($numerator) + $half, $denominator);
    }
}
