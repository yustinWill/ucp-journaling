<?php

namespace App\Domains\PurchaseInvoice\Actions;

use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceAuditLogService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePurchaseInvoiceAction
{
    public function execute($data)
    {
        return DB::transaction(function () use ($data) {

            $attachmentPath = null;
            if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
                $file = $data['attachment'];
                $fileName = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase_invoices', $fileName, 'public');
            }

            // Create invoice data
            $invoiceData = [
                'purchase_order_id' => $data['purchase_order_id'],
                'invoice_number' => $data['invoice_number'],
                'invoice_date' => $data['invoice_date'],
                'transaction_date' => $data['transaction_date'],
                // 'due_date' => $data['due_date'],
                'payment_terms' => $purchaseOrder->payment_term_name ?? 'NET 30',
                'description' => $data['description'] ?? null,
                'internal_memo' => $data['internal_memo'] ?? null,
                'attachment' => $attachmentPath,
                'tags' => $data['tags'] ?? null,
                // 'subtotal' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $data['total_amount'],
                'status' => $data['invoice_status'],
            ];

            // Create the invoice
            $invoiceId = app(PurchaseInvoiceService::class)->create($invoiceData);

            // Create invoice products
            $invoiceProducts = [];
            foreach ($data['products'] as $product) {
                if ($product['invoice_qty'] > 0) {
                    $invoicedBefore = floatval($product['invoiced_qty_before'] ?? 0);
                    $remainingBefore = floatval($product['remaining_qty_before'] ?? $product['po_qty'] ?? 0);
                    $invoiceQty = floatval($product['invoice_qty']);

                    $invoiceProducts[] = [
                        'product_id' => $product['product_id'],
                        'product_order_product_id' => $product['product_order_product_id'],
                        'invoice_qty' => $invoiceQty,
                        'unit_price' => $product['unit_price'],
                        'total_price' => $product['total_price'],
                        'invoiced_qty_before' => $invoicedBefore,
                        'remaining_qty_before' => $remainingBefore,
                        'invoiced_qty_after' => $invoicedBefore + $invoiceQty,
                        'remaining_qty_after' => $remainingBefore - $invoiceQty,
                        'note' => $product['note'] ?? null,
                        'status' => 'ACTIVE',
                    ];
                }
            }

            app(PurchaseInvoiceService::class)->createInvoiceProducts($invoiceId, $invoiceProducts);

            // Create audit log
            app(PurchaseInvoiceAuditLogService::class)->create([
                'invoice_id' => $invoiceId,
                'action_type' => ActionTypes::CREATE,
                'description' => "Purchase Invoice {$data['invoice_number']} dibuat",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
                'user_id' => Auth::id(),
            ]);
        });
    }
}
