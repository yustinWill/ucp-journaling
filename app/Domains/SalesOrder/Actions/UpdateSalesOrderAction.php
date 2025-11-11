<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateSalesOrderAction
{
    public function execute(int $id, array $data): void
    {
        DB::transaction(function () use ($id, $data) {
            // Get current data for audit log
            $oldData = app(SalesOrderService::class)->findById($id);

            // Handle file attachment upload
            if (isset($data['attachment']) && $data['attachment']) {
                $data['attachment'] = $data['attachment']->store('sales_orders', 'public');
            }

            // Update sales order
            app(SalesOrderService::class)->update($id, $data);

            // Update product lines if provided
            if (! empty($data['products'])) {
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
        $submittedIds = [];

        foreach ($products as $index => $product) {
            if (!empty($product['id'])) {
                // Update existing row
                DB::table('t_sales_order_products')
                    ->where('id', $product['id'])
                    ->update([
                        'f_sales_order_id' => $salesOrderId,
                        // 'line_sequence' => $index + 1,
                        'f_product_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'unit_price' => $this->parsePrice($product['unit_price']),
                        // 'f_tax_id' => $product['tax_id'] ?? null,
                        'line_total' => $this->parsePrice($product['total']),
                        'is_bonus' => $product['is_bonus'] ?? 0,
                        'updated_at' => now(),
                    ]);
                $submittedIds[] = $product['id'];
            } else {
                // Insert new row
                $newId = DB::table('t_sales_order_products')->insertGetId([
                    'company_code' => session('user_company_code'),
                    'f_sales_order_id' => $salesOrderId,
                    // 'line_sequence' => $index + 1,
                    'f_product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                    'unit_price' => $this->parsePrice($product['unit_price']),
                    // 'f_tax_id' => $product['tax_id'] ?? null,
                    'line_total' => $this->parsePrice($product['total']),
                    'is_bonus' => $product['is_bonus'] ?? 0,
                    'created_at' => now(),
                ]);
                $submittedIds[] = $newId;
            }
        }

        // Remove other existing rows not in submittedIds
        if (!empty($submittedIds)) {
            DB::table('t_sales_order_products')
                ->where('f_sales_order_id', $salesOrderId)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        }
    }

    private function parsePrice(string $priceString): float
    {
        // Remove currency symbols and formatting
        return (float) preg_replace('/[^\d.]/', '', $priceString);
    }
}
