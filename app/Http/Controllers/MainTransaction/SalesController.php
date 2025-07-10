<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index()
    {
        return view('pages.main_transaction.sales.index', []);
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
                    "customer" => "PT. Cahaya Abadi",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "down_payment" => 5000000,
                    "total" => 25000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "SO-2025-002",
                    "customer" => "CV. Maju Jaya",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "down_payment" => 2000000,
                    "total" => 12000000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "SO-2025-003",
                    "customer" => "PT. Sukses Mandiri",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "down_payment" => 7500000,
                    "total" => 35000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "SO-2025-004",
                    "customer" => "CV. Berkah Sentosa",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "down_payment" => 3000000,
                    "total" => 18000000,
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
                    "customer" => "PT. Cahaya Abadi",
                    "status" => "Belum Ditagih",
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-03",
                    "reference_no" => "SD-2025-002",
                    "customer" => "CV. Maju Jaya",
                    "status" => "Selesai",
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-04",
                    "reference_no" => "SD-2025-003",
                    "customer" => "PT. Sukses Mandiri",
                    "status" => "Selesai",
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-05",
                    "reference_no" => "SD-2025-004",
                    "customer" => "CV. Berkah Sentosa",
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
                    "reference_no" => "SI-2025-001",
                    "customer" => "PT. Cahaya Abadi",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "remaining_bill" => 25000000,
                    "total" => 25000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "SI-2025-002",
                    "customer" => "CV. Maju Jaya",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "remaining_bill" => 12000000,
                    "total" => 12000000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "SI-2025-003",
                    "customer" => "PT. Sukses Mandiri",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "remaining_bill" => 0,
                    "total" => 35000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "SI-2025-004",
                    "customer" => "CV. Berkah Sentosa",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "remaining_bill" => 10800000,
                    "total" => 18000000,
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-05",
                    "reference_no" => "SI-2025-005",
                    "customer" => "PT. Nusantara Prima",
                    "due_date" => "2025-07-19",
                    "status" => "Telat Bayar",
                    "remaining_bill" => 22000000,
                    "total" => 22000000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.main_transaction.sales.add', []);
    }

    public function salesOrderAdd()
    {
        return view('pages.main_transaction.sales.so_add', []);
    }

    public function salesDeliveryAdd()
    {
        return view('pages.main_transaction.sales.sd_add', []);
    }

    public function salesInvoiceAdd()
    {
        return view('pages.main_transaction.sales.si_add', []);
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