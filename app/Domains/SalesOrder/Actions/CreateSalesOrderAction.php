<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Services\TransactionNumberService;
use Illuminate\Support\Facades\DB;

class CreateSalesOrderAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // Generate transaction number if not provided
            if (! isset($data['reference_no']) || empty($data['reference_no'])) {
                $data['reference_no'] = app(TransactionNumberService::class)->generateNumber('sales_order');
            }

            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                $data['attachment'] = $data['attachment']->store('sales_orders', 'public');
            }

            // Create sales order
            $salesOrderId = app(SalesOrderService::class)->create($data);

            // Create product lines if provided
            if (! empty($data['products'])) {
                $this->createProductLines($salesOrderId, $data['products']);
            }

            // Log the creation
            app(SalesOrderAuditLogService::class)->create(
                $salesOrderId,
                'CREATE',
                [
                    'description' => 'Sales Order created',
                    'before' => null,
                    'after' => $data,
                ]
            );

            return $salesOrderId;
        });
    }

    private function createProductLines(int $salesOrderId, array $products): void
    {
        foreach ($products as $product) {
            DB::table('t_sales_order_products')->insert([
                'company_code' => session('user_company_code'),
                'f_sales_order_id' => $salesOrderId,
                'f_product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'delivered_qty' => 0,
                'remaining_qty' => $product['quantity'],
                'unit_price' => $this->parsePrice($product['unit_price']),
                // Tax fields removed - handled at invoice level
                'line_total' => $this->parsePrice($product['total']),
                'description' => $product['description'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function parsePrice(string $priceString): float
    {
        // Remove currency symbols and formatting
        return (float) preg_replace('/[^\d.]/', '', $priceString);
    }
}
