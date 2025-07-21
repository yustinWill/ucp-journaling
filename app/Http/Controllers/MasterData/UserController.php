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
                    "created_at" => "2025-01-15",
                    "name" => "Yustinus Komunal",
                    "email" => "yustinus.komunal@riyowater.com",
                    "phone" => "081234567890",
                    "role" => "Manager Produksi",
                    "last_login" => "2025-01-20 08:30:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 2,
                    "created_at" => "2025-01-10",
                    "name" => "Ririn Safitri",
                    "email" => "ririn.safitri@riyowater.com",
                    "phone" => "081234567891",
                    "role" => "Staff Keuangan",
                    "last_login" => "2025-01-20 09:15:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 3,
                    "created_at" => "2025-01-05",
                    "name" => "Ahmad Setyo",
                    "email" => "ahmad.setyo@riyowater.com",
                    "phone" => "081234567892",
                    "role" => "Supervisor QC",
                    "last_login" => "2025-01-19 16:45:00",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 4,
                    "created_at" => "2024-12-20",
                    "name" => "Siti Nurhaliza",
                    "email" => "siti.nurhaliza@riyowater.com",
                    "phone" => "081234567893",
                    "role" => "Admin Gudang",
                    "last_login" => "2025-01-18 14:20:00",
                    "status" => "INACTIVE",
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
        $userData = [
            1 => [
                'id' => 1,
                'name' => 'Yustinus Komunal',
                'email' => 'yustinus.komunal@riyowater.com',
                'phone' => '081234567890',
                'role' => 'MANAGER_PRODUKSI',
                'status' => 'ACTIVE'
            ],
            2 => [
                'id' => 2,
                'name' => 'Ririn Safitri',
                'email' => 'ririn.safitri@riyowater.com',
                'phone' => '081234567891',
                'role' => 'STAFF_KEUANGAN',
                'status' => 'ACTIVE'
            ],
            3 => [
                'id' => 3,
                'name' => 'Ahmad Setyo',
                'email' => 'ahmad.setyo@riyowater.com',
                'phone' => '081234567892',
                'role' => 'SUPERVISOR_QC',
                'status' => 'ACTIVE'
            ]
        ];
        
        $user = $userData[$id] ?? $userData[1];
        
        $selectData = [
            'roles' => $this->getMockRoleSelectData()
        ];
        
        return view('pages.master_data.user.edit', compact('user') + $selectData);
    }

    public function detail($id)
    {
        // Mock data (no DB connection)
        $userData = [
            1 => [
                'id' => 1,
                'name' => 'Yustinus Komunal',
                'email' => 'yustinus.komunal@riyowater.com',
                'phone' => '081234567890',
                'role' => 'Manager Produksi',
                'last_login' => '2025-01-20 08:30:00',
                'last_password_change' => '2025-01-15 10:00:00',
                'status' => 'ACTIVE',
                // CRITICAL: Audit logs MUST be sorted by latest date first (as per Form Standards Rule #21)
                'audit_logs' => [
                    [
                        'created_at' => '2025-01-21 14:15:30', // Most recent
                        'user_id' => 'USR-001',
                        'user_name' => 'Yustinus Komunal',
                        'action_type' => 'UPDATE',
                        'description' => 'Update profil dan nomor telepon untuk akses RIYO Water'
                    ],
                    [
                        'created_at' => '2025-01-20 08:30:00',
                        'user_id' => 'USR-001',
                        'user_name' => 'Yustinus Komunal',
                        'action_type' => 'UPDATE',
                        'description' => 'Login berhasil dari IP 192.168.1.100 - RIYO Water Production Office'
                    ],
                    [
                        'created_at' => '2025-01-15 10:00:00',
                        'user_id' => 'USR-001',
                        'user_name' => 'Yustinus Komunal',
                        'action_type' => 'UPDATE',
                        'description' => 'Password berhasil diubah untuk keamanan akun'
                    ],
                    [
                        'created_at' => '2025-01-15 09:00:00', // Oldest
                        'user_id' => 'ADM-001',
                        'user_name' => 'System Administrator',
                        'action_type' => 'CREATE',
                        'description' => 'User account dibuat dengan role Manager Produksi RIYO Water'
                    ]
                ]
            ],
            2 => [
                'id' => 2,
                'name' => 'Ririn Safitri',
                'email' => 'ririn.safitri@riyowater.com',
                'phone' => '081234567891',
                'role' => 'Staff Keuangan',
                'last_login' => '2025-01-20 09:15:00',
                'last_password_change' => '2025-01-10 14:30:00',
                'status' => 'ACTIVE',
                // CRITICAL: Audit logs MUST be sorted by latest date first (as per Form Standards Rule #21)
                'audit_logs' => [
                    [
                        'created_at' => '2025-01-21 09:45:15', // Most recent
                        'user_id' => 'USR-002',
                        'user_name' => 'Ririn Safitri',
                        'action_type' => 'UPDATE',
                        'description' => 'Update akses keuangan untuk laporan RIYO Water bulanan'
                    ],
                    [
                        'created_at' => '2025-01-20 09:15:00',
                        'user_id' => 'USR-002',
                        'user_name' => 'Ririn Safitri',
                        'action_type' => 'UPDATE',
                        'description' => 'Login berhasil dari IP 192.168.1.101 - RIYO Water Finance Office'
                    ],
                    [
                        'created_at' => '2025-01-10 14:30:00', // Oldest
                        'user_id' => 'ADM-001',
                        'user_name' => 'System Administrator',
                        'action_type' => 'CREATE',
                        'description' => 'User account dibuat dengan role Staff Keuangan RIYO Water'
                    ]
                ]
            ]
        ];
        
        $user = $userData[$id] ?? $userData[1];
        
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
