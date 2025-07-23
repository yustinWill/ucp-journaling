<?php

namespace App\Domains\User\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserAuditLogService
{
    private $table_name = 'm_user_audit_logs';

    public function create(array $data, $tag_id)
    {
        return DB::table($this->table_name)->insertGetId([
            'f_user_id' => $tag_id,
            'action_type' => $data['action_type'],
            'description' => $data['description'],
            'metadata' => $data['metadata'],
            'created_at' => now(),
            'created_by' => Auth::id()
        ]);
    }

    public function findAllBasedOnId($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.f_user_id',
                't.action_type',
                't.description',
                't.metadata',
                't.created_at',
                't.created_by AS user_id',
                'usr.name AS user_name'
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_user_id', $id)
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }
}
