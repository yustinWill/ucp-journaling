<?php
namespace App\Domains\SalesInvoice\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesInvoiceService
{
    private $table_name = 't_sales_invoices';

    public function create(array $data)
    {
        // TODO: Implement sales invoice creation logic
        return DB::table($this->table_name)->insertGetId([
            'invoice_number' => $data['invoice_number'] ?? null,
            'sales_delivery_id' => $data['sales_delivery_id'] ?? null,
            'customer_id' => $data['customer_id'] ?? null,
            'invoice_date' => $data['invoice_date'] ?? now(),
            'status' => $data['status'] ?? 'PENDING',
            'total_amount' => $data['total_amount'] ?? 0,
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function update(int $id, array $data): int
    {
        // TODO: Implement sales invoice update logic
        return DB::table($this->table_name)
            ->where('id', $id)
            ->update([
                'status' => $data['status'] ?? null,
                'total_amount' => $data['total_amount'] ?? null,
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