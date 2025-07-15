<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CreditMemoController extends Controller
{
    public function index()
    {
        return view('pages.main_transaction.credit_memo.index', []);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "transaction_number" => "CM-2025-001",
                    "contact" => "John",
                    "transaction_type" => "Pengeluaran Kredit Memo",
                    "amount" => 500000,
                    "status" => "Active",
                    "remaining_balance" => 500000,
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "transaction_number" => "CM-2025-002",
                    "contact" => "PT. Maju Jaya",
                    "transaction_type" => "Pengeluaran Kredit Memo",
                    "amount" => 1000000,
                    "status" => "Partially Used",
                    "remaining_balance" => 750000,
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "transaction_number" => "CM-2025-003",
                    "contact" => "CV. Sukses Mandiri",
                    "transaction_type" => "Adjustment",
                    "amount" => 250000,
                    "status" => "Used",
                    "remaining_balance" => 0,
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "transaction_number" => "CM-2025-004",
                    "contact" => "PT. Cahaya Abadi",
                    "transaction_type" => "Return",
                    "amount" => 300000,
                    "status" => "Active",
                    "remaining_balance" => 300000,
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $contacts = [
            ['value' => '1', 'label' => 'John'],
            ['value' => '2', 'label' => 'PT. Maju Jaya'],
            ['value' => '3', 'label' => 'CV. Sukses Mandiri'],
            ['value' => '4', 'label' => 'PT. Cahaya Abadi'],
            ['value' => '5', 'label' => 'CV. Berkah Sentosa']
        ];

        $transactionTypes = [
            ['value' => 'pengeluaran-credit-memo', 'label' => 'Pengeluaran Kredit Memo'],
            ['value' => 'penerimaan-credit-memo', 'label' => 'Penerimaan Kredit Memo'],
            ['value' => 'adjustment', 'label' => 'Adjustment'],
            ['value' => 'return', 'label' => 'Return'],
            ['value' => 'discount', 'label' => 'Discount']
        ];

        $accounts = [
            ['value' => '1', 'label' => '(1-20100) Piutang'],
            ['value' => '2', 'label' => '(2-20100) Hutang'],
            ['value' => '3', 'label' => '(4-40100) Pendapatan Penjualan'],
            ['value' => '4', 'label' => '(5-50100) Beban Operasional'],
            ['value' => '5', 'label' => '(1-11100) Kas dan Bank']
        ];

        return view('pages.main_transaction.credit_memo.add', [
            'contacts' => $contacts,
            'transactionTypes' => $transactionTypes,
            'accounts' => $accounts
        ]);
    }

    public function addWithContact($contact_id = null)
    {
        // Pre-fill contact information when coming from contact detail
        $contact_data = null;
        
        if ($contact_id) {
            // Mock contact data - in real app, fetch from database
            $contact_data = [
                'id' => $contact_id,
                'name' => 'PT Maju Jaya Sentosa',
                'type' => 'Pelanggan'
            ];
        }

        return view('pages.main_transaction.credit_memo.add', [
            'pre_filled_contact' => $contact_data
        ]);
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Tanggal Transaksi"],
            ["no" => 2, "value" => "Nomor Transaksi"],
            ["no" => 3, "value" => "Yang Membayar"],
            ["no" => 4, "value" => "Jenis Transaksi"],
            ["no" => 5, "value" => "Nama Transaksi"],
            ["no" => 6, "value" => "Akun"],
            ["no" => 7, "value" => "Nilai"],
            ["no" => 8, "value" => "Deskripsi"],
            ["no" => 9, "value" => "Tag"],
            ["no" => 10, "value" => "Memo"],
            ["no" => 11, "value" => "Status"],
            ["no" => 12, "value" => "Sisa Saldo"],
        ];
        return view('pages.main_transaction.credit_memo.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.main_transaction.credit_memo.import', []);
    }
}