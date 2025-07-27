<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTerms = [
            [
                'code' => 'NET_15',
                'name' => 'Net 15',
                'description' => 'Payment due within 15 days of invoice date',
                'days' => 15,
                'type' => 'net',
                'status' => 'active',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'NET_30',
                'name' => 'Net 30',
                'description' => 'Payment due within 30 days of invoice date',
                'days' => 30,
                'type' => 'net',
                'status' => 'active',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'NET_45',
                'name' => 'Net 45',
                'description' => 'Payment due within 45 days of invoice date',
                'days' => 45,
                'type' => 'net',
                'status' => 'active',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'NET_60',
                'name' => 'Net 60',
                'description' => 'Payment due within 60 days of invoice date',
                'days' => 60,
                'type' => 'net',
                'status' => 'active',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'IMMEDIATE',
                'name' => 'Immediate',
                'description' => 'Payment due immediately upon receipt',
                'days' => 0,
                'type' => 'immediate',
                'status' => 'active',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COD',
                'name' => 'Cash on Delivery',
                'description' => 'Payment due upon delivery of goods or services',
                'days' => 0,
                'type' => 'cod',
                'status' => 'active',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CUSTOM',
                'name' => 'Custom',
                'description' => 'Custom payment terms with manual date entry',
                'days' => 0,
                'type' => 'custom',
                'status' => 'active',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('m_payment_terms')->insert($paymentTerms);
    }
}