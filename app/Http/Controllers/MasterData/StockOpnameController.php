<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        // Status options for stock opname with adjustment workflow
        $stockOpnameStatuses = [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'in_progress', 'label' => 'Sedang Dikerjakan'],
            ['value' => 'completed', 'label' => 'Selesai'],
            ['value' => 'adjustment_pending', 'label' => 'Menunggu Adjustment'],
            ['value' => 'adjustment_created', 'label' => 'Adjustment Dibuat'],
            ['value' => 'cancelled', 'label' => 'Dibatalkan']
        ];

        // Warehouse locations for RIYO Water
        $warehouseLocations = [
            ['value' => 'GDG-001', 'label' => 'Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'Gudang Finished Goods']
        ];

        // Product categories for RIYO Water
        $productCategories = [
            ['value' => 'finished_goods', 'label' => 'Produk Jadi'],
            ['value' => 'raw_materials', 'label' => 'Bahan Baku'],
            ['value' => 'packaging', 'label' => 'Kemasan'],
            ['value' => 'chemicals', 'label' => 'Bahan Kimia'],
            ['value' => 'machinery', 'label' => 'Peralatan']
        ];

        return view('pages.master_data.stock_opname.index', [
            'stockOpnameStatuses' => $stockOpnameStatuses,
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
                    "opname_number" => "SO-2025-001",
                    "opname_date" => "2025-01-15",
                    "warehouse_name" => "Gudang Utama Pusat",
                    "pic_name" => "Yustinus Komunal",
                    "total_products" => 45,
                    "counted_products" => 45,
                    "variance_qty" => 2,
                    "variance_value" => 125000,
                    "status" => "adjustment_pending",
                    "adjustment_number" => null
                ],
                [
                    "id" => 2,
                    "opname_number" => "SO-2025-002",
                    "opname_date" => "2025-01-16",
                    "warehouse_name" => "Gudang Produksi",
                    "pic_name" => "Ririn Safitri",
                    "total_products" => 28,
                    "counted_products" => 20,
                    "variance_qty" => 0,
                    "variance_value" => 0,
                    "status" => "in_progress",
                    "adjustment_number" => null
                ],
                [
                    "id" => 3,
                    "opname_number" => "SO-2025-003",
                    "opname_date" => "2025-01-17",
                    "warehouse_name" => "Gudang Bahan Baku",
                    "pic_name" => "Hartono Wijaya",
                    "total_products" => 62,
                    "counted_products" => 0,
                    "variance_qty" => 0,
                    "variance_value" => 0,
                    "status" => "draft",
                    "adjustment_number" => null
                ],
                [
                    "id" => 4,
                    "opname_number" => "SO-2025-004",
                    "opname_date" => "2025-01-18",
                    "warehouse_name" => "Gudang Finished Goods",
                    "pic_name" => "Sari Indah",
                    "total_products" => 15,
                    "counted_products" => 15,
                    "variance_qty" => -3,
                    "variance_value" => -45000,
                    "status" => "adjustment_created",
                    "adjustment_number" => "SA-2025-001"
                ],
                [
                    "id" => 5,
                    "opname_number" => "SO-2025-005",
                    "opname_date" => "2025-01-19",
                    "warehouse_name" => "Gudang Utama Pusat",
                    "pic_name" => "Budi Santoso",
                    "total_products" => 38,
                    "counted_products" => 15,
                    "variance_qty" => 0,
                    "variance_value" => 0,
                    "status" => "in_progress",
                    "adjustment_number" => null
                ],
                [
                    "id" => 6,
                    "opname_number" => "SO-2025-006",
                    "opname_date" => "2025-01-20",
                    "warehouse_name" => "Gudang Produksi",
                    "pic_name" => "Ani Suhartini",
                    "total_products" => 32,
                    "counted_products" => 32,
                    "variance_qty" => 1,
                    "variance_value" => 15000,
                    "status" => "completed",
                    "adjustment_number" => null
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        // Warehouses for RIYO Water
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'Gudang Finished Goods']
        ];

        // PIC options
        $picOptions = [
            ['value' => 'yustinus', 'label' => 'Yustinus Komunal'],
            ['value' => 'ririn', 'label' => 'Ririn Safitri'],
            ['value' => 'hartono', 'label' => 'Hartono Wijaya'],
            ['value' => 'sari', 'label' => 'Sari Indah'],
            ['value' => 'budi', 'label' => 'Budi Santoso'],
            ['value' => 'ani', 'label' => 'Ani Suhartini']
        ];

        // Product categories for filtering
        $productCategories = [
            ['value' => 'finished_goods', 'label' => 'Produk Jadi'],
            ['value' => 'raw_materials', 'label' => 'Bahan Baku'],
            ['value' => 'packaging', 'label' => 'Kemasan'],
            ['value' => 'chemicals', 'label' => 'Bahan Kimia'],
            ['value' => 'machinery', 'label' => 'Peralatan']
        ];

        // Status options
        $statusOptions = [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'in_progress', 'label' => 'Sedang Dikerjakan'],
            ['value' => 'completed', 'label' => 'Selesai']
        ];

        return view('pages.master_data.stock_opname.add', [
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'productCategories' => $productCategories,
            'statusOptions' => $statusOptions
        ]);
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
            'status' => 'adjustment_pending'
        ];

        // Warehouses for RIYO Water
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'Gudang Finished Goods']
        ];

        // PIC options
        $picOptions = [
            ['value' => 'yustinus', 'label' => 'Yustinus Komunal'],
            ['value' => 'ririn', 'label' => 'Ririn Safitri'],
            ['value' => 'hartono', 'label' => 'Hartono Wijaya'],
            ['value' => 'sari', 'label' => 'Sari Indah'],
            ['value' => 'budi', 'label' => 'Budi Santoso'],
            ['value' => 'ani', 'label' => 'Ani Suhartini']
        ];

        // Product categories for filtering
        $productCategories = [
            ['value' => 'finished_goods', 'label' => 'Produk Jadi'],
            ['value' => 'raw_materials', 'label' => 'Bahan Baku'],
            ['value' => 'packaging', 'label' => 'Kemasan'],
            ['value' => 'chemicals', 'label' => 'Bahan Kimia'],
            ['value' => 'machinery', 'label' => 'Peralatan']
        ];

        // Status options
        $statusOptions = [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'in_progress', 'label' => 'Sedang Dikerjakan'],
            ['value' => 'completed', 'label' => 'Selesai'],
            ['value' => 'adjustment_pending', 'label' => 'Menunggu Adjustment'],
            ['value' => 'adjustment_created', 'label' => 'Adjustment Dibuat'],
            ['value' => 'cancelled', 'label' => 'Dibatalkan']
        ];

        // Mock existing products for this stock opname
        $stockOpnameProducts = [
            [
                'id' => 1,
                'code' => 'AIR-330',
                'name' => 'RIYO Water 330ml',
                'unit' => 'pcs',
                'system_stock' => 1500,
                'physical_qty' => 1502,
                'variance' => 2,
                'notes' => 'Ada penambahan dari produksi',
                'category' => 'finished_goods'
            ],
            [
                'id' => 2,
                'code' => 'AIR-600',
                'name' => 'RIYO Water 600ml',
                'unit' => 'pcs',
                'system_stock' => 800,
                'physical_qty' => 800,
                'variance' => 0,
                'notes' => 'Sesuai dengan sistem',
                'category' => 'finished_goods'
            ],
            [
                'id' => 3,
                'code' => 'AIR-1500',
                'name' => 'RIYO Water 1500ml',
                'unit' => 'pcs',
                'system_stock' => 400,
                'physical_qty' => 400,
                'variance' => 0,
                'notes' => '',
                'category' => 'finished_goods'
            ]
        ];

        return view('pages.master_data.stock_opname.edit', [
            'stockOpname' => $stockOpname,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'productCategories' => $productCategories,
            'statusOptions' => $statusOptions,
            'stockOpnameProducts' => $stockOpnameProducts
        ]);
    }

    public function detail($id)
    {
        // Mock data for detail view
        $stockOpname = [
            'id' => $id,
            'opname_number' => 'SO-2025-001',
            'opname_date' => '2025-01-15',
            'warehouse_name' => 'Gudang Utama Pusat',
            'warehouse_code' => 'GDG-001',
            'pic_name' => 'Yustinus Komunal',
            'description' => 'Stock opname rutin bulanan untuk gudang utama pusat',
            'total_products' => 45,
            'counted_products' => 45,
            'variance_qty' => 2,
            'variance_value' => 125000,
            'status' => 'adjustment_pending',
            'adjustment_number' => null,
            'created_at' => '2025-01-15 08:00:00',
            'updated_at' => '2025-01-15 16:30:00'
        ];

        return view('pages.master_data.stock_opname.detail', [
            'stockOpname' => $stockOpname
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
            ['field' => 'total_products', 'label' => 'Total Produk', 'export_name' => 'Total Produk'],
            ['field' => 'counted_products', 'label' => 'Produk Terhitung', 'export_name' => 'Produk Terhitung'],
            ['field' => 'variance_qty', 'label' => 'Selisih Qty', 'export_name' => 'Selisih Qty'],
            ['field' => 'variance_value', 'label' => 'Selisih Nilai', 'export_name' => 'Selisih Nilai'],
            ['field' => 'status', 'label' => 'Status', 'export_name' => 'Status'],
            ['field' => 'adjustment_number', 'label' => 'Nomor Adjustment', 'export_name' => 'Nomor Adjustment'],
            ['field' => 'description', 'label' => 'Deskripsi', 'export_name' => 'Deskripsi']
        ];

        // Warehouses for filter
        $warehouses = [
            ['value' => 'GDG-001', 'label' => 'Gudang Utama Pusat'],
            ['value' => 'GDG-002', 'label' => 'Gudang Produksi'],
            ['value' => 'GDG-003', 'label' => 'Gudang Bahan Baku'],
            ['value' => 'GDG-004', 'label' => 'Gudang Finished Goods']
        ];

        // Status options for filter
        $statusOptions = [
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'in_progress', 'label' => 'Sedang Dikerjakan'],
            ['value' => 'completed', 'label' => 'Selesai'],
            ['value' => 'adjustment_pending', 'label' => 'Menunggu Adjustment'],
            ['value' => 'adjustment_created', 'label' => 'Adjustment Dibuat'],
            ['value' => 'cancelled', 'label' => 'Dibatalkan']
        ];

        return view('pages.master_data.stock_opname.export', [
            'exportColumns' => $exportColumns,
            'warehouses' => $warehouses,
            'statusOptions' => $statusOptions
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
                'total_products' => 45,
                'counted_products' => 45,
                'variance_qty' => 2,
                'variance_value' => 125000,
                'status' => 'Menunggu Adjustment',
                'adjustment_number' => null,
                'description' => 'Stock opname rutin bulanan'
            ],
            [
                'opname_number' => 'SO-2025-002',
                'opname_date' => '2025-01-16',
                'warehouse_name' => 'Gudang Produksi',
                'pic_name' => 'Ririn Safitri',
                'total_products' => 28,
                'counted_products' => 20,
                'variance_qty' => 0,
                'variance_value' => 0,
                'status' => 'Sedang Dikerjakan',
                'adjustment_number' => null,
                'description' => 'Stock opname produksi mingguan'
            ],
            [
                'opname_number' => 'SO-2025-003',
                'opname_date' => '2025-01-17',
                'warehouse_name' => 'Gudang Bahan Baku',
                'pic_name' => 'Hartono Wijaya',
                'total_products' => 62,
                'counted_products' => 0,
                'variance_qty' => 0,
                'variance_value' => 0,
                'status' => 'Draft',
                'adjustment_number' => null,
                'description' => 'Stock opname bahan baku'
            ]
        ]);
        
        // Apply filters
        if (!empty($filters['date_from'])) {
            $stockOpnames = $stockOpnames->filter(function($item) use ($filters) {
                return $item['opname_date'] >= $filters['date_from'];
            });
        }
        
        if (!empty($filters['date_to'])) {
            $stockOpnames = $stockOpnames->filter(function($item) use ($filters) {
                return $item['opname_date'] <= $filters['date_to'];
            });
        }
        
        if (!empty($filters['warehouse'])) {
            $stockOpnames = $stockOpnames->filter(function($item) use ($filters) {
                return strpos($item['warehouse_name'], $filters['warehouse']) !== false;
            });
        }
        
        if (!empty($filters['status'])) {
            $statusMap = [
                'draft' => 'Draft',
                'in_progress' => 'Sedang Dikerjakan',
                'completed' => 'Selesai',
                'adjustment_pending' => 'Menunggu Adjustment',
                'adjustment_created' => 'Adjustment Dibuat',
                'cancelled' => 'Dibatalkan'
            ];
            $statusLabel = $statusMap[$filters['status']] ?? $filters['status'];
            $stockOpnames = $stockOpnames->filter(function($item) use ($statusLabel) {
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
        $filename = 'stock_opname_export_' . date('Y-m-d_H-i-s') . '.xlsx';
        $content = $this->generateMockExcelContent($data, $columns);
        
        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportToCsv($data, $columns)
    {
        $filename = 'stock_opname_export_' . date('Y-m-d_H-i-s') . '.csv';
        $content = $this->generateCsvContent($data, $columns);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function exportToPdf($data, $columns)
    {
        // Mock PDF export - in real implementation, use TCPDF or similar
        $filename = 'stock_opname_export_' . date('Y-m-d_H-i-s') . '.pdf';
        $content = $this->generateMockPdfContent($data, $columns);
        
        return response($content)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    private function generateMockExcelContent($data, $columns)
    {
        // Mock Excel content - in real implementation, use PhpSpreadsheet
        return "Mock Excel content for Stock Opname export with " . count($data) . " records and " . count($columns) . " columns";
    }

    private function generateCsvContent($data, $columns)
    {
        $csv = [];
        
        // Add headers
        $headers = array_map(function($col) { return $col['label']; }, $columns);
        $csv[] = implode(',', $headers);
        
        // Add data rows
        foreach ($data as $row) {
            $csvRow = [];
            foreach ($columns as $column) {
                $value = $row[$column['field']] ?? '';
                // Escape CSV values
                if (strpos($value, ',') !== false || strpos($value, '"') !== false) {
                    $value = '"' . str_replace('"', '""', $value) . '"';
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
        return "Mock PDF content for Stock Opname export with " . count($data) . " records";
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
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function validateImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
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
            
            // Mock import process - in real implementation, save to database
            foreach ($data as $item) {
                // Create stock opname record
                // StockOpname::create([...]);
            }
            
            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => 'Data stock opname berhasil diimport'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ]);
        }
    }

    private function generateImportTemplate()
    {
        // Mock template generation - in real implementation, create actual Excel template
        return "Mock Excel template content with headers: Nomor SO, Tanggal, Gudang, PIC, Status, Deskripsi";
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
                    'description' => 'Stock opname import test 1'
                ],
                [
                    'opname_number' => 'SO-2025-005',
                    'opname_date' => '2025-01-19',
                    'warehouse_name' => 'Gudang Produksi',
                    'pic_name' => 'Ririn Safitri',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 2'
                ],
                [
                    'opname_number' => 'SO-2025-006',
                    'opname_date' => '2025-01-20',
                    'warehouse_name' => 'Gudang Bahan Baku',
                    'pic_name' => 'Hartono Wijaya',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 3'
                ],
                [
                    'opname_number' => 'SO-2025-007',
                    'opname_date' => '2025-01-21',
                    'warehouse_name' => 'Gudang Finished Goods',
                    'pic_name' => 'Sari Indah',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test 4'
                ]
            ],
            'error_data' => [
                [
                    'opname_number' => 'SO-2025-001',
                    'opname_date' => '2025-01-22',
                    'warehouse_name' => 'Gudang Invalid',
                    'pic_name' => '',
                    'status' => 'Draft',
                    'description' => 'Stock opname import test error',
                    'errors' => ['Nomor SO sudah ada', 'Gudang tidak valid', 'PIC wajib diisi']
                ]
            ]
        ];
    }

    public function createAdjustment($id)
    {
        // This would create a stock adjustment based on the opname results
        // For now, we'll just return success response
        return response()->json([
            'success' => true,
            'message' => 'Stock Adjustment berhasil dibuat',
            'adjustment_number' => 'SA-2025-' . str_pad($id, 3, '0', STR_PAD_LEFT)
        ]);
    }
}