<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        // Adjustment types for stock adjustment workflow
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'STOCK_OPNAME', 'label' => 'STOCK_OPNAME - Hasil Stock Opname'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa']
        ];

        // Status options for stock adjustment workflow
        $adjustmentStatuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'APPLIED', 'label' => 'APPLIED - Diterapkan'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        // Warehouse locations for RIYO Water
        $warehouseLocations = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods']
        ];

        // Product categories for RIYO Water
        $productCategories = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'CHEMICALS', 'label' => 'CHEMICALS - Bahan Kimia'],
            ['value' => 'MACHINERY', 'label' => 'MACHINERY - Peralatan']
        ];

        return view('pages.main_transaction.stock_adjustment.index', [
            'adjustmentTypes' => $adjustmentTypes,
            'adjustmentStatuses' => $adjustmentStatuses,
            'warehouseLocations' => $warehouseLocations,
            'productCategories' => $productCategories
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "adjustment_number" => "SA-2025-001",
                    "adjustment_date" => "2025-01-15",
                    "created_date" => "2025-01-15",
                    "adjustment_type" => "STOCK_OPNAME",
                    "warehouse_name" => "Gudang Utama Pusat",
                    "warehouse_id" => "GDG-001",
                    "reference_number" => "SO-2025-001",
                    "total_items" => 3,
                    "total_adjustment_qty" => 2,
                    "total_adjustment_value" => 66000,
                    "status" => "APPLIED",
                    "pic_name" => "Yustinus Komunal",
                    "approved_by" => "Yustinus Komunal",
                    "approved_date" => "2025-01-15",
                    "description" => "Penyesuaian stok dari hasil stock opname bulanan - RIYO Water semua varian"
                ],
                [
                    "id" => 2,
                    "adjustment_number" => "SA-2025-002",
                    "adjustment_date" => "2025-01-16",
                    "created_date" => "2025-01-16",
                    "adjustment_type" => "MANUAL_ADJUSTMENT",
                    "warehouse_name" => "Gudang Produksi",
                    "warehouse_id" => "GDG-002",
                    "reference_number" => null,
                    "total_items" => 1,
                    "total_adjustment_qty" => -50,
                    "total_adjustment_value" => -825000,
                    "status" => "PENDING_APPROVAL",
                    "pic_name" => "Ririn Safitri",
                    "approved_by" => null,
                    "approved_date" => null,
                    "description" => "Koreksi stok botol 330ml akibat kerusakan mesin"
                ],
                [
                    "id" => 3,
                    "adjustment_number" => "SA-2025-003",
                    "adjustment_date" => "2025-01-17",
                    "created_date" => "2025-01-17",
                    "adjustment_type" => "PRODUCT_RETURN",
                    "warehouse_name" => "Gudang Finished Goods",
                    "warehouse_id" => "GDG-004",
                    "reference_number" => "RTN-2025-001",
                    "total_items" => 2,
                    "total_adjustment_qty" => 25,
                    "total_adjustment_value" => 412500,
                    "status" => "APPROVED",
                    "pic_name" => "Hartono Wijaya",
                    "approved_by" => "Ririn Safitri",
                    "approved_date" => "2025-01-17",
                    "description" => "Retur produk dari customer - kualitas tidak sesuai"
                ],
                [
                    "id" => 4,
                    "adjustment_number" => "SA-2025-004",
                    "adjustment_date" => "2025-01-18",
                    "created_date" => "2025-01-18",
                    "adjustment_type" => "DAMAGE_LOSS",
                    "warehouse_name" => "Gudang Bahan Baku",
                    "warehouse_id" => "GDG-003",
                    "reference_number" => null,
                    "total_items" => 1,
                    "total_adjustment_qty" => -100,
                    "total_adjustment_value" => -275000,
                    "status" => "DRAFT",
                    "pic_name" => "Sari Indah",
                    "approved_by" => null,
                    "approved_date" => null,
                    "description" => "Kehilangan botol PET akibat kecelakaan forklift"
                ],
                [
                    "id" => 5,
                    "adjustment_number" => "SA-2025-005",
                    "adjustment_date" => "2025-01-19",
                    "created_date" => "2025-01-19",
                    "adjustment_type" => "EXPIRY_DISPOSAL",
                    "warehouse_name" => "Gudang Utama Pusat",
                    "warehouse_id" => "GDG-001",
                    "reference_number" => null,
                    "total_items" => 2,
                    "total_adjustment_qty" => -75,
                    "total_adjustment_value" => -385000,
                    "status" => "REJECTED",
                    "pic_name" => "Budi Santoso",
                    "approved_by" => "Hartono Wijaya",
                    "approved_date" => "2025-01-19",
                    "description" => "Pembuangan label kadaluarsa - perlu verifikasi ulang"
                ],
                [
                    "id" => 6,
                    "adjustment_number" => "SA-2025-006",
                    "adjustment_date" => "2025-01-20",
                    "created_date" => "2025-01-20",
                    "adjustment_type" => "STOCK_OPNAME",
                    "warehouse_name" => "Gudang Produksi",
                    "warehouse_id" => "GDG-002",
                    "reference_number" => "SO-2025-006",
                    "total_items" => 6,
                    "total_adjustment_qty" => 25,
                    "total_adjustment_value" => 75000,
                    "status" => "APPLIED",
                    "pic_name" => "Ani Suhartini",
                    "approved_by" => "Ani Suhartini",
                    "approved_date" => "2025-01-20",
                    "description" => "Penyesuaian stok spare parts mesin produksi"
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        // Adjustment types
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa']
        ];

        // Warehouses for RIYO Water
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods']
        ];

        // PIC options
        $picOptions = [
            ['value' => 'USR-001', 'label' => 'USR-001 - Yustinus Komunal'],
            ['value' => 'USR-002', 'label' => 'USR-002 - Ririn Safitri'],
            ['value' => 'USR-003', 'label' => 'USR-003 - Hartono Wijaya'],
            ['value' => 'USR-004', 'label' => 'USR-004 - Sari Indah'],
            ['value' => 'USR-005', 'label' => 'USR-005 - Budi Santoso'],
            ['value' => 'USR-006', 'label' => 'USR-006 - Ani Suhartini']
        ];

        // Status options for new adjustment
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan']
        ];

        return view('pages.main_transaction.stock_adjustment.add', [
            'adjustmentTypes' => $adjustmentTypes,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'statusOptions' => $statusOptions
        ]);
    }

    public function edit($id)
    {
        // Mock data for edit form
        $stockAdjustment = [
            'id' => $id,
            'adjustment_number' => 'SA-2025-002',
            'adjustment_date' => '2025-01-16',
            'adjustment_type' => 'MANUAL_ADJUSTMENT',
            'warehouse_id' => 'GDG-002',
            'pic_id' => 'USR-002',
            'reference_number' => '',
            'description' => 'Koreksi stok botol 330ml akibat kerusakan mesin',
            'status' => 'PENDING_APPROVAL'
        ];

        // Adjustment types
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa']
        ];

        // Warehouses for RIYO Water
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods']
        ];

        // PIC options
        $picOptions = [
            ['value' => 'USR-001', 'label' => 'USR-001 - Yustinus Komunal'],
            ['value' => 'USR-002', 'label' => 'USR-002 - Ririn Safitri'],
            ['value' => 'USR-003', 'label' => 'USR-003 - Hartono Wijaya'],
            ['value' => 'USR-004', 'label' => 'USR-004 - Sari Indah'],
            ['value' => 'USR-005', 'label' => 'USR-005 - Budi Santoso'],
            ['value' => 'USR-006', 'label' => 'USR-006 - Ani Suhartini']
        ];

        // Status options for edit
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        // Mock adjustment items for this adjustment
        $adjustmentItems = [
            [
                'id' => 1,
                'product_code' => 'BTL-330',
                'product_name' => 'Botol PET 330ml',
                'unit' => 'pcs',
                'current_stock' => 5000,
                'adjustment_qty' => -50,
                'unit_cost' => 1650,
                'total_value' => -82500,
                'reason' => 'Kerusakan akibat mesin press',
                'category' => 'PACKAGING'
            ]
        ];

        return view('pages.main_transaction.stock_adjustment.edit', [
            'stockAdjustment' => $stockAdjustment,
            'adjustmentTypes' => $adjustmentTypes,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'statusOptions' => $statusOptions,
            'adjustmentItems' => $adjustmentItems
        ]);
    }

    public function detail($id)
    {
        // Mock data for detail view
        $stockAdjustment = [
            'id' => $id,
            'adjustment_number' => 'SA-2025-001',
            'adjustment_date' => '2025-01-15',
            'adjustment_type' => 'STOCK_OPNAME',
            'warehouse_name' => 'Gudang Utama Pusat',
            'warehouse_id' => 'GDG-001',
            'pic_name' => 'Yustinus Komunal',
            'reference_number' => 'SO-2025-001',
            'description' => 'Penyesuaian stok dari hasil stock opname bulanan - RIYO Water semua varian',
            'total_items' => 3,
            'total_adjustment_qty' => 2,
            'total_adjustment_value' => 66000,
            'status' => 'APPLIED',
            'approved_by' => 'Yustinus Komunal',
            'approved_date' => '2025-01-15',
            'created_at' => '2025-01-15 09:00:00',
            'updated_at' => '2025-01-15 14:30:00'
        ];

        // Mock adjustment items for this adjustment
        $adjustmentItems = [
            [
                'product_code' => 'AIR-330',
                'product_name' => 'RIYO Water 330ml',
                'unit' => 'pcs',
                'current_stock' => 1500,
                'adjustment_qty' => 2,
                'unit_cost' => 12000,
                'total_value' => 24000,
                'reason' => 'Kelebihan dari stock opname',
                'batch_number' => 'B2025011501'
            ],
            [
                'product_code' => 'AIR-600',
                'product_name' => 'RIYO Water 600ml',
                'unit' => 'pcs',
                'current_stock' => 800,
                'adjustment_qty' => 0,
                'unit_cost' => 18000,
                'total_value' => 0,
                'reason' => 'Sesuai dengan stock opname',
                'batch_number' => 'B2025011502'
            ],
            [
                'product_code' => 'AIR-1500',
                'product_name' => 'RIYO Water 1500ml',
                'unit' => 'pcs',
                'current_stock' => 400,
                'adjustment_qty' => 0,
                'unit_cost' => 21000,
                'total_value' => 0,
                'reason' => 'Sesuai dengan stock opname',
                'batch_number' => 'B2025011503'
            ]
        ];

        return view('pages.main_transaction.stock_adjustment.detail', [
            'stockAdjustment' => $stockAdjustment,
            'adjustmentItems' => $adjustmentItems
        ]);
    }

    public function export()
    {
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
            ['field' => 'description', 'label' => 'Deskripsi', 'export_name' => 'Deskripsi']
        ];

        // Warehouses for filter
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'GDG-003 - Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'GDG-004 - Gudang Finished Goods']
        ];

        // Status options for filter
        $statusOptions = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'APPLIED', 'label' => 'APPLIED - Diterapkan'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        // Adjustment types for filter
        $adjustmentTypes = [
            ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ['value' => 'STOCK_OPNAME', 'label' => 'STOCK_OPNAME - Hasil Stock Opname'],
            ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
            ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
            ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa']
        ];

        return view('pages.main_transaction.stock_adjustment.export', [
            'exportColumns' => $exportColumns,
            'warehouses' => $warehouses,
            'statusOptions' => $statusOptions,
            'adjustmentTypes' => $adjustmentTypes
        ]);
    }

    public function processExport(Request $request)
    {
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
                'description' => 'Penyesuaian stok dari hasil stock opname'
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
                'description' => 'Koreksi stok botol 330ml'
            ]
        ]);
        
        // Apply filters (similar to StockOpnameController)
        if (!empty($filters['date_from'])) {
            $stockAdjustments = $stockAdjustments->filter(function($item) use ($filters) {
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
        $filename = 'stock_adjustment_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $content = "Mock Excel content for Stock Adjustment export with " . count($data) . " records";
        
        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportToCsv($data, $columns)
    {
        $filename = 'stock_adjustment_export_' . date('Y-m-d_H-i-s') . '.csv';
        $content = "Mock CSV content for Stock Adjustment export";
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportToPdf($data, $columns)
    {
        $filename = 'stock_adjustment_export_' . date('Y-m-d_H-i-s') . '.pdf';
        $content = "Mock PDF content for Stock Adjustment export";
        
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function import()
    {
        return view('pages.main_transaction.stock_adjustment.import');
    }

    public function downloadTemplate()
    {
        $filename = 'stock_adjustment_import_template.xlsx';
        $content = "Mock Excel template for Stock Adjustment import";
        
        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function validateImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
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
                        'description' => 'Import adjustment test 1'
                    ],
                    [
                        'adjustment_number' => 'SA-2025-008',
                        'adjustment_date' => '2025-01-22',
                        'adjustment_type' => 'PRODUCT_RETURN',
                        'warehouse_name' => 'Gudang Finished Goods',
                        'total_items' => 2,
                        'total_adjustment_qty' => 15,
                        'status' => 'DRAFT',
                        'description' => 'Import adjustment test 2'
                    ]
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
                        'errors' => ['Nomor SA sudah ada', 'Tipe adjustment tidak valid', 'Gudang tidak ditemukan']
                    ]
                ]
            ];
            
            return response()->json([
                'success' => true,
                'total_count' => $mockData['total_count'],
                'success_count' => $mockData['success_count'],
                'error_count' => $mockData['error_count'],
                'success_data' => $mockData['success_data'],
                'error_data' => $mockData['error_data']
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading file: ' . $e->getMessage()
            ]);
        }
    }

    public function processImport(Request $request)
    {
        try {
            $data = $request->input('data');
            $importedCount = count($data);
            
            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => 'Data stock adjustment berhasil diimport'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ]);
        }
    }
}