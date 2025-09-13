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
        // Remove the old combined sales module
        DB::table('permissions')->whereIn('module_id', function ($query) {
            $query->select('id')->from('modules')->where('name', 'main_transaction_sales');
        })->delete();

        DB::table('modules')->where('name', 'main_transaction_sales')->delete();

        // Add the three separate sales modules
        $modules = [
            [
                'name' => 'main_transaction_sales_order',
                'slug' => 'main-transaction-sales-order',
                'description' => 'Main Transaction Sales Order',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'main_transaction_sales_delivery',
                'slug' => 'main-transaction-sales-delivery',
                'description' => 'Main Transaction Sales Delivery',
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'main_transaction_sales_invoice',
                'slug' => 'main-transaction-sales-invoice',
                'description' => 'Main Transaction Sales Invoice',
                'is_active' => true,
                'created_at' => now(),
            ],
        ];

        // Insert modules only if they don't already exist
        foreach ($modules as $module) {
            DB::table('modules')->insertOrIgnore($module);
        }

        // Get the new module IDs
        $salesOrder = DB::table('modules')->where('name', 'main_transaction_sales_order')->first();
        $salesDelivery = DB::table('modules')->where('name', 'main_transaction_sales_delivery')->first();
        $salesInvoice = DB::table('modules')->where('name', 'main_transaction_sales_invoice')->first();

        if (! $salesOrder || ! $salesDelivery || ! $salesInvoice) {
            throw new Exception('Failed to create separate sales modules.');
        }

        $actions = [
            'read',
            'write',
            'delete',
            'approval',
            'import',
            'export',
        ];

        $permissions = [];

        // Sales Order permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $salesOrder->id,
                'permission_type' => $action,
                'created_at' => now(),
            ];
        }

        // Sales Delivery permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $salesDelivery->id,
                'permission_type' => $action,
                'created_at' => now(),
            ];
        }

        // Sales Invoice permissions
        foreach ($actions as $action) {
            $permissions[] = [
                'module_id' => $salesInvoice->id,
                'permission_type' => $action,
                'created_at' => now(),
            ];
        }

        // Insert permissions only if they don't already exist
        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove separate modules and restore combined one
        $moduleNames = [
            'main_transaction_sales_order',
            'main_transaction_sales_delivery',
            'main_transaction_sales_invoice',
        ];

        DB::table('permissions')->whereIn('module_id', function ($query) use ($moduleNames) {
            $query->select('id')->from('modules')->whereIn('name', $moduleNames);
        })->delete();

        DB::table('modules')->whereIn('name', $moduleNames)->delete();

        // Restore original combined module
        $module = [
            'name' => 'main_transaction_sales',
            'slug' => 'main-transaction-sales',
            'description' => 'Main Transaction Sales',
            'is_active' => true,
            'created_at' => now(),
        ];

        DB::table('modules')->insert($module);
    }
};
