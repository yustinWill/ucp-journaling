<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Domains\Storage\Services\StorageService;
use App\Domains\Storage\Services\StorageAuditLogService;
use App\Domains\Storage\Actions\CreateStorageAction;
use App\Domains\Storage\Actions\UpdateStorageAction;

class StorageController extends Controller
{
    public function index()
    {
        $storageStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance']
        ];

        // Get PIC users from database
        $picUsers = DB::table('users')
            ->select('id as value', DB::raw("CONCAT(id, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->get()
            ->toArray();

        // Get available tags
        $availableTags = DB::table('m_tags')
            ->select('id as value', DB::raw("CONCAT(code, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->get()
            ->toArray();

        return view('pages.master_data.storage.index', [
            'storageStatuses' => $storageStatuses,
            'picUsers' => $picUsers,
            'availableTags' => $availableTags
        ]);
    }

    public function index_data_table()
    {
        return response()->json(app(StorageService::class)->findAllDatatable(), 200);
    }

    public function add()
    {
        $storageStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance']
        ];

        // Get PIC users from database
        $picUsers = DB::table('users')
            ->select('id as value', DB::raw("CONCAT(id, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->get()
            ->toArray();

        // Get available tags
        $availableTags = DB::table('m_tags')
            ->select('id as value', DB::raw("CONCAT(code, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->get()
            ->toArray();

        return view('pages.master_data.storage.add', [
            'storageStatuses' => $storageStatuses,
            'picUsers' => $picUsers,
            'availableTags' => $availableTags
        ]);
    }

    public function edit($id)
    {
        $storageStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance']
        ];

        // Get PIC users from database
        $picUsers = DB::table('users')
            ->select('id as value', DB::raw("CONCAT(id, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->get()
            ->toArray();

        // Get available tags
        $availableTags = DB::table('m_tags')
            ->select('id as value', DB::raw("CONCAT(code, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->get()
            ->toArray();

        // Get storage data from database
        $storage = app(StorageService::class)->findOne($id);

        return view('pages.master_data.storage.edit', [
            'storageStatuses' => $storageStatuses,
            'picUsers' => $picUsers,
            'availableTags' => $availableTags,
            'storage' => $storage
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = [
                'code' => $request->code,
                'name' => $request->name,
                'pic_users' => $request->pic_users ?? [],
                'status' => $request->status,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'sub_district' => $request->sub_district,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'tags' => $request->tags ?? []
            ];

            $result = app(CreateStorageAction::class)->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil ditambahkan',
                'data' => $result
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan gudang: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = [
                'code' => $request->code,
                'name' => $request->name,
                'pic_users' => $request->pic_users ?? [],
                'status' => $request->status,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'sub_district' => $request->sub_district,
                'address' => $request->address,
                'postal_code' => $request->postal_code,
                'tags' => $request->tags ?? []
            ];

            $result = app(UpdateStorageAction::class)->execute($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Gudang berhasil diperbarui',
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui gudang: ' . $e->getMessage()
            ], 500);
        }
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
        $storage = app(StorageService::class)->findOne($id);
        $auditLogs = app(StorageAuditLogService::class)->findAllBasedOnId($id);
        
        return view('pages.master_data.storage.detail', compact('storage', 'auditLogs'));
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
