<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\AccountingJournal\Services\AccountingJournalService;
use App\Domains\Coa\Actions\CreateCoaAction;
use App\Domains\Coa\Actions\UpdateCoaAction;
use App\Domains\Coa\Services\CoaAuditLogService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Tag\Services\TagService;
use App\Enums\ErrorCodes;
// Actions
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
// Services
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Enums
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CoaController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
    ];

    public function index()
    {
        $statuses = $this->statuses;

        return view('pages.master_data.coa.index', compact('statuses'));
    }

    public function index_data_table()
    {
        $data = app(CoaService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function journal_log_per_coa_data_table()
    {
        $data = app(AccountingJournalService::class)->findAllPerCoaDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $tags = app(TagService::class)->findAll();

        $statuses = $this->statuses;
        $coa_accounts = app(CoaService::class)->findAll();

        return view('pages.master_data.coa.add', [
            'tags' => $tags,
            'statuses' => $statuses,
            'coa_accounts' => $coa_accounts,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:m_coas,code',
                'parent_coa_code' => 'required|exists:m_coas,code',
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:I,D',
                'note' => 'nullable|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'tags' => 'nullable|array',
            ]);

            app(CreateCoaAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_COA_SUCCESS->message(),
            ], SuccessCodes::CREATE_COA_SUCCESS->httpStatus());
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

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Kode COA'],
            ['no' => 2, 'value' => 'Nama COA'],
            ['no' => 3, 'value' => 'Level COA'],
            ['no' => 4, 'value' => 'Jenis COA'],
            ['no' => 5, 'value' => 'Note'],
            ['no' => 6, 'value' => 'Status'],
        ];

        return view('pages.master_data.coa.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.coa.import', []);
    }

    public function detail($id)
    {
        $coa = app(CoaService::class)->findOne($id);

        $audit_log_data = app(CoaAuditLogService::class)->findAllBasedOnId($id);

        return view('pages.master_data.coa.detail', compact('coa', 'audit_log_data'));
    }

    public function edit($id)
    {
        $tags = app(TagService::class)->findAll();

        $statuses = $this->statuses;
        $coa_accounts = app(CoaService::class)->findAll();
        $coa = app(CoaService::class)->findOne($id);

        return view('pages.master_data.coa.edit', [
            'id' => $id,
            'tags' => $tags,
            'statuses' => $statuses,
            'coa_accounts' => $coa_accounts,
            'coa' => $coa,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_coas,id',
                'parent_coa_code' => 'required|exists:m_coas,code',
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:I,D',
                'note' => 'nullable|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'tags' => 'nullable|array',
            ]);

            app(UpdateCoaAction::class)->execute($validated['id'], $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_COA_SUCCESS->message(),
            ], SuccessCodes::UPDATE_COA_SUCCESS->httpStatus());
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
}
