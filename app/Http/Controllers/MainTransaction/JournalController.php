<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index()
    {
        return view('pages.main_transaction.journal.index', []);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "250601.JLL.QE123W",
                    "notes" => "Transaksi Pembelian Galon Air Minum",
                    "amount" => 100000,
                    "created_by" => "Cinta",
                    "created_at" => "2025-06-01 10:00:00",
                ],
                [
                    "id" => 1,
                    "date" => "2025-07-02",
                    "reference_no" => "250601.JLL.QEX23W",
                    "notes" => "Transaksi Pembelian Token Listrik",
                    "amount" => 2000000,
                    "created_by" => "Siti",
                    "created_at" => "2025-07-01 10:00:00",
                ],
                [
                    "id" => 1,
                    "date" => "2025-07-02",
                    "reference_no" => "250601.JLL.QEA23W",
                    "notes" => "Bayar Keamanan Tahunan",
                    "amount" => 10000000,
                    "created_by" => "Siti",
                    "created_at" => "2025-07-02 10:00:00",
                ],
                [
                    "id" => 1,
                    "date" => "2025-07-02",
                    "reference_no" => "250601.JLL.QEQ23W",
                    "notes" => "Gaji Karyawan Bulan Juni",
                    "amount" => 250000000,
                    "created_by" => "Amar",
                    "created_at" => "2025-07-02 10:00:00",
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.main_transaction.journal.add', []);
    }
}
