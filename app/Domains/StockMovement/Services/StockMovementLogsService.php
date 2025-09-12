<?php

namespace App\Domains\StockMovement\Services;

use Illuminate\Support\Facades\DB;

class StockMovementLogsService
{
    private $table_name = 't_stock_movement_logs';

    public function findAllLatestStockPerWarehouse($warehouseId, $categoryId = null, $global_search = null)
    {
        $query =  DB::table($this->table_name . ' AS t_stock_movement_logs')
            ->select(
                'm_products.id AS id',
                'm_products.code AS code',
                'm_products.name AS name',
                'm_units.name AS unit',
                'm_products.f_category_id AS category',
                DB::raw('SUM(
                    CASE 
                        WHEN t_stock_movement_logs.movement_type = "PURCHASE_IN" THEN t_stock_movement_logs.movement_qty
                        WHEN t_stock_movement_logs.movement_type = "SALES_OUT" THEN -t_stock_movement_logs.movement_qty
                        ELSE 0
                    END
                ) as system_stock')
            )
            ->join('m_products', 't_stock_movement_logs.product_id', '=', 'm_products.id')
            ->join('m_units', 'm_products.f_unit_id', '=', 'm_units.id')
            ->where('t_stock_movement_logs.warehouse_id', $warehouseId)
            ->where('t_stock_movement_logs.company_code', session('user_company_code'))
            ->groupBy('m_products.id', 'm_products.code', 'm_products.name');
        if ($categoryId) {
            $query->where('m_products.f_category_id', $categoryId);
        }
        if ($global_search) {
            $query->where(function ($subQuery) use ($global_search) {
                $subQuery->where('m_products.code', 'LIKE', '%' . $global_search . '%')
                    ->orWhere('m_products.name', 'LIKE', '%' . $global_search . '%');
            });
        }
        return $query->get();
    }
}
