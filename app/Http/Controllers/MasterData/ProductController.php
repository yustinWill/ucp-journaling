<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $productCategories = [
            ['value' => 'Alat Tulis', 'label' => 'Alat Tulis'],
            ['value' => 'Kertas', 'label' => 'Kertas'],
            ['value' => 'Elektronik', 'label' => 'Elektronik']
        ];

        $stockStatuses = [
            ['value' => 'available', 'label' => 'Stok Tersedia'],
            ['value' => 'low', 'label' => 'Stok Segera Habis'],
            ['value' => 'out', 'label' => 'Stok Habis']
        ];

        $warehouseStatuses = [
            ['value' => 'ACTIVE', 'label' => 'Aktif'],
            ['value' => 'INACTIVE', 'label' => 'Tidak Aktif']
        ];

        $warehouseLocations = [
            ['value' => 'Jakarta', 'label' => 'Jakarta'],
            ['value' => 'Bandung', 'label' => 'Bandung'],
            ['value' => 'Surabaya', 'label' => 'Surabaya']
        ];

        return view('pages.master_data.product.index', [
            'productCategories' => $productCategories,
            'stockStatuses' => $stockStatuses,
            'warehouseStatuses' => $warehouseStatuses,
            'warehouseLocations' => $warehouseLocations
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "product_name" => "Pulpen Pilot G2",
                    "product_code" => "P-G2-001",
                    "category" => "Alat Tulis",
                    "total_stock" => 150,
                    "available_qty" => 145,
                    "minimum" => 20,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "product_name" => "Kertas A4 80gsm",
                    "product_code" => "KRT-A4-80",
                    "category" => "Kertas",
                    "total_stock" => 500,
                    "available_qty" => 480,
                    "minimum" => 50,
                    "unit" => "rim",
                    "average_price" => 42000,
                    "last_purchase_price" => 45000,
                    "purchase_price" => 40000,
                    "selling_price" => 50000,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "product_name" => "Spidol Whiteboard",
                    "product_code" => "SPD-WB-001",
                    "category" => "Alat Tulis",
                    "total_stock" => 75,
                    "available_qty" => 70,
                    "minimum" => 15,
                    "unit" => "pcs",
                    "average_price" => 15000,
                    "last_purchase_price" => 16000,
                    "purchase_price" => 14000,
                    "selling_price" => 18000,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "product_name" => "Buku Tulis 38 Lembar",
                    "product_code" => "BK-38-001",
                    "category" => "Kertas",
                    "total_stock" => 200,
                    "available_qty" => 195,
                    "minimum" => 30,
                    "unit" => "pcs",
                    "average_price" => 5000,
                    "last_purchase_price" => 5500,
                    "purchase_price" => 4500,
                    "selling_price" => 6000,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "product_name" => "Amplop Putih A4",
                    "product_code" => "AMP-A4-001",
                    "category" => "Kertas",
                    "total_stock" => 8,
                    "available_qty" => 5,
                    "minimum" => 25,
                    "unit" => "pcs",
                    "average_price" => 2500,
                    "last_purchase_price" => 2800,
                    "purchase_price" => 2200,
                    "selling_price" => 3000,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 6,
                    "product_name" => "Gunting Kertas",
                    "product_code" => "GNT-KRT-001",
                    "category" => "Alat Tulis",
                    "total_stock" => 0,
                    "available_qty" => 0,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 25000,
                    "last_purchase_price" => 26000,
                    "purchase_price" => 24000,
                    "selling_price" => 30000,
                    "status" => "ACTIVE"
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function index_warehouse_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "warehouse_code" => "GDG-001",
                    "warehouse_name" => "Gudang Utama Pusat",
                    "pic_name" => "Yustinus",
                    "address" => "Jl. Industri No. 12, Kawasan Bisnis, Jakarta",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "warehouse_code" => "GDG-002",
                    "warehouse_name" => "Gudang Cabang Bandung",
                    "pic_name" => "Ririn",
                    "address" => "Jl. Soekarno Hatta No. 88, Bandung",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "warehouse_code" => "GDG-003",
                    "warehouse_name" => "Gudang Cabang Surabaya",
                    "pic_name" => "Hartono",
                    "address" => "Jl. Pahlawan No. 45, Surabaya",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "warehouse_code" => "GDG-004",
                    "warehouse_name" => "Gudang Cabang Medan",
                    "pic_name" => "Sari",
                    "address" => "Jl. Gatot Subroto No. 123, Medan",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "warehouse_code" => "GDG-005",
                    "warehouse_name" => "Gudang Cabang Makassar",
                    "pic_name" => "Budi",
                    "address" => "Jl. AP Pettarani No. 67, Makassar",
                    "status" => "INACTIVE"
                ]
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.master_data.product.add', []);
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Nama Produk"],
            ["no" => 2, "value" => "Kode Produk"],
            ["no" => 3, "value" => "Kategori Produk"],
            ["no" => 4, "value" => "Total Stok"],
            ["no" => 5, "value" => "Qty Tersedia"],
            ["no" => 6, "value" => "Batas Minimum"],
            ["no" => 7, "value" => "Unit"],
            ["no" => 8, "value" => "Harga Rata-Rata"],
            ["no" => 9, "value" => "Harga Beli Terakhir"],
            ["no" => 10, "value" => "Harga Beli"],
            ["no" => 11, "value" => "Harga Jual"],
        ];
        return view('pages.master_data.product.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.product.import', []);
    }
}