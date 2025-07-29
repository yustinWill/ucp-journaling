<?php

namespace App\Domains\Unit\Actions;

use App\Domains\Unit\Services\UnitService;
use App\Domains\Unit\Services\UnitTypeService;
use App\Domains\Unit\Services\UnitAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateUnitAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(UnitService::class)->findOne($id);

        $before_unit = app(UnitTypeService::class)->findOne($old_data->unit_type_id);
        $after_unit = app(UnitTypeService::class)->findOne($data['type']);

        return DB::transaction(function () use ($id, $data, $old_data, $before_unit, $after_unit) {
            app(UnitService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Unit",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                    'comparison' => [
                        'code' => [
                            'label' => 'Kode Unit',
                            'before' => $old_data->code,
                            'after' => $data['code'],
                        ],
                        'name' => [
                            'label' => 'Nama Unit',
                            'before' => $old_data->name,
                            'after' => $data['name'],
                        ],
                        'type' => [
                            'label' => 'Tipe Unit',
                            'before' => $old_data->unit_type_id . " - " . $before_unit->name,
                            'after' => $data['type'] . " - " . $after_unit->name,
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
            app(UnitAuditLogService::class)->create($log_data, $id);
        });
    }
}
