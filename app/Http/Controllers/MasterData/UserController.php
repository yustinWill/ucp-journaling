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
use App\Domains\User\Actions\CreateUserAction;
use App\Domains\User\Actions\UpdateUserAction;
//Services
use App\Domains\User\Services\UserService;
use App\Domains\User\Services\UserAuditLogService;
use App\Domains\Role\Services\RoleService;
use App\Domains\Tag\Services\TagService;
//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;

class UserController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif']
    ];

    public function index()
    {
        $statuses = $this->statuses;
        $roles = app(RoleService::class)->findAll();
        return view('pages.master_data.user.index', compact('statuses', 'roles'));
    }

    public function index_data_table()
    {
        $data = app(UserService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $roles = app(RoleService::class)->findAll();

        $tags = app(TagService::class)->findAll();

        return view('pages.master_data.user.add', [
            'roles' => $roles,
            'tags' => $tags,
            'canAddTags' => true
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone' => 'required|string|max:255|unique:users,phone',
                'role' => 'required|exists:m_tags,id',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value'))
                ],
                'password' => 'required|string|min:8|max:50|same:password_confirmation',
                'password_confirmation' => 'required|string|min:8|max:50',
                'tags' => 'nullable|array',
            ]);

            app(CreateUserAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_USER_SUCCESS->message()
            ], SuccessCodes::CREATE_USER_SUCCESS->httpStatus());
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
        $user = app(UserService::class)->findOne($id);

        if ($user == null) {
            abort(404);
        }

        $roles = app(RoleService::class)->findAll();

        $tags = app(TagService::class)->findAll();
        $canAddTags = true;

        return view('pages.master_data.user.edit', compact('user', 'roles', 'tags', 'canAddTags'));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
                'phone' => 'required|string|max:255|unique:users,phone,' . $request->id,
                'role' => 'required|exists:m_tags,id',
                'status' => [
                    'required',
                    Rule::in(array_column($this->statuses, 'value'))
                ],
                'password' => 'nullable|string|min:8|max:50|same:password_confirmation',
                'password_confirmation' => 'nullable|string|min:8|max:50',
                'tags' => 'nullable|array',
            ]);

            app(UpdateUserAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_USER_SUCCESS->message()
            ], SuccessCodes::UPDATE_USER_SUCCESS->httpStatus());
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
        $user = app(UserService::class)->findOne($id);

        if ($user == null) {
            abort(404);
        }

        $auditLogs = app(UserAuditLogService::class)->findAllBasedOnId($id);
        return view('pages.master_data.user.detail', compact('user', 'auditLogs'));
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Nama User"],
            ["no" => 2, "value" => "Email User"],
            ["no" => 3, "value" => "Nomor Telepon"],
            ["no" => 4, "value" => "Role"],
            ["no" => 5, "value" => "Terakhir Login"],
            ["no" => 6, "value" => "Password Terakhir Dirubah"],
            ["no" => 7, "value" => "Status"],
        ];
        return view('pages.master_data.user.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.user.import', []);
    }

    /**
     * Get mock role select data (no DB connection)
     */
    private function getMockRoleSelectData()
    {
        return [
            ['value' => 'SUPER_ADMIN', 'label' => 'SUPER_ADMIN - Super Administrator'],
            ['value' => 'MANAGER_PRODUKSI', 'label' => 'MANAGER_PRODUKSI - Manager Produksi'],
            ['value' => 'STAFF_KEUANGAN', 'label' => 'STAFF_KEUANGAN - Staff Keuangan'],
            ['value' => 'SUPERVISOR_QC', 'label' => 'SUPERVISOR_QC - Supervisor Quality Control'],
            ['value' => 'ADMIN_GUDANG', 'label' => 'ADMIN_GUDANG - Administrator Gudang'],
            ['value' => 'OPERATOR_PRODUKSI', 'label' => 'OPERATOR_PRODUKSI - Operator Produksi'],
            ['value' => 'STAFF_PENJUALAN', 'label' => 'STAFF_PENJUALAN - Staff Penjualan']
        ];
    }
}
