<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        $storageStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance']
        ];

        $picUsers = [
            ['value' => 'USR-001', 'label' => 'USR-001 - Yustinus Komunal (Warehouse Manager)'],
            ['value' => 'USR-002', 'label' => 'USR-002 - Ahmad Rizki (Staff Gudang)'],
            ['value' => 'USR-003', 'label' => 'USR-003 - Siti Nurhaliza (Quality Control)'],
            ['value' => 'USR-004', 'label' => 'USR-004 - Budi Santoso (Supervisor Produksi)']
        ];

        return view('pages.master_data.storage.index', [
            'storageStatuses' => $storageStatuses,
            'picUsers' => $picUsers
        ]);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "storage_code" => "GDG-001",
                    "storage_name" => "Gudang Utama Produksi",
                    "pic_names" => "Yustinus Komunal, Ahmad Rizki",
                    "address" => "Jl. Raya Ciomas No. 15, Bogor Barat",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "storage_code" => "GDG-002",
                    "storage_name" => "Gudang Raw Material",
                    "pic_names" => "Siti Nurhaliza, Budi Santoso",
                    "address" => "Jl. Raya Ciomas No. 17, Bogor Barat",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "storage_code" => "GDG-003",
                    "storage_name" => "Gudang Produk Jadi",
                    "pic_names" => "Ahmad Rizki",
                    "address" => "Jl. Raya Ciomas No. 19, Bogor Barat",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "storage_code" => "GDG-004",
                    "storage_name" => "Gudang Kemasan & Label",
                    "pic_names" => "Siti Nurhaliza",
                    "address" => "Jl. Raya Ciomas No. 21, Bogor Barat",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "storage_code" => "GDG-005",
                    "storage_name" => "Gudang Quality Control",
                    "pic_names" => "Siti Nurhaliza, Dewi Kartika",
                    "address" => "Jl. Raya Ciomas No. 23, Bogor Barat",
                    "status" => "MAINTENANCE"
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        $picUsers = [
            ['value' => '1', 'label' => 'William'],
            ['value' => '2', 'label' => 'Winona'],
            ['value' => '3', 'label' => 'Akbar'],
            ['value' => '4', 'label' => 'Tono']
        ];

        return view('pages.master_data.storage.add', [
            'picUsers' => $picUsers
        ]);
    }

    public function edit($id)
    {
        $picUsers = [
            ['value' => 'USR-001', 'label' => 'USR-001 - Yustinus Komunal (Warehouse Manager)'],
            ['value' => 'USR-002', 'label' => 'USR-002 - Ahmad Rizki (Staff Gudang)'],
            ['value' => 'USR-003', 'label' => 'USR-003 - Siti Nurhaliza (Quality Control)'],
            ['value' => 'USR-004', 'label' => 'USR-004 - Budi Santoso (Supervisor Produksi)'],
            ['value' => 'USR-005', 'label' => 'USR-005 - Maya Sari (Admin Gudang)']
        ];

        // Sample data for editing - this would normally come from database
        $storage = [
            'id' => $id,
            'code' => 'GDG-001',
            'name' => 'Gudang Utama Pusat RIYO Water',
            'pic_users' => ['USR-003', 'USR-005'],
            'status' => 'ACTIVE',
            'province' => 'Jawa Tengah',
            'city' => 'Semarang',
            'district' => 'Genuk',
            'village' => 'Bangetayu Kulon',
            'address' => 'Jl. Industri Raya No. 15, Kawasan Industri Terboyo',
            'postal_code' => '50112'
        ];

        return view('pages.master_data.storage.edit', [
            'picUsers' => $picUsers,
            'storage' => $storage
        ]);
    }

    public function export()
    {
        $data = [
            ["no" => 1, "value" => "Kode Gudang"],
            ["no" => 2, "value" => "Nama Gudang"],
            ["no" => 3, "value" => "Penanggung Jawab"],
            ["no" => 4, "value" => "Status Gudang"],
            ["no" => 5, "value" => "Alamat Gudang"],
        ];
        return view('pages.master_data.storage.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.storage.import', []);
    }

    public function detail($id)
    {
        // Sample storage data for detail view - would come from database
        $storage = [
            'id' => $id,
            'storage_code' => 'GDG-001',
            'storage_name' => 'Gudang Utama Produksi RIYO Water',
            'pic_names' => 'Yustinus Komunal, Ahmad Rizki',
            'status' => 'ACTIVE',
            'address' => 'Jl. Raya Ciomas No. 15, Bogor Barat',
            'province' => 'Jawa Barat',
            'city' => 'Bogor',
            'district' => 'Ciomas',
            'village' => 'Pagelaran',
            'postal_code' => '16610',
            'capacity' => '5000 m³',
            'current_utilization' => '3500 m³',
            'last_updated' => '2025-01-21 16:45:12'
        ];
        
        return view('pages.master_data.storage.detail', compact('storage'));
    }

    public function detail_product_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "product_code" => "PRDA",
                    "product_name" => "Produk A",
                    "category" => "Category A",
                    "total_stock" => 100,
                    "available_qty" => 90,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                ],
                [
                    "id" => 1,
                    "product_code" => "PRDA",
                    "product_name" => "Produk A",
                    "category" => "Category A",
                    "total_stock" => 100,
                    "available_qty" => 90,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                ],
                [
                    "id" => 1,
                    "product_code" => "PRDA",
                    "product_name" => "Produk A",
                    "category" => "Category A",
                    "total_stock" => 100,
                    "available_qty" => 90,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                ],
                [
                    "id" => 1,
                    "product_code" => "PRDA",
                    "product_name" => "Produk A",
                    "category" => "Category A",
                    "total_stock" => 100,
                    "available_qty" => 90,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                ],
                [
                    "id" => 1,
                    "product_code" => "PRDA",
                    "product_name" => "Produk A",
                    "category" => "Category A",
                    "total_stock" => 100,
                    "available_qty" => 90,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                ],
                [
                    "id" => 1,
                    "product_code" => "PRDA",
                    "product_name" => "Produk A",
                    "category" => "Category A",
                    "total_stock" => 100,
                    "available_qty" => 90,
                    "minimum" => 10,
                    "unit" => "pcs",
                    "average_price" => 9500,
                    "last_purchase_price" => 10000,
                    "purchase_price" => 9000,
                    "selling_price" => 12000,
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function detail_transaction_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "transaction_date" => "2025-06-01",
                    "transaction_code" => "Purchase Order #1009",
                    "contact" => "SEVENTEEN CORP",
                    "due_date" => "2025-07-01",
                    "status" => "Belum Dibayar",
                    "down_payment_amount" => 0,
                    "total_amount" =>  27750000
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function detail_batch_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "batch_code" => "BATCH-PRC001",
                    "product_name" => "Paracetamol 500mg",
                    "received_date" => "2025-06-01",
                    "expired_date" => "2026-06-01",
                    "remaining_qty" => 600,
                    "price_per_unit" => 800,
                    "status" =>  "Aktif",
                    "transaction_source" => "Pembelian",
                    "transaction_code" => "PB-00045",
                    "notes" => "Supplier: PT Sehat Selalur",
                    "last_updated" => "-",
                ],
            ]
        ];
        return response()->json($data, 200);
    }
}
