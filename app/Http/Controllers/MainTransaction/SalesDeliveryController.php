<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\SalesDelivery\Actions\CreateSalesDeliveryAction;
use App\Domains\SalesDelivery\Actions\UpdateSalesDeliveryAction;
use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Domains\Tag\Services\TagService;
use App\Exceptions\AppException;
use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Helpers\DiscordNotifier;

class SalesDeliveryController extends Controller
{
    public function index()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        $customers = app(ContactService::class)->findAllByType('CUSTOMER');
        $warehouses = app(WarehouseService::class)->findAll();

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

    public function add(Request $req)
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat data.');
        }

        $preselectedSOId = null;
        $salesOrderProducts = [];
        if (isset($req['so_id'])) {
            $preselectedSOId = $req['so_id'];
            $salesOrderProducts = app(SalesOrderService::class)->findProductLines($preselectedSOId, true);
        }

        $salesOrderList = app(SalesOrderService::class)->findAllPending();

        $customers = app(ContactService::class)->findAllByType('CUSTOMER');
        $warehouses = app(WarehouseService::class)->findAll();
        $products = [];
        $tags = app(TagService::class)->findAll();

        return view('pages.main_transaction.sales_delivery.add', [
            'customers' => $customers,
            'warehouses' => $warehouses,
            'products' => $products,
            'tags' => $tags,
            'salesOrderList' => $salesOrderList,
            'preselectedSOId' => $preselectedSOId,
            'salesOrderProducts' => $salesOrderProducts,
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
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'delivery_date' => 'required|date',
                'delivery_number' => 'required|string|max:255',
                'delivery_address' => 'required|string|max:500',
                'customer_reference' => 'nullable|string|max:255',
                'delivery_notes' => 'nullable|string|max:1000',
                'memo' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'sales_order_id' => 'nullable|integer|exists:t_sales_orders,id',
                'customer_id' => 'required|integer|exists:m_contacts,id',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
                'products' => 'required|array',
                'products.*.sales_order_product_id' => 'required|integer|exists:t_sales_order_products,id',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.so_qty' => 'required|numeric|min:0',
                'products.*.delivered_qty_before' => 'required|numeric|min:0',
                'products.*.remaining_qty_before' => 'required|numeric|min:0',
                'products.*.delivery_qty' => 'required|numeric|min:0.01',
                'products.*.note' => 'nullable|string|max:255',
                'products.*.status' => 'required|string|in:COMPLETED,PARTIAL',
            ], [], [
                'delivery_date' => 'Tanggal Pengiriman',
                'delivery_number' => 'Nomor Pengiriman',
                'delivery_address' => 'Alamat Pengiriman',
                'customer_reference' => 'Referensi Pelanggan',
                'delivery_notes' => 'Catatan Pengiriman',
                'memo' => 'Memo',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'sales_order_id' => 'Sales Order',
                'customer_id' => 'Pelanggan',
                'warehouse_id' => 'Gudang',
                'attachment' => 'Lampiran',
                'products' => 'Produk',
                'products.*.sales_order_product_id' => 'ID Produk Sales Order',
                'products.*.product_id' => 'ID Produk',
                'products.*.so_qty' => 'Qty Sales Order',
                'products.*.delivered_qty_before' => 'Qty Sudah Dikirim',
                'products.*.remaining_qty_before' => 'Qty Sisa',
                'products.*.delivery_qty' => 'Qty Pengiriman',
                'products.*.note' => 'Catatan Produk',
                'products.*.status' => 'Status Pengiriman',
            ]);

            app(CreateSalesDeliveryAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_SALES_DELIVERY_SUCCESS->message(),
            ], SuccessCodes::CREATE_SALES_DELIVERY_SUCCESS->httpStatus());
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
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        $salesDeliveryHeader = app(SalesDeliveryService::class)->findOne($id);
        $salesDeliveryProduct = app(SalesDeliveryService::class)->findProductLines($id, true);
        // TODO: Replace with actual data loading
        $sales_delivery = (object) [
            'id' => $id,
            'delivery_number' => $salesDeliveryHeader->delivery_number,
            'customer_name' => $salesDeliveryHeader->customer_name,
            'delivery_date' => $salesDeliveryHeader->delivery_date,
            'warehouse_name' => $salesDeliveryHeader->warehouse_name,
            'status' => $salesDeliveryHeader->status,
            'created_by' => $salesDeliveryHeader->created_by_name,
            'delivery_address' => $salesDeliveryHeader->delivery_address,
            'customer_reference' => $salesDeliveryHeader->customer_reference,
            'delivery_notes' => $salesDeliveryHeader->delivery_notes,
            'tags' => $salesDeliveryHeader->tags,
            'attachment' => $salesDeliveryHeader->attachment,
            'memo' => $salesDeliveryHeader->memo,
            'created_at' => $salesDeliveryHeader->created_at,
            'updated_at' => $salesDeliveryHeader->updated_at,
        ];
        // dd($sales_delivery);

        // Mock related data
        $customer = (object) [
            'name' => $salesDeliveryHeader->customer_name,
            'email' => $salesDeliveryHeader->customer_email,
            'address' => $salesDeliveryHeader->customer_address,
        ];

        $warehouse = (object) [
            'name' => $salesDeliveryHeader->warehouse_name,
        ];

        $salesOrder = (object) [
            'transaction_code' => $salesDeliveryHeader->sales_order_number,
            'status' => 'COMPLETED',
        ];

        $productLines = [];
        foreach ($salesDeliveryProduct as $product) {
            $productLines[] = (object) [
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'quantity_ordered' => $product->delivered_qty_after + $product->remaining_qty_after,
                'quantity_delivered' => $product->delivered_qty_after,
                'unit_name' => $product->unit_name,
                'delivery_status' => $product->delivery_status,
                'description' => $product->description,
            ];
        }

        $audit_logs = app(SalesDeliveryAuditLogService::class)->findAllBasedOnSalesDeliveryId($id);

        return view('pages.main_transaction.sales_delivery.detail', [
            'salesDelivery' => $sales_delivery,
            'customer' => $customer,
            'warehouse' => $warehouse,
            'salesOrder' => $salesOrder,
            'productLines' => $productLines,
            'audit_logs' => $audit_logs,
            'canEdit' => PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write'),
        ]);
    }

    public function edit($id)
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
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
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write')) {
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
                'message' => 'Sales Delivery berhasil diperbarui',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
            ], 500);
        }
    }

    public function index_data_table()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(SalesDeliveryService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    // Import/Export methods
    public function import()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'import')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.main_transaction.sales_delivery.import');
    }

    public function export()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_delivery', 'export')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

        // TODO: Implement export columns configuration
        $exportColumns = [];

        return view('pages.main_transaction.sales_delivery.export', [
            'exportColumns' => $exportColumns,
        ]);
    }

    public function downloadTemplate()
    {
        // TODO: Implement template generation
        $filename = 'sales_delivery_import_template.xlsx';

        return response()->json([
            'message' => 'Template download functionality - TODO: Implement',
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
                'error_data' => [],
            ],
        ]);
    }

    public function processImport(Request $request)
    {
        // TODO: Implement import processing
        return response()->json([
            'success' => true,
            'imported_count' => 0,
            'message' => 'Sales Delivery berhasil diimport',
        ]);
    }

    public function processExport(Request $request)
    {
        // TODO: Implement export processing
        return response()->json([
            'success' => true,
            'message' => 'Export functionality - TODO: Implement',
        ]);
    }
}
