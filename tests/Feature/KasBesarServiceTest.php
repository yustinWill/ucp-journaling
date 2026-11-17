<?php

namespace Tests\Feature;

use App\Services\KasBesarService;
use App\Services\TransactionNumberService;
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

        $counter = 0;
        $this->mock(TransactionNumberService::class)
            ->shouldReceive('generateNumber')
            ->andReturnUsing(function () use (&$counter) {
                return 'KB-'.++$counter;
            });

        session(['user_company_code' => '001']);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('t_kas_besar');

        parent::tearDown();
    }

    public function test_rows_carry_a_running_saldo(): void
    {
        $kas = app(KasBesarService::class);

        $kas->record('TOPUP', 100000, '2026-11-01');
        $row = $kas->record('DEDUCTION', -30000, '2026-11-02', 'Pengeluaran: EXP-1', 7);

        $deduction = DB::table('t_kas_besar')->find($row['id']);
        $this->assertSame('KB-2', $row['transaction_number']);
        $this->assertEquals(100000, $deduction->saldo_before);
        $this->assertEquals(70000, $deduction->saldo_after);
        $this->assertEquals(7, $deduction->f_expense_id);
    }

    public function test_a_deduction_larger_than_the_saldo_is_refused(): void
    {
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 50000, '2026-11-01');

        try {
            $kas->record('DEDUCTION', -80000, '2026-11-02');
            $this->fail('Expected the deduction to be refused.');
        } catch (ValidationException $e) {
            $this->assertSame(1, DB::table('t_kas_besar')->count());
        }
    }
}
