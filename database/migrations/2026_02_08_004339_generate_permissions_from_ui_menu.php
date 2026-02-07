<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Generate permissions based on the current UI menu structure
     */
    public function up(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions based on UI menu modules
        $permissions = [
            // Dashboard & Reporting
            'dashboard.view',
            'financial_recording.view',

            // Sales Transactions
            'sales_order.view',
            'sales_order.create',
            'sales_order.edit',
            'sales_order.delete',
            
            'sales_delivery.view',
            'sales_delivery.create',
            'sales_delivery.edit',
            'sales_delivery.delete',
            
            'sales_invoice.view',
            'sales_invoice.create',
            'sales_invoice.edit',
            'sales_invoice.delete',

            // Purchase Transactions
            'purchase_order.view',
            'purchase_order.create',
            'purchase_order.edit',
            'purchase_order.delete',
            
            'purchase_delivery.view',
            'purchase_delivery.create',
            'purchase_delivery.edit',
            'purchase_delivery.delete',
            
            'purchase_invoice.view',
            'purchase_invoice.create',
            'purchase_invoice.edit',
            'purchase_invoice.delete',

            // Expense Management
            'expense.view',
            'expense.create',
            'expense.edit',
            'expense.delete',

            // Stock Management
            'stock_adjustment.view',
            'stock_adjustment.create',
            'stock_adjustment.edit',
            'stock_adjustment.delete',
            
            'return_management.view',
            'return_management.create',
            'return_management.edit',
            'return_management.delete',
            
            'stock_opname.view',
            'stock_opname.create',
            'stock_opname.edit',
            'stock_opname.delete',

            // Master Data - Products
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',
            'product.import',
            'product.export',

            // Master Data - Contacts
            'contact.view',
            'contact.create',
            'contact.edit',
            'contact.delete',
            'contact.import',
            'contact.export',

            // Master Data - Warehouse
            'warehouse.view',
            'warehouse.create',
            'warehouse.edit',
            'warehouse.delete',
            'warehouse.stock_transfer',
            'warehouse.import',
            'warehouse.export',

            // Production & Packing
            'production.view',
            'production.create',
            'production.edit',
            'production.delete',
            
            'packing.view',
            'packing.create',
            'packing.edit',
            'packing.delete',

            // Financial Management
            'topup.view',
            'topup.create',
            'topup.edit',
            'topup.delete',

            // Budget Management
            'budget.view',
            'budget.create',
            'budget.edit',
            'budget.delete',
            
            'budget_category.view',
            'budget_category.create',
            'budget_category.edit',
            'budget_category.delete',

            // Asset & Inventory
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',
            'inventory.dispose',
            'inventory.depreciation',

            // Financial Reports
            'report_journal.view',
            'report_ledger.view',
            'report_balance_sheet.view',
            'report_profit_loss.view',
            
            // Revenue Split
            'revenue_split.view',
            'revenue_split.create',
            'revenue_split.edit',
            'revenue_split.delete',

            // System Settings - Other Lists
            'tax.view',
            'tax.create',
            'tax.edit',
            'tax.delete',
            
            'unit.view',
            'unit.create',
            'unit.edit',
            'unit.delete',
            
            'product_category.view',
            'product_category.create',
            'product_category.edit',
            'product_category.delete',
            
            'tag.view',
            'tag.create',
            'tag.edit',
            'tag.delete',
            
            'sales_person.view',
            'sales_person.create',
            'sales_person.edit',
            'sales_person.delete',
            
            'payment_term.view',
            'payment_term.create',
            'payment_term.edit',
            'payment_term.delete',
            
            'transaction_number.view',
            'transaction_number.create',
            'transaction_number.edit',
            'transaction_number.delete',

            // User Management
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
            'user.import',
            'user.export',

            // Role & Permission Management
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
            'role.manage_permissions',
            'permission.manage',

            // Audit & System
            'audit_log.view',
            'settings.manage',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Define roles with their permissions
        $roles = [
            'super_admin' => [
                'description' => 'Full system access - can access and modify everything',
                'permissions' => $permissions, // All permissions
            ],
            'admin' => [
                'description' => 'System administrator - manage users, roles, and most transactions',
                'permissions' => [
                    // Dashboard
                    'dashboard.view',
                    'financial_recording.view',
                    
                    // All transactions - view and create
                    'sales_order.view', 'sales_order.create', 'sales_order.edit',
                    'sales_delivery.view', 'sales_delivery.create', 'sales_delivery.edit',
                    'sales_invoice.view', 'sales_invoice.create', 'sales_invoice.edit',
                    
                    'purchase_order.view', 'purchase_order.create', 'purchase_order.edit',
                    'purchase_delivery.view', 'purchase_delivery.create', 'purchase_delivery.edit',
                    'purchase_invoice.view', 'purchase_invoice.create', 'purchase_invoice.edit',
                    
                    'expense.view', 'expense.create', 'expense.edit',
                    'stock_adjustment.view', 'stock_adjustment.create',
                    'return_management.view', 'return_management.create',
                    
                    // Master data management
                    'product.view', 'product.create', 'product.edit', 'product.import', 'product.export',
                    'contact.view', 'contact.create', 'contact.edit', 'contact.import', 'contact.export',
                    'warehouse.view', 'warehouse.create', 'warehouse.edit', 'warehouse.stock_transfer',
                    'revenue_split.view', 'revenue_split.create', 'revenue_split.edit',
                    
                    // Settings
                    'tax.view', 'tax.create', 'tax.edit',
                    'unit.view', 'unit.create', 'unit.edit',
                    'product_category.view', 'product_category.create', 'product_category.edit',
                    'sales_person.view', 'sales_person.create',
                    'payment_term.view', 'payment_term.create',
                    'transaction_number.view',
                    
                    // User & Role Management
                    'user.view', 'user.create', 'user.edit', 'user.import', 'user.export',
                    'role.view', 'role.create', 'role.edit', 'role.manage_permissions',
                    
                    // Reports
                    'report_journal.view',
                    'report_ledger.view',
                    'report_balance_sheet.view',
                    'report_profit_loss.view',
                    'audit_log.view',
                    'settings.manage',
                ],
            ],
            'manager' => [
                'description' => 'Department manager - can create and manage transactions',
                'permissions' => [
                    // Dashboard
                    'dashboard.view',
                    'financial_recording.view',
                    
                    // Transaction creation and viewing
                    'sales_order.view', 'sales_order.create', 'sales_order.edit',
                    'sales_delivery.view', 'sales_delivery.create', 'sales_delivery.edit',
                    'sales_invoice.view',
                    
                    'purchase_order.view', 'purchase_order.create', 'purchase_order.edit',
                    'purchase_delivery.view', 'purchase_delivery.create',
                    
                    'expense.view', 'expense.create', 'expense.edit',
                    'stock_adjustment.view', 'stock_adjustment.create',
                    'return_management.view', 'return_management.create',
                    
                    // Master data viewing
                    'product.view', 'product.import', 'product.export',
                    'contact.view', 'contact.create', 'contact.edit',
                    'warehouse.view',
                    'revenue_split.view',
                    
                    // Reports
                    'report_journal.view',
                    'report_ledger.view',
                    'report_balance_sheet.view',
                    'report_profit_loss.view',
                ],
            ],
            'staff' => [
                'description' => 'Staff member - can create transactions and view data',
                'permissions' => [
                    // Dashboard
                    'dashboard.view',
                    
                    // Transaction creation
                    'sales_order.view', 'sales_order.create',
                    'sales_delivery.view', 'sales_delivery.create',
                    'sales_invoice.view',
                    
                    'purchase_order.view', 'purchase_order.create',
                    'purchase_delivery.view',
                    
                    'expense.view', 'expense.create',
                    'stock_adjustment.view',
                    'return_management.view',
                    
                    // Master data viewing
                    'product.view',
                    'contact.view',
                    'warehouse.view',
                ],
            ],
            'viewer' => [
                'description' => 'Viewer only - can only view data',
                'permissions' => [
                    // Dashboard
                    'dashboard.view',
                    'financial_recording.view',
                    
                    // View only permissions
                    'sales_order.view',
                    'sales_delivery.view',
                    'sales_invoice.view',
                    
                    'purchase_order.view',
                    'purchase_delivery.view',
                    'purchase_invoice.view',
                    
                    'expense.view',
                    'stock_adjustment.view',
                    'return_management.view',
                    'stock_opname.view',
                    
                    // Master data viewing
                    'product.view',
                    'contact.view',
                    'warehouse.view',
                    'budget.view',
                    'inventory.view',
                    
                    // Reports
                    'report_journal.view',
                    'report_ledger.view',
                    'report_balance_sheet.view',
                    'report_profit_loss.view',
                ],
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // Assign permissions to role
            if (isset($roleData['permissions'])) {
                $rolePermissions = Permission::whereIn('name', $roleData['permissions'])->pluck('id')->toArray();
                $role->syncPermissions($rolePermissions);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear cache
        app()['cache']->forget('spatie.permission.cache');

        // Get all permissions that were created
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            $permission->delete();
        }

        // Get all roles that were created
        $roles = Role::all();
        foreach ($roles as $role) {
            $role->delete();
        }
    }
};
