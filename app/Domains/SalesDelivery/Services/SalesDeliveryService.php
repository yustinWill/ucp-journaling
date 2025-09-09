<?php
namespace App\Domains\SalesDelivery\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'created_by' => Auth::id()
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
                'updated_by' => Auth::id()
            ]);
    }

    public function findOne($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->first();
    }

    public function findAll()
    {
        return DB::table($this->table_name)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function delete($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }
}