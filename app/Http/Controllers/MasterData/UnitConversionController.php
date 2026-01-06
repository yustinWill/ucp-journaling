<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\Unit\Services\UnitConversionService;
use App\Domains\Unit\Services\UnitService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UnitConversionController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
    ];

    public function index()
    {
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_unit_conversion');

        return view('pages.master_data.unit_conversion.index', array_merge(
            compact('statuses'),
            $permissionData
        ));
    }

    public function index_data_table()
    {
        $data = app(UnitConversionService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $units = app(UnitService::class)->findAll();
        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_unit_conversion');

        return view('pages.master_data.unit_conversion.add', array_merge([
            'units' => $units,
            'statuses' => $statuses,
        ], $permissionData));
    }

    public function save(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menyimpan data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            $validated = $request->validate([
                'from_unit_id' => 'required|exists:m_units,id',
                'to_unit_id' => 'required|exists:m_units,id|different:from_unit_id',
                'conversion_factor' => 'required|numeric|min:0.0001',
                'conversion_formula' => 'nullable|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'notes' => 'nullable|string|max:1000',
            ], [], [
                'from_unit_id' => 'Unit Asal',
                'to_unit_id' => 'Unit Tujuan',
                'conversion_factor' => 'Faktor Konversi',
                'conversion_formula' => 'Formula Konversi',
                'status' => 'Status',
                'notes' => 'Catatan',
            ]);

            // Check for duplicate conversion
            $exists = \DB::table('m_unit_conversions')
                ->where('company_code', session('user_company_code'))
                ->where('f_from_unit_id', $validated['from_unit_id'])
                ->where('f_to_unit_id', $validated['to_unit_id'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Konversi unit ini sudah ada. Silakan edit konversi yang sudah ada.',
                    'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            app(UnitConversionService::class)->create($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_UNIT_CONVERSION_SUCCESS->message(),
            ], SuccessCodes::CREATE_UNIT_CONVERSION_SUCCESS->httpStatus());
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
        } catch (\Exception $e) {
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
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data.');
        }

        $unitConversion = app(UnitConversionService::class)->findOne($id);

        if (! $unitConversion) {
            abort(404, 'Data konversi unit tidak ditemukan.');
        }

        $permissionData = PermissionHelper::getViewPermissions('master_data_unit_conversion');

        return view('pages.master_data.unit_conversion.detail', array_merge([
            'unitConversion' => $unitConversion,
        ], $permissionData));
    }

    public function edit($id)
    {
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        $unitConversion = app(UnitConversionService::class)->findOne($id);

        if (! $unitConversion) {
            abort(404, 'Data konversi unit tidak ditemukan.');
        }

        $units = app(UnitService::class)->findAll();
        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_unit_conversion');

        return view('pages.master_data.unit_conversion.edit', array_merge([
            'unitConversion' => $unitConversion,
            'units' => $units,
            'statuses' => $statuses,
        ], $permissionData));
    }

    public function update(Request $request, $id)
    {
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            $validated = $request->validate([
                'from_unit_id' => 'required|exists:m_units,id',
                'to_unit_id' => 'required|exists:m_units,id|different:from_unit_id',
                'conversion_factor' => 'required|numeric|min:0.0001',
                'conversion_formula' => 'nullable|string|max:255',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'notes' => 'nullable|string|max:1000',
            ], [], [
                'from_unit_id' => 'Unit Asal',
                'to_unit_id' => 'Unit Tujuan',
                'conversion_factor' => 'Faktor Konversi',
                'conversion_formula' => 'Formula Konversi',
                'status' => 'Status',
                'notes' => 'Catatan',
            ]);

            // Check for duplicate conversion (excluding current)
            $exists = \DB::table('m_unit_conversions')
                ->where('company_code', session('user_company_code'))
                ->where('f_from_unit_id', $validated['from_unit_id'])
                ->where('f_to_unit_id', $validated['to_unit_id'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Konversi unit ini sudah ada untuk kombinasi unit tersebut.',
                    'error' => ErrorCodes::FORM_VALIDATION_ERROR->code(),
                ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
            }

            app(UnitConversionService::class)->update($id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_UNIT_CONVERSION_SUCCESS->message(),
            ], SuccessCodes::UPDATE_UNIT_CONVERSION_SUCCESS->httpStatus());
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
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function delete($id)
    {
        if (! PermissionHelper::hasPermission('master_data_unit_conversion', 'delete')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menghapus data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

        try {
            app(UnitConversionService::class)->delete($id);

            return response()->json([
                'message' => SuccessCodes::DELETE_UNIT_CONVERSION_SUCCESS->message(),
            ], SuccessCodes::DELETE_UNIT_CONVERSION_SUCCESS->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }
}
