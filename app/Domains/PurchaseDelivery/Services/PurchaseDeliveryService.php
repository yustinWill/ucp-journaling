<?php

namespace App\Domains\PurchaseDelivery\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PurchaseDeliveryService
{
    private $table_name = 't_purchase_deliveries';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['reference_no'],
            'f_purchase_order_id' => $data['purchase_order_id'],
            'delivery_date' => $data['date'],
            'delivery_number' => $data['delivery_number'],
            'tracking_number' => $data['tracking_number'],
            'attachment' => $data['attachment'] ?? null,
            'courier_name' => $data['courier_name'],
            'f_warehouse_id' => $data['warehouse_id'],
            'description' => $data['description'] ?? null,
            'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
            'internal_memo' => $data['memo'] ?? null,
            'total_item' => $data['total_items'] ?? 0,
            'delivery_status' => $data['delivery_status'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'transaction_code' => $data['reference_no'],
                'f_purchase_order_id' => $data['purchase_order_id'],
                'delivery_date' => $data['delivery_date'],
                'f_supplier_id' => $data['supplier_id'] ?? null,
                'supplier_email' => $data['email'] ?? null,
                'supplier_address' => $data['address'] ?? null,
                'supplier_reference_no' => $data['supplier_reference'] ?? null,
                'f_warehouse_id' => $data['warehouse_id'],
                'expected_received_date' => $data['expected_date'] ?? null,
                'description' => $data['description'] ?? null,
                'f_tag_codes' => isset($data['tags']) ? json_encode($data['tags']) : null,
                'internal_memo' => $data['memo'] ?? null,
                'allow_over_delivery' => $data['allow_over_delivery'] ?? false,
                'total_item' => $data['total_item'] ?? 0,
                'updated_at' => now(),
                'updated_by' => Auth::id(),
            ]);
    }

    public function findAll()
    {
        return DB::table($this->table_name.' as pd')
            ->select([
                'pd.*',
                'po.transaction_code as po_reference',
                's.name as supplier_name',
                'w.name as warehouse_name',
                'u.name as created_by_name',
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'pd.f_supplier_id', '=', 's.id')
            ->leftJoin('m_warehouses as w', 'pd.f_warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'pd.created_by', '=', 'u.id')
            ->orderBy('pd.created_at', 'DESC')
            ->get();
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name.' as pd')
            ->select([
                'pd.id',
                'pd.transaction_code as reference_no',
                'pd.delivery_date',
                'pd.delivery_number',
                'pd.tracking_number',
                'pd.courier_name',
                'pd.created_at',
                'pd.created_by',
                'pd.delivery_status as delivery_status',
                'po.transaction_code as po_reference',
                'po.id as po_id',
                's.name as supplier_name',
                's.id as supplier_id',
                'w.name as warehouse_name',
                'w.id as warehouse_id',
                'u.name as created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('name', m_tags.name, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'po.f_supplier_id', '=', 's.id')
            ->leftJoin('m_warehouses as w', 'pd.f_warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'pd.created_by', '=', 'u.id')
            ->leftJoin(DB::raw("JSON_TABLE(pd.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('pd.company_code', session('user_company_code'));

        // Apply filters
        if ($dateRange = request()->input('date_range_filter')) {
            if (strpos($dateRange, ' - ') !== false) {
                [$dateFrom, $dateTo] = array_map('trim', explode(' - ', $dateRange));
                // Ensure full day for end date
                $dateFrom .= ' 00:00:00';
                $dateTo .= ' 23:59:59';
                $query->whereBetween('pd.created_at', [$dateFrom, $dateTo]);
            }
        }

        if ($deliveryDate = request()->input('delivery_date_filter')) {
            $query->where('pd.delivery_date', $deliveryDate);
        }

        if ($supplier = request()->input('supplier_filter')) {
            $query->where('po.f_supplier_id', $supplier);
        }

        if ($deliverySearch = request()->input('delivery_search_filter')) {
            $query->where(function ($q) use ($deliverySearch) {
                $q->where('pd.transaction_code', 'like', "%{$deliverySearch}%")
                    ->orWhere('po.transaction_code', 'like', "%{$deliverySearch}%");
            });
        }

        $query->groupBy('pd.id');
        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;
        $recordsFiltered = $filteredQuery->count();

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $query->orderBy('pd.created_at', 'DESC');
        $data = $query->get();

        // Format data for DataTable
        $formattedData = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'transaction_date' => $item->created_at,
                'reference_no' => $item->reference_no,
                'delivery_number' => $item->delivery_number,
                'po_reference' => $item->po_reference,
                'po_id' => $item->po_id,
                'supplier_name' => $item->supplier_name,
                'supplier_id' => $item->supplier_id,
                'warehouse_name' => $item->warehouse_name,
                'delivery_date' => $item->delivery_date,
                'status' => $item->delivery_status,
                'tracking_number' => $item->tracking_number,
                'courier_name' => $item->courier_name,
                'created_by_name' => $item->created_by_name,
                'created_by' => $item->created_by,
                'tags' => $item->tags,
            ];
        });

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $formattedData,
        ];
    }

    public function findOne($id)
    {
        $data = DB::table($this->table_name.' as pd')
            ->select([
                'pd.*',
                'po.transaction_code as po_reference',
                'po.id as po_id',
                's.name as supplier',
                'po.supplier_email as supplier_email',
                'po.supplier_address as supplier_address',
                'st.name as warehouse',
                'st.code as warehouse_code',
                'u_created.name as created_by_name',
                'u_updated.name as updated_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'po.f_supplier_id', '=', 's.id')
            ->leftJoin('m_warehouses as st', 'pd.f_warehouse_id', '=', 'st.id')
            ->leftJoin('users as u_created', 'pd.created_by', '=', 'u_created.id')
            ->leftJoin('users as u_updated', 'pd.updated_by', '=', 'u_updated.id')
            ->leftJoin(DB::raw("JSON_TABLE(pd.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->where('pd.id', $id)
            ->groupBy('pd.id')
            ->first();

        if ($data && $data->tags) {
            $data->tags = json_decode($data->tags); // Returns objects for view compatibility
        }
        if ($data->attachment) {
            $data->attachment = Storage::disk('public')->url($data->attachment);
        }

        return $data;
    }

    public function findAllProducts($id)
    {
        return DB::table('t_purchase_delivery_products'.' as pdp')
            ->select([
                'pdp.*',
                'pdp.note AS product_delivery_note',
                'pop.qty AS ordered_qty',
                'pop.price_per_qty AS price_per_qty',
                'mp.name AS product_name',
                'mu.code AS unit_code',
                'mu.name AS unit_name',
            ])
            ->leftJoin('m_products as mp', 'pdp.f_product_id', '=', 'mp.id')
            ->leftJoin('t_purchase_order_products as pop', 'pdp.f_product_order_product_id', '=', 'pop.id')
            ->leftJoin('m_products as st', 'pdp.f_product_id', '=', 'st.id')
            ->leftJoin('m_units as mu', 'mu.id', '=', 'pop.f_unit_id')
            ->where('pdp.f_purchase_delivery_id', $id)
            ->orderBy('pdp.id', 'ASC')
            ->get();
    }

    public function createDeliveryProducts(int $deliveryId, array $products)
    {
        $insertData = [];
        foreach ($products as $product) {
            $insertData[] = [
                'f_purchase_delivery_id' => $deliveryId,
                'f_product_order_product_id' => $product['product_order_product_id'],
                'f_product_id' => $product['product_id'],
                'delivered_qty_before' => $product['delivered_qty_before'],
                'remaining_qty_before' => $product['remaining_qty_before'],
                'qty' => $product['delivery_qty'],
                'delivered_qty_after' => $product['delivered_qty_before'] + $product['delivery_qty'],
                'remaining_qty_after' => $product['remaining_qty_before'] - $product['delivery_qty'],
                'note' => $product['note'],
                'status' => $product['status'],
                'created_at' => now(),
                'created_by' => Auth::id(),
            ];
        }

        if (! empty($insertData)) {
            DB::table('t_purchase_delivery_products')->insert($insertData);
        }
    }

    public function getDeliveryProducts(int $deliveryId)
    {
        return DB::table('t_purchase_delivery_products as pdp')
            ->select([
                'pdp.*',
                'p.name as product_name',
                'p.code as product_code',
                'p.description as product_description',
                'u.name as unit_name',
                'u.code as unit_code',
            ])
            ->leftJoin('m_products as p', 'pdp.f_product_id', '=', 'p.id')
            ->leftJoin('m_units as u', 'pdp.f_unit_id', '=', 'u.id')
            ->where('pdp.f_purchase_delivery_id', $deliveryId)
            ->get();
    }

    public function findAllHeaderBasedOnPoId($poId)
    {
        return DB::table($this->table_name.' as pd')
            ->select([
                'pd.*',
                'u.name as created_by_name',
                DB::raw("JSON_ARRAYAGG(JSON_OBJECT('code', m_tags.code, 'preview', m_tag_colors.preview)) as tags"),
            ])
            ->leftJoin(DB::raw("JSON_TABLE(pd.f_tag_codes, '$[*]' COLUMNS(id VARCHAR(255) PATH '$')) as tag_json"), function ($join) {
                $join->on(DB::raw('1'), '=', DB::raw('1')); // ON TRUE
            })
            ->leftJoin('m_tags', 'tag_json.id', '=', 'm_tags.id')
            ->leftJoin('m_tag_colors', 'm_tags.f_tag_color_id', '=', 'm_tag_colors.id')
            ->leftJoin('users as u', 'pd.created_by', '=', 'u.id')
            ->where('pd.f_purchase_order_id', $poId)
            ->groupBy('pd.id')
            ->orderBy('pd.delivery_date', 'ASC')
            ->get();
    }
}
