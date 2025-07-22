<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        return view('pages.master_data.tax.index', [
            'statuses' => $statuses
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "created_at" => "2025-01-20 10:15:30",
                    "tax_code" => "PPN11",
                    "tax_name" => "PPN 11%",
                    "tax_rate" => 11.00,
                    "tax_type" => "PERCENTAGE",
                    "description" => "Pajak Pertambahan Nilai standar Indonesia",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "created_at" => "2025-01-18 14:30:22",
                    "tax_code" => "PPN0",
                    "tax_name" => "PPN 0%",
                    "tax_rate" => 0.00,
                    "tax_type" => "PERCENTAGE",
                    "description" => "Pajak Pertambahan Nilai untuk barang tertentu",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "created_at" => "2025-01-15 09:45:15",
                    "tax_code" => "PPH21",
                    "tax_name" => "PPh Pasal 21",
                    "tax_rate" => 5.00,
                    "tax_type" => "PERCENTAGE",
                    "description" => "Pajak Penghasilan atas gaji dan upah",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "created_at" => "2025-01-15 09:50:33",
                    "tax_code" => "PPH23",
                    "tax_name" => "PPh Pasal 23",
                    "tax_rate" => 2.00,
                    "tax_type" => "PERCENTAGE",
                    "description" => "Pajak Penghasilan atas jasa profesional",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "created_at" => "2025-01-10 16:20:45",
                    "tax_code" => "NOTAX",
                    "tax_name" => "Tanpa Pajak",
                    "tax_rate" => 0.00,
                    "tax_type" => "PERCENTAGE",
                    "description" => "Untuk transaksi yang tidak dikenai pajak",
                    "status" => "INACTIVE"
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $taxTypes = [
            ['value' => 'PERCENTAGE', 'label' => 'PERCENTAGE - Persentase'],
            ['value' => 'FIXED', 'label' => 'FIXED - Nominal Tetap']
        ];

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        return view('pages.master_data.tax.add', [
            'taxTypes' => $taxTypes,
            'statuses' => $statuses
        ]);
    }

    public function edit($id)
    {
        $taxTypes = [
            ['value' => 'PERCENTAGE', 'label' => 'PERCENTAGE - Persentase'],
            ['value' => 'FIXED', 'label' => 'FIXED - Nominal Tetap']
        ];

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        // Sample tax data for editing
        $tax = [
            'id' => $id,
            'code' => 'PPN11',
            'name' => 'PPN 11%',
            'rate' => '11.00',
            'type' => 'PERCENTAGE',
            'description' => 'Pajak Pertambahan Nilai standar Indonesia sebesar 11%',
            'status' => 'ACTIVE'
        ];

        return view('pages.master_data.tax.edit', [
            'taxTypes' => $taxTypes,
            'statuses' => $statuses,
            'tax' => $tax
        ]);
    }

    public function detail($id)
    {
        // Sample tax data for detail view
        $tax = [
            'id' => $id,
            'tax_code' => 'PPN11',
            'tax_name' => 'PPN 11%',
            'tax_rate' => '11.00',
            'tax_type' => 'Persentase',
            'description' => 'Pajak Pertambahan Nilai standar Indonesia sebesar 11% yang dikenakan pada sebagian besar transaksi jual beli barang dan jasa',
            'status' => 'ACTIVE',
            'last_updated' => '2025-01-21 16:30:15'
        ];

        // Audit log data
        $auditLogs = [
            [
                'created_at' => '2025-01-20 10:15:30',
                'user_id' => 'USR-001',
                'user_name' => 'Admin User',
                'action_type' => 'UPDATE',
                'description' => 'Mengubah tarif pajak dari 10% menjadi 11%'
            ],
            [
                'created_at' => '2025-01-15 14:22:45',
                'user_id' => 'USR-002',
                'user_name' => 'Tax Manager',
                'action_type' => 'CREATE',
                'description' => 'Pajak PPN 11% berhasil dibuat dengan kode PPN11'
            ]
        ];

        return view('pages.master_data.tax.detail', compact('tax', 'auditLogs'));
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Kode Pajak"],
            ["no" => 2, "value" => "Nama Pajak"],
            ["no" => 3, "value" => "Tarif Pajak"],
            ["no" => 4, "value" => "Tipe Pajak"],
            ["no" => 5, "value" => "Deskripsi"],
            ["no" => 6, "value" => "Status"]
        ];
        return view('pages.master_data.tax.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.tax.import', []);
    }
}