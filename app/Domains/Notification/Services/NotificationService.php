<?php

namespace App\Domains\Notification\Services;

use App\Enums\GeneralStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    private $table_name = 't_notification';

    public function findAllList()
    {
        $userId = Auth::id();

        return DB::table($this->table_name.' as t')
            ->select([
                't.*',
                DB::raw('CASE WHEN l.id IS NULL THEN 0 ELSE 1 END as is_read'),
            ])
            ->leftJoin('l_read_notification as l', function ($join) use ($userId) {
                $join->on('t.id', '=', 'l.f_notification_id')
                    ->where('l.f_read_by_user_id', '=', $userId);
            })
            ->where('t.status', GeneralStatuses::ACTIVE)
            ->where(function ($query) use ($userId) {
                $query->where('t.target_user_id', $userId)
                    ->orWhereNull('t.target_user_id');
            })
            ->orderBy('t.created_at', 'DESC')
            ->get();
    }

    public function create(array $data)
    {
        return DB::table($this->table_name)->insertGetId([
            'broadcast_type' => $data['broadcast_type'],
            'notification_type' => $data['notification_type'],
            'target_user_id' => $data['target_user_id'],
            'icon_text' => $data['icon_text'],
            'title' => $data['title'],  
            'message' => $data['message'],
            'metadata' => $data['metadata'],
            'status' => $data['status'],
            'created_at' => now(),
        ]);
    }
}
