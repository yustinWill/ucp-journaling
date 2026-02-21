<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Domains\Product\Services\ProductService;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Domains\PurchaseOrder\Actions\CreatePurchaseOrderAction;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Domains\PurchaseOrder\Actions\UpdatePurchaseOrderAction;
use App\Domains\PurchaseOrder\Services\PurchaseOrderAuditLogService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Tax\Services\TaxService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
// Enums
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PurchaseOrderController extends Controller
{
    public function get_delivery_status_design($delivery_status)
    {
        if ($delivery_status == 'NONE') {
            return 'bg-label-danger';
        } elseif ($delivery_status == 'MIXED') {
            return 'bg-label-warning';
        } elseif ($delivery_status == 'PARTIAL') {
            return 'bg-label-warning';
        } elseif ($delivery_status == 'COMPLETED') {
            return 'bg-label-success';
        }
    }

    public function get_invoice_status_design($invoice_status)
    {
        // Temporary: Use single color for all statuses
        return 'bg-label-primary';

        // TODO: Implement proper status-based colors later
        // switch ($invoice_status) {
        //     case 'DRAFT':
        //         return 'bg-label-secondary';
        //     case 'UNPAID':
        //         return 'bg-label-warning';
        //     case 'PAID':
        //         return 'bg-label-success';
        //     case 'PARTIAL_PAID':
        //         return 'bg-label-info';
        //     case 'OVERDUE':
        //         return 'bg-label-danger';
        //     case 'CANCELLED':
        //         return 'bg-label-dark';
        //     default:
        //         return 'bg-label-secondary';
        // }
    }

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
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');

        return view('pages.main_transaction.purchase_order.index', array_merge([
            'suppliers' => $suppliers,
        ], $permissionData));
    }

    public function index_data_table()
    {
        $data = app(PurchaseOrderService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $customers = app(ContactService::class)->findAllByFlag('employee');
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(ProductService::class)->findAllAllowPurchase();
        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Check permissions for adding related data
        $canAddSuppliers = true;
        $canAddPaymentTerms = true;
        $canAddTags = true;
        $canAddStorages = true;

        return view('pages.main_transaction.purchase_order.add', [
            'suppliers' => $suppliers,
            'customers' => $customers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions,
            'availableTags' => $availableTags,
            'canAddSuppliers' => $canAddSuppliers,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'canAddTags' => $canAddTags,
            'canAddStorages' => $canAddStorages,
        ]);
    }

    public function generateTransactionNumber()
    {
        try {
            // Generate transaction number based on current date and sequence
            $year = date('Y');
            $month = date('m');

            // Get the latest sequence number for this month
            $latestRecord = app(PurchaseOrderService::class)->getLatestByMonth($year, $month);
            $sequence = $latestRecord ? ($latestRecord->id + 1) : 1;

            // Format: PO-YYYY-MMDD-XXX (PO = Purchase Order)
            $transactionNumber = sprintf('PO-%s-%s%s-%03d', $year, $month, date('d'), $sequence);

            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date',
                'reference_no' => 'required|string|max:255',
                'supplier_id' => 'required|integer',
                'email' => 'nullable|email',
                'address' => 'nullable|string|max:255',
                'supplier_reference' => 'nullable|string|max:255',
                'payment_terms' => 'required|string|max:50',
                'payment_term_day_count' => 'nullable|integer',
                'payment_term_due_date' => 'nullable|date',
                'warehouse_id' => 'required|integer',
                'description' => 'nullable|string|max:1000',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'memo' => 'nullable|string|max:500',
                'customer_id' => 'required|exists:m_contacts,id',
                // 'discount_amount' => 'nullable|string|max:50',
                // 'subtotal' => 'required|numeric|min:0',
                // 'total_before_discount' => 'required|numeric|min:0',
                // 'tax_amount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                // 'include_tax' => 'required|boolean',
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer',
                'products.*.quantity' => 'required|numeric',
                'products.*.unit_id' => 'required|integer|exists:m_units,id',
                'products.*.unit_price' => 'required|string|max:50',
                // 'products.*.tax_id' => 'required|string|max:50',
                'products.*.total' => 'required|string|max:50',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:5120',
            ], [], [
                'date' => 'Tanggal PO',
                'reference_no' => 'Nomor Referensi',
                'supplier_id' => 'Supplier',
                'email' => 'Email Supplier',
                'address' => 'Alamat Supplier',
                'supplier_reference' => 'Referensi Supplier',
                'payment_terms' => 'Syarat Pembayaran',
                'payment_term_day_count' => 'Jumlah Hari Pembayaran',
                'payment_term_due_date' => 'Jatuh Tempo Pembayaran',
                'warehouse_id' => 'Gudang',
                'description' => 'Deskripsi',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'memo' => 'Memo',
                'customer_id' => 'Customer',
                // 'discount_amount' => 'Diskon',
                // 'subtotal' => 'Subtotal',
                // 'total_before_discount' => 'Total Sebelum Diskon',
                // 'tax_amount' => 'Jumlah Pajak',
                'total' => 'Total',
                // 'include_tax' => 'Harga Termasuk Pajak',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.quantity' => 'Qty Produk',
                'products.*.unit_id' => 'Satuan Produk',
                'products.*.unit_price' => 'Harga Satuan Produk',
                // 'products.*.tax_id' => 'ID Pajak Produk',
                'products.*.total' => 'Total Produk',
                'attachment' => 'Lampiran',
            ]);

            app(CreatePurchaseOrderAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_PURCHASE_ORDER_SUCCESS->message(),
            ], SuccessCodes::CREATE_PURCHASE_ORDER_SUCCESS->httpStatus());
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
        $po_data = app(PurchaseOrderService::class)->findOne($id);
        $po_products = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($id);
        $audit_logs = app(PurchaseOrderAuditLogService::class)->findAllBasedOnPurchaseOrderId($id);

        $purchaseDeliveries = app(PurchaseDeliveryService::class)->findAllHeaderBasedOnPoId($id);
        $purchaseInvoices = app(PurchaseInvoiceService::class)->findAllHeaderBasedOnPoId($id);
        
        // Get return data for this PO
        $purchaseReturns = app(ReturnManagementService::class)->findByPurchaseOrderId($id);

        $auditLogs = [];
        foreach ($audit_logs as $log) {
            $auditLogs[] = [
                'created_at' => $log->created_at,
                'user_id' => $log->user_id,
                'user_name' => $log->user_name,
                'action_type' => $log->action_type,
                'description' => $log->description,
            ];
        }

        $product_lines = [];
        foreach ($po_products as $prod) {
            $product_lines[] = [
                'product_id' => $prod->f_product_id,
                'product_name' => $prod->product_code.' - '.$prod->product_name,
                'description' => '',
                'quantity' => $prod->qty,
                'unit' => $prod->unit_code,
                'unit_price' => $prod->price_per_qty,
                // 'tax_name' => $prod->tax_name,
                'total' => $prod->total,
            ];
        }

        $product_delivery_summary = [];
        foreach ($po_products as $prod) {
            $product_delivery_summary[] = [
                'product_id' => $prod->f_product_id,
                'product_name' => $prod->product_code.' - '.$prod->product_name,
                'total_qty' => $prod->qty,
                'qty_delivered' => $prod->delivered_qty,
                'remaining_qty' => $prod->remaining_qty,
            ];
        }

        $purchase_deliveries = [];
        foreach ($purchaseDeliveries as $row) {
            $purchase_deliveries[] = [
                'id' => $row->id,
                'date' => $row->created_at,
                'reference_no' => $row->transaction_code,
                'status' => $row->delivery_status,
                'status_badge_class' => $this->get_delivery_status_design($row->delivery_status),
                'created_by' => $row->created_by_name,
                'created_by_id' => $row->created_by,
                'tags' => json_decode($row->tags),
            ];
        }

        $purchase_invoices = [];
        foreach ($purchaseInvoices as $row) {
            $purchase_invoices[] = [
                'id' => $row->id,
                'date' => $row->created_at,
                'reference_no' => $row->transaction_code,
                'amount' => $row->grand_total,
                'status' => $row->status,
                'status_badge_class' => $this->get_invoice_status_design($row->status),
                'created_by' => $row->created_by_name,
                'created_by_id' => $row->created_by,
                'tags' => json_decode($row->tags),
            ];
        }

        $purchase_returns = [];
        foreach ($purchaseReturns as $return) {
            $purchase_returns[] = [
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

        $purchaseOrder = [
            'id' => $id,
            'reference_no' => $po_data->transaction_code,
            'date' => $po_data->transaction_date,
            'supplier' => $po_data->supplier_name,
            'supplier_id' => $po_data->f_supplier_id,
            'email' => $po_data->supplier_email,
            'address' => $po_data->supplier_address,
            'supplier_reference' => $po_data->supplier_reference_no,
            'status' => $po_data->status,
            'due_date' => $po_data->payment_term_due_date,
            'payment_terms' => $po_data->payment_term_code.' - '.$po_data->payment_term_name,
            'payment_terms_type' => $po_data->payment_term_type,
            'days' => $po_data->payment_term_day_count,
            'description' => $po_data->description,
            'warehouse' => $po_data->warehouse_code.' - '.$po_data->warehouse_name,
            'customer_name' => $po_data->customer_name,
            // 'include_tax' => $po_data->is_price_include_tax,
            // 'down_payment' => 5000000,
            'total' => $po_data->grand_price,
            // 'subtotal' => $po_data->subtotal,
            // 'tax_amount' => $po_data->total_tax,
            // 'total_before_discount' => $po_data->total_price_before_discount,
            // 'discount_amount' => $po_data->discount,
            'total_paid' => $po_data->total_paid,
            'remaining_bill' => $po_data->remaining_bill,
            'currency' => 'IDR',
            'pic' => 'Ahmad Supardi',
            // 'payment_method' => 'Transfer Bank',
            'tags' => json_decode($po_data->tags),
            'memo' => $po_data->internal_memo,
            // 'notes' => 'Purchase Order untuk botol PET dan kemasan RIYO Water',
            'attachment' => [
                'name' => $po_data->attachment,
                'url' => $po_data->attachment,
            ],
            'created_at' => $po_data->created_at,
            'updated_at' => $po_data->updated_at,
            'product_lines' => $product_lines,
            'delivery_summary' => $product_delivery_summary,
            'purchase_deliveries' => $purchase_deliveries,
            'purchase_invoices' => $purchase_invoices,
            'purchase_returns' => $purchase_returns,
        ];

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'auditLogs' => $auditLogs,
            'canEditPurchaseOrder' => true,
            'canDeletePurchaseOrder' => true,
            'canApprovePurchaseOrder' => true,
            'canViewProduct' => true,
            'canViewUser' => true,
            'canViewPurchaseDelivery' => true,
            'canViewPurchaseInvoice' => true,
            'canCreatePurchaseDelivery' => true,
            'canCreatePurchaseInvoice' => true,
            'canViewReturnManagement' => true,
        ];

        return view('pages.main_transaction.purchase_order.detail', array_merge($data));
    }

    public function auditLogDataTable($id)
    {
        // Mock audit log data - in real implementation, get from audit log service
        $auditLogs = [
            [
                'created_at' => '2025-07-01 10:30:00',
                'user_id' => 1,
                'user_name' => 'Admin System',
                'action_type' => 'UPDATE',
                'description' => 'Status PO diubah dari PENDING_APPROVAL menjadi APPROVED',
            ],
            [
                'created_at' => '2025-07-01 08:00:00',
                'user_id' => 2,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Order dibuat dengan total Rp 25,000,000',
            ],
        ];

        return response()->json(['data' => $auditLogs], 200);
    }

    public function edit($id)
    {
        $customers = app(ContactService::class)->findAllByFlag('employee');
        $po_data = app(PurchaseOrderService::class)->findOne($id);
        $po_products = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($id);
        $audit_logs = app(PurchaseOrderAuditLogService::class)->findAllBasedOnPurchaseOrderId($id);

        $purchaseDeliveries = app(PurchaseDeliveryService::class)->findAllHeaderBasedOnPoId($id);
        $purchaseInvoices = app(PurchaseInvoiceService::class)->findAllHeaderBasedOnPoId($id);

        $auditLogs = [];
        foreach ($audit_logs as $log) {
            $auditLogs[] = [
                'created_at' => $log->created_at,
                'user_id' => $log->user_id,
                'user_name' => $log->user_name,
                'action_type' => $log->action_type,
                'description' => $log->description,
            ];
        }

        $product_lines = [];
        foreach ($po_products as $prod) {
            $product_lines[] = [
                'id' => $prod->id,
                'product_id' => $prod->f_product_id,
                'product_name' => $prod->product_code.' - '.$prod->product_name,
                'description' => '',
                'quantity' => $prod->qty,
                'unit' => $prod->unit_code,
                'unit_price' => $prod->price_per_qty,
                // 'tax_id' => $prod->f_tax_id,
                // 'tax_name' => $prod->tax_name,
                'total' => $prod->total,
            ];
        }

        $product_delivery_summary = [];
        foreach ($po_products as $prod) {
            $product_delivery_summary[] = [
                'product_id' => $prod->f_product_id,
                'product_name' => $prod->product_code.' - '.$prod->product_name,
                'total_qty' => $prod->qty,
                'qty_delivered' => $prod->delivered_qty,
                'remaining_qty' => $prod->remaining_qty,
            ];
        }

        $purchase_deliveries = [];
        foreach ($purchaseDeliveries as $row) {
            $purchase_deliveries[] = [
                'id' => $row->id,
                'date' => $row->created_at,
                'reference_no' => $row->transaction_code,
                'status' => $row->delivery_status,
                'status_badge_class' => $this->get_delivery_status_design($row->delivery_status),
                'created_by' => $row->created_by_name,
                'created_by_id' => $row->created_by,
                'tags' => json_decode($row->tags),
            ];
        }

        $purchase_invoices = [];
        foreach ($purchaseInvoices as $row) {
            $purchase_invoices[] = [
                'id' => $row->id,
                'date' => $row->created_at,
                'reference_no' => $row->transaction_code,
                'amount' => $row->grand_total,
                'status' => $row->status,
                'status_badge_class' => $this->get_invoice_status_design($row->status),
                'created_by' => $row->created_by_name,
                'created_by_id' => $row->created_by,
                'tags' => json_decode($row->tags),
            ];
        }

        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(ProductService::class)->findAll();
        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        $purchaseOrder = [
            'id' => $id,
            'reference_no' => $po_data->transaction_code,
            'date' => $po_data->transaction_date,
            'supplier' => $po_data->supplier_name,
            'supplier_id' => $po_data->f_supplier_id,
            'email' => $po_data->supplier_email,
            'address' => $po_data->supplier_address,
            'supplier_reference' => $po_data->supplier_reference_no,
            'customer_name' => $po_data->customer_name,
            'customer_id' => $po_data->f_customer_id,
            'status' => $po_data->status,
            'due_date' => $po_data->payment_term_due_date,
            'payment_term_id' => $po_data->f_payment_term_id,
            'payment_terms' => $po_data->payment_term_code.' - '.$po_data->payment_term_name,
            'payment_terms_type' => $po_data->payment_term_type,
            'days' => $po_data->payment_term_day_count,
            'description' => $po_data->description,
            'warehouse' => $po_data->warehouse_code.' - '.$po_data->warehouse_name,
            'warehouse_id' => $po_data->f_warehouse_id,
            // 'include_tax' => $po_data->is_price_include_tax,
            // 'down_payment' => 5000000,
            'total' => $po_data->grand_price,
            // 'subtotal' => $po_data->subtotal,
            // 'tax_amount' => $po_data->total_tax,
            // 'total_before_discount' => $po_data->total_price_before_discount,
            // 'discount_amount' => $po_data->discount,
            'total_paid' => $po_data->total_paid,
            'remaining_bill' => $po_data->remaining_bill,
            'currency' => 'IDR',
            'pic' => 'Ahmad Supardi',
            // 'payment_method' => 'Transfer Bank',
            'f_tag_codes' => $po_data->f_tag_codes,
            'tags' => json_decode($po_data->tags),
            'memo' => $po_data->internal_memo,
            // 'notes' => 'Purchase Order untuk botol PET dan kemasan RIYO Water',
            'attachment' => [
                'name' => $po_data->attachment,
                'url' => $po_data->attachment,
            ],
            'created_at' => $po_data->created_at,
            'updated_at' => $po_data->updated_at,
            'product_lines' => $product_lines,
            'delivery_summary' => $product_delivery_summary,
        ];

        // Check permissions for adding related data
        $canAddSuppliers = true;
        $canAddPaymentTerms = true;
        $canAddTags = true;
        $canAddStorages = true;
        $canAddPIC = true;

        // Ensure all fields have default values
        $purchaseOrder['description'] = $purchaseOrder['description'] ?? '';
        $purchaseOrder['memo'] = $purchaseOrder['memo'] ?? '';

        $data = [
            'customers' => $customers,
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'taxOptions' => $taxOptions,
            'products' => $products,
            'paymentTerms' => $paymentTerms,
            'availableTags' => $availableTags,
            'canWrite' => true,
            'canAddSuppliers' => $canAddSuppliers,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'canAddTags' => $canAddTags,
            'canAddStorages' => $canAddStorages,
            'canAddPIC' => $canAddPIC,
        ];

        return view('pages.main_transaction.purchase_order.edit', array_merge($data, $permissionData));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:t_purchase_orders,id',
                'date' => 'required|date',
                'reference_no' => 'required|string|max:255',
                'supplier_id' => 'required|integer',
                'email' => 'nullable|email',
                'address' => 'nullable|string|max:255',
                'supplier_reference' => 'nullable|string|max:255',
                'customer_id' => 'required|exists:m_contacts,id',
                'payment_terms' => 'required|string|max:50',
                'payment_term_day_count' => 'nullable|integer',
                'payment_term_due_date' => 'nullable|date',
                'warehouse_id' => 'required|integer',
                'description' => 'nullable|string|max:1000',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'memo' => 'nullable|string|max:500',
                // 'discount_amount' => 'nullable|string|max:50',
                // 'subtotal' => 'required|numeric|min:0',
                // 'total_before_discount' => 'required|numeric|min:0',
                // 'tax_amount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                // 'include_tax' => 'required|boolean',
                'products' => 'required|array',
                'products.*.id' => 'nullable|integer',
                'products.*.product_id' => 'required|integer',
                'products.*.quantity' => 'required|numeric',
                'products.*.unit' => 'required|string|max:20',
                'products.*.unit_price' => 'required|string|max:50',
                // 'products.*.tax_id' => 'required|string|max:50',
                'products.*.total' => 'required|string|max:50',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:5120',
            ], [], [
                'id' => 'ID PO',
                'date' => 'Tanggal PO',
                'reference_no' => 'Nomor Referensi',
                'supplier_id' => 'Supplier',
                'email' => 'Email Supplier',
                'address' => 'Alamat Supplier',
                'supplier_reference' => 'Referensi Supplier',
                'customer_id' => 'Customer',
                'payment_terms' => 'Syarat Pembayaran',
                'payment_term_day_count' => 'Jumlah Hari Pembayaran',
                'payment_term_due_date' => 'Jatuh Tempo Pembayaran',
                'warehouse_id' => 'Gudang',
                'description' => 'Deskripsi',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'memo' => 'Memo',
                // 'discount_amount' => 'Diskon',
                // 'subtotal' => 'Subtotal',
                // 'total_before_discount' => 'Total Sebelum Diskon',
                // 'tax_amount' => 'Jumlah Pajak',
                'total' => 'Total',
                // 'include_tax' => 'Harga Termasuk Pajak',
                'products' => 'Produk',
                'products.*.id' => 'ID Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.quantity' => 'Qty Produk',
                'products.*.unit' => 'Satuan Produk',
                'products.*.unit_price' => 'Harga Satuan Produk',
                // 'products.*.tax_id' => 'ID Pajak Produk',
                'products.*.total' => 'Total Produk',
                'attachment' => 'Lampiran',
            ]);

            app(UpdatePurchaseOrderAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_PURCHASE_ORDER_SUCCESS->message(),
            ], SuccessCodes::UPDATE_PURCHASE_ORDER_SUCCESS->httpStatus());
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

    // Import/Export Methods
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

        return view('pages.main_transaction.purchase_order.import', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function downloadTemplate(Request $request)
    {

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Order akan segera tersedia.',
            'file_url' => '/templates/purchase_order_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {

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
                    'status' => 'DRAFT',
                ],
            ],
            'validation_key' => 'po_validation_'.time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 5,
            'message' => 'Purchase Order berhasil diimport ke sistem.',
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

        return view('pages.main_transaction.purchase_order.export', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function processExport(Request $request)
    {
        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_orders_export_'.date('Y-m-d_H-i-s').'.'.$format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/'.$filename,
        ]);
    }
}
