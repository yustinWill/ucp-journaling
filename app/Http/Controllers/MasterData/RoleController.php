<?php

namespace App\Http\Controllers\MasterData;

use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Domains\Role\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index()
    {
        return view('pages.master_data.role.index');
    }

    public function index_data_table()
    {
        $data = app(RoleService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $permissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();

        return view('pages.master_data.role.add', [
            'permissions' => $permissions,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
                'description' => 'required|string|max:255',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'required|exists:permissions,id',
            ], [], [
                'name' => 'Nama Role',
                'description' => 'Deskripsi Role',
                'permissions' => 'Permissions',
                'permissions.*' => 'Permission ID',
            ]);

            // Create role using Spatie
            $role = \Spatie\Permission\Models\Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'guard_name' => 'web',
            ]);

            // Assign permissions to role
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);

            return response()->json([
                'message' => SuccessCodes::CREATE_ROLE_SUCCESS->message(),
            ], SuccessCodes::CREATE_ROLE_SUCCESS->httpStatus());
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
        $role = \Spatie\Permission\Models\Role::find($id);

        if (!$role) {
            abort(404);
        }

        $allPermissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions()->pluck('id')->toArray();

        return view('pages.master_data.role.edit', [
            'role' => $role,
            'permissions' => $allPermissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function detail($id)
    {
        $role = app(RoleService::class)->findOne($id);
        
        if (!$role) {
            abort(404);
        }

        // Get users with this role from model_has_roles table
        $userList = \App\Models\User::whereHas('roles', function($query) use ($id) {
            $query->where('role_id', $id);
        })->get();

        // Get audit logs for this role
        // $auditLogs = \Illuminate\Support\Facades\DB::table('m_role_audit_logs')
        //     ->leftJoin('users', 'm_role_audit_logs.f_user_id', '=', 'users.id')
        //     ->where('m_role_audit_logs.f_role_id', $id)
        //     ->select([
        //         'm_role_audit_logs.id',
        //         'm_role_audit_logs.created_at',
        //         'm_role_audit_logs.f_user_id as user_id',
        //         'users.name as user_name',
        //         'm_role_audit_logs.action_type',
        //         'm_role_audit_logs.description',
        //     ])
        //     ->orderBy('m_role_audit_logs.created_at', 'desc')
        //     ->get();

        // $auditLogType = 'ROLE';

        return view('pages.master_data.role.detail', [
            'role' => $role,
            'userList' => $userList,
            // 'auditLogs' => $auditLogs,
            // 'auditLogType' => $auditLogType,
        ]);
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:roles,id',
                'name' => 'required|string|max:255|unique:roles,name,'.$request->id,
                'description' => 'required|string|max:255',
                'permissions' => 'required|array|min:1',
                'permissions.*' => 'required|exists:permissions,id',
            ], [], [
                'id' => 'ID Role',
                'name' => 'Nama Role',
                'description' => 'Deskripsi Role',
                'permissions' => 'Permissions',
                'permissions.*' => 'Permission ID',
            ]);

            $role = \Spatie\Permission\Models\Role::find($request->id);
            
            // Update role
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            // Sync permissions
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $validated['permissions'])->get();
            $role->syncPermissions($permissions);

            return response()->json([
                'message' => SuccessCodes::UPDATE_ROLE_SUCCESS->message(),
            ], SuccessCodes::UPDATE_ROLE_SUCCESS->httpStatus());
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

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Nama Role'],
            ['no' => 2, 'value' => 'Deskripsi Role'],
            ['no' => 3, 'value' => 'Status Role'],
            ['no' => 4, 'value' => 'Jumlah User'],
            ['no' => 5, 'value' => 'Jumlah Permission'],
            ['no' => 6, 'value' => 'Tanggal Dibuat'],
            ['no' => 7, 'value' => 'Tanggal Diubah'],
        ];
        $selectData = $this->getExportSelectData();

        return view('pages.master_data.role.export', ['data' => $data] + $selectData);
    }

    public function import()
    {
        $selectData = $this->getImportSelectData();

        return view('pages.master_data.role.import', $selectData);
    }

    public function getRolePermissions($roleId)
    {
        $role = \Spatie\Permission\Models\Role::find($roleId);
        
        if (!$role) {
            return response()->json([], 404);
        }

        // Get all permissions for this role
        $rolePermissions = $role->permissions()->pluck('id')->toArray();
        
        // Get all permissions grouped by module name
        $allPermissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();
        
        $grouped = [];
        foreach ($allPermissions as $permission) {
            // Extract module name from permission name (e.g., "user.view" -> "User")
            $parts = explode('.', $permission->name);
            $moduleName = ucfirst($parts[0] ?? 'Other');
            
            if (!isset($grouped[$moduleName])) {
                $grouped[$moduleName] = [];
            }
            
            $granted = in_array($permission->id, $rolePermissions);
            $grouped[$moduleName][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'type' => $parts[1] ?? 'other',
                'granted' => $granted,
            ];
        }
        
        return response()->json($grouped);
    }

    public function getUsersByRole($id)
    {
        $users = \App\Models\User::whereHas('roles', function($query) use ($id) {
            $query->where('role_id', $id);
        })->select('id', 'name', 'email')->get();

        return response()->json($users);
    }
}
