<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\SalesDelivery\Actions\CreateSalesDeliveryAction;
use App\Domains\SalesDelivery\Actions\UpdateSalesDeliveryAction;
use App\Domains\SalesDelivery\Actions\AcceptSalesDeliveryAction;
use App\Domains\SalesDelivery\Actions\ConfirmSalesDeliveryAction;
use App\Domains\SalesDelivery\Services\SalesDeliveryAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Notification\Services\NotificationService;
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
        $customers = app(ContactService::class)->findAllByFlag('customer');
        $warehouses = app(WarehouseService::class)->findAll();
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_delivery');
    
        $stats = app(SalesOrderService::class)->getSummaryStats();
        return view('pages.main_transaction.sales_delivery.index', array_merge([
            'customers' => $customers,
            'warehouses' => $warehouses,
            'stats'=>$stats
        ], $permissionData));
    }

    public function add(Request $req)
    {
        $preselectedSOId = null;
        $salesOrderProducts = [];
        if (isset($req['so_id'])) {
            $preselectedSOId = $req['so_id'];
            $salesOrderProducts = app(SalesOrderService::class)->findProductLines($preselectedSOId, true);
        }

        $salesOrderList = app(SalesOrderService::class)->findAllPending();

        $customers = app(ContactService::class)->findAllByFlag('customer');
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
        try {
            $validated = $request->validate([
                'delivery_date' => 'required|date',
                'delivery_number' => 'required|string|max:255',
                'delivery_address' => 'required|string|max:500',
                'customer_reference' => 'nullable|string|max:255',
                'delivery_notes' => 'nullable|string|max:1000',
                'shipping_cost' => 'nullable|numeric|min:0',
                'memo' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'sales_order_id' => 'nullable|integer|exists:t_sales_orders,id',
                'customer_id' => 'required|integer|exists:m_contacts,id',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:5120',
                'is_reimburse_ongkir' => 'nullable|boolean',
                'products' => 'required|array',
                'products.*.sales_order_product_id' => 'required|integer|exists:t_sales_order_products,id',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.unit_id' => 'nullable|integer|exists:m_units,id',
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
                'shipping_cost' => 'Ongkos Kirim',
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
                'products.*.unit_id' => 'Unit Produk',
                'products.*.so_qty' => 'Qty Sales Order',
                'products.*.delivered_qty_before' => 'Qty Sudah Dikirim',
                'products.*.remaining_qty_before' => 'Qty Sisa',
                'products.*.delivery_qty' => 'Qty Pengiriman',
                'products.*.note' => 'Catatan Produk',
                'products.*.status' => 'Status Pengiriman',
            ]);

            $sales_delivery_id = app(CreateSalesDeliveryAction::class)->execute($validated);

            // Send notification
            $title = 'Sales Delivery Baru Dibuat';
            $message = 'Sales Delivery dengan nomor ' . $validated['delivery_number'] . ' telah dibuat.';
            $icon_text = 'ti tabler-truck-delivery';
            $notification_type = 'SALES_DELIVERY';
            app(NotificationService::class)->create([
                'broadcast_type' => 'PUBLIC',
                'notification_type' => $notification_type,
                'target_user_id' => null, // Broadcast to all users
                'icon_text' => $icon_text,
                'title' => $title,
                'message' => $message,
                'metadata' => json_encode(['type' => $notification_type, 'id' => $sales_delivery_id]),
                'status' => 'ACTIVE',
            ]);

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
            'shipping_cost' => $salesDeliveryHeader->shipping_cost ?? 0,
            'tags' => $salesDeliveryHeader->tags,
            'attachment' => $salesDeliveryHeader->attachment,
            'memo' => $salesDeliveryHeader->memo,
            'created_at' => $salesDeliveryHeader->created_at,
            'updated_at' => $salesDeliveryHeader->updated_at,
            'is_confirmed' => $salesDeliveryHeader->is_confirmed,
            'is_accepted' => $salesDeliveryHeader->is_accepted,
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

        // Check if a reimbursement was auto-created for this SD
        $hasReimbursement = \Illuminate\Support\Facades\DB::table('t_reimbursements')
            ->where('company_code', session('user_company_code'))
            ->where('reimbursement_name', 'Reimbursement Ongkir ' . $salesDeliveryHeader->delivery_number)
            ->whereNull('deleted_at')
            ->exists();

        return view('pages.main_transaction.sales_delivery.detail', [
            'salesDelivery' => $sales_delivery,
            'customer' => $customer,
            'warehouse' => $warehouse,
            'salesOrder' => $salesOrder,
            'productLines' => $productLines,
            'audit_logs' => $audit_logs,
            'canEdit' => true,
            'hasReimbursement' => $hasReimbursement,
        ]);
    }

    public function edit($id)
    {
        $salesDelivery = app(SalesDeliveryService::class)->findById($id);

        if (!$salesDelivery) {
            abort(404, 'Sales Delivery tidak ditemukan');
        }

        $customers = app(ContactService::class)->findAllByFlag('customer');
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(\App\Domains\Product\Services\ProductService::class)->findAll();
        $productLines = app(SalesDeliveryService::class)->findProductLines($id);
        $tags = app(TagService::class)->findAll();

        // Get selected tags
        $selectedTags = [];
        if ($salesDelivery->tags) {
            $tagsArray = json_decode($salesDelivery->tags, true);
            if (is_array($tagsArray)) {
                $selectedTags = $tagsArray;
            }
        }

        return view('pages.main_transaction.sales_delivery.edit', [
            'salesDelivery' => $salesDelivery,
            'customers' => $customers,
            'warehouses' => $warehouses,
            'products' => $products,
            'productLines' => $productLines,
            'tags' => $tags,
            'selectedTags' => $selectedTags,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|string',
                'delivery_date' => 'required|date',
                'customer_reference' => 'nullable|string|max:255',
                'delivery_address' => 'nullable|string|max:500',
                'delivery_notes' => 'nullable|string|max:1000',
                'shipping_cost' => 'nullable|numeric|min:0',
                'memo' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
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
        $data = app(SalesDeliveryService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    // Import/Export methods
    public function import()
    {
        return view('pages.main_transaction.sales_delivery.import');
    }

    public function export()
    {
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

    /**
     * Accept Sales Delivery (first step before confirmation)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:t_sales_deliveries,id',
            ]);

            app(AcceptSalesDeliveryAction::class)->execute($validated['id']);

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman berhasil diterima. Anda dapat melengkapi detail pengiriman dan konfirmasi.',
            ], 200);
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm Sales Delivery and process stock movement
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request, $id)
    {
        try {
            // Validate attachment if present
            $request->validate([
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
            ]);

            // Get optional fields from request
            $data = [];
            if ($request->has('shipping_cost')) {
                $data['shipping_cost'] = $request->input('shipping_cost');
            }
            if ($request->has('customer_reference')) {
                $data['customer_reference'] = $request->input('customer_reference');
            }
            if ($request->has('delivery_notes')) {
                $data['delivery_notes'] = $request->input('delivery_notes');
            }

            // Handle file upload
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $attachment = $request->file('attachment');
                $data['attachment_path'] = $attachment->store('sales_deliveries', 'public');
            }

            app(ConfirmSalesDeliveryAction::class)->execute($id, $data);

            return response()->json([
                'success' => true,
                'message' => SuccessCodes::UPDATE_SALES_DELIVERY_SUCCESS->message() . ' - Stock telah diperbarui.',
            ], SuccessCodes::UPDATE_SALES_DELIVERY_SUCCESS->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }
}
