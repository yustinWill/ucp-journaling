<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Domains\Product\Services\ProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Domains\SalesOrder\Actions\CreateSalesOrderAction;
use App\Domains\SalesOrder\Actions\UpdateSalesOrderAction;
use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Notification\Services\NotificationService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Events\MessageSent;

class SalesOrderController extends Controller
{
    public function get_return_status_design($status)
    {
        switch ($status) {
            case 'DRAFT':
                return 'bg-label-secondary';
            case 'PENDING_APPROVAL':
                return 'bg-label-warning';
            case 'APPROVED':
                return 'bg-label-success';
            case 'PROCESSED':
                return 'bg-label-info';
            case 'REJECTED':
                return 'bg-label-danger';
            case 'CANCELLED':
                return 'bg-label-dark';
            default:
                return 'bg-label-secondary';
        }
    }

    public function get_stock_adjustment_status_design($status)
    {
        switch ($status) {
            case 'NOT_ADJUSTED':
                return 'bg-label-warning';
            case 'ADJUSTED':
                return 'bg-label-success';
            default:
                return 'bg-label-secondary';
        }
    }

    public function index()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        // Load customers for filter dropdown
        $customers = DB::table('m_contacts')
            ->where('company_code', session('user_company_code'))
            ->where('is_customer', true)
            ->where('status', 'ACTIVE')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('pages.main_transaction.sales_order.index', [
            'customers' => $customers,
            'canAddSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'write'),
            'canViewSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'read'),
            'canEditSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'write'),
            'canDeleteSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'delete'),
            'canImportSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'import'),
            'canExportSalesOrder' => PermissionHelper::hasPermission('main_transaction_sales_order', 'export'),
        ]);
    }

    public function add()
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $customers = app(ContactService::class)->findAllByFlag('customer');
        $salesPersons = app(SalesPersonService::class)->findAll();
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(ProductService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Check permissions for adding related data
        $canAddCustomers = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_transaction_number', 'write');
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddWarehouses = PermissionHelper::hasPermission('master_data_warehouse', 'write');

        // Get permissions
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_order');

        return view('pages.main_transaction.sales_order.add', [
            'customers' => $customers,
            'salesPersons' => $salesPersons,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'availableTags' => $availableTags,
            'canAddCustomers' => $canAddCustomers,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses,
        ] + $permissionData);
    }

    public function generateTransactionNumber()
    {
        try {
            $transactionNumber = app(TransactionNumberService::class)->generateNumber('sales_order');

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

    public function store(Request $request)
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'reference_no' => 'required|string|max:255',
                'date' => 'required|date|before_or_equal:today',
                'customer_id' => 'required|integer|exists:m_contacts,id',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'customer_reference' => 'nullable|string|max:255',
                'payment_terms' => 'required|integer|exists:m_payment_terms,id',
                'payment_term_day_count' => 'nullable|integer|min:0|max:365',
                'payment_term_due_date' => 'nullable|date|after:date',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'shipping_cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'memo' => 'nullable|string|max:500',
                // 'include_tax' => 'required|boolean',
                // 'subtotal' => 'required|numeric|min:0',
                // 'tax_amount' => 'required|numeric|min:0',
                // 'total_before_discount' => 'required|numeric|min:0',
                // 'discount_amount' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|string',
                // 'products.*.tax_id' => 'nullable|integer|exists:m_taxes,id',
                'products.*.total' => 'required|string',
                'products.*.is_bonus' => 'nullable|boolean',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
            ], [], [
                'reference_no' => 'Nomor Referensi',
                'date' => 'Tanggal',
                'customer_id' => 'Pelanggan',
                'email' => 'Email',
                'address' => 'Alamat',
                'customer_reference' => 'Referensi Pelanggan',
                'payment_terms' => 'Syarat Pembayaran',
                'payment_term_day_count' => 'Jumlah Hari Pembayaran',
                'payment_term_due_date' => 'Jatuh Tempo Pembayaran',
                'warehouse_id' => 'Gudang',
                'shipping_cost' => 'Harga Ongkos Kirim',
                'description' => 'Deskripsi',
                'tags' => 'Tag',
                'memo' => 'Memo',
                'total' => 'Total',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.quantity' => 'Qty Produk',
                'products.*.unit_price' => 'Harga Satuan Produk',
                'products.*.total' => 'Total Produk',
                'products.*.is_bonus' => 'Apakah Bonus',
                'attachment' => 'Lampiran',
            ]);

            $sales_order_id = app(CreateSalesOrderAction::class)->execute($validated);

            $title = 'Sales Order Baru Dibuat';
            $message = 'Sales Order dengan nomor ' . $validated['reference_no'] . ' telah dibuat.';
            $icon_text = 'ti tabler-shopping-cart';
            $notification_type = 'SALES_ORDER';
            app(NotificationService::class)->create([
                'broadcast_type' => 'PUBLIC',
                'notification_type' => $notification_type,
                'target_user_id' => null, // Broadcast to all users
                'icon_text' => $icon_text,
                'title' => $title,
                'message' => $message,
                'metadata' => json_encode(['type' => $notification_type, 'id' => $sales_order_id]),
                'status' => 'ACTIVE',
            ]);

            // Sending a simple object instead of a model
            $message = [
                'icon' => $icon_text,
                'title' => $title,
                'message' => $message,
                'timestamp_text' => date('y-m-d H:i:s'),
                'metadata' => json_encode(['type' => $notification_type, 'id' => $sales_order_id]),
            ];

            // Fire the event
            broadcast(new MessageSent($message));

            return response()->json([
                'message' => SuccessCodes::CREATE_SALES_ORDER_SUCCESS->message(),
            ], SuccessCodes::CREATE_SALES_ORDER_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
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
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        // Load sales order with full details
        $salesOrder = app(SalesOrderService::class)->findWithDetails($id);

        if (! $salesOrder) {
            abort(404, 'Sales Order tidak ditemukan.');
        }

        // Get audit logs
        $auditLogs = app(SalesOrderAuditLogService::class)->findAllBasedOnSalesOrderId($id);

        // Get return data for this SO
        $salesReturns = app(ReturnManagementService::class)->findBySalesOrderId($id);

        $salesDeliveries = app(SalesDeliveryService::class)->findAllById($id);
        $salesInvoices = app(SalesInvoiceService::class)->findAllHeaderBasedOnSoId($id);

        // Format return data
        $sales_returns = [];
        foreach ($salesReturns as $return) {
            $sales_returns[] = [
                'id' => $return->id,
                'return_date' => $return->return_date,
                'transaction_code' => $return->transaction_code,
                'source_invoice_code' => $return->source_invoice_code,
                'grand_total' => $return->grand_total,
                'status' => $return->status,
                'status_badge_class' => $this->get_return_status_design($return->status),
                'stock_adjustment_status' => $return->stock_adjustment_status,
                'stock_adjustment_status_badge_class' => $this->get_stock_adjustment_status_design($return->stock_adjustment_status),
                'created_by' => $return->created_by_name,
                'created_by_id' => $return->created_by,
                'f_source_invoice_id' => $return->f_source_invoice_id,
            ];
        }

        // Get permissions
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_order');

        return view('pages.main_transaction.sales_order.detail', [
            'salesOrder' => $salesOrder,
            'audit_logs' => $auditLogs,
            'sales_returns' => $sales_returns,
            'salesDeliveries' => $salesDeliveries,
            'salesInvoices' => $salesInvoices,
            'canEdit' => $permissionData['canWrite'],
            'canDelete' => $permissionData['canDelete'],
            'canCreateSalesDelivery' => PermissionHelper::hasPermission('main_transaction_sales_delivery', 'write'),
            'canCreateSalesInvoice' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write'),
            'canViewUser' => PermissionHelper::hasPermission('master_data_user', 'read'),
            'canViewReturnManagement' => PermissionHelper::hasPermission('main_transaction_return_management', 'read'),
            'canViewSalesInvoice' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read'),
            'canViewProduct' => PermissionHelper::hasPermission('master_data_product', 'read'),
        ]);
    }

    public function edit($id)
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // Load sales order with details
        $salesOrder = app(SalesOrderService::class)->findWithDetails($id);

        if (! $salesOrder) {
            abort(404, 'Sales Order tidak ditemukan.');
        }

        // Load form data
        $customers = app(ContactService::class)->findAllByFlag('customer');
        $salesPersons = app(SalesPersonService::class)->findAll();
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(ProductService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Check permissions for adding related data
        $canAddCustomers = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_transaction_number', 'write');
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddWarehouses = PermissionHelper::hasPermission('master_data_warehouse', 'write');

        // Get permissions
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_order');

        return view('pages.main_transaction.sales_order.edit', [
            'salesOrder' => $salesOrder,
            'customers' => $customers,
            'salesPersons' => $salesPersons,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'tags' => $availableTags,
            'canAddCustomers' => $canAddCustomers,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses,
        ] + $permissionData);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:t_sales_orders,id',
                'date' => 'required|date|before_or_equal:today',
                'customer_id' => 'required|integer|exists:m_contacts,id',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'customer_reference' => 'nullable|string|max:255',
                'payment_terms' => 'required|integer|exists:m_payment_terms,id',
                'payment_term_day_count' => 'nullable|integer|min:0|max:365',
                'payment_term_due_date' => 'nullable|date|after:date',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'shipping_cost' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'memo' => 'nullable|string|max:500',
                // 'include_tax' => 'required|boolean',
                // 'subtotal' => 'required|numeric|min:0',
                // 'tax_amount' => 'required|numeric|min:0',
                // 'total_before_discount' => 'required|numeric|min:0',
                // 'discount_amount' => 'nullable|numeric|min:0',
                'grand_price' => 'required|numeric|min:0',
                'products' => 'required|array',
                'products.*.id' => 'nullable|integer|exists:t_sales_order_products,id',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|string',
                // 'products.*.tax_id' => 'nullable|integer|exists:m_taxes,id',
                'products.*.total' => 'required|string',
                'products.*.is_bonus' => 'nullable|boolean',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
            ], [], [
                'id' => 'ID Sales Order',
                'date' => 'Tanggal',
                'customer_id' => 'Pelanggan',
                'email' => 'Email',
                'address' => 'Alamat',
                'customer_reference' => 'Referensi Pelanggan',
                'payment_terms' => 'Syarat Pembayaran',
                'payment_term_day_count' => 'Jumlah Hari Pembayaran',
                'payment_term_due_date' => 'Jatuh Tempo Pembayaran',
                'warehouse_id' => 'Gudang',
                'shipping_cost' => 'Harga Ongkos Kirim',
                'description' => 'Deskripsi',
                'tags' => 'Tag',
                'memo' => 'Memo',
                'grand_price' => 'Grand Total',
                'products' => 'Produk',
                'products.*.id' => 'ID Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.quantity' => 'Qty Produk',
                'products.*.unit_price' => 'Harga Satuan Produk',
                'products.*.total' => 'Total Produk',
                'products.*.is_bonus' => 'Apakah Bonus',
                'attachment' => 'Lampiran',
            ]);

            $id = $validated['id'];

            app(UpdateSalesOrderAction::class)->execute($id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_SALES_ORDER_SUCCESS->message(),
            ], SuccessCodes::UPDATE_SALES_ORDER_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function index_data_table()
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(SalesOrderService::class)->findAllDatatable();

        return response()->json($data, 200);
    }

    // Import/Export methods
    public function import()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'import')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.main_transaction.sales_order.import');
    }

    public function export()
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'export')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

        // TODO: Implement export columns configuration
        $exportColumns = [];

        return view('pages.main_transaction.sales_order.export', [
            'exportColumns' => $exportColumns,
        ]);
    }

    public function downloadTemplate()
    {
        // TODO: Implement template generation
        $filename = 'sales_order_import_template.xlsx';

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
            'message' => 'Sales Order berhasil diimport',
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

    // Helper methods for loading form data

    // AJAX endpoints for dynamic data loading
    public function getCustomerInfo($customerId)
    {
        $customer = DB::table('m_contacts as t')
            ->select([
                't.email',
                'ca.address as billing_address',
                't.f_sales_pic_id',
                'c.name as sales_person_name',
            ])
            ->leftJoin('m_contact_addresses as ca', function ($join) {
                $join->on('ca.f_contact_id', '=', 't.id')
                    ->where('ca.type', '=', 'BILLING');
            })
            ->leftJoin('m_sales_persons as sp', 't.f_sales_pic_id', '=', 'sp.id')
            ->leftJoin('m_contacts as c', 'sp.f_employee_contact_id', '=', 'c.id')
            ->where('t.id', $customerId)
            ->where('t.company_code', session('user_company_code'))
            ->where('t.is_customer', true)
            ->first();

        if (! $customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'email' => $customer->email,
                'address' => $customer->billing_address,
                'sales_person_id' => $customer->f_sales_pic_id,
                'sales_person_name' => $customer->sales_person_name,
            ],
        ]);
    }

    public function getWarehouseProducts()
    {
        $customerId = request()->input('customer_id');

        if (! $customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ID is required',
            ], 400);
        }

        // Get products mapped to this customer with their special pricing
        $products = DB::table('m_contact_products as cp')
            ->join('m_products as p', 'cp.f_product_code', '=', 'p.code')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('cp.f_contact_id', $customerId)
            ->where('p.company_code', session('user_company_code'))
            ->where('p.status', 'ACTIVE')
            ->where('p.total_stock', '>', 0) // Only products with stock
            ->select([
                'p.id as product_id',
                'p.code as product_code',
                'p.name as product_name',
                'cp.price_per_qty as price',
                'u.name as unit_name',
                'p.total_stock as stock',
                // 'p.f_tax_id as tax_id'
            ])
            ->orderBy('p.name')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
