<?php

namespace App\Domains\PurchaseInvoice\Actions;

use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceAuditLogService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Enums\ActionTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdatePurchaseInvoiceAction
{
    public function handle(Request $request, int $id): array
    {
        try {
            DB::beginTransaction();

            // Validate request data
            $validated = $request->validate([
                'invoice_date' => 'required|date',
                'transaction_date' => 'required|date',
                'due_date' => 'nullable|date',
                'description' => 'nullable|string|max:1000',
                'internal_memo' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'products' => 'nullable|array',
                'products.*.id' => 'required_with:products|integer',
                'products.*.note' => 'nullable|string|max:255',
            ]);

            $invoiceService = app(PurchaseInvoiceService::class);
            $existingInvoice = $invoiceService->findOne($id);

            if (! $existingInvoice) {
                return [
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan.',
                ];
            }

            // Calculate due date if not provided
            if (! $validated['due_date']) {
                $invoiceDate = new \DateTime($validated['invoice_date']);
                $paymentTerms = $existingInvoice->payment_terms ?? 'NET 30';
                $match = preg_match('/\d+/', $paymentTerms, $matches);
                $paymentDays = $match ? intval($matches[0]) : 30;
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

            // Prepare data for update
            $updateData = [
                'invoice_date' => $validated['invoice_date'],
                'transaction_date' => $validated['transaction_date'],
                'due_date' => $validated['due_date'],
                'description' => $validated['description'] ?? null,
                'internal_memo' => $validated['internal_memo'] ?? null,
                'tags' => $validated['tags'] ?? null,
                'status' => $existingInvoice->status, // Keep existing status
            ];

            // Add attachment if uploaded
            if ($attachmentPath) {
                $updateData['attachment'] = $attachmentPath;
            }

            // Update invoice
            $invoiceService->update($id, $updateData);

            // Update product notes if provided
            if (isset($validated['products']) && is_array($validated['products'])) {
                foreach ($validated['products'] as $product) {
                    if (isset($product['id']) && isset($product['note'])) {
                        // Update product notes in the invoice products table
                        DB::table('t_purchase_invoice_products')
                            ->where('id', $product['id'])
                            ->update([
                                'note' => $product['note'],
                                'updated_at' => now(),
                                'updated_by' => Auth::id(),
                            ]);
                    }
                }
            }

            // Create audit log
            $auditLogService = app(PurchaseInvoiceAuditLogService::class);
            $changes = [];

            if ($existingInvoice->invoice_date != $validated['invoice_date']) {
                $changes[] = 'tanggal invoice';
            }
            if ($existingInvoice->transaction_date != $validated['transaction_date']) {
                $changes[] = 'tanggal transaksi';
            }
            if ($existingInvoice->description != $validated['description']) {
                $changes[] = 'keterangan';
            }
            if ($existingInvoice->internal_memo != $validated['internal_memo']) {
                $changes[] = 'memo internal';
            }
            if ($attachmentPath) {
                $changes[] = 'lampiran';
            }
            if (json_encode($existingInvoice->f_tag_codes ?? []) != json_encode($validated['tags'] ?? [])) {
                $changes[] = 'tag';
            }

            $changeDescription = 'Invoice diperbarui';
            if (! empty($changes)) {
                $changeDescription .= ' ('.implode(', ', $changes).')';
            }

            $auditLogService->create([
                'invoice_id' => $id,
                'action_type' => ActionTypes::UPDATE,
                'description' => $changeDescription,
                'metadata' => json_encode([
                    'changes' => $changes,
                    'before' => [
                        'invoice_date' => $existingInvoice->invoice_date,
                        'transaction_date' => $existingInvoice->transaction_date,
                        'description' => $existingInvoice->description,
                        'internal_memo' => $existingInvoice->internal_memo,
                        'tags' => $existingInvoice->f_tag_codes,
                        'attachment' => $existingInvoice->attachment,
                    ],
                    'after' => [
                        'invoice_date' => $validated['invoice_date'],
                        'transaction_date' => $validated['transaction_date'],
                        'description' => $validated['description'],
                        'internal_memo' => $validated['internal_memo'],
                        'tags' => $validated['tags'],
                        'attachment' => $attachmentPath ?? $existingInvoice->attachment,
                    ],
                ]),
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Invoice berhasil diperbarui.',
                'data' => [
                    'id' => $id,
                    'changes' => $changes,
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui invoice: '.$e->getMessage(),
            ];
        }
    }
}
