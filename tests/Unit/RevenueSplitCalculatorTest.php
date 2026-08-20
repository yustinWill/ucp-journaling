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
                [500, 126_250_000, 58_750_000, 15_000_000, 200_000_000, 0],
            ],
            'anulir only' => [
                [580, 75, 0, 232_000_000, 'perusahaan'],
                [505, 126_250_000, 60_600_000, 15_150_000, 202_000_000, 0],
            ],
            'bonus only' => [
                [580, 0, 10, 232_000_000, 'perusahaan'],
                [570, 145_000_000, 65_900_000, 17_100_000, 228_000_000, 0],
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
                [500, 126_250_000, 58_750_000, 15_431_034, 200_431_034, 431_034],
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

    public function test_negative_owner_is_calculated_and_blocks_save(): void
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
        $this->assertSame(-11_300_000, $result['porsi_owner']);
        $this->assertSame(300_000, $result['porsi_perusahaan']);
        $this->assertSame(4_000_000, $result['total_split']);
        $this->assertNotEmpty($result['errors']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Porsi Owner negatif');
        $this->calculator->assertSavable($result);
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

    public function test_shortage_cannot_allocate_more_than_effective_total_to_factory(): void
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

        $this->assertSame(400_000, $result['porsi_pabrik']);
        $this->assertSame(0, $result['porsi_owner']);
        $this->assertSame(0, $result['porsi_perusahaan']);
        $this->assertSame(400_000, $result['total_split']);

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
