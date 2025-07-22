<?php

namespace App\Domains\Tag\Actions;

use App\Domains\Tag\Services\TagService;
use App\Domains\Tag\Services\TagAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateTagAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(TagService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(TagService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Tag",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(TagAuditLogService::class)->create($log_data, $id);
        });
    }
}
