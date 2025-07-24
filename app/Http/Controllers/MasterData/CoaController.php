<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;
use App\Helpers\PermissionHelper;

//Actions
use App\Domains\Coa\Actions\CreateCoaAction;
use App\Domains\Coa\Actions\UpdateCoaAction;

//Services
use App\Domains\Coa\Services\CoaService;
use App\Domains\Coa\Services\CoaAuditLogService;
use App\Domains\Role\Services\RoleService;
use App\Domains\Tag\Services\TagService;
//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;

class CoaController extends Controller
{

    private $statuses = [
        ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
        ['value' => 'INACTIVE', 'label' => 'INACTIVE - Tidak Aktif']
    ];

    public function index()
    {
        $statuses = $this->statuses;
        return view('pages.master_data.coa.index', compact('statuses'));
    }

    public function index_data_table()
    {
        $data = app(CoaService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $tags = [
            ['value' => 'AKTIVA', 'label' => 'AKTIVA - Aktiva', 'color' => 'success'],
            ['value' => 'KEWAJIBAN', 'label' => 'KEWAJIBAN - Kewajiban', 'color' => 'danger'],
            ['value' => 'MODAL', 'label' => 'MODAL - Modal', 'color' => 'primary'],
            ['value' => 'PENDAPATAN', 'label' => 'PENDAPATAN - Pendapatan', 'color' => 'info'],
            ['value' => 'BEBAN', 'label' => 'BEBAN - Beban', 'color' => 'warning']
        ];

        // Permission check for adding new tags
        $canAddTags = true; // Mock permission check

        return view('pages.master_data.coa.add', [
            'tags' => $tags,
            'canAddTags' => $canAddTags
        ]);
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Kode COA"],
            ["no" => 2, "value" => "Nama COA"],
            ["no" => 3, "value" => "Level COA"],
            ["no" => 4, "value" => "Jenis COA"],
            ["no" => 5, "value" => "Note"],
            ["no" => 6, "value" => "Status"],
        ];
        return view('pages.master_data.coa.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.coa.import', []);
    }

    public function detail($id)
    {
        // Sample COA data for the detail view
        $coa = [
            'id' => $id,
            'coa_code' => '1101',
            'coa_name' => 'Kas',
            'level' => '4',
            'account_type' => 'AKTIVA',
            'status' => 'ACTIVE',
            'normal_position' => 'DEBIT',
            'description' => 'Akun kas untuk mencatat semua transaksi kas masuk dan keluar perusahaan.',
            'opening_balance' => 'Rp 50,000,000',
            'total_debit' => 'Rp 125,750,000',
            'total_credit' => 'Rp 98,250,000',
            'ending_balance' => 'Rp 77,500,000',
            'last_updated' => '2025-01-20 16:45:30'
        ];

        return view('pages.master_data.coa.detail', compact('coa'));
    }

    public function edit($id)
    {
        // TODO: Implement edit functionality
        return view('pages.master_data.coa.edit', ['id' => $id]);
    }
}
