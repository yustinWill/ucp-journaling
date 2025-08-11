<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;
use App\Exceptions\AppException;
use Illuminate\Validation\ValidationException;
use App\Helpers\PermissionHelper;

use App\Domains\AccountingJournal\Actions\CreateAccountingJournalAction;
use App\Domains\AccountingJournal\Actions\UpdateAccountingJournalAction;

use App\Domains\AccountingJournal\Services\AccountingJournalService;
use App\Domains\AccountingJournal\Services\AccountingJournalAuditLogService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Tag\Services\TagService;
use App\Domains\User\Services\UserService;

use App\Enums\ErrorCodes;
use App\Enums\AuditLogTypes;
use App\Enums\SuccessCodes;

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
        $data = app(AccountingJournalService::class)->findOne($id);

        if ($data == null) {
            abort(404);
        }

        $total_debit = 0;
        $total_credit = 0;
        $entries = [];
        foreach ($data as $row) {
            if ($row->debit_credit == 'D') {
                $total_debit += $row->amount;
            } else {
                $total_credit += $row->amount;
            }

            $entries[] = [
                'account_code' => $row->f_coa_code,
                'account_name' => $row->coa_name,
                'description' => $row->coa_description,
                'debit_amount' => $row->debit_credit == 'D' ? 'Rp ' . number_format($row->amount, 0, ',', '.') : null,
                'credit_amount' => $row->debit_credit == 'C' ? 'Rp ' . number_format($row->amount, 0, ',', '.') : null
            ];
        }

        $header = $data[0];

        $auditLogs = app(AccountingJournalAuditLogService::class)->findAllBasedOnId($header->id);

        $journal = [
            'id' => $id,
            'reference_no' => $header->reference_no,
            'journal_date' => $header->date,
            'description' => $header->description,
            'memo' => $header->internal_memo,
            'tags' => json_decode($header->tags, true),
            'total_debit' => 'Rp ' . number_format($total_debit, 0, ',', '.'),
            'total_credit' => 'Rp ' . number_format($total_credit, 0, ',', '.'),
            'difference' => 'Rp ' . number_format($total_debit - $total_credit, 0, ',', '.'),
            'entries' => $entries,
            'audit_logs' => $auditLogs,
            'auditLogType' =>  AuditLogTypes::MAIN_TRANSACTION_JOURNAL->code()
        ];

        return view('pages.main_transaction.journal.detail', compact('journal'));
    }

    public function edit($reference_no)
    {
        $data = app(AccountingJournalService::class)->findOne($reference_no);

        if ($data == null) {
            abort(404);
        }

        $canAddTags = PermissionHelper::canWrite('master_data_tag');
        $tags = app(TagService::class)->findAll();
        $coas = app(CoaService::class)->findAll();

        $total_debit = 0;
        $total_credit = 0;
        $entries = [];
        foreach ($data as $row) {
            if ($row->debit_credit == 'D') {
                $total_debit += $row->amount;
            } else {
                $total_credit += $row->amount;
            }

            $entries[] = [
                'id' => $row->id,
                'account_code' => $row->f_coa_code,
                'account_name' => $row->coa_name,
                'description' => $row->coa_description,
                'debit_amount' => $row->debit_credit == 'D' ? 'Rp ' . number_format($row->amount, 0, ',', '.') : null,
                'credit_amount' => $row->debit_credit == 'C' ? 'Rp ' . number_format($row->amount, 0, ',', '.') : null
            ];
        }
        $header = $data[0];
        // Sample journal data for editing
        $journal = [
            'id' => $reference_no,
            'reference_no' => $header->reference_no,
            'journal_date' => $header->date,
            'description' => $header->description,
            'memo' => $header->internal_memo,
            'tags' => json_decode($header->tags, true),
            'f_tag_codes' => json_decode($header->f_tag_codes, true),
            'total_debit' => 'Rp ' . number_format($total_debit, 0, ',', '.'),
            'total_credit' => 'Rp ' . number_format($total_credit, 0, ',', '.'),
            'difference' => 'Rp ' . number_format($total_debit - $total_credit, 0, ',', '.'),
            'entries' => $entries
        ];

        return view('pages.main_transaction.journal.edit', [
            'journal' => $journal,
            'coas' => $coas,
            'tags' => $tags,
            'canAddTags' => $canAddTags
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'reference_no' => 'required|string|exists:t_accounting_journals,reference_no',
                'date' => 'required|date_format:Y-m-d',
                'status' => 'required|string|in:ACTIVE,INACTIVE',
                'description' => 'nullable|string|max:255',
                'memo' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'journals' => 'required|array|min:1',
                'journals.*.journal_id' => 'nullable|exists:t_accounting_journals,id',
                'journals.*.account_code' => 'required|string|max:20',
                'journals.*.description' => 'nullable|string|max:255',
                'journals.*.debit_amount' => 'nullable|numeric|min:0',
                'journals.*.credit_amount' => 'nullable|numeric|min:0'
            ]);

            app(UpdateAccountingJournalAction::class)->execute($validated['reference_no'], $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_ACCOUNTING_JOURNAL_SUCCESS->message()
            ], SuccessCodes::UPDATE_ACCOUNTING_JOURNAL_SUCCESS->httpStatus());
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
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }
}
