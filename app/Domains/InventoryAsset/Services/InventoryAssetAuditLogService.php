<?php

namespace App\Domains\InventoryAsset\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryAssetAuditLogService
{
    private $table_name = 'm_inventory_asset_audit_logs';

    public function create($inventoryAssetId, $actionType, $description, $metadata = [])
    {
        return DB::table($this->table_name)->insertGetId([
            'f_inventory_asset_id' => $inventoryAssetId,
            'action_type' => $actionType,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);
    }

    public function findAllBasedOnId($inventoryAssetId)
    {
        return DB::table($this->table_name.' as audit')
            ->select([
                'audit.*',
                'users.name as user_name',
                'users.id as user_id'
            ])
            ->join('users', 'audit.created_by', '=', 'users.id')
            ->where('audit.f_inventory_asset_id', $inventoryAssetId)
            ->orderBy('audit.created_at', 'desc')
            ->get();
    }
}
