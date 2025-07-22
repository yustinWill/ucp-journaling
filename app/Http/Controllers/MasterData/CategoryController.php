<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        $categoryTypes = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Perawatan']
        ];

        return view('pages.master_data.category.index', [
            'statuses' => $statuses,
            'categoryTypes' => $categoryTypes
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "created_at" => "2025-01-19 10:30:15",
                    "category_code" => "FINISHED_GOODS",
                    "category_name" => "Produk Jadi",
                    "category_type" => "FINISHED_GOODS",
                    "description" => "Kategori untuk produk yang sudah siap jual",
                    "product_count" => 25,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "created_at" => "2025-01-19 10:35:22",
                    "category_code" => "RAW_MATERIALS",
                    "category_name" => "Bahan Baku",
                    "category_type" => "RAW_MATERIALS",
                    "description" => "Kategori untuk bahan baku produksi",
                    "product_count" => 12,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "created_at" => "2025-01-19 10:38:45",
                    "category_code" => "PACKAGING",
                    "category_name" => "Kemasan",
                    "category_type" => "PACKAGING",
                    "description" => "Kategori untuk material kemasan dan label",
                    "product_count" => 18,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "created_at" => "2025-01-19 10:42:18",
                    "category_code" => "MAINTENANCE",
                    "category_name" => "Perawatan",
                    "category_type" => "MAINTENANCE",
                    "description" => "Kategori untuk spare part dan material perawatan",
                    "product_count" => 8,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "created_at" => "2025-01-17 14:20:30",
                    "category_code" => "CHEMICALS",
                    "category_name" => "Bahan Kimia",
                    "category_type" => "RAW_MATERIALS",
                    "description" => "Kategori untuk bahan kimia treatment air",
                    "product_count" => 5,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 6,
                    "created_at" => "2025-01-15 09:15:45",
                    "category_code" => "CONSUMABLES",
                    "category_name" => "Habis Pakai",
                    "category_type" => "MAINTENANCE",
                    "description" => "Kategori untuk barang habis pakai operasional",
                    "product_count" => 0,
                    "status" => "INACTIVE"
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $categoryTypes = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Perawatan']
        ];

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        return view('pages.master_data.category.add', [
            'categoryTypes' => $categoryTypes,
            'statuses' => $statuses
        ]);
    }

    public function edit($id)
    {
        $categoryTypes = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Perawatan']
        ];

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        // Sample category data for editing
        $category = [
            'id' => $id,
            'code' => 'FINISHED_GOODS',
            'name' => 'Produk Jadi',
            'type' => 'FINISHED_GOODS',
            'description' => 'Kategori untuk produk RIYO Water yang sudah siap untuk dijual kepada konsumen, termasuk berbagai varian kemasan',
            'status' => 'ACTIVE'
        ];

        return view('pages.master_data.category.edit', [
            'categoryTypes' => $categoryTypes,
            'statuses' => $statuses,
            'category' => $category
        ]);
    }

    public function detail($id)
    {
        // Sample category data for detail view
        $category = [
            'id' => $id,
            'category_code' => 'FINISHED_GOODS',
            'category_name' => 'Produk Jadi',
            'category_type' => 'Produk Jadi',
            'description' => 'Kategori untuk produk RIYO Water yang sudah siap untuk dijual kepada konsumen, termasuk berbagai varian kemasan mulai dari 330ml hingga 19 liter',
            'product_count' => 25,
            'status' => 'ACTIVE',
            'last_updated' => '2025-01-21 17:15:30'
        ];

        // Audit log data
        $auditLogs = [
            [
                'created_at' => '2025-01-19 13:45:20',
                'user_id' => 'USR-002',
                'user_name' => 'Product Manager',
                'action_type' => 'UPDATE',
                'description' => 'Mengubah deskripsi kategori untuk menambahkan informasi varian kemasan'
            ],
            [
                'created_at' => '2025-01-05 08:30:15',
                'user_id' => 'USR-001',
                'user_name' => 'Admin User',
                'action_type' => 'CREATE',
                'description' => 'Kategori Produk Jadi berhasil dibuat dengan kode FINISHED_GOODS'
            ]
        ];

        return view('pages.master_data.category.detail', compact('category', 'auditLogs'));
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Kode Kategori"],
            ["no" => 2, "value" => "Nama Kategori"],
            ["no" => 3, "value" => "Tipe Kategori"],
            ["no" => 4, "value" => "Deskripsi"],
            ["no" => 5, "value" => "Jumlah Produk"],
            ["no" => 6, "value" => "Status"]
        ];
        return view('pages.master_data.category.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.category.import', []);
    }
}