<?php

namespace App\Domains\Unit\Actions;

use App\Domains\Unit\Services\UnitService;
use App\Domains\Unit\Services\UnitAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateUnitAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(UnitService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Unit",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(UnitAuditLogService::class)->create($log_data, $tag_id);
        });
    }
}
