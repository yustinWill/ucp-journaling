<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('pages.main_transaction.purchase.index', []);
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
                    "supplier" => "PT. Sumber Makmur",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "down_payment" => 3000000,
                    "total" => 15000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PO-2025-002",
                    "supplier" => "CV. Berkah Jaya",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "down_payment" => 1700000,
                    "total" => 8500000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PO-2025-003",
                    "supplier" => "PT. Mandiri Sejahtera",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "down_payment" => 5000000,
                    "total" => 25000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PO-2025-004",
                    "supplier" => "CV. Maju Bersama",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "down_payment" => 2400000,
                    "total" => 12000000,
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
                    "supplier" => "PT. Sumber Makmur",
                    "status" => "Belum Ditagih",
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-03",
                    "reference_no" => "PD-2025-002",
                    "supplier" => "CV. Berkah Jaya",
                    "status" => "Selesai",
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-04",
                    "reference_no" => "PD-2025-003",
                    "supplier" => "PT. Mandiri Sejahtera",
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
                    "reference_no" => "PI-2025-001",
                    "supplier" => "PT. Sumber Makmur",
                    "due_date" => "2025-07-15",
                    "status" => "Belum Dibayar",
                    "remaining_bill" => 15000000,
                    "total" => 15000000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "PI-2025-002",
                    "supplier" => "CV. Berkah Jaya",
                    "due_date" => "2025-07-16",
                    "status" => "Menunggu Pembayaran",
                    "remaining_bill" => 8500000,
                    "total" => 8500000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "PI-2025-003",
                    "supplier" => "PT. Mandiri Sejahtera",
                    "due_date" => "2025-07-17",
                    "status" => "Dibayar",
                    "remaining_bill" => 0,
                    "total" => 25000000,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "PI-2025-004",
                    "supplier" => "CV. Maju Bersama",
                    "due_date" => "2025-07-18",
                    "status" => "Dibayar Sebagian",
                    "remaining_bill" => 7200000,
                    "total" => 12000000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.main_transaction.purchase.add', []);
    }
}