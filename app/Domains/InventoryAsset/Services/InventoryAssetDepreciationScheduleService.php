<?php

namespace App\Domains\InventoryAsset\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAssetDepreciationScheduleService
{
    private $table_name = 'm_inventory_assets_depreciation_schedule';

    public function findLeftOverBasedOnId($inventoryAssetId)
    {
        return $result = DB::table('m_inventory_assets_depreciation_schedule')
             ->selectRaw('
                COUNT(CASE WHEN asset_is_depreciation IS NULL THEN 1 END) AS total_left_over,
                SUM(CASE WHEN asset_is_depreciation IS NULL THEN asset_depreciation_value ELSE 0 END) AS total_left_over_value,
                SUM(CASE WHEN asset_is_depreciation IS NOT NULL THEN asset_depreciation_value ELSE 0 END) AS total_depreciated_value
            ')
            ->where('asset_id', $inventoryAssetId)
            ->first();
    }

    public function update(array $data)
    {
        foreach ($data as $update) {
            DB::table('m_inventory_assets_depreciation_schedule')
                ->where('id', $update['id'])
                ->update([
                    'asset_is_depreciation' => 1,
                ]);
        }
        return true;
    }
}
