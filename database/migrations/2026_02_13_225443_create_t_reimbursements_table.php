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
        Schema::create('t_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code', 50)->unique()->comment('Reimbursement code: REIMB-YYYYMM-XXX');
            $table->string('reimbursement_name', 255)->comment('Free text name of reimbursement');
            $table->decimal('amount', 15, 2)->comment('Reimbursement amount in IDR');
            $table->text('description')->nullable()->comment('Additional description from staff');
            $table->string('status', 20)->default('PENDING')->comment('PENDING, APPROVED, REJECTED');
            $table->text('rejection_reason')->nullable()->comment('Reason for rejection');

            // Staff who created the reimbursement
            $table->unsignedBigInteger('created_by')->comment('Staff user ID');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');

            // Budget assignment (only filled after approval)
            $table->unsignedBigInteger('f_budget_category_id')->nullable()->comment('Budget ID assigned by admin');
            $table->foreign('f_budget_category_id')->references('id')->on('m_budgeds')->onDelete('restrict');

            // Admin who approved/rejected
            $table->unsignedBigInteger('reviewed_by')->nullable()->comment('Admin user ID who reviewed');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('restrict');
            $table->timestamp('reviewed_at')->nullable()->comment('Review timestamp');

            // Company code for multi-tenancy
            $table->string('company_code', 20);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_code');
            $table->index('status');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_reimbursements');
    }
};
