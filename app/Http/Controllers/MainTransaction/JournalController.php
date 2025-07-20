<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index()
    {
        $createdByUsers = [
            ['value' => 'USR-001', 'label' => 'USR-001 - Yustinus Komunal (Finance Manager)'],
            ['value' => 'USR-002', 'label' => 'USR-002 - Ahmad Rizki (Accounting Staff)'],
            ['value' => 'USR-003', 'label' => 'USR-003 - Siti Nurhaliza (Admin Keuangan)']
        ];

        return view('pages.main_transaction.journal.index', [
            'createdByUsers' => $createdByUsers
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-01",
                    "reference_no" => "JE-2025-001",
                    "notes" => "Penjualan RIYO Water 330ml - CV Berkah Sentosa",
                    "tags" => ["PENJUALAN", "KEUANGAN"],
                    "amount" => 12500000,
                    "created_by" => "Yustinus Komunal",
                    "created_at" => "2025-07-01 08:30:00",
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-02",
                    "reference_no" => "JE-2025-002",
                    "notes" => "Pembelian bahan baku plastik untuk kemasan",
                    "tags" => ["PEMBELIAN", "OPERASIONAL"],
                    "amount" => 25000000,
                    "created_by" => "Ahmad Rizki",
                    "created_at" => "2025-07-02 09:15:00",
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-03",
                    "reference_no" => "JE-2025-003",
                    "notes" => "Pembayaran gaji karyawan produksi bulan Juli",
                    "tags" => ["OPERASIONAL"],
                    "amount" => 85000000,
                    "created_by" => "Siti Nurhaliza",
                    "created_at" => "2025-07-03 14:20:00",
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-04",
                    "reference_no" => "JE-2025-004",
                    "notes" => "Biaya maintenance mesin produksi dan quality control",
                    "tags" => ["OPERASIONAL", "INVESTASI"],
                    "amount" => 15500000,
                    "created_by" => "Ahmad Rizki",
                    "created_at" => "2025-07-04 11:45:00",
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-05",
                    "reference_no" => "JE-2025-005",
                    "notes" => "Penerimaan pembayaran dari Supermarket Maju Jaya",
                    "tags" => ["PENJUALAN", "KEUANGAN"],
                    "amount" => 22500000,
                    "created_by" => "Yustinus Komunal",
                    "created_at" => "2025-07-05 16:10:00",
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        // Sample accounts for select options with code-name format
        $accounts = [
            ['code' => '1101', 'name' => 'Kas', 'label' => '1101 - Kas'],
            ['code' => '1102', 'name' => 'Bank BCA', 'label' => '1102 - Bank BCA'],
            ['code' => '1201', 'name' => 'Piutang Dagang', 'label' => '1201 - Piutang Dagang'],
            ['code' => '1301', 'name' => 'Persediaan Barang Dagang', 'label' => '1301 - Persediaan Barang Dagang'],
            ['code' => '4001', 'name' => 'Penjualan Barang Dagang', 'label' => '4001 - Penjualan Barang Dagang'],
            ['code' => '5001', 'name' => 'Pembelian Barang Dagang', 'label' => '5001 - Pembelian Barang Dagang'],
            ['code' => '6001', 'name' => 'Biaya Operasional', 'label' => '6001 - Biaya Operasional']
        ];

        // Sample tags loaded from master data with dynamic colors
        $availableTags = [
            ['code' => 'PENJUALAN', 'name' => 'Transaksi Penjualan', 'label' => 'PENJUALAN - Transaksi Penjualan', 'color' => 'success'],
            ['code' => 'PEMBELIAN', 'name' => 'Transaksi Pembelian', 'label' => 'PEMBELIAN - Transaksi Pembelian', 'color' => 'info'],
            ['code' => 'OPERASIONAL', 'name' => 'Biaya Operasional', 'label' => 'OPERASIONAL - Biaya Operasional', 'color' => 'warning'],
            ['code' => 'INVESTASI', 'name' => 'Investasi & Aset', 'label' => 'INVESTASI - Investasi & Aset', 'color' => 'primary'],
            ['code' => 'KEUANGAN', 'name' => 'Transaksi Keuangan', 'label' => 'KEUANGAN - Transaksi Keuangan', 'color' => 'secondary'],
            ['code' => 'PAJAK', 'name' => 'Transaksi Pajak', 'label' => 'PAJAK - Transaksi Pajak', 'color' => 'danger'],
            ['code' => 'KOREKSI', 'name' => 'Koreksi & Penyesuaian', 'label' => 'KOREKSI - Koreksi & Penyesuaian', 'color' => 'dark']
        ];

        // Permission check for adding new tags (mock from user session/role)
        $canAddTags = true; // Mock: In real app, check user permissions

        return view('pages.main_transaction.journal.add', compact('accounts', 'availableTags', 'canAddTags'));
    }

    public function import()
    {
        return view('pages.main_transaction.journal.import', []);
    }

    public function export()
    {
        $exportColumns = [
            ['field' => 'date', 'label' => 'Tanggal Jurnal', 'export_name' => 'Tanggal Jurnal'],
            ['field' => 'reference_no', 'label' => 'No Referensi', 'export_name' => 'No Referensi'],
            ['field' => 'notes', 'label' => 'Keterangan', 'export_name' => 'Keterangan'],
            ['field' => 'amount', 'label' => 'Nominal', 'export_name' => 'Nominal'],
            ['field' => 'created_by', 'label' => 'Dibuat Oleh', 'export_name' => 'Dibuat Oleh'],
            ['field' => 'created_at', 'label' => 'Tanggal Input', 'export_name' => 'Tanggal Input']
        ];

        return view('pages.main_transaction.journal.export', [
            'exportColumns' => $exportColumns
        ]);
    }

    public function detail($id)
    {
        // Sample journal data for the detail view with entries and audit logs
        $journal = [
            'id' => $id,
            'reference_no' => 'JU-2025-001',
            'journal_date' => '2025-01-20',
            'status' => 'ACTIVE',
            'total_amount' => 'Rp 15,500,000',
            'description' => 'Penerimaan kas dari penjualan produk air mineral RIYO periode Januari 2025. Transaksi ini mencatat penerimaan pembayaran dari distributor untuk pembelian 3,000 botol air mineral.',
            'memo' => 'Verifikasi pembayaran telah dilakukan oleh tim finance. Dokumen pendukung tersimpan di folder digital Q1-2025.',
            'tags' => [
                ['code' => 'PENJUALAN', 'name' => 'Transaksi Penjualan', 'color' => 'success'],
                ['code' => 'KEUANGAN', 'name' => 'Transaksi Keuangan', 'color' => 'secondary']
            ],
            'total_debit' => 'Rp 15,500,000',
            'total_credit' => 'Rp 15,500,000',
            'difference' => 'Rp 0',
            'entries' => [
                [
                    'account_code' => '1101',
                    'account_name' => 'Kas',
                    'description' => 'Penerimaan kas dari penjualan',
                    'debit_amount' => 'Rp 15,500,000',
                    'credit_amount' => null
                ],
                [
                    'account_code' => '4001',
                    'account_name' => 'Penjualan Barang Dagang',
                    'description' => 'Pendapatan dari penjualan air mineral',
                    'debit_amount' => null,
                    'credit_amount' => 'Rp 15,500,000'
                ]
            ],
            'audit_logs' => [
                [
                    'created_at' => '2025-01-20 15:45:12',
                    'user_id' => 'USR-002',
                    'user_name' => 'Finance Manager',
                    'action_type' => 'UPDATE',
                    'description' => 'Mengubah status jurnal dari DRAFT menjadi POSTED'
                ],
                [
                    'created_at' => '2025-01-20 14:35:20',
                    'user_id' => 'USR-001',
                    'user_name' => 'Admin User',
                    'action_type' => 'UPDATE',
                    'description' => 'Memperbarui deskripsi jurnal dengan detail lengkap periode Januari 2025'
                ],
                [
                    'created_at' => '2025-01-20 14:30:25',
                    'user_id' => 'USR-001',
                    'user_name' => 'Admin User',
                    'action_type' => 'CREATE',
                    'description' => 'Membuat jurnal baru untuk penerimaan kas penjualan RIYO Water'
                ]
            ]
        ];

        return view('pages.main_transaction.journal.detail', compact('journal'));
    }

    public function edit($id)
    {
        // Sample journal data for editing
        $journal = [
            'id' => $id,
            'reference_no' => 'JU-2025-001',
            'journal_date' => '2025-01-20',
            'status' => 'ACTIVE',
            'total_amount' => 'Rp 15,500,000',
            'description' => 'Penerimaan kas dari penjualan produk air mineral RIYO periode Januari 2025. Transaksi ini mencatat penerimaan pembayaran dari distributor untuk pembelian 3,000 botol air mineral.',
            'memo' => 'Verifikasi pembayaran telah dilakukan oleh tim finance. Dokumen pendukung tersimpan di folder digital Q1-2025.',
            'tags' => ['PENJUALAN', 'KEUANGAN'], // Keep as codes for form selection
            'total_debit' => 'Rp 15,500,000',
            'total_credit' => 'Rp 15,500,000',
            'difference' => 'Rp 0',
            'entries' => [
                [
                    'id' => 1,
                    'account_code' => '1101',
                    'account_name' => 'Kas',
                    'description' => 'Penerimaan kas dari penjualan',
                    'debit_amount' => 'Rp 15,500,000',
                    'credit_amount' => ''
                ],
                [
                    'id' => 2,
                    'account_code' => '4001',
                    'account_name' => 'Penjualan Barang Dagang',
                    'description' => 'Pendapatan dari penjualan air mineral',
                    'debit_amount' => '',
                    'credit_amount' => 'Rp 15,500,000'
                ]
            ]
        ];

        // Sample accounts for select options with code-name format
        $accounts = [
            ['code' => '1101', 'name' => 'Kas', 'label' => '1101 - Kas'],
            ['code' => '1102', 'name' => 'Bank BCA', 'label' => '1102 - Bank BCA'],
            ['code' => '1201', 'name' => 'Piutang Dagang', 'label' => '1201 - Piutang Dagang'],
            ['code' => '1301', 'name' => 'Persediaan Barang Dagang', 'label' => '1301 - Persediaan Barang Dagang'],
            ['code' => '4001', 'name' => 'Penjualan Barang Dagang', 'label' => '4001 - Penjualan Barang Dagang'],
            ['code' => '5001', 'name' => 'Pembelian Barang Dagang', 'label' => '5001 - Pembelian Barang Dagang'],
            ['code' => '6001', 'name' => 'Biaya Operasional', 'label' => '6001 - Biaya Operasional']
        ];

        // Sample tags loaded from master data with dynamic colors
        $availableTags = [
            ['code' => 'PENJUALAN', 'name' => 'Transaksi Penjualan', 'label' => 'PENJUALAN - Transaksi Penjualan', 'color' => 'success'],
            ['code' => 'PEMBELIAN', 'name' => 'Transaksi Pembelian', 'label' => 'PEMBELIAN - Transaksi Pembelian', 'color' => 'info'],
            ['code' => 'OPERASIONAL', 'name' => 'Biaya Operasional', 'label' => 'OPERASIONAL - Biaya Operasional', 'color' => 'warning'],
            ['code' => 'INVESTASI', 'name' => 'Investasi & Aset', 'label' => 'INVESTASI - Investasi & Aset', 'color' => 'primary'],
            ['code' => 'KEUANGAN', 'name' => 'Transaksi Keuangan', 'label' => 'KEUANGAN - Transaksi Keuangan', 'color' => 'secondary'],
            ['code' => 'PAJAK', 'name' => 'Transaksi Pajak', 'label' => 'PAJAK - Transaksi Pajak', 'color' => 'danger'],
            ['code' => 'KOREKSI', 'name' => 'Koreksi & Penyesuaian', 'label' => 'KOREKSI - Koreksi & Penyesuaian', 'color' => 'dark']
        ];

        // Permission check for adding new tags (mock from user session/role)
        $canAddTags = true; // Mock: In real app, check user permissions

        return view('pages.main_transaction.journal.edit', compact('journal', 'accounts', 'availableTags', 'canAddTags'));
    }
}
