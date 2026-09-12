<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\Notification\Services\NotificationService;
use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Domains\Product\Services\ProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Domains\SalesDelivery\Services\SalesDeliveryService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesOrder\Actions\CreateSalesOrderAction;
use App\Domains\SalesOrder\Actions\DeleteSalesOrderAction;
use App\Domains\SalesOrder\Actions\UpdateSalesOrderAction;
use App\Domains\SalesOrder\Services\SalesOrderAuditLogService;
use App\Domains\SalesOrder\Services\SalesOrderRevertLogService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Events\MessageSent;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Services\TransactionNumberService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        // Load customers for filter dropdown
        $customers = DB::table('m_contacts')
            ->where('company_code', session('user_company_code'))
            ->where('is_customer', true)
            ->where('status', 'ACTIVE')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_order');

        return view('pages.main_transaction.sales_order.index', array_merge([
            'customers' => $customers,
        ], $permissionData));
    }

    public function summaryStats()
    {
        $stats = app(SalesOrderService::class)->getSummaryStats();

        return response()->json($stats);
    }

    public function add()
    {
        $customers = app(ContactService::class)->findAllByFlag('customer');
        $salesPersons = app(SalesPersonService::class)->findAll();
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(ProductService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Check permissions for adding related data
        $canAddCustomers = true;
        $canAddPaymentTerms = true;
        $canAddTags = true;
        $canAddWarehouses = true;

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
        ]);
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
                'message' => 'Failed to generate transaction number: '.$e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if ($this->salesOrderNumberExists($request->input('reference_no'))) {
                return $this->duplicateSalesOrderNumberResponse();
            }

            $validator = Validator::make($request->all(), [
                'reference_no' => 'required|string|max:255|unique:t_sales_orders,transaction_code',
                'date' => 'required|date|before_or_equal:today',
                'customer_id' => 'required|integer|exists:m_contacts,id',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'customer_reference' => 'nullable|string|max:255',
                'payment_terms' => 'required|integer|exists:m_payment_terms,id',
                'payment_term_day_count' => 'nullable|integer|min:0|max:365',
                'payment_term_due_date' => 'nullable|date|after:date',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
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
                'products.*.unit_id' => 'nullable|integer|exists:m_units,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|string',
                // 'products.*.tax_id' => 'nullable|integer|exists:m_taxes,id',
                'products.*.total' => 'required|string',
                'products.*.is_bonus' => 'nullable|boolean',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:5120',
            ], [
                'reference_no.unique' => 'Nomor Sales Order sudah digunakan. Silakan gunakan nomor sales order lain.',
            ], [
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
                'description' => 'Deskripsi',
                'tags' => 'Tag',
                'memo' => 'Memo',
                'total' => 'Total',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.unit_id' => 'Unit Produk',
                'products.*.quantity' => 'Qty Produk',
                'products.*.unit_price' => 'Harga Satuan Produk',
                'products.*.total' => 'Total Produk',
                'products.*.is_bonus' => 'Apakah Bonus',
                'attachment' => 'Lampiran',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                    'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            $validated = $validator->validated();

            $sales_order_id = app(CreateSalesOrderAction::class)->execute($validated);

            $title = 'Sales Order Baru Dibuat';
            $message = 'Sales Order dengan nomor '.$validated['reference_no'].' telah dibuat.';
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
        } catch (QueryException $e) {
            if ($this->isDuplicateKeyException($e)) {
                return $this->duplicateSalesOrderNumberResponse();
            }

            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    private function salesOrderNumberExists(?string $transactionNumber): bool
    {
        if (empty($transactionNumber)) {
            return false;
        }

        return DB::table('t_sales_orders')
            ->where('transaction_code', $transactionNumber)
            ->exists();
    }

    private function duplicateSalesOrderNumberResponse()
    {
        return response()->json([
            'message' => 'Nomor Sales Order sudah digunakan. Silakan gunakan nomor sales order lain.',
            'errors' => [
                'reference_no' => ['Nomor Sales Order sudah digunakan. Silakan gunakan nomor sales order lain.'],
            ],
            'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
        ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
    }

    private function isDuplicateKeyException(QueryException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1062;
    }

    public function detail($id)
    {
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

        // Check if any product still has remaining qty for delivery (excluding pending unconfirmed SDs)
        $hasDeliverableQty = DB::table('t_sales_order_products as sop')
            ->where('sop.f_sales_order_id', $id)
            ->where('sop.is_bonus', 0)
            ->where(function ($q) {
                $q->whereRaw('sop.remaining_qty > 0')
                    ->whereRaw('sop.remaining_qty > COALESCE((
                      SELECT SUM(sdp.qty)
                      FROM t_sales_delivery_products sdp
                      JOIN t_sales_deliveries sd ON sdp.sales_delivery_id = sd.id
                      WHERE sdp.sales_order_product_id = sop.id
                        AND sd.is_confirmed != 1
                  ), 0)');
            })
            ->exists();

        // Check if any product still has remaining qty for invoice
        $hasInvoiceableQty = DB::table('t_sales_order_products as sop')
            ->where('sop.f_sales_order_id', $id)
            ->where('sop.is_bonus', 0)
            ->whereRaw('sop.quantity > COALESCE((
                SELECT SUM(sip.qty)
                FROM t_sales_invoice_products sip
                WHERE sip.f_sales_order_product_id = sop.id
            ), 0)')
            ->exists();

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

        return view('pages.main_transaction.sales_order.detail', [
            'salesOrder' => $salesOrder,
            'audit_logs' => $auditLogs,
            'sales_returns' => $sales_returns,
            'salesDeliveries' => $salesDeliveries,
            'salesInvoices' => $salesInvoices,
            'canEdit' => true,
            'canDelete' => true,
            'canCreateSalesDelivery' => true,
            'canCreateSalesInvoice' => true,
            'hasDeliverableQty' => $hasDeliverableQty,
            'hasInvoiceableQty' => $hasInvoiceableQty,
            'canViewUser' => true,
            'canViewReturnManagement' => true,
            'canViewSalesInvoice' => true,
            'canViewProduct' => true,
        ]);
    }

    public function cancel($id)
    {
        // Permission check
        if (! PermissionHelper::hasPermission('main_transaction_sales_order', 'write')) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
        }

        $salesOrder = DB::table('t_sales_orders')
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->first();

        if (! $salesOrder) {
            return response()->json(['success' => false, 'message' => 'Sales Order tidak ditemukan.'], 404);
        }

        if ($salesOrder->status === 'CANCELLED') {
            return response()->json(['success' => false, 'message' => 'Sales Order sudah dibatalkan.'], 422);
        }

        // Check if SD exists
        $hasDelivery = DB::table('t_sales_deliveries')
            ->where('sales_order_id', $id)
            ->exists();

        if ($hasDelivery) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat membatalkan, Sales Order sudah memiliki Sales Delivery.'], 422);
        }

        // Check if SI exists
        $hasInvoice = DB::table('t_sales_invoices')
            ->where('f_sales_order_id', $id)
            ->exists();

        if ($hasInvoice) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat membatalkan, Sales Order sudah memiliki Sales Invoice.'], 422);
        }

        // Update status to CANCELLED
        DB::table('t_sales_orders')
            ->where('id', $id)
            ->update([
                'status' => 'CANCELLED',
                'updated_at' => now(),
                'updated_by' => auth()->id(),
            ]);

        // Create audit log
        app(SalesOrderAuditLogService::class)->create(
            $id,
            'CANCEL',
            ['cancelled_by' => auth()->id(), 'cancelled_at' => now()->toDateTimeString()],
            'Sales Order dibatalkan'
        );

        return response()->json(['success' => true, 'message' => 'Sales Order berhasil dibatalkan.']);
    }

    public function revertIndex(Request $request)
    {
        return view('pages.main_transaction.sales_order.revert', [
            'canExecuteRevert' => $request->user()->can('sales_order.delete'),
        ]);
    }

    public function revertIndexData()
    {
        return response()->json(
            app(SalesOrderService::class)->findAllRevertCandidatesDatatable()
        );
    }

    public function revertLogIndex()
    {
        return view('pages.main_transaction.sales_order.revert_log');
    }

    public function revertLogIndexData()
    {
        return response()->json(
            app(SalesOrderRevertLogService::class)->findAllDatatable()
        );
    }

    public function destroy(Request $request, $id)
    {
        if (! $request->user()->can('sales_order.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melakukan revert Sales Order.',
            ], 403);
        }

        if (! session('user_company_code')) {
            return response()->json([
                'success' => false,
                'message' => 'Company aktif tidak ditemukan. Silakan login ulang sebelum melakukan revert.',
            ], 403);
        }

        try {
            $summary = app(DeleteSalesOrderAction::class)->execute((int) $id);

            return response()->json([
                'success' => true,
                'message' => 'Sales Order berhasil dihapus beserta Sales Delivery dan Sales Invoice terkait.',
                'data' => $summary,
            ]);
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
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
        $canAddCustomers = true;
        $canAddPaymentTerms = true;
        $canAddTags = true;
        $canAddWarehouses = true;

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
        ]);
    }

    public function update(Request $request)
    {
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
                'products.*.unit_id' => 'nullable|integer|exists:m_units,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|string',
                // 'products.*.tax_id' => 'nullable|integer|exists:m_taxes,id',
                'products.*.total' => 'required|string',
                'products.*.is_bonus' => 'nullable|boolean',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:5120',
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
                'description' => 'Deskripsi',
                'tags' => 'Tag',
                'memo' => 'Memo',
                'grand_price' => 'Grand Total',
                'products' => 'Produk',
                'products.*.id' => 'ID Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.unit_id' => 'Unit Produk',
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
        $data = app(SalesOrderService::class)->findAllDatatable();

        return response()->json($data, 200);
    }

    // Import/Export methods
    public function import()
    {
        return view('pages.main_transaction.sales_order.import');
    }

    public function export()
    {
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
