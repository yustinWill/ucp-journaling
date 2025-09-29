<?php

namespace App\Domains\RevenueSplit\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RevenueSplitAuditLogService
{
    private $table_name = 'm_revenue_split_audit_logs';

    public function create(array $data, $revenue_split_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'f_revenue_split_id' => $revenue_split_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id(),
        ]);
    }

    public function findAllBasedOnRevenueSplitId($id)
    {
        return DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.f_revenue_split_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name',
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_revenue_split_id', $id)
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }

    public function findOneBasedOnId($id)
    {
        return DB::table($this->table_name)
            ->where('id', $id)
            ->first();
    }

    public function findAllDatatableBasedOnRevenueSplitId($id)
    {
        $query = DB::table($this->table_name.' as t')
            ->select([
                't.id',
                't.f_revenue_split_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name',
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_revenue_split_id', $id);

        $draw = (int) request()->input('draw', 1);
        $recordsTotal = DB::table($this->table_name)->where('f_revenue_split_id', $id)->count();
        $filteredQuery = clone $query;

        // Pagination
        $start = (int) request()->input('start', 0);
        $length = (int) request()->input('length', 10);
        $query->skip($start)->take($length);
        $recordsFiltered = $filteredQuery->count();
        $query->orderBy('t.created_at', 'DESC');
        $data = $query->get();

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];
    }
}