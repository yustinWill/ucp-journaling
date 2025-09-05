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
        Schema::table('t_purchase_deliveries', function (Blueprint $table) {
            $table->dropForeign('t_purchase_deliveries_f_supplier_id_foreign');

            $table->dropColumn([
                'f_supplier_id',
                'supplier_email',
                'supplier_address',
                'supplier_reference_no',
                'expected_received_date',
                'allow_over_delivery',
            ]);

            $table->string('delivery_number')->after('delivery_date')->default(0);
            $table->string('tracking_number')->after('delivery_number')->default(0);
            $table->string('courier_name')->after('tracking_number');
            $table->string('delivery_status')->after('courier_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
