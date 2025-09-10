<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Domains\SalesDelivery\Actions\CreateSalesDeliveryAction;
use App\Domains\SalesDelivery\Actions\UpdateSalesDeliveryAction;
use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Helpers\PermissionHelper;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;

class SalesDeliveryController extends Controller
{
    public function index()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        // Load filter data if has permissions
        $customers = [];
        $warehouses = [];
        
        if (PermissionHelper::hasPermission('master_data_contact', 'read')) {
            $customers = app(\App\Domains\Contact\Services\ContactService::class)->findAllByType('CUSTOMER');
        }
        
        if (PermissionHelper::hasPermission('master_data_warehouse', 'read')) {
            $warehouses = app(\App\Domains\Warehouse\Services\WarehouseService::class)->findAll();
        }

        return view('pages.main_transaction.sales_delivery.index', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'canEdit' => PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write'),
            'canImport' => PermissionHelper::hasPermission('main_transaction_sales_delivery', 'import'),
            'canExport' => PermissionHelper::hasPermission('main_transaction_sales_delivery', 'export'),
            'canViewContact' => PermissionHelper::hasPermission('master_data_contact', 'read'),
            'canViewUser' => PermissionHelper::hasPermission('master_data_user', 'read'),
            'canViewSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'read'),
        ]);
    }

    public function add()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat data.');
        }

        // TODO: Load customers, warehouses, sales orders, etc.
        $customers = [];
        $warehouses = [];
        $salesOrders = [];

        return view('pages.main_transaction.sales_delivery.add', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'salesOrders' => $salesOrders,
        ]);
    }

    public function generateTransactionNumber()
    {
        try {
            $transactionNumber = app(TransactionNumberService::class)->generateNumber('sales_delivery');
            
            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate transaction number: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function create(Request $request)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'delivery_number' => 'required|string|max:255',
                'customer_id' => 'required|integer',
                'delivery_date' => 'required|date',
                'warehouse_id' => 'required|integer',
                'status' => 'required|string',
                // TODO: Add more validation rules
            ]);

            $sales_delivery_id = app(CreateSalesDeliveryAction::class)->execute($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sales Delivery berhasil dibuat',
                'data' => ['id' => $sales_delivery_id]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function detail($id)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        // TODO: Replace with actual data loading
        $sales_delivery = (object) [
            'id' => $id,
            'delivery_number' => 'SD-2025-001',
            'customer_name' => 'PT. Customer A',
            'delivery_date' => '2025-01-15',
            'warehouse_name' => 'Gudang Utama',
            'status' => 'PENDING',
        ];

        $audit_logs = app(SalesDeliveryAuditLogService::class)->findAllBasedOnSalesDeliveryId($id);

        return view('pages.main_transaction.sales_delivery.detail', [
            'sales_delivery' => $sales_delivery,
            'audit_logs' => $audit_logs,
            'canEdit' => PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write'),
        ]);
    }

    public function edit($id)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // TODO: Load actual data
        $sales_delivery = (object) [
            'id' => $id,
            'delivery_number' => 'SD-2025-001',
            'customer_id' => 1,
            'delivery_date' => '2025-01-15',
            'warehouse_id' => 1,
            'status' => 'PENDING',
        ];

        $customers = [];
        $warehouses = [];

        return view('pages.main_transaction.sales_delivery.edit', [
            'sales_delivery' => $sales_delivery,
            'customers' => $customers,
            'warehouses' => $warehouses,
        ]);
    }

    public function update(Request $request)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|string',
                'delivery_date' => 'required|date',
                // TODO: Add more validation rules
            ]);

            $id = $validated['id'];
            unset($validated['id']);

            app(UpdateSalesDeliveryAction::class)->execute($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Sales Delivery berhasil diperbarui'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    public function index_data_table(Request $request)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Get data from service (filters are applied within the service via request())
            $result = app(\App\Domains\SalesDelivery\Services\SalesDeliveryService::class)->findAllDatatable();

            return response()->json($result, 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Import/Export methods
    public function import()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'import')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.main_transaction.sales_delivery.import');
    }

    public function export()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_delivery', 'export')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

        // TODO: Implement export columns configuration
        $exportColumns = [];

        return view('pages.main_transaction.sales_delivery.export', [
            'exportColumns' => $exportColumns
        ]);
    }

    public function downloadTemplate()
    {
        // TODO: Implement template generation
        $filename = 'sales_delivery_import_template.xlsx';
        
        return response()->json([
            'message' => 'Template download functionality - TODO: Implement'
        ]);
    }

    public function validateImport(Request $request)
    {
        // TODO: Implement import validation
        return response()->json([
            'success' => true,
            'data' => [
                'total_count' => 0,
                'valid_count' => 0,
                'error_count' => 0,
                'valid_data' => [],
                'error_data' => []
            ]
        ]);
    }

    public function processImport(Request $request)
    {
        // TODO: Implement import processing
        return response()->json([
            'success' => true,
            'imported_count' => 0,
            'message' => 'Sales Delivery berhasil diimport'
        ]);
    }

    public function processExport(Request $request)
    {
        // TODO: Implement export processing
        return response()->json([
            'success' => true,
            'message' => 'Export functionality - TODO: Implement'
        ]);
    }

}