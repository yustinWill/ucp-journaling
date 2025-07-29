<?php

namespace App\Domains\PurchaseDelivery\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseDeliveryService
{
    private $table_name = 't_purchase_deliveries';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'reference_no' => $data['reference_no'],
            'date' => $data['date'],
            'purchase_order_id' => $data['purchase_order_id'] ?? null,
            'supplier_id' => $data['supplier_id'],
            'supplier_reference' => $data['supplier_reference'] ?? null,
            'storage_id' => $data['storage_id'],
            'expected_date' => $data['expected_date'] ?? null,
            'email' => $data['email'] ?? null,
            'address' => $data['address'] ?? null,
            'delivery_mode' => $data['delivery_mode'] ?? 'manual',
            'allow_over_delivery' => $data['allow_over_delivery'] ?? false,
            'message' => $data['message'] ?? null,
            'memo' => $data['memo'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'status' => $data['status'] ?? 'PENDING',
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function update(int $id, array $data): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'reference_no' => $data['reference_no'],
                'date' => $data['date'],  
                'purchase_order_id' => $data['purchase_order_id'],
                'supplier_id' => $data['supplier_id'],
                'supplier_reference' => $data['supplier_reference'] ?? null,
                'storage_id' => $data['storage_id'],
                'message' => $data['message'] ?? null,
                'memo' => $data['memo'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'status' => $data['status'],
                'updated_at' => now(),
                'updated_by' => Auth::id()
            ]);
    }

    public function findAllDatatable()
    {
        $query = DB::table($this->table_name . ' as pd')
            ->select([
                'pd.id',
                'pd.reference_no',
                'pd.date',
                'pd.status',
                'pd.created_at',
                'pd.updated_at',
                'pd.created_by',
                'pd.updated_by',
                'po.reference_no as po_reference',
                'po.id as po_id',
                's.name as supplier',
                's.id as supplier_id',
                'st.name as storage',
                'st.id as storage_id',
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'pd.supplier_id', '=', 's.id')
            ->leftJoin('m_storages as st', 'pd.storage_id', '=', 'st.id');

        // Filter by status
        if ($status = request()->input('status')) {
            $query->where('pd.status', $status);
        }

        if ($supplier = request()->input('supplier')) {
            $query->where('s.id', $supplier);
        }

        if ($storage = request()->input('storage')) {
            $query->where('st.id', $storage);
        }

        if ($search = request()->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('pd.reference_no', 'like', "%{$search}%")
                  ->orWhere('po.reference_no', 'like', "%{$search}%")
                  ->orWhere('s.name', 'like', "%{$search}%");
            });
        }

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('pd.created_at', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }

    public function findOne($id)
    {
        return DB::table($this->table_name . ' as pd')
            ->select([
                'pd.*',
                'po.reference_no as po_reference',
                'po.id as po_id',
                's.name as supplier',
                's.email as supplier_email',
                's.address as supplier_address',
                'st.name as storage',
                'st.code as storage_code',
                'u_created.name as created_by_name',
                'u_updated.name as updated_by_name'
            ])
            ->leftJoin('t_purchase_orders as po', 'pd.purchase_order_id', '=', 'po.id')
            ->leftJoin('m_contacts as s', 'pd.supplier_id', '=', 's.id')
            ->leftJoin('m_storages as st', 'pd.storage_id', '=', 'st.id')
            ->leftJoin('m_users as u_created', 'pd.created_by', '=', 'u_created.id')
            ->leftJoin('m_users as u_updated', 'pd.updated_by', '=', 'u_updated.id')
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
                        'purchase_delivery_id' => $deliveryId,
                        'product_id' => $product['product_id'],
                        'ordered_qty' => $product['ordered_qty'] ?? null,
                        'delivered_qty' => $product['delivered_qty'] ?? null,
                        'delivery_qty' => $product['delivery_qty'],
                        'quantity' => $product['delivery_qty'], // For consistency
                        'unit' => $product['unit'] ?? null,
                        'notes' => $product['notes'] ?? null,
                        'status' => $product['status'] ?? 'GOOD',
                        'created_at' => now(),
                        'created_by' => Auth::id()
                    ];
                }
            }
            // Handle manual products
            elseif (isset($product['quantity'])) {
                if ($product['quantity'] > 0) {
                    $insertData[] = [
                        'purchase_delivery_id' => $deliveryId,
                        'product_id' => $product['product_id'],
                        'ordered_qty' => null,
                        'delivered_qty' => null,
                        'delivery_qty' => null,
                        'quantity' => $product['quantity'],
                        'unit' => $product['unit'] ?? null,
                        'notes' => $product['notes'] ?? null,
                        'status' => $product['status'] ?? 'GOOD',
                        'created_at' => now(),
                        'created_by' => Auth::id()
                    ];
                }
            }
        }

        if (!empty($insertData)) {
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
                'u.code as unit_code'
            ])
            ->leftJoin('m_products as p', 'pdp.product_id', '=', 'p.id')
            ->leftJoin('m_units as u', 'p.unit_id', '=', 'u.id')
            ->where('pdp.purchase_delivery_id', $deliveryId)
            ->get();
    }
}