<?php

namespace App\Http\Controllers\MasterData;

// Main
use App\Domains\Tax\Actions\CreateTaxAction;
use App\Domains\Tax\Actions\UpdateTaxAction;
use App\Domains\Tax\Services\TaxAuditLogService;
use App\Domains\Tax\Services\TaxService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
// Actions
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
// Services
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Enums
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaxController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
    ];

    private $taxTypes = [
        ['value' => 'PERCENTAGE', 'label' => 'PERCENTAGE - Persentase'],
        ['value' => 'FIXED', 'label' => 'FIXED - Nominal Tetap'],
    ];

    public function index()
    {
        if (! PermissionHelper::hasPermission('master_data_tax', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        $statuses = $this->statuses;
        $taxTypes = $this->taxTypes;
        $permissionData = PermissionHelper::getViewPermissions('master_data_tax');

        return view('pages.master_data.tax.index', array_merge(
            compact('taxTypes', 'statuses'),
            $permissionData
        ));
    }

    public function index_data_table()
    {
        $data = app(TaxService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        if (! PermissionHelper::hasPermission('master_data_tax', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $taxTypes = $this->taxTypes;
        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_tax');

        return view('pages.master_data.tax.add', array_merge([
            'taxTypes' => $taxTypes,
            'statuses' => $statuses,
        ], $permissionData));
    }

    public function save(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_tax', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menyimpan data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:m_taxes,code',
                'name' => 'required|string|max:255',
                'rate' => 'required|numeric|min:0|max:100',
                'type' => [
                    'required',
                    Rule::in(array_column($this->taxTypes, 'value')),
                ],
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'description' => 'nullable|string|max:255',
            ], [], [
                'code' => 'Kode Pajak',
                'name' => 'Nama Pajak',
                'rate' => 'Tarif Pajak',
                'type' => 'Tipe Pajak',
                'status' => 'Status Pajak',
                'description' => 'Deskripsi Pajak',
            ]);

            app(CreateTaxAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAX_SUCCESS->message(),
            ], SuccessCodes::CREATE_TAX_SUCCESS->httpStatus());
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
        if (! PermissionHelper::hasPermission('master_data_tax', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        $tax = app(TaxService::class)->findOne($id);

        if ($tax == null) {
            abort(404);
        }

        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_tax');

        return view('pages.master_data.tax.edit', array_merge([
            'taxTypes' => $this->taxTypes,
            'statuses' => $statuses,
            'tax' => $tax,
        ], $permissionData));
    }

    public function update(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_tax', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_taxes,id',
                'name' => 'required|string|max:255',
                'rate' => 'required|numeric|min:0|max:100',
                'type' => [
                    'required',
                    Rule::in(array_column($this->taxTypes, 'value')),
                ],
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'description' => 'nullable|string|max:255',
            ], [], [
                'id' => 'ID Pajak',
                'name' => 'Nama Pajak',
                'rate' => 'Tarif Pajak',
                'type' => 'Tipe Pajak',
                'status' => 'Status Pajak',
                'description' => 'Deskripsi Pajak',
            ]);

            app(UpdateTaxAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_TAX_SUCCESS->message(),
            ], SuccessCodes::UPDATE_TAX_SUCCESS->httpStatus());
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
        if (! PermissionHelper::hasPermission('master_data_tax', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail data.');
        }

        $tax = app(TaxService::class)->findOne($id);

        if ($tax == null) {
            abort(404);
        }

        $auditLogs = app(TaxAuditLogService::class)->findAllBasedOnTagId($id);
        $permissionData = PermissionHelper::getViewPermissions('master_data_tax');

        return view('pages.master_data.tax.detail', array_merge(
            compact('tax', 'auditLogs'),
            $permissionData
        ));
    }

    public function export()
    {
        if (! PermissionHelper::hasPermission('master_data_tax', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

        $data = [
            ['no' => 1, 'value' => 'Kode Pajak'],
            ['no' => 2, 'value' => 'Nama Pajak'],
            ['no' => 3, 'value' => 'Tarif Pajak'],
            ['no' => 4, 'value' => 'Tipe Pajak'],
            ['no' => 5, 'value' => 'Deskripsi'],
            ['no' => 6, 'value' => 'Status'],
        ];

        return view('pages.master_data.tax.export', ['data' => $data]);
    }

    public function import()
    {
        if (! PermissionHelper::hasPermission('master_data_tax', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.master_data.tax.import', []);
    }
}
