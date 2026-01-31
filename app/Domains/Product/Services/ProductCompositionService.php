<?php

namespace App\Domains\Product\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductCompositionService
{
    private $table_name = 'm_product_compositions';

    public function create(array $data)
    {
        $insert_data = [];
        foreach ($data as $row) {
            $insert_data[] = [
                'f_product_id' => $row['f_product_id'],
                'f_product_composition_product_id' => $row['f_product_composition_product_id'],
                'qty' => $row['qty'],
                'f_unit_id' => $row['f_unit_id'],
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];
        }

        return DB::table($this->table_name)->insert($insert_data);
    }

    public function update($id, array $data)
    {
        return DB::table($this->table_name)->where('id', $id)->update([
            'f_product_composition_product_id' => $data['f_product_composition_product_id'],
            'qty' => $data['qty'],
            'f_unit_id' => $data['f_unit_id'],
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    public function findAllBasedOnProductId($product_id,$warehouseId)
    {
        $stockSub = DB::table('t_stock_movement_logs')
            ->select(
                'product_id',
                DB::raw('SUM(
                    CASE 
                        WHEN movement_type LIKE "%_IN" THEN movement_qty
                        WHEN movement_type LIKE "%_OUT" THEN -movement_qty
                        ELSE 0
                    END
                ) as total_stock')
            )
            ->where('warehouse_id', $warehouseId)
            ->groupBy('product_id');

        $data = DB::table('m_product_compositions as t')
            ->select(
                't.*',
                DB::raw('COALESCE(stock.total_stock, 0) as total_stock'),
                'prd.name as product_name',
                'unt.name as unit_name',
                'unt.code as unit_code',
                DB::raw('COALESCE((
                    SELECT final_hpp
                    FROM t_product_stock_batches
                    WHERE f_product_id = t.f_product_composition_product_id
                        AND status = "ACTIVE"
                    ORDER BY batch_date DESC, id DESC
                    LIMIT 1
                ), 0) as purchase_price')
            )
            ->join('m_products as prd', 'prd.id', '=', 't.f_product_composition_product_id')
            ->join('m_units as unt', 'unt.id', '=', 't.f_unit_id')
            ->leftJoinSub($stockSub, 'stock', function ($join) {
                $join->on('stock.product_id', '=', 't.f_product_composition_product_id');
            })
            ->where('t.f_product_id', $product_id)
            ->get();
        return $data;

        // return DB::table($this->table_name.' as t')
        //     ->select('t.*', DB::raw('SUM(
        //             CASE 
        //                 WHEN tsml.movement_type = "PURCHASE_IN" THEN tsml.movement_qty
        //                 WHEN tsml.movement_type = "SALES_OUT" THEN -tsml.movement_qty
        //                 WHEN tsml.movement_type = "ADJUSTMENT_IN" THEN tsml.movement_qty
        //                 WHEN tsml.movement_type = "ADJUSTMENT_OUT" THEN -tsml.movement_qty
        //                 WHEN tsml.movement_type = "PRODUCTION_IN" THEN tsml.movement_qty
        //                 WHEN tsml.movement_type = "PRODUCTION_OUT" THEN -tsml.movement_qty
        //                 ELSE 0
        //             END
        //         ) as total_stock'), 'prd.name as product_name', 'unt.name as unit_name')
        //     ->join('m_products as prd', 'prd.id', '=', 't.f_product_composition_product_id')
        //     // ->join('t_stock_movement_logs as tsml', function($join) use ($warehouseId) {
        //     //     $join->on('tsml.product_id','=','t.f_product_composition_product_id')
        //     //         ->where('tsml.warehouse_id', '=', $warehouseId);
        //     // })
        //     ->join('t_stock_movement_logs as tsml',
        //         'tsml.product_id',
        //         '=',
        //         't.f_product_composition_product_id'
        //     )
        //     ->join('m_units as unt', 'unt.id', '=', 't.f_unit_id')
        //     ->where('t.f_product_id', $product_id)
            
        //     ->groupBy('prd.id', 'prd.code', 'prd.name')
        //     ->get();
    }

    public function findAllBasedOnProductIdOld($product_id)
    {
        return DB::table($this->table_name.' as t')
            ->select('t.*', 'prd.total_stock', 'prd.name as product_name', 'prd.average_price as hpp', 'unt.name as unit_name')
            ->join('m_products as prd', 'prd.id', '=', 't.f_product_composition_product_id')

            ->join('m_units as unt', 'unt.id', '=', 't.f_unit_id')
            ->where('t.f_product_id', $product_id)
            ->get();
    }

    public function delete($product_id, $id)
    {
        return DB::table($this->table_name)->where('id', $id)->where('f_product_id', $product_id)->delete();
    }
}
