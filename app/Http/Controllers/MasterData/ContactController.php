<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $selectData = $this->getIndexSelectData();
        return view('pages.master_data.contact.index', $selectData);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "contact_name" => "Ahmad Wijaya",
                    "type" => "Pelanggan",
                    "company_name" => "Warung Segar Bogor",
                    "phone" => "0812-3456-7890",
                    "email" => "ahmad.wijaya@warungs.com",
                    "group_text" => "Customer Warung",
                ],
                [
                    "id" => 2,
                    "contact_name" => "Siti Rahma",
                    "type" => "Pelanggan",
                    "company_name" => "Minimarket Berkah Jaya",
                    "phone" => "0857-1234-5678",
                    "email" => "siti.rahma@berkahj.com",
                    "group_text" => "Customer Retail",
                ],
                [
                    "id" => 3,
                    "contact_name" => "Budi Santoso",
                    "type" => "Supplier",
                    "company_name" => "PT. Plastik Prima Indonesia",
                    "phone" => "021-8765-4321",
                    "email" => "budi.santoso@plastikprima.co.id",
                    "group_text" => "Supplier Kemasan",
                ],
                [
                    "id" => 4,
                    "contact_name" => "Maria Susanti",
                    "type" => "Supplier",
                    "company_name" => "CV. Teknisi Ahli Mesin",
                    "phone" => "0274-987-6543",
                    "email" => "maria.susanti@teknisiahli.com",
                    "group_text" => "Supplier Maintenance",
                ],
                [
                    "id" => 5,
                    "contact_name" => "Rahmat Hidayat",
                    "type" => "Supplier",
                    "company_name" => "CV. Ekspedisi Maju",
                    "phone" => "0813-2468-1357",
                    "email" => "rahmat.hidayat@ekspedisimaju.id",
                    "group_text" => "Supplier Logistik",
                ],
                [
                    "id" => 6,
                    "contact_name" => "Dewi Kartika",
                    "type" => "Karyawan",
                    "company_name" => "RIYO Water",
                    "phone" => "0896-5432-1098",
                    "email" => "dewi.kartika@riyowater.com",
                    "group_text" => "Quality Control",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $selectData = [
            'identity_types' => [
                ['value' => 'KTP', 'label' => 'KTP'],
                ['value' => 'SIM', 'label' => 'SIM'],
                ['value' => 'Passport', 'label' => 'Passport'],
                ['value' => 'NPWP', 'label' => 'NPWP']
            ],
            'sales_users' => [
                ['value' => 'sales_a', 'label' => 'Sales A'],
                ['value' => 'sales_b', 'label' => 'Sales B'],
                ['value' => 'sales_c', 'label' => 'Sales C'],
                ['value' => 'sales_d', 'label' => 'Sales D']
            ],
            'payable_accounts' => [
                ['value' => '2-20100', 'label' => '(2-20100) Hutang'],
                ['value' => '2-20200', 'label' => '(2-20200) Hutang Jangka Panjang'],
                ['value' => '2-20300', 'label' => '(2-20300) Hutang Bank'],
                ['value' => '2-20400', 'label' => '(2-20400) Hutang Pajak']
            ],
            'receivable_accounts' => [
                ['value' => '1-20100', 'label' => '(1-20100) Piutang'],
                ['value' => '1-20200', 'label' => '(1-20200) Piutang Jangka Panjang'],
                ['value' => '1-20300', 'label' => '(1-20300) Piutang Lain-lain'],
                ['value' => '1-20400', 'label' => '(1-20400) Piutang Karyawan']
            ],
            'payment_terms' => [
                ['value' => 'Net 15', 'label' => 'Net 15'],
                ['value' => 'Net 30', 'label' => 'Net 30'],
                ['value' => 'Net 45', 'label' => 'Net 45'],
                ['value' => 'Net 60', 'label' => 'Net 60'],
                ['value' => 'COD', 'label' => 'Cash on Delivery'],
                ['value' => 'Immediate', 'label' => 'Immediate']
            ],
            'banks' => [
                ['value' => 'BCA', 'label' => 'Bank Central Asia (BCA)'],
                ['value' => 'BRI', 'label' => 'Bank Rakyat Indonesia (BRI)'],
                ['value' => 'MANDIRI', 'label' => 'Bank Mandiri'],
                ['value' => 'BNI', 'label' => 'Bank Negara Indonesia (BNI)'],
                ['value' => 'CIMB', 'label' => 'CIMB Niaga'],
                ['value' => 'MEGA', 'label' => 'Bank Mega']
            ],
            'products' => [
                ['value' => 'prod_a', 'label' => 'Produk A'],
                ['value' => 'prod_b', 'label' => 'Produk B'],
                ['value' => 'prod_c', 'label' => 'Produk C'],
                ['value' => 'prod_d', 'label' => 'Produk D'],
                ['value' => 'prod_e', 'label' => 'Produk E']
            ]
        ];
        
        return view('pages.master_data.contact.add', $selectData);
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
        
        $selectData = [
            'contact_groups' => [
                ['value' => 'vip', 'label' => 'VIP Customer'],
                ['value' => 'regular', 'label' => 'Regular Customer'],
                ['value' => 'wholesale', 'label' => 'Wholesale'],
                ['value' => 'retail', 'label' => 'Retail'],
                ['value' => 'premium', 'label' => 'Premium']
            ],
            'banks' => [
                ['value' => 'BCA', 'label' => 'Bank Central Asia (BCA)'],
                ['value' => 'BRI', 'label' => 'Bank Rakyat Indonesia (BRI)'],
                ['value' => 'MANDIRI', 'label' => 'Bank Mandiri'],
                ['value' => 'BNI', 'label' => 'Bank Negara Indonesia (BNI)'],
                ['value' => 'CIMB', 'label' => 'CIMB Niaga'],
                ['value' => 'MEGA', 'label' => 'Bank Mega']
            ],
            'payable_accounts' => [
                ['value' => '2-20100', 'label' => '(2-20100) Hutang'],
                ['value' => '2-20200', 'label' => '(2-20200) Hutang Jangka Panjang'],
                ['value' => '2-20300', 'label' => '(2-20300) Hutang Bank'],
                ['value' => '2-20400', 'label' => '(2-20400) Hutang Pajak']
            ],
            'receivable_accounts' => [
                ['value' => '1-20100', 'label' => '(1-20100) Piutang'],
                ['value' => '1-20200', 'label' => '(1-20200) Piutang Jangka Panjang'],
                ['value' => '1-20300', 'label' => '(1-20300) Piutang Lain-lain'],
                ['value' => '1-20400', 'label' => '(1-20400) Piutang Karyawan']
            ],
            'payment_terms' => [
                ['value' => 'Net 15', 'label' => 'Net 15'],
                ['value' => 'Net 30', 'label' => 'Net 30'],
                ['value' => 'Net 45', 'label' => 'Net 45'],
                ['value' => 'Net 60', 'label' => 'Net 60'],
                ['value' => 'COD', 'label' => 'Cash on Delivery'],
                ['value' => 'Immediate', 'label' => 'Immediate']
            ]
        ];

        return view('pages.master_data.contact.edit', compact('contact') + $selectData);
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

    private function getIndexSelectData()
    {
        return [
            'groupFilters' => [
                ['value' => 'CUSTOMER_WARUNG', 'label' => 'CUSTOMER_WARUNG - Customer Warung'],
                ['value' => 'CUSTOMER_RETAIL', 'label' => 'CUSTOMER_RETAIL - Customer Retail'],
                ['value' => 'CUSTOMER_WHOLESALE', 'label' => 'CUSTOMER_WHOLESALE - Customer Grosir'],
                ['value' => 'SUPPLIER_KEMASAN', 'label' => 'SUPPLIER_KEMASAN - Supplier Kemasan'],
                ['value' => 'SUPPLIER_MAINTENANCE', 'label' => 'SUPPLIER_MAINTENANCE - Supplier Maintenance'],
                ['value' => 'SUPPLIER_LOGISTIK', 'label' => 'SUPPLIER_LOGISTIK - Supplier Logistik'],
            ],
            'typeFilters' => [
                ['value' => 'PELANGGAN', 'label' => 'PELANGGAN - Pelanggan'],
                ['value' => 'SUPPLIER', 'label' => 'SUPPLIER - Supplier'],
                ['value' => 'KARYAWAN', 'label' => 'KARYAWAN - Karyawan'],
                ['value' => 'LAINNYA', 'label' => 'LAINNYA - Lainnya'],
            ]
        ];
    }
}
