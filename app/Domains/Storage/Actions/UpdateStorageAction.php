<?php

namespace App\Domains\Storage\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Storage\Services\StorageService;
use App\Domains\Storage\Services\StorageAuditLogService;

class UpdateStorageAction
{
    public function execute(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Get original data for audit log
            $original_data = app(StorageService::class)->findOne($id);
            
            $result = app(StorageService::class)->update($id, $data);

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Storage: " . $data['name'],
                'metadata' => json_encode([
                    'before' => $original_data,
                    'after' => $data,
                ])
            ];
            app(StorageAuditLogService::class)->create($log_data, $id);

            return $result;
        });
    }
}