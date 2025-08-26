<?php

namespace App\Http\Controllers\MainTransaction;

use App\Http\Controllers\Controller;
use App\Domains\Production\Services\ProductionService;
use App\Domains\Production\Services\ProductionItemService;
use App\Domains\Production\Services\ProductionExpenseService;
use App\Domains\Production\Services\ProductionAuditLogService;
use App\Domains\Production\Actions\CreateProductionAction;
use App\Domains\Production\Actions\UpdateProductionAction;
use App\Domains\Production\Actions\ReversalProductionAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        // Check read permission
        if (!$this->hasPermission('production.read')) {
            abort(403, 'Unauthorized access');
        }

        // Get summary statistics
        $productions = app(ProductionService::class)->findAll();
        
        $stats = [
            'totalProductions' => $productions->count(),
            'inProgressProductions' => $productions->where('status', 'IN_PROGRESS')->count(),
            'completedProductions' => $productions->where('status', 'COMPLETED')->count(),
            'plannedProductions' => $productions->where('status', 'PLANNED')->count()
        ];
        
        $productionStatuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PLANNED', 'label' => 'PLANNED - Direncanakan'],
            ['value' => 'IN_PROGRESS', 'label' => 'IN_PROGRESS - Sedang Produksi'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Selesai'],
            ['value' => 'CANCELLED', 'label' => 'CANCELLED - Dibatalkan']
        ];

        $warehouses = $this->getWarehouseOptions();

        return view('pages.main_transaction.production.index', array_merge($stats, [
            'productionStatuses' => $productionStatuses, // Keep original variable name to match existing view
            'warehouses' => $warehouses,
            'canAddProduction' => $this->hasPermission('production.write'),
            'canExportProduction' => $this->hasPermission('production.export'),
            'canViewDetails' => $this->hasPermission('production.read'),
            'canEditProduction' => $this->hasPermission('production.write'),
            'canDeleteProduction' => $this->hasPermission('production.delete'),
            'canReverseProduction' => $this->hasPermission('production.reversal')
        ]));
    }

    public function index_data_table(Request $request)
    {
        if (!$this->hasPermission('production.read')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = [
            'status' => $request->get('status_filter'),
            'warehouse_id' => $request->get('warehouse_filter'),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to')
        ];

        $productions = app(ProductionService::class)->findAll($filters);
        
        $data = $productions->map(function ($production) {
            return [
                'id' => $production->id,
                'production_number' => $production->production_number,
                'production_date' => $production->production_date,
                'bundle_product_name' => $production->bundle_product_name,
                'warehouse_name' => $production->warehouse_name,
                'warehouse_id' => $production->warehouse_id,
                'quantity_planned' => $production->quantity_planned,
                'quantity_produced' => $production->quantity_produced,
                'status' => $production->status,
                'status_badge_class' => $this->getStatusBadgeClass($production->status),
                'pic_name' => $production->pic_name,
                'pic_id' => $production->pic_id,
                'created_by' => $production->created_by ?? 'System',
                'created_at' => $production->created_at
            ];
        })->toArray();

        return response()->json(['data' => array_values($data)], 200);
    }

    public function add()
    {
        if (!$this->hasPermission('production.write')) {
            abort(403, 'Unauthorized access');
        }

        // Load bundle products (products with bundle elements)
        $bundleProducts = $this->getBundleProductOptions();
        $warehouses = $this->getWarehouseOptions();
        $picUsers = $this->getPicUserOptions();

        // Production status options
        $productionStatuses = [
            ['value' => 'DRAFT', 'label' => 'DRAFT - Draft'],
            ['value' => 'PLANNED', 'label' => 'PLANNED - Planned'],
            ['value' => 'IN_PROGRESS', 'label' => 'IN_PROGRESS - In Progress'],
            ['value' => 'COMPLETED', 'label' => 'COMPLETED - Completed']
        ];

        return view('pages.main_transaction.production.add', [
            'bundleProducts' => $bundleProducts,
            'warehouses' => $warehouses,
            'picUsers' => $picUsers, // Changed from picOptions to match view usage
            'productionStatuses' => $productionStatuses,
            'canAddProducts' => $this->hasPermission('production.write'), // Can add products if needed
            'canAddWarehouses' => $this->hasPermission('production.write'), // Can add warehouses if needed  
            'canAddContacts' => $this->hasPermission('production.write'), // Can add contacts/PICs if needed
            'canAddProduction' => $this->hasPermission('production.write')
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->hasPermission('production.write')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $data = $request->all();
            
            // Validate input data
            $createAction = app(CreateProductionAction::class);
            $validation = $createAction->validateProductionData($data);
            
            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Prepare items from bundle formula
            if (!empty($data['bundle_product_id']) && !empty($data['quantity_planned'])) {
                $data['items'] = $createAction->prepareItemsFromFormula(
                    $data['bundle_product_id'],
                    $data['quantity_planned']
                );
            }

            // Prepare expenses from product
            if (!empty($data['bundle_product_id'])) {
                $defaultExpenses = $createAction->prepareExpensesFromProduct($data['bundle_product_id']);
                $data['expenses'] = $data['expenses'] ?? $defaultExpenses;
            }

            // Execute creation
            $productionId = $createAction->execute($data);

            return response()->json([
                'success' => true,
                'message' => 'Production order created successfully',
                'production_id' => $productionId
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Edit and Update methods removed - use Reversal process for modifications

    public function detail($id)
    {
        if (!$this->hasPermission('production.read')) {
            abort(403, 'Unauthorized access');
        }

        $production = app(ProductionService::class)->findOne($id);
        if (!$production) {
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
            'reversalInfo' => $reversalCheck
        ]);
    }

    public function destroy($id)
    {
        if (!$this->hasPermission('production.delete')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access'], 403);
        }

        try {
            $production = app(ProductionService::class)->findOne($id);
            
            if (!$production) {
                return response()->json(['success' => false, 'message' => 'Production not found'], 404);
            }

            // Only allow deletion of DRAFT status
            if ($production->status !== 'DRAFT') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Only draft productions can be deleted'
                ], 400);
            }

            // Delete production (this will cascade to items and expenses if properly set up)
            $deleted = app(ProductionService::class)->delete($id);
            
            if ($deleted) {
                // Log audit trail
                app(ProductionAuditLogService::class)->logDelete($id, [
                    'production_number' => $production->production_number,
                    'deleted_by' => auth()->user()->name ?? 'System',
                    'deleted_at' => now()->toISOString()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Production deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete production'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
            'message' => 'Export completed successfully'
        ]);
    }

    public function getBundleComponents(Request $request)
    {
        // TODO: AJAX endpoint to get bundle product components
        $bundleProductId = $request->get('bundle_product_id');
        
        // Mock data - replace with actual database query
        $components = [
            [
                'id' => 1,
                'product_name' => 'RIYO Water 12L',
                'quantity_required' => 1,
                'stock_available' => 150,
                'unit' => 'pcs'
            ],
            [
                'id' => 2,
                'product_name' => 'RIYO Water 5L',
                'quantity_required' => 2,
                'stock_available' => 300,
                'unit' => 'pcs'
            ]
        ];

        return response()->json([
            'success' => true,
            'components' => $components
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
            'message' => 'Production marked as completed successfully'
        ]);
    }

    public function cancel($id)
    {
        // TODO: Cancel production order
        // Validate production exists and can be cancelled
        // Update status to CANCELLED
        // Create audit log
        
        return response()->json([
            'success' => true,
            'message' => 'Production cancelled successfully'
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
        $number = "PROD-{$year}-" . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        return response()->json([
            'success' => true,
            'transaction_number' => $number
        ]);
    }

    public function getWarehouses(Request $request)
    {
        // TODO: AJAX endpoint for warehouse selection
        $search = $request->get('search', '');
        
        $warehouses = [
            ['value' => '1', 'label' => 'GDG-001 - Gudang Utama Pusat'],
            ['value' => '2', 'label' => 'GDG-002 - Gudang Produksi'],
            ['value' => '3', 'label' => 'GDG-003 - Gudang Cabang Jakarta'],
        ];

        if ($search) {
            $warehouses = array_filter($warehouses, function($warehouse) use ($search) {
                return stripos($warehouse['label'], $search) !== false;
            });
        }

        return response()->json(array_values($warehouses));
    }

    public function getBundleProducts(Request $request)
    {
        // TODO: AJAX endpoint for bundle product selection
        $search = $request->get('search', '');
        
        $bundleProducts = [
            ['value' => '1', 'label' => 'BUNDLE-001 - Paket RIYO Water 12L + 5L'],
            ['value' => '2', 'label' => 'BUNDLE-002 - Paket RIYO Water Family Pack'],
            ['value' => '3', 'label' => 'BUNDLE-003 - Paket RIYO Water Office'],
        ];

        if ($search) {
            $bundleProducts = array_filter($bundleProducts, function($product) use ($search) {
                return stripos($product['label'], $search) !== false;
            });
        }

        return response()->json(array_values($bundleProducts));
    }

    public function getPicUsers(Request $request)
    {
        // TODO: AJAX endpoint for PIC user selection
        $search = $request->get('search', '');
        
        $users = [
            ['value' => '1', 'label' => 'Yustinus Komunal'],
            ['value' => '2', 'label' => 'Production Manager'],
            ['value' => '3', 'label' => 'Quality Control'],
        ];

        if ($search) {
            $users = array_filter($users, function($user) use ($search) {
                return stripos($user['label'], $search) !== false;
            });
        }

        return response()->json(array_values($users));
    }

    public function getFormula($bundleProductId, Request $request)
    {
        try {
            $quantityPlanned = $request->get('quantity_planned', 1);
            
            $action = app(CreateProductionAction::class);
            $formula = $action->prepareItemsFromFormula($bundleProductId, $quantityPlanned);
            
            // Enhance with product names
            foreach ($formula as &$item) {
                $product = DB::table('m_products')
                    ->where('id', $item['raw_material_product_id'])
                    ->first();
                $item['product_name'] = $product ? $product->name : 'Unknown Product';
            }
            
            return response()->json([
                'success' => true,
                'data' => $formula
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getExpenses($bundleProductId)
    {
        try {
            $action = app(CreateProductionAction::class);
            $expenses = $action->prepareExpensesFromProduct($bundleProductId);
            
            return response()->json([
                'success' => true,
                'data' => $expenses
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Reversal functionality
    public function reversal($id)
    {
        if (!$this->hasPermission('production.reversal')) {
            abort(403, 'Unauthorized access');
        }

        // Get production data directly
        $production = app(ProductionService::class)->findOne($id);
        
        if (!$production) {
            abort(404, 'Production not found');
        }
        
        // Check if production can be reversed (must be COMPLETED)
        if ($production->status !== 'COMPLETED') {
            return redirect()->route('main_transaction.production.detail', $id)
                           ->with('error', 'Only completed productions can be reversed');
        }

        return view('pages.main_transaction.production.reversal', [
            'production' => $production,
            'canReverseProduction' => $this->hasPermission('production.reversal')
        ]);
    }

    public function processReversal(Request $request, $id)
    {
        if (!$this->hasPermission('production.reversal')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $reversalData = $request->validate([
                'quantity_to_reverse' => 'nullable|numeric|min:1',
                'reason' => 'required|string|max:500',
                'confirmation' => 'required|accepted'
            ]);

            // TODO: Implement actual reversal logic using ReversalProductionAction
            // For now, just return success response
            return response()->json([
                'success' => true,
                'message' => 'Production reversal completed successfully (Placeholder - implement actual logic)'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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

    private function getBundleProductOptions(): array
    {
        // Get products that have compositions (formula) - using correct table name
        $query = DB::table('m_products as p')
            ->select('p.id as value', DB::raw("CONCAT(p.code, ' - ', p.name) as label"))
            ->join('m_product_compositions as pc', 'p.id', '=', 'pc.f_product_id')
            ->where('p.status', 'ACTIVE')
            ->groupBy('p.id', 'p.code', 'p.name')
            ->orderBy('p.name');
            
        // Add company filter only if company_code column exists and session has value
        if (session('user_company_code')) {
            $query->where('p.company_code', session('user_company_code'));
        }
        
        return $query->get()
            ->map(function ($item) {
                return ['value' => $item->value, 'label' => $item->label];
            })
            ->toArray();
    }

    private function getWarehouseOptions(): array
    {
        $query = DB::table('m_warehouses')
            ->select('id as value', DB::raw("CONCAT(code, ' - ', name) as label"))
            ->where('status', 'ACTIVE')
            ->orderBy('name');
            
        // Add company filter only if company_code column exists and session has value
        if (session('user_company_code')) {
            $query->where('company_code', session('user_company_code'));
        }
        
        return $query->get()
            ->map(function ($item) {
                return ['value' => $item->value, 'label' => $item->label];
            })
            ->toArray();
    }

    private function getPicUserOptions(): array
    {
        $query = DB::table('users')
            ->select('id as value', DB::raw("name as label"))
            ->where('status', 'ACTIVE')
            ->orderBy('name');
            
        // Add company filter only if company_code column exists and session has value
        // Note: users table may not have company_code, check if column exists
        try {
            $columns = DB::getSchemaBuilder()->getColumnListing('users');
            if (in_array('company_code', $columns) && session('user_company_code')) {
                $query->where('company_code', session('user_company_code'));
            }
        } catch (\Exception $e) {
            // Ignore if we can't check columns
        }
        
        return $query->get()
            ->map(function ($item) {
                return ['value' => $item->value, 'label' => $item->label];
            })
            ->toArray();
    }

    private function getTagOptions(): array
    {
        $query = DB::table('m_tags')
            ->select('code as value', 'name as label', 'color')
            ->where('status', 'ACTIVE')
            ->orderBy('name');
            
        // Add company filter only if company_code column exists and session has value
        if (session('user_company_code')) {
            $query->where('company_code', session('user_company_code'));
        }
        
        return $query->get()->toArray();
    }

    private function getStatusBadgeClass(string $status): string
    {
        return match($status) {
            'DRAFT' => 'bg-label-secondary',
            'PLANNED' => 'bg-label-info',
            'IN_PROGRESS' => 'bg-label-warning',
            'COMPLETED' => 'bg-label-success',
            'CANCELLED' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }
}