<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.master_data.user.index', []);
    }

    public function index_data_table()
    {
        // Mock roles data (no DB connection)
        $roles = [
            "Super Admin",
            "Admin", 
            "Accounting",
            "Sales",
            "Purchase"
        ];

        $data = [
            "data" => [
                [
                    "id" => 1,
                    "name" => "User 1",
                    "email" => "email@mail.com",
                    "phone" => "08127213123",
                    "role" => $roles[0],
                    "last_login" => "2025-06-01 12:00:00",
                    "last_password_change" => "2025-06-01 12:00:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 2,
                    "name" => "User 2",
                    "email" => "email@mail.com",
                    "phone" => "08127213123",
                    "role" => $roles[1],
                    "last_login" => "2025-06-01 12:00:00",
                    "last_password_change" => "2025-06-01 12:00:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 3,
                    "name" => "User 3",
                    "email" => "email@mail.com",
                    "phone" => "08127213123",
                    "role" => $roles[2],
                    "last_login" => "2025-06-01 12:00:00",
                    "last_password_change" => "2025-06-01 12:00:00",
                    "status" => "ACTIVE",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $selectData = [
            'roles' => $this->getMockRoleSelectData()
        ];
        
        return view('pages.master_data.user.add', $selectData);
    }

    public function edit($id)
    {
        // Mock data (no DB connection)
        $user = [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => 'user' . $id . '@example.com',
            'phone' => '08127213123',
            'role' => 1, // Mock role ID
            'status' => 'ACTIVE'
        ];
        
        $selectData = [
            'roles' => $this->getMockRoleSelectData()
        ];
        
        return view('pages.master_data.user.edit', compact('user') + $selectData);
    }

    public function detail($id)
    {
        // Mock data (no DB connection)
        $user = [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => 'user' . $id . '@example.com',
            'phone' => '08127213123',
            'role' => 'Super Admin', // Mock role name
            'last_login' => '2025-06-01 12:00:00',
            'last_password_change' => '2025-06-01 12:00:00',
            'status' => 'ACTIVE'
        ];
        return view('pages.master_data.user.detail', ['user' => $user]);
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
            ['value' => 1, 'label' => 'Super Admin'],
            ['value' => 2, 'label' => 'Admin'],
            ['value' => 3, 'label' => 'Accounting'],
            ['value' => 4, 'label' => 'Sales'],
            ['value' => 5, 'label' => 'Purchase']
        ];
    }
}
