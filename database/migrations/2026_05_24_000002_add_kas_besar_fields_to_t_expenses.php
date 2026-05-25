<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->string('payment_method_type', 20)->nullable()->after('f_payment_method_id'); // kas_tunai or transfer
            $table->string('transfer_bank', 20)->nullable()->after('payment_method_type'); // BCA, Mandiri, BTPN
            $table->unsignedBigInteger('f_kas_besar_id')->nullable()->after('transfer_bank');
        });
    }

    public function down(): void
    {
        Schema::table('t_expenses', function (Blueprint $table) {
            $table->dropColumn(['payment_method_type', 'transfer_bank', 'f_kas_besar_id']);
        });
    }
};
