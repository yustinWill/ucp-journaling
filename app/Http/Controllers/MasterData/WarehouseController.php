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
use App\Helpers\PermissionHelper;
use App\Helpers\ValidationHelper;
use App\Http\Controllers\Controller;
use App\Domains\Category\Services\CategoryService;
use App\Domains\StockMovement\Services\StockMovementLogsService;
// Enums
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function stock_transfer_index()
    {
        return view('pages.master_data.warehouse.stock_transfer_index');
    }

    public function stock_transfer_index_data_table()
    {
        $query = DB::table('t_stock_adjustments as sa')
            ->leftJoin('m_warehouses as w', 'sa.warehouse_id', '=', 'w.id')
            ->leftJoin('m_contacts as c', 'sa.pic_id', '=', 'c.id')
            ->where('sa.company_code', session('user_company_code'))
            ->where('sa.adjustment_type', 'WAREHOUSE_TRANSFER')
            ->select(
                'sa.id',
                'sa.adjustment_date',
                'sa.adjustment_number',
                'sa.warehouse_id',
                'w.code as warehouse_code',
                'w.name as warehouse_name',
                'c.name as pic_name',
                'sa.total_items',
                'sa.total_adjustment_qty',
                'sa.status',
                'sa.description',
                'sa.created_at'
            )
            ->orderBy('sa.created_at', 'DESC');

        $draw = (int) request()->input('draw', 1);
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);

        $recordsTotal = DB::table('t_stock_adjustments')
            ->where('company_code', session('user_company_code'))
            ->where('adjustment_type', 'WAREHOUSE_TRANSFER')
            ->count();

        $recordsFiltered = clone $query;
        $recordsFiltered = $recordsFiltered->count();

        $data = $query->skip($start)->take($length)->get();

        // Get destination warehouse for each transfer
        foreach ($data as $row) {
            $destinationWarehouse = DB::table('t_stock_movement_logs')
                ->join('m_warehouses', 't_stock_movement_logs.warehouse_id', '=', 'm_warehouses.id')
                ->where('reference_type', 'STOCK_TRANSFER')
                ->where('reference_number', $row->adjustment_number)
                ->where('movement_type', 'ADJUSTMENT_IN')
                ->select('m_warehouses.id', 'm_warehouses.code', 'm_warehouses.name')
                ->first();

            $row->destination_warehouse_id = $destinationWarehouse->id ?? null;
            $row->destination_warehouse_code = $destinationWarehouse->code ?? '-';
            $row->destination_warehouse_name = $destinationWarehouse->name ?? '-';
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ], 200);
    }

    public function stock_transfer_add(Request $request)
    {
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
        $contactsData = app(ContactService::class)->findAllByFlag('employee');
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
        $canAddTags = true;
        $canAddWarehouses = true;
        $canAddContacts = true;
        $canAddCategories = true;

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

    public function stock_transfer_detail($id)
    {
        // Get stock adjustment record
        $adjustment = DB::table('t_stock_adjustments as sa')
            ->leftJoin('m_warehouses as w', 'sa.warehouse_id', '=', 'w.id')
            ->leftJoin('m_contacts as c', 'sa.pic_id', '=', 'c.id')
            ->where('sa.id', $id)
            ->where('sa.adjustment_type', 'WAREHOUSE_TRANSFER')
            ->select(
                'sa.*',
                'w.code as warehouse_code',
                'w.name as warehouse_name',
                'c.name as pic_name'
            )
            ->first();

        if (!$adjustment) {
            abort(404, 'Stock transfer record not found');
        }

        // Get destination warehouse from movement logs (ADJUSTMENT_IN)
        $destinationWarehouse = DB::table('t_stock_movement_logs')
            ->join('m_warehouses', 't_stock_movement_logs.warehouse_id', '=', 'm_warehouses.id')
            ->where('reference_type', 'STOCK_TRANSFER')
            ->where('reference_number', $adjustment->adjustment_number)
            ->where('movement_type', 'ADJUSTMENT_IN')
            ->select('m_warehouses.id', 'm_warehouses.code', 'm_warehouses.name')
            ->first();

        // Get adjustment items (products transferred)
        $items = DB::table('t_stock_adjustment_items')
            ->where('stock_adjustment_id', $id)
            ->where('adjustment_qty', '>', 0) // Only get positive adjustments (the OUT has negative qty)
            ->get();

        // Parse tags
        $tags = [];
        if (!empty($adjustment->f_tag_codes)) {
            $tagCodes = json_decode($adjustment->f_tag_codes, true);
            if (is_array($tagCodes)) {
                $tags = DB::table('m_tags as t')
                    ->leftJoin('m_tag_colors as tc', 't.f_tag_color_id', '=', 'tc.id')
                    ->whereIn('t.code', $tagCodes)
                    ->where('t.company_code', session('user_company_code'))
                    ->select('t.code', 't.name', 'tc.code as tag_color_code')
                    ->get();
            }
        }

        return view('pages.master_data.warehouse.stock_transfer_detail', [
            'adjustment' => $adjustment,
            'destinationWarehouse' => $destinationWarehouse,
            'items' => $items,
            'tags' => $tags,
        ]);
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
        $productWareHouseStocks = app(StockMovementLogsService::class)->findAllLatestStockPerWarehouse($id);
        $StockMovementLogs = app(StockMovementLogsService::class)->findAllLogBasedOnWarehouseId($id);
        return view('pages.master_data.warehouse.detail', compact('warehouse', 'auditLogs', 'productWareHouseStocks','StockMovementLogs'));
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
