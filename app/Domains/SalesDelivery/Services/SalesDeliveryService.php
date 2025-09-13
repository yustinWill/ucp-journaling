<?php

namespace App\Domains\SalesDelivery\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesDeliveryService
{
    private $table_name = 't_sales_deliveries';

    public function create(array $data)
    {
        // TODO: Implement sales delivery creation logic
        return DB::table($this->table_name)->insertGetId([
            'delivery_number' => $data['delivery_number'] ?? null,
            'sales_order_id' => $data['sales_order_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'delivery_date' => $data['delivery_date'] ?? now(),
            'status' => $data['status'] ?? 'PENDING',
            'warehouse_id' => $data['warehouse_id'] ?? null,
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        // TODO: Implement sales delivery update logic
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'status' => $data['status'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function findOne($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->first();
    }

    public function findById($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->first();
    }

    public function findProductLines($salesDeliveryId)
    {
        return DB::table('t_sales_delivery_products as sdp')
            ->select([
                'sdp.*',
                'p.code as product_code',
                'p.name as product_name',
                'u.name as unit_name',
            ])
            ->join('m_products as p', 'sdp.product_id', '=', 'p.id')
            ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('sdp.sales_delivery_id', $salesDeliveryId)
            ->get()->toArray();
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as sd')
            ->select([
                'sd.id',
                'sd.delivery_number',
                'sd.delivery_date',
                'sd.status',
                'sd.created_at',
                'sd.created_by',
                'so.transaction_code as sales_order_number',
                'so.id as so_id',
                'c.name as customer',
                'c.id as customer_id',
                'w.name as warehouse',
                'w.id as warehouse_id',
                'u.name as created_by',
                DB::raw("COALESCE(JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)), JSON_ARRAY()) as tags"),
            ])
            ->leftJoin('t_sales_orders as so', 'sd.sales_order_id', '=', 'so.id')
            ->leftJoin('m_contacts as c', 'sd.customer_id', '=', 'c.id')
            ->leftJoin('m_warehouses as w', 'sd.warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'sd.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(sd.tags, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->groupBy('sd.id')
            ->orderBy('sd.created_at', 'desc');

        // Apply filters if provided
        if ($dateRange = request()->input('date_range')) {
            $dates = explode(' to ', $dateRange);
            if (count($dates) == 2) {
                $query->whereBetween('sd.delivery_date', [$dates[0], $dates[1]]);
            }
        }

        if ($customerId = request()->input('customer_id')) {
            $query->where('sd.customer_id', $customerId);
        }

        if ($warehouseId = request()->input('warehouse_id')) {
            $query->where('sd.warehouse_id', $warehouseId);
        }

        if ($deliveryNo = request()->input('delivery_no')) {
            $query->where('sd.delivery_number', 'LIKE', "%{$deliveryNo}%");
        }

        if ($status = request()->input('status')) {
            $query->where('sd.status', $status);
        }

        if ($salesOrderNo = request()->input('sales_order_no')) {
            $query->where('so.transaction_code', 'LIKE', "%{$salesOrderNo}%");
        }

        $data = $query->get();

        return [
            'data' => $data,
        ];
    }

    public function delete($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }
}
