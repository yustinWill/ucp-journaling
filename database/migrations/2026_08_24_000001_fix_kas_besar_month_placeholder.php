<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $config = DB::table('m_transaction_numbers')
            ->where('module_name', 'kas_besar')
            ->first();

        if ($config && str_contains($config->format_template, '{MONTH}')) {
            DB::table('m_transaction_numbers')
                ->where('id', $config->id)
                ->update([
                    'format_template' => str_replace('{MONTH}', '{MM}', $config->format_template),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $config = DB::table('m_transaction_numbers')
            ->where('module_name', 'kas_besar')
            ->first();

        if ($config && str_contains($config->format_template, '{MM}')) {
            DB::table('m_transaction_numbers')
                ->where('id', $config->id)
                ->update([
                    'format_template' => str_replace('{MM}', '{MONTH}', $config->format_template),
                    'updated_at' => now(),
                ]);
        }
    }
};
