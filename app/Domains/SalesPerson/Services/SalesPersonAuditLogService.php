<?php

namespace App\Domains\SalesPerson\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesPersonAuditLogService
{
    private $table_name = 'm_sales_person_audit_logs';

    public function create(array $data, $tag_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'company_code' => session('user_company_code'),
            'f_sales_person_id' => $tag_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function findAllBasedOnTagId($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.f_sales_person_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name'
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.company_code', session('user_company_code'))
            ->where('t.f_sales_person_id', $id)
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }
}