<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Domains\SalesInvoice\Actions\CreateSalesInvoiceAction;
use App\Domains\SalesInvoice\Actions\PaymentSalesInvoiceAction;
use App\Domains\SalesInvoice\Actions\UpdateSalesInvoiceAction;
use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesInvoice\Services\SalesInvoicePaymentService;
use App\Domains\SalesOrder\Services\SalesOrderService;
use App\Services\TransactionNumberService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Tax\Services\TaxService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\PdfHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        $customers = app(ContactService::class)->findAllByFlag('customer');

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'UNPAID', 'label' => 'UNPAID - Belum Dibayar'],
            ['value' => 'PAID', 'label' => 'PAID - Lunas'],
            ['value' => 'PARTIAL_PAID', 'label' => 'PARTIAL_PAID - Sebagian Dibayar'],
            ['value' => 'OVERDUE', 'label' => 'OVERDUE - Jatuh Tempo'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_invoice');
        $data = [
            'customers' => $customers,
            'statuses' => $statuses,
        ];

        return view('pages.main_transaction.sales_invoice.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        $data = app(SalesInvoiceService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        $so_id = $request->query('so_id');

        // Get available Sales Orders for invoicing
        $availableSalesOrders = app(SalesInvoiceService::class)->findAvailableForInvoicing();

        $availableTags = app(TagService::class)->findAll();

        // Get tax options for discount/tax calculations
        // $taxOptions = app(TaxService::class)->findAll();

        // Permission checks for related data creation
        $canAddTags = true;

        return view('pages.main_transaction.sales_invoice.add', [
            'availableSalesOrders' => $availableSalesOrders,
            'availableTags' => $availableTags,
            // 'taxOptions' => $taxOptions,
            'canAddTags' => $canAddTags,
            'so_id' => $so_id,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'invoice_number' => 'required|string|max:50|unique:t_sales_invoices,transaction_code',
                'invoice_date' => 'required|date',
                'transaction_date' => 'required|date',
                'due_date' => 'nullable|date',
                'description' => 'nullable|string|max:255',
                'memo' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'sales_order_id' => 'required|integer|exists:t_sales_orders,id',
                'total_amount' => 'required|numeric|min:0',
                'total_items' => 'required|integer',
                'products' => 'required|array',
                'products.*.sales_order_product_id' => 'required|integer|exists:t_sales_order_products,id',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.po_qty' => 'required|numeric|min:0',
                'products.*.invoice_qty' => 'required|numeric|min:0.01',
                'products.*.remaining_qty_before' => 'required|numeric|min:0',
                'products.*.unit_price' => 'required|numeric|min:0',
            ], [], [
                'invoice_number' => 'Nomor Invoice',
                'invoice_date' => 'Tanggal Invoice',
                'transaction_date' => 'Tanggal Transaksi',
                'due_date' => 'Tanggal Jatuh Tempo',
                'description' => 'Deskripsi',
                'memo' => 'Memo',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'sales_order_id' => 'Sales Order',
                'total_amount' => 'Total Amount',
                'total_items' => 'Total Item',
                'products' => 'Produk',
                'products.*.sales_order_product_id' => 'ID Produk Sales Order',
                'products.*.product_id' => 'ID Produk',
                'products.*.po_qty' => 'Qty PO',
                'products.*.invoice_qty' => 'Qty Invoice',
                'products.*.remaining_qty_before' => 'Qty Sisa Sebelum',
                'products.*.unit_price' => 'Harga Satuan',
            ]);

            app(CreateSalesInvoiceAction::class)->execute($request->all());

            return response()->json([
                'message' => SuccessCodes::CREATE_SALES_INVOICE_SUCCESS->message(),
            ], SuccessCodes::CREATE_SALES_INVOICE_SUCCESS->httpStatus());
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
        $invoice_data = app(SalesInvoiceService::class)->findOne($id);
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $invoice_products = app(SalesInvoiceService::class)->findAllProducts($id);
        $auditLogs = app(SalesInvoiceAuditLogService::class)->findAllBySalesInvoiceId($id);


        // Get return data for this invoice (one invoice can only have one return)
        $returnData = app(ReturnManagementService::class)->findBySourceInvoiceId($id, 'SALES_RETURN');

        // Check if Sales Delivery is complete (DELIVERED status)
        $hasCompletedDelivery = false;
        if ($invoice_data->f_sales_order_id) {
            $hasCompletedDelivery = DB::table('t_sales_deliveries')
                ->where('sales_order_id', $invoice_data->f_sales_order_id)
                ->where('status', 'DELIVERED')
                ->exists();
        }

        // Get tax options for discount/tax calculations (for potential edits)
        // $taxOptions = app(TaxService::class)->findAllForOptions();

        // Permission checks
        $canEditSalesInvoice = true;
        $canViewUser = true;
        $canViewSalesOrder = true;
        $canViewReturnManagement = true;

        return view('pages.main_transaction.sales_invoice.detail', [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
            'auditLogs' => $auditLogs,
            'returnData' => $returnData,
            'hasCompletedDelivery' => $hasCompletedDelivery,
            // 'taxOptions' => $taxOptions,
            'canEditSalesInvoice' => $canEditSalesInvoice,
            'canViewUser' => $canViewUser,
            'canViewSalesOrder' => $canViewSalesOrder,
            'canViewReturnManagement' => $canViewReturnManagement,
        ]);
    }
    public function payment_recipient(request $request)
    {
        $source_invoice_id = $request->query('source_invoice_id');

        $invoice_data = app(SalesInvoiceService::class)->findOne($source_invoice_id); 
        $remaining_payment = app(SalesInvoicePaymentService::class)->getRemainingPayment($source_invoice_id); 
        $invoice_data->remaining_payment = $invoice_data->subtotal - $remaining_payment;
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $customers = app(ContactService::class)->findAllByFlag('customer');
        $coa_accounts = app(CoaService::class)->findAll();

        $payment_method = [
            ['value' => 'CASH', 'label' => 'Kas atau Tunai'],
            ['value' => 'BANK', 'label' => 'Transfer Bank'],
            ['value' => 'Other', 'label' => 'Dll']
        ];
        // Get tags with color preview
        $tags = DB::table('m_tags as t')
            ->select([
                't.id',
                't.name',
                't.code',
                DB::raw("CONCAT('bg-label-', tc.code) as preview")
            ])
            ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
            ->where('t.company_code', session('user_company_code'))
            ->where('t.status', 'ACTIVE')
            ->orderBy('t.code', 'ASC')
            ->get();
        return view('pages.main_transaction.sales_invoice.payment_recipient', [
            'customers' => $customers,
            'coa_accounts' => $coa_accounts,
            'availableTags' => $tags, // Pass $tags as $availableTags
            'invoice_data' => $invoice_data,
            'payment_method'=>$payment_method
        ]);
        
    }
    public function generateTransactionNumber()
    {
        try {
            $transactionNumber = app(TransactionNumberService::class)->generateNumber('asset_purchase'); 
            // sementara ntar module nya ganti

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
    public function auditLogDataTable($id)
    {

        $data = app(SalesInvoiceAuditLogService::class)->findAllDatatableBySalesInvoiceId($id);

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function edit($id)
    {
        $invoice_data = app(SalesInvoiceService::class)->findOne($id);
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $invoice_products = app(SalesInvoiceService::class)->findAllProducts($id);
        $availableTags = app(TagService::class)->findAll();

        // Get tax options for discount/tax calculations
        // $taxOptions = app(TaxService::class)->findAllForOptions();

        // Permission checks for related data creation
        $canAddTags = true;

        return view('pages.main_transaction.sales_invoice.edit', [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
            'availableTags' => $availableTags,
            // 'taxOptions' => $taxOptions,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            // Use validation from UpdateSalesInvoiceAction
            $validation = app(UpdateSalesInvoiceAction::class)->validateUpdateData($request->all());

            if (! $validation['is_valid']) {
                return response()->json([
                    'message' => implode(', ', $validation['errors']),
                    'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            $result = app(UpdateSalesInvoiceAction::class)->execute($id, $request->all());

            return response()->json([
                'message' => 'Sales Invoice berhasil diperbarui',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
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
        $customers = app(ContactService::class)->findAllByFlag('customer');

        return view('pages.main_transaction.sales_invoice.import', [
            'customers' => $customers,
        ]);
    }

    public function export()
    {
        $customers = app(ContactService::class)->findAllByFlag('customer');

        return view('pages.main_transaction.sales_invoice.export', [
            'customers' => $customers,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Sales Invoice akan segera tersedia.',
            'file_url' => '/templates/sales_invoice_template.xlsx',
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
                    'invoice_number' => 'SI-2025-100',
                    'invoice_date' => '2025-07-25',
                    'customer' => 'PT Maju Bersama',
                    'so_reference' => 'SO-2025-001',
                    'grand_total' => 25000000,
                    'status' => 'UNPAID',
                ],
            ],
            'validation_key' => 'si_validation_' . time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 3,
            'message' => 'Sales Invoice berhasil diimport ke sistem.',
        ]);
    }

    public function processExport(Request $request)
    {
        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'sales_invoices_export_' . date('Y-m-d_H-i-s') . '.' . $format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/' . $filename,
        ]);
    }

    public function get_so_products_for_invoice(Request $request)
    {
        try {
            $soId = $request->input('so_id');

            if (! $soId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales Order ID diperlukan.',
                ], 400);
            }
            $products = app(SalesOrderService::class)->findProductLines($soId, true);

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

    public function get_available_sales_orders()
    {
        try {
            $salesOrders = app(SalesInvoiceService::class)->findAvailableForInvoicing();

            return response()->json([
                'success' => true,
                'data' => $salesOrders,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data Sales Order.',
            ], 500);
        }
    }
    
    public function payment_save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:m_contacts,id',
                'account_code' => 'required|string|max:20',
                'payment_method' => 'required|string|max:20',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'memo' => 'nullable|string|max:255',
                'reference_number' => 'required|string|unique:t_sales_invoice_payment,reference_number',
                'sales_invoice_id' => 'required|integer|exists:t_sales_invoices,id',
                'payment_value' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'deduction_value' => 'nullable|numeric|min:0',
                'deduction_account_code' => 'nullable|string|max:20',
            ], [], [
                'customer_id' => 'Pelanggan',
                'account_code' => 'Akun Setor',
                'payment_method' => 'Cara Pembayaran',
                'memo' => 'Memo',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'sales_invoice_id' => 'Sales Invoice Id',
                'payment_value' => 'Payment Amount',
                'payment_date' => 'Tanggal Pembayaran',
                'reference_number' => 'Nomor Transaksi',
                'deduction_value' => 'Nilai Potongan',
                'deduction_account_code' => 'Akun Potongan',
            ]);
            $validator->after(function ($validator) use ($request) {
                $remaining = floatval($request->input('remaining_payment'));
                $payment  = floatval($request->input('payment_value'));

                if ($payment > $remaining) {
                    $validator->errors()->add('payment_value', 'Payment value cannot be more than remaining payment.');
                }
            });

            $validated = $validator->validate();

            app(PaymentSalesInvoiceAction::class)->execute($request->all());

            return response()->json([
                'message' => SuccessCodes::CREATE_SALES_INVOICE_PAYMENT_SUCCESS->message(),
            ], SuccessCodes::CREATE_SALES_INVOICE_PAYMENT_SUCCESS->httpStatus());
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

    /**
     * Print Sales Invoice PDF
     *
     * @param int $id - Sales Invoice ID
     * @return \Illuminate\Http\Response
     */
    public function print($id)
    {
        try {
            // Generate printable HTML
            $html = PdfHelper::getPrintableHtml('sales_invoice', $id);

            return response($html)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);
            abort(500, 'Terjadi kesalahan saat mencetak invoice: ' . $e->getMessage());
        }
    }
}
