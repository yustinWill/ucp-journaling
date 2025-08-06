<?php

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Services\ContactService;
use App\Domains\Contact\Services\ContactAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateContactAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(ContactService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(ContactService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Kategori",
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
                    ]
                ])
            ];
            app(ContactAuditLogService::class)->create($log_data, $id);
        });
    }
}
