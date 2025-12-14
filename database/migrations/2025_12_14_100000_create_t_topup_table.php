<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_topup', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_code', 10);
            $table->string('topup_number', 50)->unique()->comment('e.g., TU-202512-001');
            $table->date('topup_date');
            $table->enum('entity_type', ['KINGDOM', 'OWNER'])->comment('Entity yang melakukan pembayaran');
            $table->decimal('topup_amount', 15, 2)->default(0)->comment('Total amount yang dibayarkan');
            $table->decimal('allocated_amount', 15, 2)->default(0)->comment('Total yang sudah dialokasikan ke SO');
            $table->decimal('remaining_balance', 15, 2)->default(0)->comment('Sisa deposit yang belum dialokasikan');
            $table->text('notes')->nullable();
            $table->string('attachment_path', 255)->nullable()->comment('Bukti transfer');
            $table->enum('status', ['OPEN', 'PARTIAL', 'PAID'])->default('OPEN')->comment('OPEN=belum dialokasikan, PARTIAL=sebagian dialokasikan, PAID=fully allocated');
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Indexes
            $table->index('company_code');
            $table->index('topup_number');
            $table->index('topup_date');
            $table->index('entity_type');
            $table->index('status');
            $table->index(['company_code', 'entity_type', 'topup_date']);

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_topup');
    }
};
