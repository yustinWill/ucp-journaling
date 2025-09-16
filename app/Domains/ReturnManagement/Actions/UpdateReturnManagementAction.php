<?php

namespace App\Domains\ReturnManagement\Actions;

use App\Domains\ReturnManagement\Services\ReturnManagementAuditLogService;
use App\Domains\ReturnManagement\Services\ReturnManagementProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateReturnManagementAction
{
    public function execute(int $returnId, array $data)
    {
        return DB::transaction(function () use ($returnId, $data) {
            
            // Get current return data for comparison
            $currentReturn = app(ReturnManagementService::class)->findById($returnId);
            if (!$currentReturn) {
                throw new \Exception('Return not found');
            }

            $changes = [];

            // Handle file attachment
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old attachment if exists
                if ($currentReturn->attachment && Storage::disk('public')->exists($currentReturn->attachment)) {
                    Storage::disk('public')->delete($currentReturn->attachment);
                }
                
                $path = $data['attachment']->store('return_management', 'public');
                $data['attachment'] = $path;
                
                if ($currentReturn->attachment !== $path) {
                    $changes['attachment'] = [
                        'old' => $currentReturn->attachment,
                        'new' => $path
                    ];
                }
            }

            // Track important field changes
            $trackableFields = [
                'return_date', 'transaction_date', 'return_reason', 'return_reason_description',
                'description', 'internal_memo', 'status'
            ];

            foreach ($trackableFields as $field) {
                if (isset($data[$field]) && $currentReturn->{$field} !== $data[$field]) {
                    $changes[$field] = [
                        'old' => $currentReturn->{$field},
                        'new' => $data[$field]
                    ];
                }
            }

            // Update products if provided
            if (isset($data['products']) && is_array($data['products'])) {
                // Calculate new totals
                $subtotal = 0;
                $totalTax = 0;
                
                foreach ($data['products'] as &$product) {
                    $product['total_return_price'] = $product['return_qty'] * $product['return_unit_price'];
                    $subtotal += $product['total_return_price'];
                }
                
                $data['subtotal'] = $subtotal;
                $data['total_tax'] = $totalTax;
                $data['grand_total'] = $subtotal + $totalTax - ($data['total_discount'] ?? 0);

                // Track total changes
                if ($currentReturn->grand_total != $data['grand_total']) {
                    $changes['grand_total'] = [
                        'old' => $currentReturn->grand_total,
                        'new' => $data['grand_total']
                    ];
                }

                // Delete existing products and recreate (simpler approach)
                app(ReturnManagementProductService::class)->deleteByReturnId($returnId);
                
                foreach ($data['products'] as $product) {
                    $productData = array_merge($product, [
                        'f_return_management_id' => $returnId
                    ]);
                    
                    app(ReturnManagementProductService::class)->create($productData);
                }

                $changes['products'] = [
                    'old' => 'Products updated',
                    'new' => count($data['products']) . ' products'
                ];
            }

            // Update main return record
            $updateResult = app(ReturnManagementService::class)->update($returnId, $data);

            // Create audit log if there were changes
            if (!empty($changes)) {
                app(ReturnManagementAuditLogService::class)->logUpdate(
                    $returnId,
                    $currentReturn->transaction_code,
                    $changes,
                    [
                        'updated_fields' => array_keys($changes),
                        'update_type' => isset($data['products']) ? 'full_update' : 'basic_update'
                    ]
                );
            }

            return $updateResult;
        });
    }

    public function updateStatus(int $returnId, string $status, array $additionalData = [])
    {
        return DB::transaction(function () use ($returnId, $status, $additionalData) {
            
            $currentReturn = app(ReturnManagementService::class)->findById($returnId);
            if (!$currentReturn) {
                throw new \Exception('Return not found');
            }

            // Update status
            $result = app(ReturnManagementService::class)->updateStatus($returnId, $status, $additionalData);

            // Log the status change
            switch ($status) {
                case 'APPROVED':
                    app(ReturnManagementAuditLogService::class)->logApprove(
                        $returnId,
                        $currentReturn->transaction_code,
                        $additionalData['approval_notes'] ?? '',
                        $additionalData
                    );
                    break;

                case 'REJECTED':
                    app(ReturnManagementAuditLogService::class)->logReject(
                        $returnId,
                        $currentReturn->transaction_code,
                        $additionalData['rejection_reason'] ?? '',
                        $additionalData
                    );
                    break;

                case 'CANCELLED':
                    app(ReturnManagementAuditLogService::class)->logCancel(
                        $returnId,
                        $currentReturn->transaction_code,
                        $additionalData['cancellation_reason'] ?? '',
                        $additionalData
                    );
                    break;

                case 'PROCESSED':
                    app(ReturnManagementAuditLogService::class)->logProcess(
                        $returnId,
                        $currentReturn->transaction_code,
                        $additionalData['processing_status'] ?? 'PARTIAL',
                        $additionalData
                    );
                    break;

                default:
                    // Generic status change log
                    app(ReturnManagementAuditLogService::class)->logUpdate(
                        $returnId,
                        $currentReturn->transaction_code,
                        ['status' => ['old' => $currentReturn->status, 'new' => $status]],
                        $additionalData
                    );
                    break;
            }

            return $result;
        });
    }

    public function validateData(array $data): array
    {
        $errors = [];

        // Basic validation for updateable fields
        if (isset($data['return_date']) && empty($data['return_date'])) {
            $errors['return_date'] = 'Return date cannot be empty';
        }

        if (isset($data['transaction_date']) && empty($data['transaction_date'])) {
            $errors['transaction_date'] = 'Transaction date cannot be empty';
        }

        // Validate products if they are being updated
        if (isset($data['products'])) {
            if (!is_array($data['products']) || empty($data['products'])) {
                $errors['products'] = 'At least one product is required';
            } else {
                $productService = app(ReturnManagementProductService::class);
                $productErrors = $productService->validateReturnQuantities($data['products']);
                $errors = array_merge($errors, $productErrors);

                foreach ($data['products'] as $index => $product) {
                    if (empty($product['f_product_id'])) {
                        $errors["products.{$index}.f_product_id"] = 'Product is required';
                    }

                    if (isset($product['return_qty']) && ($product['return_qty'] <= 0)) {
                        $errors["products.{$index}.return_qty"] = 'Return quantity must be greater than 0';
                    }

                    if (isset($product['return_unit_price']) && ($product['return_unit_price'] < 0)) {
                        $errors["products.{$index}.return_unit_price"] = 'Return unit price must be 0 or greater';
                    }
                }
            }
        }

        return $errors;
    }
}