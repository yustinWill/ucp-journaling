<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class StockInformationService
{
    /**
     * Get current stock information by warehouse
     */
    public function getStockByWarehouse(?int $warehouseId, array $filters = []): array
    {
        $query = DB::table('m_products as p')
            ->select([
                'p.id as product_id',
                'p.code as product_code',
                'p.name as product_name',
                'p.total_stock',
                'u.code as unit_code',
                'u.name as unit_name',
                'c.name as category_name',
                'p.average_price',
                'p.unit_purchase_price',
            ])
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->leftJoin('m_categories as c', 'p.f_category_id', '=', 'c.id')
            ->where('p.status', 'ACTIVE');

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->where('p.f_category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $search = '%'.$filters['search'].'%';
            $query->where(function ($q) use ($search) {
                $q->where('p.code', 'like', $search)
                    ->orWhere('p.name', 'like', $search);
            });
        }

        if (isset($filters['min_stock'])) {
            $query->where('p.total_stock', '>=', $filters['min_stock']);
        }

        if (isset($filters['max_stock'])) {
            $query->where('p.total_stock', '<=', $filters['max_stock']);
        }

        $products = $query->get();

        // Get warehouse information
        $warehouse = DB::table('m_warehouses')
            ->where('id', $warehouseId)
            ->first();

        // Enhance with batch information
        $stockData = [];
        foreach ($products as $product) {
            $batchInfo = app(BatchManagementService::class)->getProductBatchInfo($product->product_id);

            $stockData[] = [
                'product_id' => $product->product_id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'unit_code' => $product->unit_code,
                'unit_name' => $product->unit_name,
                'category_name' => $product->category_name,
                'total_stock' => $product->total_stock,
                'average_price' => $product->average_price,
                'unit_purchase_price' => $product->unit_purchase_price,
                'batch_info' => [
                    'total_batches' => $batchInfo['total_batches'],
                    'batch_quantity' => $batchInfo['total_quantity'],
                    'batch_value' => $batchInfo['total_value'],
                    'weighted_average_cost' => $batchInfo['weighted_average_cost'],
                ],
                'stock_value' => $product->total_stock * $batchInfo['weighted_average_cost'],
            ];
        }

        return [
            'warehouse' => [
                'id' => $warehouse->id ?? null,
                'code' => $warehouse->code ?? null,
                'name' => $warehouse->name ?? 'All Warehouses',
            ],
            'summary' => [
                'total_products' => count($stockData),
                'total_stock_quantity' => array_sum(array_column($stockData, 'total_stock')),
                'total_stock_value' => array_sum(array_column($stockData, 'stock_value')),
            ],
            'products' => $stockData,
        ];
    }

    /**
     * Get overall stock information (all warehouses combined)
     */
    public function getOverallStock(array $filters = []): array
    {
        return $this->getStockByWarehouse(null, $filters);
    }

    /**
     * Get stock movement history for a product
     */
    public function getStockMovementHistory(int $productId, array $filters = []): array
    {
        $query = DB::table('t_stock_movement_logs as sml')
            ->select([
                'sml.movement_date',
                'sml.movement_type',
                'sml.reference_type',
                'sml.reference_number',
                'sml.stock_before',
                'sml.movement_qty',
                'sml.stock_after',
                'sml.notes',
                'w.code as warehouse_code',
                'w.name as warehouse_name',
                'u.name as created_by_name',
            ])
            ->leftJoin('m_warehouses as w', 'sml.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'sml.created_by', '=', 'u.id')
            ->where('sml.product_id', $productId)
            ->orderBy('sml.movement_date', 'desc')
            ->orderBy('sml.created_at', 'desc');

        // Apply date filters
        if (isset($filters['start_date'])) {
            $query->where('sml.movement_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('sml.movement_date', '<=', $filters['end_date']);
        }

        if (isset($filters['movement_type'])) {
            $query->where('sml.movement_type', $filters['movement_type']);
        }

        if (isset($filters['warehouse_id'])) {
            $query->where('sml.warehouse_id', $filters['warehouse_id']);
        }

        $movements = $query->limit($filters['limit'] ?? 100)->get();

        // Get product information
        $product = DB::table('m_products')
            ->select('id', 'code', 'name', 'total_stock')
            ->where('id', $productId)
            ->first();

        return [
            'product' => $product,
            'movements' => $movements->toArray(),
            'summary' => [
                'total_movements' => count($movements),
                'current_stock' => $product->total_stock ?? 0,
            ],
        ];
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(float $threshold = 10): array
    {
        $products = DB::table('m_products as p')
            ->select([
                'p.id',
                'p.code',
                'p.name',
                'p.total_stock',
                'u.code as unit_code',
                'c.name as category_name',
            ])
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->leftJoin('m_categories as c', 'p.f_category_id', '=', 'c.id')
            ->where('p.status', 'ACTIVE')
            ->where('p.total_stock', '<=', $threshold)
            ->orderBy('p.total_stock', 'asc')
            ->get();

        return [
            'threshold' => $threshold,
            'count' => count($products),
            'products' => $products->toArray(),
        ];
    }

    /**
     * Get stock valuation report
     */
    public function getStockValuation(array $filters = []): array
    {
        $query = DB::table('m_products as p')
            ->select([
                'p.id',
                'p.code',
                'p.name',
                'p.total_stock',
                'p.average_price',
                'c.name as category_name',
                'u.code as unit_code',
            ])
            ->leftJoin('m_categories as c', 'p.f_category_id', '=', 'c.id')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.status', 'ACTIVE')
            ->where('p.total_stock', '>', 0);

        if (isset($filters['category_id'])) {
            $query->where('p.f_category_id', $filters['category_id']);
        }

        $products = $query->get();

        $valuationData = [];
        $totalValue = 0;

        foreach ($products as $product) {
            $batchInfo = app(BatchManagementService::class)->getProductBatchInfo($product->id);

            $stockValue = $product->total_stock * $batchInfo['weighted_average_cost'];
            $totalValue += $stockValue;

            $valuationData[] = [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'category_name' => $product->category_name,
                'unit_code' => $product->unit_code,
                'quantity' => $product->total_stock,
                'average_price' => $product->average_price,
                'batch_weighted_cost' => $batchInfo['weighted_average_cost'],
                'stock_value_average' => $product->total_stock * $product->average_price,
                'stock_value_batch' => $stockValue,
                'batch_count' => $batchInfo['total_batches'],
            ];
        }

        return [
            'summary' => [
                'total_products' => count($valuationData),
                'total_stock_value' => $totalValue,
            ],
            'products' => $valuationData,
        ];
    }
}
