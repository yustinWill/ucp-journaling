<?php

namespace App\Domains\Category\Actions;

use App\Domains\Category\Services\CategoryService;
use App\Domains\Category\Services\CategoryAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateCategoryAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(CategoryService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(CategoryService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Kategori",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(CategoryAuditLogService::class)->create($log_data, $id);
        });
    }
}