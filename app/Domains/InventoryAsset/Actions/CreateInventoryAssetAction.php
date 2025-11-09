<?php

namespace App\Domains\InventoryAsset\Actions;

use App\Domains\InventoryAsset\Services\InventoryAssetAuditLogService;
use App\Domains\InventoryAsset\Services\InventoryAssetService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateInventoryAssetAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {
            $inventoryAssetId = app(InventoryAssetService::class)->create($data);
            $inventorySchedule = app(InventoryAssetService::class)->createSchedule($inventoryAssetId, $data);
            // Create audit log
            app(InventoryAssetAuditLogService::class)->create(
                $inventoryAssetId,
                ActionTypes::CREATE,
                'Inventory/Asset created',
                ['data' => $data]
            );

            return $inventoryAssetId;
        });
    }
}
