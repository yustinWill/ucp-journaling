<?php

namespace App\Domains\PurchaseOrder\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderAuditLogService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderProductService;
use App\Domains\PurchaseOrder\Services\PurchaseOrderService;
use App\Enums\ActionTypes;
use Illuminate\Support\Facades\DB;

class UpdatePurchaseOrderAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {

            $po_id = $data['id'];

            $old_po_data = app(PurchaseOrderService::class)->findOne($po_id);
            $old_po_products = app(PurchaseOrderProductService::class)->findAllBasedOnPOId($po_id);

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('purchase_order', 'public');
                $data['attachment'] = $path;
            }

            app(PurchaseOrderService::class)->update($po_id, $data);

            foreach ($data['products'] as $product) {
                $unit_data = app(ProductService::class)->findOne($product['product_id']);

                $productData = [
                    'product_id' => $product['product_id'],
                    'qty' => $product['quantity'],
                    'unit_id' => $unit_data->f_unit_id,
                    'price_per_qty' => $product['unit_price'],
                    'tax_id' => $product['tax_id'],
                    'total' => $product['total'],
                ];

                if (! empty($product['id'])) {
                    app(PurchaseOrderProductService::class)->update($po_id, $product['id'], $productData);
                    $existingProductIds[] = $product['id'];
                } else {
                    $newId = app(PurchaseOrderProductService::class)->create($po_id, $productData);
                    $existingProductIds[] = $newId;
                }
            }

            // Delete products that are no longer present

            $oldProductIds = collect($old_po_products)->pluck('id')->toArray();
            // dd($oldProductIds);
            $toDelete = array_diff($oldProductIds, $existingProductIds);

            foreach ($toDelete as $deleteId) {
                app(PurchaseOrderProductService::class)->delete($po_id, $deleteId);
            }

            $log_data = [
                'action_type' => ActionTypes::UPDATE,
                'description' => 'Perubahan Purchase order',
                'metadata' => json_encode([
                    'before' => [
                        'header' => $old_po_data,
                        'products' => $old_po_products,
                    ],
                    'after' => $data,
                    'comparison' => [
                        'transaction_date' => [
                            'label' => 'Tanggal Transaksi',
                            'before' => $old_po_data->transaction_date,
                            'after' => $data['date'],
                        ],
                        'transaction_no' => [
                            'label' => 'Nomor Transaksi',
                            'before' => $old_po_data->transaction_code,
                            'after' => $data['reference_no'],
                        ],
                        'supplier' => [
                            'label' => 'Supplier',
                            'before' => $old_po_data->f_supplier_id,
                            'after' => $data['supplier_id'],
                        ],
                        'email' => [
                            'label' => 'Email',
                            'before' => $old_po_data->supplier_email,
                            'after' => $data['email'] ?? null,
                        ],
                        'address' => [
                            'label' => 'Alamat',
                            'before' => $old_po_data->supplier_address,
                            'after' => $data['address'] ?? null,
                        ],
                        'supplier_reference' => [
                            'label' => 'Nomor Referensi Supplier',
                            'before' => $old_po_data->supplier_reference_no,
                            'after' => $data['supplier_reference'] ?? null,
                        ],
                        'term_of_payment' => [
                            'label' => 'Syarat Pembayaran',
                            'before' => $old_po_data->f_payment_term_id,
                            'after' => $data['payment_terms'] ?? null,
                        ],
                        'warehouse' => [
                            'label' => 'Gudang',
                            'before' => $old_po_data->f_warehouse_id,
                            'after' => $data['warehouse_id'] ?? null,
                        ],
                        'description' => [
                            'label' => 'Keterangan',
                            'before' => $old_po_data->description,
                            'after' => $data['description'] ?? null,
                        ],
                    ],
                ]),
            ];
            app(PurchaseOrderAuditLogService::class)->create($log_data, $po_id);
        });
    }
}
