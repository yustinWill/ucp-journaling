<?php

namespace App\Domains\Product\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Product\Services\ProductService;
use App\Domains\Product\Services\ProductExpenseService;
use App\Domains\Product\Services\ProductCompositionService;
use App\Domains\Product\Services\ProductAuditLogService;


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
                    $product_data = app(ProductService::class)->findOne($row['product_id']);
                    $bundle_components[] = [
                        'f_product_id' => $product_id,
                        'f_product_composition_product_id' => $row['product_id'],
                        'qty' => $row['qty'],
                        'f_unit_id' => $product_data->f_unit_id,
                    ];
                }
                app(ProductCompositionService::class)->create($bundle_components);
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

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Product",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(ProductAuditLogService::class)->create($log_data, $product_id);
        });
    }
}
