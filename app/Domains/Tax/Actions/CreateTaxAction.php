<?php

namespace App\Domains\Tax\Actions;

use App\Domains\Tax\Services\TaxService;
use App\Domains\Tax\Services\TaxAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateTaxAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(TaxService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Pajak",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(TaxAuditLogService::class)->create($log_data, $tag_id);
        });
    }
}