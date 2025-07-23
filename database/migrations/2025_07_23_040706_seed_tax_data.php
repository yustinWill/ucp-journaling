<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $taxes = [
            [
                'code' => 'PPN11',
                'name' => 'PPN 11%',
                'rate' => 11.00,
                'type' => 'PERCENTAGE',
                'description' => 'Pajak Pertambahan Nilai standar Indonesia',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PPN0',
                'name' => 'PPN 0%',
                'rate' => 0.00,
                'type' => 'PERCENTAGE',
                'description' => 'Pajak Pertambahan Nilai untuk barang tertentu',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PPH21',
                'name' => 'PPh Pasal 21',
                'rate' => 5.00,
                'type' => 'PERCENTAGE',
                'description' => 'Pajak Penghasilan atas gaji dan upah',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'PPH23',
                'name' => 'PPh Pasal 23',
                'rate' => 2.00,
                'type' => 'PERCENTAGE',
                'description' => 'Pajak Penghasilan atas jasa profesional',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'NOTAX',
                'name' => 'Tanpa Pajak',
                'rate' => 0.00,
                'type' => 'PERCENTAGE',
                'description' => 'Untuk transaksi yang tidak dikenai pajak',
                'status' => 'INACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('m_taxes')->insert($taxes);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('m_taxes')->truncate();
    }
};