<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Budged\Services\BudgedService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Contact\Services\ContactService;
use App\Domains\Expense\Actions\CreateExpenseAction;
use App\Domains\Expense\Actions\PaymentExpenseAction;
use App\Domains\Expense\Actions\UpdateExpenseAction;
use App\Domains\Expense\Services\ExpenseAuditLogService;
use App\Domains\Expense\Services\ExpensePaymentService;
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
use App\Services\TransactionNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExpenseController extends Controller
{
    public function index()
    {
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
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
        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $paymentMethods = app(PaymentMethodService::class)->findAll();
        $coa_accounts = app(CoaService::class)->findAll();
        $activeBudgets = app(BudgedService::class)->getActiveBudgets();
        return view('pages.main_transaction.expense.add', [
            'canAddTags' => true,
            'canAddContacts' => true,
            'canAddPaymentTerms' => true,
            'taxOptions' => $taxOptions,
            'availableTags' => $availableTags,
            'suppliers' => $suppliers,
            'paymentTerms' => $paymentTerms,
            'paymentMethods' => $paymentMethods,
            'coa_accounts' => $coa_accounts,
            'activeBudgets' => $activeBudgets,
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
            ['field' => 'created_by', 'label' => 'Dibuat Oleh', 'export_name' => 'Dibuat Oleh'],
        ];

        return view('pages.main_transaction.expense.export', [
            'exportColumns' => $exportColumns,
        ]);
    }

    public function edit($id)
    {
        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $paymentMethods = app(PaymentMethodService::class)->findAll();
        $coa_accounts = app(CoaService::class)->findAll();
        $activeBudgets = app(BudgedService::class)->getActiveBudgets();

        $expense = app(ExpenseService::class)->findOne($id);

        // Get current budget mapping
        $currentBudget = DB::table('t_budged_expenses')
            ->where('expense_id', $id)
            ->first();

        return view('pages.main_transaction.expense.edit', [
            'expense' => $expense,
            'canAddTags' => true,
            'canAddContacts' => true,
            'canAddPaymentTerms' => true,
            'paymentMethods' => $paymentMethods,
            'paymentTerms' => $paymentTerms,
            'suppliers' => $suppliers,
            'availableTags' => $availableTags,
            'taxOptions' => $taxOptions,
            'coa_accounts' => $coa_accounts,
            'activeBudgets' => $activeBudgets,
            'currentBudget' => $currentBudget,
        ]);
    }

    public function payment($id)
    {
        // Check permissions for adding related data
        $canAddTags = true;
        $canAddContacts = true;
        $canAddPaymentTerms = true;

        $taxOptions = app(TaxService::class)->findAll();
        $availableTags = app(TagService::class)->findAll();
        $suppliers = app(ContactService::class)->findAllByFlag('supplier');
        $paymentTerms = app(PaymentTermService::class)->findAll();
        $paymentMethods = app(PaymentMethodService::class)->findAll();
        $coa_accounts = app(CoaService::class)->findAll();

        $expense = app(ExpenseService::class)->findOne($id);

        return view('pages.main_transaction.expense.payment', [
            'expense' => $expense,
            'canAddTags' => $canAddTags,
            'canAddContacts' => $canAddContacts,
            'canAddPaymentTerms' => $canAddPaymentTerms,
            'paymentMethods' => $paymentMethods,
            'paymentTerms' => $paymentTerms,
            'suppliers' => $suppliers,
            'availableTags' => $availableTags,
            'taxOptions' => $taxOptions,
            'coa_accounts' => $coa_accounts,
        ]);
    }

    public function payment_detail($id)
    {
        $payment = app(ExpensePaymentService::class)->findOneById($id);

        return view('pages.main_transaction.expense.payment_detail', [
            'payment' => $payment,
        ]);
    }

    public function detail($id)
    {
        $expense = app(ExpenseService::class)->findOne($id);

        // Get audit logs
        $auditLogs = app(ExpenseAuditLogService::class)->findAllBasedOnExpenseId($id);

        // Permission checks for action buttons
        $canEdit = true;

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
                'payment_method_type' => 'required|string|in:kas_tunai,transfer',
                'transfer_bank' => 'nullable|string|in:BCA,Mandiri,BTPN',
                'payment_method' => 'nullable|string|exists:m_payment_methods,id',
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
                // 'category_type' => session('user_company_code') === '001' ? 'required|string|in:ANGGARAN,OVERHEAD,LAINNYA' : 'nullable|string',
                'entity_type' => 'required|string|in:kingdom,owner',
                // Budget field
                'budged_history_id' => 'nullable|string|exists:m_budgeds,id', 

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
                'payment_method_type.required' => 'Sumber pembayaran wajib dipilih.',
                'payment_method_type.in' => 'Sumber pembayaran tidak valid.',
                'transfer_bank.in' => 'Bank transfer tidak valid.',
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
                'entity_type.required' => 'Entitas wajib dipilih.',
                'attachment.max' => 'Ukuran file lampiran maksimal 5MB.',
            ]);

            // Kas/Tunai: validate saldo and deduct inside the action
            if ($validated['payment_method_type'] === 'kas_tunai') {
                $companyCode = session('user_company_code');
                $latest = DB::table('t_kas_besar')
                    ->where('company_code', $companyCode)
                    ->orderBy('id', 'DESC')
                    ->first();
                $saldo = $latest ? (float) $latest->saldo_after : 0;
                $expenseAmount = (float) $validated['final_total'];
                if ($expenseAmount > $saldo) {
                    return response()->json([
                        'message' => "Saldo Kas Besar tidak mencukupi. Saldo saat ini: Rp " . number_format($saldo, 0, ',', '.'),
                        'error' => 'KAS_BESAR_INSUFFICIENT',
                    ], 422);
                }
            }

            app(CreateExpenseAction::class)->execute($validated);

            // Retrieve the newly created expense ID
            $expenseId = DB::table('t_expenses')
                ->where('transaction_no', $validated['transaction_number'])
                ->value('id');

            // Deduct from Kas Besar if payment is Kas/Tunai
            if ($validated['payment_method_type'] === 'kas_tunai') {
                $companyCode = session('user_company_code');
                $latest = DB::table('t_kas_besar')
                    ->where('company_code', $companyCode)
                    ->orderBy('id', 'DESC')
                    ->first();
                $saldoBefore = $latest ? (float) $latest->saldo_after : 0;
                $expenseAmount = (float) $validated['final_total'];
                $saldoAfter = $saldoBefore - $expenseAmount;

                $kasNumber = app(TransactionNumberService::class)->generateNumber('kas_besar');
                $kasId = DB::table('t_kas_besar')->insertGetId([
                    'company_code'       => $companyCode,
                    'transaction_number' => $kasNumber,
                    'transaction_date'   => $validated['transaction_date'],
                    'type'               => 'DEDUCTION',
                    'topup_amount'       => -$expenseAmount,
                    'saldo_before'       => $saldoBefore,
                    'saldo_after'        => $saldoAfter,
                    'f_expense_id'       => $expenseId,
                    'notes'              => 'Pengeluaran: ' . $validated['transaction_number'],
                    'created_by'         => auth()->id(),
                    'updated_by'         => auth()->id(),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                if ($expenseId) {
                    DB::table('t_expenses')
                        ->where('id', $expenseId)
                        ->update([
                            'payment_method_type' => 'kas_tunai',
                            'f_kas_besar_id'      => $kasId,
                            'updated_at'          => now(),
                        ]);
                }
            } elseif ($validated['payment_method_type'] === 'transfer' && $expenseId) {
                DB::table('t_expenses')
                    ->where('id', $expenseId)
                    ->update([
                        'payment_method_type' => 'transfer',
                        'transfer_bank'       => $validated['transfer_bank'] ?? null,
                        'updated_at'          => now(),
                    ]);
            }

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
    }

    public function payment_save(Request $request)
    {
        try {
            $validated = $request->validate([
                'expense_id' => 'required|integer|exists:t_expenses,id',
                'transaction_number' => 'required|string|max:50|unique:t_expenses,transaction_no',
                'recipient_id' => 'nullable|integer|exists:m_contacts,id',
                'transaction_date' => 'required|date|before_or_equal:today',
                'payment_coa' => 'nullable|string|exists:m_coas,code',

                // Additional information
                'description' => 'nullable|string|max:1000',
                'memo' => 'nullable|string|max:1000',

                // Financial fields
                'remaining_amount' => 'required|numeric|min:0.01',
                'payment_amount' => 'required|numeric|min:0.01',

                // Optional fields
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            ], [
                'transaction_number.required' => 'Nomor transaksi wajib diisi.',
                'transaction_number.unique' => 'Nomor transaksi sudah digunakan.',
                'recipient_id.exists' => 'Penerima tidak valid.',
                'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
                'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh lebih dari hari ini.',
                
                'remaining_amount.required' => 'Subtotal wajib diisi.',
                'remaining_amount.min' => 'Subtotal harus lebih dari 0.',
                'payment_amount.required' => 'Total akhir wajib diisi.',
                'payment_amount.min' => 'Total akhir harus lebih dari 0.',
                'tags.*.exists' => 'Tag tidak valid.',
                'attachment.mimes' => 'File lampiran harus berformat PDF, JPG, JPEG, atau PNG.',
                'attachment.max' => 'Ukuran file lampiran maksimal 5MB.',
            ]);
            app(PaymentExpenseAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_EXPENSE_PAYMENT_SUCCESS->message(),
            ], SuccessCodes::CREATE_EXPENSE_PAYMENT_SUCCESS->httpStatus());
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
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                // Basic transaction fields
                'expense_id' => 'required|integer|exists:t_expenses,id',
                'payment_method' => 'nullable|string|exists:m_payment_methods,id',
                'pay_later' => 'required|boolean',
                // 'transaction_number' => 'required|string|max:50|unique:t_expenses,transaction_no',
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
                // 'category_type' => session('user_company_code') === '001' ? 'required|string|in:Anggaran,Overhead,Lainnya' : 'nullable|string|in:Anggaran,Overhead,Lainnya',
                // Budget field
                'budged_history_id' => 'nullable|string|exists:m_budgeds,id',

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
                // 'transaction_number.required' => 'Nomor transaksi wajib diisi.',
                // 'transaction_number.unique' => 'Nomor transaksi sudah digunakan.',
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
                // 'category_type.required' => 'Kategori pengeluaran wajib dipilih.',
                // 'category_type.in' => 'Kategori pengeluaran harus salah satu dari: Anggaran, Overhead, atau Lainnya.',
            ]);

            app(UpdateExpenseAction::class)->execute($validated);

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
    }

    public function getBudgetsByDate(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $budgets = app(BudgedService::class)->getActiveBudgets(null, $date);
        return response()->json($budgets);
    }
}
