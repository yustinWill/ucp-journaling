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
        $units = [
            ['value' => 'pcs', 'label' => 'pcs'],
            ['value' => 'kg', 'label' => 'kg'],
            ['value' => 'meter', 'label' => 'meter'],
            ['value' => 'liter', 'label' => 'liter'],
            ['value' => 'rim', 'label' => 'rim'],
            ['value' => 'box', 'label' => 'box'],
            ['value' => 'set', 'label' => 'set'],
            ['value' => 'roll', 'label' => 'roll'],
            ['value' => 'pack', 'label' => 'pack']
        ];

        $categories = [
            ['value' => 'Alat Tulis', 'label' => 'Alat Tulis'],
            ['value' => 'Kertas', 'label' => 'Kertas'],
            ['value' => 'Elektronik', 'label' => 'Elektronik'],
            ['value' => 'Furniture', 'label' => 'Furniture'],
            ['value' => 'Konsumsi', 'label' => 'Konsumsi']
        ];

        $accounts = [
            ['value' => '1-10100', 'label' => '(1-10100) Kas dan Bank'],
            ['value' => '1-20100', 'label' => '(1-20100) Piutang'],
            ['value' => '1-30100', 'label' => '(1-30100) Persediaan'],
            ['value' => '2-20100', 'label' => '(2-20100) Hutang'],
            ['value' => '4-40100', 'label' => '(4-40100) Pendapatan Penjualan'],
            ['value' => '5-50100', 'label' => '(5-50100) Beban Operasional']
        ];

        $taxes = [
            ['value' => 'PPN', 'label' => 'PPN (11%)'],
            ['value' => 'No Tax', 'label' => 'Tidak Ada Pajak']
        ];

        $products = [
            ['value' => '1', 'label' => 'Pulpen Pilot G2', 'unit' => 'pcs', 'price' => 12000],
            ['value' => '2', 'label' => 'Kertas A4 80gsm', 'unit' => 'rim', 'price' => 50000],
            ['value' => '3', 'label' => 'Spidol Whiteboard', 'unit' => 'pcs', 'price' => 18000],
            ['value' => '4', 'label' => 'Buku Tulis 38 Lembar', 'unit' => 'pcs', 'price' => 6000]
        ];

        return view('pages.master_data.product.add', [
            'units' => $units,
            'categories' => $categories,
            'accounts' => $accounts,
            'taxes' => $taxes,
            'products' => $products
        ]);
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

    public function detail($id)
    {
        // Sample product data for the detail view
        $product = [
            'id' => $id,
            'product_name' => 'Air Mineral RIYO 600ml',
            'product_code' => 'PROD-2025-001',
            'unit' => 'botol',
            'category' => 'Minuman',
            'description' => 'Air mineral premium dengan kualitas terbaik, dikemas dalam botol 600ml yang praktis untuk konsumsi sehari-hari.',
            'product_type' => 'Single',
            'purchase_price' => 'Rp 2,500',
            'purchase_account' => '[5001] - Pembelian Barang Dagang',
            'purchase_tax' => '[PPN11] - PPN 11%',
            'selling_price' => 'Rp 4,000',
            'sales_account' => '[4001] - Penjualan Barang Dagang',
            'sales_tax' => '[PPN11] - PPN 11%',
            'minimum_stock' => '100',
            'inventory_account' => '[1301] - Persediaan Barang Dagang',
            'track_quantity' => true,
            'track_batch' => false,
            'image' => null
        ];

        return view('pages.master_data.product.detail', compact('product'));
    }

    public function edit($id)
    {
        // TODO: Implement edit functionality
        return view('pages.master_data.product.edit', ['id' => $id]);
    }
}