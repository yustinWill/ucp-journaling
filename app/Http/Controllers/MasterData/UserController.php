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
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "name" => "User 1",
                    "email" => "email@mail.com",
                    "phone" => "08127213123",
                    "role" => "Suoper Admin",
                    "last_login" => "2025-06-01 12:00:00",
                    "last_password_change" => "2025-06-01 12:00:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 2,
                    "name" => "User 2",
                    "email" => "email@mail.com",
                    "phone" => "08127213123",
                    "role" => "Admin",
                    "last_login" => "2025-06-01 12:00:00",
                    "last_password_change" => "2025-06-01 12:00:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 3,
                    "name" => "User 3",
                    "email" => "email@mail.com",
                    "phone" => "08127213123",
                    "role" => "Accounting",
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
            'roles' => [
                ['value' => 'Super Admin', 'label' => 'Super Admin'],
                ['value' => 'Admin', 'label' => 'Admin'],
                ['value' => 'Accounting', 'label' => 'Accounting'],
                ['value' => 'Sales', 'label' => 'Sales'],
                ['value' => 'Purchase', 'label' => 'Purchase'],
                ['value' => 'Manager', 'label' => 'Manager'],
                ['value' => 'Staff', 'label' => 'Staff']
            ]
        ];
        
        return view('pages.master_data.user.add', $selectData);
    }

    public function edit($id)
    {
        // In a real implementation, you would fetch the user from the database
        $user = [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => 'user' . $id . '@example.com',
            'phone' => '08127213123',
            'role' => 'Super Admin',
            'status' => 'ACTIVE'
        ];
        
        $selectData = [
            'roles' => [
                ['value' => 'Super Admin', 'label' => 'Super Admin'],
                ['value' => 'Admin', 'label' => 'Admin'],
                ['value' => 'Accounting', 'label' => 'Accounting'],
                ['value' => 'Sales', 'label' => 'Sales'],
                ['value' => 'Purchase', 'label' => 'Purchase'],
                ['value' => 'Manager', 'label' => 'Manager'],
                ['value' => 'Staff', 'label' => 'Staff']
            ]
        ];
        
        return view('pages.master_data.user.edit', compact('user') + $selectData);
    }

    public function detail($id)
    {
        // In a real implementation, you would fetch the user from the database
        $user = [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => 'user' . $id . '@example.com',
            'phone' => '08127213123',
            'role' => 'Super Admin',
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
}
