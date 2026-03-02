<?php

namespace App\Domains\PurchaseInvoice\Actions;

use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceAuditLogService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Enums\ActionTypes;
use Illuminate\Http\UploadedFile;
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
                $fileName = time().'_'.$file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase_invoices', $fileName, 'public');
            }

            // Map invoice_status to database status enum
            // invoice_status (from form): COMPLETED, PARTIAL
            // status (database): DRAFT, UNPAID, PAID, PARTIAL_PAID, OVERDUE, CANCELLED
            $statusMapping = [
                'COMPLETED' => 'PAID',
                'PARTIAL' => 'PARTIAL_PAID',
            ];
            $dbStatus = $statusMapping[$data['invoice_status']] ?? 'UNPAID';

            // Get purchase order data for due_date and payment_terms if not provided
            $purchaseOrder = DB::table('t_purchase_orders')
                ->where('id', $data['purchase_order_id'])
                ->first();

            // Create invoice data
            $invoiceData = [
                'purchase_order_id' => $data['purchase_order_id'],
                'invoice_number' => $data['invoice_number'],
                'invoice_date' => $data['invoice_date'],
                'transaction_date' => $data['transaction_date'],
                'due_date' => $data['due_date'] ?? $purchaseOrder->payment_term_due_date ?? now()->addDays(30),
                'payment_terms' => $purchaseOrder->payment_term_name ?? 'NET 30',
                'description' => $data['description'] ?? null,
                'internal_memo' => $data['internal_memo'] ?? null,
                'attachment' => $attachmentPath,
                'tags' => $data['tags'] ?? null,
                // 'subtotal' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $data['total_amount'],
                'status' => $dbStatus,
            ];

            // Create the invoice
            $invoiceId = app(PurchaseInvoiceService::class)->create($invoiceData);

            // Create invoice products (PO quantities used as reference)
            $invoiceProducts = [];
            foreach ($data['products'] as $product) {
                $poQty = floatval($product['po_qty'] ?? 0);
                $unitPrice = floatval($product['unit_price'] ?? 0);

                $invoiceProducts[] = [
                    'product_id' => $product['product_id'],
                    'product_order_product_id' => $product['product_order_product_id'],
                    'invoice_qty' => $poQty,
                    'unit_price' => $unitPrice,
                    'total_price' => $poQty * $unitPrice,
                    'invoiced_qty_before' => 0,
                    'remaining_qty_before' => $poQty,
                    'invoiced_qty_after' => $poQty,
                    'remaining_qty_after' => 0,
                    'note' => null,
                    'status' => 'ACTIVE',
                ];
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
