<?php

namespace App\Http\Controllers\MainTransaction;

use App\Helpers\PermissionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        // Check read permission for sales module
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_sales');

        $pesananStatuses = [
            ['value' => 'Belum Ditagih', 'label' => 'Belum Ditagih'],
            ['value' => 'Dikirim Sebagian', 'label' => 'Dikirim Sebagian'],
            ['value' => 'Selesai', 'label' => 'Selesai'],
        ];

        $pengirimanStatuses = [
            ['value' => 'Belum Ditagih', 'label' => 'Belum Ditagih'],
            ['value' => 'Selesai', 'label' => 'Selesai'],
        ];

        $fakturStatuses = [
            ['value' => 'Menunggu Pembayaran', 'label' => 'Menunggu Pembayaran'],
            ['value' => 'Telat Bayar', 'label' => 'Telat Bayar'],
            ['value' => 'Dibayar', 'label' => 'Dibayar'],
            ['value' => 'Dibayar Sebagian', 'label' => 'Dibayar Sebagian'],
            ['value' => 'Belum Dibayar', 'label' => 'Belum Dibayar'],
        ];

        // Map permissions to view variables
        $canAddSales = PermissionHelper::canWrite('main_transaction_sales');
        $canImportSales = PermissionHelper::canWrite('main_transaction_sales');
        $canExportSales = PermissionHelper::canRead('main_transaction_sales');
        $canViewSales = PermissionHelper::canRead('main_transaction_sales');
        $canEditSales = PermissionHelper::canWrite('main_transaction_sales');

        $data = [
            'pesananStatuses' => $pesananStatuses,
            'pengirimanStatuses' => $pengirimanStatuses,
            'fakturStatuses' => $fakturStatuses,
            'canAddSales' => $canAddSales,
            'canImportSales' => $canImportSales,
            'canExportSales' => $canExportSales,
            'canViewSales' => $canViewSales,
            'canEditSales' => $canEditSales,
        ];

        return view('pages.main_transaction.sales.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $tab = $request->get('tab', 'pesanan');

        switch ($tab) {
            case 'pesanan':
                return $this->getPesananData();
            case 'pengiriman':
                return $this->getPengirimanData();
            case 'faktur':
                return $this->getFakturData();
            default:
                return $this->getPesananData();
        }
    }

    private function getPesananData()
    {
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'date' => '2025-07-01',
                    'reference_no' => 'SO-2025-001',
                    'customer' => 'Toko Segar Mart',
                    'customer_id' => 1,
                    'due_date' => '2025-07-15',
                    'status' => 'Belum Ditagih',
                    'down_payment' => 2500000,
                    'total' => 12500000,
                    'tags' => [
                        ['name' => 'Retail', 'color' => 'primary'],
                        ['name' => 'Regular', 'color' => 'success'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-02',
                    'reference_no' => 'SO-2025-002',
                    'customer' => 'Minimarket Berkah',
                    'customer_id' => 2,
                    'due_date' => '2025-07-16',
                    'status' => 'Dikirim Sebagian',
                    'down_payment' => 1800000,
                    'total' => 9000000,
                    'tags' => [
                        ['name' => 'Wholesale', 'color' => 'info'],
                    ],
                ],
                [
                    'id' => 3,
                    'date' => '2025-07-03',
                    'reference_no' => 'SO-2025-003',
                    'customer' => 'Supermarket Maju Jaya',
                    'customer_id' => 3,
                    'due_date' => '2025-07-17',
                    'status' => 'Selesai',
                    'down_payment' => 4500000,
                    'total' => 22500000,
                    'tags' => [
                        ['name' => 'Supermarket', 'color' => 'warning'],
                        ['name' => 'VIP', 'color' => 'danger'],
                    ],
                ],
                [
                    'id' => 4,
                    'date' => '2025-07-04',
                    'reference_no' => 'SO-2025-004',
                    'customer' => 'Warung Sembako Pak Haji',
                    'customer_id' => 4,
                    'due_date' => '2025-07-18',
                    'status' => 'Belum Ditagih',
                    'down_payment' => 1200000,
                    'total' => 6000000,
                    'tags' => [],
                ],
            ],
        ];

        return response()->json($data, 200);
    }

    private function getPengirimanData()
    {
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'date' => '2025-07-02',
                    'reference_no' => 'SD-2025-001',
                    'customer' => 'Toko Segar Mart',
                    'customer_id' => 1,
                    'status' => 'Belum Ditagih',
                    'tags' => [
                        ['name' => 'Delivered', 'color' => 'success'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-03',
                    'reference_no' => 'SD-2025-002',
                    'customer' => 'Minimarket Berkah',
                    'customer_id' => 2,
                    'status' => 'Selesai',
                    'tags' => [
                        ['name' => 'Complete', 'color' => 'primary'],
                    ],
                ],
                [
                    'id' => 3,
                    'date' => '2025-07-04',
                    'reference_no' => 'SD-2025-003',
                    'customer' => 'Supermarket Maju Jaya',
                    'customer_id' => 3,
                    'status' => 'Selesai',
                    'tags' => [
                        ['name' => 'Express', 'color' => 'warning'],
                    ],
                ],
                [
                    'id' => 4,
                    'date' => '2025-07-05',
                    'reference_no' => 'SD-2025-004',
                    'customer' => 'Warung Sembako Pak Haji',
                    'customer_id' => 4,
                    'status' => 'Belum Ditagih',
                    'tags' => [],
                ],
                [
                    'id' => 5,
                    'date' => '2025-07-06',
                    'reference_no' => 'SD-2025-005',
                    'customer' => 'Depot Air Minum Prima',
                    'customer_id' => 5,
                    'status' => 'Selesai',
                    'tags' => [
                        ['name' => 'Depot', 'color' => 'info'],
                    ],
                ],
            ],
        ];

        return response()->json($data, 200);
    }

    private function getFakturData()
    {
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'date' => '2025-07-01',
                    'reference_no' => 'SI-2025-001',
                    'customer' => 'Toko Segar Mart',
                    'customer_id' => 1,
                    'due_date' => '2025-07-15',
                    'status' => 'Belum Dibayar',
                    'remaining_bill' => 12500000,
                    'total' => 12500000,
                    'tags' => [
                        ['name' => 'Outstanding', 'color' => 'danger'],
                    ],
                ],
                [
                    'id' => 2,
                    'date' => '2025-07-02',
                    'reference_no' => 'SI-2025-002',
                    'customer' => 'Minimarket Berkah',
                    'customer_id' => 2,
                    'due_date' => '2025-07-16',
                    'status' => 'Menunggu Pembayaran',
                    'remaining_bill' => 9000000,
                    'total' => 9000000,
                    'tags' => [
                        ['name' => 'Pending', 'color' => 'warning'],
                    ],
                ],
                [
                    'id' => 3,
                    'date' => '2025-07-03',
                    'reference_no' => 'SI-2025-003',
                    'customer' => 'Supermarket Maju Jaya',
                    'customer_id' => 3,
                    'due_date' => '2025-07-17',
                    'status' => 'Dibayar',
                    'remaining_bill' => 0,
                    'total' => 22500000,
                    'tags' => [
                        ['name' => 'Paid', 'color' => 'success'],
                    ],
                ],
                [
                    'id' => 4,
                    'date' => '2025-07-04',
                    'reference_no' => 'SI-2025-004',
                    'customer' => 'Warung Sembako Pak Haji',
                    'customer_id' => 4,
                    'due_date' => '2025-07-18',
                    'status' => 'Dibayar Sebagian',
                    'remaining_bill' => 3600000,
                    'total' => 6000000,
                    'tags' => [
                        ['name' => 'Partial', 'color' => 'info'],
                    ],
                ],
                [
                    'id' => 5,
                    'date' => '2025-07-05',
                    'reference_no' => 'SI-2025-005',
                    'customer' => 'Depot Air Minum Prima',
                    'customer_id' => 5,
                    'due_date' => '2025-07-19',
                    'status' => 'Telat Bayar',
                    'remaining_bill' => 18000000,
                    'total' => 18000000,
                    'tags' => [
                        ['name' => 'Overdue', 'color' => 'danger'],
                        ['name' => 'High Value', 'color' => 'primary'],
                    ],
                ],
            ],
        ];

        return response()->json($data, 200);
    }

    public function salesOrderAdd()
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
        // Get all required options for the Sales Order form
        $customers = [
            ['value' => '1', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '2', 'label' => 'CV. Maju Jaya'],
            ['value' => '3', 'label' => 'PT. Sukses Mandiri'],
            ['value' => '4', 'label' => 'CV. Berkah Sentosa'],
            ['value' => '5', 'label' => 'PT. Nusantara Prima'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate'],
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B'],
        ];

        $products = [
            ['value' => '1', 'label' => 'ALBUM SVT'],
            ['value' => '2', 'label' => 'Buku Tulis'],
            ['value' => '3', 'label' => 'Pensil'],
            ['value' => '4', 'label' => 'Penghapus'],
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak'],
        ];

        return view('pages.main_transaction.sales.so_add', [
            'customers' => $customers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions,
        ]);
    }

    public function salesDeliveryAdd()
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
        // Get required options for the Sales Delivery form
        $customers = [
            ['value' => '1', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '2', 'label' => 'CV. Maju Jaya'],
            ['value' => '3', 'label' => 'PT. Sukses Mandiri'],
            ['value' => '4', 'label' => 'CV. Berkah Sentosa'],
            ['value' => '5', 'label' => 'PT. Nusantara Prima'],
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B'],
        ];

        return view('pages.main_transaction.sales.sd_add', [
            'customers' => $customers,
            'warehouses' => $warehouses,
        ]);
    }

    public function salesInvoiceAdd()
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
        // Get all required options for the Sales Invoice form
        $customers = [
            ['value' => '1', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '2', 'label' => 'CV. Maju Jaya'],
            ['value' => '3', 'label' => 'PT. Sukses Mandiri'],
            ['value' => '4', 'label' => 'CV. Berkah Sentosa'],
            ['value' => '5', 'label' => 'PT. Nusantara Prima'],
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate'],
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B'],
        ];

        $products = [
            ['value' => '1', 'label' => 'ALBUM SVT'],
            ['value' => '2', 'label' => 'Buku Tulis'],
            ['value' => '3', 'label' => 'Pensil'],
            ['value' => '4', 'label' => 'Penghapus'],
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak'],
        ];

        return view('pages.main_transaction.sales.si_add', [
            'customers' => $customers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions,
        ]);
    }

    // Import/Export Methods
    public function import(Request $request)
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }
        $customers = [
            ['value' => '1', 'label' => 'Toko Segar Mart'],
            ['value' => '2', 'label' => 'Minimarket Berkah'],
            ['value' => '3', 'label' => 'Supermarket Maju Jaya'],
            ['value' => '4', 'label' => 'Warung Sembako Pak Haji'],
            ['value' => '5', 'label' => 'Depot Air Minum Prima'],
        ];

        $salesReps = [
            ['value' => '1', 'label' => 'Ahmad Rizki - Sales Manager'],
            ['value' => '2', 'label' => 'Siti Nurhaliza - Sales Executive'],
            ['value' => '3', 'label' => 'Budi Santoso - Area Sales'],
        ];

        return view('pages.main_transaction.sales.import', [
            'customers' => $customers,
            'salesReps' => $salesReps,
        ]);
    }

    public function downloadTemplate()
    {
        // Check write permission for import template
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }
        // Generate and return Excel template file
        $filename = 'sales_import_template.xlsx';
        $content = $this->generateImportTemplate();

        return response($content)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    private function generateImportTemplate()
    {
        // Mock template content - in real implementation, use Laravel Excel
        return base64_decode('UEsDBBQAAAgIAAAAAAAAAAAAAAAAAAAUAAAAeGwvd29ya3NoZWV0cy9zaGVldDEueG1s');
    }

    public function validateImport(Request $request)
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            // Process and validate file
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

    private function generateMockImportValidation()
    {
        return [
            'total_count' => 5,
            'success_count' => 4,
            'error_count' => 1,
            'success_data' => [
                [
                    'date' => '2025-07-19',
                    'reference_no' => 'SO-2025-020',
                    'customer' => 'Toko Segar Mart',
                    'type' => 'Sales Order',
                    'total' => 'Rp 12,500,000',
                    'status' => 'Valid',
                ],
                [
                    'date' => '2025-07-20',
                    'reference_no' => 'SO-2025-021',
                    'customer' => 'Minimarket Berkah',
                    'type' => 'Sales Order',
                    'total' => 'Rp 9,000,000',
                    'status' => 'Valid',
                ],
                [
                    'date' => '2025-07-21',
                    'reference_no' => 'SD-2025-020',
                    'customer' => 'Supermarket Maju Jaya',
                    'type' => 'Sales Delivery',
                    'total' => 'Rp 22,500,000',
                    'status' => 'Valid',
                ],
                [
                    'date' => '2025-07-22',
                    'reference_no' => 'SI-2025-020',
                    'customer' => 'Warung Sembako Pak Haji',
                    'type' => 'Sales Invoice',
                    'total' => 'Rp 6,000,000',
                    'status' => 'Valid',
                ],
            ],
            'error_data' => [
                [
                    'date' => '2025-13-30',
                    'reference_no' => '',
                    'customer' => 'Unknown Customer',
                    'type' => 'Sales Order',
                    'total' => 'invalid_amount',
                    'status' => 'Error',
                    'errors' => [
                        'Tanggal tidak valid',
                        'No referensi wajib diisi',
                        'Customer tidak ditemukan',
                        'Total harus berupa angka',
                    ],
                ],
            ],
        ];
    }

    public function processImport(Request $request)
    {
        // Check write permission for import
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        try {
            $data = $request->input('data');
            $importedCount = count($data);

            // Process import
            foreach ($data as $item) {
                // Save to database
            }

            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => 'Data penjualan berhasil diimport',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: '.$e->getMessage(),
            ]);
        }
    }

    public function export()
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }
        // Export columns configuration
        $exportColumns = [
            ['field' => 'date', 'label' => 'Tanggal', 'export_name' => 'Tanggal'],
            ['field' => 'reference_no', 'label' => 'No Referensi', 'export_name' => 'No Referensi'],
            ['field' => 'customer', 'label' => 'Customer', 'export_name' => 'Customer'],
            ['field' => 'type', 'label' => 'Tipe', 'export_name' => 'Tipe'],
            ['field' => 'status', 'label' => 'Status', 'export_name' => 'Status'],
            ['field' => 'due_date', 'label' => 'Tgl Jatuh Tempo', 'export_name' => 'Tgl Jatuh Tempo'],
            ['field' => 'down_payment', 'label' => 'Uang Muka', 'export_name' => 'Uang Muka'],
            ['field' => 'total', 'label' => 'Total', 'export_name' => 'Total'],
            ['field' => 'remaining_bill', 'label' => 'Sisa Tagihan', 'export_name' => 'Sisa Tagihan'],
            ['field' => 'sales_rep', 'label' => 'Sales Rep', 'export_name' => 'Sales Rep'],
            ['field' => 'notes', 'label' => 'Catatan', 'export_name' => 'Catatan'],
        ];

        return view('pages.main_transaction.sales.export', [
            'exportColumns' => $exportColumns,
        ]);
    }

    public function processExport(Request $request)
    {
        // Check read permission for export
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $format = $request->input('format');
        $columns = json_decode($request->input('columns'), true);
        $filters = json_decode($request->input('filters'), true);

        // Get filtered data
        $data = $this->getFilteredData($filters);

        switch ($format) {
            case 'excel':
                return $this->exportToExcel($data, $columns);
            case 'csv':
                return $this->exportToCsv($data, $columns);
            case 'pdf':
                return $this->exportToPdf($data, $columns);
            default:
                return response()->json(['error' => 'Invalid format'], 400);
        }
    }

    private function getFilteredData($filters)
    {
        // Mock filtered data - in real implementation, query database with filters
        return [
            [
                'date' => '2025-07-19',
                'reference_no' => 'SO-2025-020',
                'customer' => 'Toko Segar Mart',
                'type' => 'Sales Order',
                'status' => 'Belum Ditagih',
                'due_date' => '2025-08-02',
                'down_payment' => 2500000,
                'total' => 12500000,
                'remaining_bill' => 10000000,
                'sales_rep' => 'Ahmad Rizki',
                'notes' => 'Penjualan RIYO Water 330ml - 5000 pcs',
            ],
            [
                'date' => '2025-07-20',
                'reference_no' => 'SD-2025-021',
                'customer' => 'Minimarket Berkah',
                'type' => 'Sales Delivery',
                'status' => 'Selesai',
                'due_date' => '2025-08-03',
                'down_payment' => 0,
                'total' => 9000000,
                'remaining_bill' => 9000000,
                'sales_rep' => 'Siti Nurhaliza',
                'notes' => 'Pengiriman RIYO Water 600ml - 2500 pcs',
            ],
        ];
    }

    private function exportToExcel($data, $columns)
    {
        // Mock Excel export - in real implementation, use Laravel Excel
        $filename = 'sales_export_'.now()->format('Y-m-d_H-i-s').'.xlsx';
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ];

        $content = 'Mock Excel Content for Sales Export';

        return response($content, 200, $headers);
    }

    private function exportToCsv($data, $columns)
    {
        $filename = 'sales_export_'.now()->format('Y-m-d_H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Generate CSV content
        $csvContent = '';

        // Add headers
        if (! empty($columns)) {
            $headerRow = [];
            foreach ($columns as $column) {
                $headerRow[] = $column['label'] ?? $column['field'];
            }
            $csvContent .= implode(',', $headerRow)."\n";
        }

        // Add data rows
        foreach ($data as $row) {
            $dataRow = [];
            foreach ($columns as $column) {
                $value = $row[$column['field']] ?? '';
                $dataRow[] = '"'.str_replace('"', '""', $value).'"';
            }
            $csvContent .= implode(',', $dataRow)."\n";
        }

        return response($csvContent, 200, $headers);
    }

    private function exportToPdf($data, $columns)
    {
        // Mock PDF export - in real implementation, use DomPDF or similar
        $filename = 'sales_export_'.now()->format('Y-m-d_H-i-s').'.pdf';
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $content = 'Mock PDF Content for Sales Export';

        return response($content, 200, $headers);
    }

    public function detail($id)
    {
        // Check read permission
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }

        // TODO: Implement sales detail functionality
        return view('pages.main_transaction.sales.detail', [
            'id' => $id,
        ]);
    }

    public function edit($id)
    {
        // Check write permission
        if (! PermissionHelper::hasPermission('main_transaction_sales', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        // TODO: Implement sales edit functionality
        return view('pages.main_transaction.sales.edit', [
            'id' => $id,
        ]);
    }
}
