<?php

namespace App\Domains\PurchaseInvoice\Actions;

use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceAuditLogService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Enums\ActionTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePurchaseInvoiceAction
{
    public function handle(Request $request): array
    {
        try {
            DB::beginTransaction();

            // Validate request data
            $validated = $request->validate([
                'purchase_order_id' => 'required|exists:t_purchase_orders,id',
                'invoice_number' => 'required|string|max:50',
                'invoice_date' => 'required|date',
                'transaction_date' => 'required|date',
                'due_date' => 'nullable|date',
                'description' => 'nullable|string|max:1000',
                'internal_memo' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer',
                'products.*.product_order_product_id' => 'required|integer',
                'products.*.invoice_qty' => 'required|numeric|min:0',
                'products.*.unit_price' => 'required|numeric|min:0',
                'products.*.total_price' => 'required|numeric|min:0',
                'products.*.invoiced_qty_before' => 'nullable|numeric|min:0',
                'products.*.remaining_qty_before' => 'nullable|numeric|min:0',
            ]);

            // Check if at least one product has quantity > 0
            $hasValidProducts = false;
            $totalAmount = 0;
            foreach ($validated['products'] as $product) {
                if ($product['invoice_qty'] > 0) {
                    $hasValidProducts = true;
                    $totalAmount += $product['total_price'];
                }
            }

            if (! $hasValidProducts) {
                return [
                    'success' => false,
                    'message' => 'Pilih minimal satu produk dengan quantity lebih dari 0.',
                ];
            }

            if ($totalAmount <= 0) {
                return [
                    'success' => false,
                    'message' => 'Total invoice harus lebih dari 0.',
                ];
            }

            // Get PO data for payment terms
            $purchaseOrder = app(PurchaseOrderService::class)->findOne($validated['purchase_order_id']);
            if (! $purchaseOrder) {
                return [
                    'success' => false,
                    'message' => 'Purchase Order tidak ditemukan.',
                ];
            }

            // Calculate due date if not provided
            if (! $validated['due_date']) {
                $invoiceDate = new \DateTime($validated['invoice_date']);
                $paymentDays = $purchaseOrder->payment_term_day_count ?? 30;
                $invoiceDate->add(new \DateInterval('P'.$paymentDays.'D'));
                $validated['due_date'] = $invoiceDate->format('Y-m-d');
            }

            // Handle file upload
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time().'_'.$file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase_invoices', $fileName, 'public');
            }

            // Create invoice data
            $invoiceData = [
                'purchase_order_id' => $validated['purchase_order_id'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'transaction_date' => $validated['transaction_date'],
                'due_date' => $validated['due_date'],
                'payment_terms' => $purchaseOrder->payment_term_name ?? 'NET 30',
                'description' => $validated['description'] ?? null,
                'internal_memo' => $validated['internal_memo'] ?? null,
                'attachment' => $attachmentPath,
                'tags' => $validated['tags'] ?? null,
                'subtotal' => $totalAmount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $totalAmount,
                'status' => 'UNPAID',
            ];

            // Create the invoice
            $invoiceService = app(PurchaseInvoiceService::class);
            $invoiceId = $invoiceService->create($invoiceData);

            // Create invoice products
            $invoiceProducts = [];
            foreach ($validated['products'] as $product) {
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

            $invoiceService->createInvoiceProducts($invoiceId, $invoiceProducts);

            // Create audit log
            $auditLogService = app(PurchaseInvoiceAuditLogService::class);
            $auditLogService->create([
                'invoice_id' => $invoiceId,
                'action_type' => ActionTypes::CREATE,
                'description' => "Purchase Invoice {$validated['invoice_number']} dibuat dengan total Rp ".number_format($totalAmount, 0, ',', '.'),
                'metadata' => json_encode([
                    'invoice_number' => $validated['invoice_number'],
                    'total_amount' => $totalAmount,
                    'total_products' => count($invoiceProducts),
                ]),
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Invoice berhasil dibuat.',
                'data' => [
                    'id' => $invoiceId,
                    'invoice_number' => $validated['invoice_number'],
                    'total_amount' => $totalAmount,
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat invoice: '.$e->getMessage(),
            ];
        }
    }
}
