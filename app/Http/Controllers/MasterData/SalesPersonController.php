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
use App\Helpers\PermissionHelper;
// Services
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
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat halaman ini.');
        }

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
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        }

        $statuses = $this->statuses;
        $employees = app(ContactService::class)->findAllByFlag('employee');
        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        return view('pages.master_data.sales_person.add', array_merge([
            'statuses' => $statuses,
            'employees' => $employees,
        ], $permissionData));
    }

    public function save(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk menyimpan data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

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
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data.');
        }

        $salesPerson = app(SalesPersonService::class)->findOne($id);

        if ($salesPerson == null) {
            abort(404);
        }

        $statuses = $this->statuses;
        $employees = app(ContactService::class)->findAllByFlag('employee');
        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        return view('pages.master_data.sales_person.edit', array_merge([
            'statuses' => $statuses,
            'employees' => $employees,
            'salesPerson' => $salesPerson,
        ], $permissionData));
    }

    public function update(Request $request)
    {
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'write')) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk mengupdate data.',
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], 403);
        }

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
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail data.');
        }

        $salesPerson = app(SalesPersonService::class)->findOne($id);

        if ($salesPerson == null) {
            abort(404);
        }

        $auditLogs = app(SalesPersonAuditLogService::class)->findAllBasedOnTagId($id);
        $permissionData = PermissionHelper::getViewPermissions('master_data_sales_person');

        // Convert to array for consistency
        $salesPersonArray = (array) $salesPerson;

        return view('pages.master_data.sales_person.detail', array_merge([
            'salesPerson' => $salesPersonArray,
            'auditLogs' => $auditLogs,
            'auditLogType' => AuditLogTypes::MASTER_DATA_SALES_PERSON->code(),
        ], $permissionData));
    }

    public function export()
    {
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'read')) {
            abort(403, 'Anda tidak memiliki akses untuk export data.');
        }

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
        if (! PermissionHelper::hasPermission('master_data_sales_person', 'write')) {
            abort(403, 'Anda tidak memiliki akses untuk import data.');
        }

        return view('pages.master_data.sales_person.import', []);
    }
}
