<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'FINISHED_GOODS',
                'name' => 'Produk Jadi',
                'type' => 'FINISHED_GOODS',
                'description' => 'Kategori untuk produk RIYO Water yang sudah siap untuk dijual kepada konsumen',
                'status' => 'ACTIVE',
            ],
            [
                'code' => 'RAW_MATERIALS',
                'name' => 'Bahan Baku',
                'type' => 'RAW_MATERIALS',
                'description' => 'Kategori untuk bahan baku produksi air minum',
                'status' => 'ACTIVE',
            ],
            [
                'code' => 'PACKAGING',
                'name' => 'Kemasan',
                'type' => 'PACKAGING',
                'description' => 'Kategori untuk material kemasan dan label produk',
                'status' => 'ACTIVE',
            ],
            [
                'code' => 'MAINTENANCE',
                'name' => 'Perawatan',
                'type' => 'MAINTENANCE',
                'description' => 'Kategori untuk spare part dan material perawatan mesin',
                'status' => 'ACTIVE',
            ],
            [
                'code' => 'CHEMICALS',
                'name' => 'Bahan Kimia',
                'type' => 'RAW_MATERIALS',
                'description' => 'Kategori untuk bahan kimia treatment air',
                'status' => 'ACTIVE',
            ],
            [
                'code' => 'CONSUMABLES',
                'name' => 'Habis Pakai',
                'type' => 'MAINTENANCE',
                'description' => 'Kategori untuk barang habis pakai operasional',
                'status' => 'INACTIVE',
            ],
        ];

        foreach ($categories as $categoryData) {
            DB::table('m_categories')->insert(array_merge($categoryData, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
