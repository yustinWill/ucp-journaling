<?php

namespace App\Http\Controllers\MasterData;

// Main
use App\Domains\Role\Services\RoleService;
use App\Domains\Tag\Services\TagService;
use App\Domains\User\Actions\CreateUserAction;
use App\Domains\User\Actions\DeleteUserAction;
use App\Domains\User\Actions\UpdateUserAction;
use App\Domains\User\Services\UserAuditLogService;
use App\Domains\User\Services\UserService;
use App\Enums\AuditLogTypes;
use App\Enums\ErrorCodes;
// Actions
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
// Services
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
// Enums
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
        ['value' => 'DELETED', 'label' => 'DELETED - Dihapus'],
    ];

    public function index()
    {
        $statuses = $this->statuses;
        $roles = app(RoleService::class)->findAll();
        $permissionData = PermissionHelper::getViewPermissions('master_data_user');

        return view('pages.master_data.user.index', array_merge(compact('statuses', 'roles'), $permissionData));
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

        // Permission checks for add buttons
        $canAddTags = true;
        $canAddRoles = true;

        return view('pages.master_data.user.add', [
            'roles' => $roles,
            'tags' => $tags,
            'canAddTags' => $canAddTags,
            'canAddRoles' => $canAddRoles,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'phone' => 'nullable|string|max:255|unique:users,phone',
                'role' => 'required|exists:roles,id',
                'status' => [
                    'required',
                    Rule::in(['ACTIVE', 'INACTIVE']),
                ],
                'password' => 'required|string|min:8|max:50|same:password_confirmation',
                'password_confirmation' => 'required|string|min:8|max:50',
                // 'tags' => 'nullable|array',
            ], [], [
                'name' => 'Nama User',
                'email' => 'Email User',
                'phone' => 'Nomor Telepon',
                'role' => 'Role',
                'status' => 'Status User',
                'password' => 'Password',
                'password_confirmation' => 'Konfirmasi Password',
                // 'tags' => 'Tag',
            ]);

            app(CreateUserAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_USER_SUCCESS->message(),
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
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function edit($id)
    {
        $user = app(UserService::class)->findOne($id);

        if ($user == null || $user->deleted_at !== null) {
            abort(404);
        }

        $roles = app(RoleService::class)->findAll();
        $tags = app(TagService::class)->findAll();

        // Permission checks for add buttons
        $canAddTags = true;
        $canAddRoles = true;

        return view('pages.master_data.user.edit', [
            'user' => $user,
            'roles' => $roles,
            'tags' => $tags,
            'canAddTags' => $canAddTags,
            'canAddRoles' => $canAddRoles,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => [
                    'required',
                    Rule::exists('users', 'id')->whereNull('deleted_at'),
                ],
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$request->id,
                'phone' => 'nullable|string|max:255|unique:users,phone,'.$request->id,
                'role' => 'required|exists:roles,id',
                'status' => [
                    'required',
                    Rule::in(['ACTIVE', 'INACTIVE']),
                ],
                'password' => 'nullable|string|min:8|max:50|same:password_confirmation',
                'password_confirmation' => 'nullable|string|min:8|max:50',
                'tags' => 'nullable|array',
            ], [], [
                'id' => 'ID User',
                'name' => 'Nama User',
                'email' => 'Email User',
                'phone' => 'Nomor Telepon',
                'role' => 'Role',
                'status' => 'Status User',
                'password' => 'Password',
                'password_confirmation' => 'Konfirmasi Password',
                'tags' => 'Tag',
            ]);

            app(UpdateUserAction::class)->execute($request->id, $validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_USER_SUCCESS->message(),
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
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function delete(Request $request)
    {
        if (! $request->user()->can('user.delete')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menghapus user.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'id' => [
                    'required',
                    Rule::exists('users', 'id')->whereNull('deleted_at'),
                ],
            ], [], ['id' => 'ID User']);

            if ((int) $validated['id'] === (int) $request->user()->id) {
                throw ValidationException::withMessages([
                    'id' => 'Anda tidak dapat menghapus akun yang sedang digunakan.',
                ]);
            }

            app(DeleteUserAction::class)->execute((int) $validated['id']);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dinonaktifkan. Data user tetap tersimpan.',
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

        return view('pages.master_data.user.detail', compact('user', 'auditLogs'), [
            'auditLogType' => AuditLogTypes::MASTER_DATA_USER->code(),
        ]);
    }

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Nama User'],
            ['no' => 2, 'value' => 'Email User'],
            ['no' => 3, 'value' => 'Nomor Telepon'],
            ['no' => 4, 'value' => 'Role'],
            ['no' => 5, 'value' => 'Terakhir Login'],
            ['no' => 6, 'value' => 'Password Terakhir Dirubah'],
            ['no' => 7, 'value' => 'Status'],
        ];

        return view('pages.master_data.user.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.user.import', []);
    }
}
