<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Product\Services\ProductService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Domains\User\Services\UserService;
use App\Enums\SuccessCodes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackingController extends Controller
{
    public function index()
    {
        // Check read permission
        // if (! $this->hasPermission('packing.read')) {
        //     abort(403, 'Unauthorized access');
        // }

        // Get only bundle products marked as packaging products
        $bundleProducts = DB::table('m_products')
            ->where('company_code', session('user_company_code'))
            ->where('type', 'bundle')
            ->where('is_packaging', true)  // Only packaging products
            ->where('status', 'ACTIVE')
            ->select('id', 'code', 'name')
            ->get();

        $warehouses = app(WarehouseService::class)->findAll();

        return view('pages.main_transaction.packing.index', [
            'bundleProducts' => $bundleProducts,
            'warehouses' => $warehouses,
        ]);
    }

    public function index_data_table(Request $request)
    {
        // Group by transaction_number from t_packaging
        $query = DB::table('t_packaging as pkg')
            ->select(
                'pkg.transaction_number',
                DB::raw('MIN(p.production_date) as packing_date'),
                DB::raw('MIN(p.id) as id'), // First production ID for the transaction
                DB::raw('GROUP_CONCAT(DISTINCT p.production_number ORDER BY p.production_number SEPARATOR ", ") as packing_number'),
                DB::raw('GROUP_CONCAT(DISTINCT bp.name ORDER BY p.production_number SEPARATOR ", ") as bundle_product_name'),
                DB::raw('GROUP_CONCAT(DISTINCT bp.code ORDER BY p.production_number SEPARATOR ", ") as bundle_product_code'),
                DB::raw('SUM(pkg.quantity_packed) as quantity_packed'),
                DB::raw('MIN(w.name) as warehouse_name'),
                DB::raw('MIN(w.code) as warehouse_code'),
                DB::raw('MIN(u.name) as pic_name'),
                DB::raw('(SELECT tpi.unit_code FROM t_production_items as tpi WHERE tpi.production_id = MIN(p.id) LIMIT 1) as unit_code'),
                DB::raw("(SELECT AVG(CASE WHEN psb.hpp_status = 'SETTLED' THEN psb.final_hpp ELSE psb.estimated_hpp END) FROM t_product_stock_batches as psb WHERE psb.f_source_transaction_id IN (SELECT tp.id FROM t_productions tp WHERE tp.id IN (SELECT pkg2.production_id FROM t_packaging pkg2 WHERE pkg2.transaction_number = pkg.transaction_number)) AND psb.source_type = 'PRODUCTION') as hpp_packing"),
                DB::raw("(SELECT sml.movement_type FROM t_stock_movement_logs as sml WHERE sml.reference_type = 'PACKING_ADJUSTMENT' AND sml.reference_number LIKE CONCAT('ADJ-PACK-', '%') LIMIT 1) as adjustment_type"),
                DB::raw("(SELECT sml.movement_qty FROM t_stock_movement_logs as sml WHERE sml.reference_type = 'PACKING_ADJUSTMENT' AND sml.reference_number LIKE CONCAT('ADJ-PACK-', '%') LIMIT 1) as adjustment_qty")
            )
            ->leftJoin('t_productions as p', 'pkg.production_id', '=', 'p.id')
            ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->leftJoin('m_warehouses as w', 'p.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'p.pic_id', '=', 'u.id')
            ->where('p.company_code', session('user_company_code'))
            ->where('p.is_packaging', true);  // Filter only packing productions

        if ($date = request()->input('date')) {
            if (strpos($date, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $date);
                $query->whereBetween('p.production_date', [$dateFrom, $dateTo]);
            } else {
                $query->where('p.production_date', $date);
            }
        }

        if ($product_id = request()->input('product')) {
            $query->where('p.bundle_product_id', $product_id);
        }

        if ($warehouse_id = request()->input('warehouse')) {
            $query->where('p.warehouse_id', $warehouse_id);
        }

        if ($packing_no = request()->input('packing_no')) {
            $query->where(function($q) use ($packing_no) {
                $q->where('p.production_number', 'like', "%{$packing_no}%")
                  ->orWhere('pkg.transaction_number', 'like', "%{$packing_no}%");
            });
        }

        $query->groupBy('pkg.transaction_number');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table('t_packaging')
            ->join('t_productions', 't_packaging.production_id', '=', 't_productions.id')
            ->where('t_productions.is_packaging', true)
            ->distinct('t_packaging.transaction_number')
            ->count('t_packaging.transaction_number');

        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('pkg.transaction_number', 'DESC');
        $data = $query->get();

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ], SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    public function add()
    {
        $bundleProducts = app(ProductService::class)->findAllBundle();
        $warehouses = app(WarehouseService::class)->findAll();
        $picUsers = app(UserService::class)->findAll();

        // Production status options
        $productionStatuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PLANNED', 'label' => 'PLANNED - Planned'],
            ['value' => 'IN_PROGRESS', 'label' => 'IN_PROGRESS - In Progress'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Completed'],
        ];

        return view('pages.main_transaction.packing.add', [
            'bundleProducts' => $bundleProducts,
            'warehouses' => $warehouses,
            'bundleProducts' => $bundleProducts,
            'warehouses' => $warehouses,
            'picUsers' => $picUsers, // Changed from picOptions to match view usage
            'productionStatuses' => $productionStatuses,
        ]);
    }

    public function store(Request $request)
    {
        // Check permission
        // if (!$this->hasPermission('packing.write')) {
        //     return response()->json(['error' => 'Unauthorized'], 403);
        // }

        try {
            $data = $request->all();

            // Decode packaging products JSON
            $packagingProducts = json_decode($request->input('packaging_products'), true);
            $data['packaging_products'] = $packagingProducts;

            // Filter out packaging products with qty = 0
            $data['packaging_products'] = array_filter($data['packaging_products'], function($packaging) {
                return isset($packaging['quantity_packed']) && $packaging['quantity_packed'] > 0;
            });

            // Validate input data
            $createAction = app(\App\Domains\Production\Actions\CreatePackingAction::class);
            $validation = $createAction->validatePackingData($data);

            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Calculate bundle qty needed for each packaging product
            foreach ($data['packaging_products'] as &$packaging) {
                // bundle_qty_needed is already the qty per unit from database
                // We need total = qty_packed × qty_per_unit
                $bundleQtyNeeded = $packaging['quantity_packed'] * ($packaging['bundle_qty_needed'] ?? 0);
                $packaging['bundle_qty_needed'] = $bundleQtyNeeded;
            }

            // Execute creation (creates one production per packaging product)
            $productionIds = $createAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Data packing berhasil disimpan. ' . count($productionIds) . ' production record(s) created.',
                'production_ids' => $productionIds,
                'count' => count($productionIds)
            ]);

        } catch (\App\Exceptions\AppException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            \App\Helpers\DiscordNotifier::reportException($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    public function detail($id)
    {
        // Get the first production record details
        $production = DB::table('t_productions as p')
            ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->leftJoin('m_warehouses as w', 'p.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'p.pic_id', '=', 'u.id')
            ->leftJoin('m_units as unit', 'bp.f_unit_id', '=', 'unit.id')
            ->where('p.id', $id)
            ->where('p.is_packaging', true)
            ->select(
                'p.*',
                'bp.name as bundle_product_name',
                'bp.code as bundle_product_code',
                'w.name as warehouse_name',
                'w.code as warehouse_code',
                'u.name as pic_name',
                'unit.code as bundle_unit_code'
            )
            ->first();

        if (!$production) {
            abort(404, 'Packing record not found');
        }

        // Get all packaging products from this transaction
        $transactionNumber = DB::table('t_packaging')
            ->where('production_id', $id)
            ->value('transaction_number');

        $packagingProducts = DB::table('t_packaging as pkg')
            ->join('t_productions as p', 'pkg.production_id', '=', 'p.id')
            ->join('m_products as prod', 'pkg.packaging_product_id', '=', 'prod.id')
            ->leftJoin('m_units as u', 'prod.f_unit_id', '=', 'u.id')
            ->where('pkg.transaction_number', $transactionNumber)
            ->select(
                'pkg.*',
                'p.id as production_id',
                'p.production_number',
                'prod.code as product_code',
                'prod.name as product_name',
                'u.code as unit_code',
                DB::raw("(SELECT CASE WHEN psb.hpp_status = 'SETTLED' THEN psb.final_hpp ELSE psb.estimated_hpp END FROM t_product_stock_batches as psb WHERE psb.f_source_transaction_id = p.id AND psb.source_type = 'PRODUCTION' LIMIT 1) as hpp_packing")
            )
            ->get();

        // Get production items (materials consumed) for each packaging product
        foreach ($packagingProducts as $packaging) {
            $packaging->items = DB::table('t_production_items as pi')
                ->join('m_products as p', 'pi.raw_material_product_id', '=', 'p.id')
                ->where('pi.production_id', $packaging->production_id)
                ->select(
                    'pi.*',
                    'p.code as product_code',
                    'p.name as product_name'
                )
                ->get();
        }

        // Get source bundle product info
        $sourceBundleProductId = $packagingProducts[0]->bundle_product_id ?? null;
        $sourceBundleProduct = DB::table('m_products as p')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.id', $sourceBundleProductId)
            ->select('p.id', 'p.code', 'p.name', 'u.code as unit_code')
            ->first();

        // Find the source production (the one that was packed)
        $sourceProduction = null;
        if ($sourceBundleProductId) {
            $sourceProduction = DB::table('t_productions')
                ->where('bundle_product_id', $sourceBundleProductId)
                ->where('warehouse_id', $production->warehouse_id)
                ->where('company_code', $production->company_code)
                ->where('is_packaging', false)
                ->where('is_packed', true)
                ->orderBy('updated_at', 'DESC')
                ->select('id', 'production_number', 'quantity_produced')
                ->first();
        }

        // Calculate total bundle qty consumed across all packaging products
        $totalBundleConsumed = 0;
        foreach ($packagingProducts as $packaging) {
            $bundleItem = $packaging->items->first(function ($item) use ($sourceBundleProductId) {
                return $item->raw_material_product_id == $sourceBundleProductId;
            });
            if ($bundleItem) {
                $totalBundleConsumed += $bundleItem->quantity_consumed;
            }
        }

        // Get stock adjustment for this specific transaction
        $productionNumbers = $packagingProducts->pluck('production_number')->toArray();
        $stockAdjustment = DB::table('t_stock_movement_logs')
            ->where('reference_type', 'PACKING_ADJUSTMENT')
            ->where('product_id', $sourceBundleProductId)
            ->where('warehouse_id', $production->warehouse_id)
            ->where('company_code', $production->company_code)
            ->where('reference_number', 'LIKE', 'ADJ-PACK-%')
            ->whereBetween('created_at', [
                $production->created_at ? \Carbon\Carbon::parse($production->created_at)->subMinute() : now()->subYear(),
                $production->created_at ? \Carbon\Carbon::parse($production->created_at)->addMinute() : now(),
            ])
            ->select('movement_type', 'movement_qty', 'stock_before', 'stock_after', 'notes', 'reference_number', 'movement_date')
            ->first();

        return view('pages.main_transaction.packing.detail', [
            'production' => $production,
            'packagingProducts' => $packagingProducts,
            'sourceBundleProduct' => $sourceBundleProduct,
            'sourceProduction' => $sourceProduction,
            'totalBundleConsumed' => $totalBundleConsumed,
            'transactionNumber' => $transactionNumber,
            'stockAdjustment' => $stockAdjustment,
        ]);
    }

    public function destroy($id)
    {
        

        // This will be implemented later with PackingService
        return response()->json(['message' => 'Not implemented yet'], 501);
    }

    // AJAX Routes
    public function getBundleComponents(Request $request)
    {
        $bundleProductId = $request->input('bundle_product_id');
        $quantityPlanned = $request->input('quantity_planned');

        // This will be implemented later
        return response()->json([]);
    }

    public function getWarehouses()
    {
        $warehouses = app(WarehouseService::class)->findAll();
        return response()->json($warehouses);
    }

    public function getBundleProducts()
    {
        // Get ALL bundle products (not packaging products)
        // These are the main products that will be packed into smaller packages
        $products = DB::table('m_products')
            ->where('company_code', session('user_company_code'))
            ->where('type', 'bundle')
            ->where('status', 'ACTIVE')
            ->select('id', 'code', 'name', 'description')
            ->get();

        return response()->json($products);
    }

    public function getPackagingProducts(Request $request)
    {
        $bundleProductId = $request->input('bundle_product_id');
        $warehouseId = $request->input('warehouse_id');

        if (!$bundleProductId) {
            return response()->json([
                'success' => false,
                'message' => 'Bundle product ID is required'
            ], 400);
        }

        // Get all packaging products that have the selected bundle as a component
        $packagingProducts = DB::table('m_products as p')
            ->join('m_product_compositions as pc', 'p.id', '=', 'pc.f_product_id')
            ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.company_code', session('user_company_code'))
            ->where('p.is_packaging', true)
            ->where('p.type', 'bundle')
            ->where('p.status', 'ACTIVE')
            ->where('pc.f_product_composition_product_id', $bundleProductId)
            ->select(
                'p.id',
                'p.code',
                'p.name',
                'u.code as unit_code',
                'pc.qty as bundle_qty_needed'
            )
            ->get();

        // For each packaging product, get its other components (excluding the main bundle)
        foreach ($packagingProducts as &$packaging) {
            $otherComponents = DB::table('m_product_compositions as pc')
                ->join('m_products as comp', 'pc.f_product_composition_product_id', '=', 'comp.id')
                ->join('m_units as u', 'comp.f_unit_id', '=', 'u.id')
                ->where('pc.f_product_id', $packaging->id)
                ->where('pc.f_product_composition_product_id', '!=', $bundleProductId)
                ->select(
                    'comp.id as product_id',
                    'comp.code as product_code',
                    'comp.name as product_name',
                    'u.code as unit_code',
                    'pc.qty as qty_per_unit'
                )
                ->get();

            // Get available stock for each component if warehouse is provided using stock movement logs
            if ($warehouseId) {
                foreach ($otherComponents as &$component) {
                    $stock = DB::table('t_stock_movement_logs')
                        ->where('product_id', $component->product_id)
                        ->where('warehouse_id', $warehouseId)
                        ->select(
                            DB::raw('SUM(
                                CASE
                                    WHEN movement_type LIKE "%_IN" THEN movement_qty
                                    WHEN movement_type LIKE "%_OUT" THEN -movement_qty
                                    ELSE 0
                                END
                            ) as total_stock')
                        )
                        ->value('total_stock');

                    $component->available_stock = (float) ($stock ?? 0);
                }
            }

            $packaging->other_components = $otherComponents;
        }

        return response()->json([
            'success' => true,
            'data' => $packagingProducts
        ]);
    }

    public function getBundleStock(Request $request)
    {
        $productionId = $request->input('production_id');

        if (!$productionId) {
            return response()->json([
                'success' => false,
                'message' => 'Production ID is required'
            ], 400);
        }

        // Get the production record
        $production = DB::table('t_productions as p')
            ->join('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->join('m_units as u', 'bp.f_unit_id', '=', 'u.id')
            ->where('p.id', $productionId)
            ->where('p.company_code', session('user_company_code'))
            ->select('p.quantity_produced', 'p.bundle_product_id', 'p.warehouse_id', 'u.code as unit_code')
            ->first();

        if (!$production) {
            return response()->json([
                'success' => true,
                'available_qty' => 0,
                'production_qty' => 0,
                'unit_code' => ''
            ]);
        }

        // Get current total stock from movement logs (latest stock_after per warehouse)
        $latestMovement = DB::table('t_stock_movement_logs')
            ->where('product_id', $production->bundle_product_id)
            ->where('warehouse_id', $production->warehouse_id)
            ->where('company_code', session('user_company_code'))
            ->orderBy('id', 'DESC')
            ->first();

        $currentStock = $latestMovement ? (float) $latestMovement->stock_after : 0;

        return response()->json([
            'success' => true,
            'available_qty' => $currentStock,
            'production_qty' => (float) $production->quantity_produced,
            'unit_code' => $production->unit_code
        ]);
    }

    public function getAvailableProductions(Request $request)
    {
        $bundleProductId = $request->input('bundle_product_id');
        $warehouseId = $request->input('warehouse_id');

        $query = DB::table('t_productions as p')
            ->join('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->join('m_units as u', 'bp.f_unit_id', '=', 'u.id')
            ->leftJoin('m_warehouses as w', 'p.warehouse_id', '=', 'w.id')
            ->where('p.company_code', session('user_company_code'))
            ->where('p.is_packaging', false)
            ->where('p.is_packed', false)
            ->where('p.status', 'COMPLETED')
            ->select(
                'p.id',
                'p.production_number',
                'p.production_date',
                'p.bundle_product_id',
                'bp.code as bundle_product_code',
                'bp.name as bundle_product_name',
                'p.quantity_produced',
                'u.code as unit_code',
                'p.warehouse_id',
                'w.name as warehouse_name'
            )
            ->orderBy('p.production_date', 'DESC');

        if ($bundleProductId) {
            $query->where('p.bundle_product_id', $bundleProductId);
        }

        if ($warehouseId) {
            $query->where('p.warehouse_id', $warehouseId);
        }

        $productions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $productions
        ]);
    }

    public function getPicUsers()
    {
        $users = DB::table('users')
            ->where('company_code', session('user_company_code'))
            ->where('status', 'ACTIVE')
            ->select('id', 'name', 'email')
            ->get();

        return response()->json($users);
    }

    public function generateTransactionNumber()
    {
        $year = date('Y');
        $month = date('m');

        // Get next sequence number for this month from productions with is_packaging = true
        $lastPacking = DB::table('t_productions')
            ->where('company_code', session('user_company_code'))
            ->where('is_packaging', true)
            ->where('production_number', 'LIKE', "PACK-{$year}{$month}-%")
            ->orderBy('production_number', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastPacking) {
            $lastSequence = (int) substr($lastPacking->production_number, -4);
            $sequence = $lastSequence + 1;
        }

        $packingNumber = "PACK-{$year}{$month}-".str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return response()->json(['production_number' => $packingNumber]);
    }

    public function calculateFifoPrices(Request $request)
    {
        try {
            $bundleProductId = $request->input('bundle_product_id');
            $quantityPlanned = $request->input('quantity_planned');

            // Parse JSON string to array
            $productRequirements = json_decode($request->input('product_requirements', '[]'), true);

            $prices = [];

            foreach ($productRequirements as $requirement) {
                $productId = $requirement['product_id'];
                $quantityRequired = $requirement['quantity_required'];

                // Get batches ordered by FIFO (oldest first)
                $batches = DB::table('t_product_stock_batches')
                    ->where('f_product_id', $productId)
                    ->where('status', 'ACTIVE')
                    ->where('remaining_qty', '>', 0)
                    ->orderBy('batch_date', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->get();
                $totalCost = 0;
                $remainingNeed = $quantityRequired;

                foreach ($batches as $batch) {
                    if ($remainingNeed <= 0) {
                        break;
                    }

                    // Take what we can from this batch
                    $qtyToTake = min($batch->remaining_qty, $remainingNeed);

                    // Calculate cost from this batch using estimated_hpp (weighted average HPP)
                    $hppPerUnit = $batch->estimated_hpp ?? $batch->purchase_price ?? 0;
                    $batchCost = $qtyToTake * $hppPerUnit;
                    $totalCost += $batchCost;

                    // Reduce remaining need
                    $remainingNeed -= $qtyToTake;
                }

                // Calculate HPP per piece for this material
                $pricePerPcs = $quantityPlanned > 0 ? ($totalCost / $quantityPlanned) : 0;

                $prices[] = [
                    'product_id' => $productId,
                    'quantity_required' => $quantityRequired,
                    'total_cost' => round($totalCost, 2),
                    'price_per_pcs' => round($pricePerPcs, 2),
                    'batches_used' => $batches->count()
                ];
            }

            return response()->json([
                'success' => true,
                'prices' => $prices,
                'quantity_planned' => $quantityPlanned
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate FIFO prices: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBundlePrice(Request $request)
    {
        // This will be implemented later
        return response()->json([]);
    }

    public function export()
    {
        

        // This will be implemented later
        return response()->json(['message' => 'Not implemented yet'], 501);
    }

    public function processExport(Request $request)
    {
        

        // This will be implemented later
        return response()->json(['message' => 'Not implemented yet'], 501);
    }
}
