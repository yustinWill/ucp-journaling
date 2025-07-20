<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        // Get options for filter dropdowns in the list page
        $categories = [
            ['value' => 'EXP-001', 'label' => 'EXP-001 - Operasional Produksi'],
            ['value' => 'EXP-002', 'label' => 'EXP-002 - Marketing & Promosi'],
            ['value' => 'EXP-003', 'label' => 'EXP-003 - Administrasi & Umum'],
            ['value' => 'EXP-004', 'label' => 'EXP-004 - Maintenance & Repair'],
            ['value' => 'EXP-005', 'label' => 'EXP-005 - Distribusi & Logistik']
        ];

        $statuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'SUBMITTED', 'label' => 'SUBMITTED - Diajukan'],
            ['value' => 'APPROVED', 'label' => 'APPROVED - Disetujui'],
            ['value' => 'PAID', 'label' => 'PAID - Dibayar'],
            ['value' => 'REJECTED', 'label' => 'REJECTED - Ditolak']
        ];

        return view('pages.main_transaction.expense.index', [
            'expenseCategories' => $categories,
            'expenseStatuses' => $statuses
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-15",
                    "reference_no" => "EXP-2025-001",
                    "description" => "Pembelian bahan baku plastik untuk kemasan RIYO Water 330ml",
                    "category" => "Operasional Produksi", 
                    "supplier" => "PT. Plastik Prima Indonesia",
                    "status" => "Dibayar",
                    "amount" => 25000000,
                    "created_by" => "Ahmad Rizki",
                    "created_at" => "2025-07-15 09:30:00"
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-14",
                    "reference_no" => "EXP-2025-002",
                    "description" => "Biaya promosi dan iklan RIYO Water di media sosial",
                    "category" => "Marketing & Promosi",
                    "supplier" => "Digital Marketing Agency",
                    "status" => "Disetujui",
                    "amount" => 8500000,
                    "created_by" => "Siti Nurhaliza",
                    "created_at" => "2025-07-14 14:15:00"
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-13",
                    "reference_no" => "EXP-2025-003",
                    "description" => "Biaya listrik dan air untuk fasilitas produksi",
                    "category" => "Operasional Produksi",
                    "supplier" => "PLN & PDAM",
                    "status" => "Dibayar",
                    "amount" => 12000000,
                    "created_by" => "Yustinus Komunal",
                    "created_at" => "2025-07-13 16:45:00"
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-12",
                    "reference_no" => "EXP-2025-004",
                    "description" => "Maintenance mesin filling dan quality control equipment",
                    "category" => "Maintenance & Repair",
                    "supplier" => "PT. Teknisi Ahli Mesin",
                    "status" => "Diajukan",
                    "amount" => 15500000,
                    "created_by" => "Budi Santoso",
                    "created_at" => "2025-07-12 11:20:00"
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-11",
                    "reference_no" => "EXP-2025-005",
                    "description" => "Biaya transportasi distribusi ke wilayah Jabodetabek",
                    "category" => "Distribusi & Logistik",
                    "supplier" => "CV. Ekspedisi Maju",
                    "status" => "Draft",
                    "amount" => 6500000,
                    "created_by" => "Ahmad Rizki",
                    "created_at" => "2025-07-11 08:30:00"
                ],
                [
                    "id" => 6,
                    "date" => "2025-07-10",
                    "reference_no" => "EXP-2025-006",
                    "description" => "Gaji karyawan produksi dan quality assurance",
                    "category" => "Administrasi & Umum",
                    "supplier" => null,
                    "status" => "Ditolak",
                    "amount" => 85000000,
                    "created_by" => "Siti Nurhaliza",
                    "created_at" => "2025-07-10 13:10:00"
                ]
            ]
        ];

        return response()->json($data, 200);
    }

    public function add()
    {
        // Get all required options for the form
        $paymentMethods = [
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'check', 'label' => 'Check'],
            ['value' => 'e_wallet', 'label' => 'E-wallet']
        ];

        $paymentTerms = [
            ['value' => 'net_15', 'label' => 'Net 15'],
            ['value' => 'net_30', 'label' => 'Net 30'],
            ['value' => 'net_45', 'label' => 'Net 45'],
            ['value' => 'net_60', 'label' => 'Net 60'],
            ['value' => 'custom', 'label' => 'Custom']
        ];

        $recipients = [
            ['value' => 'PT. Supplier Utama', 'label' => 'PT. Supplier Utama'],
            ['value' => 'CV. Mitra Jaya', 'label' => 'CV. Mitra Jaya'],
            ['value' => 'PT. Teknologi Maju', 'label' => 'PT. Teknologi Maju'],
            ['value' => 'UD. Berkah Sejahtera', 'label' => 'UD. Berkah Sejahtera'],
            ['value' => 'PT. Global Solutions', 'label' => 'PT. Global Solutions']
        ];

        $accounts = [
            ['value' => '5101', 'label' => '5101 - Biaya Operasional'],
            ['value' => '5201', 'label' => '5201 - Biaya Marketing'],
            ['value' => '5301', 'label' => '5301 - Biaya Administrasi'],
            ['value' => '5401', 'label' => '5401 - Biaya IT & Teknologi'],
            ['value' => '5501', 'label' => '5501 - Biaya SDM']
        ];

        return view('pages.main_transaction.expense.add', [
            'paymentMethods' => $paymentMethods,
            'paymentTerms' => $paymentTerms,
            'recipients' => $recipients,
            'accounts' => $accounts
        ]);
    }

    public function import()
    {
        return view('pages.main_transaction.expense.import', []);
    }

    public function export()
    {
        $exportColumns = [
            ['field' => 'date', 'label' => 'Tanggal', 'export_name' => 'Tanggal'],
            ['field' => 'reference_no', 'label' => 'No Referensi', 'export_name' => 'No Referensi'],
            ['field' => 'description', 'label' => 'Keterangan', 'export_name' => 'Keterangan'],
            ['field' => 'category', 'label' => 'Kategori', 'export_name' => 'Kategori'],
            ['field' => 'supplier', 'label' => 'Supplier/Vendor', 'export_name' => 'Supplier/Vendor'],
            ['field' => 'status', 'label' => 'Status', 'export_name' => 'Status'],
            ['field' => 'amount', 'label' => 'Nominal', 'export_name' => 'Nominal'],
            ['field' => 'created_by', 'label' => 'Dibuat Oleh', 'export_name' => 'Dibuat Oleh']
        ];

        return view('pages.main_transaction.expense.export', [
            'exportColumns' => $exportColumns
        ]);
    }

    public function edit($id)
    {
        // Get all required options for the form (same as add form)
        $paymentMethods = [
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'check', 'label' => 'Check'],
            ['value' => 'e_wallet', 'label' => 'E-wallet']
        ];

        $paymentTerms = [
            ['value' => 'net_15', 'label' => 'Net 15'],
            ['value' => 'net_30', 'label' => 'Net 30'],
            ['value' => 'net_45', 'label' => 'Net 45'],
            ['value' => 'net_60', 'label' => 'Net 60'],
            ['value' => 'custom', 'label' => 'Custom']
        ];

        $recipients = [
            ['value' => 'PT. Supplier Utama', 'label' => 'PT. Supplier Utama'],
            ['value' => 'CV. Mitra Jaya', 'label' => 'CV. Mitra Jaya'],
            ['value' => 'PT. Teknologi Maju', 'label' => 'PT. Teknologi Maju'],
            ['value' => 'UD. Berkah Sejahtera', 'label' => 'UD. Berkah Sejahtera'],
            ['value' => 'PT. Global Solutions', 'label' => 'PT. Global Solutions']
        ];

        $accounts = [
            ['value' => '5101', 'label' => '5101 - Biaya Operasional'],
            ['value' => '5201', 'label' => '5201 - Biaya Marketing'],
            ['value' => '5301', 'label' => '5301 - Biaya Administrasi'],
            ['value' => '5401', 'label' => '5401 - Biaya IT & Teknologi'],
            ['value' => '5501', 'label' => '5501 - Biaya SDM']
        ];

        return view('pages.main_transaction.expense.edit', [
            'id' => $id,
            'paymentMethods' => $paymentMethods,
            'paymentTerms' => $paymentTerms,
            'recipients' => $recipients,
            'accounts' => $accounts
        ]);
    }

    public function detail($id)
    {
        return view('pages.main_transaction.expense.detail', [
            'id' => $id
        ]);
    }

    public function categories(Request $request)
    {
        $search = $request->get('search', '');
        
        $categories = [
            ['value' => 'Operasional', 'label' => 'Operasional'],
            ['value' => 'Marketing', 'label' => 'Marketing'],
            ['value' => 'Administrasi', 'label' => 'Administrasi'],
            ['value' => 'IT', 'label' => 'IT & Technology'],
            ['value' => 'HRD', 'label' => 'Human Resources'],
            ['value' => 'Legal', 'label' => 'Legal & Compliance'],
            ['value' => 'Finance', 'label' => 'Finance & Accounting']
        ];

        if ($search) {
            $categories = array_filter($categories, function($category) use ($search) {
                return stripos($category['label'], $search) !== false;
            });
        }

        return response()->json(array_values($categories));
    }

    public function statuses(Request $request)
    {
        $search = $request->get('search', '');
        
        $statuses = [
            ['value' => 'Draft', 'label' => 'Draft'],
            ['value' => 'Diajukan', 'label' => 'Diajukan'],
            ['value' => 'Disetujui', 'label' => 'Disetujui'],
            ['value' => 'Dibayar', 'label' => 'Dibayar'],
            ['value' => 'Ditolak', 'label' => 'Ditolak'],
            ['value' => 'Dibatalkan', 'label' => 'Dibatalkan']
        ];

        if ($search) {
            $statuses = array_filter($statuses, function($status) use ($search) {
                return stripos($status['label'], $search) !== false;
            });
        }

        return response()->json(array_values($statuses));
    }

    public function recipients(Request $request)
    {
        $search = $request->get('search', '');
        
        $recipients = [
            ['value' => 'PT. Supplier Utama', 'label' => 'PT. Supplier Utama'],
            ['value' => 'CV. Mitra Jaya', 'label' => 'CV. Mitra Jaya'],
            ['value' => 'PT. Teknologi Maju', 'label' => 'PT. Teknologi Maju'],
            ['value' => 'UD. Berkah Sejahtera', 'label' => 'UD. Berkah Sejahtera'],
            ['value' => 'PT. Global Solutions', 'label' => 'PT. Global Solutions'],
            ['value' => 'CV. Prima Service', 'label' => 'CV. Prima Service'],
            ['value' => 'PT. Mandiri Elektronik', 'label' => 'PT. Mandiri Elektronik'],
            ['value' => 'Toko Alat Kantor', 'label' => 'Toko Alat Kantor']
        ];

        if ($search) {
            $recipients = array_filter($recipients, function($recipient) use ($search) {
                return stripos($recipient['label'], $search) !== false;
            });
        }

        return response()->json(array_values($recipients));
    }

    public function accounts(Request $request)
    {
        $search = $request->get('search', '');
        
        $accounts = [
            ['value' => '5101', 'label' => '5101 - Biaya Operasional'],
            ['value' => '5201', 'label' => '5201 - Biaya Marketing'],
            ['value' => '5301', 'label' => '5301 - Biaya Administrasi'],
            ['value' => '5401', 'label' => '5401 - Biaya IT & Teknologi'],
            ['value' => '5501', 'label' => '5501 - Biaya SDM'],
            ['value' => '5601', 'label' => '5601 - Biaya Legal & Compliance'],
            ['value' => '5701', 'label' => '5701 - Biaya Keuangan'],
            ['value' => '5801', 'label' => '5801 - Biaya Perjalanan Dinas'],
            ['value' => '5901', 'label' => '5901 - Biaya Konsumsi'],
            ['value' => '6001', 'label' => '6001 - Biaya Utilitas'],
            ['value' => '6101', 'label' => '6101 - Biaya Pemeliharaan'],
            ['value' => '6201', 'label' => '6201 - Biaya Sewa']
        ];

        if ($search) {
            $accounts = array_filter($accounts, function($account) use ($search) {
                return stripos($account['label'], $search) !== false;
            });
        }

        return response()->json(array_values($accounts));
    }

    public function paymentMethods(Request $request)
    {
        $search = $request->get('search', '');
        
        $paymentMethods = [
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'check', 'label' => 'Check'],
            ['value' => 'e_wallet', 'label' => 'E-wallet']
        ];

        if ($search) {
            $paymentMethods = array_filter($paymentMethods, function($method) use ($search) {
                return stripos($method['label'], $search) !== false;
            });
        }

        return response()->json(array_values($paymentMethods));
    }

    public function paymentTerms(Request $request)
    {
        $search = $request->get('search', '');
        
        $paymentTerms = [
            ['value' => 'net_15', 'label' => 'Net 15'],
            ['value' => 'net_30', 'label' => 'Net 30'],
            ['value' => 'net_45', 'label' => 'Net 45'],
            ['value' => 'net_60', 'label' => 'Net 60'],
            ['value' => 'custom', 'label' => 'Custom']
        ];

        if ($search) {
            $paymentTerms = array_filter($paymentTerms, function($term) use ($search) {
                return stripos($term['label'], $search) !== false;
            });
        }

        return response()->json(array_values($paymentTerms));
    }
}