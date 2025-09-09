<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Domains\SalesInvoice\Actions\CreateSalesInvoiceAction;
use App\Domains\SalesInvoice\Actions\UpdateSalesInvoiceAction;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Helpers\PermissionHelper;
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;

class SalesInvoiceController extends Controller
{
    public function index()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        return view('pages.main_transaction.sales_invoice.index', [
            'canAdd' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write'),
            'canEdit' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write'),
            'canDelete' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'delete'),
            'canImport' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'import'),
            'canExport' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'export'),
        ]);
    }

    public function add()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk membuat data.');
        }

        // TODO: Load customers, sales deliveries, etc.
        $customers = [];
        $salesDeliveries = [];
        $paymentTerms = [];

        return view('pages.main_transaction.sales_invoice.add', [
            'customers' => $customers,
            'salesDeliveries' => $salesDeliveries,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function generateTransactionNumber()
    {
        try {
            $transactionNumber = app(TransactionNumberService::class)->generateNumber('sales_invoice');
            
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
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'invoice_number' => 'required|string|max:255',
                'customer_id' => 'required|integer',
                'invoice_date' => 'required|date',
                'status' => 'required|string',
                'total_amount' => 'required|numeric|min:0',
                // TODO: Add more validation rules
            ]);

            $sales_invoice_id = app(CreateSalesInvoiceAction::class)->execute($validated);

            return response()->json([
                'success' => true,
                'message' => 'Sales Invoice berhasil dibuat',
                'data' => ['id' => $sales_invoice_id]
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
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        // TODO: Replace with actual data loading
        $sales_invoice = (object) [
            'id' => $id,
            'invoice_number' => 'SI-2025-001',
            'customer_name' => 'PT. Customer A',
            'invoice_date' => '2025-01-15',
            'status' => 'PENDING',
            'total_amount' => 55000000,
        ];

        $audit_logs = app(SalesInvoiceAuditLogService::class)->findAllBasedOnSalesInvoiceId($id);

        return view('pages.main_transaction.sales_invoice.detail', [
            'sales_invoice' => $sales_invoice,
            'audit_logs' => $audit_logs,
            'canEdit' => PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write'),
        ]);
    }

    public function edit($id)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // TODO: Load actual data
        $sales_invoice = (object) [
            'id' => $id,
            'invoice_number' => 'SI-2025-001',
            'customer_id' => 1,
            'invoice_date' => '2025-01-15',
            'status' => 'PENDING',
            'total_amount' => 55000000,
        ];

        $customers = [];
        $paymentTerms = [];

        return view('pages.main_transaction.sales_invoice.edit', [
            'sales_invoice' => $sales_invoice,
            'customers' => $customers,
            'paymentTerms' => $paymentTerms,
        ]);
    }

    public function update(Request $request)
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'write')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|string',
                'total_amount' => 'required|numeric|min:0',
                // TODO: Add more validation rules
            ]);

            $id = $validated['id'];
            unset($validated['id']);

            app(UpdateSalesInvoiceAction::class)->execute($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Sales Invoice berhasil diperbarui'
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
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // TODO: Replace with actual data
        $mockData = [
            [
                'id' => 1,
                'invoice_date' => '2025-01-15',
                'invoice_number' => 'SI-2025-001',
                'customer' => 'PT. Customer A',
                'total_amount' => 'Rp 55,000,000',
                'status' => 'PENDING',
                'created_by' => 'Admin User',
            ]
        ];

        return response()->json(['data' => $mockData], 200);
    }

    // Import/Export methods
    public function import()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'import')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.main_transaction.sales_invoice.import');
    }

    public function export()
    {
        // Permission check
        if (!PermissionHelper::hasPermission('main_transaction_sales_invoice', 'export')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

        // TODO: Implement export columns configuration
        $exportColumns = [];

        return view('pages.main_transaction.sales_invoice.export', [
            'exportColumns' => $exportColumns
        ]);
    }

    public function downloadTemplate()
    {
        // TODO: Implement template generation
        $filename = 'sales_invoice_import_template.xlsx';
        
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
            'message' => 'Sales Invoice berhasil diimport'
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