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
        $tags = [
            ['value' => 'MANAGEMENT', 'label' => 'MANAGEMENT - Manajemen', 'color' => 'primary'],
            ['value' => 'STAFF', 'label' => 'STAFF - Staff', 'color' => 'info'],
            ['value' => 'SUPERVISOR', 'label' => 'SUPERVISOR - Supervisor', 'color' => 'warning'],
            ['value' => 'ADMIN', 'label' => 'ADMIN - Administrator', 'color' => 'secondary'],
            ['value' => 'OPERATOR', 'label' => 'OPERATOR - Operator', 'color' => 'success']
        ];

        // Permission check for adding new tags
        $canAddTags = true; // Mock permission check

        $selectData = [
            'roles' => $this->getMockRoleSelectData(),
            'tags' => $tags,
            'canAddTags' => $canAddTags
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

        $tags = [
            ['value' => 'MANAGEMENT', 'label' => 'MANAGEMENT - Manajemen', 'color' => 'primary'],
            ['value' => 'STAFF', 'label' => 'STAFF - Staff', 'color' => 'info'],
            ['value' => 'SUPERVISOR', 'label' => 'SUPERVISOR - Supervisor', 'color' => 'warning'],
            ['value' => 'ADMIN', 'label' => 'ADMIN - Administrator', 'color' => 'secondary'],
            ['value' => 'OPERATOR', 'label' => 'OPERATOR - Operator', 'color' => 'success']
        ];

        // Permission check for adding new tags
        $canAddTags = true; // Mock permission check

        $selectData = [
            'roles' => $this->getMockRoleSelectData(),
            'tags' => $tags,
            'canAddTags' => $canAddTags
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
