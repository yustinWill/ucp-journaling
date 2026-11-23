<?php

namespace Tests\Feature;

use App\Domains\Expense\Actions\PaymentExpenseAction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ExpenseNumberOnSaveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
            $table->string('company_code');
            $table->string('transaction_no');
            $table->string('payment_method_type');
            $table->unsignedBigInteger('f_kas_besar_id')->nullable();
            $table->decimal('total_remaining_amount', 15, 2);
            $table->decimal('total_paid_amount', 15, 2)->default(0);
        });
        Schema::create('t_expense_payments', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->unsignedBigInteger('f_expense_id');
            $table->string('transaction_no');
            $table->date('transaction_date');
            $table->unsignedBigInteger('f_receiver_customer_id')->nullable();
            $table->string('f_payment_coa_code')->nullable();
            foreach (['description', 'memo', 'f_tag_codes', 'attachment'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['remaining_amount', 'payment_amount', 'latest_remaining_amount'] as $column) {
                $table->decimal($column, 15, 2);
            }
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
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

        foreach (['expense' => 'EXP', 'expense_payment' => 'EXPP', 'kas_besar' => 'KB'] as $module => $initial) {
            DB::table('m_transaction_numbers')->insert([
                'module_name' => $module, 'module_initial' => $initial, 'format_template' => $initial.'-{YEAR}-{COUNTER}',
                'current_counter' => 5, 'counter_padding' => 3, 'reset_frequency' => 'never', 'status' => 'active',
            ]);
        }

        $this->withoutMiddleware();
        session(['user_company_code' => '001']);
    }

    protected function tearDown(): void
    {
        foreach (['m_transaction_numbers', 't_expenses', 't_expense_payments', 't_kas_besar'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    private function counter(string $module): int
    {
        return (int) DB::table('m_transaction_numbers')->where('module_name', $module)->value('current_counter');
    }

    private function payment(string $method, array $extra): array
    {
        $id = DB::table('t_expenses')->insertGetId([
            'company_code' => '001', 'transaction_no' => 'EXP-2026-001', 'payment_method_type' => $method, 'total_remaining_amount' => 1000,
        ]);

        return $extra + [
            'expense_id' => $id, 'transaction_number' => 'EXPP-'.date('Y').'-006', 'transaction_date' => now()->toDateString(),
            'recipient_id' => null, 'payment_coa' => null, 'remaining_amount' => 1000, 'payment_amount' => 1000,
        ];
    }

    public function test_opening_a_form_only_previews_the_number(): void
    {
        $url = route('master_data.transaction_number.generate_number', 'expense');

        $this->postJson($url, ['preview' => 1])->assertJson(['number' => 'EXP-'.date('Y').'-006']);
        $this->postJson($url, ['preview' => 1])->assertJson(['number' => 'EXP-'.date('Y').'-006']);
        $this->assertSame(5, $this->counter('expense'));

        $this->postJson($url)->assertJson(['number' => 'EXP-'.date('Y').'-006']);
        $this->assertSame(6, $this->counter('expense'));
    }

    public function test_an_untouched_number_is_taken_on_save(): void
    {
        DB::table('m_transaction_numbers')->where('module_name', 'expense_payment')->update(['current_counter' => 9]);

        app(PaymentExpenseAction::class)->execute($this->payment('transfer', ['auto_number' => true]));

        $this->assertSame('EXPP-'.date('Y').'-010', DB::table('t_expense_payments')->value('transaction_no'));
        $this->assertSame(10, $this->counter('expense_payment'));
    }

    public function test_a_typed_number_is_kept_and_takes_nothing(): void
    {
        app(PaymentExpenseAction::class)->execute($this->payment('transfer', ['transaction_number' => 'MANUAL-1']));

        $this->assertSame('MANUAL-1', DB::table('t_expense_payments')->value('transaction_no'));
        $this->assertSame(5, $this->counter('expense_payment'));
    }

    public function test_the_payment_number_setting_is_added_once(): void
    {
        DB::table('m_transaction_numbers')->where('module_name', 'expense_payment')->delete();
        Schema::table('m_transaction_numbers', function (Blueprint $table) {
            $table->string('company_code')->nullable();
            $table->string('module_display_name')->nullable();
        });
        DB::table('m_transaction_numbers')->where('module_name', 'expense')->update(['company_code' => 'UCP']);
        $migration = require database_path('migrations/2026_11_23_000001_seed_expense_payment_transaction_number.php');

        $migration->up();
        $migration->up();

        $rows = DB::table('m_transaction_numbers')->where('module_name', 'expense_payment')->get();
        $this->assertCount(1, $rows);
        $this->assertSame('UCP', $rows[0]->company_code);
        $this->postJson(route('master_data.transaction_number.generate_number', 'expense_payment'), ['preview' => 1])
            ->assertJson(['number' => 'EP-'.now()->format('ymd').'-00001']);
    }

    public function test_a_failed_save_takes_no_number(): void
    {
        // Kas/Tunai with an empty Kas Besar: the deduction is refused and everything rolls back.
        try {
            app(PaymentExpenseAction::class)->execute($this->payment('kas_tunai', ['auto_number' => true]));
            $this->fail('The payment should have been refused.');
        } catch (ValidationException) {
        }

        $this->assertSame(5, $this->counter('expense_payment'));
        $this->assertSame(0, DB::table('t_expense_payments')->count());
    }
}
