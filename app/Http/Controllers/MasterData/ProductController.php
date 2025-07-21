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
                    "product_name" => "RIYO Water 600ml",
                    "product_code" => "RW-600ML",
                    "category" => "Finished Goods",
                    "tags" => [
                        ["name" => "FINISHED_GOODS", "color" => "success"],
                        ["name" => "POPULAR", "color" => "primary"]
                    ],
                    "total_stock" => 15000,
                    "available_qty" => 14500,
                    "minimum" => 2000,
                    "unit" => "btl",
                    "average_price" => 1200,
                    "last_purchase_price" => 1250,
                    "purchase_price" => 1100,
                    "selling_price" => 1500,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "product_name" => "RIYO Water 1500ml",
                    "product_code" => "RW-1500ML",
                    "category" => "Finished Goods",
                    "tags" => [
                        ["name" => "FINISHED_GOODS", "color" => "success"]
                    ],
                    "total_stock" => 8500,
                    "available_qty" => 8200,
                    "minimum" => 1000,
                    "unit" => "btl",
                    "average_price" => 2800,
                    "last_purchase_price" => 2900,
                    "purchase_price" => 2600,
                    "selling_price" => 3500,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "product_name" => "Plastic Bottle 600ml",
                    "product_code" => "PB-600ML",
                    "category" => "Packaging",
                    "tags" => [
                        ["name" => "PACKAGING", "color" => "warning"],
                        ["name" => "RAW_MATERIALS", "color" => "info"]
                    ],
                    "total_stock" => 25000,
                    "available_qty" => 23000,
                    "minimum" => 5000,
                    "unit" => "pcs",
                    "average_price" => 450,
                    "last_purchase_price" => 470,
                    "purchase_price" => 420,
                    "selling_price" => 0,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "product_name" => "Bottle Label 600ml",
                    "product_code" => "BL-600ML",
                    "category" => "Packaging",
                    "tags" => [
                        ["name" => "PACKAGING", "color" => "warning"],
                        ["name" => "CONSUMABLES", "color" => "secondary"]
                    ],
                    "total_stock" => 30000,
                    "available_qty" => 28500,
                    "minimum" => 10000,
                    "unit" => "pcs",
                    "average_price" => 150,
                    "last_purchase_price" => 160,
                    "purchase_price" => 140,
                    "selling_price" => 0,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "product_name" => "Water Filtration Cartridge",
                    "product_code" => "WFC-STD",
                    "category" => "Maintenance",
                    "tags" => [
                        ["name" => "MAINTENANCE", "color" => "danger"],
                        ["name" => "CONSUMABLES", "color" => "secondary"]
                    ],
                    "total_stock" => 45,
                    "available_qty" => 38,
                    "minimum" => 20,
                    "unit" => "pcs",
                    "average_price" => 250000,
                    "last_purchase_price" => 260000,
                    "purchase_price" => 240000,
                    "selling_price" => 0,
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 6,
                    "product_name" => "Bottle Cap 28mm",
                    "product_code" => "BC-28MM",
                    "category" => "Packaging",
                    "tags" => [
                        ["name" => "PACKAGING", "color" => "warning"]
                    ],
                    "total_stock" => 0,
                    "available_qty" => 0,
                    "minimum" => 5000,
                    "unit" => "pcs",
                    "average_price" => 65,
                    "last_purchase_price" => 70,
                    "purchase_price" => 60,
                    "selling_price" => 0,
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

        $tags = [
            ['value' => 'FINISHED_GOODS', 'label' => 'FINISHED_GOODS - Barang Jadi', 'color' => 'success'],
            ['value' => 'RAW_MATERIALS', 'label' => 'RAW_MATERIALS - Bahan Baku', 'color' => 'info'],
            ['value' => 'PACKAGING', 'label' => 'PACKAGING - Kemasan', 'color' => 'warning'],
            ['value' => 'CONSUMABLES', 'label' => 'CONSUMABLES - Habis Pakai', 'color' => 'secondary'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Perawatan', 'color' => 'danger']
        ];

        // Permission check for adding new tags
        $canAddTags = true; // Mock permission check

        return view('pages.master_data.product.add', [
            'units' => $units,
            'categories' => $categories,
            'accounts' => $accounts,
            'taxes' => $taxes,
            'products' => $products,
            'tags' => $tags,
            'canAddTags' => $canAddTags
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