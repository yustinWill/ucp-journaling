<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RecalculateBudgetRemainingValuesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('m_budgeds', function (Blueprint $table) {
            $table->id();
            $table->string('budged_name');
            $table->decimal('budged_limit_value', 15, 2);
            $table->decimal('budged_remaining_value', 15, 2);
        });
        Schema::create('t_expenses', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_amount', 15, 2);
        });
        Schema::create('t_budged_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budged_history_id');
            $table->unsignedBigInteger('expense_id');
        });
        Schema::create('t_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
        });
        Schema::create('t_budged_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budged_history_id');
            $table->unsignedBigInteger('reimbursement_id');
        });
    }

    public function test_remaining_subtracts_expenses_and_approved_reimbursements(): void
    {
        DB::table('m_budgeds')->insert(['id' => 1, 'budged_name' => 'Akomodasi', 'budged_limit_value' => 25000000, 'budged_remaining_value' => 0]);
        DB::table('t_expenses')->insert(['id' => 10, 'total_amount' => 5000000]);
        DB::table('t_budged_expenses')->insert(['budged_history_id' => 1, 'expense_id' => 10]);
        DB::table('t_reimbursements')->insert(['id' => 20, 'amount' => 1500000]);
        DB::table('t_budged_reimbursements')->insert(['budged_history_id' => 1, 'reimbursement_id' => 20]);

        $this->artisan('budget:recalculate')->assertSuccessful();

        $this->assertEquals(18500000, (float) DB::table('m_budgeds')->where('id', 1)->value('budged_remaining_value'));
    }
}
