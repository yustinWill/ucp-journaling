<?php

namespace App\Domains\ReturnManagement\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnManagementProductService
{
    private $table_name = 't_return_management_products';

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'f_return_management_id' => $data['f_return_management_id'],
            'f_source_invoice_product_id' => $data['f_source_invoice_product_id'],
            'f_product_id' => $data['f_product_id'],
            'original_qty' => $data['original_qty'],
            'returned_qty_before' => $data['returned_qty_before'] ?? 0,
            'available_qty' => $data['available_qty'],
            'return_qty' => $data['return_qty'],
            'returned_qty_after' => $data['returned_qty_after'] ?? 0,
            'unit_price' => $data['unit_price'],
            'return_unit_price' => $data['return_unit_price'],
            'total_return_price' => $data['total_return_price'],
            'return_condition' => $data['return_condition'] ?? 'NEW',
            'return_reason' => $data['return_reason'] ?? null,
            'note' => $data['note'] ?? null,
            'status' => $data['status'] ?? 'ACTIVE',
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function getByReturnId(int $returnId)
    {
        return DB::table($this->table_name . ' as rmp')
            ->join('m_products as p', 'rmp.f_product_id', '=', 'p.id')
            ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('rmp.f_return_management_id', $returnId)
            ->where('rmp.status', 'ACTIVE')
            ->select([
                'rmp.*',
                'p.code as product_code',
                'p.name as product_name',
                'u.code as unit_code',
                'u.name as unit_name'
            ])
            ->get();
    }

    public function update(int $id, array $data): int
    {
        $updateData = [];
        
        $allowedFields = [
            'return_qty', 'return_unit_price', 'total_return_price',
            'return_condition', 'return_reason', 'note', 'returned_qty_after'
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        $updateData['updated_at'] = now();
        $updateData['updated_by'] = Auth::id();

        return DB::table($this->table_name)
            ->where('id', $id)
            ->update($updateData);
    }

    public function delete(int $id): int
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->delete();
    }

    public function deleteByReturnId(int $returnId): int
    {
        return DB::table($this->table_name)
            ->where('f_return_management_id', $returnId)
            ->delete();
    }

    public function bulkCreate(int $returnId, array $products): array
    {
        $createdIds = [];
        
        foreach ($products as $product) {
            $product['f_return_management_id'] = $returnId;
            $createdIds[] = $this->create($product);
        }

        return $createdIds;
    }

    public function getAvailableQtyForReturn(int $sourceInvoiceProductId): float
    {
        // Get original invoiced quantity from invoice product
        $invoiceProduct = DB::table('t_purchase_invoice_products')
            ->where('id', $sourceInvoiceProductId)
            ->first(['qty']);
        
        if (!$invoiceProduct) {
            // Try sales invoice products if not found in purchase
            $invoiceProduct = DB::table('t_sales_invoice_products')
                ->where('id', $sourceInvoiceProductId)
                ->first(['qty']);
        }

        if (!$invoiceProduct) {
            return 0;
        }

        $originalQty = $invoiceProduct->qty;

        // Calculate total already returned quantity
        $totalReturned = DB::table($this->table_name)
            ->where('f_source_invoice_product_id', $sourceInvoiceProductId)
            ->where('status', 'ACTIVE')
            ->sum('return_qty');

        return max(0, $originalQty - $totalReturned);
    }

    public function calculateReturnedQuantities(int $sourceInvoiceProductId): array
    {
        // Get all returns for this invoice product
        $returns = DB::table($this->table_name)
            ->where('f_source_invoice_product_id', $sourceInvoiceProductId)
            ->where('status', 'ACTIVE')
            ->orderBy('created_at', 'asc')
            ->get(['return_qty', 'created_at']);

        $totalReturned = 0;
        $returnHistory = [];

        foreach ($returns as $return) {
            $totalReturned += $return->return_qty;
            $returnHistory[] = [
                'date' => $return->created_at,
                'qty' => $return->return_qty,
                'total_returned_after' => $totalReturned
            ];
        }

        return [
            'total_returned' => $totalReturned,
            'return_history' => $returnHistory
        ];
    }

    public function validateReturnQuantities(array $returnProducts): array
    {
        $errors = [];

        foreach ($returnProducts as $index => $product) {
            $availableQty = $this->getAvailableQtyForReturn($product['f_source_invoice_product_id']);
            
            if ($product['return_qty'] > $availableQty) {
                $errors["products.{$index}.return_qty"] = "Return quantity ({$product['return_qty']}) cannot exceed available quantity ({$availableQty})";
            }

            if ($product['return_qty'] <= 0) {
                $errors["products.{$index}.return_qty"] = "Return quantity must be greater than 0";
            }
        }

        return $errors;
    }
}