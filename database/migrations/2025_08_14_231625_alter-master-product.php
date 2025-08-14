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
        Schema::table('m_products', function (Blueprint $table) {
            $table->decimal('total_stock', 15, 2)->default(0)->after('internal_memo');
            $table->decimal('qty_available', 15, 2)->default(0)->after('total_stock');
            $table->decimal('average_price', 15, 2)->default(0)->after('qty_available');
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
