<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\PermissionHelper;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;

//Services
use App\Domains\Role\Services\RoleService;
use App\Domains\User\Services\UserService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Permission\Services\PermissionService;
use App\Domains\Module\Services\ModuleService;

//Actions
use App\Domains\Role\Actions\CreateRoleAction;
use App\Domains\Role\Actions\UpdateRoleAction;
use App\Domains\Role\Services\RoleAuditLogService;
//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;

class RoleController extends Controller
{
    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif']
    ];

    private $permission_types =  [
        ['value' => 'read', 'label' => 'Read'],
        ['value' => 'write', 'label' => 'Write'],
        ['value' => 'delete', 'label' => 'Delete'],
        ['value' => 'approval', 'label' => 'Approval'],
    ];

    public function index()
    {
        $selectData = $this->getIndexSelectData();
        return view('pages.master_data.role.index', $selectData);
    }

    public function index_data_table()
    {
        $data = app(RoleService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $roles = app(RoleService::class)->findAll();
        $tags = app(TagService::class)->findAll();
        $modules = app(ModuleService::class)->findAll();
        $permissions = app(PermissionService::class)->findAll();

        // Permission checks for add buttons
        $canAddTags = PermissionHelper::canWrite('master_data_tag');
        $canAddRoles = PermissionHelper::canWrite('master_data_role');

        return view('pages.master_data.role.add', [
            'roles' => $roles,
            'tags' => $tags,
            'canAddTags' => $canAddTags,
            'canAddRoles' => $canAddRoles,
            'statuses' => $this->statuses,
            'permission_types' => $this->permission_types,
            'modules' => $modules,
            'permissions' => $permissions
        ]);
    }

    public function save(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|string|in:ACTIVE,INACTIVE',
                'description' => 'required|string|max:255',
                'tags' => 'nullable|array',
                'permissions' => 'required|array',
                'permissions.*.module_id' => 'required|integer|exists:modules,id',
                'permissions.*.permission_type' => [
                    'required',
                    Rule::in(['read', 'write', 'delete', 'approval']),
                ],
            ]);

            app(CreateRoleAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_ROLE_SUCCESS->message()
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
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function edit($id)
    {
        // Mock role data - will be replaced with actual database queries
        $role = [
            'id' => $id,
            'name' => 'Admin',
            'description' => 'Administrative access with limited permissions',
            'is_active' => true,
            'permissions' => [1, 2, 3, 5, 6, 7, 9, 10] // Mock selected permission IDs
        ];

        $selectData = $this->getSelectData();
        return view('pages.master_data.role.edit', compact('role') + $selectData);
    }

    public function detail($id)
    {
        $role = app(RoleService::class)->findOne($id);

        if ($role == null) {
            abort(404);
        }

        $auditLogs = app(RoleAuditLogService::class)->findAllBasedOnId($id);
        $userList = app(UserService::class)->findAllBasedOnRole($id);

        $selectData = $this->getSelectData();
        return view('pages.master_data.role.detail', compact('role', 'auditLogs', 'userList'));
    }

    public function store(Request $request)
    {
        // TODO: Implement role creation
        return response()->json(['message' => 'Role created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement role update
        return response()->json(['message' => 'Role updated successfully'], 200);
    }

    public function destroy($id)
    {
        try {
            // Check if role exists
            $role = DB::table('roles')->where('id', $id)->first();
            if (!$role) {
                return response()->json(['message' => 'Role not found'], 404);
            }

            // Check if role has users assigned
            $userCount = DB::table('users')->where('role_id', $id)->count();
            if ($userCount > 0) {
                return response()->json([
                    'message' => 'Cannot delete role. There are ' . $userCount . ' user(s) still assigned to this role.',
                    'error' => 'role_has_users'
                ], 422);
            }

            // Delete role permissions first
            DB::table('role_permissions')->where('role_id', $id)->delete();

            // Delete the role
            DB::table('roles')->where('id', $id)->delete();

            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Nama Role"],
            ["no" => 2, "value" => "Deskripsi Role"],
            ["no" => 3, "value" => "Status Role"],
            ["no" => 4, "value" => "Jumlah User"],
            ["no" => 5, "value" => "Jumlah Permission"],
            ["no" => 6, "value" => "Tanggal Dibuat"],
            ["no" => 7, "value" => "Tanggal Diubah"],
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
        $permissions = app(PermissionService::class)->findAllBasedOnRole($roleId);

        $groupedData = [];
        foreach ($permissions as $perm) {
            $groupedData[$perm->module_name][] = [
                'id' => $perm->id,
                'name' => $perm->module_name . ' - ' . $perm->type,
                'type' => $perm->type,
                'granted' => (bool) $perm->granted,
            ];
        }

        return response()->json($groupedData);
    }

    public function getUsersByRole($id)
    {
        // Mock users data for the role
        $users = [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com'],
            ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com'],
        ];

        return response()->json($users);
    }

    private function getSelectData()
    {
        return [
            'modules' => [
                ['id' => 1, 'name' => 'User Management', 'slug' => 'user'],
                ['id' => 2, 'name' => 'Contact Management', 'slug' => 'contact'],
                ['id' => 3, 'name' => 'Product Management', 'slug' => 'product'],
                ['id' => 4, 'name' => 'Sales Management', 'slug' => 'sales'],
                ['id' => 5, 'name' => 'Purchase Management', 'slug' => 'purchase'],
                ['id' => 6, 'name' => 'Journal Management', 'slug' => 'journal'],
                ['id' => 7, 'name' => 'Expense Management', 'slug' => 'expense'],
                ['id' => 8, 'name' => 'COA Management', 'slug' => 'coa'],
            ],
            'permissions' => [
                ['id' => 1, 'module_id' => 1, 'name' => 'User Read', 'type' => 'read'],
                ['id' => 2, 'module_id' => 1, 'name' => 'User Write', 'type' => 'write'],
                ['id' => 3, 'module_id' => 1, 'name' => 'User Delete', 'type' => 'delete'],
                ['id' => 4, 'module_id' => 1, 'name' => 'User Approval', 'type' => 'approval'],
                ['id' => 5, 'module_id' => 2, 'name' => 'Contact Read', 'type' => 'read'],
                ['id' => 6, 'module_id' => 2, 'name' => 'Contact Write', 'type' => 'write'],
                ['id' => 7, 'module_id' => 2, 'name' => 'Contact Delete', 'type' => 'delete'],
                ['id' => 8, 'module_id' => 2, 'name' => 'Contact Approval', 'type' => 'approval'],
                ['id' => 9, 'module_id' => 3, 'name' => 'Product Read', 'type' => 'read'],
                ['id' => 10, 'module_id' => 3, 'name' => 'Product Write', 'type' => 'write'],
                ['id' => 11, 'module_id' => 3, 'name' => 'Product Delete', 'type' => 'delete'],
                ['id' => 12, 'module_id' => 3, 'name' => 'Product Approval', 'type' => 'approval'],
                ['id' => 13, 'module_id' => 4, 'name' => 'Sales Read', 'type' => 'read'],
                ['id' => 14, 'module_id' => 4, 'name' => 'Sales Write', 'type' => 'write'],
                ['id' => 15, 'module_id' => 4, 'name' => 'Sales Delete', 'type' => 'delete'],
                ['id' => 16, 'module_id' => 4, 'name' => 'Sales Approval', 'type' => 'approval'],
            ],
            'permission_types' => [
                ['value' => 'read', 'label' => 'Read'],
                ['value' => 'write', 'label' => 'Write'],
                ['value' => 'delete', 'label' => 'Delete'],
                ['value' => 'approval', 'label' => 'Approval'],
            ],
        ];
    }

    private function getIndexSelectData()
    {
        return [
            'statusFilters' => [
                ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
                ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif'],
            ],
            'userFilters' => [
                ['value' => '0', 'label' => '0 - Tanpa User'],
                ['value' => '1-5', 'label' => '1-5 - Satu hingga Lima User'],
                ['value' => '6-10', 'label' => '6-10 - Enam hingga Sepuluh User'],
                ['value' => '10+', 'label' => '10+ - Lebih dari Sepuluh User'],
            ]
        ];
    }

    private function getExportSelectData()
    {
        return [
            'exportFormats' => [
                ['value' => 'excel', 'label' => 'Excel (.xlsx)'],
                ['value' => 'csv', 'label' => 'CSV (.csv)'],
                ['value' => 'pdf', 'label' => 'PDF (.pdf)'],
            ],
            'statusFilters' => [
                ['value' => 'all', 'label' => 'All Roles'],
                ['value' => 'active', 'label' => 'Active Roles Only'],
                ['value' => 'inactive', 'label' => 'Inactive Roles Only'],
            ]
        ];
    }

    private function getImportSelectData()
    {
        return [
            'importModes' => [
                ['value' => 'create_only', 'label' => 'Create New Only'],
                ['value' => 'update_only', 'label' => 'Update Existing Only'],
                ['value' => 'create_update', 'label' => 'Create New & Update Existing'],
            ],
            'duplicateHandlings' => [
                ['value' => 'skip', 'label' => 'Skip Duplicates'],
                ['value' => 'overwrite', 'label' => 'Overwrite Duplicates'],
                ['value' => 'append', 'label' => 'Append with Suffix'],
            ]
        ];
    }
}
