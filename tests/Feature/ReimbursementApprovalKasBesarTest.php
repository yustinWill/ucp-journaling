<?php

namespace Tests\Feature;

use App\Services\KasBesarService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReimbursementApprovalKasBesarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
        });
        Schema::create('t_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code');
            $table->string('reimbursement_name');
            $table->date('reimbursement_date')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('status');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('f_budget_category_id')->nullable();
            $table->string('payment_method_type')->nullable();
            $table->string('transfer_bank')->nullable();
            $table->unsignedBigInteger('f_kas_besar_id')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('company_code');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('m_budgeds', function (Blueprint $table) {
            $table->id();
            $table->decimal('budged_remaining_value', 15, 2);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
        Schema::create('t_budged_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budged_history_id');
            $table->unsignedBigInteger('reimbursement_id');
            $table->timestamps();
        });
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
        Schema::create('t_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');
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

        DB::table('users')->insert(['id' => 1, 'name' => 'Staff']);
        DB::table('m_budgeds')->insert(['id' => 5, 'budged_remaining_value' => 1000000]);
        DB::table('m_transaction_numbers')->insert([
            'module_name' => 'kas_besar', 'module_initial' => 'KB', 'format_template' => 'KB-{YEAR}-{MM}-{COUNTER}',
            'current_counter' => 0, 'counter_padding' => 4, 'reset_frequency' => 'monthly',
            'last_reset_date' => now()->toDateString(), 'status' => 'active',
        ]);
        DB::table('t_reimbursements')->insert([
            'id' => 9, 'transaction_code' => 'RMB-0009', 'reimbursement_name' => 'Bensin', 'amount' => 300000,
            'status' => 'PENDING', 'created_by' => 1, 'company_code' => '001', 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Route permissions are covered by PermissionRoutesTest; here only the approval is under test.
        $this->withoutMiddleware();
        $this->withSession(['user_company_code' => '001']);
        session(['user_company_code' => '001']);
    }

    protected function tearDown(): void
    {
        foreach (['t_reimbursements', 'm_budgeds', 't_budged_reimbursements', 't_kas_besar', 't_expenses', 'm_transaction_numbers', 'users'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    private function approve(array $input)
    {
        return $this->postJson(route('main_transaction.reimbursement_approval.approve', 9), $input + ['budget_category_id' => 5]);
    }

    public function test_kas_tunai_approval_deducts_kas_besar_and_links_the_row(): void
    {
        app(KasBesarService::class)->record('TOPUP', 500000, now()->toDateString());

        $this->approve(['payment_method_type' => 'kas_tunai'])->assertOk();

        $reimbursement = DB::table('t_reimbursements')->find(9);
        $kas = DB::table('t_kas_besar')->find($reimbursement->f_kas_besar_id);
        $this->assertSame('APPROVED', $reimbursement->status);
        $this->assertSame('kas_tunai', $reimbursement->payment_method_type);
        $this->assertEquals(-300000, $kas->topup_amount);
        $this->assertEquals(200000, $kas->saldo_after);
        $this->assertSame('Reimbursement: RMB-0009', $kas->notes);
        $this->assertEquals(700000, DB::table('m_budgeds')->value('budged_remaining_value'));

        $row = collect($this->getJson(route('main_transaction.kas_besar.index_data_table', ['search_transaction_no' => 'RMB-0009']))->json('data'))->sole();
        $this->assertSame('RMB-0009', $row['reimbursement_code']);
    }

    public function test_kas_tunai_approval_is_refused_when_kas_besar_is_short_and_changes_nothing(): void
    {
        app(KasBesarService::class)->record('TOPUP', 100000, now()->toDateString());

        $response = $this->approve(['payment_method_type' => 'kas_tunai'])->assertStatus(400);

        $this->assertStringContainsString('Saldo Kas Besar tidak mencukupi', $response->getContent());
        $this->assertSame('PENDING', DB::table('t_reimbursements')->value('status'));
        $this->assertEquals(1000000, DB::table('m_budgeds')->value('budged_remaining_value'));
        $this->assertSame(0, DB::table('t_budged_reimbursements')->count());
        $this->assertSame(1, DB::table('t_kas_besar')->count());
        $this->assertEquals(1, DB::table('m_transaction_numbers')->value('current_counter'));
    }

    public function test_transfer_approval_needs_a_bank_and_leaves_kas_besar_alone(): void
    {
        $this->assertStringContainsString('Bank transfer wajib dipilih.', $this->approve(['payment_method_type' => 'transfer'])->assertStatus(400)->getContent());
        $this->assertStringContainsString('Sumber pembayaran wajib dipilih.', $this->approve([])->assertStatus(400)->getContent());

        $this->approve(['payment_method_type' => 'transfer', 'transfer_bank' => 'BCA'])->assertOk();

        $reimbursement = DB::table('t_reimbursements')->find(9);
        $this->assertSame('APPROVED', $reimbursement->status);
        $this->assertSame('BCA', $reimbursement->transfer_bank);
        $this->assertNull($reimbursement->f_kas_besar_id);
        $this->assertSame(0, DB::table('t_kas_besar')->count());
        $this->assertEquals(700000, DB::table('m_budgeds')->value('budged_remaining_value'));
    }
}
