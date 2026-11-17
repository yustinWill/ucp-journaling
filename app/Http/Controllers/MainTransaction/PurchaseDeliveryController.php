<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\PurchaseDelivery\Actions\CreatePurchaseDeliveryAction;
use App\Domains\PurchaseDelivery\Actions\UpdatePurchaseDeliveryAction;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryAuditLogService;
// Domain Services
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
// Enums
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseDeliveryController extends Controller
{
    public function index()
    {
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');

        // Map permissions to view variables
        $canAddPurchaseDelivery = auth()->user()->can('purchase_delivery.create');
        $canImportPurchaseDelivery = auth()->user()->can('purchase_delivery.create');
        $canExportPurchaseDelivery = auth()->user()->can('purchase_delivery.view');
        $canViewPurchaseDelivery = auth()->user()->can('purchase_delivery.view');
        $canEditPurchaseDelivery = auth()->user()->can('purchase_delivery.edit');

        $data = [
            'suppliers' => $suppliers,
            'canAddPurchaseDelivery' => $canAddPurchaseDelivery,
            'canImportPurchaseDelivery' => $canImportPurchaseDelivery,
            'canExportPurchaseDelivery' => $canExportPurchaseDelivery,
            'canViewPurchaseDelivery' => $canViewPurchaseDelivery,
            'canEditPurchaseDelivery' => $canEditPurchaseDelivery,
            'canViewUser' => auth()->user()->can('user.view'),
            'canViewPurchaseOrder' => auth()->user()->can('purchase_order.view'),
        ];

        return view('pages.main_transaction.purchase_delivery.index', array_merge($data));
    }

    public function index_data_table(Request $request)
    {
        $data = app(PurchaseDeliveryService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        // Check if coming from PO detail page
        $preselectedPOId = $request->get('po_id');

        // Get available Purchase Orders for delivery
        $purchaseOrder = app(PurchaseOrderService::class)->findOne($preselectedPOId);
        $purchaseOrderProducts = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($preselectedPOId);

        $warehouses = app(WarehouseService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Permission checks for related data creation
        $canAddStorages = auth()->user()->can('warehouse.create');
        $canAddTags = auth()->user()->can('tag.create');

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
        $purchaseDelivery = app(PurchaseDeliveryService::class)->findOne($id);
        if (! $purchaseDelivery) {
            abort(404, 'Purchase Delivery not found');
        }

        $purchaseDeliveryProducts = app(PurchaseDeliveryService::class)->findAllProducts($id);

        // Load audit logs
        $auditLogs = app(PurchaseDeliveryAuditLogService::class)->findByPurchaseDeliveryId($id);

        return view('pages.main_transaction.purchase_delivery.detail', [
            'purchaseDelivery' => $purchaseDelivery,
            'auditLogs' => $auditLogs,
            'purchaseDeliveryProducts' => $purchaseDeliveryProducts,
        ]);
    }

    public function auditLogDataTable($id)
    {
        $data = app(PurchaseDeliveryAuditLogService::class)->findAllDatatableByPurchaseDeliveryId($id);

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function edit($id)
    {
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
        $canAddStorages = auth()->user()->can('warehouse.create');
        $canAddTags = auth()->user()->can('tag.create');

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
        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Delivery akan segera tersedia.',
            'file_url' => '/templates/purchase_delivery_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {
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
            'validation_key' => 'pd_validation_'.time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 3,
            'message' => 'Purchase Delivery berhasil diimport ke sistem.',
        ]);
    }

    public function export()
    {
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
        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_deliveries_export_'.date('Y-m-d_H-i-s').'.'.$format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/'.$filename,
        ]);
    }

    public function printPDF($poId)
    {
        // Mock PDF generation - in real implementation, generate PDF document
        // This would typically involve:
        // 1. Fetch PO data and related products
        // 2. Generate PDF using a library like TCPDF or DomPDF
        // 3. Return the PDF as a downloadable response

        $filename = 'purchase_delivery_form_PO-'.$poId.'_'.date('Y-m-d_H-i-s').'.pdf';

        // For now, return a JSON response indicating success
        // In production, this would return: return response()->file($pdfPath);
        return response()->json([
            'success' => true,
            'message' => 'PDF berhasil digenerate.',
            'filename' => $filename,
            'download_url' => '/temp/'.$filename,
        ]);
    }

    public function generateTransactionNumber()
    {
        // Generate unique purchase delivery number
        $year = date('Y');
        $month = date('m');
        $timestamp = time();

        // Mock generation - in real implementation, use proper sequence
        $number = "PD{$year}{$month}.".str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT).'/DO/STG';

        return response()->json([
            'success' => true,
            'transaction_number' => $number,
        ]);
    }

    public function generateDeliveryNumber()
    {
        // Generate unique delivery number
        $year = date('Y');
        $month = date('m');

        // Mock generation - in real implementation, use proper sequence
        $number = 'DN-'.str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'delivery_number' => $number,
        ]);
    }
}
