<?php

namespace App\Domains\Expense\Services;

use Illuminate\Support\Facades\DB;

class ExpenseCoaDetailService
{
    private $table_name = 't_expense_coa_details';

    public function create($data)
    {
        return DB::table($this->table_name)->insert($data);
    }
    
    public function deleteByExpenseId($expense_id)
    {
        return DB::table($this->table_name)
            ->where('f_expense_id', $expense_id)
            ->delete();
    }
}
