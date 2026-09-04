<?php

namespace Tests\Unit;

use App\Services\RevenueSplitCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RevenueSplitCalculatorTest extends TestCase
{
    private RevenueSplitCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new RevenueSplitCalculator;
    }

    public static function validCases(): array
    {
        return [
            'normal' => [
                [580, 0, 0, 232_000_000, 'perusahaan'],
                [580, 145_000_000, 69_600_000, 17_400_000, 232_000_000, 0],
            ],
            'anulir and bonus' => [
                [580, 75, 5, 232_000_000, 'perusahaan'],
                [500, 126_250_000, 60_000_000, 13_750_000, 200_000_000, -1_250_000],
            ],
            'anulir only' => [
                [580, 75, 0, 232_000_000, 'perusahaan'],
                [505, 126_250_000, 60_600_000, 15_150_000, 202_000_000, 0],
            ],
            'bonus only' => [
                [580, 0, 10, 232_000_000, 'perusahaan'],
                [570, 145_000_000, 68_400_000, 14_600_000, 228_000_000, -2_500_000],
            ],
            'fully cancelled' => [
                [580, 580, 0, 232_000_000, 'perusahaan'],
                [0, 0, 0, 0, 0, 0],
            ],
            'rounding remainder' => [
                [580, 0, 0, 232_500_000, 'perusahaan'],
                [580, 145_000_000, 69_600_000, 17_900_000, 232_500_000, 500_000],
            ],
            'combined with remainder' => [
                [580, 75, 5, 232_500_000, 'perusahaan'],
                [500, 126_250_000, 60_000_000, 14_181_034, 200_431_034, -818_966],
            ],
        ];
    }

    #[DataProvider('validCases')]
    public function test_specification_cases(array $case, array $expected): void
    {
        [$qtyTotal, $qtyAnulir, $qtyBonus, $totalProduk, $sisaKe] = $case;

        $result = $this->calculator->calculate([
            'qty_total' => $qtyTotal,
            'qty_anulir' => $qtyAnulir,
            'qty_bonus' => $qtyBonus,
            'rate_pabrik' => 250_000,
            'rate_owner' => 120_000,
            'rate_perusahaan' => 30_000,
            'total_produk' => $totalProduk,
            'sisa_ke' => $sisaKe,
        ]);

        [
            $qtyNormal,
            $pabrik,
            $owner,
            $perusahaan,
            $totalEfektif,
            $selisih,
        ] = $expected;

        $this->assertSame($qtyNormal, $result['qty_normal']);
        $this->assertSame($pabrik, $result['porsi_pabrik']);
        $this->assertSame($owner, $result['porsi_owner']);
        $this->assertSame($perusahaan, $result['porsi_perusahaan']);
        $this->assertSame($totalEfektif, $result['total_efektif']);
        $this->assertSame($totalEfektif, $result['total_split']);
        $this->assertSame($selisih, $result['remaining_amount']);
        $this->assertSame([], $result['errors']);
    }

    public function test_large_bonus_uses_factory_owner_company_priority(): void
    {
        $result = $this->calculator->calculate([
            'qty_total' => 60,
            'qty_anulir' => 0,
            'qty_bonus' => 50,
            'rate_pabrik' => 250_000,
            'rate_owner' => 120_000,
            'rate_perusahaan' => 30_000,
            'total_produk' => 24_000_000,
            'sisa_ke' => 'perusahaan',
        ]);

        $this->assertSame(10, $result['qty_normal']);
        $this->assertSame(15_000_000, $result['porsi_pabrik']);
        $this->assertSame(0, $result['porsi_owner']);
        $this->assertSame(0, $result['porsi_perusahaan']);
        $this->assertSame(15_000_000, $result['total_split']);
        $this->assertSame(15_000_000, $result['required_split_total']);
        $this->assertSame(11_000_000, $result['factory_funding_shortfall']);
        $this->assertSame(-12_500_000, $result['remaining_amount']);
        $this->assertSame([], $result['errors']);

        $this->calculator->assertSavable($result);
    }

    public function test_bonus_only_product_pays_factory_and_zeroes_owner_and_company(): void
    {
        $result = $this->calculator->calculate([
            'qty_total' => 20,
            'qty_anulir' => 0,
            'qty_bonus' => 20,
            'rate_pabrik' => 40_000,
            'rate_owner' => 24_000,
            'rate_perusahaan' => 16_000,
            'total_produk' => 0,
            'sisa_ke' => 'perusahaan',
        ]);

        $this->assertSame(0, $result['qty_normal']);
        $this->assertSame(0, $result['total_efektif']);
        $this->assertSame(800_000, $result['porsi_pabrik']);
        $this->assertSame(0, $result['porsi_owner']);
        $this->assertSame(0, $result['porsi_perusahaan']);
        $this->assertSame(800_000, $result['total_split']);
        $this->assertSame(800_000, $result['required_split_total']);
        $this->assertSame(800_000, $result['factory_funding_shortfall']);
        $this->assertSame(-800_000, $result['remaining_amount']);
        $this->assertSame([], $result['errors']);

        $this->calculator->assertSavable($result);
    }

    public function test_each_product_keeps_its_own_waterfall_in_cross_product_bonus_case(): void
    {
        $menexotic = $this->calculator->calculate([
            'qty_total' => 525,
            'qty_anulir' => 0,
            'qty_bonus' => 25,
            'rate_pabrik' => 250_000,
            'rate_owner' => 120_000,
            'rate_perusahaan' => 30_000,
            'total_produk' => 210_000_000,
            'sisa_ke' => 'perusahaan',
        ]);
        $soulmate = $this->calculator->calculate([
            'qty_total' => 20,
            'qty_anulir' => 0,
            'qty_bonus' => 20,
            'rate_pabrik' => 40_000,
            'rate_owner' => 24_000,
            'rate_perusahaan' => 16_000,
            'total_produk' => 0,
            'sisa_ke' => 'perusahaan',
        ]);

        $this->assertSame(131_250_000, $menexotic['porsi_pabrik']);
        $this->assertSame(60_000_000, $menexotic['porsi_owner']);
        $this->assertSame(8_750_000, $menexotic['porsi_perusahaan']);
        $this->assertSame(200_000_000, $menexotic['total_split']);

        $this->assertSame(800_000, $soulmate['porsi_pabrik']);
        $this->assertSame(0, $soulmate['porsi_owner']);
        $this->assertSame(0, $soulmate['porsi_perusahaan']);
        $this->assertSame(800_000, $soulmate['total_split']);

        $this->calculator->assertSavable($menexotic);
        $this->calculator->assertSavable($soulmate);
    }

    public function test_remainder_is_allocated_to_selected_party(): void
    {
        $result = $this->calculator->calculate([
            'qty_total' => 580,
            'qty_anulir' => 0,
            'qty_bonus' => 0,
            'rate_pabrik' => 250_000,
            'rate_owner' => 120_000,
            'rate_perusahaan' => 30_000,
            'total_produk' => 232_500_000,
            'sisa_ke' => 'owner',
        ]);

        $this->assertSame(70_100_000, $result['porsi_owner']);
        $this->assertSame(17_400_000, $result['porsi_perusahaan']);
        $this->assertSame(232_500_000, $result['total_split']);
    }

    public function test_remainder_above_one_percent_does_not_block_save(): void
    {
        $result = $this->calculator->calculate([
            'qty_total' => 3,
            'qty_anulir' => 0,
            'qty_bonus' => 0,
            'rate_pabrik' => 250_000,
            'rate_owner' => 120_000,
            'rate_perusahaan' => 30_000,
            'total_produk' => 1_500_000,
            'sisa_ke' => 'perusahaan',
        ]);

        $this->assertSame(300_000, $result['remaining_amount']);
        $this->assertSame(390_000, $result['porsi_perusahaan']);
        $this->assertSame(1_500_000, $result['total_split']);
        $this->assertSame([], $result['errors']);

        $this->calculator->assertSavable($result);
    }

    public function test_shortage_is_allocated_by_factory_owner_company_priority(): void
    {
        foreach (['pabrik', 'owner', 'perusahaan'] as $sisaKe) {
            $result = $this->calculator->calculate([
                'qty_total' => 1,
                'qty_anulir' => 0,
                'qty_bonus' => 0,
                'rate_pabrik' => 450_000,
                'rate_owner' => 360_000,
                'rate_perusahaan' => 90_000,
                'total_produk' => 800_000,
                'sisa_ke' => $sisaKe,
            ]);

            $this->assertSame(450_000, $result['porsi_pabrik']);
            $this->assertSame(350_000, $result['porsi_owner']);
            $this->assertSame(0, $result['porsi_perusahaan']);
            $this->assertSame(-100_000, $result['remaining_amount']);
            $this->assertSame(800_000, $result['total_split']);
            $this->assertSame([], $result['errors']);

            $this->calculator->assertSavable($result);
        }
    }

    public function test_factory_obligation_can_exceed_effective_total(): void
    {
        $result = $this->calculator->calculate([
            'qty_total' => 1,
            'qty_anulir' => 0,
            'qty_bonus' => 0,
            'rate_pabrik' => 450_000,
            'rate_owner' => 360_000,
            'rate_perusahaan' => 90_000,
            'total_produk' => 400_000,
            'sisa_ke' => 'perusahaan',
        ]);

        $this->assertSame(450_000, $result['porsi_pabrik']);
        $this->assertSame(0, $result['porsi_owner']);
        $this->assertSame(0, $result['porsi_perusahaan']);
        $this->assertSame(450_000, $result['total_split']);
        $this->assertSame(450_000, $result['required_split_total']);
        $this->assertSame(50_000, $result['factory_funding_shortfall']);

        $this->calculator->assertSavable($result);
    }

    public function test_anulir_and_bonus_cannot_exceed_total_quantity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Qty anulir + qty bonus');

        $this->calculator->calculate([
            'qty_total' => 10,
            'qty_anulir' => 6,
            'qty_bonus' => 5,
            'rate_pabrik' => 1,
            'rate_owner' => 1,
            'rate_perusahaan' => 1,
            'total_produk' => 30,
            'sisa_ke' => 'perusahaan',
        ]);
    }
}
