<?php

namespace App\Domains\SalesPerson\Actions;

use App\Domains\SalesPerson\Services\SalesPersonAuditLogService;
use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateSalesPersonAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $tag_id = app(SalesPersonService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan Sales Person',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(SalesPersonAuditLogService::class)->create($log_data, $tag_id);

            return $tag_id;
        });
    }
}
