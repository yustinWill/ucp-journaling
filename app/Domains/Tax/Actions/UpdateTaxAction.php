<?php

namespace App\Domains\Tax\Actions;

use App\Domains\Tax\Services\TaxService;
use App\Domains\Tax\Services\TaxAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateTaxAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(TaxService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(TaxService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Pajak",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(TaxAuditLogService::class)->create($log_data, $id);
        });
    }
}