<?php

namespace App\Domains\Category\Actions;

use App\Domains\Category\Services\CategoryService;
use App\Domains\Category\Services\CategoryAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateCategoryAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(CategoryService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Kategori",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(CategoryAuditLogService::class)->create($log_data, $tag_id);
        });
    }
}