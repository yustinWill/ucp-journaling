<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index()
    {
        return view('pages.master_data.storage.index', []);
    }

    public function index_data_table()
    {
        $data = [
            "data" => [
                [
                    "id" => 1,
                    "storage_code" => "STG01",
                    "storage_name" => "Main Storage",
                    "pic_names" => "William, Handoko, Winona",
                    "address" => "Jalan Kemanggisan Raya No. 10, Jakarta",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 2,
                    "storage_code" => "STG02",
                    "storage_name" => "Storage - A",
                    "pic_names" => "Vanessa, Jonathan",
                    "address" => "Jalan Kemanggisan Raya No. 11, Jakarta",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 3,
                    "storage_code" => "STG03",
                    "storage_name" => "Storage - B",
                    "pic_names" => "Vanessa, Jonathan",
                    "address" => "Jalan Kemanggisan Raya No. 12, Jakarta",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 4,
                    "storage_code" => "STG04",
                    "storage_name" => "Storage - C",
                    "pic_names" => "Hartono",
                    "address" => "Jalan Kemanggisan Raya No. 13, Jakarta",
                    "status" => "ACTIVE"
                ],
                [
                    "id" => 5,
                    "storage_code" => "STG05",
                    "storage_name" => "Storage - D",
                    "pic_names" => "Akbar",
                    "address" => "Jalan Kemanggisan Raya No. 14, Jakarta",
                    "status" => "ACTIVE"
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function add()
    {
        return view('pages.master_data.storage.add', []);
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

    public function detail()
    {
        return view('pages.master_data.storage.detail', []);
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
