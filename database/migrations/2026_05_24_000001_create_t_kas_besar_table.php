<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_kas_besar', function (Blueprint $table) {
            $table->id();
            $table->string('company_code', 10);
            $table->string('transaction_number', 50)->unique();
            $table->date('transaction_date');
            $table->decimal('topup_amount', 20, 2); // negative for deductions
            $table->decimal('saldo_before', 20, 2)->default(0);
            $table->decimal('saldo_after', 20, 2)->default(0);
            $table->string('type', 20)->default('TOPUP'); // TOPUP or DEDUCTION
            $table->unsignedBigInteger('f_expense_id')->nullable(); // set when type=DEDUCTION
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('company_code');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_kas_besar');
    }
};
