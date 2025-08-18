<?php

namespace App\Domains\Product\Actions;

use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

use App\Domains\Product\Services\ProductService;
use App\Domains\Product\Services\ProductExpenseService;
use App\Domains\Product\Services\ProductCompositionService;
use App\Domains\Product\Services\ProductAuditLogService;
use App\Domains\Unit\Services\UnitService;
use App\Domains\Category\Services\CategoryService;

class UpdateProductAction
{
    public function execute(array $data)
    {
        $product_id = $data['id'];
        $old_data = app(ProductService::class)->findOne($product_id);

        $new_data_master['unit'] = app(UnitService::class)->findOne($data['unit_id']);
        $new_data_master['category'] = app(CategoryService::class)->findOne($data['category_id']);

        return DB::transaction(function () use ($product_id, $old_data, $new_data_master, $data) {

            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['image']->store('products', 'public');
                $data['image'] = $path;
            }

            app(ProductService::class)->update($product_id, $data);

            // if (isset($data['bundle_components'])) {
            //     $bundle_components = [];
            //     foreach ($data['bundle_components'] as $row) {
            //         $product_data = app(ProductService::class)->findOne($row['product_id']);
            //         $bundle_components[] = [
            //             'f_product_id' => $product_id,
            //             'f_product_composition_product_id' => $row['product_id'],
            //             'qty' => $row['qty'],
            //             'f_unit_id' => $product_data->f_unit_id,
            //         ];
            //     }
            //     app(ProductCompositionService::class)->create($bundle_components);
            // }

            // if (isset($data['bundle_accounts'])) {
            //     $bundle_accounts = [];
            //     foreach ($data['bundle_accounts'] as $row) {
            //         $bundle_accounts[] = [
            //             'f_product_id' => $product_id,
            //             'f_expense_coa_code' => $row['account'],
            //             'expense_price' => $row['cost'],
            //         ];
            //     }
            //     app(ProductExpenseService::class)->create($bundle_accounts);
            // }


            $this->insertAuditLog($product_id, $old_data, $data, $new_data_master);
        });
    }

    function insertAuditLog($product_id, $old_data, $new_data, $new_data_master)
    {
        $log_data = [
            'action_type' => ActionTypes::UPDATE,
            'description' => "Perubahan Kategori",
            'metadata' => json_encode([
                'before' => $old_data,
                'after' => $new_data,
                'comparison' => [
                    'name' => [
                        'label' => 'Nama Produk',
                        'before' => $old_data->name,
                        'after' => $new_data['name'],
                    ],
                    'unit' => [
                        'label' => 'Unit',
                        'before' => $old_data->unit_name,
                        'after' => $new_data_master['unit']->name,
                    ],
                    'status' => [
                        'label' => 'Status',
                        'before' => $old_data->status,
                        'after' => $new_data['status'],
                    ],
                    'category' => [
                        'label' => 'Kategori Produk',
                        'before' => $old_data->category_name,
                        'after' => $new_data_master['category']->name,
                    ],
                    'type' => [
                        'label' => 'Tipe Produk',
                        'before' => $old_data->type,
                        'after' => $new_data['type'] ?? null,
                    ],
                    'description' => [
                        'label' => 'Deskripsi',
                        'before' => $old_data->description,
                        'after' => $new_data['description'] ?? null,
                    ],
                ]
            ])
        ];
        app(ProductAuditLogService::class)->create($log_data, $product_id);
    }
}
