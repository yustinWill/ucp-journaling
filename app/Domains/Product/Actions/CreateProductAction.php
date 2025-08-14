<?php

namespace App\Domains\Product\Actions;

use App\Domains\Product\Services\ProductService;
use App\Domains\Product\Services\ProductAuditLogService;
use Illuminate\Support\Facades\DB;
use App\Enums\ActionTypes;

class CreateProductAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['image']->store('products', 'public');
                $data['image'] = $path;
            }

            $id = app(ProductService::class)->create($data);

            $log_data = [
                'action_type' => ActionTypes::CREATE,
                'description' => "Pembuatan Product",
                'metadata' => json_encode([
                    'before' => null,
                    'after' => $data,
                ])
            ];
            app(ProductAuditLogService::class)->create($log_data, $id);
        });
    }
}
