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
}
