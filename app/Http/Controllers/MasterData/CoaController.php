<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index()
    {
        return view('pages.master_data.coa.index', []);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "coa_code" => "1-10000",
                    "coa_name" => "Kas dan Bank",
                    "level" => "1",
                    "type" => "Header",
                    "tags" => [
                        ["name" => "AKTIVA", "color" => "success"]
                    ],
                    "note" => "Akun untuk kas dan bank RIYO Water",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 2,
                    "coa_code" => "1-10100",
                    "coa_name" => "Kas Besar",
                    "level" => "2",
                    "type" => "Detail",
                    "tags" => [
                        ["name" => "AKTIVA", "color" => "success"]
                    ],
                    "note" => "Kas besar RIYO Water",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 3,
                    "coa_code" => "1-10200",
                    "coa_name" => "Bank BCA",
                    "level" => "2",
                    "type" => "Detail",
                    "tags" => [
                        ["name" => "AKTIVA", "color" => "success"]
                    ],
                    "note" => "Rekening operasional RIYO Water di BCA",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 4,
                    "coa_code" => "1-20100",
                    "coa_name" => "Piutang Dagang",
                    "level" => "2",
                    "type" => "Detail",
                    "tags" => [
                        ["name" => "AKTIVA", "color" => "success"]
                    ],
                    "note" => "Piutang dari penjualan RIYO Water",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 5,
                    "coa_code" => "2-20100",
                    "coa_name" => "Hutang Dagang",
                    "level" => "2",
                    "type" => "Detail",
                    "tags" => [
                        ["name" => "KEWAJIBAN", "color" => "danger"]
                    ],
                    "note" => "Hutang kepada supplier RIYO Water",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 6,
                    "coa_code" => "4-40100",
                    "coa_name" => "Penjualan RIYO Water",
                    "level" => "2",
                    "type" => "Detail",
                    "tags" => [
                        ["name" => "PENDAPATAN", "color" => "info"]
                    ],
                    "note" => "Pendapatan dari penjualan produk RIYO Water",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 7,
                    "coa_code" => "5-50100",
                    "coa_name" => "Beban Produksi",
                    "level" => "2",
                    "type" => "Detail",
                    "tags" => [
                        ["name" => "BEBAN", "color" => "warning"]
                    ],
                    "note" => "Biaya produksi RIYO Water",
                    "status" => "ACTIVE",
                ],
            ]
        ];
        return response()->json($data, 200);
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
