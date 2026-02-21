<?php

namespace App\Http\Controllers\MasterData;

// Main
use App\Domains\Category\Actions\CreateCategoryAction;
use App\Domains\Category\Actions\UpdateCategoryAction;
use App\Domains\Category\Services\CategoryAuditLogService;
use App\Domains\Category\Services\CategoryService;
use App\Enums\AuditLogTypes;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
// Actions
use App\Helpers\DiscordNotifier;
// Services
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
// Enums
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
    ];

    private $categoryTypes = [
        ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
        ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
        ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
        ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Perawatan'],
    ];

    public function index()
    {
        $statuses = $this->statuses;
        $categoryTypes = $this->categoryTypes;
        $permissionData = PermissionHelper::getViewPermissions('master_data_category');

        return view('pages.master_data.category.index', array_merge(
            compact('categoryTypes', 'statuses'),
            $permissionData
        ));
    }

    public function index_data_table()
    {
        $data = app(CategoryService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $categoryTypes = $this->categoryTypes;
        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_product_category');

        // Map permission for blade compatibility
        $canWrite = $permissionData['canCreateProductCategory'] ?? false;

        return view('pages.master_data.category.add', array_merge([
            'categoryTypes' => $categoryTypes,
            'statuses' => $statuses,
            'canWrite' => $canWrite,
        ], $permissionData));
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:m_categories,code',
                'name' => 'required|string|max:255',
                'type' => [
                    'required',
                    Rule::in(array_column($this->categoryTypes, 'value')),
                ],
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'description' => 'nullable|string|max:255',
            ], [], [
                'code' => 'Kode Kategori',
                'name' => 'Nama Kategori',
                'type' => 'Tipe Kategori',
                'status' => 'Status Kategori',
                'description' => 'Deskripsi Kategori',
            ]);

            app(CreateCategoryAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_CATEGORY_SUCCESS->message(),
            ], SuccessCodes::CREATE_CATEGORY_SUCCESS->httpStatus());
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

    public function edit($id)
    {
        $category = app(CategoryService::class)->findOne($id);

        if ($category == null) {
            abort(404);
        }

        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_product_category');

        // Map permission for blade compatibility
        $canWrite = $permissionData['canEditProductCategory'] ?? false;

        return view('pages.master_data.category.edit', array_merge([
            'categoryTypes' => $this->categoryTypes,
            'statuses' => $statuses,
            'category' => $category,
            'canWrite' => $canWrite,
        ], $permissionData));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_categories,id',
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'type' => [
                    'required',
                    Rule::in(array_column($this->categoryTypes, 'value')),
                ],
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'description' => 'nullable|string|max:255',
            ], [], [
                'id' => 'ID Kategori',
                'code' => 'Kode Kategori',
                'name' => 'Nama Kategori',
                'type' => 'Tipe Kategori',
                'status' => 'Status Kategori',
                'description' => 'Deskripsi Kategori',
            ]);

            app(UpdateCategoryAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_CATEGORY_SUCCESS->message(),
            ], SuccessCodes::UPDATE_CATEGORY_SUCCESS->httpStatus());
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

    public function detail($id)
    {
        $category = app(CategoryService::class)->findOne($id);

        if ($category == null) {
            abort(404);
        }

        // Convert to array format expected by the view
        $categoryArray = [
            'id' => $category->id,
            'category_code' => $category->code,
            'category_name' => $category->name,
            'category_type' => $category->type,
            'description' => $category->description,
            'status' => $category->status,
            'product_count' => rand(0, 50), // TODO: Calculate actual product count
            'last_updated' => $category->updated_at,
            'created_at' => $category->created_at,
        ];

        $auditLogs = app(CategoryAuditLogService::class)->findAllBasedOnTagId($id);
        $permissionData = PermissionHelper::getViewPermissions('master_data_product_category');

        // Map permission variables for blade template compatibility
        $canShowEditButton = $permissionData['canEditProductCategory'] ?? false;
        $canShowDeleteButton = $permissionData['canDeleteProductCategory'] ?? false;

        return view('pages.master_data.category.detail', array_merge([
            'category' => $categoryArray,
            'auditLogs' => $auditLogs,
            'canShowEditButton' => $canShowEditButton,
            'canShowDeleteButton' => $canShowDeleteButton,
        ], ['auditLogType' => AuditLogTypes::MASTER_DATA_CATEGORY->code()], $permissionData));
    }

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Kode Kategori'],
            ['no' => 2, 'value' => 'Nama Kategori'],
            ['no' => 3, 'value' => 'Tipe Kategori'],
            ['no' => 4, 'value' => 'Deskripsi'],
            ['no' => 5, 'value' => 'Jumlah Produk'],
            ['no' => 6, 'value' => 'Status'],
        ];

        return view('pages.master_data.category.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.category.import', []);
    }
}
