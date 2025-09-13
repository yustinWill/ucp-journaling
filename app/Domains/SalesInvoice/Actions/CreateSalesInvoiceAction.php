<?php

namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use Illuminate\Support\Facades\DB;

class CreateSalesInvoiceAction
{
    public function validateSalesInvoiceData(array $data): array
    {
        $errors = [];

        // Basic validation
        if (empty($data['invoice_date'])) {
            $errors[] = 'Tanggal invoice wajib diisi';
        }

        if (empty($data['invoice_number'])) {
            $errors[] = 'Nomor invoice wajib diisi';
        }

        if (empty($data['sales_order_id'])) {
            $errors[] = 'Sales Order wajib dipilih';
        }

        if (empty($data['transaction_date'])) {
            $errors[] = 'Tanggal transaksi wajib diisi';
        }

        if (empty($data['due_date'])) {
            $errors[] = 'Tanggal jatuh tempo wajib diisi';
        }

        // Validate products
        if (empty($data['products']) || ! is_array($data['products'])) {
            $errors[] = 'Minimal satu produk harus diinvoice';
        } else {
            $totalAmount = 0;
            foreach ($data['products'] as $product) {
                if (isset($product['invoice_qty']) && $product['invoice_qty'] > 0) {
                    $totalAmount += $product['invoice_qty'] * ($product['unit_price'] ?? 0);
                }
            }

            if ($totalAmount <= 0) {
                $errors[] = 'Total invoice harus lebih dari 0';
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle file upload
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('sales_invoice', 'public');
                $data['attachment'] = $path;
            }

            // Calculate totals
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $product) {
                    if (isset($product['invoice_qty']) && $product['invoice_qty'] > 0) {
                        $lineTotal = $product['invoice_qty'] * ($product['unit_price'] ?? 0);
                        $subtotal += $lineTotal;
                    }
                }
            }

            $grandTotal = $subtotal + $totalTax - $totalDiscount;

            // Add calculated totals to data
            $data['subtotal'] = $subtotal;
            $data['total_tax'] = $totalTax;
            $data['total_discount'] = $totalDiscount;
            $data['grand_total'] = $grandTotal;

            // Create sales invoice record
            $invoiceId = app(SalesInvoiceService::class)->create($data);

            // Create invoice products
            if (isset($data['products']) && is_array($data['products'])) {
                $invoiceProducts = [];
                foreach ($data['products'] as $product) {
                    if (isset($product['invoice_qty']) && $product['invoice_qty'] > 0) {
                        $invoiceProducts[] = [
                            'sales_order_product_id' => $product['sales_order_product_id'],
                            'product_id' => $product['product_id'],
                            'invoice_qty' => $product['invoice_qty'],
                            'unit_price' => $product['unit_price'],
                            'invoiced_qty_before' => $product['invoiced_qty_before'] ?? 0,
                            'remaining_qty_before' => $product['remaining_qty_before'] ?? 0,
                            'note' => $product['note'] ?? null,
                            'status' => 'INVOICED',
                        ];
                    }
                }

                if (! empty($invoiceProducts)) {
                    app(SalesInvoiceService::class)->createInvoiceProducts($invoiceId, $invoiceProducts);
                }
            }

            // Create audit log
            app(SalesInvoiceAuditLogService::class)->create($invoiceId, 'CREATE', 'Sales Invoice created', [
                'invoice_number' => $data['invoice_number'],
                'sales_order_id' => $data['sales_order_id'],
                'grand_total' => $grandTotal,
                'total_products' => count($invoiceProducts ?? []),
            ]);

            return $invoiceId;
        });
    }
}
