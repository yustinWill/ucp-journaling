<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'canEditPurchaseDelivery' => $canEditPurchaseDelivery,
            'canViewUser' => PermissionHelper::canRead('master_data_user'),
            'canViewPurchaseOrder' => PermissionHelper::canRead('main_transaction_purchase_order')
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
                    "status_badge_class" => "bg-label-success",
                    "po_reference" => "PO-2025-001",
                    "po_id" => 1,
                    "total_items" => 1000,
                    "delivered_items" => 1000,
                    "created_by" => "Warehouse Staff",
                    "created_by_id" => "USR-003",
                    "tags" => [
                        ["name" => "Urgent", "preview" => "bg-label-warning"],
                        ["name" => "Bahan Baku", "preview" => "bg-label-primary"]
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
                    "status_badge_class" => "bg-label-warning",
                    "po_reference" => "PO-2025-002",
                    "po_id" => 2,
                    "total_items" => 500,
                    "delivered_items" => 0,
                    "created_by" => "Logistics Staff",
                    "created_by_id" => "USR-004",
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
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Get Purchase Orders with enhanced supplier information
        $purchaseOrders = [
            [
                'value' => '1', 
                'label' => 'PO-2025-001 - CV Plastik Prima',
                'supplier' => 'CV Plastik Prima',
                'supplier_id' => 1,
                'email' => 'plastik@prima.com',
                'address' => 'Jl. Industri No. 123, Jakarta Timur',
                'warehouse_id' => '1' // Default warehouse from PO
            ],
            [
                'value' => '2', 
                'label' => 'PO-2025-002 - PT Grafika Mandiri',
                'supplier' => 'PT Grafika Mandiri',
                'supplier_id' => 2,
                'email' => 'info@grafika.com',
                'address' => 'Jl. Percetakan Raya No. 45, Tangerang',
                'warehouse_id' => '2' // Default warehouse from PO
            ],
            [
                'value' => '3', 
                'label' => 'PO-2025-003 - CV Kemasan Sejahtera',
                'supplier' => 'CV Kemasan Sejahtera',
                'supplier_id' => 3,
                'email' => 'kemasan@sejahtera.co.id',
                'address' => 'Jl. Kemasan Indah No. 67, Bekasi',
                'warehouse_id' => '1' // Default warehouse from PO
            ]
        ];

        // Get Suppliers for manual entry mode
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - CV Plastik Prima', 'email' => 'plastik@prima.com', 'address' => 'Jl. Industri No. 123, Jakarta Timur'],
            ['value' => '2', 'label' => 'SUP-002 - PT Grafika Mandiri', 'email' => 'info@grafika.com', 'address' => 'Jl. Percetakan Raya No. 45, Tangerang'],
            ['value' => '3', 'label' => 'SUP-003 - CV Kemasan Sejahtera', 'email' => 'kemasan@sejahtera.co.id', 'address' => 'Jl. Kemasan Indah No. 67, Bekasi'],
            ['value' => '4', 'label' => 'SUP-004 - PT Supplier Bahan Kimia', 'email' => 'kimia@supplier.com', 'address' => 'Jl. Kimia Industri No. 89, Cikarang']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'GD-001 - Gudang Utama Jakarta'],
            ['value' => '2', 'label' => 'GD-002 - Gudang Produksi Tangerang'],
            ['value' => '3', 'label' => 'GD-003 - Gudang Distribusi Bekasi']
        ];

        $availableTags = [
            ['value' => '1', 'label' => 'Urgent', 'preview' => 'bg-label-danger'],
            ['value' => '2', 'label' => 'Normal', 'preview' => 'bg-label-primary'],
            ['value' => '3', 'label' => 'Bahan Baku', 'preview' => 'bg-label-info'],
            ['value' => '4', 'label' => 'Kemasan', 'preview' => 'bg-label-warning']
        ];

        // Permission checks
        $canAddSuppliers = PermissionHelper::canWrite('master_data_contact');
        $canAddStorages = PermissionHelper::canWrite('master_data_warehouse');
        $canAddTags = PermissionHelper::canWrite('master_data_tag');

        return view('pages.main_transaction.purchase_delivery.add', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'availableTags' => $availableTags,
            'canAddSuppliers' => $canAddSuppliers,
            'canAddStorages' => $canAddStorages,
            'canAddTags' => $canAddTags
        ]);
    }

    public function store(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menambah data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $deliveryMode = $request->input('delivery_mode', 'manual');
            
            // Base validation rules
            $validationRules = [
                'date' => 'required|date',
                'reference_no' => 'required|string|max:50',
                'delivery_mode' => 'required|in:po,manual',
                'warehouse_id' => 'required|integer',
                'supplier_reference' => 'nullable|string|max:100',
                'expected_date' => 'nullable|date',
                'tags' => 'nullable|array',
                'tags.*' => 'integer',
                'message' => 'nullable|string|max:500',
                'memo' => 'nullable|string|max:500',
                'attachment' => 'nullable|file|max:10240', // 10MB
                'products' => 'required|array|min:1'
            ];

            // Conditional validation rules based on delivery mode
            if ($deliveryMode === 'po') {
                // PO-based delivery validation
                $validationRules['purchase_order_id'] = 'required|integer';
                $validationRules['allow_over_delivery'] = 'nullable|boolean';
                $validationRules['products.*.product_id'] = 'required|integer';
                $validationRules['products.*.ordered_qty'] = 'required|numeric|min:0';
                $validationRules['products.*.delivered_qty'] = 'required|numeric|min:0';
                $validationRules['products.*.delivery_qty'] = 'required|numeric|min:1';
                $validationRules['products.*.unit'] = 'required|string';
                $validationRules['products.*.status'] = 'required|in:GOOD,DAMAGED,PARTIAL';
            } else {
                // Manual delivery validation
                $validationRules['supplier_id'] = 'required|integer';
                $validationRules['email'] = 'nullable|email';
                $validationRules['address'] = 'nullable|string|max:500';
                $validationRules['products.*.product_id'] = 'required|integer';
                $validationRules['products.*.quantity'] = 'required|numeric|min:1';
                $validationRules['products.*.unit'] = 'required|string';
                $validationRules['products.*.notes'] = 'nullable|string|max:200';
                $validationRules['products.*.status'] = 'required|in:GOOD,DAMAGED,PARTIAL';
            }

            $validated = $request->validate($validationRules);

            // Additional validation for PO mode
            if ($deliveryMode === 'po' && !$request->input('allow_over_delivery', false)) {
                foreach ($validated['products'] as $product) {
                    $remaining = $product['ordered_qty'] - $product['delivered_qty'];
                    if ($product['delivery_qty'] > $remaining) {
                        return response()->json([
                            'message' => 'Kuantitas pengiriman tidak boleh melebihi sisa yang belum dikirim. Aktifkan "Izinkan Over Delivery" jika diperlukan.',
                            'error' => 'VALIDATION_ERROR',
                        ], 422);
                    }
                }
            }

            // Use transaction to ensure data consistency
            DB::beginTransaction();

            try {
                // Prepare data for service layer
                $deliveryData = [
                    'reference_no' => $validated['reference_no'],
                    'date' => $validated['date'],
                    'warehouse_id' => $validated['warehouse_id'],
                    'supplier_reference' => $validated['supplier_reference'] ?? null,
                    'expected_date' => $validated['expected_date'] ?? null,
                    'message' => $validated['message'] ?? null,
                    'memo' => $validated['memo'] ?? null,
                    'attachment' => null, // Handle file upload separately
                    'delivery_mode' => $deliveryMode,
                    'status' => 'PENDING'
                ];

                // Add mode-specific data
                if ($deliveryMode === 'po') {
                    $deliveryData['purchase_order_id'] = $validated['purchase_order_id'];
                    $deliveryData['allow_over_delivery'] = $request->input('allow_over_delivery', false);
                    // Get supplier from PO (in real implementation, fetch from database)
                    $deliveryData['supplier_id'] = 1; // Mock supplier ID from PO
                } else {
                    $deliveryData['supplier_id'] = $validated['supplier_id'];
                    $deliveryData['email'] = $validated['email'] ?? null;
                    $deliveryData['address'] = $validated['address'] ?? null;
                }

                // Create purchase delivery record
                $deliveryService = app(\App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService::class);
                $deliveryId = $deliveryService->create($deliveryData);

                // Create delivery products based on mode
                if ($deliveryMode === 'po') {
                    // PO-based products
                    $productData = [];
                    foreach ($validated['products'] as $product) {
                        if ($product['delivery_qty'] > 0) {
                            $productData[] = [
                                'product_id' => $product['product_id'],
                                'ordered_qty' => $product['ordered_qty'],
                                'delivered_qty' => $product['delivered_qty'],
                                'delivery_qty' => $product['delivery_qty'],
                                'unit' => $product['unit'],
                                'status' => $product['status']
                            ];
                        }
                    }
                } else {
                    // Manual products
                    $productData = [];
                    foreach ($validated['products'] as $product) {
                        if ($product['quantity'] > 0) {
                            $productData[] = [
                                'product_id' => $product['product_id'],
                                'quantity' => $product['quantity'],
                                'unit' => $product['unit'],
                                'notes' => $product['notes'] ?? null,
                                'status' => $product['status']
                            ];
                        }
                    }
                }

                $deliveryService->createDeliveryProducts($deliveryId, $productData);

                // Handle tags if provided
                if (!empty($validated['tags'])) {
                    // In real implementation, create delivery tag relationships
                    // DB::table('t_purchase_delivery_tags')->insert($tagData);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Purchase Delivery berhasil dibuat',
                    'id' => $deliveryId,
                    'mode' => $deliveryMode
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

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

    public function auditLogDataTable($id)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock audit log data - in real implementation, get from audit log service
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

        return response()->json(['data' => $auditLogs], 200);
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
            'canWrite' => PermissionHelper::canWrite('main_transaction_purchase_delivery'),
            'canAddSuppliers' => PermissionHelper::canWrite('master_data_contact'),
            'canAddStorages' => PermissionHelper::canWrite('master_data_warehouse')
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
                'purchase_order_id' => 'required|integer',
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

    public function printPDF($poId)
    {
        // Check read permission for PDF generation
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak PDF.');
        }

        // Mock PDF generation - in real implementation, generate PDF document
        // This would typically involve:
        // 1. Fetch PO data and related products
        // 2. Generate PDF using a library like TCPDF or DomPDF
        // 3. Return the PDF as a downloadable response
        
        $filename = 'purchase_delivery_form_PO-' . $poId . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // For now, return a JSON response indicating success
        // In production, this would return: return response()->file($pdfPath);
        return response()->json([
            'success' => true,
            'message' => 'PDF berhasil digenerate.',
            'filename' => $filename,
            'download_url' => '/temp/' . $filename
        ]);
    }

    public function getPOProducts(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $poId = $request->input('po_id');
        if (!$poId) {
            return response()->json(['error' => 'PO ID required'], 400);
        }

        // Mock PO products data - in real implementation, fetch from database
        $products = [
            [
                'product_id' => 1,
                'product_name' => 'Botol PET 330ml',
                'unit' => 'pcs',
                'ordered_qty' => 1000,
                'delivered_qty' => 400,
                'remaining_qty' => 600
            ],
            [
                'product_id' => 2,
                'product_name' => 'Label Kemasan',
                'unit' => 'lembar',
                'ordered_qty' => 500,
                'delivered_qty' => 200,
                'remaining_qty' => 300
            ],
            [
                'product_id' => 3,
                'product_name' => 'Tutup Botol',
                'unit' => 'pcs',
                'ordered_qty' => 800,
                'delivered_qty' => 800,
                'remaining_qty' => 0
            ]
        ];

        return response()->json(['products' => $products]);
    }

    public function getPreviousDeliveries(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $poId = $request->input('po_id');
        if (!$poId) {
            return response()->json(['error' => 'PO ID required'], 400);
        }

        // Mock previous deliveries data - in real implementation, fetch from database
        $deliveries = [
            [
                'reference_no' => 'PD-2025-001',
                'date' => '2025-07-10',
                'status' => 'DELIVERED',
                'total_items' => 300
            ],
            [
                'reference_no' => 'PD-2025-002',
                'date' => '2025-07-15',
                'status' => 'DELIVERED',
                'total_items' => 100
            ]
        ];

        return response()->json(['deliveries' => $deliveries]);
    }

    public function getProducts(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $supplierId = $request->input('supplier_id');
        if (!$supplierId) {
            return response()->json(['error' => 'Supplier ID required'], 400);
        }

        // Mock products data - in real implementation, fetch from database
        $products = [
            [
                'product_id' => 1,
                'product_name' => 'PRD-001 - Botol PET 330ml',
                'unit' => 'pcs'
            ],
            [
                'product_id' => 2,
                'product_name' => 'PRD-002 - Label Kemasan',
                'unit' => 'lembar'
            ],
            [
                'product_id' => 3,
                'product_name' => 'PRD-003 - Tutup Botol',
                'unit' => 'pcs'
            ],
            [
                'product_id' => 4,
                'product_name' => 'PRD-004 - Kardus Packaging',
                'unit' => 'dus'
            ]
        ];

        return response()->json(['products' => $products]);
    }
}