<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;

use App\Domains\Product\Actions\CreateProductAction;
use App\Domains\Product\Actions\UpdateProductAction;
use App\Domains\StockStatus\Services\StockStatusService;
use App\Domains\Category\Services\CategoryService;
use App\Domains\Unit\Services\UnitService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Tax\Services\TaxService;
use App\Domains\Product\Services\ProductService;
use App\Domains\Product\Services\ProductAuditLogService;
use App\Domains\Product\Services\ProductCompositionService;
use App\Domains\Product\Services\ProductExpenseService;
//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Enums\AuditLogTypes;

class ProductController extends Controller
{
    public function index()
    {
        $productCategories = app(CategoryService::class)->findAll();

        $stockStatuses = app(StockStatusService::class)->findAll();

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        return view('pages.master_data.product.index', [
            'productCategories' => $productCategories,
            'stockStatuses' => $stockStatuses,
            'statuses' => $statuses
        ]);
    }

    public function index_data_table()
    {
        $data = app(ProductService::class)->findAllDatatable();
        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
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
        $categories = app(CategoryService::class)->findAll();
        $tags = app(TagService::class)->findAll();
        $units = app(UnitService::class)->findAll();
        $accounts = app(CoaService::class)->findAll();
        $taxes = app(TaxService::class)->findAll();
        $products = app(ProductService::class)->findAll();

        // Permission checks for adding new options
        $canAddTags = true; // Mock permission check
        $canAddUnits = true; // Mock permission check
        $canAddCategories = true; // Mock permission check
        $canAddAccounts = true; // Mock permission check
        $canAddTaxes = true; // Mock permission check

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        return view('pages.master_data.product.add', [
            'units' => $units,
            'categories' => $categories,
            'accounts' => $accounts,
            'taxes' => $taxes,
            'products' => $products,
            'tags' => $tags,
            'statuses' => $statuses,
            'canAddTags' => $canAddTags,
            'canAddUnits' => $canAddUnits,
            'canAddCategories' => $canAddCategories,
            'canAddAccounts' => $canAddAccounts,
            'canAddTaxes' => $canAddTaxes
        ]);
    }

    public function save(Request $req)
    {
        try {
            $validated = $req->validate([
                'name' => 'required|string|max:255',
                'unit_id' => 'required|integer|exists:m_units,id',
                'code' => 'required|string|max:255|unique:m_products,code',
                'category_id' => 'nullable|integer|exists:m_categories,id',
                'description' => 'nullable|string|max:1000',
                'memo' => 'nullable|string|max:1000',
                'status' => 'required|in:ACTIVE,INACTIVE',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'type' => 'required|in:single,bundle',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'purchase_enabled' => 'required_if:type,"single"|in:true,false',
                'sales_enabled' => 'required_if:type,"single"|in:true,false',
                'inventory_enabled' => 'required_if:type,"single"|in:true,false',
                'purchase_price' => [
                    'integer',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase price field is required when type is single and purchase enabled is true.');
                        }
                    }
                ],
                'purchase_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase account field is required when type is single and purchase enabled is true.');
                        }
                    }
                ],
                'purchase_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase tax field is required when type is single and purchase enabled is true.');
                        }
                    }
                ],
                'sales_price' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is single and sales enabled is true.');
                        }
                    }
                ],
                'sales_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is single and sales enabled is true.');
                        }
                    }
                ],
                'sales_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is single and sales enabled is true.');
                        }
                    }
                ],
                'minimum_stock' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The minimum stock field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],
                'inventory_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The inventory account field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],
                'track_quantity' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track quantity field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],

                'track_batch' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track batch field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_purchase_enabled' => 'required_if:type,"bundle"|in:true,false',
                'bundle_sales_enabled' => 'required_if:type,"bundle"|in:true,false',
                'bundle_inventory_enabled' => 'required_if:type,"bundle"|in:true,false',
                'bundle_purchase_price' => [
                    'integer',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase price field is required when type is bundle and purchase enabled is true.');
                        }
                    }
                ],
                'bundle_purchase_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase account field is required when type is bundle and purchase enabled is true.');
                        }
                    }
                ],
                'bundle_purchase_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase tax field is required when type is bundle and purchase enabled is true.');
                        }
                    }
                ],
                'bundle_sales_price' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is bundle and sales enabled is true.');
                        }
                    }
                ],
                'bundle_sales_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is bundle and sales enabled is true.');
                        }
                    }
                ],
                'bundle_sales_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is bundle and sales enabled is true.');
                        }
                    }
                ],
                'bundle_minimum_stock' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The minimum stock field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_inventory_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The inventory account field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_track_quantity' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track quantity field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_track_batch' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track batchs field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_components' => 'required_if:type,"bundle"|array',
                'bundle_components.*.product_id' => 'required_if:type,"bundle"|required_with:bundle_components|integer',
                'bundle_components.*.qty' => 'required_if:type,"bundle"|required_with:bundle_components|integer|min:1',
                // Bundle accounts
                'bundle_accounts' => [
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_accounts.*.account' => [
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account acc field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_accounts.*.cost' => [
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account cost field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
            ]);

            app(CreateProductAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::CREATE_PRODUCT_SUCCESS->message()
            ], SuccessCodes::CREATE_PRODUCT_SUCCESS->httpStatus());
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
        $product = app(ProductService::class)->findOne($id);

        if ($product == null) {
            abort(404);
        }

        $auditLogs = app(ProductAuditLogService::class)->findAllBasedOnId($id);

        // Inventory data per warehouse from controller
        $inventoryData = [
            [
                'warehouse_id' => 1,
                'warehouse_code' => 'GDG-001',
                'warehouse_name' => 'Gudang Utama Produksi',
                'available_stock' => 2500,
                'reserved_stock' => 150,
                'total_stock' => 2650,
                'average_price' => 'Rp 2,750',
                'inventory_value' => 'Rp 7,287,500'
            ],
            [
                'warehouse_id' => 2,
                'warehouse_code' => 'GDG-002',
                'warehouse_name' => 'Gudang Raw Material',
                'available_stock' => 800,
                'reserved_stock' => 50,
                'total_stock' => 850,
                'average_price' => 'Rp 2,700',
                'inventory_value' => 'Rp 2,295,000'
            ],
            [
                'warehouse_id' => 3,
                'warehouse_code' => 'GDG-003',
                'warehouse_name' => 'Gudang Produk Jadi',
                'available_stock' => 1200,
                'reserved_stock' => 80,
                'total_stock' => 1280,
                'average_price' => 'Rp 2,750',
                'inventory_value' => 'Rp 3,520,000'
            ]
        ];

        $auditLogs = app(ProductAuditLogService::class)->findAllBasedOnId($id);

        return view('pages.master_data.product.detail', [
            'product' => $product,
            'inventoryData' => $inventoryData,
            'auditLogs' => $auditLogs,
            'auditLogType' => AuditLogTypes::MASTER_DATA_PRODUCT->code()
        ]);
    }

    public function edit($id)
    {
        $product = app(ProductService::class)->findOne($id);

        if ($product == null) {
            abort(404);
        }

        $productCompositions = app(ProductCompositionService::class)->findAllBasedOnProductId($id);
        $productExpenses = app(ProductExpenseService::class)->findAllBasedOnProductId($id);

        $categories = app(CategoryService::class)->findAll();
        $tags = app(TagService::class)->findAll();
        $units = app(UnitService::class)->findAll();
        $accounts = app(CoaService::class)->findAll();
        $taxes = app(TaxService::class)->findAll();
        $products = app(ProductService::class)->findAll();

        $statuses = [
            ['value' => 'ACTIVE', 'label' => 'ACTIVE - Aktif'],
            ['value' => 'INACTIVE', 'label' => 'INACTIVE - Inaktif']
        ];

        // Permission checks for adding new options
        $canAddTags = true; // Mock permission check
        $canAddUnits = true; // Mock permission check
        $canAddCategories = true; // Mock permission check
        $canAddAccounts = true; // Mock permission check
        $canAddTaxes = true; // Mock permission check

        return view('pages.master_data.product.edit', [
            'units' => $units,
            'categories' => $categories,
            'accounts' => $accounts,
            'taxes' => $taxes,
            'products' => $products,
            'tags' => $tags,
            'statuses' => $statuses,
            'canAddTags' => $canAddTags,
            'canAddUnits' => $canAddUnits,
            'canAddCategories' => $canAddCategories,
            'canAddAccounts' => $canAddAccounts,
            'canAddTaxes' => $canAddTaxes,
            'product' => $product,
            'productCompositions' => $productCompositions,
            'productExpenses' => $productExpenses
        ]);
    }

    public function update(Request $req)
    {
        try {
            $validated = $req->validate([
                'id' => 'required|exists:m_products,id',
                'name' => 'required|string|max:255',
                'unit_id' => 'required|integer|exists:m_units,id',
                'code' => 'required|string|max:255|exists:m_products,code',
                'category_id' => 'nullable|integer|exists:m_categories,id',
                'description' => 'nullable|string|max:1000',
                'memo' => 'nullable|string|max:1000',
                'status' => 'required|in:ACTIVE,INACTIVE',
                'tags' => 'nullable|array',
                'tags.*' => 'integer|exists:m_tags,id',
                'type' => 'required|in:single,bundle',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
                'purchase_enabled' => 'required_if:type,"single"|in:true,false',
                'sales_enabled' => 'required_if:type,"single"|in:true,false',
                'inventory_enabled' => 'required_if:type,"single"|in:true,false',
                'purchase_price' => [
                    'numeric',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase price field is required when type is single and purchase enabled is true.');
                        }
                    }
                ],
                'purchase_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase account field is required when type is single and purchase enabled is true.');
                        }
                    }
                ],
                'purchase_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase tax field is required when type is single and purchase enabled is true.');
                        }
                    }
                ],
                'sales_price' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is single and sales enabled is true.');
                        }
                    }
                ],
                'sales_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is single and sales enabled is true.');
                        }
                    }
                ],
                'sales_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is single and sales enabled is true.');
                        }
                    }
                ],
                'minimum_stock' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The minimum stock field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],
                'inventory_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The inventory account field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],
                'track_quantity' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track quantity field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],

                'track_batch' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'single' &&
                            $req->input('inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track batch field is required when type is single and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_purchase_enabled' => 'required_if:type,"bundle"|in:true,false',
                'bundle_sales_enabled' => 'required_if:type,"bundle"|in:true,false',
                'bundle_inventory_enabled' => 'required_if:type,"bundle"|in:true,false',
                'bundle_purchase_price' => [
                    'numeric',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase price field is required when type is bundle and purchase enabled is true.');
                        }
                    }
                ],
                'bundle_purchase_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase account field is required when type is bundle and purchase enabled is true.');
                        }
                    }
                ],
                'bundle_purchase_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_purchase_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The purchase tax field is required when type is bundle and purchase enabled is true.');
                        }
                    }
                ],
                'bundle_sales_price' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is bundle and sales enabled is true.');
                        }
                    }
                ],
                'bundle_sales_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is bundle and sales enabled is true.');
                        }
                    }
                ],
                'bundle_sales_tax' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_sales_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The sales price field is required when type is bundle and sales enabled is true.');
                        }
                    }
                ],
                'bundle_minimum_stock' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The minimum stock field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_inventory_account' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The inventory account field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_track_quantity' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track quantity field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_track_batch' => [
                    'string',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The track batch field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_components' => 'required_if:type,"bundle"|array',
                'bundle_components.*.composition_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            empty($value)
                        ) {
                            $fail('The bundle account component composition id field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_components.*.product_id' => 'required_if:type,"bundle"|required_with:bundle_components|integer',
                'bundle_components.*.qty' => 'required_if:type,"bundle"|required_with:bundle_components|numeric|min:1',
                // Bundle accounts
                'bundle_accounts' => [
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_accounts.*.expense_id' => [
                    'nullable',
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account expense id field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_accounts.*.account' => [
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account account field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
                'bundle_accounts.*.cost' => [
                    function ($attribute, $value, $fail) use ($req) {
                        if (
                            $req->input('type') === 'bundle' &&
                            $req->input('bundle_inventory_enabled') === 'true' &&
                            empty($value)
                        ) {
                            $fail('The bundle account cost field is required when type is bundle and inventory enabled is true.');
                        }
                    }
                ],
            ]);

            app(UpdateProductAction::class)->execute($validated);

            return response()->json([
                'message' => SuccessCodes::UPDATE_PRODUCT_SUCCESS->message()
            ], SuccessCodes::UPDATE_PRODUCT_SUCCESS->httpStatus());
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
}
