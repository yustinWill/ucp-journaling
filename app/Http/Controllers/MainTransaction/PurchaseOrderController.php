<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        // Check read permission for purchase order module
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');
        
        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        // Map permissions to view variables
        $canAddPurchaseOrder = PermissionHelper::canWrite('main_transaction_purchase_order');
        $canImportPurchaseOrder = PermissionHelper::canWrite('main_transaction_purchase_order');
        $canExportPurchaseOrder = PermissionHelper::canRead('main_transaction_purchase_order');
        $canViewPurchaseOrder = PermissionHelper::canRead('main_transaction_purchase_order');
        $canEditPurchaseOrder = PermissionHelper::canWrite('main_transaction_purchase_order');

        $data = [
            'statuses' => $statuses,
            'canAddPurchaseOrder' => $canAddPurchaseOrder,
            'canImportPurchaseOrder' => $canImportPurchaseOrder,
            'canExportPurchaseOrder' => $canExportPurchaseOrder,
            'canViewPurchaseOrder' => $canViewPurchaseOrder,
            'canEditPurchaseOrder' => $canEditPurchaseOrder
        ];
        
        return view('pages.main_transaction.purchase_order.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock data specific to Purchase Orders
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "PO-2025-001",
                    "supplier" => "CV Plastik Prima",
                    "supplier_id" => "CNT-001",
                    "due_date" => "2025-07-15",
                    "status" => "Disetujui",
                    "status_badge_class" => "bg-label-success",
                    "down_payment" => 5000000,
                    "total" => 25000000,
                    "created_by" => "Ahmad Supardi",
                    "created_by_id" => "USR-001",
                    "tags" => [
                        ["name" => "Bahan Baku", "preview" => "bg-label-primary"],
                        ["name" => "Urgent", "preview" => "bg-label-warning"]
                    ],
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PO-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "supplier_id" => "CNT-002",
                    "due_date" => "2025-07-16",
                    "status" => "Draft",
                    "status_badge_class" => "bg-label-secondary",
                    "down_payment" => 3000000,
                    "total" => 15000000,
                    "created_by" => "Siti Nurhaliza",
                    "created_by_id" => "USR-002",
                    "tags" => [
                        ["name" => "Kemasan", "preview" => "bg-label-success"]
                    ],
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Get required options for the Purchase Order form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'SUP-002 - CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'SUP-003 - PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - CV. Maju Bersama']
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate'],
            ['value' => 'Custom', 'label' => 'Custom - Manual Date Entry']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'GDG-001 - Gudang Utama'],
            ['value' => '2', 'label' => 'GDG-002 - Gudang Cabang A'],
            ['value' => '3', 'label' => 'GDG-003 - Gudang Cabang B']
        ];

        $products = [
            ['value' => '1', 'label' => 'PRD-001 - Botol PET 330ml'],
            ['value' => '2', 'label' => 'PRD-002 - Label Kemasan'],
            ['value' => '3', 'label' => 'PRD-003 - Tutup Botol'],
            ['value' => '4', 'label' => 'PRD-004 - Kardus Packaging']
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN - 11%'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak']
        ];

        $availableTags = [
            ['value' => 'PO001', 'label' => 'PO001 - Bahan Baku', 'preview' => 'bg-label-primary'],
            ['value' => 'PO002', 'label' => 'PO002 - Kemasan', 'preview' => 'bg-label-success'],
            ['value' => 'PO003', 'label' => 'PO003 - Urgent', 'preview' => 'bg-label-warning'],
            ['value' => 'PO004', 'label' => 'PO004 - Regular', 'preview' => 'bg-label-info'],
            ['value' => 'PO005', 'label' => 'PO005 - Seasonal', 'preview' => 'bg-label-secondary']
        ];

        // Check permissions for adding related data
        $canAddSuppliers = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_transaction_number', 'write');
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddWarehouses = PermissionHelper::hasPermission('master_data_storage', 'write');

        return view('pages.main_transaction.purchase_order.add', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions,
            'availableTags' => $availableTags,
            'canAddSuppliers' => $canAddSuppliers,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses
        ]);
    }

    public function store(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menambah data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'supplier_id' => 'required|integer',
                'due_date' => 'nullable|date',
                'payment_terms' => 'nullable|string',
                'warehouse_id' => 'nullable|integer',
                'currency' => 'nullable|string',
                'down_payment' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'payment_method' => 'nullable|string',
                'tax' => 'nullable|string',
                'notes' => 'nullable|string|max:500'
            ]);

            // Mock store logic - in real implementation, call Action
            // $id = app(CreatePurchaseOrderAction::class)->execute($validated);

            return response()->json([
                'message' => 'Purchase Order berhasil disimpan',
                'id' => 1 // Mock ID
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: ' . implode(', ', array_map(function($errors) {
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
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');
        
        // Mock purchase order data
        $purchaseOrder = [
            'id' => $id,
            'reference_no' => 'PO-2025-001',
            'date' => '2025-07-01',
            'supplier' => 'CV Plastik Prima',
            'supplier_id' => 1,
            'status' => 'APPROVED',
            'due_date' => '2025-07-15',
            'description' => 'Purchase Order untuk pengadaan bahan baku kemasan botol PET 330ml dan label kemasan untuk produk RIYO Water bulan Juli 2025',
            'type' => 'Purchase Order',
            'down_payment' => 5000000,
            'total' => 25000000,
            'remaining_bill' => 20000000,
            'currency' => 'IDR',
            'warehouse' => 'Gudang Utama',
            'pic' => 'Ahmad Supardi',
            'payment_method' => 'Transfer Bank',
            'payment_terms' => 'Net 30',
            'tags' => [
                ['name' => 'Bahan Baku', 'preview' => 'bg-label-primary'],
                ['name' => 'Urgent', 'preview' => 'bg-label-warning']
            ],
            'memo' => 'Catatan internal: PO ini terkait dengan peningkatan produksi untuk mengantisipasi peningkatan permintaan di musim kemarau.',
            'notes' => 'Purchase Order untuk botol PET dan kemasan RIYO Water',
            'created_at' => '2025-07-01 08:00:00',
            'updated_at' => '2025-07-01 10:30:00',
            'product_lines' => [
                [
                    'product_name' => 'PRD-001 - Botol PET 330ml',
                    'description' => 'Botol plastik PET untuk kemasan air mineral 330ml',
                    'quantity' => 10000,
                    'unit' => 'pcs',
                    'unit_price' => 150,
                    'tax_name' => 'PPN 11%',
                    'total' => 1500000
                ],
                [
                    'product_name' => 'PRD-002 - Label Kemasan',
                    'description' => 'Label kemasan dengan logo RIYO Water',
                    'quantity' => 10000,
                    'unit' => 'lembar',
                    'unit_price' => 75,
                    'tax_name' => 'PPN 11%',
                    'total' => 750000
                ]
            ]
        ];
        
        // Mock audit logs
        $auditLogs = [
            [
                'created_at' => '2025-07-01 10:30:00',
                'user_id' => 1,
                'user_name' => 'Admin System',
                'action_type' => 'UPDATE',
                'description' => 'Status PO diubah dari PENDING_APPROVAL menjadi APPROVED'
            ],
            [
                'created_at' => '2025-07-01 08:00:00',
                'user_id' => 2,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Order dibuat dengan total Rp 25,000,000'
            ]
        ];
        
        $data = [
            'purchaseOrder' => $purchaseOrder,
            'auditLogs' => $auditLogs,
            'canEditPurchaseOrder' => PermissionHelper::canWrite('main_transaction_purchase_order'),
            'canDeletePurchaseOrder' => PermissionHelper::canDelete('main_transaction_purchase_order'),
            'canApprovePurchaseOrder' => PermissionHelper::canApprove('main_transaction_purchase_order')
        ];
        
        return view('pages.main_transaction.purchase_order.detail', array_merge($data, $permissionData));
    }

    public function auditLogDataTable($id)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock audit log data - in real implementation, get from audit log service
        $auditLogs = [
            [
                'created_at' => '2025-07-01 10:30:00',
                'user_id' => 1,
                'user_name' => 'Admin System',
                'action_type' => 'UPDATE',
                'description' => 'Status PO diubah dari PENDING_APPROVAL menjadi APPROVED'
            ],
            [
                'created_at' => '2025-07-01 08:00:00',
                'user_id' => 2,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Order dibuat dengan total Rp 25,000,000'
            ]
        ];

        return response()->json(['data' => $auditLogs], 200);
    }

    public function edit($id)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');
        
        // Mock purchase order data
        $purchaseOrder = [
            'id' => $id,
            'reference_no' => 'PO-2025-001',
            'date' => '2025-07-01',
            'supplier_id' => 1,
            'status' => 'DRAFT',
            'due_date' => '2025-07-15',
            'description' => 'Purchase Order untuk pengadaan bahan baku kemasan botol PET 330ml dan label kemasan untuk produk RIYO Water bulan Juli 2025',
            'down_payment' => 5000000,
            'total' => 25000000,
            'currency' => 'IDR',
            'warehouse_id' => 1,
            'pic_id' => 1,
            'payment_method' => 'Transfer Bank',
            'payment_terms' => 'Net 30',
            'tags' => [
                ['code' => 'PO001', 'name' => 'Bahan Baku', 'preview' => 'bg-label-primary'],
                ['code' => 'PO003', 'name' => 'Urgent', 'preview' => 'bg-label-warning']
            ],
            'memo' => 'Catatan internal: PO ini terkait dengan peningkatan produksi untuk mengantisipasi peningkatan permintaan di musim kemarau.',
            'notes' => 'Purchase Order untuk botol PET dan kemasan RIYO Water'
        ];
        
        // Options for form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'SUP-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'SUP-003 - CV Kemasan Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - PT Supplier Bahan Kimia']
        ];

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        $currencies = [
            ['value' => 'IDR', 'label' => 'IDR - Rupiah'],
            ['value' => 'USD', 'label' => 'USD - Dollar'],
            ['value' => 'EUR', 'label' => 'EUR - Euro']
        ];

        $paymentMethods = [
            ['value' => 'Transfer Bank', 'label' => 'Transfer Bank'],
            ['value' => 'Cash', 'label' => 'Cash'],
            ['value' => 'Cek', 'label' => 'Cek'],
            ['value' => 'Giro', 'label' => 'Giro']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'GDG-001 - Gudang Utama'],
            ['value' => '2', 'label' => 'GDG-002 - Gudang Cabang A'],
            ['value' => '3', 'label' => 'GDG-003 - Gudang Cabang B']
        ];

        $picOptions = [
            ['value' => '1', 'label' => 'USR-001 - Ahmad Supardi'],
            ['value' => '2', 'label' => 'USR-002 - Siti Nurhaliza'],
            ['value' => '3', 'label' => 'USR-003 - Budi Santoso']
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate'],
            ['value' => 'Custom', 'label' => 'Custom - Manual Date Entry']
        ];

        $availableTags = [
            ['value' => 'PO001', 'label' => 'PO001 - Bahan Baku', 'preview' => 'bg-label-primary'],
            ['value' => 'PO002', 'label' => 'PO002 - Kemasan', 'preview' => 'bg-label-success'],
            ['value' => 'PO003', 'label' => 'PO003 - Urgent', 'preview' => 'bg-label-warning'],
            ['value' => 'PO004', 'label' => 'PO004 - Regular', 'preview' => 'bg-label-info'],
            ['value' => 'PO005', 'label' => 'PO005 - Seasonal', 'preview' => 'bg-label-secondary']
        ];

        // Check permissions for adding related data
        $canAddSuppliers = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_transaction_number', 'write');
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddWarehouses = PermissionHelper::hasPermission('master_data_storage', 'write');
        $canAddPIC = PermissionHelper::hasPermission('master_data_user', 'write');
        
        // Ensure all fields have default values
        $purchaseOrder['description'] = $purchaseOrder['description'] ?? '';
        $purchaseOrder['memo'] = $purchaseOrder['memo'] ?? '';
        
        $data = [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'statuses' => $statuses,
            'currencies' => $currencies,
            'paymentMethods' => $paymentMethods,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'paymentTerms' => $paymentTerms,
            'availableTags' => $availableTags,
            'canWrite' => PermissionHelper::canWrite('main_transaction_purchase_order'),
            'canAddSuppliers' => $canAddSuppliers,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses,
            'canAddPIC' => $canAddPIC
        ];
        
        return view('pages.main_transaction.purchase_order.edit', array_merge($data, $permissionData));
    }

    public function update(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
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
                'down_payment' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'currency' => 'nullable|string',
                'payment_method' => 'nullable|string',
                'warehouse_id' => 'nullable|integer',
                'pic_id' => 'nullable|integer',
                'payment_terms' => 'nullable|string',
                'notes' => 'nullable|string|max:500'
            ]);

            // Mock update logic - in real implementation, update database
            // app(UpdatePurchaseOrderAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => 'Purchase Order berhasil diperbarui'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: ' . implode(', ', array_map(function($errors) {
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
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }

        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Bahan Baku'],
            ['value' => '2', 'label' => 'Gudang Produksi'],
            ['value' => '3', 'label' => 'Gudang Finished Goods']
        ];

        return view('pages.main_transaction.purchase_order.import', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Check write permission for template download
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Order akan segera tersedia.',
            'file_url' => '/templates/purchase_order_template.xlsx'
        ]);
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import validation
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock validation - in real implementation, validate uploaded file
        $mockValidationResults = [
            'success' => true,
            'valid_count' => 5,
            'errors' => [],
            'valid_data' => [
                [
                    'reference_no' => 'PO-2025-100',
                    'date' => '2025-07-25',
                    'supplier' => 'CV Plastik Prima',
                    'total' => 15000000,
                    'status' => 'DRAFT'
                ]
            ],
            'validation_key' => 'po_validation_' . time()
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Check write permission for import processing
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 5,
            'message' => 'Purchase Order berhasil diimport ke sistem.'
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }

        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Bahan Baku'],
            ['value' => '2', 'label' => 'Gudang Produksi'],
            ['value' => '3', 'label' => 'Gudang Finished Goods']
        ];

        return view('pages.main_transaction.purchase_order.export', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export processing
        if (!PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_orders_export_' . date('Y-m-d_H-i-s') . '.' . $format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/' . $filename
        ]);
    }
}