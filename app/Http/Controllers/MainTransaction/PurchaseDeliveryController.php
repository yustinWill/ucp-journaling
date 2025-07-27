<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;

class PurchaseDeliveryController extends Controller
{
    public function index()
    {
        // Check read permission for purchase delivery module
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_delivery');
        
        $statuses = [
            ['value' => 'PENDING', 'label' => 'PENDING - Menunggu'],
            ['value' => 'IN_TRANSIT', 'label' => 'IN_TRANSIT - Dalam Perjalanan'],
            ['value' => 'DELIVERED', 'label' => 'DELIVERED - Terkirim'],
            ['value' => 'PARTIAL_DELIVERED', 'label' => 'PARTIAL_DELIVERED - Sebagian Terkirim'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        // Map permissions to view variables
        $canAddPurchaseDelivery = PermissionHelper::canWrite('main_transaction_purchase_delivery');
        $canImportPurchaseDelivery = PermissionHelper::canWrite('main_transaction_purchase_delivery');
        $canExportPurchaseDelivery = PermissionHelper::canRead('main_transaction_purchase_delivery');
        $canViewPurchaseDelivery = PermissionHelper::canRead('main_transaction_purchase_delivery');
        $canEditPurchaseDelivery = PermissionHelper::canWrite('main_transaction_purchase_delivery');

        $data = [
            'statuses' => $statuses,
            'canAddPurchaseDelivery' => $canAddPurchaseDelivery,
            'canImportPurchaseDelivery' => $canImportPurchaseDelivery,
            'canExportPurchaseDelivery' => $canExportPurchaseDelivery,
            'canViewPurchaseDelivery' => $canViewPurchaseDelivery,
            'canEditPurchaseDelivery' => $canEditPurchaseDelivery
        ];
        
        return view('pages.main_transaction.purchase_delivery.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock data specific to Purchase Deliveries
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-05",
                    "reference_no" => "PD-2025-001",
                    "supplier" => "CV Plastik Prima",
                    "supplier_id" => 1,
                    "delivery_date" => "2025-07-10",
                    "status" => "DELIVERED",
                    "po_reference" => "PO-2025-001",
                    "total_items" => 1000,
                    "delivered_items" => 1000,
                    "tags" => [
                        ["name" => "Urgent", "color" => "warning"],
                        ["name" => "Bahan Baku", "color" => "primary"]
                    ],
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-06",
                    "reference_no" => "PD-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "supplier_id" => 2,
                    "delivery_date" => "2025-07-12",
                    "status" => "IN_TRANSIT",
                    "po_reference" => "PO-2025-002",
                    "total_items" => 500,
                    "delivered_items" => 0,
                    "tags" => [
                        ["name" => "Kemasan", "color" => "success"]
                    ],
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Get required options for the Purchase Delivery form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'SUP-002 - CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'SUP-003 - PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - CV. Maju Bersama']
        ];

        $purchaseOrders = [
            ['value' => '1', 'label' => 'PO-2025-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'PO-2025-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'PO-2025-003 - CV Kemasan Sejahtera']
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

        $carriers = [
            ['value' => 'Internal', 'label' => 'Internal Transport'],
            ['value' => 'JNE', 'label' => 'JNE Express'],
            ['value' => 'TIKI', 'label' => 'TIKI Delivery'],
            ['value' => 'Pos Indonesia', 'label' => 'Pos Indonesia']
        ];

        return view('pages.main_transaction.purchase_delivery.add', [
            'suppliers' => $suppliers,
            'purchaseOrders' => $purchaseOrders,
            'warehouses' => $warehouses,
            'products' => $products,
            'carriers' => $carriers
        ]);
    }

    public function detail($id)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_delivery');
        
        // Mock purchase delivery data
        $purchaseDelivery = [
            'id' => $id,
            'reference_no' => 'PD-2025-001',
            'date' => '2025-07-05',
            'supplier' => 'CV Plastik Prima',
            'supplier_id' => 1,
            'status' => 'DELIVERED',
            'delivery_date' => '2025-07-10',
            'po_reference' => 'PO-2025-001',
            'carrier' => 'Internal Transport',
            'tracking_number' => 'INT-2025-0001',
            'total_items' => 1000,
            'delivered_items' => 1000,
            'warehouse' => 'Gudang Utama',
            'received_by' => 'Ahmad Supardi',
            'delivery_notes' => 'Pengiriman botol PET 330ml sesuai PO-2025-001, kondisi barang baik',
            'created_at' => '2025-07-05 08:00:00',
            'updated_at' => '2025-07-10 14:30:00'
        ];
        
        // Mock audit logs
        $auditLogs = [
            [
                'created_at' => '2025-07-10 14:30:00',
                'user_id' => 1,
                'user_name' => 'Warehouse Staff',
                'action_type' => 'UPDATE',
                'description' => 'Status pengiriman diubah dari IN_TRANSIT menjadi DELIVERED'
            ],
            [
                'created_at' => '2025-07-08 10:00:00',
                'user_id' => 2,
                'user_name' => 'Logistics Coordinator',
                'action_type' => 'UPDATE',
                'description' => 'Status pengiriman diubah dari PENDING menjadi IN_TRANSIT'
            ],
            [
                'created_at' => '2025-07-05 08:00:00',
                'user_id' => 3,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Delivery dibuat untuk PO-2025-001 dengan 1000 items'
            ]
        ];
        
        $data = [
            'purchaseDelivery' => $purchaseDelivery,
            'auditLogs' => $auditLogs,
            'canEditPurchaseDelivery' => PermissionHelper::canWrite('main_transaction_purchase_delivery'),
            'canDeletePurchaseDelivery' => PermissionHelper::canDelete('main_transaction_purchase_delivery'),
            'canApprovePurchaseDelivery' => PermissionHelper::canApprove('main_transaction_purchase_delivery')
        ];
        
        return view('pages.main_transaction.purchase_delivery.detail', array_merge($data, $permissionData));
    }

    public function edit($id)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_delivery');
        
        // Mock purchase delivery data
        $purchaseDelivery = [
            'id' => $id,
            'reference_no' => 'PD-2025-001',
            'date' => '2025-07-05',
            'supplier_id' => 1,
            'status' => 'IN_TRANSIT',
            'delivery_date' => '2025-07-10',
            'po_reference' => 'PO-2025-001',
            'carrier' => 'Internal Transport',
            'tracking_number' => 'INT-2025-0001',
            'total_items' => 1000,
            'delivered_items' => 800,
            'warehouse_id' => 1,
            'received_by' => 'Ahmad Supardi',
            'delivery_notes' => 'Pengiriman botol PET 330ml sesuai PO-2025-001'
        ];
        
        // Options for form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'SUP-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'SUP-003 - CV Kemasan Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - PT Supplier Bahan Kimia']
        ];

        $statuses = [
            ['value' => 'PENDING', 'label' => 'PENDING - Menunggu'],
            ['value' => 'IN_TRANSIT', 'label' => 'IN_TRANSIT - Dalam Perjalanan'],
            ['value' => 'DELIVERED', 'label' => 'DELIVERED - Terkirim'],
            ['value' => 'PARTIAL_DELIVERED', 'label' => 'PARTIAL_DELIVERED - Sebagian Terkirim'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        $purchaseOrders = [
            ['value' => 'PO-2025-001', 'label' => 'PO-2025-001 - CV Plastik Prima'],
            ['value' => 'PO-2025-002', 'label' => 'PO-2025-002 - PT Grafika Mandiri'],
            ['value' => 'PO-2025-003', 'label' => 'PO-2025-003 - CV Kemasan Sejahtera']
        ];

        $carriers = [
            ['value' => 'Internal Transport', 'label' => 'Internal Transport'],
            ['value' => 'JNE Express', 'label' => 'JNE Express'],
            ['value' => 'TIKI Delivery', 'label' => 'TIKI Delivery'],
            ['value' => 'Pos Indonesia', 'label' => 'Pos Indonesia']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'GDG-001 - Gudang Utama'],
            ['value' => '2', 'label' => 'GDG-002 - Gudang Cabang A'],
            ['value' => '3', 'label' => 'GDG-003 - Gudang Cabang B']
        ];

        $receiverOptions = [
            ['value' => 'Ahmad Supardi', 'label' => 'Ahmad Supardi'],
            ['value' => 'Siti Nurhaliza', 'label' => 'Siti Nurhaliza'],
            ['value' => 'Budi Santoso', 'label' => 'Budi Santoso']
        ];
        
        $data = [
            'purchaseDelivery' => $purchaseDelivery,
            'suppliers' => $suppliers,
            'statuses' => $statuses,
            'purchaseOrders' => $purchaseOrders,
            'carriers' => $carriers,
            'warehouses' => $warehouses,
            'receiverOptions' => $receiverOptions,
            'canWrite' => PermissionHelper::canWrite('main_transaction_purchase_delivery')
        ];
        
        return view('pages.main_transaction.purchase_delivery.edit', array_merge($data, $permissionData));
    }

    public function update(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
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
                'delivery_date' => 'nullable|date',
                'po_reference' => 'required|string',
                'carrier' => 'nullable|string',
                'tracking_number' => 'nullable|string',
                'total_items' => 'required|integer|min:0',
                'delivered_items' => 'required|integer|min:0',
                'warehouse_id' => 'nullable|integer',
                'received_by' => 'nullable|string',
                'delivery_notes' => 'nullable|string|max:500'
            ]);

            // Mock update logic - in real implementation, update database
            // app(UpdatePurchaseDeliveryAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => 'Purchase Delivery berhasil diperbarui'
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
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
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

        return view('pages.main_transaction.purchase_delivery.import', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Check write permission for template download
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Delivery akan segera tersedia.',
            'file_url' => '/templates/purchase_delivery_template.xlsx'
        ]);
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import validation
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock validation - in real implementation, validate uploaded file
        $mockValidationResults = [
            'success' => true,
            'valid_count' => 3,
            'errors' => [],
            'valid_data' => [
                [
                    'reference_no' => 'PD-2025-100',
                    'date' => '2025-07-25',
                    'supplier' => 'CV Plastik Prima',
                    'po_reference' => 'PO-2025-001',
                    'total_items' => 500,
                    'status' => 'PENDING'
                ]
            ],
            'validation_key' => 'pd_validation_' . time()
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Check write permission for import processing
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 3,
            'message' => 'Purchase Delivery berhasil diimport ke sistem.'
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
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

        return view('pages.main_transaction.purchase_delivery.export', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export processing
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_deliveries_export_' . date('Y-m-d_H-i-s') . '.' . $format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/' . $filename
        ]);
    }
}