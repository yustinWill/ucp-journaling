<?php

namespace App\Domains\SalesOrder\Actions;

use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\GlobalConfig\Services\GlobalConfigService;
use App\Domains\SalesInvoice\Actions\CreateSalesInvoiceAction;
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

            $config_data = app(GlobalConfigService::class)->findOne('AUTO_GENERATE_SI_AFTER_SO');

            if ($config_data && $config_data->value == 'true') {
                $invoiceData = [
                    'invoice_number' => $data['reference_no'],
                    'invoice_date' => $data['date'],
                    'transaction_date' => $data['date'],
                    'due_date' => $data['payment_term_due_date'],
                    'description' => $data['description'],
                    'memo' => $data['memo'] ?? null,
                    'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
                    'sales_order_id' => $salesOrderId,
                    'total_amount' => $data['total'] ?? 0,
                    'total_items' => is_array($data['products']) ? count($data['products']) : 0,
                    'products' => [],
                ];

                $soProducts = app(SalesOrderService::class)->findProductLines($salesOrderId, true);

                foreach ($soProducts as $product) {
                    $invoiceData['products'][] = [
                        'sales_order_product_id' => $product->id,
                        'product_id'             => $product->f_product_id,
                        'po_qty'                 => $product->quantity,
                        'invoice_qty'            => $product->quantity, // or use actual invoice qty if available
                        'remaining_qty_before'   => $product->remaining_qty_for_invoice,
                        'unit_price'             => $product->unit_price,
                    ];
                }
                app(CreateSalesInvoiceAction::class)->execute($invoiceData);
            }

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
