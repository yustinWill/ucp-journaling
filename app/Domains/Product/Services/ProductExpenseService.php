<?php

namespace App\Domains\Product\Services;

use App\Enums\StockStatuses;
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
                'created_at' => now()
            ];
        }
        return DB::table($this->table_name)->insert($insert_data);
    }
}
