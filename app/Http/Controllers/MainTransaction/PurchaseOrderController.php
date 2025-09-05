<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Domains\Product\Services\ProductService;
use App\Domains\PurchaseOrder\Actions\CreatePurchaseOrderAction;
use App\Domains\PurchaseOrder\Actions\UpdatePurchaseOrderAction;
use App\Domains\PurchaseOrder\Services\PurchaseOrderAuditLogService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Domains\PurchaseDelivery\Services\PurchaseDeliveryService;
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
        } else if ($delivery_status == 'MIXED') {
            return 'bg-label-warning';
        } else if ($delivery_status == 'PARTIAL') {
            return 'bg-label-warning';
        } else if ($delivery_status == 'COMPLETE') {
            return 'bg-label-success';
        }
    }
    public function index()
    {
        // Check read permission for purchase order module
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');

        $suppliers = app(ContactService::class)->findAllByType('SUPPLIER');

        // Map permissions to view variables
        $canAddPurchaseOrder = PermissionHelper::canWrite('main_transaction_purchase_order');
        $canImportPurchaseOrder = PermissionHelper::canWrite('main_transaction_purchase_order');
        $canExportPurchaseOrder = PermissionHelper::canRead('main_transaction_purchase_order');
        $canViewPurchaseOrder = PermissionHelper::canRead('main_transaction_purchase_order');
        $canEditPurchaseOrder = PermissionHelper::canWrite('main_transaction_purchase_order');

        $data = [
            'suppliers' => $suppliers,
            'canAddPurchaseOrder' => $canAddPurchaseOrder,
            'canImportPurchaseOrder' => $canImportPurchaseOrder,
            'canExportPurchaseOrder' => $canExportPurchaseOrder,
            'canViewPurchaseOrder' => $canViewPurchaseOrder,
            'canEditPurchaseOrder' => $canEditPurchaseOrder,
        ];

        return view('pages.main_transaction.purchase_order.index', array_merge($data, $permissionData));
    }

    public function index_data_table()
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(PurchaseOrderService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $suppliers = app(ContactService::class)->findAllByType('SUPPLIER');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $warehouses = app(WarehouseService::class)->findAll();
        $products = app(ProductService::class)->findAll();
        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();

        // Check permissions for adding related data
        $canAddSuppliers = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_transaction_number', 'write');
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddStorages = PermissionHelper::hasPermission('master_data_warehouse', 'write');

        return view('pages.main_transaction.purchase_order.add', [
            'suppliers' => $suppliers,
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
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menambah data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

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
                'discount_amount' => 'nullable|string|max:50',
                'subtotal' => 'required|numeric|min:0',
                'total_before_discount' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'include_tax' => 'required|boolean',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer',
                'products.*.quantity' => 'required|numeric|min:1',
                'products.*.unit' => 'required|string|max:20',
                'products.*.unit_price' => 'required|string|max:50',
                'products.*.tax_id' => 'required|string|max:50',
                'products.*.total' => 'required|string|max:50',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
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
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');

        $po_data = app(PurchaseOrderService::class)->findOne($id);
        $po_products = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($id);
        $audit_logs = app(PurchaseOrderAuditLogService::class)->findAllBasedOnPurchaseOrderId($id);

        $purchaseDeliveries = app(PurchaseDeliveryService::class)->findAllHeaderBasedOnPoId($id);

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
                'product_name' => $prod->product_code . ' - ' . $prod->product_name,
                'description' => '',
                'quantity' => $prod->qty,
                'unit' => $prod->unit_code,
                'unit_price' => $prod->price_per_qty,
                'tax_name' => $prod->tax_name,
                'total' => $prod->total,
            ];
        }

        $product_delivery_summary = [];
        foreach ($po_products as $prod) {
            $product_delivery_summary[] = [
                'product_id' => $prod->id,
                'product_name' => $prod->product_code . ' - ' . $prod->product_name,
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
            'payment_terms' => $po_data->payment_term_code . ' - ' . $po_data->payment_term_name,
            'payment_terms_type' => $po_data->payment_term_type,
            'days' => $po_data->payment_term_day_count,
            'description' => $po_data->description,
            'warehouse' => $po_data->warehouse_code . ' - ' . $po_data->warehouse_name,
            'include_tax' => $po_data->is_price_include_tax,
            // 'down_payment' => 5000000,
            'total' => $po_data->grand_price,
            'subtotal' => $po_data->subtotal,
            'tax_amount' => $po_data->total_tax,
            'total_before_discount' => $po_data->total_price_before_discount,
            'discount_amount' => $po_data->discount,
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
            'purchase_invoices' => [
                [
                    'id' => 1,
                    'date' => '2025-07-16 16:00:00',
                    'reference_no' => 'INV-2025-001',
                    'amount' => 5000000,
                    'status' => 'Paid',
                    'status_badge_class' => 'bg-label-success',
                    'created_by' => 'Finance Team',
                    'created_by_id' => 3,
                    'tags' => [
                        ['name' => 'Down Payment', 'preview' => 'bg-label-primary'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-25 11:30:00',
                    'reference_no' => 'INV-2025-002',
                    'amount' => 20000000,
                    'status' => 'Pending',
                    'status_badge_class' => 'bg-label-warning',
                    'created_by' => 'Finance Team',
                    'created_by_id' => 3,
                    'tags' => [
                        ['name' => 'Final Payment', 'preview' => 'bg-label-info'],
                    ],
                ],
            ],
        ];

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'auditLogs' => $auditLogs,
            'canEditPurchaseOrder' => PermissionHelper::canWrite('main_transaction_purchase_order'),
            'canDeletePurchaseOrder' => PermissionHelper::canDelete('main_transaction_purchase_order'),
            'canApprovePurchaseOrder' => PermissionHelper::canApprove('main_transaction_purchase_order'),
            'canViewProduct' => PermissionHelper::canRead('master_data_product'),
            'canViewUser' => PermissionHelper::canRead('master_data_user'),
            'canViewPurchaseDelivery' => PermissionHelper::canRead('main_transaction_purchase_delivery'),
            'canViewPurchaseInvoice' => PermissionHelper::canRead('main_transaction_purchase_invoice'),
            'canCreatePurchaseDelivery' => PermissionHelper::canWrite('main_transaction_purchase_delivery'),
        ];

        return view('pages.main_transaction.purchase_order.detail', array_merge($data, $permissionData));
    }

    public function auditLogDataTable($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase_order');

        $po_data = app(PurchaseOrderService::class)->findOne($id);
        $po_products = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($id);
        $audit_logs = app(PurchaseOrderAuditLogService::class)->findAllBasedOnPurchaseOrderId($id);

        // dd($po_data);
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
                'product_name' => $prod->product_code . ' - ' . $prod->product_name,
                'description' => '',
                'quantity' => $prod->qty,
                'unit' => $prod->unit_code,
                'unit_price' => $prod->price_per_qty,
                'tax_id' => $prod->f_tax_id,
                'tax_name' => $prod->tax_name,
                'total' => $prod->total,
            ];
        }

        $suppliers = app(ContactService::class)->findAllByType('SUPPLIER');
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
            'status' => $po_data->status,
            'due_date' => $po_data->payment_term_due_date,
            'payment_term_id' => $po_data->f_payment_term_id,
            'payment_terms' => $po_data->payment_term_code . ' - ' . $po_data->payment_term_name,
            'payment_terms_type' => $po_data->payment_term_type,
            'days' => $po_data->payment_term_day_count,
            'description' => $po_data->description,
            'warehouse' => $po_data->warehouse_code . ' - ' . $po_data->warehouse_name,
            'warehouse_id' => $po_data->f_warehouse_id,
            'include_tax' => $po_data->is_price_include_tax,
            // 'down_payment' => 5000000,
            'total' => $po_data->grand_price,
            'subtotal' => $po_data->subtotal,
            'tax_amount' => $po_data->total_tax,
            'total_before_discount' => $po_data->total_price_before_discount,
            'discount_amount' => $po_data->discount,
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
            'delivery_summary' => [
                [
                    'product_id' => 1,
                    'product_name' => 'PRD-001 - Botol PET 330ml',
                    'total_qty' => 10000,
                    'qty_delivered' => 7500,
                    'remaining_qty' => 2500,
                ],
                [
                    'product_id' => 2,
                    'product_name' => 'PRD-002 - Label Kemasan',
                    'total_qty' => 10000,
                    'qty_delivered' => 10000,
                    'remaining_qty' => 0,
                ],
            ],
            'purchase_deliveries' => [
                [
                    'id' => 1,
                    'date' => '2025-07-15 14:30:00',
                    'reference_no' => 'DEL-2025-001',
                    'status' => 'Completed',
                    'status_badge_class' => 'bg-label-success',
                    'created_by' => 'Staff Gudang A',
                    'created_by_id' => 5,
                    'tags' => [
                        ['name' => 'Partial', 'preview' => 'bg-label-warning'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-20 09:15:00',
                    'reference_no' => 'DEL-2025-002',
                    'status' => 'In Transit',
                    'status_badge_class' => 'bg-label-info',
                    'created_by' => 'Staff Gudang B',
                    'created_by_id' => 6,
                    'tags' => [
                        ['name' => 'Final', 'preview' => 'bg-label-success'],
                    ],
                ],
            ],
            'purchase_invoices' => [
                [
                    'id' => 1,
                    'date' => '2025-07-16 16:00:00',
                    'reference_no' => 'INV-2025-001',
                    'amount' => 5000000,
                    'status' => 'Paid',
                    'status_badge_class' => 'bg-label-success',
                    'created_by' => 'Finance Team',
                    'created_by_id' => 3,
                    'tags' => [
                        ['name' => 'Down Payment', 'preview' => 'bg-label-primary'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-25 11:30:00',
                    'reference_no' => 'INV-2025-002',
                    'amount' => 20000000,
                    'status' => 'Pending',
                    'status_badge_class' => 'bg-label-warning',
                    'created_by' => 'Finance Team',
                    'created_by_id' => 3,
                    'tags' => [
                        ['name' => 'Final Payment', 'preview' => 'bg-label-info'],
                    ],
                ],
            ],
        ];

        // Check permissions for adding related data
        $canAddSuppliers = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_transaction_number', 'write');
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddStorages = PermissionHelper::hasPermission('master_data_warehouse', 'write');
        $canAddPIC = PermissionHelper::hasPermission('master_data_user', 'write');

        // Ensure all fields have default values
        $purchaseOrder['description'] = $purchaseOrder['description'] ?? '';
        $purchaseOrder['memo'] = $purchaseOrder['memo'] ?? '';

        $data = [
            'purchaseOrder' => $purchaseOrder,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'taxOptions' => $taxOptions,
            'products' => $products,
            'paymentTerms' => $paymentTerms,
            'availableTags' => $availableTags,
            'canWrite' => PermissionHelper::canWrite('main_transaction_purchase_order'),
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
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|exists:t_purchase_orders,id',
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
                'discount_amount' => 'nullable|string|max:50',
                'subtotal' => 'required|numeric|min:0',
                'total_before_discount' => 'required|numeric|min:0',
                'tax_amount' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'include_tax' => 'required|boolean',
                'products' => 'required|array|min:1',
                'products.*.id' => 'nullable|integer',
                'products.*.product_id' => 'required|integer',
                'products.*.quantity' => 'required|numeric|min:1',
                'products.*.unit' => 'required|string|max:20',
                'products.*.unit_price' => 'required|string|max:50',
                'products.*.tax_id' => 'required|string|max:50',
                'products.*.total' => 'required|string|max:50',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:10240',
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
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
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

        return view('pages.main_transaction.purchase_order.import', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Check write permission for template download
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Order akan segera tersedia.',
            'file_url' => '/templates/purchase_order_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import validation
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
            'validation_key' => 'po_validation_' . time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Check write permission for import processing
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 5,
            'message' => 'Purchase Order berhasil diimport ke sistem.',
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
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

        return view('pages.main_transaction.purchase_order.export', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export processing
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_orders_export_' . date('Y-m-d_H-i-s') . '.' . $format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/' . $filename,
        ]);
    }
}
