<?php

namespace App\Domains\InventoryAsset\Actions;

use App\Domains\InventoryAsset\Services\InventoryAssetJournalService;
use App\Domains\InventoryAsset\Services\InventoryAssetDepreciationScheduleService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateInventoryAssetDepreciationAction
{
    public function execute(array $data): int
    {
        $data= $data['data'];
        return DB::transaction(function () use ($data) {
            $updates = [];
            foreach ($data as $row) {
                $updates[] = [
                    'id' => $row->depreciation_id,
                    'asset_is_depreciation' => 1,
                ];
            }
            
            app(InventoryAssetDepreciationScheduleService::class)->update(
                $updates
            );
            $journal = app(InventoryAssetJournalService::class)->create(
                $data
            );
            return $journal; // Return the number of rows updated
        });
    }
}
