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
        // Get the payment term module ID
        $paymentTermModule = DB::table('modules')->where('name', 'master_data_payment_term')->first();

        if (!$paymentTermModule) {
            throw new Exception('Payment term module not found. Please ensure the payment term module is seeded first.');
        }

        $actions = [
            'read',
            'write',
            'delete',
            'approval'
        ];

        $permissions = [];

        // Payment term module permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $paymentTermModule->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }

        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the payment term module ID
        $paymentTermModule = DB::table('modules')->where('name', 'master_data_payment_term')->first();

        if ($paymentTermModule) {
            DB::table('permissions')->where('module_id', $paymentTermModule->id)->delete();
        }
    }
};