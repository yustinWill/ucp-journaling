<?php
namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class UpdateSalesInvoiceAction
{
    public function execute(int $id, array $data)
    {
        $old_data = app(SalesInvoiceService::class)->findOne($id);
        
        return DB::transaction(function () use ($id, $data, $old_data) {
            app(SalesInvoiceService::class)->update($id, $data);
            
            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => "Perubahan Sales Invoice",
                'metadata' => json_encode([
                    'before' => $old_data,
                    'after' => $data,
                ])
            ];
            app(SalesInvoiceAuditLogService::class)->create($log_data, $id);
        });
    }
}