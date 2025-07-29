<?php

namespace App\Domains\Tag\Actions;

use App\Domains\Tag\Services\TagService;
use App\Domains\Tag\Services\TagColorService;
use App\Domains\Tag\Services\TagAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateTagAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(TagService::class)->findOne($id);

        $before_color = app(TagColorService::class)->findOne($old_data->tag_color_id);
        $after_color = app(TagColorService::class)->findOne($data['f_tag_color_id']);

        return DB::transaction(function () use ($id, $data, $old_data, $before_color, $after_color) {
            app(TagService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Tag",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'code' => [
                            'label' => 'Kode Tag',
                            'before' => $old_data->code,
                            'after' => $data['code'],
                        ],
                        'name' => [
                            'label' => 'Nama Tag',
                            'before' => $old_data->name,
                            'after' => $data['name'],
                        ],
                        'type' => [
                            'label' => 'Warna Badge',
                            'before' => $old_data->tag_color_id . " - " . $before_color->name,
                            'after' => $data['f_tag_color_id'] . " - " . $after_color->name,
                        ],
                        'status' => [
                            'label' => 'Status',
                            'before' => $old_data->status,
                            'after' => $data['status'],
                        ],
                        'description' => [
                            'label' => 'Deskripsi',
                            'before' => $old_data->description,
                            'after' => $data['description'] ?? null,
                        ],
                        'memo' => [
                            'label' => 'Memo',
                            'before' => $old_data->memo,
                            'after' => $data['memo'] ?? null,
                        ],
                    ]
                ])
            ];
            app(TagAuditLogService::class)->create($log_data, $id);
        });
    }
}
