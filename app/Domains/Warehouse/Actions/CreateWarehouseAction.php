<?php

namespace App\Domains\Warehouse\Actions;

use App\Domains\Warehouse\Services\WarehouseAuditLogService;
use App\Domains\Warehouse\Services\WarehouseService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateWarehouseAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $warehouse_id = app(WarehouseService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan Warehouse: '.$data['name'],
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(WarehouseAuditLogService::class)->create($log_data, $warehouse_id);

            return $warehouse_id;
        });
    }
}
