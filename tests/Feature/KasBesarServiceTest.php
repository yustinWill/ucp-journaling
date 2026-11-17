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
