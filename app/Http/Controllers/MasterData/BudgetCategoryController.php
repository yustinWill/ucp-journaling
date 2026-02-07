<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\BudgetCategory\Services\BudgetCategoryService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BudgetCategoryController extends Controller
{
    public function index()
    {
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
        ];
        return view('pages.master_data.budget_category.index', $permissionData);
    }

    public function index_data_table()
    {
        $data = app(BudgetCategoryService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        return view('pages.master_data.budget_category.add');
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_name' => 'required|string|max:255|unique:m_budget_categories,category_name',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ], [], [
                'category_name' => 'Nama Kategori',
                'description' => 'Deskripsi',
                'status' => 'Status',
            ]);

            app(BudgetCategoryService::class)->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategori anggaran berhasil ditambahkan',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
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

    public function edit($id)
    {
        $category = app(BudgetCategoryService::class)->findById($id);
        $canWrite = true;

        return view('pages.master_data.budget_category.edit', compact('category', 'canWrite'));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_budget_categories,id',
                'category_name' => 'required|string|max:255|unique:m_budget_categories,category_name,' . $request->id,
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
            ], [], [
                'id' => 'ID',
                'category_name' => 'Nama Kategori',
                'description' => 'Deskripsi',
                'status' => 'Status',
            ]);

            app(BudgetCategoryService::class)->update($validated['id'], $validated);

            return response()->json([
                'success' => true,
                'message' => 'Kategori anggaran berhasil diperbarui',
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

    public function delete(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_budget_categories,id',
            ]);

            app(BudgetCategoryService::class)->delete($validated['id']);

            return response()->json([
                'success' => true,
                'message' => 'Kategori anggaran berhasil dihapus',
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

    public function get_all()
    {
        try {
            $categories = app(BudgetCategoryService::class)->findAllActive();

            return response()->json([
                'success' => true,
                'data' => $categories,
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
