<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        // Check read permission for purchase module
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase');
        
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

        // Map permissions to view variables
        $canAddPurchase = PermissionHelper::canWrite('main_transaction_purchase');
        $canImportPurchase = PermissionHelper::canWrite('main_transaction_purchase');
        $canExportPurchase = PermissionHelper::canRead('main_transaction_purchase');
        $canViewPurchase = PermissionHelper::canRead('main_transaction_purchase');
        $canEditPurchase = PermissionHelper::canWrite('main_transaction_purchase');

        $data = [
            'pesananStatuses' => $pesananStatuses,
            'pengirimanStatuses' => $pengirimanStatuses,
            'fakturStatuses' => $fakturStatuses,
            'canAddPurchase' => $canAddPurchase,
            'canImportPurchase' => $canImportPurchase,
            'canExportPurchase' => $canExportPurchase,
            'canViewPurchase' => $canViewPurchase,
            'canEditPurchase' => $canEditPurchase
        ];
        
        return view('pages.main_transaction.purchase.index', array_merge($data, $permissionData));
    }

    public function index_data_table(Request $request)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'read')) {
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
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "PO-2025-001",
                    "supplier" => "CV Plastik Prima",
                    "supplier_id" => 1,
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dikirim",
                    "down_payment" => 5000000,
                    "total" => 25000000,
                    "tags" => [
                        ["name" => "Bahan Baku", "color" => "primary"],
                        ["name" => "Urgent", "color" => "warning"]
                    ],
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PO-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "supplier_id" => 2,
                    "due_date" => "2025-07-16",
                    "status" => "Dikirim Sebagian",
                    "down_payment" => 3000000,
                    "total" => 15000000,
                    "tags" => [
                        ["name" => "Kemasan", "color" => "success"]
                    ],
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PO-2025-003",
                    "supplier" => "CV Kemasan Sejahtera",
                    "supplier_id" => 3,
                    "due_date" => "2025-07-17",
                    "status" => "Selesai",
                    "down_payment" => 4000000,
                    "total" => 20000000,
                    "tags" => [
                        ["name" => "Botol", "color" => "info"],
                        ["name" => "Regular", "color" => "secondary"]
                    ],
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PO-2025-004",
                    "supplier" => "PT Supplier Bahan Kimia",
                    "supplier_id" => 4,
                    "due_date" => "2025-07-18",
                    "status" => "Belum Dikirim",
                    "down_payment" => 2500000,
                    "total" => 12500000,
                    "tags" => [],
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
                    "supplier_id" => 1,
                    "status" => "Diterima",
                    "tags" => [
                        ["name" => "Diterima", "color" => "success"]
                    ],
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-03",
                    "reference_no" => "PD-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "supplier_id" => 2,
                    "status" => "Belum Ditagih",
                    "tags" => [
                        ["name" => "Pending", "color" => "warning"]
                    ],
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-04",
                    "reference_no" => "PD-2025-003",
                    "supplier" => "CV Kemasan Sejahtera",
                    "supplier_id" => 3,
                    "status" => "Selesai",
                    "tags" => [
                        ["name" => "Complete", "color" => "success"]
                    ],
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-05",
                    "reference_no" => "PD-2025-004",
                    "supplier" => "PT Supplier Bahan Kimia",
                    "supplier_id" => 4,
                    "status" => "Belum Ditagih",
                    "tags" => [],
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
                    "supplier_id" => 1,
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "remaining_bill" => 25000000,
                    "total" => 25000000,
                    "tags" => [
                        ["name" => "High Value", "color" => "danger"]
                    ],
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PI-2025-002",
                    "supplier" => "PT Grafika Mandiri",
                    "supplier_id" => 2,
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "remaining_bill" => 15000000,
                    "total" => 15000000,
                    "tags" => [
                        ["name" => "Approved", "color" => "info"]
                    ],
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PI-2025-003",
                    "supplier" => "CV Kemasan Sejahtera",
                    "supplier_id" => 3,
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "remaining_bill" => 0,
                    "total" => 20000000,
                    "tags" => [
                        ["name" => "Paid", "color" => "success"]
                    ],
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PI-2025-004",
                    "supplier" => "PT Supplier Bahan Kimia",
                    "supplier_id" => 4,
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "remaining_bill" => 7500000,
                    "total" => 12500000,
                    "tags" => [
                        ["name" => "Partial", "color" => "warning"]
                    ],
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-05",
                    "reference_no" => "PI-2025-005",
                    "supplier" => "PT Mesin Produksi Indonesia",
                    "supplier_id" => 5,
                    "due_date" => "2025-07-19",
                    "status" => "Telat Bayar",
                    "remaining_bill" => 45000000,
                    "total" => 45000000,
                    "tags" => [
                        ["name" => "Overdue", "color" => "danger"],
                        ["name" => "High Value", "color" => "primary"]
                    ],
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function purchaseOrderAdd()
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
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
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
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
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }
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
        // Check write permission for import
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengimpor data.');
        }
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
        // Check write permission for import template
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template.');
        }
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
        // Check write permission for import
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
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
        // Check write permission for import
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
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
        // Check read permission for export
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk mengekspor data.');
        }
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
        // Check read permission for export
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
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

    public function detail($id)
    {
        // Check read permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase');
        
        // Mock purchase data - in real implementation, get from database
        $purchase = [
            'id' => $id,
            'reference_no' => 'PO-2025-001',
            'date' => '2025-07-01',
            'supplier' => 'CV Plastik Prima',
            'supplier_id' => 1,
            'status' => 'APPROVED',
            'due_date' => '2025-07-15',
            'type' => 'Purchase Order',
            'down_payment' => 5000000,
            'total' => 25000000,
            'remaining_bill' => 20000000,
            'currency' => 'IDR',
            'warehouse' => 'Gudang Utama',
            'pic' => 'Ahmad Supardi',
            'payment_method' => 'Transfer Bank',
            'payment_terms' => 'Net 30',
            'notes' => 'Pembelian botol PET untuk produksi RIYO Water',
            'created_at' => '2025-07-01 08:00:00',
            'updated_at' => '2025-07-01 10:30:00'
        ];
        
        // Mock audit logs
        $auditLogs = [
            [
                'created_at' => '2025-07-01 10:30:00',
                'user_id' => 1,
                'user_name' => 'Admin System',
                'action_type' => 'UPDATE',
                'description' => 'Status diubah dari PENDING_APPROVAL menjadi APPROVED'
            ],
            [
                'created_at' => '2025-07-01 08:00:00',
                'user_id' => 2,
                'user_name' => 'Purchasing Staff',
                'action_type' => 'CREATE',
                'description' => 'Purchase order dibuat dengan total Rp 25,000,000'
            ]
        ];
        
        $data = [
            'purchase' => $purchase,
            'auditLogs' => $auditLogs,
            'canEditPurchase' => PermissionHelper::canWrite('main_transaction_purchase'),
            'canDeletePurchase' => PermissionHelper::canDelete('main_transaction_purchase'),
            'canApprovePurchase' => PermissionHelper::canApprove('main_transaction_purchase')
        ];
        
        return view('pages.main_transaction.purchase.detail', array_merge($data, $permissionData));
    }

    public function edit($id)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }
        
        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_purchase');
        
        // Mock purchase data - in real implementation, get from database
        $purchase = [
            'id' => $id,
            'reference_no' => 'PO-2025-001',
            'date' => '2025-07-01',
            'supplier_id' => 1,
            'status' => 'DRAFT',
            'due_date' => '2025-07-15',
            'type' => 'Purchase Order',
            'down_payment' => 5000000,
            'total' => 25000000,
            'currency' => 'IDR',
            'warehouse_id' => 1,
            'pic_id' => 1,
            'payment_method' => 'Transfer Bank',
            'payment_terms' => 'Net 30',
            'notes' => 'Pembelian botol PET untuk produksi RIYO Water'
        ];
        
        // Options for form
        $suppliers = [
            ['value' => '1', 'label' => 'SUP-001 - CV Plastik Prima'],
            ['value' => '2', 'label' => 'SUP-002 - PT Grafika Mandiri'],
            ['value' => '3', 'label' => 'SUP-003 - CV Kemasan Sejahtera'],
            ['value' => '4', 'label' => 'SUP-004 - PT Supplier Bahan Kimia']
        ];

        $purchaseTypes = [
            ['value' => 'Purchase Order', 'label' => 'PO - Purchase Order'],
            ['value' => 'Purchase Delivery', 'label' => 'PD - Purchase Delivery'],
            ['value' => 'Purchase Invoice', 'label' => 'PI - Purchase Invoice']
        ];

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PENDING_APPROVAL', 'label' => 'PENDING_APPROVAL - Menunggu Persetujuan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
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

        $warehouses = [
            ['value' => '1', 'label' => 'GDG-001 - Gudang Utama'],
            ['value' => '2', 'label' => 'GDG-002 - Gudang Cabang A'],
            ['value' => '3', 'label' => 'GDG-003 - Gudang Cabang B']
        ];

        $picOptions = [
            ['value' => '1', 'label' => 'USR-001 - Ahmad Supardi'],
            ['value' => '2', 'label' => 'USR-002 - Siti Nurhaliza'],
            ['value' => '3', 'label' => 'USR-003 - Budi Santoso']
        ];

        $paymentTerms = [
            ['value' => 'Net 30', 'label' => 'Net 30'],
            ['value' => 'Net 15', 'label' => 'Net 15'],
            ['value' => 'Net 45', 'label' => 'Net 45'],
            ['value' => 'Cash on Delivery', 'label' => 'Cash on Delivery'],
            ['value' => 'Immediate', 'label' => 'Immediate']
        ];
        
        $data = [
            'purchase' => $purchase,
            'suppliers' => $suppliers,
            'purchaseTypes' => $purchaseTypes,
            'statuses' => $statuses,
            'currencies' => $currencies,
            'paymentMethods' => $paymentMethods,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            'paymentTerms' => $paymentTerms,
            'canWrite' => PermissionHelper::canWrite('main_transaction_purchase')
        ];
        
        return view('pages.main_transaction.purchase.edit', array_merge($data, $permissionData));
    }
    
    public function update(Request $request)
    {
        // Check write permission
        if (!PermissionHelper::hasPermission('main_transaction_purchase', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => 'PERMISSION_DENIED',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'date' => 'required|date',
                'supplier_id' => 'required|integer',
                'type' => 'required|string',
                'status' => 'required|string',
                'due_date' => 'nullable|date',
                'down_payment' => 'nullable|numeric|min:0',
                'total' => 'required|numeric|min:0',
                'currency' => 'nullable|string',
                'payment_method' => 'nullable|string',
                'warehouse_id' => 'nullable|integer',
                'pic_id' => 'nullable|integer',
                'payment_terms' => 'nullable|string',
                'notes' => 'nullable|string|max:500'
            ]);

            // Mock update logic - in real implementation, update database
            // app(UpdatePurchaseAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => 'Pembelian berhasil diperbarui'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Kesalahan validasi: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'error' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => 'INTERNAL_SERVER_ERROR',
            ], 500);
        }
    }
}