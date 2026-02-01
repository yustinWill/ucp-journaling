<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            ['code' => 'CASH', 'name' => 'Kas atau Tunai', 'status' => 'ACTIVE'],
            ['code' => 'BANK', 'name' => 'Transfer Bank', 'status' => 'ACTIVE'],
            ['code' => 'Other', 'name' => 'Dll', 'status' => 'ACTIVE'],
        ];

        foreach ($paymentMethods as $method) {
            DB::table('m_payment_methods')->updateOrInsert(
                ['code' => $method['code']],
                array_merge($method, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
