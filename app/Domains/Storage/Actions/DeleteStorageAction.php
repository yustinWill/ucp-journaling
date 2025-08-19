<?php

namespace App\Domains\Storage\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Storage\Services\StorageService;
use App\Domains\Storage\Services\StorageAuditLogService;

class DeleteStorageAction
{
    public function execute(int $id)
    {
        return DB::transaction(function () use ($id) {
            // Get original data for audit log
            $original_data = app(StorageService::class)->findOne($id);
            
            // Soft delete by updating status to INACTIVE
            $result = app(StorageService::class)->update($id, [
                'name' => $original_data->name,
                'pic_users' => json_decode($original_data->f_pic_ids ?? '[]'),
                'province' => $original_data->province,
                'city' => $original_data->city,
                'district' => $original_data->district,
                'sub_district' => $original_data->sub_district,
                'address' => $original_data->address,
                'postal_code' => $original_data->postal_code,
                'tags' => json_decode($original_data->f_tag_codes ?? '[]'),
                'status' => 'INACTIVE'
            ]);

            $log_data = [
                'action_type' => ActionTypes::DELETE,
                'description' => "Penghapusan Storage: " . $original_data->name,
                'metadata' => json_encode([
                    'before' => $original_data,
                    'after' => null,
                ])
            ];
            app(StorageAuditLogService::class)->create($log_data, $id);

            return $result;
        });
    }
}