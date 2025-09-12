<?php

namespace App\Domains\StockOpname\Actions;

use App\Domains\StockOpname\Services\StockOpnameAuditLogService;
use App\Domains\StockOpname\Services\StockOpnameProductService;
use App\Domains\StockOpname\Services\StockOpnameService;
use Illuminate\Support\Facades\DB;

class CreateStockOpnameAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {
            // Create main stock opname record
            $stockOpnameId = app(StockOpnameService::class)->create([
                'stock_opname_no' => $data['opname_number'],
                'transaction_date' => $data['opname_date'],
                'f_warehouse_id' => $data['warehouse'],
                'f_pic_id' => $data['pic'],
                'status' => 'SUBMITTED',
                'description' => $data['description'] ?? null,
                'memo' => $data['memo'] ?? null,
                'f_tag_codes' => $data['tags'] ?? [],
                'count_product' => count($data['products']),
                'count_variance' => 0,
                'total_system_stock' => array_sum(array_column($data['products'], 'system_stock')),
                'total_physical_stock' => array_sum(array_column($data['products'], 'physical_qty')),
                'total_variance' => array_sum(array_column($data['products'], 'variance')),
            ]);

            // Create adjustment items and stock movement logs
            $items = [];
            foreach ($data['products'] as $product) {
                $items[] = [
                    'f_stock_opname_id' => $stockOpnameId,
                    'f_product_id' => $product['product_id'],
                    'system_stock' => $product['system_stock'],
                    'physical_qty' => $product['physical_qty'],
                    'variance' => $product['variance'],
                    'note' => $product['note'],
                ];
            }
            if (! empty($items)) {
                app(StockOpnameProductService::class)->createMany($items);
            }

            app(StockOpnameAuditLogService::class)->create(
                $stockOpnameId,
                'CREATE',
                'Stock opname created',
                $data
            );

            return $stockOpnameId;
        });
    }
}
