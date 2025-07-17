<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        return view('pages.master_data.role.index', []);
    }

    public function index_data_table()
    {
        // Mock data for now - will be replaced with actual database queries
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "name" => "Super Admin",
                    "description" => "Full system access with all permissions",
                    "user_count" => 1,
                    "permissions_count" => 12,
                    "status" => "ACTIVE",
                    "created_at" => "2025-01-01 10:00:00",
                ],
                [
                    "id" => 2,
                    "name" => "Admin",
                    "description" => "Administrative access with limited permissions",
                    "user_count" => 3,
                    "permissions_count" => 8,
                    "status" => "ACTIVE",
                    "created_at" => "2025-01-01 10:00:00",
                ],
                [
                    "id" => 3,
                    "name" => "Accounting",
                    "description" => "Financial and accounting module access",
                    "user_count" => 2,
                    "permissions_count" => 6,
                    "status" => "ACTIVE",
                    "created_at" => "2025-01-01 10:00:00",
                ],
                [
                    "id" => 4,
                    "name" => "Sales",
                    "description" => "Sales and customer management access",
                    "user_count" => 4,
                    "permissions_count" => 4,
                    "status" => "ACTIVE",
                    "created_at" => "2025-01-01 10:00:00",
                ],
                [
                    "id" => 5,
                    "name" => "Purchase",
                    "description" => "Purchase and supplier management access",
                    "user_count" => 0,
                    "permissions_count" => 4,
                    "status" => "INACTIVE",
                    "created_at" => "2025-01-01 10:00:00",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $selectData = $this->getSelectData();
        return view('pages.master_data.role.add', $selectData);
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
        // Mock role data - will be replaced with actual database queries
        $role = [
            'id' => $id,
            'name' => 'Admin',
            'description' => 'Administrative access with limited permissions',
            'is_active' => true,
            'user_count' => 3,
            'permissions_count' => 8,
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00'
        ];

        $selectData = $this->getSelectData();
        return view('pages.master_data.role.detail', compact('role') + $selectData);
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
        return view('pages.master_data.role.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.role.import', []);
    }

    public function getRolePermissions($id)
    {
        // Mock permission data grouped by module
        $permissions = [
            'User Management' => [
                ['id' => 1, 'name' => 'User Read', 'type' => 'read', 'granted' => true],
                ['id' => 2, 'name' => 'User Write', 'type' => 'write', 'granted' => true],
                ['id' => 3, 'name' => 'User Delete', 'type' => 'delete', 'granted' => true],
                ['id' => 4, 'name' => 'User Approval', 'type' => 'approval', 'granted' => false],
            ],
            'Contact Management' => [
                ['id' => 5, 'name' => 'Contact Read', 'type' => 'read', 'granted' => true],
                ['id' => 6, 'name' => 'Contact Write', 'type' => 'write', 'granted' => true],
                ['id' => 7, 'name' => 'Contact Delete', 'type' => 'delete', 'granted' => true],
                ['id' => 8, 'name' => 'Contact Approval', 'type' => 'approval', 'granted' => false],
            ],
            'Product Management' => [
                ['id' => 9, 'name' => 'Product Read', 'type' => 'read', 'granted' => true],
                ['id' => 10, 'name' => 'Product Write', 'type' => 'write', 'granted' => true],
                ['id' => 11, 'name' => 'Product Delete', 'type' => 'delete', 'granted' => false],
                ['id' => 12, 'name' => 'Product Approval', 'type' => 'approval', 'granted' => false],
            ],
            'Sales Management' => [
                ['id' => 13, 'name' => 'Sales Read', 'type' => 'read', 'granted' => false],
                ['id' => 14, 'name' => 'Sales Write', 'type' => 'write', 'granted' => false],
                ['id' => 15, 'name' => 'Sales Delete', 'type' => 'delete', 'granted' => false],
                ['id' => 16, 'name' => 'Sales Approval', 'type' => 'approval', 'granted' => false],
            ]
        ];

        return response()->json($permissions);
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
            ]
        ];
    }
}