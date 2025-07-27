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
        // Get existing module IDs
        $purchase = DB::table('modules')->where('name', 'main_transaction_purchase')->first();
        $sales = DB::table('modules')->where('name', 'main_transaction_sales')->first();
        $stock_adjustment = DB::table('modules')->where('name', 'main_transaction_stock_adjustment')->first();

        if (!$purchase || !$sales || !$stock_adjustment) {
            throw new Exception('Required transaction modules not found. Please ensure modules are seeded first.');
        }

        $actions = [
            'read',
            'write',
            'delete',
            'approval'
        ];

        $permissions = [];

        // Purchase module permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $purchase->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }

        // Sales module permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $sales->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }

        // Stock Adjustment module permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $stock_adjustment->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }


        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};