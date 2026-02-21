<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\Category\Services\CategoryService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\StockMovement\Services\StockMovementLogsService;
use App\Domains\StockOpname\Actions\CreateStockOpnameAction;
use App\Domains\StockOpname\Services\StockOpnameAuditLogService;
use App\Domains\StockOpname\Services\StockOpnameProductService;
use App\Domains\StockOpname\Services\StockOpnameService;
use App\Domains\Tag\Services\TagService;
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

class StockOpnameController extends Controller
{
    public function index()
    {
        // Status options
        $statusOptions = [
            ['value' => 'SUBMITTED', 'label' => 'SUBMITTED - Tersimpan'],
            ['value' => 'EXECUTED', 'label' => 'EXECUTED - Stock Sudah Diadjust'],
            // ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Warehouse locations for RIYO Water
        $warehouses = app(WarehouseService::class)->findAll();

        $permissionData = PermissionHelper::getViewPermissions('master_data_stock_opname');
        return view('pages.master_data.stock_opname.index', array_merge([
            'stockOpnameStatuses' => $statusOptions,
            'warehouses' => $warehouses,
        ], $permissionData));
    }

    public function index_data_table()
    {
        $data = app(StockOpnameService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function get_product_list(Request $req)
    {
        $warehouse = $req->query('warehouse');
        $category = $req->query('category');

        $products = app(StockMovementLogsService::class)->findAllLatestStockPerWarehouse($warehouse, $category);

        $product_array = [];
        foreach ($products as $key => $value) {
            $product_array[] = [
                'id' => $value->id,
                'code' => $value->code,
                'name' => $value->name,
                'unit' => $value->unit,
                'system_stock' => $value->system_stock,
                'category' => $value->category,
            ];
        }

        return response()->json([
            'message' => SuccessCodes::GENERAL_SUCCESS->message(),
            'data' => $product_array,
        ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        // Warehouses for RIYO Water
        $warehouses = app(WarehouseService::class)->findAll();

        // PIC options
        $picUsers = app(ContactService::class)->findAllByFlag('employee');

        // Product categories for filtering
        $categories = app(CategoryService::class)->findAll();

        // Status options
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'IN_PROGRESS', 'label' => 'IN_PROGRESS - Sedang Dikerjakan'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
        ];

        $tags = app(TagService::class)->findAll();

        // Permission check for adding new tags
        $canAddTags = true; // Mock permission check

        return view('pages.master_data.stock_opname.add', [
            'warehouses' => $warehouses,
            'picOptions' => $picUsers,
            'productCategories' => $categories,
            'statusOptions' => $statusOptions,
            'tags' => $tags,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'opname_number' => 'required|string|max:50|unique:t_stock_opnames,stock_opname_no',
                'opname_date' => 'required|date',
                'warehouse' => 'required|integer',
                'pic' => 'required|integer',
                'description' => 'nullable|string|max:500',
                'memo' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'tags.*' => 'string|max:50',
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer|exists:m_products,id',
                'products.*.system_stock' => 'required|integer|min:0',
                'products.*.physical_qty' => 'required|integer|min:0',
                'products.*.variance' => 'required|integer',
                'products.*.note' => 'nullable|string|max:255',
            ], [], [
                'opname_number' => 'Nomor Stock Opname',
                'opname_date' => 'Tanggal Stock Opname',
                'warehouse' => 'Gudang',
                'pic' => 'Penanggung Jawab',
                'description' => 'Deskripsi',
                'memo' => 'Memo',
                'tags' => 'Tag',
                'tags.*' => 'Tag',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.system_stock' => 'Stok Sistem',
                'products.*.physical_qty' => 'Qty Fisik',
                'products.*.variance' => 'Selisih',
                'products.*.note' => 'Catatan Produk',
            ]);

            app(CreateStockOpnameAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_STOCK_OPNAME_SUCCESS->message(),
            ], SuccessCodes::CREATE_STOCK_OPNAME_SUCCESS->httpStatus());
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

    public function edit($id)
    {
        // Mock data for edit form
        $stockOpname = [
            'id' => $id,
            'opname_number' => 'SO-2025-001',
            'opname_date' => '2025-01-15',
            'warehouse_code' => 'GDG-001',
            'pic_name' => 'yustinus',
            'description' => 'Stock opname rutin bulanan untuk gudang utama pusat',
            'status' => 'ADJUSTMENT_PENDING',
        ];

        // Warehouses for RIYO Water
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods'],
        ];

        // PIC options
        $picOptions = [
            ['value' => 'USR-001', 'label' => 'USR-001 - Yustinus Komunal'],
            ['value' => 'USR-002', 'label' => 'USR-002 - Ririn Safitri'],
            ['value' => 'USR-003', 'label' => 'USR-003 - Hartono Wijaya'],
            ['value' => 'USR-004', 'label' => 'USR-004 - Sari Indah'],
            ['value' => 'USR-005', 'label' => 'USR-005 - Budi Santoso'],
            ['value' => 'USR-006', 'label' => 'USR-006 - Ani Suhartini'],
        ];

        // Product categories for filtering
        $productCategories = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'CHEMICALS', 'label' => 'CHEMICALS - Bahan Kimia'],
            ['value' => 'MACHINERY', 'label' => 'MACHINERY - Peralatan'],
        ];

        // Status options
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'IN_PROGRESS', 'label' => 'IN_PROGRESS - Sedang Dikerjakan'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
            ['value' => 'ADJUSTMENT_PENDING', 'label' => 'ADJUSTMENT_PENDING - Menunggu Adjustment'],
            ['value' => 'ADJUSTMENT_CREATED', 'label' => 'ADJUSTMENT_CREATED - Adjustment Dibuat'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Mock existing products for this stock opname
        $stockOpnameProducts = [
            [
                'id' => 24,
                'code' => 'AIR-330',
                'name' => 'RIYO Water 330ml',
                'unit' => 'pcs',
                'system_stock' => 1500,
                'physical_qty' => 1502,
                'variance' => 2,
                'notes' => 'Ada penambahan dari produksi',
                'category' => 'finished_goods',
            ],
            [
                'id' => 25,
                'code' => 'AIR-600',
                'name' => 'RIYO Water 600ml',
                'unit' => 'pcs',
                'system_stock' => 800,
                'physical_qty' => 800,
                'variance' => 0,
                'notes' => 'Sesuai dengan sistem',
                'category' => 'finished_goods',
            ],
        ];

        $tags = [
            ['value' => 'ROUTINE', 'label' => 'ROUTINE - Opname Rutin', 'color' => 'primary'],
            ['value' => 'AUDIT', 'label' => 'AUDIT - Audit Stok', 'color' => 'danger'],
            ['value' => 'MONTHLY', 'label' => 'MONTHLY - Bulanan', 'color' => 'info'],
            ['value' => 'ANNUAL', 'label' => 'ANNUAL - Tahunan', 'color' => 'success'],
            ['value' => 'INVESTIGATION', 'label' => 'INVESTIGATION - Investigasi', 'color' => 'warning'],
        ];

        // Permission check for adding new tags
        $canAddTags = true; // Mock permission check

        return view('pages.master_data.stock_opname.edit', [
            'stockOpname' => $stockOpname,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'productCategories' => $productCategories,
            'statusOptions' => $statusOptions,
            'stockOpnameProducts' => $stockOpnameProducts,
            'tags' => $tags,
            'canAddTags' => $canAddTags,
        ]);
    }

    public function detail($id)
    {
        $header_data = app(StockOpnameService::class)->findOne($id);
        // Mock data for detail view
        $stockOpname = [
            'id' => $id,
            'opname_number' => $header_data->stock_opname_no,
            'opname_date' => $header_data->transaction_date,
            'warehouse_name' => $header_data->warehouse_name,
            'warehouse_code' => $header_data->warehouse_code,
            'pic_name' => $header_data->pic_name,
            'description' => $header_data->description,
            'memo' => $header_data->memo,
            // 'variant_products' => $header_data->count_variance,
            'total_products' => $header_data->count_product,
            'counted_products' => $header_data->count_variance,
            'system_qty' => $header_data->total_system_stock,
            'physical_qty' => $header_data->total_physical_stock,
            'variance_qty' => $header_data->total_variance,
            'variance_value' => 0,
            'status' => $header_data->status,
            'adjustment_number' => null,
            'created_at' => $header_data->created_at,
            'updated_at' => $header_data->updated_at,
        ];

        $product_data = app(StockOpnameProductService::class)->findAllBasedOnId($id);

        foreach ($product_data as $row) {
            $productList[] = [
                'code' => $row->product_code,
                'name' => $row->product_name,
                'unit' => $row->unit_name,
                'system_stock' => $row->system_stock,
                'physical_qty' => $row->physical_qty,
                'variance' => $row->variance,
                'notes' => $row->note,
            ];
        }

        $audit_logs = app(StockOpnameAuditLogService::class)->findByStockOpnameId($id);

        $auditLogs = [];
        foreach ($audit_logs as $log) {
            $auditLogs[] = [
                'created_at' => $log->created_at,
                'user_id' => $log->created_by,
                'user_name' => $log->user_name,
                'action_type' => $log->action_type,
                'description' => $log->description,
            ];
        }

        return view('pages.master_data.stock_opname.detail', [
            'stockOpname' => $stockOpname,
            'productList' => $productList,
            'auditLogs' => $auditLogs,
        ]);
    }

    public function export()
    {
        // Export columns configuration
        $exportColumns = [
            ['field' => 'opname_number', 'label' => 'Nomor Stock Opname', 'export_name' => 'Nomor Stock Opname'],
            ['field' => 'opname_date', 'label' => 'Tanggal Opname', 'export_name' => 'Tanggal Opname'],
            ['field' => 'warehouse_name', 'label' => 'Nama Gudang', 'export_name' => 'Nama Gudang'],
            ['field' => 'pic_name', 'label' => 'Penanggung Jawab', 'export_name' => 'Penanggung Jawab'],
            ['field' => 'variant_products', 'label' => 'Varian Produk', 'export_name' => 'Varian Produk'],
            ['field' => 'system_qty', 'label' => 'Sistem QTY', 'export_name' => 'Sistem QTY'],
            ['field' => 'physical_qty', 'label' => 'Fisik QTY', 'export_name' => 'Fisik QTY'],
            ['field' => 'variance_qty', 'label' => 'Selisih Qty', 'export_name' => 'Selisih Qty'],
            ['field' => 'variance_value', 'label' => 'Selisih Nilai', 'export_name' => 'Selisih Nilai'],
            ['field' => 'status', 'label' => 'Status', 'export_name' => 'Status'],
            ['field' => 'adjustment_number', 'label' => 'Nomor Adjustment', 'export_name' => 'Nomor Adjustment'],
            ['field' => 'description', 'label' => 'Deskripsi', 'export_name' => 'Deskripsi'],
        ];

        // Warehouses for filter
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods'],
        ];

        // Status options for filter (restricted to 4 valid statuses)
        $statusOptions = [
            ['value' => 'ADJUSTMENT_PENDING', 'label' => 'ADJUSTMENT_PENDING - Menunggu Adjustment'],
            ['value' => 'ADJUSTMENT_CREATED', 'label' => 'ADJUSTMENT_CREATED - Adjustment Dibuat'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dihapus'],
        ];

        return view('pages.master_data.stock_opname.export', [
            'exportColumns' => $exportColumns,
            'warehouses' => $warehouses,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function processExport(Request $request)
    {
        $format = $request->input('format');
        $columns = json_decode($request->input('columns'), true);
        $filters = json_decode($request->input('filters'), true);

        // Mock data for stock opnames
        $stockOpnames = collect([
            [
                'opname_number' => 'SO-2025-001',
                'opname_date' => '2025-01-15',
                'warehouse_name' => 'Gudang Utama Pusat',
                'pic_name' => 'Yustinus Komunal',
                'variant_products' => 3,
                'system_qty' => 2700,
                'physical_qty' => 2702,
                'variance_qty' => 2,
                'variance_value' => 125000,
                'status' => 'Menunggu Adjustment',
                'adjustment_number' => null,
                'description' => 'Stock opname rutin bulanan',
            ],
            [
                'opname_number' => 'SO-2025-002',
                'opname_date' => '2025-01-16',
                'warehouse_name' => 'Gudang Produksi',
                'pic_name' => 'Ririn Safitri',
                'variant_products' => 2,
                'system_qty' => 1200,
                'physical_qty' => 1200,
                'variance_qty' => 0,
                'variance_value' => 0,
                'status' => 'Sedang Dikerjakan',
                'adjustment_number' => null,
                'description' => 'Stock opname produksi mingguan',
            ],
            [
                'opname_number' => 'SO-2025-003',
                'opname_date' => '2025-01-17',
                'warehouse_name' => 'Gudang Bahan Baku',
                'pic_name' => 'Hartono Wijaya',
                'variant_products' => 4,
                'system_qty' => 850,
                'physical_qty' => 0,
                'variance_qty' => 0,
                'variance_value' => 0,
                'status' => 'Draft',
                'adjustment_number' => null,
                'description' => 'Stock opname bahan baku',
            ],
        ]);

        // Apply filters
        if (! empty($filters['date_from'])) {
            $stockOpnames = $stockOpnames->filter(function ($item) use ($filters) {
                return $item['opname_date'] >= $filters['date_from'];
            });
        }

        if (! empty($filters['date_to'])) {
            $stockOpnames = $stockOpnames->filter(function ($item) use ($filters) {
                return $item['opname_date'] <= $filters['date_to'];
            });
        }

        if (! empty($filters['warehouse'])) {
            $stockOpnames = $stockOpnames->filter(function ($item) use ($filters) {
                return strpos($item['warehouse_name'], $filters['warehouse']) !== false;
            });
        }

        if (! empty($filters['status'])) {
            $statusMap = [
                'draft' => 'Draft',
                'in_progress' => 'Sedang Dikerjakan',
                'completed' => 'Selesai',
                'adjustment_pending' => 'Menunggu Adjustment',
                'adjustment_created' => 'Adjustment Dibuat',
                'cancelled' => 'Dibatalkan',
            ];
            $statusLabel = $statusMap[$filters['status']] ?? $filters['status'];
            $stockOpnames = $stockOpnames->filter(function ($item) use ($statusLabel) {
                return $item['status'] === $statusLabel;
            });
        }

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($stockOpnames, $columns);
            case 'csv':
                return $this->exportToCsv($stockOpnames, $columns);
            case 'pdf':
                return $this->exportToPdf($stockOpnames, $columns);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function exportToExcel($data, $columns)
    {
        // Mock Excel export - in real implementation, use PhpSpreadsheet
        $filename = 'stock_opname_export_'.date('Y-m-d_H-i-s').'.xlsx';
        $content = $this->generateMockExcelContent($data, $columns);

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function exportToCsv($data, $columns)
    {
        $filename = 'stock_opname_export_'.date('Y-m-d_H-i-s').'.csv';
        $content = $this->generateCsvContent($data, $columns);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function exportToPdf($data, $columns)
    {
        // Mock PDF export - in real implementation, use TCPDF or similar
        $filename = 'stock_opname_export_'.date('Y-m-d_H-i-s').'.pdf';
        $content = $this->generateMockPdfContent($data, $columns);

        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function generateMockExcelContent($data, $columns)
    {
        // Mock Excel content - in real implementation, use PhpSpreadsheet
        return 'Mock Excel content for Stock Opname export with '.count($data).' records and '.count($columns).' columns';
    }

    private function generateCsvContent($data, $columns)
    {
        $csv = [];

        // Add headers
        $headers = array_map(function ($col) {
            return $col['label'];
        }, $columns);
        $csv[] = implode(',', $headers);

        // Add data rows
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($columns as $column) {
                $value = $row[$column['field']] ?? '';
                // Escape CSV values
                if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
                    $value = '"'.str_replace('"', '""', $value).'"';
                }
                $csvRow[] = $value;
            }
            $csv[] = implode(',', $csvRow);
        }

        return implode("\n", $csv);
    }

    private function generateMockPdfContent($data, $columns)
    {
        // Mock PDF content - in real implementation, use TCPDF
        return 'Mock PDF content for Stock Opname export with '.count($data).' records';
    }

    public function import()
    {
        return view('pages.master_data.stock_opname.import');
    }

    public function downloadTemplate()
    {
        // Mock template download - in real implementation, generate actual Excel template
        $filename = 'stock_opname_import_template.xlsx';
        $content = $this->generateImportTemplate();

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function validateImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // Mock validation - in real implementation, read and validate file
            $mockData = $this->generateMockImportValidation();

            return response()->json([
                'success' => true,
                'total_count' => $mockData['total_count'],
                'success_count' => $mockData['success_count'],
                'error_count' => $mockData['error_count'],
                'success_data' => $mockData['success_data'],
                'error_data' => $mockData['error_data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading file: '.$e->getMessage(),
            ]);
        }
    }

    public function processImport(Request $request)
    {
        try {
            $data = $request->input('data');
            $importedCount = count($data);

            // Mock import process - in real implementation, save to database
            foreach ($data as $item) {
                // Create stock opname record
                // StockOpname::create([...]);
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => 'Data stock opname berhasil diimport',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: '.$e->getMessage(),
            ]);
        }
    }

    private function generateImportTemplate()
    {
        // Mock template generation - in real implementation, create actual Excel template
        return 'Mock Excel template content with headers: Nomor SO, Tanggal, Gudang, PIC, Status, Deskripsi';
    }

    private function generateMockImportValidation()
    {
        return [
            'total_count' => 5,
            'success_count' => 4,
            'error_count' => 1,
            'success_data' => [
                [
                    'opname_number' => 'SO-2025-004',
                    'opname_date' => '2025-01-18',
                    'warehouse_name' => 'Gudang Utama Pusat',
                    'pic_name' => 'Yustinus Komunal',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 1',
                ],
                [
                    'opname_number' => 'SO-2025-005',
                    'opname_date' => '2025-01-19',
                    'warehouse_name' => 'Gudang Produksi',
                    'pic_name' => 'Ririn Safitri',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 2',
                ],
                [
                    'opname_number' => 'SO-2025-006',
                    'opname_date' => '2025-01-20',
                    'warehouse_name' => 'Gudang Bahan Baku',
                    'pic_name' => 'Hartono Wijaya',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 3',
                ],
                [
                    'opname_number' => 'SO-2025-007',
                    'opname_date' => '2025-01-21',
                    'warehouse_name' => 'Gudang Finished Goods',
                    'pic_name' => 'Sari Indah',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 4',
                ],
            ],
            'error_data' => [
                [
                    'opname_number' => 'SO-2025-001',
                    'opname_date' => '2025-01-22',
                    'warehouse_name' => 'Gudang Invalid',
                    'pic_name' => '',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test error',
                    'errors' => ['Nomor SO sudah ada', 'Gudang tidak valid', 'PIC wajib diisi'],
                ],
            ],
        ];
    }

    public function createAdjustment($id)
    {
        // This would create a stock adjustment based on the opname results
        // For now, we'll just return success response
        return response()->json([
            'success' => true,
            'message' => 'Stock Adjustment berhasil dibuat',
            'adjustment_number' => 'SA-2025-'.str_pad($id, 3, '0', STR_PAD_LEFT),
        ]);
    }
}
