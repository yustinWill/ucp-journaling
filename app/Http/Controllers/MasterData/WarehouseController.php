<?php

namespace App\Http\Controllers\MasterData;

use App\Domains\Tag\Services\TagService;
use App\Domains\User\Services\UserService;
use App\Domains\Warehouse\Actions\CreateWarehouseAction;
use App\Domains\Warehouse\Actions\UpdateWarehouseAction;
use App\Domains\Warehouse\Actions\CreateStockTransferAction;
use App\Domains\Warehouse\Services\WarehouseAuditLogService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Domains\Contact\Services\ContactService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use App\Domains\Category\Services\CategoryService;
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

    public function stock_transfer(Request $request)
    {
        // Check write permission
        // if (! PermissionHelper::hasPermission('main_transaction_stock_adjustment', 'write')) {
        //     abort(403, 'Anda tidak memiliki akses untuk menambah data.');
        // }
        // Adjustment types
        if (isset($request->id) && isset($request->type)) {
            $adjustmentTypes = [
                ['value' => 'STOCK_OPNAME', 'label' => 'STOCK_OPNAME - Hasil Stock Opname'],
                ['value' => 'PRODUCT_RETURN', 'label' => 'PRODUCT_RETURN - Retur Produk'],
                ['value' => 'DAMAGE_LOSS', 'label' => 'DAMAGE_LOSS - Kerusakan/Kehilangan'],
                ['value' => 'EXPIRY_DISPOSAL', 'label' => 'EXPIRY_DISPOSAL - Produk Kadaluarsa'],
            ];
        } else {
            $adjustmentTypes = [
                ['value' => 'MANUAL_ADJUSTMENT', 'label' => 'MANUAL_ADJUSTMENT - Penyesuaian Manual'],
            ];
        }

        // Get warehouses from service
        $warehousesData = app(WarehouseService::class)->findAll();
        $warehouses = [];
        foreach ($warehousesData as $warehouse) {
            $warehouses[] = [
                'value' => $warehouse->id,
                'label' => $warehouse->code.' - '.$warehouse->name,
            ];
        }

        // Get employees from contact service
        $contactsData = app(ContactService::class)->findAllByType('EMPLOYEE');
        $picOptions = [];
        foreach ($contactsData as $contact) {
            $picOptions[] = [
                'value' => $contact->id,
                'label' => $contact->name,
            ];
        }

        // Status options for new adjustment
        // $statusOptions = [
        //     ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
        //     ['value' => 'WAITING_APPROVAL', 'label' => 'WAITING_APPROVAL - Menunggu Persetujuan'],
        // ];

        // Get product categories from service
        $categoriesData = app(CategoryService::class)->findAll();
        $productCategories = [];
        foreach ($categoriesData as $category) {
            $productCategories[] = [
                'value' => $category->id,
                'label' => $category->code.' - '.$category->name,
            ];
        }

        // Get tags from service
        $tagsData = app(TagService::class)->findAll();
        $availableTags = [];
        foreach ($tagsData as $tag) {
            $availableTags[] = [
                'code' => $tag->code,
                'label' => $tag->code.' - '.$tag->name,
                'color' => $tag->tag_color_code ?? 'primary',
            ];
        }

        // Permission checks for add buttons
        $canAddTags = PermissionHelper::canWrite('master_data_tag');
        $canAddWarehouses = PermissionHelper::canWrite('master_data_warehouse');
        $canAddContacts = PermissionHelper::canWrite('master_data_contact');
        $canAddCategories = PermissionHelper::canWrite('master_data_category');

        $header_data = [];
        $items = [];

        return view('pages.master_data.warehouse.stock_transfer', [
            'adjustmentTypes' => $adjustmentTypes,
            'warehouses' => $warehouses,
            'picOptions' => $picOptions,
            // 'statusOptions' => $statusOptions,
            'productCategories' => $productCategories,
            'availableTags' => $availableTags,
            'canAddTags' => $canAddTags,
            'canAddWarehouses' => $canAddWarehouses,
            'canAddContacts' => $canAddContacts,
            'canAddCategories' => $canAddCategories,
            'header_data' => $header_data,
            'items' => $items,
        ]);
    }

    public function stock_transfer_save(Request $request)
    {
        try {
            $validated = $request->validate([
                'adjustment_date' => 'required|date',
                'warehouse_id' => 'required|integer|exists:m_warehouses,id',
                'warehouse_dest_id' => 'required|integer|exists:m_warehouses,id',
                'pic_id' => 'nullable|integer|exists:m_contacts,id',
                'description' => 'required|string|max:1000',
                'memo' => 'nullable|string|max:500',
                'form_type' => 'nullable|string|max:500',
                'form_reference_id' => 'nullable|string|max:500',
                'tags' => 'nullable|array',
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer',
                'products.*.product_code' => 'required|string',
                'products.*.product_name' => 'required|string',
                'products.*.unit' => 'required|string',
                'products.*.stock_before' => 'required|numeric',
                'products.*.adjustment_qty' => 'required|numeric|not_in:0',
                'products.*.reason' => 'nullable|string|max:200',
            ], [], [
                'adjustment_date' => 'Tanggal Adjustment',
                'adjustment_type' => 'Tipe Adjustment',
                'warehouse_id' => 'Gudang Sumber',
                'warehouse_dest_id' => 'Gudang Tujuan',
                'pic_id' => 'PIC',
                'description' => 'Deskripsi',
                'memo' => 'Memo',
                'form_reference_id' => 'ID Referensi Form',
                'tags' => 'Tag',
                'products' => 'Produk',
                'products.*.product_id' => 'ID Produk',
                'products.*.product_code' => 'Kode Produk',
                'products.*.product_name' => 'Nama Produk',
                'products.*.unit' => 'Satuan',
                'products.*.stock_before' => 'Stok Sebelum',
                'products.*.adjustment_qty' => 'Qty Adjustment',
                'products.*.reason' => 'Alasan Adjustment',
            ]);

            app(CreateStockTransferAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_STOCK_TRANSFER_SUCCESS->message(),
            ], SuccessCodes::CREATE_STOCK_TRANSFER_SUCCESS->httpStatus());
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
            $validated = $request->validate([
                'code' => 'required|string|max:255|unique:m_warehouses,code',
                'name' => 'required|string|max:255',
                'pic_users' => 'nullable|array',
                'pic_users.*' => 'integer|exists:users,id',
                'status' => 'required|in:ACTIVE,INACTIVE,MAINTENANCE',
                'province' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'sub_district' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
            ], [], [
                'code' => 'Kode Gudang',
                'name' => 'Nama Gudang',
                'pic_users' => 'Penanggung Jawab',
                'pic_users.*' => 'Penanggung Jawab',
                'status' => 'Status Gudang',
                'province' => 'Provinsi',
                'city' => 'Kota',
                'district' => 'Kecamatan',
                'sub_district' => 'Kelurahan',
                'address' => 'Alamat Gudang',
                'postal_code' => 'Kode Pos',
                'tags' => 'Tag Gudang',
                'tags.*' => 'Tag Gudang',
            ]);
            app(CreateWarehouseAction::class)->execute($validated);

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
            $validated = $request->validate([
                'code' => 'required|string|max:255|exists:m_warehouses,code',
                'name' => 'required|string|max:255',
                'pic_users' => 'nullable|array',
                'pic_users.*' => 'integer|exists:users,id',
                'status' => 'required|in:ACTIVE,INACTIVE,MAINTENANCE',
                'province' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255',
                'sub_district' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:10',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
            ], [], [
                'code' => 'Kode Gudang',
                'name' => 'Nama Gudang',
                'pic_users' => 'Penanggung Jawab',
                'pic_users.*' => 'Penanggung Jawab',
                'status' => 'Status Gudang',
                'province' => 'Provinsi',
                'city' => 'Kota',
                'district' => 'Kecamatan',
                'sub_district' => 'Kelurahan',
                'address' => 'Alamat Gudang',
                'postal_code' => 'Kode Pos',
                'tags' => 'Tag Gudang',
                'tags.*' => 'Tag Gudang',
            ]);

            app(UpdateWarehouseAction::class)->execute($id, $validated);

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
