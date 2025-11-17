<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_sales_invoice_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_number')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('account_code')->nullable();
            $table->string('payment_method')->nullable();
            $table->json('f_tags_code')->nullable();
            $table->string('internal_memo')->nullable();
            $table->string('sales_invoice_id')->nullable();
            $table->decimal('payment_value', 15, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('deduction_account_code')->nullable();
            $table->decimal('deduction_value', 15, 2)->nullable();
            $table->string('attachment')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('created_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_sales_invoice_payment');
    }
};
