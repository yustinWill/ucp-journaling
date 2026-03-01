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
            $table->decimal('recipe_qty', 15, 4)->nullable()->default(null)->after('bundle_batch_qty')
                ->comment('Number of recipes/batches that produce bundle_batch_qty (only for bundle type).');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_products', function (Blueprint $table) {
            $table->dropColumn('recipe_qty');
        });
    }
};
