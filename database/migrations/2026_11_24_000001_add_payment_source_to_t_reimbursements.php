<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Where an approved reimbursement was paid from; Kas / Tunai links the Kas Besar row it deducted. */
    public function up(): void
    {
        Schema::table('t_reimbursements', function (Blueprint $table) {
            if (! Schema::hasColumn('t_reimbursements', 'payment_method_type')) {
                $table->string('payment_method_type', 20)->nullable()->after('f_budget_category_id');
            }
            if (! Schema::hasColumn('t_reimbursements', 'transfer_bank')) {
                $table->string('transfer_bank', 20)->nullable()->after('payment_method_type');
            }
            if (! Schema::hasColumn('t_reimbursements', 'f_kas_besar_id')) {
                $table->unsignedBigInteger('f_kas_besar_id')->nullable()->after('transfer_bank');
            }
        });
    }

    public function down(): void
    {
        Schema::table('t_reimbursements', function (Blueprint $table) {
            foreach (['f_kas_besar_id', 'transfer_bank', 'payment_method_type'] as $column) {
                if (Schema::hasColumn('t_reimbursements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
