<?php

namespace App\Domains\Coa\Actions;

use App\Domains\Coa\Services\CoaAuditLogService;
use App\Domains\Coa\Services\CoaService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class UpdateCoaAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(CoaService::class)->findOne($id);

        return DB::transaction(function () use ($id, $data, $old_data) {
            app(CoaService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Coa',
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ]),
            ];
            app(CoaAuditLogService::class)->create($log_data, $id);
        });
    }
}
