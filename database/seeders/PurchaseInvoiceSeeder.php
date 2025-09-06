<?php

namespace Database\Seeders;

use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceAuditLogService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Enums\ActionTypes;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // Set session company code for testing
        session(['user_company_code' => '001']);

        $invoiceService = new PurchaseInvoiceService;
        $auditLogService = new PurchaseInvoiceAuditLogService;

        // Get available POs for invoicing
        $purchaseOrders = DB::table('t_purchase_orders')
            ->whereIn('status', ['APPROVED', 'PARTIAL_INVOICED'])
            ->whereIn('id', [11, 12]) // Use specific POs we know have products
            ->get();

        foreach ($purchaseOrders as $po) {
            // Get PO products for this specific PO
            $poProducts = DB::table('t_purchase_order_products')
                ->where('f_purchase_order_id', $po->id)
                ->get();

            if ($poProducts->isEmpty()) {
                continue;
            }

            // Scenario 1: Partial Invoice (50% of first PO)
            if ($po->id == 11) {
                $this->createPartialInvoice($invoiceService, $auditLogService, $po, $poProducts->toArray());
            }

            // Scenario 2: Full Invoice (100% of second PO)
            if ($po->id == 12) {
                $this->createFullInvoice($invoiceService, $auditLogService, $po, $poProducts->toArray());
            }
        }

        echo "Purchase Invoice test data created successfully!\n";
        echo "- Partial invoice created for PO ID: 11\n";
        echo "- Full invoice created for PO ID: 12\n";
        echo "- PO ID: 13 remains unfulfilled for testing\n";
    }

    private function createPartialInvoice($invoiceService, $auditLogService, $po, $poProducts)
    {
        $invoiceData = [
            'purchase_order_id' => $po->id,
            'invoice_number' => 'INV-2025-0906-P01',
            'invoice_date' => '2025-09-06',
            'transaction_date' => '2025-09-06',
            'due_date' => '2025-10-06',
            'payment_terms' => 'NET 30',
            'description' => 'Partial Invoice - 50% of PO items',
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'status' => 'UNPAID',
            'created_by' => 1,
        ];

        $invoiceId = $invoiceService->create($invoiceData);

        $totalAmount = 0;
        $invoiceProducts = [];

        // Create partial invoice products (50% quantity)
        foreach ($poProducts as $product) {
            $partialQty = floatval($product->qty) * 0.5; // 50%
            $unitPrice = floatval($product->price_per_qty);
            $totalPrice = $partialQty * $unitPrice;
            $totalAmount += $totalPrice;

            $invoiceProducts[] = [
                'f_purchase_invoice_id' => $invoiceId,
                'f_product_order_product_id' => $product->id,
                'f_product_id' => $product->f_product_id,
                'invoiced_qty_before' => 0,
                'remaining_qty_before' => floatval($product->qty),
                'qty' => $partialQty,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'invoiced_qty_after' => $partialQty,
                'remaining_qty_after' => floatval($product->qty) - $partialQty,
                'note' => 'Partial invoice - 50% quantity',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ];
        }

        DB::table('t_purchase_invoice_products')->insert($invoiceProducts);

        // Update invoice totals
        DB::table('t_purchase_invoices')
            ->where('id', $invoiceId)
            ->update([
                'subtotal' => $totalAmount,
                'grand_total' => $totalAmount,
                'updated_at' => now(),
                'updated_by' => 1,
            ]);

        // Update PO status to PARTIAL_INVOICED
        DB::table('t_purchase_orders')
            ->where('id', $po->id)
            ->update(['status' => 'PARTIAL_INVOICED']);

        // Create audit log
        $auditLogService->create([
            'invoice_id' => $invoiceId,
            'action_type' => ActionTypes::CREATE,
            'description' => 'Purchase Invoice INV-2025-0906-P01 dibuat dengan total Rp '.number_format($totalAmount, 0, ',', '.'),
            'metadata' => json_encode([
                'invoice_number' => 'INV-2025-0906-P01',
                'total_amount' => $totalAmount,
                'total_products' => count($invoiceProducts),
                'invoice_type' => 'partial',
            ]),
            'user_id' => 1,
        ]);
    }

    private function createFullInvoice($invoiceService, $auditLogService, $po, $poProducts)
    {
        $invoiceData = [
            'purchase_order_id' => $po->id,
            'invoice_number' => 'INV-2025-0906-F02',
            'invoice_date' => '2025-09-06',
            'transaction_date' => '2025-09-06',
            'due_date' => '2025-10-06',
            'payment_terms' => 'NET 30',
            'description' => 'Full Invoice - 100% of PO items',
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'status' => 'PAID', // Mark as paid for testing
            'created_by' => 1,
        ];

        $invoiceId = $invoiceService->create($invoiceData);

        $totalAmount = 0;
        $invoiceProducts = [];

        // Create full invoice products (100% quantity)
        foreach ($poProducts as $product) {
            $fullQty = floatval($product->qty);
            $unitPrice = floatval($product->price_per_qty);
            $totalPrice = $fullQty * $unitPrice;
            $totalAmount += $totalPrice;

            $invoiceProducts[] = [
                'f_purchase_invoice_id' => $invoiceId,
                'f_product_order_product_id' => $product->id,
                'f_product_id' => $product->f_product_id,
                'invoiced_qty_before' => 0,
                'remaining_qty_before' => $fullQty,
                'qty' => $fullQty,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'invoiced_qty_after' => $fullQty,
                'remaining_qty_after' => 0,
                'note' => 'Full invoice - 100% quantity',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => 1,
            ];
        }

        DB::table('t_purchase_invoice_products')->insert($invoiceProducts);

        // Update invoice totals
        DB::table('t_purchase_invoices')
            ->where('id', $invoiceId)
            ->update([
                'subtotal' => $totalAmount,
                'grand_total' => $totalAmount,
                'updated_at' => now(),
                'updated_by' => 1,
            ]);

        // Update PO status to FULLY_INVOICED
        DB::table('t_purchase_orders')
            ->where('id', $po->id)
            ->update(['status' => 'FULLY_INVOICED']);

        // Create audit log
        $auditLogService->create([
            'invoice_id' => $invoiceId,
            'action_type' => ActionTypes::CREATE,
            'description' => 'Purchase Invoice INV-2025-0906-F02 dibuat dengan total Rp '.number_format($totalAmount, 0, ',', '.'),
            'metadata' => json_encode([
                'invoice_number' => 'INV-2025-0906-F02',
                'total_amount' => $totalAmount,
                'total_products' => count($invoiceProducts),
                'invoice_type' => 'full',
            ]),
            'user_id' => 1,
        ]);
    }
}
