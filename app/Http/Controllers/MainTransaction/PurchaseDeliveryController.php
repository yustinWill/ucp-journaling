<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\PurchaseDelivery\Actions\CreatePurchaseDeliveryAction;
use App\Domains\PurchaseDelivery\Actions\UpdatePurchaseDeliveryAction;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryAuditLogService;
// Domain Services
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
// Enums
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseDeliveryController extends Controller
{
    public function index()
    {
        // Check read permission for purchase delivery module
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_delivery');

        $suppliers = app(ContactService::class)->findAllByType('SUPPLIER');

        // Map permissions to view variables
        $canAddPurchaseDelivery = PermissionHelper::canWrite('main_transaction_purchase_delivery');
        $canImportPurchaseDelivery = PermissionHelper::canWrite('main_transaction_purchase_delivery');
        $canExportPurchaseDelivery = PermissionHelper::canRead('main_transaction_purchase_delivery');
        $canViewPurchaseDelivery = PermissionHelper::canRead('main_transaction_purchase_delivery');
        $canEditPurchaseDelivery = PermissionHelper::canWrite('main_transaction_purchase_delivery');

        $data = [
            'suppliers' => $suppliers,
            'canAddPurchaseDelivery' => $canAddPurchaseDelivery,
            'canImportPurchaseDelivery' => $canImportPurchaseDelivery,
            'canExportPurchaseDelivery' => $canExportPurchaseDelivery,
            'canViewPurchaseDelivery' => $canViewPurchaseDelivery,
            'canEditPurchaseDelivery' => $canEditPurchaseDelivery,
            'canViewUser' => PermissionHelper::canRead('master_data_user'),
            'canViewPurchaseOrder' => PermissionHelper::canRead('main_transaction_purchase_order'),
        ];

        return view('pages.main_transaction.purchase_delivery.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(PurchaseDeliveryService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Check if coming from PO detail page
        $preselectedPOId = $request->get('po_id');

        // Get available Purchase Orders for delivery
        $purchaseOrder = app(PurchaseOrderService::class)->findOne($preselectedPOId);
        $purchaseOrderProducts = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($preselectedPOId);

        $warehouses = app(WarehouseService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Permission checks for related data creation
        $canAddStorages = PermissionHelper::canWrite('master_data_warehouse');
        $canAddTags = PermissionHelper::canWrite('master_data_tag');

        return view('pages.main_transaction.purchase_delivery.add', [
            'purchaseOrder' => $purchaseOrder,
            'purchaseOrderProducts' => $purchaseOrderProducts,
            'warehouses' => $warehouses,
            'availableTags' => $availableTags,
            'canAddStorages' => $canAddStorages,
            'canAddTags' => $canAddTags,
            'preselectedPOId' => $preselectedPOId,
        ]);
    }

    public function store(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menambah data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $data = $request->all();

            // Use action for validation and creation
            $createAction = app(CreatePurchaseDeliveryAction::class);
            $validation = $createAction->validatePurchaseDeliveryData($data);

            if (! $validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Execute creation
            $deliveryId = $createAction->execute($data);

            return response()->json([
                'message' => SuccessCodes::CREATE_PURCHASE_DELIVERY_SUCCESS->message(),
            ], SuccessCodes::CREATE_PURCHASE_DELIVERY_SUCCESS->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function detail($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }

        $purchaseDelivery = app(PurchaseDeliveryService::class)->findOne($id);
        if (! $purchaseDelivery) {
            abort(404, 'Purchase Delivery not found');
        }

        // Load audit logs
        $auditLogs = app(PurchaseDeliveryAuditLogService::class)->findByPurchaseDeliveryId($id);

        return view('pages.main_transaction.purchase_delivery.detail', [
            'purchaseDelivery' => $purchaseDelivery,
            'auditLogs' => $auditLogs,
            'canEdit' => PermissionHelper::canWrite('main_transaction_purchase_delivery') && in_array($purchaseDelivery->status, ['PENDING', 'IN_TRANSIT']),
            'canDelete' => PermissionHelper::canDelete('main_transaction_purchase_delivery') && $purchaseDelivery->status === 'PENDING',
            'canCancel' => PermissionHelper::canWrite('main_transaction_purchase_delivery') && in_array($purchaseDelivery->status, ['PENDING', 'IN_TRANSIT']),
        ]);
    }

    public function auditLogDataTable($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(PurchaseDeliveryAuditLogService::class)->findAllDatatableByPurchaseDeliveryId($id);

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function edit($id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        $purchaseDelivery = app(PurchaseDeliveryService::class)->findOne($id);
        if (! $purchaseDelivery) {
            abort(404, 'Purchase Delivery not found');
        }

        // Check if delivery can be edited
        if (! in_array($purchaseDelivery->status, ['PENDING', 'IN_TRANSIT'])) {
            abort(403, 'Cannot edit delivery in current status');
        }

        $purchaseOrders = app(PurchaseOrderService::class)->findAvailableForDelivery();
        $warehouses = app(WarehouseService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Permission checks
        $canAddStorages = PermissionHelper::canWrite('master_data_warehouse');
        $canAddTags = PermissionHelper::canWrite('master_data_tag');

        return view('pages.main_transaction.purchase_delivery.edit', [
            'purchaseDelivery' => $purchaseDelivery,
            'purchaseOrders' => $purchaseOrders,
            'warehouses' => $warehouses,
            'availableTags' => $availableTags,
            'canAddStorages' => $canAddStorages,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $data = $request->all();
            $data['id'] = $id;

            // Use action for validation and update
            $updateAction = app(UpdatePurchaseDeliveryAction::class);
            $validation = $updateAction->validatePurchaseDeliveryData($data);

            if (! $validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Execute update
            $updateAction->execute($id, $data);

            return response()->json([
                'message' => SuccessCodes::UPDATE_PURCHASE_DELIVERY_SUCCESS->message(),
            ], SuccessCodes::UPDATE_PURCHASE_DELIVERY_SUCCESS->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    // Import/Export Methods following Purchase Order patterns
    public function import(Request $request)
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }

        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Bahan Baku'],
            ['value' => '2', 'label' => 'Gudang Produksi'],
            ['value' => '3', 'label' => 'Gudang Finished Goods'],
        ];

        return view('pages.main_transaction.purchase_delivery.import', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Check write permission for template download
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Delivery akan segera tersedia.',
            'file_url' => '/templates/purchase_delivery_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import validation
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
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
                    'status' => 'PENDING',
                ],
            ],
            'validation_key' => 'pd_validation_' . time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Check write permission for import processing
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 3,
            'message' => 'Purchase Delivery berhasil diimport ke sistem.',
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }

        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Bahan Baku'],
            ['value' => '2', 'label' => 'Gudang Produksi'],
            ['value' => '3', 'label' => 'Gudang Finished Goods'],
        ];

        return view('pages.main_transaction.purchase_delivery.export', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export processing
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_deliveries_export_' . date('Y-m-d_H-i-s') . '.' . $format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/' . $filename,
        ]);
    }

    public function printPDF($poId)
    {
        // Check read permission for PDF generation
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
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
            'download_url' => '/temp/' . $filename,
        ]);
    }

    // public function getPOProducts(Request $request)
    // {
    //     // Check read permission
    //     if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $poId = $request->input('po_id');
    //     if (! $poId) {
    //         return response()->json(['error' => 'PO ID required'], 400);
    //     }

    //     // Mock PO products data - in real implementation, fetch from database
    //     $products = [
    //         [
    //             'product_id' => 1,
    //             'product_name' => 'Botol PET 330ml',
    //             'unit' => 'pcs',
    //             'ordered_qty' => 1000,
    //             'delivered_qty' => 400,
    //             'remaining_qty' => 600,
    //         ],
    //         [
    //             'product_id' => 2,
    //             'product_name' => 'Label Kemasan',
    //             'unit' => 'lembar',
    //             'ordered_qty' => 500,
    //             'delivered_qty' => 200,
    //             'remaining_qty' => 300,
    //         ],
    //         [
    //             'product_id' => 3,
    //             'product_name' => 'Tutup Botol',
    //             'unit' => 'pcs',
    //             'ordered_qty' => 800,
    //             'delivered_qty' => 800,
    //             'remaining_qty' => 0,
    //         ],
    //     ];

    //     return response()->json(['products' => $products]);
    // }

    // public function getPreviousDeliveries(Request $request)
    // {
    //     // Check read permission
    //     if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $poId = $request->input('po_id');
    //     if (! $poId) {
    //         return response()->json(['error' => 'PO ID required'], 400);
    //     }

    //     // Mock previous deliveries data - in real implementation, fetch from database
    //     $deliveries = [
    //         [
    //             'reference_no' => 'PD-2025-001',
    //             'date' => '2025-07-10',
    //             'status' => 'DELIVERED',
    //             'total_items' => 300,
    //         ],
    //         [
    //             'reference_no' => 'PD-2025-002',
    //             'date' => '2025-07-15',
    //             'status' => 'DELIVERED',
    //             'total_items' => 100,
    //         ],
    //     ];

    //     return response()->json(['deliveries' => $deliveries]);
    // }

    // public function getProducts(Request $request)
    // {
    //     // Check read permission
    //     if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $supplierId = $request->input('supplier_id');
    //     if (! $supplierId) {
    //         return response()->json(['error' => 'Supplier ID required'], 400);
    //     }

    //     // Mock products data - in real implementation, fetch from database
    //     $products = [
    //         [
    //             'product_id' => 1,
    //             'product_name' => 'PRD-001 - Botol PET 330ml',
    //             'unit' => 'pcs',
    //         ],
    //         [
    //             'product_id' => 2,
    //             'product_name' => 'PRD-002 - Label Kemasan',
    //             'unit' => 'lembar',
    //         ],
    //         [
    //             'product_id' => 3,
    //             'product_name' => 'PRD-003 - Tutup Botol',
    //             'unit' => 'pcs',
    //         ],
    //         [
    //             'product_id' => 4,
    //             'product_name' => 'PRD-004 - Kardus Packaging',
    //             'unit' => 'dus',
    //         ],
    //     ];

    //     return response()->json(['products' => $products]);
    // }

    public function generateTransactionNumber()
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Generate unique purchase delivery number
        $year = date('Y');
        $month = date('m');
        $timestamp = time();

        // Mock generation - in real implementation, use proper sequence
        $number = "PD{$year}{$month}." . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT) . '/DO/STG';

        return response()->json([
            'success' => true,
            'transaction_number' => $number,
        ]);
    }

    public function generateDeliveryNumber()
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Generate unique delivery number
        $year = date('Y');
        $month = date('m');

        // Mock generation - in real implementation, use proper sequence
        $number = 'DN-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'delivery_number' => $number,
        ]);
    }

    // public function getPODetails(Request $request)
    // {
    //     // Check read permission
    //     if (! PermissionHelper::hasPermission('main_transaction_purchase_delivery', 'read')) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $poId = $request->get('po_id');
    //     if (! $poId) {
    //         return response()->json(['error' => 'PO ID required'], 400);
    //     }

    //     try {
    //         // Get PO details
    //         $poData = app(PurchaseOrderService::class)->findOne($poId);
    //         if (! $poData) {
    //             return response()->json(['error' => 'PO not found'], 404);
    //         }

    //         // Get PO products with delivery tracking
    //         $poProducts = app(\App\Domains\PurchaseOrder\Services\PurchaseOrderProductService::class)->findAllBasedOnPOId($poId);

    //         $items = [];
    //         foreach ($poProducts as $product) {
    //             // Mock delivered quantity - in real implementation, calculate from existing deliveries
    //             $deliveredQty = 0; // This should be calculated from t_purchase_delivery_products
    //             $remainingQty = $product->qty - $deliveredQty;

    //             $items[] = [
    //                 'product_id' => $product->product_id,
    //                 'product_name' => $product->product_code . ' - ' . $product->product_name,
    //                 'unit_name' => $product->unit_code,
    //                 'ordered_qty' => $product->qty,
    //                 'delivered_qty' => $deliveredQty,
    //                 'remaining_qty' => $remainingQty,
    //                 'unit_price' => $product->price_per_qty,
    //                 'tax_name' => $product->tax_name,
    //             ];
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'po_data' => [
    //                 'id' => $poData->id,
    //                 'reference_no' => $poData->transaction_code,
    //                 'supplier_name' => $poData->supplier_name,
    //                 'supplier_id' => $poData->f_supplier_id,
    //                 'supplier_email' => $poData->supplier_email,
    //                 'supplier_address' => $poData->supplier_address,
    //                 'warehouse_id' => $poData->f_warehouse_id,
    //                 'warehouse_name' => $poData->warehouse_code . ' - ' . $poData->warehouse_name,
    //                 'date' => $poData->transaction_date,
    //             ],
    //             'items' => $items,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Failed to load PO details: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
