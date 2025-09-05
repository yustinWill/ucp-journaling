<?php

namespace App\Http\Controllers\MainTransaction;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        // Check read permission for purchase invoice module
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_invoice');

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_PAYMENT', 'label' => 'PENDING_PAYMENT - Menunggu Pembayaran'],
            ['value' => 'PAID', 'label' => 'PAID - Lunas'],
            ['value' => 'PARTIAL_PAID', 'label' => 'PARTIAL_PAID - Sebagian Dibayar'],
            ['value' => 'OVERDUE', 'label' => 'OVERDUE - Jatuh Tempo'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Map permissions to view variables
        $canAddPurchaseInvoice = PermissionHelper::canWrite('main_transaction_purchase_invoice');
        $canImportPurchaseInvoice = PermissionHelper::canWrite('main_transaction_purchase_invoice');
        $canExportPurchaseInvoice = PermissionHelper::canRead('main_transaction_purchase_invoice');
        $canViewPurchaseInvoice = PermissionHelper::canRead('main_transaction_purchase_invoice');
        $canEditPurchaseInvoice = PermissionHelper::canWrite('main_transaction_purchase_invoice');
        $canViewUser = PermissionHelper::canRead('master_data_user');
        $canViewPurchaseOrder = PermissionHelper::canRead('main_transaction_purchase_order');
        $canViewPurchaseDelivery = PermissionHelper::canRead('main_transaction_purchase_delivery');

        $data = [
            'statuses' => $statuses,
            'canAddPurchaseInvoice' => $canAddPurchaseInvoice,
            'canImportPurchaseInvoice' => $canImportPurchaseInvoice,
            'canExportPurchaseInvoice' => $canExportPurchaseInvoice,
            'canViewPurchaseInvoice' => $canViewPurchaseInvoice,
            'canEditPurchaseInvoice' => $canEditPurchaseInvoice,
            'canViewUser' => $canViewUser,
            'canViewPurchaseOrder' => $canViewPurchaseOrder,
            'canViewPurchaseDelivery' => $canViewPurchaseDelivery,
        ];

        return view('pages.main_transaction.purchase_invoice.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock data specific to Purchase Invoices
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'date' => '2025-07-10',
                    'reference_no' => 'PI-2025-001',
                    'supplier' => 'CV Plastik Prima',
                    'supplier_id' => 1,
                    'due_date' => '2025-08-10',
                    'status' => 'PAID',
                    'status_badge_class' => 'bg-label-success',
                    'po_reference' => 'PO-2025-001',
                    'pd_reference' => 'PD-2025-001',
                    'invoice_amount' => 25000000,
                    'paid_amount' => 25000000,
                    'remaining_amount' => 0,
                    'created_by' => 'Finance Staff',
                    'created_by_id' => 'USR-002',
                    'po_id' => 1,
                    'pd_id' => 1,
                    'tags' => [
                        ['name' => 'Lunas', 'preview' => 'bg-label-success'],
                        ['name' => 'Bahan Baku', 'preview' => 'bg-label-primary'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-12',
                    'reference_no' => 'PI-2025-002',
                    'supplier' => 'PT Grafika Mandiri',
                    'supplier_id' => 2,
                    'due_date' => '2025-08-12',
                    'status' => 'PARTIAL_PAID',
                    'status_badge_class' => 'bg-label-warning',
                    'po_reference' => 'PO-2025-002',
                    'pd_reference' => 'PD-2025-002',
                    'invoice_amount' => 15000000,
                    'paid_amount' => 7500000,
                    'remaining_amount' => 7500000,
                    'created_by' => 'Accounting Staff',
                    'created_by_id' => 'USR-004',
                    'po_id' => 2,
                    'pd_id' => 2,
                    'tags' => [
                        ['name' => 'Sebagian Bayar', 'preview' => 'bg-label-warning'],
                        ['name' => 'Kemasan', 'preview' => 'bg-label-info'],
                    ],
                ],
            ],
        ];

        return response()->json($data, 200);
    }

    public function add()
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Get required options for the Purchase Invoice form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'SUP-002 - CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'SUP-003 - PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - CV. Maju Bersama'],
        ];

        $purchaseOrders = [
            ['value' => '1', 'label' => 'PO-2025-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'PO-2025-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'PO-2025-003 - CV Kemasan Sejahtera'],
        ];

        $purchaseDeliveries = [
            ['value' => '1', 'label' => 'PD-2025-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'PD-2025-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'PD-2025-003 - CV Kemasan Sejahtera'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate'],
        ];

        $currencies = [
            ['value' => 'IDR', 'label' => 'IDR - Rupiah'],
            ['value' => 'USD', 'label' => 'USD - Dollar'],
            ['value' => 'EUR', 'label' => 'EUR - Euro'],
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN - 11%'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak'],
        ];

        return view('pages.main_transaction.purchase_invoice.add', [
            'suppliers' => $suppliers,
            'purchaseOrders' => $purchaseOrders,
            'purchaseDeliveries' => $purchaseDeliveries,
            'paymentTerms' => $paymentTerms,
            'currencies' => $currencies,
            'taxOptions' => $taxOptions,
        ]);
    }

    public function store(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menambah data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'reference_no' => 'required|string|max:50',
                'supplier_id' => 'required|integer',
                'purchase_order_id' => 'nullable|integer',
                'purchase_delivery_id' => 'nullable|integer',
                'due_date' => 'required|date',
                'payment_terms' => 'nullable|string',
                'currency' => 'nullable|string',
                'invoice_amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'total_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:500',
                'attachment' => 'nullable|file|max:10240',
            ]);

            // Mock store logic - in real implementation, call Action
            // $id = app(CreatePurchaseInvoiceAction::class)->execute($validated);

            return response()->json([
                'message' => 'Purchase Invoice berhasil disimpan',
                'id' => 1, // Mock ID
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: '.implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'error' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }

    public function detail($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_invoice');

        // Mock purchase invoice data
        $purchaseInvoice = [
            'id' => $id,
            'reference_no' => 'PI-2025-001',
            'date' => '2025-07-10',
            'supplier' => 'CV Plastik Prima',
            'supplier_id' => 1,
            'status' => 'PAID',
            'due_date' => '2025-08-10',
            'po_reference' => 'PO-2025-001',
            'pd_reference' => 'PD-2025-001',
            'invoice_amount' => 25000000,
            'paid_amount' => 25000000,
            'remaining_amount' => 0,
            'currency' => 'IDR',
            'payment_terms' => 'Net 30',
            'tax_amount' => 2500000,
            'subtotal' => 22500000,
            'discount' => 0,
            'invoice_notes' => 'Invoice untuk pengiriman botol PET 330ml sesuai PO-2025-001',
            'payment_method' => 'Transfer Bank',
            'bank_account' => 'BCA 1234567890',
            'created_at' => '2025-07-10 09:00:00',
            'updated_at' => '2025-07-15 16:30:00',
        ];

        // Mock audit logs
        $auditLogs = [
            [
                'created_at' => '2025-07-15 16:30:00',
                'user_id' => 1,
                'user_name' => 'Finance Manager',
                'action_type' => 'UPDATE',
                'description' => 'Status invoice diubah dari PENDING_PAYMENT menjadi PAID, pembayaran Rp 25,000,000',
            ],
            [
                'created_at' => '2025-07-12 11:00:00',
                'user_id' => 2,
                'user_name' => 'Accounting Staff',
                'action_type' => 'UPDATE',
                'description' => 'Invoice dikonfirmasi dan siap untuk pembayaran',
            ],
            [
                'created_at' => '2025-07-10 09:00:00',
                'user_id' => 3,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Invoice dibuat berdasarkan PD-2025-001 dengan nilai Rp 25,000,000',
            ],
        ];

        $data = [
            'purchaseInvoice' => $purchaseInvoice,
            'auditLogs' => $auditLogs,
            'canEditPurchaseInvoice' => PermissionHelper::canWrite('main_transaction_purchase_invoice'),
            'canDeletePurchaseInvoice' => PermissionHelper::canDelete('main_transaction_purchase_invoice'),
            'canApprovePurchaseInvoice' => PermissionHelper::canApprove('main_transaction_purchase_invoice'),
        ];

        return view('pages.main_transaction.purchase_invoice.detail', array_merge($data, $permissionData));
    }

    public function auditLogDataTable($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock audit log data - in real implementation, get from audit log service
        $auditLogs = [
            [
                'created_at' => '2025-07-15 16:00:00',
                'user_id' => 1,
                'user_name' => 'Finance Staff',
                'action_type' => 'UPDATE',
                'description' => 'Status invoice diubah dari PENDING_PAYMENT menjadi PAID',
            ],
            [
                'created_at' => '2025-07-12 14:30:00',
                'user_id' => 2,
                'user_name' => 'Accounting Staff',
                'action_type' => 'UPDATE',
                'description' => 'Invoice amount diupdate dari Rp 20,000,000 menjadi Rp 25,000,000',
            ],
            [
                'created_at' => '2025-07-10 09:00:00',
                'user_id' => 3,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Invoice dibuat dengan total Rp 25,000,000 untuk PO-2025-001',
            ],
        ];

        return response()->json(['data' => $auditLogs], 200);
    }

    public function edit($id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_invoice');

        // Mock purchase invoice data
        $purchaseInvoice = [
            'id' => $id,
            'reference_no' => 'PI-2025-001',
            'date' => '2025-07-10',
            'supplier_id' => 1,
            'status' => 'PENDING_PAYMENT',
            'due_date' => '2025-08-10',
            'po_reference' => 'PO-2025-001',
            'pd_reference' => 'PD-2025-001',
            'invoice_amount' => 25000000,
            'paid_amount' => 0,
            'remaining_amount' => 25000000,
            'currency' => 'IDR',
            'payment_terms' => 'Net 30',
            'tax_amount' => 2500000,
            'subtotal' => 22500000,
            'discount' => 0,
            'invoice_notes' => 'Invoice untuk pengiriman botol PET 330ml sesuai PO-2025-001',
            'payment_method' => 'Transfer Bank',
            'bank_account' => 'BCA 1234567890',
        ];

        // Options for form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'SUP-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'SUP-003 - CV Kemasan Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - PT Supplier Bahan Kimia'],
        ];

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_PAYMENT', 'label' => 'PENDING_PAYMENT - Menunggu Pembayaran'],
            ['value' => 'PAID', 'label' => 'PAID - Lunas'],
            ['value' => 'PARTIAL_PAID', 'label' => 'PARTIAL_PAID - Sebagian Dibayar'],
            ['value' => 'OVERDUE', 'label' => 'OVERDUE - Jatuh Tempo'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        $purchaseOrders = [
            ['value' => 'PO-2025-001', 'label' => 'PO-2025-001 - CV Plastik Prima'],
            ['value' => 'PO-2025-002', 'label' => 'PO-2025-002 - PT Grafika Mandiri'],
            ['value' => 'PO-2025-003', 'label' => 'PO-2025-003 - CV Kemasan Sejahtera'],
        ];

        $purchaseDeliveries = [
            ['value' => 'PD-2025-001', 'label' => 'PD-2025-001 - CV Plastik Prima'],
            ['value' => 'PD-2025-002', 'label' => 'PD-2025-002 - PT Grafika Mandiri'],
            ['value' => 'PD-2025-003', 'label' => 'PD-2025-003 - CV Kemasan Sejahtera'],
        ];

        $currencies = [
            ['value' => 'IDR', 'label' => 'IDR - Rupiah'],
            ['value' => 'USD', 'label' => 'USD - Dollar'],
            ['value' => 'EUR', 'label' => 'EUR - Euro'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate'],
        ];

        $paymentMethods = [
            ['value' => 'Transfer Bank', 'label' => 'Transfer Bank'],
            ['value' => 'Cash', 'label' => 'Cash'],
            ['value' => 'Cek', 'label' => 'Cek'],
            ['value' => 'Giro', 'label' => 'Giro'],
        ];

        $bankAccounts = [
            ['value' => 'BCA 1234567890', 'label' => 'BCA 1234567890 - CV Plastik Prima'],
            ['value' => 'Mandiri 0987654321', 'label' => 'Mandiri 0987654321 - CV Plastik Prima'],
            ['value' => 'BNI 1122334455', 'label' => 'BNI 1122334455 - CV Plastik Prima'],
        ];

        $data = [
            'purchaseInvoice' => $purchaseInvoice,
            'suppliers' => $suppliers,
            'statuses' => $statuses,
            'purchaseOrders' => $purchaseOrders,
            'purchaseDeliveries' => $purchaseDeliveries,
            'currencies' => $currencies,
            'paymentTerms' => $paymentTerms,
            'paymentMethods' => $paymentMethods,
            'bankAccounts' => $bankAccounts,
            'canWrite' => PermissionHelper::canWrite('main_transaction_purchase_invoice'),
        ];

        return view('pages.main_transaction.purchase_invoice.edit', array_merge($data, $permissionData));
    }

    public function update(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'date' => 'required|date',
                'supplier_id' => 'required|integer',
                'status' => 'required|string',
                'due_date' => 'nullable|date',
                'po_reference' => 'required|string',
                'pd_reference' => 'nullable|string',
                'invoice_amount' => 'required|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string',
                'payment_terms' => 'nullable|string',
                'tax_amount' => 'nullable|numeric|min:0',
                'subtotal' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'invoice_notes' => 'nullable|string|max:500',
                'payment_method' => 'nullable|string',
                'bank_account' => 'nullable|string',
            ]);

            // Mock update logic - in real implementation, update database
            // app(UpdatePurchaseInvoiceAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => 'Purchase Invoice berhasil diperbarui',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: '.implode(', ', array_map(function ($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'error' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }

    // Import/Export Methods
    public function import(Request $request)
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }

        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
        ];

        return view('pages.main_transaction.purchase_invoice.import', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }

        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
        ];

        return view('pages.main_transaction.purchase_invoice.export', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Check write permission for template download
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Invoice akan segera tersedia.',
            'file_url' => '/templates/purchase_invoice_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import validation
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock validation - in real implementation, validate uploaded file
        $mockValidationResults = [
            'success' => true,
            'valid_count' => 4,
            'errors' => [],
            'valid_data' => [
                [
                    'reference_no' => 'PI-2025-100',
                    'date' => '2025-07-25',
                    'supplier' => 'CV Plastik Prima',
                    'po_reference' => 'PO-2025-001',
                    'invoice_amount' => 15000000,
                    'status' => 'DRAFT',
                ],
            ],
            'validation_key' => 'pi_validation_'.time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Check write permission for import processing
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 4,
            'message' => 'Purchase Invoice berhasil diimport ke sistem.',
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export processing
        if (! PermissionHelper::hasPermission('main_transaction_purchase_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_invoices_export_'.date('Y-m-d_H-i-s').'.'.$format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/'.$filename,
        ]);
    }
}
