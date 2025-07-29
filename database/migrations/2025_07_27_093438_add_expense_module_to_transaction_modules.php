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
        // Check if expense module already exists
        $existingModule = DB::table('modules')->where('name', 'main_transaction_expense')->first();
        
        if (!$existingModule) {
            // Insert the expense module
            $moduleId = DB::table('modules')->insertGetId([
                'name' => 'main_transaction_expense',
                'slug' => 'main-transaction-expense',
                'description' => 'Main Transaction Expense',
                'is_active' => true,
                'created_at' => now(),
            ]);

            // Create permissions for the expense module
            $actions = ['read', 'write', 'delete', 'approval'];
            $permissions = [];

            foreach ($actions as $action) {
                $permissions[] = [
                    'module_id' => $moduleId,
                    'permission_type' => $action,
                    'created_at' => now()
                ];
            }

            DB::table('permissions')->insert($permissions);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove expense module permissions first
        $expenseModule = DB::table('modules')->where('name', 'main_transaction_expense')->first();
        
        if ($expenseModule) {
            DB::table('permissions')->where('module_id', $expenseModule->id)->delete();
            DB::table('modules')->where('name', 'main_transaction_expense')->delete();
        }
    }
};