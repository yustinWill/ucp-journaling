<?php

namespace App\Domains\Tag\Services;

use Illuminate\Support\Facades\DB;

class TagUsageLogService
{
    private $table_name = 'm_tag_usage_logs';

    public function findAllBasedOnTagId($id)
    {
        return DB::table($this->table_name . ' as t')
            ->select([
                't.f_tag_id',
                't.module',
                't.f_transaction_code AS transaction_code',
                't.description',
                't.amount',
                't.created_at',
                't.created_by',
                'usr.name AS user_name'
            ])
            ->leftJoin('users as usr', 'usr.id', '=', 't.created_by')
            ->where('t.f_tag_id', $id)
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }
}
