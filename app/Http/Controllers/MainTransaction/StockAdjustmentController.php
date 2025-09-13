<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Category\Services\CategoryService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\StockAdjustment\Actions\CreateStockAdjustmentAction;
use App\Domains\StockAdjustment\Actions\UpdateStockAdjustmentAction;
use App\Domains\StockAdjustment\Services\StockAdjustmentAuditLogService;
use App\Domains\StockAdjustment\Services\StockAdjustmentService;
use App\Domains\StockMovement\Services\StockMovementLogsService;
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

class StockAdjustmentController extends Controller
{
    public function index()
    {
        // Check read permission for stock adjustment module
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_stock_adjustment');

        // Map permissions to view variables
        $canAddStockAdjustment = PermissionHelper::canWrite('main_transaction_stock_adjustment');
        $canImportStockAdjustment = PermissionHelper::canWrite('main_transaction_stock_adjustment');
        $canExportStockAdjustment = PermissionHelper::canRead('main_transaction_stock_adjustment');
        $canViewDetails = PermissionHelper::canRead('main_transaction_stock_adjustment');
        $canEditStockAdjustment = PermissionHelper::canWrite('main_transaction_stock_adjustment');
        $canDeleteStockAdjustment = PermissionHelper::canDelete('main_transaction_stock_adjustment');

        // Adjustment types for stock adjustment workflow
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'STOCK_OPNAME', 'label' => 'STOCK_OPNAME - Hasil Stock Opname'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa'],
        ];

        // Status options for stock adjustment workflow
        $adjustmentStatuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'APPLIED', 'label' => 'APPLIED - Diterapkan'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Warehouse locations for RIYO Water
        $warehouseLocations = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods'],
        ];

        // Product categories for RIYO Water
        $productCategories = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'CHEMICALS', 'label' => 'CHEMICALS - Bahan Kimia'],
            ['value' => 'MACHINERY', 'label' => 'MACHINERY - Peralatan'],
        ];

        $data = [
            'adjustmentTypes' => $adjustmentTypes,
            'adjustmentStatuses' => $adjustmentStatuses,
            'warehouseLocations' => $warehouseLocations,
            'productCategories' => $productCategories,
            'canAddStockAdjustment' => $canAddStockAdjustment,
            'canImportStockAdjustment' => $canImportStockAdjustment,
            'canExportStockAdjustment' => $canExportStockAdjustment,
            'canViewDetails' => $canViewDetails,
            'canEditStockAdjustment' => $canEditStockAdjustment,
            'canDeleteStockAdjustment' => $canDeleteStockAdjustment,
        ];

        return view('pages.main_transaction.stock_adjustment.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_purchase_order', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = app(StockAdjustmentService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add(Request $request)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
        // Adjustment types
        if (isset($request->id) && isset($request->type)) {
            $adjustmentTypes = [
                ['value' => 'STOCK_OPNAME', 'label' => 'STOCK_OPNAME - Hasil Stock Opname'],
                ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
                ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
                ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa'],
            ];
        } else {
            $adjustmentTypes = [
                ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ];
        }

        // Get warehouses from service
        $warehousesData = app(WarehouseService::class)->findAll();
        $warehouses = [];
        foreach ($warehousesData as $warehouse) {
            $warehouses[] = [
                'value' => $warehouse->id,
                'label' => $warehouse->code.' - '.$warehouse->name,
            ];
        }

        // Get employees from contact service
        $contactsData = app(ContactService::class)->findAllByType('EMPLOYEE');
        $picOptions = [];
        foreach ($contactsData as $contact) {
            $picOptions[] = [
                'value' => $contact->id,
                'label' => $contact->name,
            ];
        }

        // Status options for new adjustment
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'WAITING_APPROVAL', 'label' => 'WAITING_APPROVAL - Menunggu Persetujuan'],
        ];

        // Get product categories from service
        $categoriesData = app(CategoryService::class)->findAll();
        $productCategories = [];
        foreach ($categoriesData as $category) {
            $productCategories[] = [
                'value' => $category->id,
                'label' => $category->code.' - '.$category->name,
            ];
        }

        // Get tags from service
        $tagsData = app(TagService::class)->findAll();
        $availableTags = [];
        foreach ($tagsData as $tag) {
            $availableTags[] = [
                'code' => $tag->code,
                'label' => $tag->code.' - '.$tag->name,
                'color' => $tag->tag_color_code ?? 'primary',
            ];
        }

        // Permission checks for add buttons
        $canAddTags = PermissionHelper::canWrite('master_data_tag');
        $canAddWarehouses = PermissionHelper::canWrite('master_data_warehouse');
        $canAddContacts = PermissionHelper::canWrite('master_data_contact');
        $canAddCategories = PermissionHelper::canWrite('master_data_category');

        $header_data = [];
        $items = [];
        if (isset($request->id) && isset($request->type)) {
            switch ($request->type) {
                case 'STOCK_OPNAME':
                    $header = app(StockOpnameService::class)->findOne($request->id);
                    $opnameProducts = app(StockOpnameProductService::class)->findAllBasedOnId($request->id);
                    $header_data['transaction_date'] = $header->transaction_date;
                    $header_data['form_type'] = $request->type;
                    $header_data['form_reference_id'] = $header->id;
                    $header_data['warehouse_id'] = $header->warehouse_id;
                    $header_data['pic'] = $header->f_pic_id;
                    $header_data['description'] = $header->description;
                    $header_data['memo'] = $header->memo;

                    foreach ($opnameProducts as $item) {
                        $items[] = [
                            'product_id' => $item->product_id,
                            'product_code' => $item->product_code,
                            'product_name' => $item->product_name,
                            'unit' => $item->unit_name,
                            'current_stock' => $item->system_stock,
                            'adjustment_qty' => $item->physical_qty,
                            'unit_cost' => 0,
                            'reason' => $item->note,
                        ];
                    }
                    break;
            }
        }

        return view('pages.main_transaction.stock_adjustment.add', [
            'adjustmentTypes' => $adjustmentTypes,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'statusOptions' => $statusOptions,
            'productCategories' => $productCategories,
            'availableTags' => $availableTags,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses,
            'canAddContacts' => $canAddContacts,
            'canAddCategories' => $canAddCategories,
            'header_data' => $header_data,
            'items' => $items,
        ]);
    }

    public function edit($id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // Get stock adjustment data from service
        $stockAdjustment = app(StockAdjustmentService::class)->findOne($id);
        if (! $stockAdjustment) {
            abort(404, 'Stock adjustment tidak ditemukan.');
        }

        // Adjustment types
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa'],
        ];

        // Get warehouses from service
        $warehousesData = app(WarehouseService::class)->findAll();
        $warehouses = [];
        foreach ($warehousesData as $warehouse) {
            $warehouses[] = [
                'value' => $warehouse->id,
                'label' => $warehouse->code.' - '.$warehouse->name,
            ];
        }

        // Get employees from contact service
        $contactsData = app(ContactService::class)->findAllByType('EMPLOYEE');
        $picOptions = [];
        foreach ($contactsData as $contact) {
            $picOptions[] = [
                'value' => $contact->id,
                'label' => 'EMP-'.str_pad($contact->id, 3, '0', STR_PAD_LEFT).' - '.$contact->name,
            ];
        }

        // Status options for edit
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Get adjustment items from service
        $adjustmentItems = app(StockAdjustmentService::class)->getAdjustmentItems($id);

        // Get product categories from service
        $categoriesData = app(CategoryService::class)->findAll();
        $productCategories = [];
        foreach ($categoriesData as $category) {
            $productCategories[] = [
                'value' => $category->id,
                'label' => $category->code.' - '.$category->name,
            ];
        }

        // Get tags from service
        $availableTags = app(TagService::class)->findAll();
        // $availableTags = [];
        // foreach ($tagsData as $tag) {
        //     $availableTags[] = [
        //         'code' => $tag->code,
        //         'label' => $tag->code . ' - ' . $tag->name,
        //         'color' => $tag->tag_color_code ?? 'primary'
        //     ];
        // }

        // Permission checks for add buttons
        $canAddTags = PermissionHelper::canWrite('master_data_tag');
        $canAddWarehouses = PermissionHelper::canWrite('master_data_warehouse');
        $canAddContacts = PermissionHelper::canWrite('master_data_contact');
        $canAddCategories = PermissionHelper::canWrite('master_data_category');

        return view('pages.main_transaction.stock_adjustment.edit', [
            'stockAdjustment' => $stockAdjustment,
            'adjustmentTypes' => $adjustmentTypes,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'statusOptions' => $statusOptions,
            'adjustmentItems' => $adjustmentItems,
            'productCategories' => $productCategories,
            'availableTags' => $availableTags,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses,
            'canAddContacts' => $canAddContacts,
            'canAddCategories' => $canAddCategories,
        ]);
    }

    public function detail($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }

        // Permission checks for detail page actions
        $canEditStockAdjustment = PermissionHelper::canWrite('main_transaction_stock_adjustment');
        $canApproveStockAdjustment = PermissionHelper::canApprove('main_transaction_stock_adjustment');
        $canRejectStockAdjustment = PermissionHelper::canApprove('main_transaction_stock_adjustment');
        $canApplyStockAdjustment = PermissionHelper::canApprove('main_transaction_stock_adjustment');

        // Get stock adjustment data from service
        $stockAdjustment = app(StockAdjustmentService::class)->findOne($id);
        if (! $stockAdjustment) {
            abort(404, 'Stock adjustment tidak ditemukan.');
        }

        // Get adjustment items from service
        $adjustmentItems = app(StockAdjustmentService::class)->getAdjustmentItems($id);
        $auditLog = app(StockAdjustmentAuditLogService::class)->findByStockAdjustmentId($id);

        return view('pages.main_transaction.stock_adjustment.detail', [
            'stockAdjustment' => $stockAdjustment,
            'adjustmentItems' => $adjustmentItems,
            'canEditStockAdjustment' => $canEditStockAdjustment,
            'canApproveStockAdjustment' => $canApproveStockAdjustment,
            'canRejectStockAdjustment' => $canRejectStockAdjustment,
            'canApplyStockAdjustment' => $canApplyStockAdjustment,
            'auditLog' => $auditLog,
        ]);
    }

    public function export()
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }
        // Export columns configuration
        $exportColumns = [
            ['field' => 'adjustment_number', 'label' => 'Nomor Adjustment', 'export_name' => 'Nomor Adjustment'],
            ['field' => 'adjustment_date', 'label' => 'Tanggal Adjustment', 'export_name' => 'Tanggal Adjustment'],
            ['field' => 'adjustment_type', 'label' => 'Tipe Adjustment', 'export_name' => 'Tipe Adjustment'],
            ['field' => 'warehouse_name', 'label' => 'Nama Gudang', 'export_name' => 'Nama Gudang'],
            ['field' => 'reference_number', 'label' => 'Nomor Referensi', 'export_name' => 'Nomor Referensi'],
            ['field' => 'total_items', 'label' => 'Total Item', 'export_name' => 'Total Item'],
            ['field' => 'total_adjustment_qty', 'label' => 'Total Qty Adjustment', 'export_name' => 'Total Qty Adjustment'],
            ['field' => 'total_adjustment_value', 'label' => 'Total Nilai Adjustment', 'export_name' => 'Total Nilai Adjustment'],
            ['field' => 'status', 'label' => 'Status', 'export_name' => 'Status'],
            ['field' => 'approved_by', 'label' => 'Disetujui Oleh', 'export_name' => 'Disetujui Oleh'],
            ['field' => 'approved_date', 'label' => 'Tanggal Persetujuan', 'export_name' => 'Tanggal Persetujuan'],
            ['field' => 'description', 'label' => 'Deskripsi', 'export_name' => 'Deskripsi'],
        ];

        // Warehouses for filter
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods'],
        ];

        // Status options for filter
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'APPLIED', 'label' => 'APPLIED - Diterapkan'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan'],
        ];

        // Adjustment types for filter
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'STOCK_OPNAME', 'label' => 'STOCK_OPNAME - Hasil Stock Opname'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa'],
        ];

        return view('pages.main_transaction.stock_adjustment.export', [
            'exportColumns' => $exportColumns,
            'warehouses' => $warehouses,
            'statusOptions' => $statusOptions,
            'adjustmentTypes' => $adjustmentTypes,
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $format = $request->input('format');
        $columns = json_decode($request->input('columns'), true);
        $filters = json_decode($request->input('filters'), true);

        // Mock data for stock adjustments
        $stockAdjustments = collect([
            [
                'adjustment_number' => 'SA-2025-001',
                'adjustment_date' => '2025-01-15',
                'adjustment_type' => 'Hasil Stock Opname',
                'warehouse_name' => 'Gudang Utama Pusat',
                'reference_number' => 'SO-2025-001',
                'total_items' => 3,
                'total_adjustment_qty' => 2,
                'total_adjustment_value' => 66000,
                'status' => 'Diterapkan',
                'approved_by' => 'Yustinus Komunal',
                'approved_date' => '2025-01-15',
                'description' => 'Penyesuaian stok dari hasil stock opname',
            ],
            [
                'adjustment_number' => 'SA-2025-002',
                'adjustment_date' => '2025-01-16',
                'adjustment_type' => 'Penyesuaian Manual',
                'warehouse_name' => 'Gudang Produksi',
                'reference_number' => null,
                'total_items' => 1,
                'total_adjustment_qty' => -50,
                'total_adjustment_value' => -825000,
                'status' => 'Menunggu Persetujuan',
                'approved_by' => null,
                'approved_date' => null,
                'description' => 'Koreksi stok botol 330ml',
            ],
        ]);

        // Apply filters (similar to StockOpnameController)
        if (! empty($filters['date_from'])) {
            $stockAdjustments = $stockAdjustments->filter(function ($item) use ($filters) {
                return $item['adjustment_date'] >= $filters['date_from'];
            });
        }

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($stockAdjustments, $columns);
            case 'csv':
                return $this->exportToCsv($stockAdjustments, $columns);
            case 'pdf':
                return $this->exportToPdf($stockAdjustments, $columns);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function exportToExcel($data, $columns)
    {
        $filename = 'stock_adjustment_export_'.date('Y-m-d_H-i-s').'.xlsx';
        $content = 'Mock Excel content for Stock Adjustment export with '.count($data).' records';

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function exportToCsv($data, $columns)
    {
        $filename = 'stock_adjustment_export_'.date('Y-m-d_H-i-s').'.csv';
        $content = 'Mock CSV content for Stock Adjustment export';

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function exportToPdf($data, $columns)
    {
        $filename = 'stock_adjustment_export_'.date('Y-m-d_H-i-s').'.pdf';
        $content = 'Mock PDF content for Stock Adjustment export';

        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function import()
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }

        return view('pages.main_transaction.stock_adjustment.import');
    }

    public function downloadTemplate()
    {
        // Check write permission for import template
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }
        $filename = 'stock_adjustment_import_template.xlsx';
        $content = 'Mock Excel template for Stock Adjustment import';

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $mockData = [
                'total_count' => 3,
                'success_count' => 2,
                'error_count' => 1,
                'success_data' => [
                    [
                        'adjustment_number' => 'SA-2025-007',
                        'adjustment_date' => '2025-01-21',
                        'adjustment_type' => 'MANUAL_ADJUSTMENT',
                        'warehouse_name' => 'Gudang Utama Pusat',
                        'total_items' => 1,
                        'total_adjustment_qty' => 10,
                        'status' => 'DRAFT',
                        'description' => 'Import adjustment test 1',
                    ],
                    [
                        'adjustment_number' => 'SA-2025-008',
                        'adjustment_date' => '2025-01-22',
                        'adjustment_type' => 'PRODUCT_RETURN',
                        'warehouse_name' => 'Gudang Finished Goods',
                        'total_items' => 2,
                        'total_adjustment_qty' => 15,
                        'status' => 'DRAFT',
                        'description' => 'Import adjustment test 2',
                    ],
                ],
                'error_data' => [
                    [
                        'adjustment_number' => 'SA-2025-001',
                        'adjustment_date' => '2025-01-23',
                        'adjustment_type' => 'INVALID_TYPE',
                        'warehouse_name' => 'Gudang Invalid',
                        'total_items' => 0,
                        'total_adjustment_qty' => 0,
                        'status' => 'DRAFT',
                        'description' => 'Import adjustment test error',
                        'errors' => ['Nomor SA sudah ada', 'Tipe adjustment tidak valid', 'Gudang tidak ditemukan'],
                    ],
                ],
            ];

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
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        try {
            $data = $request->input('data');
            $importedCount = count($data);

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => 'Data stock adjustment berhasil diimport',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: '.$e->getMessage(),
            ]);
        }
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'adjustment_date' => 'required|date',
                'adjustment_type' => 'required|string|in:MANUAL_ADJUSTMENT,STOCK_OPNAME',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'pic_id' => 'required|integer|exists:users,id',
                // 'status' => 'required|string|in:DRAFT,WAITING_APPROVAL',
                'description' => 'required|string|max:1000',
                'memo' => 'nullable|string|max:500',
                'form_type' => 'nullable|string|max:500',
                'form_reference_id' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer',
                'products.*.product_code' => 'required|string',
                'products.*.product_name' => 'required|string',
                'products.*.unit' => 'required|string',
                'products.*.stock_before' => 'required|numeric',
                'products.*.adjustment_qty' => 'required|numeric|not_in:0',
                'products.*.unit_cost' => 'nullable|numeric|min:0',
                'products.*.reason' => 'nullable|string|max:200',
            ], [], [
                'adjustment_date' => 'Tanggal Adjustment',
                'adjustment_type' => 'Tipe Adjustment',
                'warehouse_id' => 'Gudang',
                'pic_id' => 'PIC',
                'description' => 'Deskripsi',
                'memo' => 'Memo',
                'form_type' => 'Tipe Form',
                'form_reference_id' => 'ID Referensi Form',
                'tags' => 'Tag',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.product_code' => 'Kode Produk',
                'products.*.product_name' => 'Nama Produk',
                'products.*.unit' => 'Satuan',
                'products.*.stock_before' => 'Stok Sebelum',
                'products.*.adjustment_qty' => 'Qty Adjustment',
                'products.*.unit_cost' => 'Harga Satuan',
                'products.*.reason' => 'Alasan Adjustment',
            ]);

            app(CreateStockAdjustmentAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_STOCK_ADJUSTMENT_SUCCESS->message(),
            ], SuccessCodes::CREATE_STOCK_ADJUSTMENT_SUCCESS->httpStatus());
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

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'adjustment_id' => 'required|integer|exists:t_stock_adjustments,id',
                'adjustment_date' => 'required|date',
                'adjustment_type' => 'required|string|in:MANUAL_ADJUSTMENT,PRODUCT_RETURN,DAMAGE_LOSS,EXPIRY_DISPOSAL,WAREHOUSE_TRANSFER',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'pic_id' => 'required|integer|exists:users,id',
                'status' => 'required|string|in:DRAFT,WAITING_APPROVAL,APPROVED,CANCELLED',
                'description' => 'required|string|max:1000',
                'memo' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|integer',
                'products.*.product_code' => 'required|string',
                'products.*.product_name' => 'required|string',
                'products.*.unit' => 'required|string',
                'products.*.stock_before' => 'required|numeric',
                'products.*.adjustment_qty' => 'required|numeric|not_in:0',
                'products.*.unit_cost' => 'nullable|numeric|min:0',
                'products.*.reason' => 'nullable|string|max:200',
            ], [], [
                'adjustment_id' => 'ID Adjustment',
                'adjustment_date' => 'Tanggal Adjustment',
                'adjustment_type' => 'Tipe Adjustment',
                'warehouse_id' => 'Gudang',
                'pic_id' => 'PIC',
                'status' => 'Status',
                'description' => 'Deskripsi',
                'memo' => 'Memo',
                'tags' => 'Tag',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.product_code' => 'Kode Produk',
                'products.*.product_name' => 'Nama Produk',
                'products.*.unit' => 'Satuan',
                'products.*.stock_before' => 'Stok Sebelum',
                'products.*.adjustment_qty' => 'Qty Adjustment',
                'products.*.unit_cost' => 'Harga Satuan',
                'products.*.reason' => 'Alasan Adjustment',
            ]);

            app(UpdateStockAdjustmentAction::class)->execute($validated['adjustment_id'], $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_STOCK_ADJUSTMENT_SUCCESS->message(),
            ], SuccessCodes::UPDATE_STOCK_ADJUSTMENT_SUCCESS->httpStatus());
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

    public function getWarehouseProducts(Request $request)
    {
        try {
            // Check read permission
            if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'read')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $warehouse = $request->query('warehouse_id');
            $category = $request->query('category_filter');
            $search_filter = $request->query('search_filter');

            $products = app(StockMovementLogsService::class)->findAllLatestStockPerWarehouse($warehouse, $category, $search_filter);

            $product_array = [];
            foreach ($products as $key => $value) {
                $product_array[] = [
                    'id' => $value->id,
                    'code' => $value->code,
                    'name' => $value->name,
                    'unit' => $value->unit,
                    'current_stock' => $value->system_stock,
                    'unit_cost' => 0,
                    'category' => $value->category,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $product_array,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'error' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Error: '.$e->getMessage(),
                'error' => 'INTERNAL_SERVER_ERROR',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function generateTransactionNumber()
    {
        try {
            // Check read permission
            if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'read')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Generate transaction number based on current date and sequence
            $currentDate = date('Y-m-d');
            $year = date('Y');
            $month = date('m');

            // Get the latest sequence number for this month
            $latestRecord = app(StockAdjustmentService::class)->getLatestByMonth($year, $month);
            $sequence = $latestRecord ? ($latestRecord->id + 1) : 1;

            // Format: SA-YYYY-MMDD-XXX (SA = Stock Adjustment)
            $transactionNumber = sprintf('SA-%s-%s%s-%03d', $year, $month, date('d'), $sequence);

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
}
