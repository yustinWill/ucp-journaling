<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Validation\ValidationException;
use App\Exceptions\AppException;
use App\Helpers\ValidationHelper;
use App\Helpers\DiscordNotifier;

use App\Domains\Product\Actions\CreateProductAction;
use App\Domains\StockStatus\Services\StockStatusService;
use App\Domains\Category\Services\CategoryService;
use App\Domains\Unit\Services\UnitService;
use App\Domains\Tag\Services\TagService;
use App\Domains\Coa\Services\CoaService;
use App\Domains\Tax\Services\TaxService;
use App\Domains\Product\Services\ProductService;


//Enums
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;

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

        return view('pages.master_data.product.add', [
            'units' => $units,
            'categories' => $categories,
            'accounts' => $accounts,
            'taxes' => $taxes,
            'products' => $products,
            'tags' => $tags,
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
                            $fail('The track batch field is required when type is bundle and inventory enabled is true.');
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
                            $fail('The track batch field is required when type is bundle and inventory enabled is true.');
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
                            $fail('The track batch field is required when type is bundle and inventory enabled is true.');
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
                            $fail('The track batch field is required when type is bundle and inventory enabled is true.');
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
            'estimated_cogs' => 'Rp 2,750',
            'profit_margin' => '31.25%',
            'profit_per_unit' => 'Rp 1,250',
            'minimum_stock' => '100',
            'inventory_account' => '[1301] - Persediaan Barang Dagang',
            'track_quantity' => true,
            'track_batch' => false,
            'image' => null,
            'tags' => [
                ['name' => 'FINISHED_GOODS', 'color' => 'success'],
                ['name' => 'POPULAR', 'color' => 'primary']
            ],
            'memo' => 'Produk utama RIYO Water dengan tingkat penjualan tertinggi. Diproduksi dengan teknologi RO dan UV sterilization.',
            'last_updated' => '2025-01-21 14:30:25'
        ];

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

        // Audit log data with standard 4-column structure
        $auditLogs = [
            [
                'created_at' => '2025-01-20 14:30:25',
                'user_id' => 'USR-001',
                'user_name' => 'Admin User',
                'action_type' => 'UPDATE',
                'description' => 'Memperbarui harga jual dari Rp 3,800 menjadi Rp 4,000'
            ],
            [
                'created_at' => '2025-01-18 09:15:10',
                'user_id' => 'USR-002',
                'user_name' => 'Manager Warehouse',
                'action_type' => 'UPDATE',
                'description' => 'Mengubah batas stok minimum dari 50 menjadi 100 unit'
            ],
            [
                'created_at' => '2025-01-15 16:45:33',
                'user_id' => 'USR-001',
                'user_name' => 'Admin User',
                'action_type' => 'CREATE',
                'description' => 'Produk RIYO Water 600ml berhasil dibuat dengan kode PROD-2025-001'
            ]
        ];

        return view('pages.master_data.product.detail', compact('product', 'inventoryData', 'auditLogs'));
    }

    public function edit($id)
    {
        $units = [
            ['value' => 'btl', 'label' => 'BTL - Botol'],
            ['value' => 'dus', 'label' => 'DUS - Dus'],
            ['value' => 'kg', 'label' => 'KG - Kilogram'],
            ['value' => 'ltr', 'label' => 'LTR - Liter'],
            ['value' => 'pcs', 'label' => 'PCS - Pieces']
        ];

        $categories = [
            ['value' => 'finished_goods', 'label' => 'FINISHED_GOODS - Produk Jadi'],
            ['value' => 'raw_materials', 'label' => 'RAW_MATERIALS - Bahan Baku'],
            ['value' => 'packaging', 'label' => 'PACKAGING - Kemasan'],
            ['value' => 'chemicals', 'label' => 'CHEMICALS - Bahan Kimia']
        ];

        $accounts = [
            ['value' => '5001', 'label' => '[5001] - Pembelian Barang Dagang'],
            ['value' => '4001', 'label' => '[4001] - Penjualan Barang Dagang'],
            ['value' => '1003', 'label' => '[1003] - Persediaan Barang Dagang'],
            ['value' => '5002', 'label' => '[5002] - Biaya Pembelian']
        ];

        $taxes = [
            ['value' => 'ppn_11', 'label' => '[PPN11] - PPN 11%'],
            ['value' => 'ppn_0', 'label' => '[PPN0] - PPN 0%'],
            ['value' => 'no_tax', 'label' => '[NOTAX] - Tanpa Pajak']
        ];

        $tags = [
            ['value' => 'finished_goods', 'label' => 'FINISHED_GOODS - Produk Jadi', 'color' => 'success'],
            ['value' => 'retail', 'label' => 'RETAIL - Produk Retail', 'color' => 'info'],
            ['value' => 'premium', 'label' => 'PREMIUM - Produk Premium', 'color' => 'warning'],
            ['value' => 'popular', 'label' => 'POPULAR - Produk Populer', 'color' => 'primary']
        ];

        // Sample product data for editing
        $product = [
            'id' => $id,
            'name' => 'RIYO Water 600ml',
            'unit' => 'btl',
            'code' => 'PROD-2025-001',
            'category' => 'finished_goods',
            'description' => 'Air minum dalam kemasan botol 600ml berkualitas tinggi dengan proses filtrasi canggih',
            'image' => '/custom_assets/images/riyo-water-600ml.jpg',
            'type' => 'single',
            'enable_purchase' => true,
            'purchase_price' => 'Rp 1,200',
            'purchase_account' => '5001',
            'purchase_tax' => 'ppn_11',
            'enable_sales' => true,
            'sales_price' => 'Rp 2,500',
            'sales_account' => '4001',
            'sales_tax' => 'ppn_11',
            'enable_inventory' => true,
            'minimum_stock' => '1000',
            'inventory_account' => '1003',
            'track_quantity' => true,
            'track_batch' => false,
            'tags' => ['finished_goods', 'retail'],
            'memo' => 'Produk utama RIYO Water dengan margin keuntungan terbaik'
        ];

        $canAddTags = true; // Permission-based flag

        return view('pages.master_data.product.edit', [
            'units' => $units,
            'categories' => $categories,
            'accounts' => $accounts,
            'taxes' => $taxes,
            'tags' => $tags,
            'product' => $product,
            'canAddTags' => $canAddTags
        ]);
    }
}
