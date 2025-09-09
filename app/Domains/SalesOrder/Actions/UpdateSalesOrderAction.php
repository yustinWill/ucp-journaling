<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateSalesOrderAction
{
    public function execute(int $id, array $data): void
    {
        DB::transaction(function () use ($id, $data) {
            // Get current data for audit log
            $oldData = app(SalesOrderService::class)->findById($id);
            
            if (!$oldData) {
                throw new \Exception('Sales Order not found');
            }

            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                // Delete old attachment if exists
                if ($oldData->attachment) {
                    Storage::disk('public')->delete($oldData->attachment);
                }
                $data['attachment'] = $data['attachment']->store('sales_orders', 'public');
            }

            // Update sales order
            app(SalesOrderService::class)->update($id, $data);

            // Update product lines if provided
            if (!empty($data['products'])) {
                $this->updateProductLines($id, $data['products']);
            }

            // Log the update
            app(SalesOrderAuditLogService::class)->create(
                $id,
                'UPDATE',
                [
                    'description' => 'Sales Order updated',
                    'before' => (array) $oldData,
                    'after' => $data,
                ]
            );
        });
    }

    private function updateProductLines(int $salesOrderId, array $products): void
    {
        // Delete existing product lines
        DB::table('t_sales_order_lines')
            ->where('f_sales_order_id', $salesOrderId)
            ->delete();

        // Insert new product lines
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