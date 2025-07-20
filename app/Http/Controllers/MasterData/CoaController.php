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
                    "coa_code" => "1",
                    "coa_name" => "Asset",
                    "level" => "1",
                    "type" => "Header",
                    "note" => "-",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 1,
                    "coa_code" => "1.1",
                    "coa_name" => "Kas Besar",
                    "level" => "2",
                    "type" => "Detail",
                    "note" => "-",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 1,
                    "coa_code" => "1.2",
                    "coa_name" => "Kas Besar",
                    "level" => "2",
                    "type" => "Detail",
                    "note" => "-",
                    "status" => "ACTIVE",
                ],
                [
                    "id" => 1,
                    "coa_code" => "1.3",
                    "coa_name" => "Piutang",
                    "level" => "2",
                    "type" => "Detail",
                    "note" => "-",
                    "status" => "ACTIVE",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.master_data.coa.add', []);
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
