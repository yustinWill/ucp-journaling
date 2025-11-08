<?php

namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Services\SalesOrderRevenueSplitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class CreateSalesInvoiceAction
{
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

            foreach ($data['products'] as $product) {
                $product['f_product_id'] = $product['product_id'];
                $product['quantity'] = $product['invoice_qty'];
                $product['id'] = $product['sales_order_product_id'];
                app(SalesOrderRevenueSplitService::class)->createRevenueSplitForProduct(
                    $data['sales_order_id'],
                    $product,
                    session('user_company_code'),
                    Auth::id()
                );
            }

            return $invoiceId;
        });
    }
}
