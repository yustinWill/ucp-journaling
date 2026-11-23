<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BudgetPeriodEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('m_budget_periods', function (Blueprint $table) {
            $table->id();
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->string('type');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
        Schema::create('m_budget_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
        });
        Schema::create('m_budgeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_budget_period_id');
            $table->unsignedBigInteger('f_budget_category_id')->nullable();
            $table->string('budged_name');
            $table->decimal('budged_limit_value', 15, 2);
            $table->decimal('budged_remaining_value', 15, 2);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::create('t_expenses', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
        });
        Schema::create('t_budged_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budged_history_id');
            $table->unsignedBigInteger('expense_id');
        });

        DB::table('m_budget_periods')->insert(['id' => 12, 'period_start_date' => '2026-11-01', 'period_end_date' => '2026-11-30', 'type' => 'kingdom']);
        DB::table('m_budget_categories')->insert([['id' => 5, 'category_name' => 'Kategori A'], ['id' => 6, 'category_name' => 'Kategori B']]);
        DB::table('m_budgeds')->insert(['id' => 27, 'f_budget_period_id' => 12, 'f_budget_category_id' => 5, 'budged_name' => 'Kategori A', 'budged_limit_value' => 1000000, 'budged_remaining_value' => 950000]);
        DB::table('t_expenses')->insert(['id' => 30, 'transaction_date' => '2026-11-17']);
        DB::table('t_budged_expenses')->insert(['budged_history_id' => 27, 'expense_id' => 30]);

        // Route permissions are covered by PermissionRoutesTest; here only the save is under test.
        $this->withoutMiddleware();
    }

    protected function tearDown(): void
    {
        foreach (['m_budget_periods', 'm_budget_categories', 'm_budgeds', 't_expenses', 't_budged_expenses'] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    private function save(array $input)
    {
        return $this->postJson(route('master_data.budged.update-period-budgets'), $input + [
            'period_id' => 12,
            'type' => 'kingdom',
            'budgets' => [['id' => 27, 'f_budget_category_id' => 5, 'budged_limit_value' => 1000000, 'budged_remaining_value' => 950000]],
        ]);
    }

    public function test_a_new_end_date_is_saved(): void
    {
        $this->save(['period_end_date' => '2026-12-15'])->assertOk();
        $this->assertSame('2026-12-15', substr(DB::table('m_budget_periods')->value('period_end_date'), 0, 10));

        $this->save(['period_end_date' => '2026-11-20'])->assertOk();
        $this->assertSame('2026-11-20', substr(DB::table('m_budget_periods')->value('period_end_date'), 0, 10));
    }

    public function test_the_end_date_cannot_move_before_the_start_or_a_charged_expense(): void
    {
        $this->assertStringContainsString('sebelum tanggal mulai', $this->save(['period_end_date' => '2026-10-31'])->assertStatus(400)->json('message'));
        $this->assertStringContainsString('sebelum 17/11/2026', $this->save(['period_end_date' => '2026-11-16'])->assertStatus(400)->json('message'));

        $this->assertSame('2026-11-30', substr(DB::table('m_budget_periods')->value('period_end_date'), 0, 10));
        $this->save(['period_end_date' => '2026-11-17'])->assertOk();
    }

    public function test_a_limit_change_keeps_what_was_used_and_a_new_category_row_saves(): void
    {
        $this->postJson(route('master_data.budged.update-period-budgets'), [
            'period_id' => 12,
            'type' => 'kingdom',
            'budgets' => [
                ['id' => 27, 'f_budget_category_id' => 5, 'budged_limit_value' => 2000000, 'budged_remaining_value' => 950000],
                ['f_budget_category_id' => 6, 'budged_limit_value' => 300000],
            ],
        ])->assertOk();

        $this->assertEquals(1950000, DB::table('m_budgeds')->where('id', 27)->value('budged_remaining_value'));
        $new = DB::table('m_budgeds')->where('f_budget_category_id', 6)->first();
        $this->assertSame('Kategori B', $new->budged_name);
        $this->assertEquals(300000, $new->budged_remaining_value);
        $this->assertSame('2026-11-30', substr(DB::table('m_budget_periods')->value('period_end_date'), 0, 10));
    }
}
