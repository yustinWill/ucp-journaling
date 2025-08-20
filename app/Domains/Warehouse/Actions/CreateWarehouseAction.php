<?php

namespace App\Domains\Warehouse\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Warehouse\Services\WarehouseService;
use App\Domains\Warehouse\Services\WarehouseAuditLogService;

class CreateWarehouseAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $warehouse_id = app(WarehouseService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Warehouse: " . $data['name'],
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(WarehouseAuditLogService::class)->create($log_data, $warehouse_id);

            return $warehouse_id;
        });
    }
}