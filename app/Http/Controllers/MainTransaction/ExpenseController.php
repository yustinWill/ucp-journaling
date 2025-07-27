<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data pengeluaran.');
        }

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

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_expense');

        return view('pages.main_transaction.expense.index', array_merge([
            'expenseCategories' => $categories,
            'expenseStatuses' => $statuses
        ], $permissionData));
    }

    public function index_data_table()
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "date" => "2025-07-15",
                    "reference_no" => "EXP-2025-001",
                    "description" => "Pembelian bahan baku plastik untuk kemasan RIYO Water 330ml",
                    "category" => "Operasional Produksi", 
                    "supplier" => "PT. Plastik Prima Indonesia",
                    "supplier_id" => "CNT-001",
                    "tags" => [
                        ["name" => "Operasional", "preview" => "bg-label-primary"],
                        ["name" => "Urgent", "preview" => "bg-label-danger"]
                    ],
                    "status" => "Dibayar",
                    "status_badge_class" => "bg-label-primary",
                    "amount" => 25000000,
                    "created_by" => "Ahmad Rizki",
                    "created_by_id" => "USR-001",
                    "created_at" => "2025-07-15 09:30:00"
                ],
                [
                    "id" => 2,
                    "date" => "2025-07-14",
                    "reference_no" => "EXP-2025-002",
                    "description" => "Biaya promosi dan iklan RIYO Water di media sosial",
                    "category" => "Marketing & Promosi",
                    "supplier" => "Digital Marketing Agency",
                    "supplier_id" => "CNT-002",
                    "tags" => [
                        ["name" => "Marketing", "preview" => "bg-label-success"]
                    ],
                    "status" => "Disetujui",
                    "status_badge_class" => "bg-label-success",
                    "amount" => 8500000,
                    "created_by" => "Siti Nurhaliza",
                    "created_by_id" => "USR-002",
                    "created_at" => "2025-07-14 14:15:00"
                ],
                [
                    "id" => 3,
                    "date" => "2025-07-13",
                    "reference_no" => "EXP-2025-003",
                    "description" => "Biaya listrik dan air untuk fasilitas produksi",
                    "category" => "Operasional Produksi",
                    "supplier" => "PLN & PDAM",
                    "supplier_id" => "CNT-003",
                    "tags" => [
                        ["name" => "Operasional", "preview" => "bg-label-primary"],
                        ["name" => "Utilities", "preview" => "bg-label-secondary"]
                    ],
                    "status" => "Dibayar",
                    "status_badge_class" => "bg-label-primary",
                    "amount" => 12000000,
                    "created_by" => "Yustinus Komunal",
                    "created_by_id" => "USR-003",
                    "created_at" => "2025-07-13 16:45:00"
                ],
                [
                    "id" => 4,
                    "date" => "2025-07-12",
                    "reference_no" => "EXP-2025-004",
                    "description" => "Maintenance mesin filling dan quality control equipment",
                    "category" => "Maintenance & Repair",
                    "supplier" => "PT. Teknisi Ahli Mesin",
                    "supplier_id" => "CNT-004",
                    "tags" => [
                        ["name" => "Maintenance", "preview" => "bg-label-warning"]
                    ],
                    "status" => "Diajukan",
                    "status_badge_class" => "bg-label-info",
                    "amount" => 15500000,
                    "created_by" => "Budi Santoso",
                    "created_by_id" => "USR-004",
                    "created_at" => "2025-07-12 11:20:00"
                ],
                [
                    "id" => 5,
                    "date" => "2025-07-11",
                    "reference_no" => "EXP-2025-005",
                    "description" => "Biaya transportasi distribusi ke wilayah Jabodetabek",
                    "category" => "Distribusi & Logistik",
                    "supplier" => "CV. Ekspedisi Maju",
                    "supplier_id" => "CNT-005",
                    "tags" => [
                        ["name" => "Distribusi", "preview" => "bg-label-info"]
                    ],
                    "status" => "Draft",
                    "status_badge_class" => "bg-label-secondary",
                    "amount" => 6500000,
                    "created_by" => "Ahmad Rizki",
                    "created_by_id" => "USR-001",
                    "created_at" => "2025-07-11 08:30:00"
                ],
                [
                    "id" => 6,
                    "date" => "2025-07-10",
                    "reference_no" => "EXP-2025-006",
                    "description" => "Gaji karyawan produksi dan quality assurance",
                    "category" => "Administrasi & Umum",
                    "supplier" => null,
                    "supplier_id" => null,
                    "tags" => [
                        ["name" => "SDM", "preview" => "bg-label-dark"]
                    ],
                    "status" => "Ditolak",
                    "status_badge_class" => "bg-label-danger",
                    "amount" => 85000000,
                    "created_by" => "Siti Nurhaliza",
                    "created_by_id" => "USR-002",
                    "created_at" => "2025-07-10 13:10:00"
                ]
            ]
        ];

        return response()->json($data, 200);
    }

    public function add()
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data pengeluaran.');
        }

        // Check permissions for adding related data
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddContacts = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_payment_term', 'write');

        // Static options that don't change frequently
        $paymentMethods = [
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'check', 'label' => 'Check'],
            ['value' => 'e_wallet', 'label' => 'E-wallet']
        ];

        return view('pages.main_transaction.expense.add', [
            'canAddTags' => $canAddTags,
            'canAddContacts' => $canAddContacts,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function import()
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk import data pengeluaran.');
        }

        return view('pages.main_transaction.expense.import', []);
    }

    public function export()
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk export data pengeluaran.');
        }

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
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data pengeluaran.');
        }

        // Check permissions for adding related data
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddContacts = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_payment_term', 'write');

        // Static options that don't change frequently
        $paymentMethods = [
            ['value' => 'cash', 'label' => 'Cash'],
            ['value' => 'bank_transfer', 'label' => 'Bank Transfer'],
            ['value' => 'credit_card', 'label' => 'Credit Card'],
            ['value' => 'check', 'label' => 'Check'],
            ['value' => 'e_wallet', 'label' => 'E-wallet']
        ];

        // Mocked expense data (in real implementation, this would come from database)
        $expense = (object) [
            'id' => $id,
            'transaction_number' => 'EXP-2025-001',
            'payment_method' => 'cash',
            'pay_later' => false,
            'recipient' => 'PT. Supplier Utama',
            'transaction_date' => '2025-07-26',
            'payment_terms' => null,
            'due_date' => null,
            'billing_address' => 'Jl. Industri No. 123, Jakarta Barat 11530',
            'description' => 'Keterangan tambahan untuk pembelian bahan baku plastik',
            'memo' => 'Pembelian bahan baku untuk produksi bulan ini. Supplier memberikan diskon 5% untuk pembelian dalam jumlah besar.',
            'attachment' => 'invoice_supplier.pdf',
            'discount_amount' => 1200000,
            'status' => 'PAID',
            'created_by' => 'Ahmad Rizki',
            'created_at' => '2025-07-26 09:30:00',
            'updated_at' => '2025-07-26 10:15:00',
            'tags' => [
                (object) ['code' => 'EXP001', 'name' => 'Operasional', 'preview' => 'bg-label-primary'],
                (object) ['code' => 'EXP002', 'name' => 'Urgent', 'preview' => 'bg-label-danger']
            ],
            'expense_lines' => [
                (object) [
                    'account_code' => '5101',
                    'account_name' => '5101 - Biaya Operasional',
                    'description' => 'Pembelian bahan baku plastik untuk kemasan',
                    'tax_code' => 'PPN01',
                    'tax_name' => 'PPN01 - PPN 11%',
                    'amount' => 20000000
                ],
                (object) [
                    'account_code' => '5201',
                    'account_name' => '5201 - Biaya Marketing',
                    'description' => 'Biaya promosi produk',
                    'tax_code' => null,
                    'tax_name' => null,
                    'amount' => 5000000
                ]
            ]
        ];

        return view('pages.main_transaction.expense.edit', [
            'expense' => $expense,
            'canAddTags' => $canAddTags,
            'canAddContacts' => $canAddContacts,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'paymentMethods' => $paymentMethods
        ]);
    }

    public function detail($id)
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail pengeluaran.');
        }

        // Permission checks for action buttons
        $canEdit = PermissionHelper::hasPermission('main_transaction_expense', 'write');

        // Mocked expense data (in real implementation, this would come from database)
        $expense = (object) [
            'id' => $id,
            'transaction_number' => 'EXP-2025-001',
            'payment_method' => 'Cash',
            'pay_later' => false,
            'recipient' => 'PT. Supplier Utama',
            'transaction_date' => '2025-07-26',
            'payment_terms' => '-',
            'due_date' => '-',
            'billing_address' => 'Jl. Industri No. 123, Jakarta Barat 11530',
            'description' => 'Keterangan tambahan untuk pembelian bahan baku plastik',
            'memo' => 'Pembelian bahan baku untuk produksi bulan ini. Supplier memberikan diskon 5% untuk pembelian dalam jumlah besar.',
            'attachment' => 'invoice_supplier.pdf',
            'subtotal' => 25000000,
            'tax_amount' => 2200000,
            'total_before_discount' => 27200000,
            'discount_amount' => 1200000,
            'final_total' => 26000000,
            'status' => 'Dibayar',
            'created_by' => 'Ahmad Rizki',
            'created_at' => '2025-07-26 09:30',
            'updated_at' => '2025-07-26 10:15',
            'tags' => [
                (object) ['name' => 'Operasional', 'preview' => 'bg-label-primary'],
                (object) ['name' => 'Urgent', 'preview' => 'bg-label-success']
            ],
            'expense_lines' => [
                (object) [
                    'account_name' => '5101 - Biaya Operasional',
                    'description' => 'Pembelian bahan baku plastik untuk kemasan',
                    'tax_name' => 'PPN 11%',
                    'amount' => 20000000
                ],
                (object) [
                    'account_name' => '5201 - Biaya Marketing',
                    'description' => 'Biaya promosi produk',
                    'tax_name' => '-',
                    'amount' => 5000000
                ]
            ]
        ];

        return view('pages.main_transaction.expense.detail', [
            'expense' => $expense,
            'canEdit' => $canEdit
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate data pengeluaran.');
        }

        // Validation rules (implement as needed)
        $validated = $request->validate([
            'transaction_number' => 'required|string|max:255',
            'payment_method' => 'required|string',
            'recipient' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'billing_address' => 'nullable|string',
            'description' => 'nullable|string',
            'memo' => 'nullable|string',
            'tags' => 'nullable|array',
            'expense_lines' => 'required|array|min:1',
        ]);

        // Update logic would go here
        // For now, just redirect back with success message
        return redirect()->route('main_transaction.expense.detail', $id)
                        ->with('success', 'Data pengeluaran berhasil diupdate.');
    }

    public function categories(Request $request)
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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

    public function taxes(Request $request)
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $search = $request->get('search', '');
        
        $taxes = [
            ['value' => 'PPN01', 'label' => 'PPN01 - PPN 11%'],
            ['value' => 'PPN02', 'label' => 'PPN02 - PPN 1%'],
            ['value' => 'PPH21', 'label' => 'PPH21 - PPh Pasal 21'],
            ['value' => 'PPH23', 'label' => 'PPH23 - PPh Pasal 23'],
            ['value' => 'PPH4A2', 'label' => 'PPH4A2 - PPh Final Pasal 4 Ayat 2']
        ];

        if ($search) {
            $taxes = array_filter($taxes, function($tax) use ($search) {
                return stripos($tax['label'], $search) !== false;
            });
        }

        return response()->json(array_values($taxes));
    }

    public function tags(Request $request)
    {
        if (!PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $search = $request->get('search', '');
        
        $tags = [
            ['code' => 'EXP001', 'label' => 'EXP001 - Operasional', 'preview' => 'bg-label-primary'],
            ['code' => 'EXP002', 'label' => 'EXP002 - Marketing', 'preview' => 'bg-label-success'],
            ['code' => 'EXP003', 'label' => 'EXP003 - Administrasi', 'preview' => 'bg-label-info'],
            ['code' => 'EXP004', 'label' => 'EXP004 - IT', 'preview' => 'bg-label-warning'],
            ['code' => 'EXP005', 'label' => 'EXP005 - SDM', 'preview' => 'bg-label-dark']
        ];

        if ($search) {
            $tags = array_filter($tags, function($tag) use ($search) {
                return stripos($tag['label'], $search) !== false;
            });
        }

        return response()->json(array_values($tags));
    }
}