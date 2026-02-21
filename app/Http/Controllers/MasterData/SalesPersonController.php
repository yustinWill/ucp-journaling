<?php

namespace App\Http\Controllers\MasterData;

// Main
use App\Domains\Contact\Services\ContactService;
use App\Domains\SalesPerson\Actions\CreateSalesPersonAction;
use App\Domains\SalesPerson\Actions\UpdateSalesPersonAction;
use App\Domains\SalesPerson\Services\SalesPersonAuditLogService;
use App\Domains\SalesPerson\Services\SalesPersonService;
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

class SalesPersonController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
    ];

    public function index()
    {
        $statuses = $this->statuses;
        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        return view('pages.master_data.sales_person.index', array_merge(
            compact('statuses'),
            $permissionData
        ));
    }

    public function index_data_table()
    {
        $data = app(SalesPersonService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $statuses = $this->statuses;
        $employees = app(ContactService::class)->findAllByFlag('employee');
        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        // Map permission for blade compatibility
        $canWrite = $permissionData['canCreateSalesPerson'] ?? false;

        return view('pages.master_data.sales_person.add', array_merge([
            'statuses' => $statuses,
            'employees' => $employees,
            'canWrite' => $canWrite,
        ], $permissionData));
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'f_employee_contact_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) {
                        if (! app(SalesPersonService::class)->isEmployeeAvailable($value)) {
                            $fail('Karyawan ini sudah terdaftar sebagai sales person.');
                        }
                    },
                ],
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'commission_rate' => 'required|numeric|min:0|max:100',
                'description' => 'nullable|string|max:500',
            ], [], [
                'f_employee_contact_id' => 'Karyawan',
                'status' => 'Status Sales Person',
                'commission_rate' => 'Komisi (%)',
                'description' => 'Keterangan',
            ]);

            app(CreateSalesPersonAction::class)->execute($validated);

            return response()->json([
                'message' => 'Sales person berhasil dibuat',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
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
        $salesPerson = app(SalesPersonService::class)->findOne($id);

        if ($salesPerson == null) {
            abort(404);
        }

        $statuses = $this->statuses;
        $employees = app(ContactService::class)->findAllByFlag('employee');
        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        // Map permission for blade compatibility
        $canWrite = $permissionData['canEditSalesPerson'] ?? false;

        return view('pages.master_data.sales_person.edit', array_merge([
            'statuses' => $statuses,
            'employees' => $employees,
            'salesPerson' => $salesPerson,
            'canWrite' => $canWrite,
        ], $permissionData));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:m_sales_persons,id',
                'f_sales_person_code' => 'required|string|max:255',
                'f_employee_contact_id' => [
                    'required',
                    'integer',
                    function ($attribute, $value, $fail) use ($request) {
                        if (! app(SalesPersonService::class)->isEmployeeAvailable($value, $request->id)) {
                            $fail('Karyawan ini sudah terdaftar sebagai sales person.');
                        }
                    },
                ],
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value')),
                ],
                'commission_rate' => 'required|numeric|min:0|max:100',
                'description' => 'nullable|string|max:500',
            ], [], [
                'id' => 'ID Sales Person',
                'f_sales_person_code' => 'Kode Sales Person',
                'f_employee_contact_id' => 'Karyawan',
                'status' => 'Status Sales Person',
                'commission_rate' => 'Komisi (%)',
                'description' => 'Keterangan',
            ]);

            app(UpdateSalesPersonAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => 'Sales person berhasil diperbarui',
            ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
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
        $salesPerson = app(SalesPersonService::class)->findOne($id);

        if ($salesPerson == null) {
            abort(404);
        }

        $auditLogs = app(SalesPersonAuditLogService::class)->findAllBasedOnTagId($id);

        // Convert to array for consistency
        $salesPersonArray = (array) $salesPerson;

        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        // Map permission variables for blade template compatibility
        $canShowEditButton = $permissionData['canEditSalesPerson'] ?? false;
        $canShowDeleteButton = $permissionData['canDeleteSalesPerson'] ?? false;

        return view('pages.master_data.sales_person.detail', array_merge([
            'salesPerson' => $salesPersonArray,
            'auditLogs' => $auditLogs,
            'auditLogType' => AuditLogTypes::MASTER_DATA_SALES_PERSON->code(),
            'canShowEditButton' => $canShowEditButton,
            'canShowDeleteButton' => $canShowDeleteButton,
        ], $permissionData));
    }

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Kode Sales Person'],
            ['no' => 2, 'value' => 'Nama Karyawan'],
            ['no' => 3, 'value' => 'Email'],
            ['no' => 4, 'value' => 'Telepon'],
            ['no' => 5, 'value' => 'Komisi (%)'],
            ['no' => 6, 'value' => 'Status'],
            ['no' => 7, 'value' => 'Keterangan'],
        ];

        return view('pages.master_data.sales_person.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.sales_person.import', []);
    }
}
