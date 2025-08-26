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
        // Insert Production module into modules table (if it doesn't exist)
        $existingModule = DB::table('modules')->where('name', 'main_transaction_production')->first();
        
        if (!$existingModule) {
            DB::table('modules')->insert([
                [
                    'name' => 'main_transaction_production',
                    'slug' => 'main-transaction-production',
                    'description' => 'Production Management - Bundle product manufacturing with stock tracking',
                    'view_order_no' => 6,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Get the module ID
        $moduleId = DB::table('modules')->where('name', 'main_transaction_production')->value('id');

        // Clear existing permissions for this module first
        DB::table('permissions')->where('module_id', $moduleId)->delete();

        // Insert Production permissions into permissions table
        // Note: Using only valid ENUM values - reversal and export will need custom implementation
        $permissionTypes = ['read', 'write', 'delete', 'approval']; // approval can be used for reversal
        
        foreach ($permissionTypes as $type) {
            DB::table('permissions')->insert([
                'module_id' => $moduleId,
                'permission_type' => $type,
                'permission_name' => $type === 'approval' ? 'production_reversal' : '', // Custom naming for approval as reversal
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions first
        $moduleId = DB::table('modules')->where('name', 'main_transaction_production')->value('id');
        if ($moduleId) {
            DB::table('permissions')->where('module_id', $moduleId)->delete();
        }

        // Remove module
        DB::table('modules')->where('name', 'main_transaction_production')->delete();
    }
};