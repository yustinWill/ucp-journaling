<?php

namespace App\Domains\PurchaseDelivery\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseDeliveryService
{
    private $table_name = 't_purchase_deliveries';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'transaction_code' => $data['reference_no'],
            'f_purchase_order_id' => $data['purchase_order_id'] ?? null,
            'delivery_date' => $data['delivery_date'] ?? null,
            'f_supplier_id' => $data['supplier_id'],
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
                'pd.created_at',
                'pd.created_by',
                'po.transaction_code as po_reference',
                'po.id as po_id',
                's.name as supplier_name',
                's.id as supplier_id',
                'w.name as warehouse_name',
                'w.id as warehouse_id',
                'u.name as created_by_name',
                DB::raw('COALESCE(pd.f_tag_codes, "[]") as tags'),
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'pd.f_supplier_id', '=', 's.id')
            ->leftJoin('m_warehouses as w', 'pd.f_warehouse_id', '=', 'w.id')
            ->leftJoin('users as u', 'pd.created_by', '=', 'u.id')
            ->where('pd.company_code', session('user_company_code'));

        // Apply filters
        if ($dateRange = request()->input('date_range_filter')) {
            // Check if date is a range (contains ' - ')
            if (strpos($dateRange, ' - ') !== false) {
                [$dateFrom, $dateTo] = explode(' - ', $dateRange);
                $query->whereBetween('pd.created_at', [$dateFrom, $dateTo]);
            }
        }

        if ($deliveryDate = request()->input('delivery_date_filter')) {
            $query->where('pd.delivery_date', $deliveryDate);
        }

        if ($supplier = request()->input('supplier_filter')) {
            $query->where('pd.f_supplier_id', $supplier);
        }

        if ($deliverySearch = request()->input('delivery_search_filter')) {
            $query->where(function ($q) use ($deliverySearch) {
                $q->where('pd.transaction_code', 'like', "%{$deliverySearch}%")
                    ->orWhere('po.transaction_code', 'like', "%{$deliverySearch}%");
            });
        }

        if ($search = request()->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('pd.transaction_code', 'like', "%{$search}%")
                    ->orWhere('po.transaction_code', 'like', "%{$search}%")
                    ->orWhere('s.name', 'like', "%{$search}%");
            });
        }

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
                'transaction_date' => date('d/m/Y', strtotime($item->created_at)),
                'reference_no' => $item->reference_no,
                'delivery_number' => '-', // Not in current schema
                'po_reference' => $item->po_reference,
                'po_id' => $item->po_id,
                'supplier_name' => $item->supplier_name,
                'supplier_id' => $item->supplier_id,
                'warehouse_name' => $item->warehouse_name,
                'delivery_date' => $item->delivery_date ? date('d/m/Y', strtotime($item->delivery_date)) : '-',
                'status' => 'PENDING', // Default status as not in current schema
                'tracking_number' => '-', // Not in current schema
                'courier_name' => '-', // Not in current schema
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
        return DB::table($this->table_name.' as pd')
            ->select([
                'pd.*',
                'po.transaction_code as po_reference',
                'po.id as po_id',
                's.name as supplier',
                's.email as supplier_email',
                's.address as supplier_address',
                'st.name as warehouse',
                'st.code as warehouse_code',
                'u_created.name as created_by_name',
                'u_updated.name as updated_by_name',
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.f_purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'pd.f_supplier_id', '=', 's.id')
            ->leftJoin('m_warehouses as st', 'pd.f_warehouse_id', '=', 'st.id')
            ->leftJoin('users as u_created', 'pd.created_by', '=', 'u_created.id')
            ->leftJoin('users as u_updated', 'pd.updated_by', '=', 'u_updated.id')
            ->where('pd.id', $id)
            ->first();
    }

    public function createDeliveryProducts(int $deliveryId, array $products)
    {
        $insertData = [];
        foreach ($products as $product) {
            // Handle PO-based products
            if (isset($product['delivery_qty'])) {
                if ($product['delivery_qty'] > 0) {
                    $insertData[] = [
                        'f_purchase_delivery_id' => $deliveryId,
                        'f_product_id' => $product['product_id'],
                        'qty' => $product['delivery_qty'],
                        'f_unit_id' => $product['unit_id'] ?? 1, // Default unit ID
                        'note' => $product['notes'] ?? null,
                        'status' => $product['status'] ?? 'GOOD',
                        'created_at' => now(),
                        'created_by' => Auth::id(),
                    ];
                }
            }
            // Handle manual products
            elseif (isset($product['quantity'])) {
                if ($product['quantity'] > 0) {
                    $insertData[] = [
                        'f_purchase_delivery_id' => $deliveryId,
                        'f_product_id' => $product['product_id'],
                        'qty' => $product['quantity'],
                        'f_unit_id' => $product['unit_id'] ?? 1,
                        'note' => $product['notes'] ?? null,
                        'status' => $product['status'] ?? 'GOOD',
                        'created_at' => now(),
                        'created_by' => Auth::id(),
                    ];
                }
            }
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
}
