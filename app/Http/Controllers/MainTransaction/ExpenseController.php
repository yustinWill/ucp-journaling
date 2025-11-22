<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Coa\Services\CoaService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\Expense\Actions\CreateExpenseAction;
use App\Domains\Expense\Services\ExpenseAuditLogService;
use App\Domains\Expense\Services\ExpenseService;
use App\Domains\PaymentMethod\Services\PaymentMethodService;
use App\Domains\PaymentTerm\Services\PaymentTermService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Tax\Services\TaxService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    public function index()
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data pengeluaran.');
        }

        $suppliers = app(ContactService::class)->findAllByFlag('supplier');

        // Get permission data for view
        $permissionData = PermissionHelper::getViewPermissions('main_transaction_expense');

        return view('pages.main_transaction.expense.index', array_merge([
            'suppliers' => $suppliers,
        ], $permissionData));
    }

    public function index_data_table()
    {
        $data = app(ExpenseService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data pengeluaran.');
        }

        // Check permissions for adding related data
        $canAddTags = PermissionHelper::hasPermission('master_data_tag', 'write');
        $canAddContacts = PermissionHelper::hasPermission('master_data_contact', 'write');
        $canAddPaymentTerms = PermissionHelper::hasPermission('master_data_payment_term', 'write');

        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $paymentMethods = app(PaymentMethodService::class)->findAll();
        $coa_accounts = app(CoaService::class)->findAll();

        return view('pages.main_transaction.expense.add', [
            'canAddTags' => $canAddTags,
            'canAddContacts' => $canAddContacts,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'taxOptions' => $taxOptions,
            'availableTags' => $availableTags,
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
            'paymentMethods' => $paymentMethods,
            'coa_accounts' => $coa_accounts,

        ]);
    }

    public function import()
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk import data pengeluaran.');
        }

        return view('pages.main_transaction.expense.import', []);
    }

    public function export()
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
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
            ['field' => 'created_by', 'label' => 'Dibuat Oleh', 'export_name' => 'Dibuat Oleh'],
        ];

        return view('pages.main_transaction.expense.export', [
            'exportColumns' => $exportColumns,
        ]);
    }

    public function edit($id)
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
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
            ['value' => 'e_wallet', 'label' => 'E-wallet'],
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
                (object) ['code' => 'EXP002', 'name' => 'Urgent', 'preview' => 'bg-label-danger'],
            ],
            'expense_lines' => [
                (object) [
                    'account_code' => '5101',
                    'account_name' => '5101 - Biaya Operasional',
                    'description' => 'Pembelian bahan baku plastik untuk kemasan',
                    'tax_code' => 'PPN01',
                    'tax_name' => 'PPN01 - PPN 11%',
                    'amount' => 20000000,
                ],
                (object) [
                    'account_code' => '5201',
                    'account_name' => '5201 - Biaya Marketing',
                    'description' => 'Biaya promosi produk',
                    'tax_code' => null,
                    'tax_name' => null,
                    'amount' => 5000000,
                ],
            ],
        ];

        return view('pages.main_transaction.expense.edit', [
            'expense' => $expense,
            'canAddTags' => $canAddTags,
            'canAddContacts' => $canAddContacts,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function detail($id)
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail pengeluaran.');
        }

        $expense = app(ExpenseService::class)->findOne($id);

        // Get audit logs
        $auditLogs = app(ExpenseAuditLogService::class)->findAllBasedOnExpenseId($id);

        // Permission checks for action buttons
        $canEdit = PermissionHelper::hasPermission('main_transaction_expense', 'write');

        return view('pages.main_transaction.expense.detail', [
            'expense' => $expense,
            'canEdit' => $canEdit,
            'audit_logs' => $auditLogs,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                // Basic transaction fields
                'payment_method' => 'required|string|in:CASH,BANK_TRANSFER,CREDIT_CARD,DEBIT_CARD,E_WALLET,CHECK',
                'pay_later' => 'required|boolean',
                'transaction_number' => 'required|string|max:50|unique:t_expenses,transaction_no',
                'recipient_id' => 'nullable|integer|exists:m_contacts,id',
                'transaction_date' => 'required|date|before_or_equal:today',

                // Payment terms (required if pay_later is true)
                'payment_terms' => 'nullable|integer|exists:m_payment_terms,id',
                'due_date' => 'nullable|date|after:transaction_date',

                // Additional information
                'billing_address' => 'nullable|string|max:500',
                'description' => 'nullable|string|max:1000',
                'memo' => 'nullable|string|max:1000',

                // Financial fields
                'discount_amount' => 'nullable|numeric|min:0',
                'subtotal' => 'required|numeric|min:0.01',
                'tax_amount' => 'required|numeric|min:0',
                'total_before_discount' => 'required|numeric|min:0.01',
                'final_total' => 'required|numeric|min:0.01',

                // Expense lines validation
                'expense_lines' => 'required|array|min:1',
                'expense_lines.*.expense_account' => 'required|string|exists:m_coas,code',
                'expense_lines.*.tax_id' => 'nullable|string|exists:m_taxes,id',
                'expense_lines.*.tax_rate' => 'nullable|numeric|min:0|max:100',
                'expense_lines.*.amount' => 'required|numeric|min:0.01',

                // Optional fields
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            ], [
                // Custom error messages
                'payment_method.required' => 'Metode pembayaran wajib dipilih.',
                'payment_method.in' => 'Metode pembayaran tidak valid.',
                'transaction_number.required' => 'Nomor transaksi wajib diisi.',
                'transaction_number.unique' => 'Nomor transaksi sudah digunakan.',
                'recipient_id.exists' => 'Penerima tidak valid.',
                'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
                'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh lebih dari hari ini.',
                'payment_terms.exists' => 'Term pembayaran tidak valid.',
                'due_date.after' => 'Tanggal jatuh tempo harus setelah tanggal transaksi.',
                'subtotal.required' => 'Subtotal wajib diisi.',
                'subtotal.min' => 'Subtotal harus lebih dari 0.',
                'final_total.required' => 'Total akhir wajib diisi.',
                'final_total.min' => 'Total akhir harus lebih dari 0.',
                'expense_lines.required' => 'Minimal harus ada satu baris pengeluaran.',
                'expense_lines.min' => 'Minimal harus ada satu baris pengeluaran.',
                'expense_lines.*.expense_account.required' => 'Akun pengeluaran wajib dipilih.',
                'expense_lines.*.expense_account.exists' => 'Akun pengeluaran tidak valid.',
                'expense_lines.*.tax_id.exists' => 'Kode pajak tidak valid.',
                'expense_lines.*.tax_rate.min' => 'Rate pajak tidak boleh kurang dari 0.',
                'expense_lines.*.tax_rate.max' => 'Rate pajak tidak boleh lebih dari 100.',
                'expense_lines.*.amount.required' => 'Jumlah pengeluaran wajib diisi.',
                'expense_lines.*.amount.min' => 'Jumlah pengeluaran harus lebih dari 0.',
                'tags.*.exists' => 'Tag tidak valid.',
                'attachment.mimes' => 'File lampiran harus berformat PDF, JPG, JPEG, atau PNG.',
                'attachment.max' => 'Ukuran file lampiran maksimal 5MB.',
            ]);
            app(CreateExpenseAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_EXPENSE_SUCCESS->message(),
            ], SuccessCodes::CREATE_EXPENSE_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }

        // if (! PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
        //     abort(403, 'Anda tidak memiliki akses untuk mengupdate data pengeluaran.');
        // }

        // // Validation rules (implement as needed)
        // $validated = $request->validate([
        //     'transaction_number' => 'required|string|max:255',
        //     'payment_method' => 'required|string',
        //     'recipient' => 'required|string|max:255',
        //     'transaction_date' => 'required|date',
        //     'billing_address' => 'nullable|string',
        //     'description' => 'nullable|string',
        //     'memo' => 'nullable|string',
        //     'tags' => 'nullable|array',
        //     'expense_lines' => 'required|array',
        // ]);

        // // Update logic would go here
        // // For now, just redirect back with success message
        // return redirect()->route('main_transaction.expense.detail', $id)
        //     ->with('success', 'Data pengeluaran berhasil diupdate.');
    }

    public function update(Request $request, $id)
    {
        if (! PermissionHelper::hasPermission('main_transaction_expense', 'write')) {
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
            'expense_lines' => 'required|array',
        ]);

        // Update logic would go here
        // For now, just redirect back with success message
        return redirect()->route('main_transaction.expense.detail', $id)
            ->with('success', 'Data pengeluaran berhasil diupdate.');
    }
}
