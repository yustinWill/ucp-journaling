<?php

namespace App\Domains\Product\Actions;

use App\Domains\Product\Services\ProductAuditLogService;
use App\Domains\Product\Services\ProductCompositionService;
use App\Domains\Product\Services\ProductCostCompositionService;
use App\Domains\Product\Services\ProductExpenseService;
use App\Domains\Product\Services\ProductService;
use App\Domains\Product\Services\ProductUnitService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['image']->store('products', 'public');
                $data['image'] = $path;
            }
            $product_id = app(ProductService::class)->create($data);

            if (isset($data['bundle_components'])) {
                $bundle_components = [];
                foreach ($data['bundle_components'] as $row) {
                    // Skip if row doesn't have required fields
                    if (!isset($row['product_id']) || !isset($row['unit_id']) || !isset($row['qty'])) {
                        continue;
                    }

                    $bundle_components[] = [
                        'f_product_id' => $product_id,
                        'f_product_composition_product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'f_unit_id' => $row['unit_id'],
                    ];
                }

                if (!empty($bundle_components)) {
                    app(ProductCompositionService::class)->create($bundle_components);
                }
            }

            if (isset($data['cost'])) {
                $cost_components = [];
                foreach ($data['cost'] as $row) {
                    $cost_components[] = [
                        'f_product_id' => $product_id,
                        'account_code' => $row['coa'],
                        'cost_value' => $row['value'],
                    ];
                }
                app(ProductCostCompositionService::class)->create($cost_components);
            }

            if (isset($data['bundle_accounts'])) {
                $bundle_accounts = [];
                foreach ($data['bundle_accounts'] as $row) {
                    $bundle_accounts[] = [
                        'f_product_id' => $product_id,
                        'f_expense_coa_code' => $row['account'],
                        'expense_price' => $row['cost'],
                    ];
                }
                app(ProductExpenseService::class)->create($bundle_accounts);
            }

            if (isset($data['product_units']) && !empty($data['product_units'])) {
                $product_units = [];
                foreach ($data['product_units'] as $row) {
                    // Skip if row doesn't have unit_id
                    if (!isset($row['unit_id']) || empty($row['unit_id'])) {
                        continue;
                    }

                    $product_units[] = [
                        'unit_id' => $row['unit_id'],
                        'is_default' => $row['is_default'] ?? 0,
                        'conversion_factor' => $row['conversion_factor'] ?? 1,
                        'status' => $row['status'] ?? 'ACTIVE',
                        'note' => $row['note'] ?? '',
                    ];
                }

                if (!empty($product_units)) {
                    app(ProductUnitService::class)->createMultiple($product_id, $product_units);
                }
            }

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => 'Pembuatan Product',
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ]),
            ];
            app(ProductAuditLogService::class)->create($log_data, $product_id);
        });
    }
}
