<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        $pesananStatuses = [
            ['value' => 'Belum Dikirim', 'label' => 'Belum Dikirim'],
            ['value' => 'Dikirim Sebagian', 'label' => 'Dikirim Sebagian'],
            ['value' => 'Selesai', 'label' => 'Selesai'],
            ['value' => 'Dibatalkan', 'label' => 'Dibatalkan']
        ];

        $pengirimanStatuses = [
            ['value' => 'Belum Ditagih', 'label' => 'Belum Ditagih'],
            ['value' => 'Diterima', 'label' => 'Diterima'],
            ['value' => 'Selesai', 'label' => 'Selesai']
        ];

        $fakturStatuses = [
            ['value' => 'Belum Dibayar', 'label' => 'Belum Dibayar'],
            ['value' => 'Menunggu Pembayaran', 'label' => 'Menunggu Pembayaran'],
            ['value' => 'Telat Bayar', 'label' => 'Telat Bayar'],
            ['value' => 'Dibayar', 'label' => 'Dibayar'],
            ['value' => 'Dibayar Sebagian', 'label' => 'Dibayar Sebagian']
        ];

        return view('pages.main_transaction.purchase.index', [
            'pesananStatuses' => $pesananStatuses,
            'pengirimanStatuses' => $pengirimanStatuses,
            'fakturStatuses' => $fakturStatuses
        ]);
    }

    public function index_data_table(Request $request)
    {
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
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "PO-2025-001",
                    "supplier" => "CV Plastik Prima",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dikirim",
                    "down_payment" => 5000000,
                    "total" => 25000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PO-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "due_date" => "2025-07-16",
                    "status" => "Dikirim Sebagian",
                    "down_payment" => 3000000,
                    "total" => 15000000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PO-2025-003",
                    "supplier" => "CV Kemasan Sejahtera",
                    "due_date" => "2025-07-17",
                    "status" => "Selesai",
                    "down_payment" => 4000000,
                    "total" => 20000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PO-2025-004",
                    "supplier" => "PT Supplier Bahan Kimia",
                    "due_date" => "2025-07-18",
                    "status" => "Belum Dikirim",
                    "down_payment" => 2500000,
                    "total" => 12500000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    private function getPengirimanData()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-02",
                    "reference_no" => "PD-2025-001",
                    "supplier" => "CV Plastik Prima",
                    "status" => "Diterima",
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-03",
                    "reference_no" => "PD-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "status" => "Belum Ditagih",
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-04",
                    "reference_no" => "PD-2025-003",
                    "supplier" => "CV Kemasan Sejahtera",
                    "status" => "Selesai",
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-05",
                    "reference_no" => "PD-2025-004",
                    "supplier" => "PT Supplier Bahan Kimia",
                    "status" => "Belum Ditagih",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    private function getFakturData()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "PI-2025-001",
                    "supplier" => "CV Plastik Prima",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "remaining_bill" => 25000000,
                    "total" => 25000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PI-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "remaining_bill" => 15000000,
                    "total" => 15000000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PI-2025-003",
                    "supplier" => "CV Kemasan Sejahtera",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "remaining_bill" => 0,
                    "total" => 20000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PI-2025-004",
                    "supplier" => "PT Supplier Bahan Kimia",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "remaining_bill" => 7500000,
                    "total" => 12500000,
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-05",
                    "reference_no" => "PI-2025-005",
                    "supplier" => "PT Mesin Produksi Indonesia",
                    "due_date" => "2025-07-19",
                    "status" => "Telat Bayar",
                    "remaining_bill" => 45000000,
                    "total" => 45000000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function purchaseOrderAdd()
    {
        // Get all required options for the Purchase Order form
        $suppliers = [
            ['value' => '1', 'label' => 'PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'CV. Maju Bersama']
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B']
        ];

        $products = [
            ['value' => '1', 'label' => 'ALBUM SVT'],
            ['value' => '2', 'label' => 'Buku Tulis'],
            ['value' => '3', 'label' => 'Pensil'],
            ['value' => '4', 'label' => 'Penghapus']
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak']
        ];

        return view('pages.main_transaction.purchase.po_add', [
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions
        ]);
    }

    public function purchaseDeliveryAdd()
    {
        // Get required options for the Purchase Delivery form
        $suppliers = [
            ['value' => '1', 'label' => 'PT. Sumber Makmur'],
            ['value' => '2', 'label' => 'CV. Berkah Jaya'],
            ['value' => '3', 'label' => 'PT. Mandiri Sejahtera'],
            ['value' => '4', 'label' => 'CV. Maju Bersama']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B']
        ];

        return view('pages.main_transaction.purchase.pd_add', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function purchaseInvoiceAdd()
    {
        // Get required options for the Purchase Invoice form
        $purchaseDeliveries = [
            ['value' => 'PD-2025-001', 'label' => 'PD-2025-001 - PT. Sumber Makmur'],
            ['value' => 'PD-2025-002', 'label' => 'PD-2025-002 - CV. Berkah Jaya'],
            ['value' => 'PD-2025-003', 'label' => 'PD-2025-003 - PT. Mandiri Sejahtera'],
            ['value' => 'PD-2025-004', 'label' => 'PD-2025-004 - CV. Maju Bersama']
        ];

        $currencies = [
            ['value' => 'IDR', 'label' => 'IDR - Rupiah'],
            ['value' => 'USD', 'label' => 'USD - Dollar'],
            ['value' => 'EUR', 'label' => 'EUR - Euro']
        ];

        $paymentMethods = [
            ['value' => 'Transfer Bank', 'label' => 'Transfer Bank'],
            ['value' => 'Cash', 'label' => 'Cash'],
            ['value' => 'Cek', 'label' => 'Cek'],
            ['value' => 'Giro', 'label' => 'Giro']
        ];

        $taxOptions = [
            ['value' => 'PPN', 'label' => 'PPN 11%'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak']
        ];

        $costTypes = [
            ['value' => 'Biaya Kirim', 'label' => 'Biaya Kirim'],
            ['value' => 'Biaya Asuransi', 'label' => 'Biaya Asuransi'],
            ['value' => 'Biaya Bongkar', 'label' => 'Biaya Bongkar'],
            ['value' => 'Biaya Lainnya', 'label' => 'Biaya Lainnya']
        ];

        return view('pages.main_transaction.purchase.pi_add', [
            'purchaseDeliveries' => $purchaseDeliveries,
            'currencies' => $currencies,
            'paymentMethods' => $paymentMethods,
            'taxOptions' => $taxOptions,
            'costTypes' => $costTypes
        ]);
    }

    // Import/Export Methods
    public function import(Request $request)
    {
        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
            ['value' => '4', 'label' => 'PT Supplier Bahan Kimia'],
            ['value' => '5', 'label' => 'PT Mesin Produksi Indonesia']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Bahan Baku'],
            ['value' => '2', 'label' => 'Gudang Produksi'],
            ['value' => '3', 'label' => 'Gudang Finished Goods']
        ];

        return view('pages.main_transaction.purchase.import', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function downloadTemplate(Request $request)
    {
        // Generate Excel template for purchase import
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_purchase.xlsx"',
            'Cache-Control' => 'max-age=0',
        ];

        // Mock template content - in real implementation, use Laravel Excel
        $content = base64_decode('UEsDBBQAAAgIAAAAAAAAAAAAAAAAAAAUAAAAeGwvd29ya3NoZWV0cy9zaGVldDEueG1s');
        
        return response($content, 200, $headers);
    }

    public function validateImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        // Mock validation response
        $mockData = [
            'total' => 5,
            'valid_count' => 4,
            'error_count' => 1,
            'valid_data' => [
                [
                    'row' => 2,
                    'date' => '2025-07-19',
                    'reference_no' => 'PO-2025-020',
                    'supplier' => 'CV Plastik Prima',
                    'type' => 'Purchase Order',
                    'total' => 25000000,
                    'status' => 'Valid'
                ],
                [
                    'row' => 3,
                    'date' => '2025-07-20',
                    'reference_no' => 'PO-2025-021',
                    'supplier' => 'PT Grafika Mandiri',
                    'type' => 'Purchase Order',
                    'total' => 15000000,
                    'status' => 'Valid'
                ],
                [
                    'row' => 4,
                    'date' => '2025-07-21',
                    'reference_no' => 'PD-2025-020',
                    'supplier' => 'CV Kemasan Sejahtera',
                    'type' => 'Purchase Delivery',
                    'total' => 18000000,
                    'status' => 'Valid'
                ],
                [
                    'row' => 5,
                    'date' => '2025-07-22',
                    'reference_no' => 'PI-2025-020',
                    'supplier' => 'PT Supplier Bahan Kimia',
                    'type' => 'Purchase Invoice',
                    'total' => 12000000,
                    'status' => 'Valid'
                ]
            ],
            'error_data' => [
                [
                    'row' => 6,
                    'data' => [
                        'date' => '2025-13-30',
                        'reference_no' => '',
                        'supplier' => 'Unknown Supplier',
                        'total' => 'invalid_amount'
                    ],
                    'errors' => [
                        'Tanggal tidak valid',
                        'No referensi wajib diisi',
                        'Supplier tidak ditemukan',
                        'Total harus berupa angka'
                    ]
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $mockData
        ]);
    }

    public function processImport(Request $request)
    {
        $data = $request->json('data');
        
        // Mock import processing
        $importedCount = count($data);
        
        // Simulate processing time
        sleep(1);

        return response()->json([
            'success' => true,
            'imported_count' => $importedCount,
            'message' => 'Data pembelian berhasil diimpor'
        ]);
    }

    public function export(Request $request)
    {
        $suppliers = [
            ['value' => '1', 'label' => 'CV Plastik Prima'],
            ['value' => '2', 'label' => 'PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'CV Kemasan Sejahtera'],
            ['value' => '4', 'label' => 'PT Supplier Bahan Kimia'],
            ['value' => '5', 'label' => 'PT Mesin Produksi Indonesia']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Bahan Baku'],
            ['value' => '2', 'label' => 'Gudang Produksi'],
            ['value' => '3', 'label' => 'Gudang Finished Goods']
        ];

        return view('pages.main_transaction.purchase.export', [
            'suppliers' => $suppliers,
            'warehouses' => $warehouses
        ]);
    }

    public function processExport(Request $request)
    {
        $format = $request->input('export_format', 'excel');
        $filters = $request->only(['purchase_type', 'status', 'start_date', 'end_date', 'supplier', 'warehouse']);
        $columns = $request->input('columns', []);
        $columnNames = $request->input('column_names', []);

        // Mock export data based on filters
        $exportData = $this->getFilteredExportData($filters);
        
        // Generate filename
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "purchase_export_{$timestamp}";

        switch ($format) {
            case 'excel':
                return $this->generateExcelExport($exportData, $columns, $columnNames, $filename);
            case 'csv':
                return $this->generateCsvExport($exportData, $columns, $columnNames, $filename);
            case 'pdf':
                return $this->generatePdfExport($exportData, $columns, $columnNames, $filename);
            default:
                return redirect()->back()->with('error', 'Format export tidak valid');
        }
    }

    private function getFilteredExportData($filters)
    {
        // Mock filtered data - in real implementation, query database with filters
        return [
            [
                'date' => '2025-07-19',
                'reference_no' => 'PO-2025-020',
                'supplier' => 'CV Plastik Prima',
                'type' => 'Purchase Order',
                'status' => 'Belum Dikirim',
                'due_date' => '2025-08-02',
                'down_payment' => 5000000,
                'total' => 25000000,
                'remaining_bill' => 20000000,
                'notes' => 'Pembelian botol PET 330ml'
            ],
            [
                'date' => '2025-07-20',
                'reference_no' => 'PD-2025-021',
                'supplier' => 'PT Grafika Mandiri',
                'type' => 'Purchase Delivery',
                'status' => 'Diterima',
                'due_date' => '2025-08-03',
                'down_payment' => 0,
                'total' => 15000000,
                'remaining_bill' => 15000000,
                'notes' => 'Label kemasan RIYO Water'
            ]
        ];
    }

    private function generateExcelExport($data, $columns, $columnNames, $filename)
    {
        // Mock Excel export - in real implementation, use Laravel Excel
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xlsx\"",
            'Cache-Control' => 'max-age=0',
        ];

        $content = "Mock Excel Content for Purchase Export";
        return response($content, 200, $headers);
    }

    private function generateCsvExport($data, $columns, $columnNames, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        // Generate CSV content
        $csvContent = '';
        
        // Add headers
        if (!empty($columns)) {
            $headerRow = [];
            foreach ($columns as $column) {
                $headerRow[] = $columnNames[$column] ?? $column;
            }
            $csvContent .= implode(',', $headerRow) . "\n";
        }

        // Add data rows
        foreach ($data as $row) {
            $dataRow = [];
            foreach ($columns as $column) {
                $value = $row[$column] ?? '';
                $dataRow[] = '"' . str_replace('"', '""', $value) . '"';
            }
            $csvContent .= implode(',', $dataRow) . "\n";
        }

        return response($csvContent, 200, $headers);
    }

    private function generatePdfExport($data, $columns, $columnNames, $filename)
    {
        // Mock PDF export - in real implementation, use DomPDF or similar
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}.pdf\"",
        ];

        $content = "Mock PDF Content for Purchase Export";
        return response($content, 200, $headers);
    }
}