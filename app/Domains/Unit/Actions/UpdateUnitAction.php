<?php

namespace App\Domains\Unit\Actions;

use App\Domains\Unit\Services\UnitService;
use App\Domains\Unit\Services\UnitAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateUnitAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(UnitService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(UnitService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Unit",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(UnitAuditLogService::class)->create($log_data, $id);
        });
    }
}
