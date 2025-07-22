<?php

namespace App\Domains\Tag\Actions;

use App\Domains\Tag\Services\TagService;
use App\Domains\Tag\Services\TagAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateTagAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(TagService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Tag",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(TagAuditLogService::class)->create($log_data, $tag_id);
        });
    }
}
