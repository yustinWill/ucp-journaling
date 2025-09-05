<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $availableColors = [
            ['code' => 'WEIGHT', 'name' => 'WEIGHT - Berat'],
            ['code' => 'VOLUME', 'name' => 'VOLUME - Volume'],
            ['code' => 'LENGTH', 'name' => 'LENGTH - Panjang'],
            ['code' => 'PIECE', 'name' => 'PIECE - Satuan'],
        ];
        DB::table('m_unit_types')->insert($availableColors);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
