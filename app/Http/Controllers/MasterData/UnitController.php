<?php

namespace App\Http\Controllers\MasterData;

//Main
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;

//Actions
use App\Domains\Unit\Actions\CreateUnitAction;
use App\Domains\Unit\Actions\UpdateUnitAction;
//Services
use App\Domains\Unit\Services\UnitService;
use App\Domains\Unit\Services\UnitAuditLogService;
use App\Domains\Unit\Services\UnitTypeService;
//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;

class UnitController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif']
    ];

    public function index()
    {
        $statuses = $this->statuses;
        $unitTypes = app(UnitTypeService::class)->findAll();

        return view('pages.master_data.unit.index', compact('unitTypes', 'statuses'));
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

        return view('pages.master_data.unit.add', [
            'unitTypes' => $unitTypes,
            'statuses' => $statuses
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:m_units,code',
                'name' => 'required|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value'))
                ],
                'symbol' => 'required|string|max:255',
                'type' => 'required|exists:m_unit_types,id',
                'description' => 'nullable|string|max:255',
            ]);

            app(CreateUnitAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_TAG_SUCCESS->message()
            ], SuccessCodes::CREATE_TAG_SUCCESS->httpStatus());
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

    public function edit($id)
    {
        $unit = app(UnitService::class)->findOne($id);

        if ($unit == null) {
            abort(404);
        }

        $unitTypes = app(UnitTypeService::class)->findAll();
        $statuses = $this->statuses;

        return view('pages.master_data.unit.edit', [
            'unitTypes' => $unitTypes,
            'statuses' => $statuses,
            'unit' => $unit
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_units,id',
                'name' => 'required|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value'))
                ],
                'symbol' => 'required|string|max:255',
                'type' => 'required|exists:m_unit_types,id',
                'description' => 'nullable|string|max:255',
            ]);

            app(UpdateUnitAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_UNIT_SUCCESS->message()
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

        return view('pages.master_data.unit.detail', compact('unit', 'auditLogs'));
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Kode Unit"],
            ["no" => 2, "value" => "Nama Unit"],
            ["no" => 3, "value" => "Simbol Unit"],
            ["no" => 4, "value" => "Tipe Unit"],
            ["no" => 5, "value" => "Deskripsi"],
            ["no" => 6, "value" => "Status"]
        ];
        return view('pages.master_data.unit.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.unit.import', []);
    }
}
