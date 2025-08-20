<?php

namespace App\Domains\Warehouse\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Warehouse\Services\WarehouseService;
use App\Domains\Warehouse\Services\WarehouseAuditLogService;

class UpdateWarehouseAction
{
    public function execute(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Get original data for audit log
            $original_data = app(WarehouseService::class)->findOne($id);
            
            $result = app(WarehouseService::class)->update($id, $data);

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Storage: " . $data['name'],
                'metadata' => json_encode([
                    'before' => $original_data,
                    'after' => $data,
                ])
            ];
            app(WarehouseAuditLogService::class)->create($log_data, $id);

            return $result;
        });
    }
}