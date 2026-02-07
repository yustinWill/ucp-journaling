<?php

namespace App\Http\Controllers\MasterData;

// Main
use App\Domains\Unit\Actions\CreateUnitAction;
use App\Domains\Unit\Actions\UpdateUnitAction;
use App\Domains\Unit\Services\UnitAuditLogService;
use App\Domains\Unit\Services\UnitService;
use App\Domains\Unit\Services\UnitTypeService;
use App\Enums\AuditLogTypes;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
// Actions
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
// Services
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
// Enums
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
    ];

    public function index()
    {
        $statuses = $this->statuses;
        $unitTypes = app(UnitTypeService::class)->findAll();

        return view('pages.master_data.unit.index', array_merge(
            compact('unitTypes', 'statuses'),
            $permissionData
        ));
    }

    public function index_data_table()
    {
        $data = app(UnitService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $unitTypes = app(UnitTypeService::class)->findAll();
        $statuses = $this->statuses;

        return view('pages.master_data.unit.add', array_merge([
            'unitTypes' => $unitTypes,
            'statuses' => $statuses,
        ]));
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:m_units,code',
                'name' => 'required|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'type' => 'required|exists:m_unit_types,id',
                'description' => 'nullable|string|max:255',
            ], [], [
                'code' => 'Kode Unit',
                'name' => 'Nama Unit',
                'status' => 'Status Unit',
                'type' => 'Tipe Unit',
                'description' => 'Deskripsi Unit',
            ]);

            app(CreateUnitAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_UNIT_SUCCESS->message(),
            ], SuccessCodes::CREATE_UNIT_SUCCESS->httpStatus());
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
        $unit = app(UnitService::class)->findOne($id);

        if ($unit == null) {
            abort(404);
        }

        $unitTypes = app(UnitTypeService::class)->findAll();
        $statuses = $this->statuses;

        return view('pages.master_data.unit.edit', array_merge([
            'unitTypes' => $unitTypes,
            'statuses' => $statuses,
            'unit' => $unit,
        ]));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_units,id',
                'code' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'type' => 'required|exists:m_unit_types,id',
                'description' => 'nullable|string|max:255',
            ], [], [
                'id' => 'ID Unit',
                'code' => 'Kode Unit',
                'name' => 'Nama Unit',
                'status' => 'Status Unit',
                'type' => 'Tipe Unit',
                'description' => 'Deskripsi Unit',
            ]);

            app(UpdateUnitAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_UNIT_SUCCESS->message(),
            ], SuccessCodes::UPDATE_UNIT_SUCCESS->httpStatus());
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
        $unit = app(UnitService::class)->findOne($id);

        if ($unit == null) {
            abort(404);
        }

        $auditLogs = app(UnitAuditLogService::class)->findAllBasedOnTagId($id);

        return view('pages.master_data.unit.detail', array_merge(
            compact('unit', 'auditLogs'),
            ['auditLogType' => AuditLogTypes::MASTER_DATA_UNIT->code()]
        ));
    }

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Kode Unit'],
            ['no' => 2, 'value' => 'Nama Unit'],
            ['no' => 3, 'value' => 'Tipe Unit'],
            ['no' => 4, 'value' => 'Deskripsi'],
            ['no' => 5, 'value' => 'Status'],
        ];

        return view('pages.master_data.unit.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.unit.import', []);
    }
}
