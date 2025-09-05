<?php

namespace App\Domains\Category\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryAuditLogService
{
    private $table_name = 'm_category_audit_logs';

    public function create(array $data, $tag_id)
    {
        $companyCode = session('user_company_code');

        return DB::table($this->table_name)->insertGetId([
            'company_code' => $companyCode,
            'f_category_id' => $tag_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findAllBasedOnTagId($id)
    {
        $companyCode = session('user_company_code');

        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.f_category_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name',
                't.created_at',
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.company_code', $companyCode)
            ->where('t.f_category_id', $id)
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
