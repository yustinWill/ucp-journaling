<?php

namespace App\Domains\Expense\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseAuditLogService
{
    private $table_name = 't_expense_audit_logs';

    public function create(array $data, $expense_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_expense_id' => $expense_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findAllBasedOnExpenseId($id)
    {
        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.f_expense_id',
                't.action_type as action',
                't.description',
                't.metadata',
                't.created_at AS date',
                't.created_by AS user_id',
                'usr.name AS user_name',
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_expense_id', $id)
            ->where('t.company_code', session('user_company_code'))
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }

    public function findOneBasedOnId($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->where('company_code', session('user_company_code'))
            ->first();
    }
}
