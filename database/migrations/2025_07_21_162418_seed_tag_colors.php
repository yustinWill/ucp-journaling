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
            ['code' => 'primary', 'name' => 'PRIMARY - Primary (Ungu)', 'preview' => 'bg-label-primary'],
            ['code' => 'secondary', 'name' => 'SECONDARY - Secondary (Abu-abu)', 'preview' => 'bg-label-secondary'],
            ['code' => 'success', 'name' => 'SUCCESS - Success (Hijau)', 'preview' => 'bg-label-success'],
            ['code' => 'info', 'name' => 'INFO - Info (Biru)', 'preview' => 'bg-label-info'],
            ['code' => 'warning', 'name' => 'WARNING - Warning (Kuning)', 'preview' => 'bg-label-warning'],
            ['code' => 'danger', 'name' => 'DANGER - Danger (Merah)', 'preview' => 'bg-label-danger'],
            ['code' => 'dark', 'name' => 'DARK - Dark (Hitam)', 'preview' => 'bg-label-dark'],
        ];
        DB::table('m_tag_colors')->insert($availableColors);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
