<?php

namespace App\Domains\SalesDelivery\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalesDeliveryService
{
    private $table_name = 't_sales_deliveries';

    public function create(array $data)
    {
        // TODO: Implement sales delivery creation logic
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'delivery_number' => $data['delivery_number'],
            'attachment_path' => $data['attachment'] ?? null,
            'sales_order_id' => $data['sales_order_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'customer_reference' => $data['customer_reference'] ?? null,
            'delivery_notes' => $data['delivery_notes'] ?? null,
            'delivery_address' => $data['delivery_address'] ?? null,
            'shipping_cost' => $data['shipping_cost'] ?? 0,
            'tags' => isset($data['tags']) ? json_encode($data['tags']) : null,
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
                'customer_reference' => $data['customer_reference'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_notes' => $data['delivery_notes'] ?? null,
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name . ' as t_sales_deliveries')
            ->select([
                't_sales_deliveries.*',
                'so.transaction_code as sales_order_number',
                't_sales_deliveries.attachment_path AS attachment',
                'c.name as customer_name',
                'c.email as customer_email',
                'ca.address as customer_address',

                'w.name as warehouse_name',
                'u.name as created_by_name',
                'is_confirmed',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('m_contacts as c', 't_sales_deliveries.customer_id', '=', 'c.id')
            ->leftJoin('m_contact_addresses as ca', function ($join) {
                $join->on('c.id', '=', 'ca.f_contact_id')
                    ->where('ca.type', '=', 'SHIPPING');
            })
            ->leftJoin('m_warehouses as w', 't_sales_deliveries.warehouse_id', '=', 'w.id')
            ->leftJoin('t_sales_orders as so', 't_sales_deliveries.sales_order_id', '=', 'so.id')
            ->leftJoin('users as u', 't_sales_deliveries.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(t_sales_deliveries.tags, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('t_sales_deliveries.id', $id)
            ->where('t_sales_deliveries.company_code', session('user_company_code'))
            ->groupBy('t_sales_deliveries.id','ca.address')
            ->first();

        if ($data && $data->tags) {
            $data->tags = json_decode($data->tags); // Returns objects for view compatibility
        }
        if ($data->attachment) {
            $data->attachment = Storage::disk('public')->url($data->attachment);
        }
        return $data;
    }

    public function findById($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->first();
    }

    public function findProductLines($salesDeliveryId)
    {
        return DB::table('t_sales_delivery_products as sdp')
            ->select([
                'sdp.*',
                'p.code as product_code',
                'p.name as product_name',
                DB::raw('COALESCE(u.name, u_default.name) as unit_name'),
            ])
            ->join('m_products as p', 'sdp.product_id', '=', 'p.id')
            ->leftJoin('m_units as u', 'sdp.f_unit_id', '=', 'u.id')
            ->leftJoin('m_units as u_default', 'p.f_unit_id', '=', 'u_default.id')
            ->where('sdp.sales_delivery_id', $salesDeliveryId)
            ->get()->toArray();
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->where('company_code', session('user_company_code'))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findAllById($id)
    {
        return DB::table($this->table_name)
            ->select([
                't_sales_deliveries.*',
                'u.name as created_by_name',
            ])
            ->leftJoin('users as u', 't_sales_deliveries.created_by', '=', 'u.id')
            ->where('company_code', session('user_company_code'))
            ->where('sales_order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as sd')
            ->select([
                'sd.id',
                'sd.delivery_number',
                'sd.delivery_date',
                'sd.status',
                'sd.is_confirmed',
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
            ->where('sd.company_code', session('user_company_code'))
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
            $query->where(function ($q) use ($deliveryNo) {
                $q->where('so.transaction_code', 'LIKE', "%{$deliveryNo}%")
                    ->orWhere('sd.delivery_number', 'LIKE', "%{$deliveryNo}%");
            });
        }

        $query->groupBy('sd.id');

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->get()->count();
        $query->orderBy('sd.id', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function delete($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }

    public function createDeliveryProducts(int $deliveryId, array $products)
    {
        $insertData = [];
        foreach ($products as $product) {
            $insertData[] = [
                'sales_delivery_id' => $deliveryId,
                'sales_order_product_id' => $product['sales_order_product_id'],
                'product_id' => $product['product_id'],
                'f_unit_id' => $product['unit_id'] ?? null,
                'delivered_qty_before' => $product['delivered_qty_before'],
                'remaining_qty_before' => $product['remaining_qty_before'],
                'qty' => $product['delivery_qty'],
                'delivered_qty_after' => $product['delivered_qty_before'] + $product['delivery_qty'],
                'remaining_qty_after' => $product['remaining_qty_before'] - $product['delivery_qty'],
                'description' => $product['note'],
                'delivery_status' => $product['status'],
                'created_at' => now(),
                'created_by' => Auth::id(),
            ];
        }

        if (! empty($insertData)) {
            DB::table('t_sales_delivery_products')->insert($insertData);
        }
    }
}
