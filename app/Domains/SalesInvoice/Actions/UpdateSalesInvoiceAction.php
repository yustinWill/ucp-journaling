<?php

namespace App\Domains\SalesInvoice\Actions;

use App\Domains\SalesInvoice\Services\SalesInvoiceAuditLogService;
use App\Domains\SalesInvoice\Services\SalesInvoiceService;
use Illuminate\Support\Facades\DB;

class UpdateSalesInvoiceAction
{
    public function validateUpdateData(array $data): array
    {
        $errors = [];

        // Basic validation
        if (empty($data['invoice_date'])) {
            $errors[] = 'Tanggal invoice wajib diisi';
        }

        if (empty($data['transaction_date'])) {
            $errors[] = 'Tanggal transaksi wajib diisi';
        }

        if (empty($data['due_date'])) {
            $errors[] = 'Tanggal jatuh tempo wajib diisi';
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    public function execute(int $invoiceId, array $data)
    {
        return DB::transaction(function () use ($invoiceId, $data) {
            // Get current data for audit logging
            $beforeData = app(SalesInvoiceService::class)->findOne($invoiceId);

            // Handle file upload
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('sales_invoice', 'public');
                $data['attachment'] = $path;
            }

            // Update sales invoice record
            $result = app(SalesInvoiceService::class)->update($invoiceId, $data);

            // Get updated data
            $afterData = app(SalesInvoiceService::class)->findOne($invoiceId);

            // Identify changed fields
            $changedFields = [];
            $fieldsToCheck = ['invoice_date', 'transaction_date', 'due_date', 'description', 'internal_memo'];

            foreach ($fieldsToCheck as $field) {
                if (isset($beforeData->$field) && isset($afterData->$field) && $beforeData->$field !== $afterData->$field) {
                    $changedFields[$field] = [
                        'before' => $beforeData->$field,
                        'after' => $afterData->$field,
                    ];
                }
            }

            // Create audit log
            app(SalesInvoiceAuditLogService::class)->create($invoiceId, 'UPDATE', 'Sales Invoice updated', [
                'changed_fields' => $changedFields,
                'before' => $beforeData,
                'after' => $afterData,
            ]);

            return $result;
        });
    }
}
