<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Enums\SuccessCodes;
use Illuminate\Http\Request;
use App\Domains\AccountingJournal\Services\AccountingJournalService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Tag\Services\TagService;
use App\Domains\User\Services\UserService;
use App\Helpers\PermissionHelper;
use App\Domains\AccountingJournal\Actions\CreateAccountingJournalAction;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;
use App\Exceptions\AppException;
use Illuminate\Validation\ValidationException;
use App\Enums\ErrorCodes;

class JournalController extends Controller
{
    public function index()
    {
        $createdByUsers = app(UserService::class)->findAll();

        return view('pages.main_transaction.journal.index', [
            'createdByUsers' => $createdByUsers
        ]);
    }

    public function index_data_table()
    {
        $data = app(AccountingJournalService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function generate_reference_no($date)
    {
        $data = app(AccountingJournalService::class)->generateReferenceNo($date);

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $tags = app(TagService::class)->findAll();
        $coas = app(CoaService::class)->findAll();
        $referenceNo = app(AccountingJournalService::class)->generateReferenceNo(date('Y-m-d'));
        $canAddTags = PermissionHelper::canWrite('master_data_tag');

        return view('pages.main_transaction.journal.add', [
            'today' => date('Y-m-d'),
            'canAddTags' => $canAddTags,
            'tags' => $tags,
            'coas' => $coas,
            'referenceNo' => $referenceNo
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'reference_no' => 'required|string|unique:t_accounting_journals,reference_no',
                'date' => 'required|date_format:Y-m-d',
                'description' => 'nullable|string|max:255',
                'memo' => 'nullable|string|max:255',
                // 'amount' => 'required|numeric|min:0',
                'tags' => 'nullable|array',
                'journals' => 'required|array|min:1',
                'journals.*.account_code' => 'required|string|max:20',
                'journals.*.description' => 'nullable|string|max:255',
                'journals.*.debit_amount' => 'nullable|numeric|min:0',
                'journals.*.credit_amount' => 'nullable|numeric|min:0'
            ]);

            app(CreateAccountingJournalAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_ACCOUNTING_JOURNAL_SUCCESS->message()
            ], SuccessCodes::CREATE_ACCOUNTING_JOURNAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            dd('bbb');
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
            dd($e);
            DiscordNotifier::reportException($e);
            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
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
