<?php

namespace App\Domains\SalesPerson\Actions;

use App\Domains\SalesPerson\Services\SalesPersonService;
use App\Domains\SalesPerson\Services\SalesPersonAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateSalesPersonAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(SalesPersonService::class)->findOne($id);
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(SalesPersonService::class)->update($id, $data);
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Sales Person",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(SalesPersonAuditLogService::class)->create($log_data, $id);
            
            return true;
        });
    }
}