<?php

namespace App\Domains\Product\Actions;

use App\Domains\Category\Services\CategoryService;
use App\Domains\Product\Services\ProductAuditLogService;
use App\Domains\Product\Services\ProductCompositionService;
use App\Domains\Product\Services\ProductExpenseService;
use App\Domains\Product\Services\ProductService;
use App\Domains\Unit\Services\UnitService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

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

            $current_composition = app(ProductCompositionService::class)->findAllBasedOnProductId($product_id);

            $current_composition_ids = [];
            foreach ($current_composition as $row) {
                $current_composition_ids[] = $row->id;
            }

            if (isset($data['bundle_components'])) {
                $submitted_composition_ids = [];
                foreach ($data['bundle_components'] as $composition) {
                    $product_data = app(ProductService::class)->findOne($composition['product_id']);
                    if (isset($composition['composition_id'])) {

                        app(ProductCompositionService::class)->update($composition['composition_id'], [
                            'f_product_composition_product_id' => $composition['product_id'],
                            'qty' => $composition['qty'],
                            'f_unit_id' => $product_data->f_unit_id,
                        ]);

                        $submitted_composition_ids[] = $composition['composition_id'];
                    } else {
                        app(ProductCompositionService::class)->create([
                            [
                                'f_product_id' => $product_id,
                                'f_product_composition_product_id' => $composition['product_id'],
                                'qty' => $composition['qty'],
                                'f_unit_id' => $product_data->f_unit_id,
                            ],
                        ]);
                    }
                }

                $composition_ids_to_delete = array_diff($current_composition_ids, $submitted_composition_ids);
                foreach ($composition_ids_to_delete as $delete_id) {
                    app(ProductCompositionService::class)->delete($product_id, $delete_id);
                }
            }

            $current_account = app(ProductExpenseService::class)->findAllBasedOnProductId($product_id);

            $current_account_ids = [];
            foreach ($current_account as $row) {
                $current_account_ids[] = $row->id;
            }

            if (isset($data['bundle_accounts'])) {
                $submitted_account_ids = [];
                foreach ($data['bundle_accounts'] as $account) {
                    if (isset($account['expense_id'])) {
                        app(ProductExpenseService::class)->update($account['expense_id'], [
                            'f_expense_coa_code' => $account['account'],
                            'expense_price' => $account['cost'],
                        ]);

                        $submitted_account_ids[] = $account['expense_id'];
                    } else {
                        app(ProductExpenseService::class)->create([
                            [
                                'f_product_id' => $product_id,
                                'f_expense_coa_code' => $account['account'],
                                'expense_price' => $account['cost'],
                            ],
                        ]);
                    }
                }

                $account_ids_to_delete = array_diff($current_account_ids, $submitted_account_ids);
                foreach ($account_ids_to_delete as $delete_id) {
                    app(ProductExpenseService::class)->delete($product_id, $delete_id);
                }
            }

            $this->insertAuditLog($product_id, $old_data, $data, $new_data_master);
        });
    }

    public function insertAuditLog($product_id, $old_data, $new_data, $new_data_master)
    {
        $log_data = [
            'action_type' => ActionTypes::UPDATE,
            'description' => 'Perubahan Kategori',
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
                ],
            ]),
        ];
        app(ProductAuditLogService::class)->create($log_data, $product_id);
    }
}
