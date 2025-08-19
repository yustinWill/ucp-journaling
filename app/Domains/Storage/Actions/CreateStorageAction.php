<?php

namespace App\Domains\Storage\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Storage\Services\StorageService;
use App\Domains\Storage\Services\StorageAuditLogService;

class CreateStorageAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $storage_id = app(StorageService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Storage: " . $data['name'],
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(StorageAuditLogService::class)->create($log_data, $storage_id);

            return $storage_id;
        });
    }
}