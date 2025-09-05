<?php

namespace App\Domains\Category\Actions;

use App\Domains\Category\Services\CategoryAuditLogService;
use App\Domains\Category\Services\CategoryService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class UpdateCategoryAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(CategoryService::class)->findOne($id);

        return DB::transaction(function () use ($id, $data, $old_data) {
            app(CategoryService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Kategori',
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'code' => [
                            'label' => 'Kode Kategori',
                            'before' => $old_data->code,
                            'after' => $data['code'],
                        ],
                        'name' => [
                            'label' => 'Nama Kategori',
                            'before' => $old_data->name,
                            'after' => $data['name'],
                        ],
                        'type' => [
                            'label' => 'Tipe Kategori',
                            'before' => $old_data->type,
                            'after' => $data['type'],
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
                    ],
                ]),
            ];
            app(CategoryAuditLogService::class)->create($log_data, $id);
        });
    }
}
