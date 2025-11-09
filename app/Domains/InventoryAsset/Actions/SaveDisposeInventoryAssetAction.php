<?php

namespace App\Domains\InventoryAsset\Actions;

use App\Domains\InventoryAsset\Services\InventoryAssetAuditLogService;
use App\Domains\InventoryAsset\Services\InventoryAssetService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class SaveDisposeInventoryAssetAction
{
    public function execute($id, array $data): int
    {
        return DB::transaction(function () use ($id, $data) {
            $before = app(InventoryAssetService::class)->findOne($id);

            $result = app(InventoryAssetService::class)->updateDispose($id, $data);

            // Create audit log
            app(InventoryAssetAuditLogService::class)->create(
                $id,
                ActionTypes::UPDATE,
                'Inventory/Asset disposed',
                ['before' => $before, 'after' => $data]
            );

            return $result;
        });
    }
}
