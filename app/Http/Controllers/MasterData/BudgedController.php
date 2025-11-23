<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\Budged\Services\BudgedService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BudgedController extends Controller
{
    public function index()
    {
        // $permissionData = PermissionHelper::getViewPermissions('master_data_budged');
        
        $permissionData =  [
            "permissions" => [
                "read" => true,
                "write" => true,
                "delete" => true,
                "approval" => true
            ],
            "canRead" => true,
            "canWrite" => true,
            "canDelete" => true,
            "canApprove" => true,
            "canShowAddButton" => true,
            "canShowEditButton" => true,
            "canShowDeleteButton" => true,
            "canShowImportButton" => true,
            "canShowExportButton" => true
    ]   ; 
        return view('pages.master_data.budged.index', $permissionData);
    }

    public function index_data_table()
    {
        $data = app(BudgedService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $budgedlist = app(BudgedService::class)->findAllDatatable();
        // dd($budgedlist["data"]);
        return view('pages.master_data.budged.add',["budgedlist" => $budgedlist["data"]]);
    }

    public function save(Request $request)
    {
        try {
            $budgetType = $request->input('budget_type');

            $rules = [
                'budget_type' => 'required|in:new,existing',
                'period_start_date' => 'required|date',
                'period_end_date' => 'required|date|after_or_equal:period_start_date',
                'budged_limit_value' => 'required|numeric|min:0',
            ];

            if ($budgetType === 'new') {
                $rules['budged_name'] = 'required|string|max:255';
            } else {
                $rules['existing_budged_id'] = 'required|exists:m_budgeds,id';
            }

            $validated = $request->validate($rules, [], [
                'budged_name' => 'Nama Anggaran',
                'existing_budged_id' => 'Anggaran',
                'period_start_date' => 'Tanggal Mulai Periode',
                'period_end_date' => 'Tanggal Akhir Periode',
                'budged_limit_value' => 'Nilai Limit Anggaran',
            ]);

            $budgedService = app(BudgedService::class);

            if ($budgetType === 'new') {
                // Create new budget
                $budgedId = $budgedService->create(['budged_name' => $validated['budged_name']]);
            } else {
                // Use existing budget
                $budgedId = $validated['existing_budged_id'];

                // Validate period doesn't overlap
                $validation = $budgedService->validatePeriodOverlap(
                    $budgedId,
                    $validated['period_start_date'],
                    $validated['period_end_date']
                );

                if (!$validation['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => $validation['message'],
                    ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
                }
            }

            // Create period for the budget
            $budgedService->createHistory($budgedId, [
                'budged_limit_value' => $validated['budged_limit_value'],
                'period_start_date' => $validated['period_start_date'],
                'period_end_date' => $validated['period_end_date'],
            ]);

            $message = $budgetType === 'new'
                ? 'Anggaran dan periode berhasil ditambahkan'
                : 'Periode anggaran berhasil ditambahkan';

            return response()->json([
                'success' => true,
                'message' => $message,
                'budged_id' => $budgedId,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function detail($id)
    {
        $budged = app(BudgedService::class)->findOne($id);
        $history = app(BudgedService::class)->findHistoryWithExpenses($id);
        $permissionData = PermissionHelper::getViewPermissions('master_data_budged');

        return view('pages.master_data.budged.detail', array_merge(
            compact('budged', 'history'),
            $permissionData
        ));
    }

    public function edit($id)
    {
        $budged = app(BudgedService::class)->findOne($id);
        $canWrite = true;
        return view('pages.master_data.budged.edit', compact('budged','canWrite'));
    }

    public function update(Request $request)
    {

        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_budgeds,id',
                'budged_name' => 'required|string|max:255',
            ], [], [
                'id' => 'ID Anggaran',
                'budged_name' => 'Nama Anggaran',
            ]);

            app(BudgedService::class)->update($validated['id'], $validated);

            return response()->json([
                'success' => true,
                'message' => 'Anggaran berhasil diperbarui',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    // Budget History/Periode Methods
    public function add_periode(Request $request)
    {
        try {
            $validated = $request->validate([
                'budged_id' => 'required|exists:m_budgeds,id',
                'period_start_date' => 'required|date',
                'period_end_date' => 'required|date|after_or_equal:period_start_date',
                'budged_limit_value' => 'required|numeric|min:0',
            ], [], [
                'budged_id' => 'ID Anggaran',
                'period_start_date' => 'Tanggal Mulai Periode',
                'period_end_date' => 'Tanggal Akhir Periode',
                'budged_limit_value' => 'Nilai Limit Anggaran',
            ]);

            // Validate period overlap
            $budgedService = app(BudgedService::class);
            $validation = $budgedService->validatePeriodOverlap(
                $validated['budged_id'],
                $validated['period_start_date'],
                $validated['period_end_date']
            );

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message'],
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            $budgedService->createHistory($validated['budged_id'], $validated);

            return response()->json([
                'success' => true,
                'message' => 'Periode anggaran berhasil ditambahkan',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function update_periode(Request $request)
    {
        try {
            $validated = $request->validate([
                'history_id' => 'required|exists:m_budgeds_history,id',
                'period_start_date' => 'required|date',
                'period_end_date' => 'required|date|after_or_equal:period_start_date',
                'budged_limit_value' => 'required|numeric|min:0',
                'budged_remaining_value' => 'required|numeric|min:0',
            ], [], [
                'history_id' => 'ID Periode',
                'period_start_date' => 'Tanggal Mulai Periode',
                'period_end_date' => 'Tanggal Akhir Periode',
                'budged_limit_value' => 'Nilai Limit Anggaran',
                'budged_remaining_value' => 'Nilai Sisa Anggaran',
            ]);

            // Get current period to find budget_id
            $budgedService = app(BudgedService::class);
            $currentPeriod = $budgedService->findHistoryOne($validated['history_id']);

            // Validate period overlap (excluding current period)
            $validation = $budgedService->validatePeriodOverlap(
                $currentPeriod->budged_id,
                $validated['period_start_date'],
                $validated['period_end_date'],
                $validated['history_id']
            );

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $validation['message'],
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            $budgedService->updateHistory($validated['history_id'], $validated);

            return response()->json([
                'success' => true,
                'message' => 'Periode anggaran berhasil diperbarui',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function delete_periode(Request $request)
    {
        try {
            $validated = $request->validate([
                'history_id' => 'required|exists:m_budgeds_history,id',
            ]);

            app(BudgedService::class)->deleteHistory($validated['history_id']);

            return response()->json([
                'success' => true,
                'message' => 'Periode anggaran berhasil dihapus',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function validate_period(Request $request)
    {
        try {
            $validated = $request->validate([
                'budged_id' => 'required|exists:m_budgeds,id',
                'period_start_date' => 'required|date',
                'period_end_date' => 'required|date|after_or_equal:period_start_date',
            ]);

            $result = app(BudgedService::class)->validatePeriodOverlap(
                $validated['budged_id'],
                $validated['period_start_date'],
                $validated['period_end_date']
            );

            return response()->json($result);
        } catch (ValidationException $e) {
            return response()->json([
                'valid' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (\Throwable $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Gagal memvalidasi periode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function get_all_budgets()
    {
        try {
            $budgets = app(BudgedService::class)->findAll();

            return response()->json([
                'success' => true,
                'data' => $budgets,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function get_periods($id)
    {
        try {
            $periods = app(BudgedService::class)->findHistoryByBudgedId($id);

            return response()->json([
                'success' => true,
                'data' => $periods,
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }
}
