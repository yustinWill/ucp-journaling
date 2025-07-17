<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        $pesananStatuses = [
            ['value' => 'Belum Ditagih', 'label' => 'Belum Ditagih'],
            ['value' => 'Dikirim Sebagian', 'label' => 'Dikirim Sebagian'],
            ['value' => 'Selesai', 'label' => 'Selesai']
        ];

        $pengirimanStatuses = [
            ['value' => 'Belum Ditagih', 'label' => 'Belum Ditagih'],
            ['value' => 'Selesai', 'label' => 'Selesai']
        ];

        $fakturStatuses = [
            ['value' => 'Menunggu Pembayaran', 'label' => 'Menunggu Pembayaran'],
            ['value' => 'Telat Bayar', 'label' => 'Telat Bayar'],
            ['value' => 'Dibayar', 'label' => 'Dibayar'],
            ['value' => 'Dibayar Sebagian', 'label' => 'Dibayar Sebagian'],
            ['value' => 'Belum Dibayar', 'label' => 'Belum Dibayar']
        ];

        return view('pages.main_transaction.sales.index', [
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
                    "reference_no" => "SO-2025-001",
                    "customer" => "Toko Segar Mart",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Ditagih",
                    "down_payment" => 2500000,
                    "total" => 12500000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "SO-2025-002",
                    "customer" => "Minimarket Berkah",
                    "due_date" => "2025-07-16",
                    "status" => "Dikirim Sebagian",
                    "down_payment" => 1800000,
                    "total" => 9000000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "SO-2025-003",
                    "customer" => "Supermarket Maju Jaya",
                    "due_date" => "2025-07-17",
                    "status" => "Selesai",
                    "down_payment" => 4500000,
                    "total" => 22500000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "SO-2025-004",
                    "customer" => "Warung Sembako Pak Haji",
                    "due_date" => "2025-07-18",
                    "status" => "Belum Ditagih",
                    "down_payment" => 1200000,
                    "total" => 6000000,
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
                    "reference_no" => "SD-2025-001",
                    "customer" => "Toko Segar Mart",
                    "status" => "Belum Ditagih",
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-03",
                    "reference_no" => "SD-2025-002",
                    "customer" => "Minimarket Berkah",
                    "status" => "Selesai",
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-04",
                    "reference_no" => "SD-2025-003",
                    "customer" => "Supermarket Maju Jaya",
                    "status" => "Selesai",
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-05",
                    "reference_no" => "SD-2025-004",
                    "customer" => "Warung Sembako Pak Haji",
                    "status" => "Belum Ditagih",
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-06",
                    "reference_no" => "SD-2025-005",
                    "customer" => "Depot Air Minum Prima",
                    "status" => "Selesai",
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
                    "reference_no" => "SI-2025-001",
                    "customer" => "Toko Segar Mart",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "remaining_bill" => 12500000,
                    "total" => 12500000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "SI-2025-002",
                    "customer" => "Minimarket Berkah",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "remaining_bill" => 9000000,
                    "total" => 9000000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "SI-2025-003",
                    "customer" => "Supermarket Maju Jaya",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "remaining_bill" => 0,
                    "total" => 22500000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "SI-2025-004",
                    "customer" => "Warung Sembako Pak Haji",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "remaining_bill" => 3600000,
                    "total" => 6000000,
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-05",
                    "reference_no" => "SI-2025-005",
                    "customer" => "Depot Air Minum Prima",
                    "due_date" => "2025-07-19",
                    "status" => "Telat Bayar",
                    "remaining_bill" => 18000000,
                    "total" => 18000000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function salesOrderAdd()
    {
        // Get all required options for the Sales Order form
        $customers = [
            ['value' => '1', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '2', 'label' => 'CV. Maju Jaya'],
            ['value' => '3', 'label' => 'PT. Sukses Mandiri'],
            ['value' => '4', 'label' => 'CV. Berkah Sentosa'],
            ['value' => '5', 'label' => 'PT. Nusantara Prima']
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

        return view('pages.main_transaction.sales.so_add', [
            'customers' => $customers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions
        ]);
    }

    public function salesDeliveryAdd()
    {
        // Get required options for the Sales Delivery form
        $customers = [
            ['value' => '1', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '2', 'label' => 'CV. Maju Jaya'],
            ['value' => '3', 'label' => 'PT. Sukses Mandiri'],
            ['value' => '4', 'label' => 'CV. Berkah Sentosa'],
            ['value' => '5', 'label' => 'PT. Nusantara Prima']
        ];

        $warehouses = [
            ['value' => '1', 'label' => 'Gudang Utama'],
            ['value' => '2', 'label' => 'Gudang Cabang A'],
            ['value' => '3', 'label' => 'Gudang Cabang B']
        ];

        return view('pages.main_transaction.sales.sd_add', [
            'customers' => $customers,
            'warehouses' => $warehouses
        ]);
    }

    public function salesInvoiceAdd()
    {
        // Get all required options for the Sales Invoice form
        $customers = [
            ['value' => '1', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '2', 'label' => 'CV. Maju Jaya'],
            ['value' => '3', 'label' => 'PT. Sukses Mandiri'],
            ['value' => '4', 'label' => 'CV. Berkah Sentosa'],
            ['value' => '5', 'label' => 'PT. Nusantara Prima']
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

        return view('pages.main_transaction.sales.si_add', [
            'customers' => $customers,
            'paymentTerms' => $paymentTerms,
            'warehouses' => $warehouses,
            'products' => $products,
            'taxOptions' => $taxOptions
        ]);
    }

    public function handleImport(Request $request)
    {
        // TODO: Implement sales import functionality
        return response()->json(['message' => 'Sales import functionality to be implemented'], 200);
    }

    public function handleExport(Request $request)
    {
        // TODO: Implement sales export functionality
        return response()->json(['message' => 'Sales export functionality to be implemented'], 200);
    }
}