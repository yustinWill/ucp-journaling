<?php

namespace Tests\Feature;

use App\Services\KasBesarService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KasBesarControllerTest extends TestCase
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
        Schema::create('t_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');
        });
        DB::table('m_transaction_numbers')->insert([
            'module_name' => 'kas_besar', 'module_initial' => 'KB', 'format_template' => 'KB-{YEAR}-{MM}-{COUNTER}',
            'current_counter' => 0, 'counter_padding' => 4, 'reset_frequency' => 'monthly',
            'last_reset_date' => now()->toDateString(), 'status' => 'active',
        ]);

        // Route permissions are covered by PermissionRoutesTest; here only the controller is under test.
        $this->withoutMiddleware();
        $this->withSession(['user_company_code' => '001']);
        session(['user_company_code' => '001']);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('t_kas_besar');
        Schema::dropIfExists('m_transaction_numbers');
        Schema::dropIfExists('t_expenses');

        parent::tearDown();
    }

    public function test_a_top_up_is_saved_with_its_number(): void
    {
        $this->postJson(route('main_transaction.kas_besar.store'), ['transaction_date' => now()->toDateString(), 'topup_amount' => 50000])
            ->assertOk()
            ->assertJson(['success' => true, 'transaction_number' => 'KB-'.now()->format('Y-m').'-0001']);
    }

    public function test_bad_top_up_input_is_refused_with_a_message(): void
    {
        $today = now()->toDateString();
        $cases = [
            'tomorrow' => [['transaction_date' => now()->addDay()->toDateString(), 'topup_amount' => 1000], 'Tanggal transaksi tidak boleh lebih dari hari ini.'],
            'no date' => [['topup_amount' => 1000], 'Tanggal transaksi wajib diisi.'],
            'not a date' => [['transaction_date' => 'kemarin', 'topup_amount' => 1000], null],
            'zero' => [['transaction_date' => $today, 'topup_amount' => 0], 'Jumlah topup harus lebih dari 0.'],
            'negative' => [['transaction_date' => $today, 'topup_amount' => -5000], 'Jumlah topup harus lebih dari 0.'],
            'text' => [['transaction_date' => $today, 'topup_amount' => 'sejuta'], 'Jumlah topup harus berupa angka.'],
            'too large' => [['transaction_date' => $today, 'topup_amount' => '99999999999999'], 'Jumlah topup terlalu besar.'],
            'no amount' => [['transaction_date' => $today], 'Jumlah topup wajib diisi.'],
        ];

        foreach ($cases as $name => [$input, $message]) {
            $response = $this->postJson(route('main_transaction.kas_besar.store'), $input)->assertStatus(422);
            if ($message) {
                $this->assertContains($message, collect($response->json('errors'))->flatten()->all(), $name);
            }
        }
        $this->assertSame(0, DB::table('t_kas_besar')->count());
        $this->assertEquals(0, DB::table('m_transaction_numbers')->value('current_counter'));
    }

    public function test_a_broken_number_setting_answers_with_its_reason(): void
    {
        DB::table('m_transaction_numbers')->update(['status' => 'inactive']);

        $this->postJson(route('main_transaction.kas_besar.store'), ['transaction_date' => now()->toDateString(), 'topup_amount' => 1000])
            ->assertStatus(500)
            ->assertJson(['success' => false, 'message' => 'Gagal menyimpan: Pengaturan nomor transaksi Kas Besar tidak ditemukan atau tidak aktif.']);
        $this->getJson(route('main_transaction.kas_besar.generate_transaction_number'))
            ->assertStatus(500)
            ->assertJson(['success' => false]);
    }

    public function test_the_form_previews_follow_the_date(): void
    {
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 100000, now()->subDays(40)->toDateString());
        $kas->record('DEDUCTION', -80000, now()->toDateString());
        $lastMonth = now()->subMonthNoOverflow();

        $this->getJson(route('main_transaction.kas_besar.generate_transaction_number', ['date' => $lastMonth->toDateString()]))
            ->assertJson(['transaction_number' => 'KB-'.$lastMonth->format('Y-m').'-000'.(DB::table('t_kas_besar')->where('transaction_number', 'like', 'KB-'.$lastMonth->format('Y-m').'%')->count() + 1)]);
        $this->getJson(route('main_transaction.kas_besar.get_latest_saldo', ['date' => now()->subDays(20)->toDateString()]))
            ->assertJson(['saldo_after' => 100000, 'available' => 20000]);
        $this->getJson(route('main_transaction.kas_besar.get_latest_saldo'))
            ->assertJson(['date' => now()->toDateString(), 'saldo_after' => 20000, 'available' => 20000]);
        $this->getJson(route('main_transaction.kas_besar.get_latest_saldo', ['date' => 'bukan-tanggal']))
            ->assertJson(['date' => now()->toDateString()]);
    }

    public function test_the_list_carries_the_period_summary_and_finds_expense_numbers(): void
    {
        DB::table('t_expenses')->insert(['id' => 7, 'transaction_no' => 'EXP-2026-007']);
        $kas = app(KasBesarService::class);
        $kas->record('TOPUP', 100000, now()->subDays(20)->toDateString(), 'Setoran owner');
        $kas->record('DEDUCTION', -30000, now()->subDays(10)->toDateString(), 'Pengeluaran: EXP-2026-007', 7);

        $json = $this->getJson(route('main_transaction.kas_besar.index_data_table', ['start_date' => now()->subDays(15)->toDateString()]))->assertOk()->json();
        $this->assertEquals(['opening' => 100000, 'in' => 0, 'out' => 30000, 'closing' => 70000, 'current' => 70000], $json['summary']);
        $this->assertSame(1, $json['recordsFiltered']);
        $this->assertSame('EXP-2026-007', $json['data'][0]['expense_transaction_no']);

        $found = $this->getJson(route('main_transaction.kas_besar.index_data_table', ['search_transaction_no' => 'EXP-2026-007']))->json();
        $this->assertSame(1, $found['recordsFiltered']);
    }
}
