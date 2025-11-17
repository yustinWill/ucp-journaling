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
        $query = DB::table('t_stock_movement_logs as t_stock_movement_logs')
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
                DB::raw('SUM(
                    CASE 
                        WHEN t_stock_movement_logs.movement_type = "PURCHASE_IN" THEN t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type = "SALES_OUT" THEN -t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type = "ADJUSTMENT_IN" THEN t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type = "ADJUSTMENT_OUT" THEN -t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type = "PRODUCTION_IN" THEN t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type = "PRODUCTION_OUT" THEN -t_stock_movement_logs.movement_qty
                        ELSE 0
                    END
                ) as system_stock'),
                DB::raw('IFNULL((
                    SELECT pop.price_per_qty
                    FROM t_purchase_order_products pop
                    WHERE pop.f_product_id = m_products.id
                    ORDER BY pop.created_at DESC
                    LIMIT 1
                ), 0) as latest_purchase_price')
            )
            ->join('m_products', 't_stock_movement_logs.product_id', '=', 'm_products.id')
            ->join('m_categories as categories', 'categories.id', '=', 'm_products.f_category_id')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->where('t_stock_movement_logs.warehouse_id', $warehouseId)
            ->where('t_stock_movement_logs.company_code', session('user_company_code'))
            ->groupBy('m_products.id', 'm_products.code', 'm_products.name');
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
        $query = DB::table($this->table_name.' AS t_stock_movement_logs')
            ->select(
                'm_products.id AS id',
                'm_products.code AS code',
                'm_products.name AS name',
                'm_units.name AS unit',
                't_stock_movement_logs.warehouse_id AS warehouse_id',
                'm_warehouses.code AS warehouse_code',
                'm_warehouses.name AS warehouse_name',
                'm_products.f_category_id AS category',
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
            ->where('t_stock_movement_logs.company_code', session('user_company_code'))
            ->groupBy('t_stock_movement_logs.warehouse_id', 'm_products.id');

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
        return DB::table('t_stock_movement_logs as inlog')
            ->join('t_stock_movement_logs as outlog', function ($join) {
                $join->on('outlog.company_code', '=', 'inlog.company_code')
                    ->on('outlog.reference_number', '=', 'inlog.reference_number')
                    ->on('outlog.product_id', '=', 'inlog.product_id')
                    ->whereColumn('outlog.id', '<>', 'inlog.id')
                    ->whereColumn('outlog.stock_after', '<', 'outlog.stock_before'); // OUT
            })
            ->whereColumn('inlog.stock_after', '>', 'inlog.stock_before')            // IN
            ->where(function ($q) use ($warehouseId) {
                $q->where('inlog.warehouse_id', $warehouseId)
                ->orWhere('outlog.warehouse_id', $warehouseId);
            })
            ->orderBy('inlog.movement_date')
            ->orderBy('inlog.id')
            ->select([
                'inlog.movement_date as movement_date',
                'inlog.product_id as product_id',
                'inlog.product_code as product_code',
                'inlog.movement_qty as qty',
                DB::raw("
                    CASE
                        WHEN inlog.warehouse_id = $warehouseId
                            THEN inlog.stock_before
                        ELSE outlog.stock_before
                    END as stock_before
                "),
                DB::raw("
                    CASE
                        WHEN inlog.warehouse_id = $warehouseId
                            THEN inlog.stock_after
                        ELSE outlog.stock_after
                    END as stock_after
                "),
                'outlog.warehouse_id as warehouse_id_from',
                'outlog.warehouse_name as warehouse_from',
                'inlog.warehouse_id as warehouse_id_to',
                'inlog.warehouse_name as warehouse_to',
            ])
            ->get();
    }
}
