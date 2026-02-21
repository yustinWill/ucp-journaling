<?php

namespace App\Domains\ReturnManagement\Actions;

use App\Domains\ReturnManagement\Services\ReturnManagementAuditLogService;
use App\Domains\ReturnManagement\Services\ReturnManagementProductService;
use App\Domains\ReturnManagement\Services\ReturnManagementService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateReturnManagementAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            // Handle file attachment
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('return_management', 'public');
                $data['attachment'] = $path;
            }

            // Generate transaction code if not provided
            // if (empty($data['transaction_code'])) {
            //     $data['transaction_code'] = app(ReturnManagementService::class)
            //         ->generateTransactionNumber($data['return_type']);
            // }

            // Calculate totals from products
            if (!empty($data['products'])) {
                $subtotal = 0;
                $totalTax = 0;
                
                foreach ($data['products'] as &$product) {
                    // Calculate product total
                    $product['total_return_price'] = $product['return_qty'] * $product['return_unit_price'];
                    $subtotal += $product['total_return_price'];
                }
                
                $data['subtotal'] = $subtotal;
                $data['total_tax'] = $totalTax; // TODO: Implement tax calculation if needed
                $data['grand_total'] = $subtotal + $totalTax - ($data['total_discount'] ?? 0);
            }

            // Create return management record
            $returnId = app(ReturnManagementService::class)->create($data);

            // Create return product records
            if (!empty($data['products'])) {
                foreach ($data['products'] as $product) {
                    $productData = array_merge($product, [
                        'f_return_management_id' => $returnId
                    ]);

                    app(ReturnManagementProductService::class)->create($productData);
                }
            }

            // Create audit log
            app(ReturnManagementAuditLogService::class)->logCreate(
                $returnId,
                $data['transaction_code'],
                [
                    'return_type' => $data['return_type'],
                    'source_invoice_code' => $data['source_invoice_code'],
                    'total_products' => count($data['products'] ?? []),
                    'grand_total' => $data['grand_total']
                ]
            );

            // Auto-process stock movement for the return
            app(ProcessReturnStockAction::class)->execute($returnId);

            return $returnId;
        });
    }

    public function validateData(array $data): array
    {
        $errors = [];

        // Basic validation
        if (empty($data['return_type'])) {
            $errors['return_type'] = 'Return type is required';
        }

        if (empty($data['source_invoice_id'])) {
            $errors['source_invoice_id'] = 'Source invoice is required';
        }

        if (empty($data['return_date'])) {
            $errors['return_date'] = 'Return date is required';
        }

        if (empty($data['transaction_date'])) {
            $errors['transaction_date'] = 'Transaction date is required';
        }

        // Validate products
        if (empty($data['products']) || !is_array($data['products'])) {
            $errors['products'] = 'At least one product is required';
        } else {
            $productService = app(ReturnManagementProductService::class);
            $productErrors = $productService->validateReturnQuantities($data['products']);
            $errors = array_merge($errors, $productErrors);

            foreach ($data['products'] as $index => $product) {
                if (empty($product['f_product_id'])) {
                    $errors["products.{$index}.f_product_id"] = 'Product is required';
                }

                if (empty($product['return_qty']) || $product['return_qty'] <= 0) {
                    $errors["products.{$index}.return_qty"] = 'Return quantity must be greater than 0';
                }

                if (empty($product['return_unit_price']) || $product['return_unit_price'] < 0) {
                    $errors["products.{$index}.return_unit_price"] = 'Return unit price must be 0 or greater';
                }
            }
        }

        return $errors;
    }
}