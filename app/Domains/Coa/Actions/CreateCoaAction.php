<?php

namespace App\Domains\Coa\Actions;

use App\Domains\Coa\Services\CoaService;
use App\Domains\Coa\Services\CoaAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateCoaAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(CoaService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Coa",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(CoaAuditLogService::class)->create($log_data, $tag_id);
        });
    }
}
