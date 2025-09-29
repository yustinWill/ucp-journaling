<?php

namespace App\Domains\RevenueSplit\Actions;

use App\Domains\RevenueSplit\Services\RevenueSplitAuditLogService;
use App\Domains\RevenueSplit\Services\RevenueSplitService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateRevenueSplitAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $id = app(RevenueSplitService::class)->create($data);
            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan Cycle Keuntungan Penjualan',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(RevenueSplitAuditLogService::class)->create($log_data, $id);
            
            return $id;
        });
    }
}