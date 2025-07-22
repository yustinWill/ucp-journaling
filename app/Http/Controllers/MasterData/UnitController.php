<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        $unitTypes = [
            ['value' => 'WEIGHT', 'label' => 'WEIGHT - Berat'],
            ['value' => 'VOLUME', 'label' => 'VOLUME - Volume'],
            ['value' => 'LENGTH', 'label' => 'LENGTH - Panjang'],
            ['value' => 'PIECE', 'label' => 'PIECE - Satuan']
        ];

        return view('pages.master_data.unit.index', [
            'statuses' => $statuses,
            'unitTypes' => $unitTypes
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "created_at" => "2025-01-20 11:30:15",
                    "unit_code" => "PCS",
                    "unit_name" => "Pieces",
                    "unit_symbol" => "pcs",
                    "unit_type" => "PIECE",
                    "description" => "Unit untuk barang satuan",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "created_at" => "2025-01-18 09:45:30",
                    "unit_code" => "KG",
                    "unit_name" => "Kilogram",
                    "unit_symbol" => "kg",
                    "unit_type" => "WEIGHT",
                    "description" => "Unit untuk berat dalam kilogram",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "created_at" => "2025-01-18 09:47:22",
                    "unit_code" => "LTR",
                    "unit_name" => "Liter",
                    "unit_symbol" => "ltr",
                    "unit_type" => "VOLUME",
                    "description" => "Unit untuk volume cairan",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "created_at" => "2025-01-16 14:20:45",
                    "unit_code" => "BTL",
                    "unit_name" => "Botol",
                    "unit_symbol" => "btl",
                    "unit_type" => "PIECE",
                    "description" => "Unit untuk produk dalam kemasan botol",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "created_at" => "2025-01-16 14:25:18",
                    "unit_code" => "DUS",
                    "unit_name" => "Dus",
                    "unit_symbol" => "dus",
                    "unit_type" => "PIECE",
                    "description" => "Unit untuk kemasan dus/kotak",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 6,
                    "created_at" => "2025-01-12 10:15:30",
                    "unit_code" => "MTR",
                    "unit_name" => "Meter",
                    "unit_symbol" => "m",
                    "unit_type" => "LENGTH",
                    "description" => "Unit untuk panjang/jarak",
                    "status" => "INACTIVE"
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $unitTypes = [
            ['value' => 'WEIGHT', 'label' => 'WEIGHT - Berat'],
            ['value' => 'VOLUME', 'label' => 'VOLUME - Volume'],
            ['value' => 'LENGTH', 'label' => 'LENGTH - Panjang'],
            ['value' => 'PIECE', 'label' => 'PIECE - Satuan']
        ];

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        return view('pages.master_data.unit.add', [
            'unitTypes' => $unitTypes,
            'statuses' => $statuses
        ]);
    }

    public function edit($id)
    {
        $unitTypes = [
            ['value' => 'WEIGHT', 'label' => 'WEIGHT - Berat'],
            ['value' => 'VOLUME', 'label' => 'VOLUME - Volume'],
            ['value' => 'LENGTH', 'label' => 'LENGTH - Panjang'],
            ['value' => 'PIECE', 'label' => 'PIECE - Satuan']
        ];

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        // Sample unit data for editing
        $unit = [
            'id' => $id,
            'code' => 'BTL',
            'name' => 'Botol',
            'symbol' => 'btl',
            'type' => 'PIECE',
            'description' => 'Unit untuk produk RIYO Water dalam kemasan botol plastik dan kaca',
            'status' => 'ACTIVE'
        ];

        return view('pages.master_data.unit.edit', [
            'unitTypes' => $unitTypes,
            'statuses' => $statuses,
            'unit' => $unit
        ]);
    }

    public function detail($id)
    {
        // Sample unit data for detail view
        $unit = [
            'id' => $id,
            'unit_code' => 'BTL',
            'unit_name' => 'Botol',
            'unit_symbol' => 'btl',
            'unit_type' => 'Satuan',
            'description' => 'Unit untuk produk RIYO Water dalam kemasan botol plastik dan kaca. Digunakan untuk menghitung inventory dan penjualan produk air minum',
            'status' => 'ACTIVE',
            'last_updated' => '2025-01-21 16:45:22'
        ];

        // Audit log data
        $auditLogs = [
            [
                'created_at' => '2025-01-18 11:30:15',
                'user_id' => 'USR-001',
                'user_name' => 'Admin User',
                'action_type' => 'UPDATE',
                'description' => 'Mengubah deskripsi unit BTL untuk menambahkan informasi kemasan kaca'
            ],
            [
                'created_at' => '2025-01-10 09:20:45',
                'user_id' => 'USR-003',
                'user_name' => 'Warehouse Manager',
                'action_type' => 'CREATE',
                'description' => 'Unit Botol (BTL) berhasil dibuat dengan kode BTL'
            ]
        ];

        return view('pages.master_data.unit.detail', compact('unit', 'auditLogs'));
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Kode Unit"],
            ["no" => 2, "value" => "Nama Unit"],
            ["no" => 3, "value" => "Simbol Unit"],
            ["no" => 4, "value" => "Tipe Unit"],
            ["no" => 5, "value" => "Deskripsi"],
            ["no" => 6, "value" => "Status"]
        ];
        return view('pages.master_data.unit.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.unit.import', []);
    }
}