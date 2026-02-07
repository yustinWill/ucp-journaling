<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\PurchaseInvoice\Actions\CreatePurchaseInvoiceAction;
use App\Domains\PurchaseInvoice\Actions\UpdatePurchaseInvoiceAction;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceAuditLogService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Tax\Services\TaxService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PurchaseInvoiceController extends Controller
{
    public function index()
    {
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');

        $statuses = [
            // ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            // ['value' => 'UNPAID', 'label' => 'UNPAID - Belum Dibayar'],
            // ['value' => 'PAID', 'label' => 'PAID - Lunas'],
            ['value' => 'PARTIAL', 'label' => 'PARTIAL - Sebagian Dibayar'],
            // ['value' => 'OVERDUE', 'label' => 'OVERDUE - Jatuh Tempo'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Sudah Dibayar'],
        ];

        // Map permissions to view variables - following user requirement to remove add/import buttons
        $canAddPurchaseInvoice = false; // Disabled per requirement - creation only from PO detail page
        $canImportPurchaseInvoice = false; // Disabled per requirement
        $canExportPurchaseInvoice = true;
        $canViewPurchaseInvoice = true;
        $canEditPurchaseInvoice = true;

        $data = [
            'suppliers' => $suppliers,
            'statuses' => $statuses,
            'canAddPurchaseInvoice' => $canAddPurchaseInvoice,
            'canImportPurchaseInvoice' => $canImportPurchaseInvoice,
            'canExportPurchaseInvoice' => $canExportPurchaseInvoice,
            'canViewPurchaseInvoice' => $canViewPurchaseInvoice,
            'canEditPurchaseInvoice' => $canEditPurchaseInvoice,
            'canViewUser' => true,
            'canViewPurchaseOrder' => true,
        ];

        return view('pages.main_transaction.purchase_invoice.index', array_merge($data));
    }

    public function index_data_table(Request $request)
    {
        $data = app(PurchaseInvoiceService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        // Check if coming from PO detail page
        $preselectedPOId = $request->get('po_id');

        // Get Purchase Order data and products for invoicing
        $purchaseOrder = app(PurchaseOrderService::class)->findOne($preselectedPOId);

        $purchaseOrderProducts = app(PurchaseOrderProductService::class)->findAllBasedOnPOIdForInvoicing($preselectedPOId);

        $availableTags = app(TagService::class)->findAll();

        // Get tax options for discount/tax calculations
        // $taxOptions = app(TaxService::class)->findAllForOptions();

        // Permission checks for related data creation
        $canAddTags = true;

        return view('pages.main_transaction.purchase_invoice.add', [
            'purchaseOrder' => $purchaseOrder,
            'purchaseOrderProducts' => $purchaseOrderProducts,
            'availableTags' => $availableTags,
            // 'taxOptions' => $taxOptions,
            'canAddTags' => $canAddTags,
            'preselectedPOId' => $preselectedPOId,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_date' => 'required|date',
                'invoice_number' => 'required|string|max:50',
                'purchase_order_id' => 'required|integer|exists:t_purchase_orders,id',
                'transaction_date' => 'required|date',
                'due_date' => 'nullable|date',
                'description' => 'nullable|string|max:255',
                'internal_memo' => 'nullable|string|max:255',
                'invoice_status' => 'required|in:COMPLETED,PARTIAL',
                'tags' => 'nullable|array',
                'tags.*' => 'integer',
                'attachment' => 'nullable|file|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip|max:5120',
                'total_amount' => 'required|numeric|min:0',
                'total_items' => 'required|integer',
                'products' => 'required|array',
                'products.*.product_order_product_id' => 'required|integer|exists:t_purchase_order_products,id',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.po_qty' => 'required|numeric|min:0',
                'products.*.invoiced_qty_before' => 'required|numeric|min:0',
                'products.*.remaining_qty_before' => 'required|numeric|min:0',
                'products.*.invoice_qty' => 'required|numeric|min:0',
                'products.*.unit_price' => 'required|numeric|min:0',
                'products.*.total_price' => 'required|numeric|min:0',
            ], [], [
                'invoice_date' => 'Tanggal Invoice',
                'invoice_number' => 'Nomor Invoice',
                'purchase_order_id' => 'Purchase Order',
                'transaction_date' => 'Tanggal Transaksi',
                'due_date' => 'Tanggal Jatuh Tempo',
                'description' => 'Deskripsi',
                'internal_memo' => 'Memo Internal',
                'invoice_status' => 'Status Invoice',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'attachment' => 'Lampiran',
                'total_amount' => 'Total Amount',
                'total_items' => 'Total Item',
                'products' => 'Produk',
                'products.*.product_order_product_id' => 'ID Produk PO',
                'products.*.product_id' => 'ID Produk',
                'products.*.po_qty' => 'Qty PO',
                'products.*.invoiced_qty_before' => 'Qty Sudah Diinvoice',
                'products.*.remaining_qty_before' => 'Qty Sisa',
                'products.*.invoice_qty' => 'Qty Invoice',
                'products.*.unit_price' => 'Harga Satuan',
                'products.*.total_price' => 'Total Harga',
            ]);

            app(CreatePurchaseInvoiceAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_PURCHASE_INVOICE_SUCCESS->message(),
            ], SuccessCodes::CREATE_PURCHASE_INVOICE_SUCCESS->httpStatus());
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
        $invoice_data = app(PurchaseInvoiceService::class)->findOne($id);
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $invoice_products = app(PurchaseInvoiceService::class)->findAllProducts($id);
        $auditLogs = app(PurchaseInvoiceAuditLogService::class)->findAllBasedOnInvoiceId($id);

        // Get return data for this invoice (one invoice can only have one return)
        $returnData = app(ReturnManagementService::class)->findBySourceInvoiceId($id, 'PURCHASE_RETURN');

        // Get tax options for discount/tax calculations (for potential edits)
        // $taxOptions = app(TaxService::class)->findAllForOptions();

        // Permission checks
        $canEditPurchaseInvoice = true;
        $canViewUser = true;
        $canViewPurchaseOrder = true;
        $canViewReturnManagement = true;
        $canViewProduct = true;

        return view('pages.main_transaction.purchase_invoice.detail', [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
            'auditLogs' => $auditLogs,
            'returnData' => $returnData,
            // 'taxOptions' => $taxOptions,
            'canEditPurchaseInvoice' => $canEditPurchaseInvoice,
            'canViewUser' => $canViewUser,
            'canViewPurchaseOrder' => $canViewPurchaseOrder,
            'canViewReturnManagement' => $canViewReturnManagement,
            'canViewProduct' => $canViewProduct,
        ]);
    }

    public function auditLogDataTable($id)
    {
        // Mock audit log data - in real implementation, get from audit log service
        $auditLogs = [
            [
                'created_at' => '2025-07-15 16:00:00',
                'user_id' => 1,
                'user_name' => 'Finance Staff',
                'action_type' => 'UPDATE',
                'description' => 'Status invoice diubah dari PENDING_PAYMENT menjadi PAID',
            ],
            [
                'created_at' => '2025-07-12 14:30:00',
                'user_id' => 2,
                'user_name' => 'Accounting Staff',
                'action_type' => 'UPDATE',
                'description' => 'Invoice amount diupdate dari Rp 20,000,000 menjadi Rp 25,000,000',
            ],
            [
                'created_at' => '2025-07-10 09:00:00',
                'user_id' => 3,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase Invoice dibuat dengan total Rp 25,000,000 untuk PO-2025-001',
            ],
        ];

        return response()->json(['data' => $auditLogs], 200);
    }

    public function edit($id)
    {
        $invoice_data = app(PurchaseInvoiceService::class)->findOne($id);
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $invoice_products = app(PurchaseInvoiceService::class)->findAllProducts($id);
        $availableTags = app(TagService::class)->findAll();

        // Get tax options for discount/tax calculations
        // $taxOptions = app(TaxService::class)->findAllForOptions();

        // Permission checks for related data creation
        $canAddTags = true;

        return view('pages.main_transaction.purchase_invoice.edit', [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
            'availableTags' => $availableTags,
            // 'taxOptions' => $taxOptions,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function update(Request $request)
    {

        // try {
        //     $id = $request->input('id');
        //     if (! $id) {
        //         return response()->json(['error' => 'Invoice ID is required'], 400);
        //     }

        //     $action = app(UpdatePurchaseInvoiceAction::class);
        //     $result = $action->handle($request, $id);

        //     if ($result['success']) {
        //         return response()->json([
        //             'success' => true,
        //             'message' => 'Invoice berhasil diperbarui.',
        //             'data' => $result['data'],
        //         ], SuccessCodes::UPDATE_SUCCESS->httpStatus());
        //     } else {
        //         return response()->json([
        //             'success' => false,
        //             'message' => $result['message'],
        //         ], 422);
        //     }
        // } catch (AppException $e) {
        //     DiscordNotifier::notify(ErrorCodes::PURCHASE_INVOICE_UPDATE_FAILED, $e->getMessage(), ['request' => $request->all()]);

        //     return response()->json([
        //         'success' => false,
        //         'message' => $e->getMessage(),
        //     ], $e->getHttpStatusCode());
        // } catch (\Exception $e) {
        //     DiscordNotifier::notify(ErrorCodes::PURCHASE_INVOICE_UPDATE_FAILED, $e->getMessage(), ['request' => $request->all()]);

        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Terjadi kesalahan sistem saat memperbarui invoice.',
        //     ], 500);
        // }
    }

    // Import/Export Methods
    public function import(Request $request)
    {
        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
        ];

        return view('pages.main_transaction.purchase_invoice.import', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function export()
    {
        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
        ];

        return view('pages.main_transaction.purchase_invoice.export', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Purchase Invoice akan segera tersedia.',
            'file_url' => '/templates/purchase_invoice_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {

        // Mock validation - in real implementation, validate uploaded file
        $mockValidationResults = [
            'success' => true,
            'valid_count' => 4,
            'errors' => [],
            'valid_data' => [
                [
                    'reference_no' => 'PI-2025-100',
                    'date' => '2025-07-25',
                    'supplier' => 'CV Plastik Prima',
                    'po_reference' => 'PO-2025-001',
                    'invoice_amount' => 15000000,
                    'status' => 'DRAFT',
                ],
            ],
            'validation_key' => 'pi_validation_'.time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 4,
            'message' => 'Purchase Invoice berhasil diimport ke sistem.',
        ]);
    }

    public function processExport(Request $request)
    {
        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'purchase_invoices_export_'.date('Y-m-d_H-i-s').'.'.$format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/'.$filename,
        ]);
    }

    public function get_po_products_for_invoice(Request $request)
    {
        try {
            $poId = $request->input('po_id');
            if (! $poId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase Order ID diperlukan.',
                ], 400);
            }

            $products = app(PurchaseOrderProductService::class)->findAllBasedOnPOIdForInvoicing($poId);

            return response()->json([
                'success' => true,
                'data' => $products,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data produk.',
            ], 500);
        }
    }

    public function get_available_purchase_orders()
    {
        try {
            $purchaseOrders = app(PurchaseInvoiceService::class)->findAvailableForInvoicing();

            return response()->json([
                'success' => true,
                'data' => $purchaseOrders,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data Purchase Order.',
            ], 500);
        }
    }
}
