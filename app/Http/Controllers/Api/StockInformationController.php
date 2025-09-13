<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BatchManagementService;
use App\Services\StockInformationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockInformationController extends Controller
{
    protected $stockService;

    protected $batchService;

    public function __construct(StockInformationService $stockService, BatchManagementService $batchService)
    {
        $this->stockService = $stockService;
        $this->batchService = $batchService;
    }

    /**
     * Get current stock by warehouse
     */
    public function stockByWarehouse(Request $request, ?int $warehouseId = null): JsonResponse
    {
        try {
            $filters = $request->only(['category_id', 'search', 'min_stock', 'max_stock']);

            if ($warehouseId) {
                $data = $this->stockService->getStockByWarehouse($warehouseId, $filters);
            } else {
                $data = $this->stockService->getOverallStock($filters);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stock information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get overall stock (all warehouses)
     */
    public function overallStock(Request $request): JsonResponse
    {
        return $this->stockByWarehouse($request);
    }

    /**
     * Get product batch information
     */
    public function productBatchInfo(int $productId): JsonResponse
    {
        try {
            $includeEmpty = request()->boolean('include_empty', false);
            $batchInfo = $this->batchService->getProductBatchInfo($productId, $includeEmpty);

            return response()->json([
                'success' => true,
                'data' => $batchInfo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve batch information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate COGS for a product quantity
     */
    public function calculateCOGS(Request $request, int $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            $quantity = $request->get('quantity');
            $cogsData = $this->batchService->calculateCOGS($productId, $quantity);

            return response()->json([
                'success' => true,
                'data' => $cogsData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate COGS',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock movement history
     */
    public function movementHistory(Request $request, int $productId): JsonResponse
    {
        try {
            $filters = $request->only(['start_date', 'end_date', 'movement_type', 'warehouse_id', 'limit']);
            $history = $this->stockService->getStockMovementHistory($productId, $filters);

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve movement history',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get low stock products
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 10);
            $lowStock = $this->stockService->getLowStockProducts($threshold);

            return response()->json([
                'success' => true,
                'data' => $lowStock,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve low stock products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock valuation report
     */
    public function stockValuation(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['category_id']);
            $valuation = $this->stockService->getStockValuation($filters);

            return response()->json([
                'success' => true,
                'data' => $valuation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stock valuation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get stock dashboard data
     */
    public function dashboard(): JsonResponse
    {
        try {
            // Get overall stock summary
            $overallStock = $this->stockService->getOverallStock();

            // Get low stock products
            $lowStock = $this->stockService->getLowStockProducts(10);

            // Get stock valuation
            $valuation = $this->stockService->getStockValuation();

            $dashboard = [
                'stock_summary' => $overallStock['summary'],
                'low_stock_alert' => [
                    'count' => $lowStock['count'],
                    'threshold' => 10,
                    'products' => array_slice($lowStock['products'], 0, 5), // Top 5 lowest
                ],
                'valuation_summary' => $valuation['summary'],
                'recent_movements' => [], // Could add recent movements here
            ];

            return response()->json([
                'success' => true,
                'data' => $dashboard,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
