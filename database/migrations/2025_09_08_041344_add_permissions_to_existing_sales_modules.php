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
        // Get the existing sales module IDs
        $salesOrder = DB::table('modules')->where('name', 'main_transaction_sales_order')->first();
        $salesDelivery = DB::table('modules')->where('name', 'main_transaction_sales_delivery')->first();
        $salesInvoice = DB::table('modules')->where('name', 'main_transaction_sales_invoice')->first();

        if (! $salesOrder || ! $salesDelivery || ! $salesInvoice) {
            throw new Exception('Sales modules not found. Please run the sales modules creation migration first.');
        }

        // Clear any existing permissions for these modules first
        DB::table('permissions')->whereIn('module_id', [$salesOrder->id, $salesDelivery->id, $salesInvoice->id])->delete();

        // Use only the standard permissions that match the ENUM definition
        $actions = [
            'read',
            'write',
            'delete',
            'approval',
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

        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions for the sales modules
        $moduleIds = DB::table('modules')
            ->whereIn('name', [
                'main_transaction_sales_order',
                'main_transaction_sales_delivery',
                'main_transaction_sales_invoice',
            ])
            ->pluck('id');

        DB::table('permissions')->whereIn('module_id', $moduleIds)->delete();
    }
};
