<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.master_data.contact.index', []);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "contact_name" => "Jonathan",
                    "type" => "Pelanggan",
                    "company_name" => "-",
                    "phone" => "08123456789",
                    "email" => "jonathan@mail.com",
                    "group_text" => "Group A",
                ],
                [
                    "id" => 2,
                    "contact_name" => "William",
                    "type" => "Pelanggan",
                    "company_name" => "-",
                    "phone" => "08123456789",
                    "email" => "william@mail.com",
                    "group_text" => "Group A",
                ],
                [
                    "id" => 3,
                    "contact_name" => "Yustinus",
                    "type" => "Supplier",
                    "company_name" => "CV. Megah Sentosa",
                    "phone" => "08123456789",
                    "email" => "cv_megah@mail.com",
                    "group_text" => "Group B",
                ],
                [
                    "id" => 4,
                    "contact_name" => "Ivana",
                    "type" => "Supplier",
                    "company_name" => "CV. Matahari Indah",
                    "phone" => "08123456789",
                    "email" => "cv_indah@mail.com",
                    "group_text" => "Group B",
                ],
                [
                    "id" => 5,
                    "contact_name" => "Yustinus",
                    "type" => "Supplier",
                    "company_name" => "CV. Megah Sentosa",
                    "phone" => "08123456789",
                    "email" => "cv_megah@mail.com",
                    "group_text" => "Group B",
                ],
                [
                    "id" => 6,
                    "contact_name" => "Ivanka",
                    "type" => "Lainnya",
                    "company_name" => "CV. ABC",
                    "phone" => "08123456789",
                    "email" => "cv_abc@mail.com",
                    "group_text" => "Group B",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.master_data.contact.add', []);
    }

    public function edit($id)
    {
        // Mock contact data - in real app, fetch from database
        $contact = [
            'id' => $id,
            'contact_name' => 'Anwar',
            'company_name' => 'PT Maju Jaya Sentosa',
            'type' => 'Pelanggan',
            'group' => 'VIP Customer',
            'email' => 'pelanggan1@example.com',
            'phone' => '081234567890',
            'landline' => '021-12345678',
            'fax' => '021-12345679',
            'identity_type' => 'KTP',
            'identity_number' => '3175091203940000',
            'npwp' => '01.234.567.8-999.000',
            'billing_address' => 'Jl. Sudirman No. 123, Jakarta Pusat',
            'shipping_address' => 'Ciomas, Bogor',
            'bank_name' => 'Bank Central Asia',
            'bank_branch' => 'Bogor',
            'account_holder' => 'Anwar',
            'account_number' => '541-002-7891',
            'debt_account' => '(2-20100) Hutang',
            'receivable_account' => '(1-20100) Piutang',
            'payment_terms' => 'Net 30'
        ];

        return view('pages.master_data.contact.edit', ['contact' => $contact]);
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Nama Kontak"],
            ["no" => 2, "value" => "Tipe Kontak"],
            ["no" => 3, "value" => "Status Kontak"],
            ["no" => 4, "value" => "Jenis Identitas"],
            ["no" => 5, "value" => "No Identitas"],
            ["no" => 6, "value" => "Email"],
            ["no" => 7, "value" => "No Telepon"],
            ["no" => 8, "value" => "Nama Perusahaan"],
            ["no" => 9, "value" => "No NPWP"],
            ["no" => 10, "value" => "No Telepon Perusahaan"],
            ["no" => 11, "value" => "No Fax"],
            ["no" => 12, "value" => "Alamat Penagihan - Provinsi"],
            ["no" => 13, "value" => "Alamat Penagihan - Kota"],
            ["no" => 14, "value" => "Alamat Penagihan - Kecamatan"],
            ["no" => 15, "value" => "Alamat Penagihan - Kelurahan"],
            ["no" => 16, "value" => "Alamat Pengriman - Provinsi"],
            ["no" => 17, "value" => "Alamat Pengriman - Kota"],
            ["no" => 18, "value" => "Alamat Pengriman - Kecamatan"],
            ["no" => 19, "value" => "Alamat Pengriman - Kelurahan"],
            ["no" => 20, "value" => "Akun Hutang"],
            ["no" => 21, "value" => "Akun Piutang"],
            ["no" => 22, "value" => "Syarat Pembayaran"],
        ];
        return view('pages.master_data.contact.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.contact.import', []);
    }

    public function detail($id)
    {
        // Mock contact data - in real app, fetch from database
        $contact = [
            'id' => $id,
            'contact_name' => 'Anwar',
            'company_name' => 'PT Maju Jaya Sentosa',
            'type' => 'Pelanggan',
            'group' => '',
            'email' => 'pelanggan1@example.com',
            'phone' => '081234567890',
            'identity_type' => 'KTP',
            'identity_number' => '3175091203940000.00',
            'billing_address' => '',
            'shipping_address' => 'Ciomas, Bogor',
            'other_info' => '-',
            'landline' => '',
            'fax' => '',
            'npwp' => '01.234.567.8-999.000',
            'bank_name' => 'Bank Central Asia',
            'bank_branch' => 'Bogor',
            'account_holder' => 'Anwar',
            'account_number' => '541-002-7891',
            'debt_account' => '(2-20100) Hutang',
            'receivable_account' => '(1-20100) Piutang',
            'payment_terms' => ''
        ];

        return view('pages.master_data.contact.detail', ['contact' => $contact]);
    }

    public function getProfilData($id)
    {
        // Return the same contact data for profile tab
        $contact = [
            'id' => $id,
            'contact_name' => 'Anwar',
            'company_name' => 'PT Maju Jaya Sentosa',
            'type' => 'Pelanggan',
            'group' => '',
            'email' => 'pelanggan1@example.com',
            'phone' => '081234567890',
            'identity_type' => 'KTP',
            'identity_number' => '3175091203940000.00',
            'billing_address' => '',
            'shipping_address' => 'Ciomas, Bogor',
            'other_info' => '-',
            'landline' => '',
            'fax' => '',
            'npwp' => '01.234.567.8-999.000',
            'bank_name' => 'Bank Central Asia',
            'bank_branch' => 'Bogor',
            'account_holder' => 'Anwar',
            'account_number' => '541-002-7891',
            'debt_account' => '(2-20100) Hutang',
            'receivable_account' => '(1-20100) Piutang',
            'payment_terms' => ''
        ];

        return response()->json($contact);
    }

    public function getTransactionData($id)
    {
        $data = [
            'summary' => [
                'monthly_invoiced' => 5,
                'total_amount' => 5,
                'delivery_receivables' => 0
            ],
            'transactions' => [
                [
                    'date' => '12/12/2024',
                    'transaction_number' => 'Sales Invoice #100002',
                    'due_date' => '2/12/2024',
                    'status' => 'Paid',
                    'total' => 'Rp 5,000,000.00'
                ],
                [
                    'date' => '8/12/2024',
                    'transaction_number' => 'Purchase Order #10001',
                    'due_date' => '3/1/2025',
                    'status' => 'Open',
                    'total' => 'Rp 3,000,000.00'
                ]
            ]
        ];

        return response()->json($data);
    }

    public function getCreditMemoData($id)
    {
        $data = [
            'credit_memos' => [
                [
                    'date' => '5/5/2024',
                    'credit_memo_number' => 'Credit Memo #100001',
                    'original_amount' => 'Rp 5,000,000.00',
                    'used_amount' => 'Rp 500,000.00',
                    'remaining_balance' => 'Rp 4,500,000.00',
                    'selected' => true
                ],
                [
                    'date' => '5/5/2024',
                    'credit_memo_number' => 'Credit Memo #100002',
                    'original_amount' => 'Rp 4,000,000.00',
                    'used_amount' => 'Rp 4,000,000.00',
                    'remaining_balance' => 'Rp -',
                    'selected' => false
                ],
                [
                    'date' => '5/5/2024',
                    'credit_memo_number' => 'Credit Memo #100003',
                    'original_amount' => 'Rp 500,000.00',
                    'used_amount' => 'Rp 500,000.00',
                    'remaining_balance' => 'Rp -',
                    'selected' => false
                ]
            ]
        ];

        return response()->json($data);
    }
}
