<?php

namespace App\Http\Controllers\MainTransaction;


use App\Domains\ReturnManagement\Actions\CreateReturnManagementAction;
use App\Domains\ReturnManagement\Services\ReturnManagementAuditLogService;
use App\Domains\ReturnManagement\Services\ReturnManagementProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\PurchaseInvoice\Services\PurchaseInvoiceService;
use App\Domains\Tag\Services\TagService;
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

class ReturnManagementController extends Controller
{
    public function get_status_design($status)
    {
        switch ($status) {
            case 'DRAFT': return 'bg-label-secondary';
            case 'PENDING_APPROVAL': return 'bg-label-warning';
            case 'APPROVED': return 'bg-label-success';
            case 'PROCESSED': return 'bg-label-info';
            case 'REJECTED': return 'bg-label-danger';
            case 'CANCELLED': return 'bg-label-dark';
            default: return 'bg-label-secondary';
        }
    }

    public function get_processing_status_design($status)
    {
        switch ($status) {
            case 'NOT_PROCESSED': return 'bg-label-secondary';
            case 'PARTIAL': return 'bg-label-warning';
            case 'COMPLETED': return 'bg-label-success';
            default: return 'bg-label-secondary';
        }
    }

    public function index()
    {
        // Check read permission for return management module
        if (! PermissionHelper::hasPermission('main_transaction_return_management', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_return_management');

        $canViewReturnManagement = PermissionHelper::canRead('main_transaction_return_management');
        $canCreateReturnManagement = PermissionHelper::canWrite('main_transaction_return_management');

        return view('pages.main_transaction.return_management.index', [
            'canCreateReturnManagement' => $canCreateReturnManagement,
            'canViewReturnManagement' => $canViewReturnManagement,
        ]);
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_return_management', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(ReturnManagementService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        $return_type = $request->input('return_type');
        $source_invoice_id = $request->input('source_invoice_id');
        $canCreateReturnManagement = PermissionHelper::canWrite('main_transaction_return_management');

        $transaction_number = '';
        if ($return_type == 'PURCHASE_RETURN') {
            $invoice_data = app(PurchaseInvoiceService::class)->findOne($source_invoice_id);
            $transaction_number = $invoice_data->transaction_code;
        } elseif ($return_type == 'SALES_RETURN') {
            $invoice_data = app(SalesInvoiceService::class)->findOne($source_invoice_id);
            $transaction_number = $invoice_data->transaction_code;
        }

        if (! $canCreateReturnManagement) {
            return redirect()->route('main_transaction.return_management.index')->with('error', 'Anda tidak memiliki akses untuk membuat Return Management');
        }

        $tags = app(TagService::class)->findAll();
        $canViewProduct = PermissionHelper::canRead('master_data_product');

        return view('pages.main_transaction.return_management.add', [
            'canCreateReturnManagement' => $canCreateReturnManagement,
            'availableTags' => $tags,
            'canViewProduct' => $canViewProduct,
            'return_type' => $return_type,
            'source_invoice_id' => $source_invoice_id,
            'invoice_data' => $invoice_data,
            'transaction_number' => $transaction_number,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $canCreateReturnManagement = PermissionHelper::canWrite('main_transaction_return_management');

        if (! $canCreateReturnManagement) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Validate basic request data
            $validatedData = $request->validate([
                'return_type' => 'required|in:PURCHASE_RETURN,SALES_RETURN',
                'transaction_code' => 'required|unique:t_return_managements,transaction_code',
                'source_invoice_id' => 'required|integer',
                'source_invoice_code' => 'required|string',
                'warehouse_id' => 'required|exists:m_warehouses,id',
                'return_date' => 'required|date',
                'transaction_date' => 'required|date',
                'return_reason' => 'required|string',
                'return_reason_description' => 'nullable|string',
                'description' => 'nullable|string',
                'internal_memo' => 'nullable|string',
                'tags' => 'nullable|array',
                'attachment' => 'nullable|file|max:10240|mimes:xlsx,xls,doc,docx,pdf,png,jpg,jpeg,zip',
                'products' => 'required|array|min:1',
                'products.*.f_product_id' => 'required|integer',
                'products.*.f_source_invoice_product_id' => 'required|integer',
                'products.*.return_qty' => 'required|numeric|min:0.001',
                'products.*.return_unit_price' => 'required|numeric|min:0',
                'products.*.return_condition' => 'required|string',
                'products.*.return_reason' => 'nullable|string',
                'products.*.note' => 'nullable|string',
            ], [], [
                'return_type' => 'Tipe Return',
                'transaction_code' => 'Kode Transaksi',
                'source_invoice_id' => 'ID Invoice Sumber',
                'source_invoice_code' => 'Kode Invoice Sumber',
                'warehouse_id' => 'Gudang',
                'return_date' => 'Tanggal Return',
                'transaction_date' => 'Tanggal Transaksi',
                'return_reason' => 'Alasan Return',
                'return_reason_description' => 'Deskripsi Alasan Return',
                'description' => 'Deskripsi',
                'internal_memo' => 'Memo Internal',
                'tags' => 'Tag',
                'attachment' => 'Lampiran',
                'products' => 'Produk',
                'products.*.f_product_id' => 'ID Produk',
                'products.*.f_source_invoice_product_id' => 'ID Produk Invoice Sumber',
                'products.*.return_qty' => 'Qty Return',
                'products.*.return_unit_price' => 'Harga Satuan Return',
                'products.*.return_condition' => 'Kondisi Return',
                'products.*.return_reason' => 'Alasan Return Produk',
                'products.*.note' => 'Catatan Produk',
            ]);
            // Execute creation
            app(CreateReturnManagementAction::class)->execute($validatedData);

            if ($validatedData['return_type'] == 'PURCHASE_RETURN') {
                return response()->json([
                    'message' => SuccessCodes::CREATE_PURCHASE_RETURN_SUCCESS->message(),
                ], SuccessCodes::CREATE_PURCHASE_RETURN_SUCCESS->httpStatus());
            } else {
                return response()->json([
                    'message' => SuccessCodes::UPDATE_SALES_RETURN_SUCCESS->message(),
                ], SuccessCodes::UPDATE_SALES_RETURN_SUCCESS->httpStatus());
            }

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
        $canViewReturnManagement = PermissionHelper::canRead('main_transaction_return_management');

        if (! $canViewReturnManagement) {
            return redirect()->route('main_transaction.return_management.index')->with('error', 'Anda tidak memiliki akses untuk melihat detail Return Management');
        }

        $return = app(ReturnManagementService::class)->findById($id);

        if (! $return) {
            return redirect()->route('main_transaction.return_management.index')->with('error', 'Return tidak ditemukan');
        }

        $returnProducts = app(ReturnManagementProductService::class)->getByReturnId($id);
        $auditLogs = app(ReturnManagementAuditLogService::class)->findByReturnId($id);

        // Permission checks
        $canViewUser = PermissionHelper::canRead('master_data_user');
        $canViewProduct = PermissionHelper::canRead('master_data_product');

        return view('pages.main_transaction.return_management.detail', [
            'return_data' => $return,
            'return_products' => $returnProducts,
            'auditLogs' => $auditLogs,
            'canViewReturnManagement' => $canViewReturnManagement,
            'canViewUser' => $canViewUser,
            'canViewProduct' => $canViewProduct,
        ]);
    }

    public function auditLogDataTable($id)
    {
        $canViewReturnManagement = PermissionHelper::canRead('main_transaction_return_management');

        if (! $canViewReturnManagement) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = app(ReturnManagementAuditLogService::class)->getForDataTable($id);

        return DataTables::of($query)
            ->addColumn('created_at', function ($row) {
                return $row->created_at;
            })
            ->addColumn('user_name', function ($row) {
                return $row->user_name ?: 'System';
            })
            ->addColumn('action_type', function ($row) {
                return $row->action_type;
            })
            ->addColumn('description', function ($row) {
                return $row->description;
            })
            ->make(true);
    }

    public function generateTransactionNumber(Request $request)
    {
        $returnType = $request->input('return_type', 'PURCHASE_RETURN');

        try {
            // Map return type to transaction number module name
            $moduleName = $returnType === 'PURCHASE_RETURN' ? 'purchase_return' : 'sales_return';
            $transactionNumber = app(TransactionNumberService::class)->generateNumber($moduleName);

            return response()->json([
                'success' => true,
                'transaction_number' => $transactionNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor transaksi: '.$e->getMessage(),
            ], 500);
        }
    }

    // API endpoints for getting invoice data for returns
    public function getInvoiceProducts(Request $request)
    {
        $invoiceId = $request->input('invoice_id');
        $returnType = $request->input('return_type');

        try {
            $products = [];

            if ($returnType === 'PURCHASE_RETURN') {
                $products = DB::table('t_purchase_invoice_products as pip')
                    ->join('m_products as p', 'pip.f_product_id', '=', 'p.id')
                    ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
                    ->where('pip.f_purchase_invoice_id', $invoiceId)
                    ->select([
                        'pip.id as invoice_product_id',
                        'pip.f_product_id',
                        'p.code as product_code',
                        'p.name as product_name',
                        'pip.qty as original_qty',
                        'pip.unit_price',
                        'pip.total_price',
                        'u.code as unit_code',
                    ])
                    ->get();
            } elseif ($returnType === 'SALES_RETURN') {
                $products = DB::table('t_sales_invoice_products as sip')
                    ->join('m_products as p', 'sip.f_product_id', '=', 'p.id')
                    ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
                    ->where('sip.f_sales_invoice_id', $invoiceId)
                    ->select([
                        'sip.id as invoice_product_id',
                        'sip.f_product_id',
                        'p.code as product_code',
                        'p.name as product_name',
                        'sip.qty as original_qty',
                        'sip.unit_price',
                        'sip.line_total as total_price',
                        'u.code as unit_code',
                    ])
                    ->get();
            }

            // dd($products);

            // Calculate available quantities for return
            foreach ($products as $product) {
                $availableQty = app(ReturnManagementProductService::class)
                    ->getAvailableQtyForReturn($product->invoice_product_id);
                $product->available_qty = $availableQty;
            }

            return response()->json([
                'success' => true,
                'products' => $products,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk: '.$e->getMessage(),
            ], 500);
        }
    }
}
