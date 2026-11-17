<?php

namespace Tests\Feature;

use App\Domains\Expense\Services\ExpenseService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ExpenseEditKeepsPaymentsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('t_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('company_code');
            $table->date('transaction_date')->nullable();
            $table->boolean('to_be_paid_later')->default(false);
            foreach (['f_payment_term_id', 'f_payment_method_id', 'f_receiver_customer_id', 'updated_by'] as $column) {
                $table->unsignedBigInteger($column)->nullable();
            }
            foreach (['due_date', 'billing_address', 'description', 'memo', 'f_tag_codes', 'category_type', 'entity_type', 'attachment'] as $column) {
                $table->string($column)->nullable();
            }
            foreach (['subtotal', 'total_tax', 'total_before_discount', 'total_discount', 'total_amount', 'total_remaining_amount', 'total_paid_amount'] as $column) {
                $table->decimal($column, 15, 2)->default(0);
            }
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('t_expense_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('f_expense_id');
            $table->decimal('payment_amount', 15, 2);
        });

        session(['user_company_code' => '001']);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('t_expense_payments');
        Schema::dropIfExists('t_expenses');

        parent::tearDown();
    }

    public function test_editing_a_paid_expense_keeps_the_paid_amount(): void
    {
        $id = $this->paidExpense(100000);

        app(ExpenseService::class)->update($this->editData(120000), $id);

        $expense = DB::table('t_expenses')->find($id);
        $this->assertEquals(100000, $expense->total_paid_amount);
        $this->assertEquals(20000, $expense->total_remaining_amount);
    }

    public function test_total_cannot_drop_below_what_was_paid(): void
    {
        $id = $this->paidExpense(100000);

        $this->expectException(ValidationException::class);

        app(ExpenseService::class)->update($this->editData(90000), $id);
    }

    private function paidExpense(float $amount): int
    {
        $id = DB::table('t_expenses')->insertGetId([
            'company_code' => '001',
            'total_amount' => $amount,
            'total_paid_amount' => $amount,
        ]);
        DB::table('t_expense_payments')->insert(['f_expense_id' => $id, 'payment_amount' => $amount]);

        return $id;
    }

    private function editData(float $total): array
    {
        return [
            'pay_later' => false,
            'payment_method' => null,
            'payment_terms' => null,
            'due_date' => null,
            'recipient_id' => null,
            'billing_address' => null,
            'transaction_date' => '2026-11-01',
            'subtotal' => $total,
            'tax_amount' => 0,
            'total_before_discount' => $total,
            'discount_amount' => 0,
            'final_total' => $total,
        ];
    }
}
