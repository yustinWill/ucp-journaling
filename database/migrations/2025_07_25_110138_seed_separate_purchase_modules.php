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
        // Remove the old combined purchase module
        DB::table('permissions')->whereIn('module_id', function($query) {
            $query->select('id')->from('modules')->where('name', 'main_transaction_purchase');
        })->delete();
        
        DB::table('modules')->where('name', 'main_transaction_purchase')->delete();

        // Add the three separate purchase modules
        $modules = [
            [
                'name' => 'main_transaction_purchase_order',
                'slug' => 'main-transaction-purchase-order',
                'description' => 'Main Transaction Purchase Order',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'main_transaction_purchase_delivery',
                'slug' => 'main-transaction-purchase-delivery',
                'description' => 'Main Transaction Purchase Delivery',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'main_transaction_purchase_invoice',
                'slug' => 'main-transaction-purchase-invoice',
                'description' => 'Main Transaction Purchase Invoice',
                'is_active' => true,
                'created_at' => now(),
            ]
        ];

        DB::table('modules')->insert($modules);

        // Get the new module IDs
        $purchaseOrder = DB::table('modules')->where('name', 'main_transaction_purchase_order')->first();
        $purchaseDelivery = DB::table('modules')->where('name', 'main_transaction_purchase_delivery')->first();
        $purchaseInvoice = DB::table('modules')->where('name', 'main_transaction_purchase_invoice')->first();

        if (!$purchaseOrder || !$purchaseDelivery || !$purchaseInvoice) {
            throw new Exception('Failed to create separate purchase modules.');
        }

        $actions = [
            'read',
            'write',
            'delete',
            'approval'
        ];

        $permissions = [];

        // Purchase Order permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $purchaseOrder->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }

        // Purchase Delivery permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $purchaseDelivery->id,
                'permission_type' => $action,
                'created_at' => now()
            ];
        }

        // Purchase Invoice permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $purchaseInvoice->id,
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
        // Remove separate modules and restore combined one
        $moduleNames = [
            'main_transaction_purchase_order',
            'main_transaction_purchase_delivery', 
            'main_transaction_purchase_invoice'
        ];

        DB::table('permissions')->whereIn('module_id', function($query) use ($moduleNames) {
            $query->select('id')->from('modules')->whereIn('name', $moduleNames);
        })->delete();
        
        DB::table('modules')->whereIn('name', $moduleNames)->delete();

        // Restore original combined module
        $module = [
            'name' => 'main_transaction_purchase',
            'slug' => 'main-transaction-purchase',
            'description' => 'Main Transaction Purchase',
            'is_active' => true,
            'created_at' => now(),
        ];

        DB::table('modules')->insert($module);
    }
};