<?php

namespace App\Http\Controllers\MainTransaction;

use App\Domains\Product\Services\ProductCompositionService;
use App\Domains\Product\Services\ProductService;
use App\Domains\Production\Actions\CreateProductionAction;
use App\Domains\Production\Actions\ReversalProductionAction;
use App\Domains\Production\Services\ProductionAuditLogService;
use App\Domains\Production\Services\ProductionExpenseService;
use App\Domains\Product\Services\ProductCostCompositionService;
use App\Domains\Production\Services\ProductionItemService;
use App\Domains\Production\Services\ProductionService;
use App\Domains\User\Services\UserService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ErrorCodes;
use App\Enums\SuccessCodes;
use App\Exceptions\AppException;
use App\Helpers\DiscordNotifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        // Check read permission
        if (! $this->hasPermission('production.read')) {
            abort(403, 'Unauthorized access');
        }

        // Get summary statistics
        $productions = app(ProductionService::class)->findAll();

        $stats = [
            'totalProductions' => $productions->count(),
            'inProgressProductions' => $productions->where('status', 'IN_PROGRESS')->count(),
            'completedProductions' => $productions->where('status', 'COMPLETED')->count(),
            'plannedProductions' => $productions->where('status', 'PLANNED')->count(),
        ];

        $bundleProducts = app(ProductService::class)->findAllBundle();
        $warehouses = app(WarehouseService::class)->findAll();

        return view('pages.main_transaction.production.index', array_merge($stats, [
            'bundleProducts' => $bundleProducts,
            'warehouses' => $warehouses,
            'canAddProduction' => $this->hasPermission('production.write'),
            'canExportProduction' => $this->hasPermission('production.export'),
            'canViewDetails' => $this->hasPermission('production.read'),
            'canEditProduction' => $this->hasPermission('production.write'),
            'canDeleteProduction' => $this->hasPermission('production.delete'),
            'canReverseProduction' => $this->hasPermission('production.reversal'),
        ]));
    }

    public function index_data_table(Request $request)
    {
        $data = app(ProductionService::class)->findAllDatatable();

        return response()->json($data, SuccessCodes::GENERAL_SUCCESS->httpStatus());
    }

    /**
     * Calculate material costs using FIFO method with HPP from batches
     *
     * For Bundle Product HPP Formula:
     * HPP = (biaya produksi + sum(hpp material × qty yang dibutuhkan)) / qty produksi
     *
     * This method calculates: sum(hpp material × qty yang dibutuhkan)
     * Where hpp material comes from batch estimated_hpp (weighted average)
     */
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
                    // This ensures we use the correct HPP for material costs in bundle production
                    $hppPerUnit = $batch->estimated_hpp ?? $batch->purchase_price ?? 0;
                    $batchCost = $qtyToTake * $hppPerUnit;
                    $totalCost += $batchCost;

                    // Reduce remaining need
                    $remainingNeed -= $qtyToTake;
                }
                
                // Calculate HPP per piece for this material
                // This represents the cost per unit of bundle product for this specific material
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
        try {
            $bundleProductId = $request->input('bundle_product_id');
            $productionItems = app(ProductCostCompositionService::class)->getSumBasedOnProductId($bundleProductId);
            
            return response()->json([
                'success' => true,
                'bundle_price' => $productionItems
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get bundle cost: ' . $e->getMessage()
            ], 500);
        }
    }

    public function add()
    {
        if (! $this->hasPermission('production.write')) {
            abort(403, 'Unauthorized access');
        }

        // Load bundle products (products with bundle elements)
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

        return view('pages.main_transaction.production.add', [
            'bundleProducts' => $bundleProducts,
            'warehouses' => $warehouses,
            'picUsers' => $picUsers, // Changed from picOptions to match view usage
            'productionStatuses' => $productionStatuses,
            'canAddProducts' => $this->hasPermission('production.write'), // Can add products if needed
            'canAddWarehouses' => $this->hasPermission('production.write'), // Can add warehouses if needed
            'canAddContacts' => $this->hasPermission('production.write'), // Can add contacts/PICs if needed
            'canAddProduction' => $this->hasPermission('production.write'),
        ]);
    }

    public function store(Request $request)
    {
        if (! $this->hasPermission('production.write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $data = $request->all();

            // Validate input data
            $createAction = app(CreateProductionAction::class);
            $validation = $createAction->validateProductionData($data);
            if (! $validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors'],
                ], 422);
            }

            // Handle production mode
            $productionMode = $data['production_mode'] ?? 'QUANTITY_PLANNED';

            // Prepare items based on production mode
            if (! empty($data['bundle_product_id']) && ! empty($data['quantity_planned'])) {
                if ($productionMode === 'MATERIAL_BASED') {
                    // Material-Based mode: Use actual material inputs
                    $materialInputs = json_decode($data['material_inputs'] ?? '[]', true);
                    $data['items'] = $createAction->prepareItemsFromMaterialInputs(
                        $data['bundle_product_id'],
                        $data['quantity_planned'],
                        $materialInputs
                    );
                    // Store leftover materials for stock opname tracking
                    $data['leftover_materials'] = json_encode($materialInputs);
                } else {
                    // Quantity Planned mode: Calculate from formula
                    $data['items'] = $createAction->prepareItemsFromFormula(
                        $data['bundle_product_id'],
                        $data['quantity_planned']
                    );
                }
            }

            // Store production mode in data
            $data['production_mode_used'] = $productionMode;

            // Prepare expenses from product
            if (! empty($data['bundle_product_id'])) {
                // $defaultExpenses = $createAction->prepareExpensesFromProduct($data['bundle_product_id']);
                // $data['expenses'] = $data['expenses'] ?? $defaultExpenses;
            }

            // Execute creation
            $productionId = $createAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Production order created successfully',
                'production_id' => $productionId,
                'production_mode' => $productionMode,
                'quantity_produced' => $data['quantity_planned'],
            ]);
        } catch (AppException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getCodeValue(),
            ], $e->getCode());
        } catch (\Exception $e) {
            DiscordNotifier::reportException($e);

            return response()->json([
                'message' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->message(),
                'error' => ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->code(),
                'trace' => $e->getMessage(),
            ], ErrorCodes::GENERAL_INTERNAL_SERVER_ERROR->httpStatus());
        }
    }

    // Edit and Update methods removed - use Reversal process for modifications

    public function detail($id)
    {
        if (! $this->hasPermission('production.read')) {
            abort(403, 'Unauthorized access');
        }

        $production = app(ProductionService::class)->findOne($id);
        if (! $production) {
            abort(404, 'Production not found');
        }

        // Load production items (raw materials)
        $productionItems = app(ProductionItemService::class)->findByProductionId($id);

        // Load production expenses
        $productionExpenses = app(ProductionExpenseService::class)->findByProductionId($id);

        // Load audit logs
        $auditLogs = app(ProductionAuditLogService::class)->findByProductionId($id);

        // Check if production can be reversed
        $reversalCheck = app(ReversalProductionAction::class)->validateReversal($id);

        return view('pages.main_transaction.production.detail', [
            'production' => $production,
            'production_items' => $productionItems, // Fixed variable name
            'production_expenses' => $productionExpenses, // Fixed variable name
            'audit_logs' => $auditLogs, // Fixed variable name
            'canEdit' => $this->hasPermission('production.write') && in_array($production->status, ['DRAFT', 'PLANNED']),
            'canDelete' => $this->hasPermission('production.delete') && $production->status === 'DRAFT',
            'canReversal' => $this->hasPermission('production.reversal') && $reversalCheck['can_reverse'],
            'reversalInfo' => $reversalCheck,
        ]);
    }

    public function export()
    {
        // TODO: Implement export functionality
        return view('pages.main_transaction.production.export');
    }

    public function processExport(Request $request)
    {
        // TODO: Process export with filters
        return response()->json([
            'success' => true,
            'message' => 'Export completed successfully',
        ]);
    }

    public function getBundleComponents(Request $request)
    {
        $bundleProductId = $request->get('bundle_product_id');
        $warehouseId = $request->get('warehouse_id');

        $data = app(ProductCompositionService::class)->findAllBasedOnProductId($bundleProductId,$warehouseId);
        
        $components = [];
        foreach ($data as $row) {
            $components[] = [
                'id' => $row->f_product_composition_product_id,
                'product_name' => $row->product_name,
                'quantity_required' => $row->qty,
                'stock_available' => $row->total_stock ?? 0,
                'unit' => $row->unit_name,
                'purchase_price' => $row->purchase_price,
            ];
        }

        return response()->json([
            'success' => true,
            'components' => $components,
        ]);
    }

    public function complete($id)
    {
        // TODO: Mark production as completed
        // Validate production exists and can be completed
        // Update status and quantity_produced
        // Create audit log

        return response()->json([
            'success' => true,
            'message' => 'Production marked as completed successfully',
        ]);
    }

    public function generateTransactionNumber()
    {
        // TODO: Generate unique production number
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $timestamp = time();

        // Mock generation - replace with proper sequence
        $number = "PROD-{$year}-".str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'transaction_number' => $number,
        ]);
    }

    // public function getFormula($bundleProductId, Request $request)
    // {
    //     try {
    //         $quantityPlanned = $request->get('quantity_planned', 1);

    //         $action = app(CreateProductionAction::class);
    //         $formula = $action->prepareItemsFromFormula($bundleProductId, $quantityPlanned);

    //         // Enhance with product names
    //         foreach ($formula as &$item) {
    //             $product = DB::table('m_products')
    //                 ->where('id', $item['raw_material_product_id'])
    //                 ->first();
    //             $item['product_name'] = $product ? $product->name : 'Unknown Product';
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'data' => $formula
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function getExpenses($bundleProductId)
    // {
    //     try {
    //         $action = app(CreateProductionAction::class);
    //         $expenses = $action->prepareExpensesFromProduct($bundleProductId);

    //         return response()->json([
    //             'success' => true,
    //             'data' => $expenses
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // Reversal functionality
    public function reversal($id)
    {
        if (! $this->hasPermission('production.reversal')) {
            abort(403, 'Unauthorized access');
        }

        // Get production data directly
        $production = app(ProductionService::class)->findOne($id);

        if (! $production) {
            abort(404, 'Production not found');
        }

        // Check if production can be reversed (must be COMPLETED)
        if ($production->status !== 'COMPLETED') {
            return redirect()->route('main_transaction.production.detail', $id)
                ->with('error', 'Only completed productions can be reversed');
        }

        return view('pages.main_transaction.production.reversal', [
            'production' => $production,
            'canReverseProduction' => $this->hasPermission('production.reversal'),
        ]);
    }

    public function processReversal(Request $request, $id)
    {
        if (! $this->hasPermission('production.reversal')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $reversalData = $request->validate([
                'quantity_to_reverse' => 'nullable|numeric',
                'reason' => 'required|string|max:500',
                'confirmation' => 'required|accepted',
            ]);

            // TODO: Implement actual reversal logic using ReversalProductionAction
            // For now, just return success response
            return response()->json([
                'success' => true,
                'message' => 'Production reversal completed successfully (Placeholder - implement actual logic)',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Helper methods
    private function hasPermission(string $permission): bool
    {
        // TODO: Implement actual permission checking
        // This should check user's role permissions
        return true; // Temporary - replace with actual permission check
    }

    private function getStatusBadgeClass(string $status): string
    {
        return match ($status) {
            'DRAFT' => 'bg-label-secondary',
            'PLANNED' => 'bg-label-info',
            'IN_PROGRESS' => 'bg-label-warning',
            'COMPLETED' => 'bg-label-success',
            'CANCELLED' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }
}
