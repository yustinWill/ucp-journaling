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
            ['value' => 'Operasional', 'label' => 'Operasional'],
            ['value' => 'Marketing', 'label' => 'Marketing'],
            ['value' => 'Administrasi', 'label' => 'Administrasi'],
            ['value' => 'IT', 'label' => 'IT & Technology'],
            ['value' => 'HRD', 'label' => 'Human Resources']
        ];

        $statuses = [
            ['value' => 'Draft', 'label' => 'Draft'],
            ['value' => 'Diajukan', 'label' => 'Diajukan'],
            ['value' => 'Disetujui', 'label' => 'Disetujui'],
            ['value' => 'Dibayar', 'label' => 'Dibayar'],
            ['value' => 'Ditolak', 'label' => 'Ditolak']
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
                    "date" => "2025-07-10",
                    "reference_no" => "EXP-2025-001",
                    "description" => "Biaya ATK Kantor",
                    "category" => "Operasional", 
                    "supplier" => "Toko Gramedia",
                    "status" => "Dibayar",
                    "amount" => 750000,
                    "created_by" => "Admin",
                    "created_at" => "2025-07-10 09:30:00"
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-09",
                    "reference_no" => "EXP-2025-002",
                    "description" => "Biaya Iklan Facebook Ads",
                    "category" => "Marketing",
                    "supplier" => "Meta Platforms",
                    "status" => "Disetujui",
                    "amount" => 2500000,
                    "created_by" => "Marketing",
                    "created_at" => "2025-07-09 14:15:00"
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-08",
                    "reference_no" => "EXP-2025-003",
                    "description" => "Biaya Listrik Kantor",
                    "category" => "Operasional",
                    "supplier" => "PLN",
                    "status" => "Dibayar",
                    "amount" => 1200000,
                    "created_by" => "Admin",
                    "created_at" => "2025-07-08 16:45:00"
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-07",
                    "reference_no" => "EXP-2025-004",
                    "description" => "Biaya Hosting Website",
                    "category" => "Administrasi",
                    "supplier" => "AWS Indonesia",
                    "status" => "Diajukan",
                    "amount" => 850000,
                    "created_by" => "IT Support",
                    "created_at" => "2025-07-07 11:20:00"
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-06",
                    "reference_no" => "EXP-2025-005",
                    "description" => "Biaya Transport Karyawan",
                    "category" => "Operasional",
                    "supplier" => null,
                    "status" => "Draft",
                    "amount" => 500000,
                    "created_by" => "HRD",
                    "created_at" => "2025-07-06 08:30:00"
                ],
                [
                    "id" => 6,
                    "date" => "2025-07-05",
                    "reference_no" => "EXP-2025-006",
                    "description" => "Biaya Cetak Brosur",
                    "category" => "Marketing",
                    "supplier" => "Digital Printing Sukses",
                    "status" => "Ditolak",
                    "amount" => 350000,
                    "created_by" => "Marketing",
                    "created_at" => "2025-07-05 13:10:00"
                ],
                [
                    "id" => 7,
                    "date" => "2025-07-04",
                    "reference_no" => "EXP-2025-007",
                    "description" => "Biaya Konsultasi Hukum",
                    "category" => "Administrasi",
                    "supplier" => "Law Firm Partners",
                    "status" => "Dibayar",
                    "amount" => 3500000,
                    "created_by" => "Legal",
                    "created_at" => "2025-07-04 10:00:00"
                ],
                [
                    "id" => 8,
                    "date" => "2025-07-03",
                    "reference_no" => "EXP-2025-008",
                    "description" => "Biaya Maintenance AC",
                    "category" => "Operasional",
                    "supplier" => "Teknik Sejahtera",
                    "status" => "Dibayar",
                    "amount" => 650000,
                    "created_by" => "Admin",
                    "created_at" => "2025-07-03 15:30:00"
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

    public function export()
    {
        // TODO: Implement export functionality
        return redirect()->back()->with('success', 'Export functionality will be implemented');
    }

    public function import(Request $request)
    {
        // TODO: Implement import functionality
        // $request will be used for file upload validation and processing
        return redirect()->back()->with('success', 'Import functionality will be implemented');
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