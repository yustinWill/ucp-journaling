<?php

namespace App\Domains\StockMovement\Services;

use Illuminate\Support\Facades\DB;

class StockMovementLogsService
{
    private $table_name = 't_stock_movement_logs';

    public function findAllLatestStockPerWarehouse($warehouseId, $categoryId = null, $global_search = null)
    {
        // $query = DB::table($this->table_name.' AS t_stock_movement_logs')
        //     ->select(
        //         'm_products.id AS id',
        //         'm_products.code AS code',
        //         'm_products.name AS name',
        //         'm_units.name AS unit',
        //         'm_products.f_category_id AS category',
        //         'm_products.minimum_stock AS minimum_stock',
        //         'm_products.unit_purchase_price AS unit_purchase_price',
        //         'm_products.unit_sell_price AS unit_sell_price',
        //         DB::raw('SUM(
        //             CASE 
        //                 WHEN t_stock_movement_logs.movement_type = "PURCHASE_IN" THEN t_stock_movement_logs.movement_qty
        //                 WHEN t_stock_movement_logs.movement_type = "SALES_OUT" THEN -t_stock_movement_logs.movement_qty

        //                 WHEN t_stock_movement_logs.movement_type = "ADJUSTMENT_IN" THEN t_stock_movement_logs.movement_qty
        //                 WHEN t_stock_movement_logs.movement_type = "ADJUSTMENT_OUT" THEN -t_stock_movement_logs.movement_qty

        //                 WHEN t_stock_movement_logs.movement_type = "PRODUCTION_IN" THEN t_stock_movement_logs.movement_qty
        //                 WHEN t_stock_movement_logs.movement_type = "PRODUCTION_OUT" THEN -t_stock_movement_logs.movement_qty
        //                 ELSE 0
        //             END
        //         ) as system_stock'),
        //         DB::raw('lp.price_per_qty as latest_purchase_price')
        //     )
        //     ->join('m_products', 't_stock_movement_logs.product_id', '=', 'm_products.id')
        //     ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
        //     ->leftJoinSub($latestPurchaseSub, 'lp', function ($join) {
        //         $join->on('lp.product_id', '=', 'm_products.id');
        //     })
        //     ->where('t_stock_movement_logs.warehouse_id', $warehouseId)
        //     ->where('t_stock_movement_logs.company_code', session('user_company_code'))
        //     ->groupBy('m_products.id', 'm_products.code', 'm_products.name');
        $companyCode = session('user_company_code');

        // Subquery to get the latest stock_after for each product in the warehouse
        $latestStockSubquery = DB::table('t_stock_movement_logs as sml')
            ->select(
                'sml.product_id',
                'sml.stock_after'
            )
            ->whereRaw('sml.id = (
                SELECT MAX(sml2.id)
                FROM t_stock_movement_logs sml2
                WHERE sml2.product_id = sml.product_id
                  AND sml2.warehouse_id = ?
                  AND sml2.company_code = ?
            )', [$warehouseId, $companyCode])
            ->where('sml.warehouse_id', $warehouseId)
            ->where('sml.company_code', $companyCode);

        // Main query
        $query = DB::table('m_products')
            ->select(
                'm_products.id AS id',
                'm_products.code AS code',
                'm_products.name AS name',
                'm_units.name AS unit',
                'm_products.f_category_id AS category',
                'categories.name AS category_name',
                'm_products.minimum_stock AS minimum_stock',
                'm_products.unit_purchase_price AS unit_purchase_price',
                'm_products.unit_sell_price AS unit_sell_price',
                DB::raw('COALESCE(latest_stock.stock_after, 0) as system_stock'),
                DB::raw('IFNULL((
                    SELECT pop.price_per_qty
                    FROM t_purchase_order_products pop
                    WHERE pop.f_product_id = m_products.id
                    ORDER BY pop.created_at DESC
                    LIMIT 1
                ), 0) as latest_purchase_price')
            )
            ->join('m_categories as categories', 'categories.id', '=', 'm_products.f_category_id')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->leftJoinSub($latestStockSubquery, 'latest_stock', function ($join) {
                $join->on('latest_stock.product_id', '=', 'm_products.id');
            })
            ->where('m_products.company_code', $companyCode)
            ->whereNotNull('latest_stock.stock_after'); // Only show products that have movement logs

        if ($categoryId) {
            $query->where('m_products.f_category_id', $categoryId);
        }

        if ($global_search) {
            $query->where(function ($subQuery) use ($global_search) {
                $subQuery->where('m_products.code', 'LIKE', '%'.$global_search.'%')
                    ->orWhere('m_products.name', 'LIKE', '%'.$global_search.'%');
            });
        }

        return $query->get();
    }

    public function findAllLatestStockPerProduct($productId)
    {
        $companyCode = session('user_company_code');

        // Subquery to get the latest stock_after for this product per warehouse
        $latestStockSubquery = DB::table('t_stock_movement_logs as sml')
            ->select(
                'sml.warehouse_id',
                'sml.stock_after'
            )
            ->whereRaw('sml.id = (
                SELECT MAX(sml2.id)
                FROM t_stock_movement_logs sml2
                WHERE sml2.product_id = sml.product_id
                  AND sml2.warehouse_id = sml.warehouse_id
                  AND sml2.company_code = ?
            )', [$companyCode])
            ->where('sml.product_id', $productId)
            ->where('sml.company_code', $companyCode);

        $query = DB::table('m_products')
            ->select(
                'm_products.id AS id',
                'm_products.code AS code',
                'm_products.name AS name',
                'm_units.name AS unit',
                'latest_stock.warehouse_id AS warehouse_id',
                'm_warehouses.code AS warehouse_code',
                'm_warehouses.name AS warehouse_name',
                'm_products.f_category_id AS category',
                DB::raw('COALESCE(latest_stock.stock_after, 0) as system_stock')
            )
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->joinSub($latestStockSubquery, 'latest_stock', function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1'));
            })
            ->join('m_warehouses', 'latest_stock.warehouse_id', '=', 'm_warehouses.id')
            ->where('m_products.id', $productId)
            ->where('m_products.company_code', $companyCode);

        return $query->get();
    }

    public function findAllLatestStockPerProductAndWarehouse($productId, $warehouseId)
    {
        $query = DB::table($this->table_name.' AS t_stock_movement_logs')
            ->select(
                'm_products.id AS id',
                'm_products.code AS code',
                'm_products.name AS name',
                'm_units.name AS unit',
                DB::raw('SUM(
                    CASE 
                        WHEN t_stock_movement_logs.movement_type LIKE "%_IN" THEN t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type LIKE "%_OUT" THEN -t_stock_movement_logs.movement_qty
                        ELSE 0
                    END
                ) as system_stock')
            )
            ->join('m_products', 't_stock_movement_logs.product_id', '=', 'm_products.id')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->join('m_warehouses', 't_stock_movement_logs.warehouse_id', '=', 'm_warehouses.id')
            ->where('t_stock_movement_logs.product_id', $productId)
            ->where('t_stock_movement_logs.warehouse_id', $warehouseId)
            ->where('t_stock_movement_logs.company_code', session('user_company_code'))
            ->groupBy('m_products.id');

        return $query->first();
    }

    public function findAllLogBasedOnWarehouseId($warehouseId)
    {
        $companyCode = session('user_company_code');

        return DB::table('t_stock_movement_logs as sml')
            ->select([
                'sml.id',
                'sml.movement_date',
                'sml.product_id',
                'sml.product_code',
                'sml.product_name',
                'sml.movement_qty as qty',
                'sml.movement_type',
                'sml.reference_type',
                'sml.reference_number',
                'sml.stock_before',
                'sml.stock_after',
                'sml.warehouse_id',
                'sml.warehouse_code',
                'sml.warehouse_name',
                'sml.unit',
                'sml.unit_cost',
                'sml.notes',
                // Get paired warehouse info for transfers (warehouse_from for IN movements)
                DB::raw("(
                    SELECT paired.warehouse_name
                    FROM t_stock_movement_logs paired
                    WHERE paired.reference_number = sml.reference_number
                    AND paired.product_id = sml.product_id
                    AND paired.company_code = sml.company_code
                    AND paired.id <> sml.id
                    AND paired.movement_type LIKE '%_OUT'
                    LIMIT 1
                ) AS warehouse_from"),
                // Get paired warehouse info for transfers (warehouse_to for OUT movements)
                DB::raw("(
                    SELECT paired.warehouse_name
                    FROM t_stock_movement_logs paired
                    WHERE paired.reference_number = sml.reference_number
                    AND paired.product_id = sml.product_id
                    AND paired.company_code = sml.company_code
                    AND paired.id <> sml.id
                    AND paired.movement_type LIKE '%_IN'
                    LIMIT 1
                ) AS warehouse_to"),
                // Direction indicator based on stock change
                DB::raw("
                    CASE
                        WHEN sml.stock_after > sml.stock_before THEN 'IN'
                        WHEN sml.stock_after < sml.stock_before THEN 'OUT'
                        ELSE 'NO_CHANGE'
                    END AS direction
                "),
            ])
            ->where('sml.company_code', $companyCode)
            ->where('sml.warehouse_id', $warehouseId)
            ->orderBy('sml.movement_date', 'desc')
            ->orderBy('sml.id', 'desc')
            ->get();
    }
}
