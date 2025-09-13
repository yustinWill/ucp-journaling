<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Contact\Services\ContactService;
use App\Domains\SalesInvoice\Actions\CreateSalesInvoiceAction;
use App\Domains\SalesInvoice\Actions\UpdateSalesInvoiceAction;
use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesOrder\Services\SalesOrderProductService;
use App\Domains\Tag\Services\TagService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        // Check read permission for sales invoice module
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales_invoice');

        $customers = app(ContactService::class)->findAllByType('CUSTOMER');

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'UNPAID', 'label' => 'UNPAID - Belum Dibayar'],
            ['value' => 'PAID', 'label' => 'PAID - Lunas'],
            ['value' => 'PARTIAL_PAID', 'label' => 'PARTIAL_PAID - Sebagian Dibayar'],
            ['value' => 'OVERDUE', 'label' => 'OVERDUE - Jatuh Tempo'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Map permissions to view variables
        $canAddSalesInvoice = PermissionHelper::canWrite('main_transaction_sales_invoice');
        $canImportSalesInvoice = PermissionHelper::canWrite('main_transaction_sales_invoice');
        $canExportSalesInvoice = PermissionHelper::canRead('main_transaction_sales_invoice');
        $canViewSalesInvoice = PermissionHelper::canRead('main_transaction_sales_invoice');
        $canEditSalesInvoice = PermissionHelper::canWrite('main_transaction_sales_invoice');

        $data = [
            'customers' => $customers,
            'statuses' => $statuses,
            'canAddSalesInvoice' => $canAddSalesInvoice,
            'canImportSalesInvoice' => $canImportSalesInvoice,
            'canExportSalesInvoice' => $canExportSalesInvoice,
            'canViewSalesInvoice' => $canViewSalesInvoice,
            'canEditSalesInvoice' => $canEditSalesInvoice,
            'canViewUser' => PermissionHelper::canRead('master_data_user'),
            'canViewSalesOrder' => PermissionHelper::canRead('main_transaction_sales_order'),
        ];

        return view('pages.main_transaction.sales_invoice.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(SalesInvoiceService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        // Get available Sales Orders for invoicing
        $availableSalesOrders = app(SalesInvoiceService::class)->findAvailableForInvoicing();

        $availableTags = app(TagService::class)->findAll();

        // Permission checks for related data creation
        $canAddTags = PermissionHelper::canWrite('master_data_tag');

        return view('pages.main_transaction.sales_invoice.add', [
            'availableSalesOrders' => $availableSalesOrders,
            'availableTags' => $availableTags,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function store(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Use validation from CreateSalesInvoiceAction
            $validation = app(CreateSalesInvoiceAction::class)->validateSalesInvoiceData($request->all());

            if (! $validation['is_valid']) {
                return response()->json([
                    'message' => implode(', ', $validation['errors']),
                    'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            $invoiceId = app(CreateSalesInvoiceAction::class)->execute($request->all());

            return response()->json([
                'message' => 'Sales Invoice berhasil dibuat',
                'invoice_id' => $invoiceId,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());

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
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail invoice.');
        }

        $invoice_data = app(SalesInvoiceService::class)->findOne($id);
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $invoice_products = app(SalesInvoiceService::class)->findAllProducts($id);
        $auditLogs = app(SalesInvoiceAuditLogService::class)->findAllBySalesInvoiceId($id);

        // Permission checks
        $canEditSalesInvoice = PermissionHelper::canWrite('main_transaction_sales_invoice');
        $canViewUser = PermissionHelper::canRead('master_data_user');
        $canViewSalesOrder = PermissionHelper::canRead('main_transaction_sales_order');

        return view('pages.main_transaction.sales_invoice.detail', [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
            'auditLogs' => $auditLogs,
            'canEditSalesInvoice' => $canEditSalesInvoice,
            'canViewUser' => $canViewUser,
            'canViewSalesOrder' => $canViewSalesOrder,
        ]);
    }

    public function auditLogDataTable($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(SalesInvoiceAuditLogService::class)->findAllDatatableBySalesInvoiceId($id);

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function edit($id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit invoice.');
        }

        $invoice_data = app(SalesInvoiceService::class)->findOne($id);
        if (! $invoice_data) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $invoice_products = app(SalesInvoiceService::class)->findAllProducts($id);
        $availableTags = app(TagService::class)->findAll();

        // Permission checks for related data creation
        $canAddTags = PermissionHelper::canWrite('master_data_tag');

        return view('pages.main_transaction.sales_invoice.edit', [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products,
            'availableTags' => $availableTags,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }

        $customers = app(ContactService::class)->findAllByType('CUSTOMER');

        return view('pages.main_transaction.sales_invoice.import', [
            'customers' => $customers,
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }

        $customers = app(ContactService::class)->findAllByType('CUSTOMER');

        return view('pages.main_transaction.sales_invoice.export', [
            'customers' => $customers,
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Check write permission for template download
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }

        // Mock template download - in real implementation, generate Excel file
        return response()->json([
            'message' => 'Template download untuk Sales Invoice akan segera tersedia.',
            'file_url' => '/templates/sales_invoice_template.xlsx',
        ]);
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import validation
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
            'validation_key' => 'si_validation_'.time(),
        ];

        return response()->json($mockValidationResults);
    }

    public function processImport(Request $request)
    {
        // Check write permission for import processing
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock import processing
        return response()->json([
            'success' => true,
            'imported_count' => 3,
            'message' => 'Sales Invoice berhasil diimport ke sistem.',
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export processing
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mock export processing - in real implementation, generate file
        $format = $request->input('export_format', 'excel');
        $filename = 'sales_invoices_export_'.date('Y-m-d_H-i-s').'.'.$format;

        return response()->json([
            'success' => true,
            'message' => 'Export berhasil diproses.',
            'filename' => $filename,
            'download_url' => '/exports/'.$filename,
        ]);
    }

    public function get_so_products_for_invoice(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $soId = $request->input('so_id');
            if (! $soId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales Order ID diperlukan.',
                ], 400);
            }

            // This would need to be implemented in SalesOrderProductService
            // For now, return empty array until the service method is created
            $products = [];

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
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
}
