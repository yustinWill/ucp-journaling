<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create the Return Management module and permissions
        $this->createModuleAndPermissions();
        
        // Then, create sample data
        $this->createSampleData();
    }

    private function createModuleAndPermissions(): void
    {
        // Check if Return Management module already exists
        $existingModule = DB::table('modules')->where('slug', 'return_management')->first();
        
        if ($existingModule) {
            $moduleId = $existingModule->id;
        } else {
            // Create Return Management module
            $moduleId = DB::table('modules')->insertGetId([
                'name' => 'Return Management',
                'slug' => 'return_management',
                'description' => 'Return management for purchase and sales invoices',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create permissions for Return Management module if they don't exist
        $permissions = ['read', 'write', 'delete', 'approval'];
        foreach ($permissions as $permission) {
            $existingPermission = DB::table('permissions')
                ->where('module_id', $moduleId)
                ->where('permission_type', $permission)
                ->first();
                
            if (!$existingPermission) {
                DB::table('permissions')->insert([
                    'module_id' => $moduleId,
                    'permission_type' => $permission,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Grant permissions to Admin role (assuming role ID 1 is Admin)
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->first()->id ?? 1;
        $permissionIds = DB::table('permissions')->where('module_id', $moduleId)->pluck('id');

        foreach ($permissionIds as $permissionId) {
            $existingRolePermission = DB::table('role_permissions')
                ->where('role_id', $adminRoleId)
                ->where('permission_id', $permissionId)
                ->first();
                
            if (!$existingRolePermission) {
                DB::table('role_permissions')->insert([
                    'company_code' => '001',
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId,
                    'status' => 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function createSampleData(): void
    {
        // Get sample company code (assuming '001' is default)
        $companyCode = '001';
        
        // Get sample user ID (assuming user ID 1 exists)
        $userId = DB::table('users')->first()->id ?? 1;

        // Get some existing purchase and sales invoices to create returns from
        $purchaseInvoices = DB::table('t_purchase_invoices')->limit(3)->get();
        $salesInvoices = DB::table('t_sales_invoices')->limit(3)->get();

        // Create sample Purchase Returns
        if ($purchaseInvoices->count() > 0) {
            foreach ($purchaseInvoices as $index => $pi) {
                $returnId = $this->createSampleReturn([
                    'company_code' => $companyCode,
                    'transaction_code' => 'PR-2024-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'return_type' => 'PURCHASE_RETURN',
                    'f_source_invoice_id' => $pi->id,
                    'source_invoice_code' => $pi->transaction_code,
                    'return_date' => now()->subDays(rand(1, 30)),
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'return_reason' => $this->getRandomReturnReason(),
                    'return_reason_description' => 'Return reason description for ' . $pi->transaction_code,
                    'description' => 'Sample purchase return for ' . $pi->transaction_code,
                    'status' => $this->getRandomStatus(),
                    'processing_status' => $this->getRandomProcessingStatus(),
                    'subtotal' => rand(100000, 500000),
                    'total_tax' => rand(10000, 50000),
                    'total_discount' => 0,
                    'grand_total' => rand(110000, 550000),
                    'created_by' => $userId,
                ]);

                $this->createSampleReturnProducts($returnId, $pi->id, 'PURCHASE_RETURN');
                $this->createSampleAuditLogs($returnId, $companyCode, $userId);
            }
        }

        // Create sample Sales Returns
        if ($salesInvoices->count() > 0) {
            foreach ($salesInvoices as $index => $si) {
                $returnId = $this->createSampleReturn([
                    'company_code' => $companyCode,
                    'transaction_code' => 'SR-2024-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'return_type' => 'SALES_RETURN',
                    'f_source_invoice_id' => $si->id,
                    'source_invoice_code' => $si->transaction_code,
                    'return_date' => now()->subDays(rand(1, 30)),
                    'transaction_date' => now()->subDays(rand(1, 30)),
                    'return_reason' => $this->getRandomReturnReason(),
                    'return_reason_description' => 'Return reason description for ' . $si->transaction_code,
                    'description' => 'Sample sales return for ' . $si->transaction_code,
                    'status' => $this->getRandomStatus(),
                    'processing_status' => $this->getRandomProcessingStatus(),
                    'subtotal' => rand(100000, 500000),
                    'total_tax' => rand(10000, 50000),
                    'total_discount' => 0,
                    'grand_total' => rand(110000, 550000),
                    'created_by' => $userId,
                ]);

                $this->createSampleReturnProducts($returnId, $si->id, 'SALES_RETURN');
                $this->createSampleAuditLogs($returnId, $companyCode, $userId);
            }
        }
    }

    private function createSampleReturn(array $data): int
    {
        return DB::table('t_return_managements')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));
    }

    private function createSampleReturnProducts(int $returnId, int $sourceInvoiceId, string $returnType): void
    {
        // Get products from the source invoice
        if ($returnType === 'PURCHASE_RETURN') {
            $invoiceProducts = DB::table('t_purchase_invoice_products')
                ->where('f_purchase_invoice_id', $sourceInvoiceId)
                ->limit(rand(1, 3))
                ->get();
        } else {
            $invoiceProducts = DB::table('t_sales_invoice_products')
                ->where('f_sales_invoice_id', $sourceInvoiceId)
                ->limit(rand(1, 3))
                ->get();
        }

        foreach ($invoiceProducts as $invoiceProduct) {
            $originalQty = $invoiceProduct->qty ?? 1;
            $returnQty = $originalQty * (rand(20, 80) / 100); // Return 20-80% of original
            $unitPrice = $invoiceProduct->unit_price ?? 10000;
            $returnUnitPrice = $unitPrice * (rand(80, 100) / 100); // Return price 80-100% of original

            DB::table('t_return_management_products')->insert([
                'f_return_management_id' => $returnId,
                'f_source_invoice_product_id' => $invoiceProduct->id,
                'f_product_id' => $invoiceProduct->f_product_id,
                'original_qty' => $originalQty,
                'returned_qty_before' => 0,
                'available_qty' => $originalQty,
                'return_qty' => $returnQty,
                'returned_qty_after' => $returnQty,
                'unit_price' => $unitPrice,
                'return_unit_price' => $returnUnitPrice,
                'total_return_price' => $returnQty * $returnUnitPrice,
                'return_condition' => $this->getRandomReturnCondition(),
                'return_reason' => $this->getRandomReturnReason(),
                'note' => 'Sample return product note',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => DB::table('users')->first()->id ?? 1,
            ]);
        }
    }

    private function createSampleAuditLogs(int $returnId, string $companyCode, int $userId): void
    {
        $transactionCode = DB::table('t_return_managements')
            ->where('id', $returnId)
            ->value('transaction_code');

        // Create audit log
        DB::table('t_return_management_audit_logs')->insert([
            'company_code' => $companyCode,
            'f_return_management_id' => $returnId,
            'action_type' => 'CREATE',
            'description' => "Return {$transactionCode} dibuat",
            'metadata' => json_encode([
                'created_via' => 'seeder',
                'sample_data' => true
            ]),
            'user_id' => $userId,
            'created_at' => now(),
        ]);
    }

    private function getRandomReturnReason(): string
    {
        $reasons = [
            'DEFECTIVE', 'WRONG_ITEM', 'DAMAGED', 'EXPIRED', 
            'OVERSTOCKED', 'CUSTOMER_REQUEST', 'OTHER'
        ];
        return $reasons[array_rand($reasons)];
    }

    private function getRandomStatus(): string
    {
        $statuses = [
            'DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'PROCESSED', 'REJECTED'
        ];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomProcessingStatus(): string
    {
        $statuses = ['NOT_PROCESSED', 'PARTIAL', 'COMPLETED'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomReturnCondition(): string
    {
        $conditions = ['NEW', 'USED_GOOD', 'USED_FAIR', 'DAMAGED', 'DEFECTIVE'];
        return $conditions[array_rand($conditions)];
    }
}