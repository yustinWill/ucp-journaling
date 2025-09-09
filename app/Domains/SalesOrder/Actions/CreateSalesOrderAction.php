<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Services\TransactionNumberService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateSalesOrderAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // Generate transaction number if not provided
            if (!isset($data['reference_no']) || empty($data['reference_no'])) {
                $data['reference_no'] = app(TransactionNumberService::class)->generateNumber('sales_order');
            }

            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                $data['attachment'] = $data['attachment']->store('sales_orders', 'public');
            }

            // Create sales order
            $salesOrderId = app(SalesOrderService::class)->create($data);

            // Create product lines if provided
            if (!empty($data['products'])) {
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
        foreach ($products as $index => $product) {
            DB::table('t_sales_order_lines')->insert([
                'f_sales_order_id' => $salesOrderId,
                'line_sequence' => $index + 1,
                'f_product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'unit_price' => $this->parsePrice($product['unit_price']),
                'f_tax_id' => $product['tax_id'] ?? null,
                'line_total' => $this->parsePrice($product['total']),
                'created_at' => now(),
            ]);
        }
    }

    private function parsePrice(string $priceString): float
    {
        // Remove currency symbols and formatting
        return (float) preg_replace('/[^\d.]/', '', $priceString);
    }
}