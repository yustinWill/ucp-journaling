<?php
namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateSalesInvoiceAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sales_invoice_id = app(SalesInvoiceService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Sales Invoice",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(SalesInvoiceAuditLogService::class)->create($log_data, $sales_invoice_id);

            return $sales_invoice_id;
        });
    }
}