<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\Tag\Services\TagService;
use App\Domains\User\Services\UserService;
use App\Domains\Warehouse\Actions\CreateWarehouseAction;
use App\Domains\Warehouse\Actions\UpdateWarehouseAction;
use App\Domains\Warehouse\Services\WarehouseAuditLogService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
// Enums
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouseStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance'],
        ];

        // Get available tags
        $tags = app(TagService::class)->findAll();

        return view('pages.master_data.warehouse.index', [
            'warehouseStatuses' => $warehouseStatuses,
            'availableTags' => $tags,
        ]);
    }

    public function index_data_table()
    {
        return response()->json(app(WarehouseService::class)->findAllDatatable(), 200);
    }

    public function add()
    {
        $warehouseStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance'],
        ];

        // Get PIC users from database
        $picUsers = app(UserService::class)->findAll();

        // Get available tags
        $availableTags = app(TagService::class)->findAll();

        return view('pages.master_data.warehouse.add', [
            'warehouseStatuses' => $warehouseStatuses,
            'picUsers' => $picUsers,
            'availableTags' => $availableTags,
        ]);
    }

    public function edit($id)
    {
        $warehouseStatuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif'],
            ['value' => 'MAINTENANCE', 'label' => 'MAINTENANCE - Maintenance'],
        ];

        // Get PIC users from database
        $picUsers = app(UserService::class)->findAll();

        // Get available tags
        $availableTags = app(TagService::class)->findAll();

        // Get warehouse data from database
        $warehouse = app(WarehouseService::class)->findOne($id);

        return view('pages.master_data.warehouse.edit', [
            'warehouseStatuses' => $warehouseStatuses,
            'picUsers' => $picUsers,
            'availableTags' => $availableTags,
            'warehouse' => $warehouse,
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
                'tags' => $request->tags ?? [],
            ];

            app(CreateWarehouseAction::class)->execute($data);

            return response()->json([
                'message' => SuccessCodes::CREATE_WEARHOUSE_SUCCESS->message(),
            ], SuccessCodes::CREATE_WEARHOUSE_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
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
                'tags' => $request->tags ?? [],
            ];

            app(UpdateWarehouseAction::class)->execute($id, $data);

            return response()->json([
                'message' => SuccessCodes::UPDATE_WEARHOUSE_SUCCESS->message(),
            ], SuccessCodes::UPDATE_WEARHOUSE_SUCCESS->httpStatus());
        } catch (ValidationException $e) {
            return response()->json([
                'message' => ValidationHelper::formatErrors($e->errors()),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
            ], ErrorCodes::FORM_VALIDATION_ERROR->httpStatus());
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Throwable $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    public function export()
    {
        $data = [
            ['no' => 1, 'value' => 'Kode Gudang'],
            ['no' => 2, 'value' => 'Nama Gudang'],
            ['no' => 3, 'value' => 'Penanggung Jawab'],
            ['no' => 4, 'value' => 'Status Gudang'],
            ['no' => 5, 'value' => 'Alamat Gudang'],
        ];

        return view('pages.master_data.warehouse.export', ['data' => $data]);
    }

    public function import()
    {
        return view('pages.master_data.warehouse.import', []);
    }

    public function detail($id)
    {
        $warehouse = app(WarehouseService::class)->findOne($id);
        $auditLogs = app(WarehouseAuditLogService::class)->findAllBasedOnId($id);

        return view('pages.master_data.warehouse.detail', compact('warehouse', 'auditLogs'));
    }

    public function detail_product_data_table()
    {
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'product_code' => 'PRDA',
                    'product_name' => 'Produk A',
                    'category' => 'Category A',
                    'total_stock' => 100,
                    'available_qty' => 90,
                    'minimum' => 10,
                    'unit' => 'pcs',
                    'average_price' => 9500,
                    'last_purchase_price' => 10000,
                    'purchase_price' => 9000,
                    'selling_price' => 12000,
                ],
                [
                    'id' => 1,
                    'product_code' => 'PRDA',
                    'product_name' => 'Produk A',
                    'category' => 'Category A',
                    'total_stock' => 100,
                    'available_qty' => 90,
                    'minimum' => 10,
                    'unit' => 'pcs',
                    'average_price' => 9500,
                    'last_purchase_price' => 10000,
                    'purchase_price' => 9000,
                    'selling_price' => 12000,
                ],
                [
                    'id' => 1,
                    'product_code' => 'PRDA',
                    'product_name' => 'Produk A',
                    'category' => 'Category A',
                    'total_stock' => 100,
                    'available_qty' => 90,
                    'minimum' => 10,
                    'unit' => 'pcs',
                    'average_price' => 9500,
                    'last_purchase_price' => 10000,
                    'purchase_price' => 9000,
                    'selling_price' => 12000,
                ],
                [
                    'id' => 1,
                    'product_code' => 'PRDA',
                    'product_name' => 'Produk A',
                    'category' => 'Category A',
                    'total_stock' => 100,
                    'available_qty' => 90,
                    'minimum' => 10,
                    'unit' => 'pcs',
                    'average_price' => 9500,
                    'last_purchase_price' => 10000,
                    'purchase_price' => 9000,
                    'selling_price' => 12000,
                ],
                [
                    'id' => 1,
                    'product_code' => 'PRDA',
                    'product_name' => 'Produk A',
                    'category' => 'Category A',
                    'total_stock' => 100,
                    'available_qty' => 90,
                    'minimum' => 10,
                    'unit' => 'pcs',
                    'average_price' => 9500,
                    'last_purchase_price' => 10000,
                    'purchase_price' => 9000,
                    'selling_price' => 12000,
                ],
                [
                    'id' => 1,
                    'product_code' => 'PRDA',
                    'product_name' => 'Produk A',
                    'category' => 'Category A',
                    'total_stock' => 100,
                    'available_qty' => 90,
                    'minimum' => 10,
                    'unit' => 'pcs',
                    'average_price' => 9500,
                    'last_purchase_price' => 10000,
                    'purchase_price' => 9000,
                    'selling_price' => 12000,
                ],
            ],
        ];

        return response()->json($data, 200);
    }

    public function detail_transaction_data_table()
    {
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'transaction_date' => '2025-06-01',
                    'transaction_code' => 'Purchase Order #1009',
                    'contact' => 'SEVENTEEN CORP',
                    'due_date' => '2025-07-01',
                    'status' => 'Belum Dibayar',
                    'down_payment_amount' => 0,
                    'total_amount' => 27750000,
                ],
            ],
        ];

        return response()->json($data, 200);
    }

    public function detail_batch_data_table()
    {
        $data = [
            'data' => [
                [
                    'id' => 1,
                    'batch_code' => 'BATCH-PRC001',
                    'product_name' => 'Paracetamol 500mg',
                    'received_date' => '2025-06-01',
                    'expired_date' => '2026-06-01',
                    'remaining_qty' => 600,
                    'price_per_unit' => 800,
                    'status' => 'Aktif',
                    'transaction_source' => 'Pembelian',
                    'transaction_code' => 'PB-00045',
                    'notes' => 'Supplier: PT Sehat Selalur',
                    'last_updated' => '-',
                ],
            ],
        ];

        return response()->json($data, 200);
    }
}
