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
}
