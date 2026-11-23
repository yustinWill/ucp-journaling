<?php

namespace Tests\Feature;

use App\Services\KasBesarService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class KasBesarServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('t_kas_besar', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->string('transaction_number');
            $table->date('transaction_date');
            $table->string('type');
            $table->decimal('topup_amount', 15, 2);
            $table->decimal('saldo_before', 15, 2);
            $table->decimal('saldo_after', 15, 2);
            $table->unsignedBigInteger('f_expense_id')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('m_transaction_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('module_name');
            $table->string('module_initial');
            $table->string('format_template');
            $table->unsignedBigInteger('current_counter');
            $table->tinyInteger('counter_padding');
            $table->string('reset_frequency');
            $table->date('last_reset_date')->nullable();
            $table->string('status');
            $table->timestamps();
        });
        DB::table('m_transaction_numbers')->insert([
            'module_name' => 'kas_besar', 'module_initial' => 'KB', 'format_template' => 'KB-{YEAR}-{MM}-{COUNTER}',
            'current_counter' => 0, 'counter_padding' => 4, 'reset_frequency' => 'monthly',
            'last_reset_date' => now()->toDateString(), 'status' => 'active',
        ]);

        session(['user_company_code' => '001']);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('t_kas_besar');
        Schema::dropIfExists('m_transaction_numbers');

        parent::tearDown();
    }

    public function test_rows_carry_a_running_saldo(): void
    {
        $kas = app(KasBesarService::class);

        $kas->record('TOPUP', 100000, now()->toDateString());
        $row = $kas->record('DEDUCTION', -30000, now()->toDateString(), 'Pengeluaran: EXP-1', 7);

        $deduction = DB::table('t_kas_besar')->find($row['id']);
        $this->assertSame('KB-'.now()->format('Y-m').'-0002', $row['transaction_number']);
        $this->assertEquals(100000, $deduction->saldo_before);
        $this->assertEquals(70000, $deduction->saldo_after);
        $this->assertEquals(7, $deduction->f_expense_id);
    }

    public function test_a_deduction_larger_than_the_saldo_is_refused(): void
    {
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 50000, now()->toDateString());

        try {
            $kas->record('DEDUCTION', -80000, now()->toDateString());
            $this->fail('Expected the deduction to be refused.');
        } catch (ValidationException $e) {
            $this->assertSame(1, DB::table('t_kas_besar')->count());
        }
    }

    public function test_a_backdated_row_continues_its_own_month(): void
    {
        $kas = app(KasBesarService::class);
        $lastMonth = now()->subMonthNoOverflow();

        $this->assertSame('KB-'.now()->format('Y-m').'-0001', $kas->record('TOPUP', 10000, now()->toDateString())['transaction_number']);
        $this->assertSame('KB-'.$lastMonth->format('Y-m').'-0001', $kas->record('TOPUP', 10000, $lastMonth->toDateString())['transaction_number']);
        $this->assertSame('KB-'.$lastMonth->format('Y-m').'-0002', $kas->record('TOPUP', 10000, $lastMonth->toDateString())['transaction_number']);
        $this->assertSame('KB-'.now()->format('Y-m').'-0002', $kas->record('TOPUP', 10000, now()->toDateString())['transaction_number']);
    }

    public function test_a_backdated_row_moves_the_saldo_of_every_later_row(): void
    {
        $kas = app(KasBesarService::class);
        $today = now()->toDateString();
        $earlier = now()->subDays(40)->toDateString();

        $kas->record('TOPUP', 100000, $today);
        $kas->record('DEDUCTION', -30000, $today);
        $kas->record('TOPUP', 50000, $earlier);

        $this->assertSame([[0, 50000], [50000, 150000], [150000, 120000]], $this->chain());
    }

    public function test_a_backdated_deduction_that_would_overdraw_a_later_row_is_refused(): void
    {
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 10000, now()->subDays(40)->toDateString());
        $kas->record('DEDUCTION', -10000, now()->toDateString());

        try {
            $kas->record('DEDUCTION', -5000, now()->subDays(20)->toDateString());
            $this->fail('Expected the backdated deduction to be refused.');
        } catch (ValidationException $e) {
            $this->assertSame(2, DB::table('t_kas_besar')->count());
            $this->assertSame([[0, 10000], [10000, 0]], $this->chain());
        }
    }

    public function test_the_preview_follows_the_date_and_takes_nothing(): void
    {
        $kas = app(KasBesarService::class);
        $lastMonth = now()->subMonthNoOverflow();

        $this->assertSame('KB-'.now()->format('Y-m').'-0001', $kas->previewNumber(now()->toDateString()));
        $this->assertSame('KB-'.$lastMonth->format('Y-m').'-0001', $kas->previewNumber($lastMonth->toDateString()));
        $this->assertEquals(0, DB::table('m_transaction_numbers')->value('current_counter'));

        $kas->record('TOPUP', 1000, $lastMonth->toDateString());
        $this->assertSame('KB-'.$lastMonth->format('Y-m').'-0002', $kas->previewNumber($lastMonth->toDateString()));
    }

    public function test_the_preview_restarts_at_one_after_a_monthly_reset(): void
    {
        DB::table('m_transaction_numbers')->update(['current_counter' => 7, 'last_reset_date' => now()->subMonthNoOverflow()->toDateString()]);

        $this->assertSame('KB-'.now()->format('Y-m').'-0001', app(KasBesarService::class)->previewNumber(now()->toDateString()));
    }

    public function test_a_template_without_the_month_keeps_one_sequence_for_backdated_rows(): void
    {
        DB::table('m_transaction_numbers')->update(['format_template' => 'KB-{YEAR}-{COUNTER}', 'reset_frequency' => 'yearly']);
        $kas = app(KasBesarService::class);
        $year = now()->format('Y');

        $numbers = [
            $kas->record('TOPUP', 1000, now()->toDateString())['transaction_number'],
            $kas->record('TOPUP', 1000, now()->startOfYear()->toDateString())['transaction_number'],
            $kas->record('TOPUP', 1000, now()->toDateString())['transaction_number'],
        ];

        $this->assertSame(["KB-$year-0001", "KB-$year-0002", "KB-$year-0003"], $numbers);
    }

    public function test_a_template_with_text_after_the_counter_numbers_backdated_rows(): void
    {
        DB::table('m_transaction_numbers')->update(['format_template' => '{COUNTER}/KB/{MM}/{YEAR}']);
        $kas = app(KasBesarService::class);
        $lastMonth = now()->subMonthNoOverflow();
        $period = $lastMonth->format('m/Y');

        $kas->record('TOPUP', 1000, now()->toDateString());
        $this->assertSame("0001/KB/$period", $kas->record('TOPUP', 1000, $lastMonth->toDateString())['transaction_number']);
        $this->assertSame("0002/KB/$period", $kas->record('TOPUP', 1000, $lastMonth->toDateString())['transaction_number']);
    }

    public function test_a_missing_inactive_or_broken_number_setting_is_refused_clearly(): void
    {
        $kas = app(KasBesarService::class);
        $lastMonth = now()->subMonthNoOverflow()->toDateString();

        DB::table('m_transaction_numbers')->update(['status' => 'inactive']);
        foreach ([now()->toDateString(), $lastMonth] as $date) {
            try {
                $kas->record('TOPUP', 1000, $date);
                $this->fail('Expected an inactive setting to be refused.');
            } catch (\Exception $e) {
                $this->assertStringContainsString('Pengaturan nomor transaksi Kas Besar', $e->getMessage());
            }
        }

        DB::table('m_transaction_numbers')->update(['status' => 'active', 'format_template' => 'KB-{YEAR}-{MM}']);
        try {
            $kas->record('TOPUP', 1000, $lastMonth);
            $this->fail('Expected a template without {COUNTER} to be refused.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('{COUNTER}', $e->getMessage());
        }

        $this->assertSame(0, DB::table('t_kas_besar')->count());
    }

    public function test_saldo_on_a_date_and_what_a_deduction_may_take(): void
    {
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 100000, now()->subDays(20)->toDateString());
        $kas->record('DEDUCTION', -70000, now()->subDays(5)->toDateString());

        $this->assertEquals(0, $kas->saldoOn(now()->subDays(30)->toDateString()));
        $this->assertEquals(100000, $kas->saldoOn(now()->subDays(10)->toDateString()));
        $this->assertEquals(30000, $kas->saldoOn(now()->toDateString()));
        // On day -10 the saldo is 100.000, but the later deduction leaves only 30.000 to spare.
        $this->assertEquals(30000, $kas->availableOn(now()->subDays(10)->toDateString()));
        $this->assertEquals(0, $kas->availableOn(now()->subDays(30)->toDateString()));
    }

    public function test_the_summary_ties_opening_in_out_and_closing(): void
    {
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 100000, now()->subDays(20)->toDateString());
        $kas->record('DEDUCTION', -30000, now()->subDays(10)->toDateString());
        $kas->record('TOPUP', 5000, now()->subDays(9)->toDateString());
        $kas->record('DEDUCTION', -1000, now()->subDays(2)->toDateString());

        $sum = $kas->summary(now()->subDays(10)->toDateString(), now()->subDays(5)->toDateString());
        $this->assertEquals([100000, 5000, 30000, 75000, 74000], [$sum['opening'], $sum['in'], $sum['out'], $sum['closing'], $sum['current']]);
        $this->assertEquals($sum['opening'] + $sum['in'] - $sum['out'], $sum['closing']);

        $all = $kas->summary(null, null);
        $this->assertEquals([0, 105000, 31000, 74000], [$all['opening'], $all['in'], $all['out'], $all['closing']]);
    }

    /** [saldo_before, saldo_after] per row in date order; each before must equal the previous after. */
    private function chain(): array
    {
        $rows = DB::table('t_kas_besar')->orderBy('transaction_date')->orderBy('id')->get()
            ->map(fn ($r) => [(int) $r->saldo_before, (int) $r->saldo_after])->all();
        foreach ($rows as $i => $row) {
            $this->assertSame($i ? $rows[$i - 1][1] : 0, $row[0], 'chain broken at row '.$i);
        }

        return $rows;
    }
}
