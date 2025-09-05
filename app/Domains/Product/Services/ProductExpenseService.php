<?php

namespace App\Domains\Product\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductExpenseService
{
    private $table_name = 'm_product_expenses';

    public function create(array $data)
    {
        $insert_data = [];
        foreach ($data as $row) {
            $insert_data[] = [
                'f_product_id' => $row['f_product_id'],
                'f_expense_coa_code' => $row['f_expense_coa_code'],
                'expense_price' => $row['expense_price'],
                'created_by' => Auth::id(),
                'created_at' => now(),
            ];
        }

        return DB::table($this->table_name)->insert($insert_data);
    }

    public function update($id, array $data)
    {
        return DB::table($this->table_name)->where('id', $id)->update([
            'f_expense_coa_code' => $data['f_expense_coa_code'],
            'expense_price' => $data['expense_price'],
            'updated_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    public function findAllBasedOnProductId($product_id)
    {
        return DB::table($this->table_name.' as t')
            ->select('t.*')
            ->where('t.f_product_id', $product_id)
            ->get();
    }

    public function delete($product_id, $id)
    {
        return DB::table($this->table_name)->where('id', $id)->where('f_product_id', $product_id)->delete();
    }
}
