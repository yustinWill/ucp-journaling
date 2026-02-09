<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Services\ProductionAuditLogService;
use App\Domains\Production\Services\ProductionItemService;
use App\Domains\Production\Services\ProductionService;
use App\Domains\Production\Services\ProductionStockService;
use App\Enums\ErrorCodes;
use App\Exceptions\AppException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreatePackingAction
{
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {

            // 1. Validate stock availability before creating packing
            $this->validateStockAvailability($data);

            // Handle file attachment
            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('packings', 'public');
                $data['attachment'] = $path;
            }

            $productionIds = [];
            $productionService = app(ProductionService::class);

            // Capture stock BEFORE packing operations for adjustment calculation
            $stockBeforePacking = 0;
            $latestMovementBeforePacking = DB::table('t_stock_movement_logs')
                ->where('product_id', $data['bundle_product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->where('company_code', session('user_company_code'))
                ->orderBy('id', 'DESC')
                ->first();
            $stockBeforePacking = $latestMovementBeforePacking ? $latestMovementBeforePacking->stock_after : 0;

            // 2. Generate shared transaction number for all packaging products in this packing
            $year = date('Y');
            $month = date('m');
            $transactionNumber = $data['production_number'] ?? "PACK-{$year}{$month}-" . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // 3. Create separate production record for each packaging product
            foreach ($data['packaging_products'] as $index => $packaging) {
                // Calculate HPP for this specific packaging product
                $totals = $this->calculateTotalsForSinglePackaging($data, $packaging);

                // Generate unique production number per line (shared base + index suffix)
                $productionNumber = $transactionNumber . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

                // Create production record with packaging product as bundle_product_id
                $productionData = [
                    'company_code' => session('user_company_code'),
                    'production_date' => $data['production_date'],
                    'production_number' => $productionNumber,
                    'bundle_product_id' => $packaging['packaging_product_id'], // THIS is the product being produced
                    'warehouse_id' => $data['warehouse_id'],
                    'pic_id' => $data['pic_id'],
                    'is_packaging' => true,
                    'status' => 'COMPLETED',
                    'quantity_planned' => $packaging['quantity_packed'],
                    'quantity_produced' => $packaging['quantity_packed'],
                    'total_raw_material_cost' => $totals['total_raw_material_cost'],
                    'total_production_expense' => 0,
                    'total_production_cost' => $totals['total_production_cost'],
                    'bundle_price' => $totals['bundle_price'],
                    'hpp_production' => $totals['hpp_production'],
                    'total_items_consumed' => $totals['total_items_consumed'],
                    'notes' => $data['notes'] ?? null,
                    'attachment' => $data['attachment'] ?? null,
                ];

                $productionId = $productionService->create($productionData);
                $productionIds[] = $productionId;

                // 3. Create production items (materials consumed: source bundle + other components)
                $this->createProductionItemsForSinglePackaging($productionId, $data['bundle_product_id'], $packaging);

                // 4. Process stock movements
                $this->processStockMovementsForSinglePackaging($productionId, $productionData, $data['bundle_product_id'], $packaging);

                // 5. Create t_packaging mapping record
                $this->createPackagingMapping($transactionNumber, $productionId, $data['bundle_product_id'], $packaging);

                // 6. Create audit log
                app(ProductionAuditLogService::class)->logCreation(
                    $productionId,
                    $productionData,
                    [$packaging],
                    []
                );
            }

            // 7. Create stock adjustment for remaining bundle product (if any)
            $this->createRemainingBundleAdjustment($data, $productionIds, $stockBeforePacking);

            // 8. Mark source production as packed
            if (!empty($data['source_production_id'])) {
                DB::table('t_productions')
                    ->where('id', $data['source_production_id'])
                    ->where('company_code', session('user_company_code'))
                    ->update(['is_packed' => true, 'updated_at' => now()]);
            }

            return $productionIds;
        });
    }

    private function validateStockAvailability(array $data): void
    {
        // Stock validation disabled for repacking
        // Allows negative stock for flexibility in packing adjustments
        return;

        // Previous validation code kept for reference but disabled:
        /*
        if (empty($data['packaging_products'])) {
            return;
        }

        $warehouseId = $data['warehouse_id'];
        $bundleProductId = $data['bundle_product_id'];

        // Calculate total bundle needed
        $totalBundleNeeded = 0;
        foreach ($data['packaging_products'] as $packaging) {
            $totalBundleNeeded += $packaging['bundle_qty_needed'];
        }

        // Check bundle stock availability
        $availableStock = DB::table('t_stock_movement_logs')
            ->where('product_id', $bundleProductId)
            ->where('warehouse_id', $warehouseId)
            ->select(
                DB::raw('SUM(
                    CASE
                        WHEN movement_type LIKE "%_IN" THEN movement_qty
                        WHEN movement_type LIKE "%_OUT" THEN -movement_qty
                        ELSE 0
                    END
                ) as total_stock')
            )
            ->value('total_stock');

        if ($availableStock < $totalBundleNeeded) {
            throw new AppException(
                ErrorCodes::INSUFFICIENT_STOCK,
                'Insufficient bundle stock. Available: ' . $availableStock . ', Needed: ' . $totalBundleNeeded
            );
        }

        // Validate component stock for each packaging product
        foreach ($data['packaging_products'] as $packaging) {
            if (!empty($packaging['other_components'])) {
                foreach ($packaging['other_components'] as $component) {
                    $componentStock = DB::table('t_stock_movement_logs')
                        ->where('product_id', $component['product_id'])
                        ->where('warehouse_id', $warehouseId)
                        ->select(
                            DB::raw('SUM(
                                CASE
                                    WHEN movement_type LIKE "%_IN" THEN movement_qty
                                    WHEN movement_type LIKE "%_OUT" THEN -movement_qty
                                    ELSE 0
                                END
                            ) as total_stock')
                        )
                        ->value('total_stock');

                    $neededQty = $component['qty_per_unit'] * $packaging['quantity_packed'];

                    if ($componentStock < $neededQty) {
                        throw new AppException(
                            ErrorCodes::INSUFFICIENT_STOCK,
                            'Insufficient stock for component: ' . $component['product_name'] .
                            '. Available: ' . $componentStock . ', Needed: ' . $neededQty
                        );
                    }
                }
            }
        }
        */
    }

    private function calculateTotalsForSinglePackaging(array $data, array $packaging): array
    {
        $sourceBundleProductId = $data['bundle_product_id']; // The bundle being consumed
        $bundleQtyNeeded = $packaging['bundle_qty_needed']; // Qty of source bundle needed
        $quantityPacked = $packaging['quantity_packed']; // Qty of packaging product being produced

        // 1. Calculate cost of source bundle consumed (FIFO)
        $bundleCost = 0;
        $batches = DB::table('t_product_stock_batches')
            ->where('f_product_id', $sourceBundleProductId)
            ->where('status', 'ACTIVE')
            ->where('remaining_qty', '>', 0)
            ->orderBy('batch_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $remainingNeed = $bundleQtyNeeded;
        foreach ($batches as $batch) {
            if ($remainingNeed <= 0) break;
            $qtyToTake = min($batch->remaining_qty, $remainingNeed);
            $hppPerUnit = $batch->estimated_hpp ?? $batch->purchase_price ?? 0;
            $bundleCost += ($qtyToTake * $hppPerUnit);
            $remainingNeed -= $qtyToTake;
        }

        // 2. Calculate cost of other components consumed (FIFO)
        $componentCost = 0;
        $componentCount = 0;
        if (!empty($packaging['other_components'])) {
            foreach ($packaging['other_components'] as $component) {
                $componentQtyNeeded = $component['qty_per_unit'] * $quantityPacked;

                $componentBatches = DB::table('t_product_stock_batches')
                    ->where('f_product_id', $component['product_id'])
                    ->where('status', 'ACTIVE')
                    ->where('remaining_qty', '>', 0)
                    ->orderBy('batch_date', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->get();

                $componentNeed = $componentQtyNeeded;
                foreach ($componentBatches as $batch) {
                    if ($componentNeed <= 0) break;
                    $qtyToTake = min($batch->remaining_qty, $componentNeed);
                    $hppPerUnit = $batch->estimated_hpp ?? $batch->purchase_price ?? 0;
                    $componentCost += ($qtyToTake * $hppPerUnit);
                    $componentNeed -= $qtyToTake;
                }
                $componentCount++;
            }
        }

        $totalCost = $bundleCost + $componentCost;
        $hppPerUnit = $quantityPacked > 0 ? ($totalCost / $quantityPacked) : 0;

        return [
            'total_raw_material_cost' => $totalCost,
            'total_production_expense' => 0,
            'total_production_cost' => $totalCost,
            'total_items_consumed' => 1 + $componentCount, // Source bundle + other components
            'bundle_price' => $bundleCost,
            'hpp_production' => $hppPerUnit,
        ];
    }

    private function createProductionItemsForSinglePackaging(int $productionId, int $sourceBundleProductId, array $packaging): void
    {
        $items = [];

        // 1. Add source bundle as consumed material
        $bundleUnitCode = DB::table('m_products as p')
            ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.id', $sourceBundleProductId)
            ->value('u.code');

        $items[] = [
            'production_id' => $productionId,
            'raw_material_product_id' => $sourceBundleProductId,
            'quantity_required' => $packaging['bundle_qty_needed'],
            'quantity_consumed' => $packaging['bundle_qty_needed'],
            'unit_code' => $bundleUnitCode ?? '',
            'unit_cost' => 0,
            'total_cost' => 0,
            'batch_consumption' => json_encode([]),
            'notes' => 'Source bundle consumed for packing',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // 2. Add other components as consumed materials
        if (!empty($packaging['other_components'])) {
            foreach ($packaging['other_components'] as $component) {
                $componentQtyNeeded = $component['qty_per_unit'] * $packaging['quantity_packed'];

                $componentUnitCode = DB::table('m_products as p')
                    ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
                    ->where('p.id', $component['product_id'])
                    ->value('u.code');

                $items[] = [
                    'production_id' => $productionId,
                    'raw_material_product_id' => $component['product_id'],
                    'quantity_required' => $componentQtyNeeded,
                    'quantity_consumed' => $componentQtyNeeded,
                    'unit_code' => $componentUnitCode ?? '',
                    'unit_cost' => 0,
                    'total_cost' => 0,
                    'batch_consumption' => json_encode([]),
                    'notes' => 'Component consumed for packing',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert all items
        if (!empty($items)) {
            DB::table('t_production_items')->insert($items);
        }
    }

    private function processStockMovementsForSinglePackaging(int $productionId, array $productionData, int $sourceBundleProductId, array $packaging): array
    {
        $stockService = app(ProductionStockService::class);

        // Prepare items as materials consumed (source bundle + other components)
        $items = [];

        // 1. Add source bundle as consumed material
        $items[] = [
            'raw_material_product_id' => $sourceBundleProductId,
            'quantity_consumed' => $packaging['bundle_qty_needed'],
            'quantity_required' => $packaging['bundle_qty_needed'],
        ];

        // 2. Add other components as consumed materials
        if (!empty($packaging['other_components'])) {
            foreach ($packaging['other_components'] as $component) {
                $componentQtyNeeded = $component['qty_per_unit'] * $packaging['quantity_packed'];

                $items[] = [
                    'raw_material_product_id' => $component['product_id'],
                    'quantity_consumed' => $componentQtyNeeded,
                    'quantity_required' => $componentQtyNeeded,
                ];
            }
        }

        // ProductionStockService will handle:
        // - Deducting consumed materials (source bundle + components)
        // - Adding produced packaging product stock
        return $stockService->createProductionStockMovements(
            $productionId,
            $items,
            $productionData
        );
    }

    private function createPackagingMapping(string $transactionnumber, int $productionId, int $sourceBundleProductId, array $packaging): void
    {
        $companyCode = session('user_company_code');

        DB::table('t_packaging')->insert([
            'company_code' => $companyCode,
            'production_id' => $productionId,
            'bundle_product_id' => $sourceBundleProductId, // The bundle that was consumed
            'packaging_product_id' => $packaging['packaging_product_id'], // The packaging product produced
            'quantity_packed' => $packaging['quantity_packed'],
            'transaction_number' => $transactionnumber,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function validatePackingData(array $data): array
    {
        $errors = [];

        // Required fields validation
        $requiredFields = [
            'production_date' => 'Production date is required',
            'production_number' => 'Production number is required',
            'source_production_id' => 'Source production is required',
            'bundle_product_id' => 'Bundle product is required',
            'warehouse_id' => 'Warehouse is required',
            'pic_id' => 'PIC is required',
        ];

        foreach ($requiredFields as $field => $message) {
            if (empty($data[$field])) {
                $errors[] = $message;
            }
        }

        // Validate packaging products
        if (empty($data['packaging_products']) || !is_array($data['packaging_products'])) {
            $errors[] = 'At least one packaging product is required';
        } else {
            // Check if at least one has qty > 0
            $hasValidQty = false;
            foreach ($data['packaging_products'] as $packaging) {
                if (isset($packaging['quantity_packed']) && $packaging['quantity_packed'] > 0) {
                    $hasValidQty = true;
                    break;
                }
            }
            if (!$hasValidQty) {
                $errors[] = 'At least one packaging product must have quantity > 0';
            }
        }

        // Validate unique production number
        // if (!empty($data['production_number'])) {
        //     $existingProduction = DB::table('t_productions')
        //         ->where('production_number', $data['production_number'])
        //         ->where('company_code', session('user_company_code'))
        //         ->first();

        //     if ($existingProduction) {
        //         $errors[] = 'Production number already exists: ' . $data['production_number'];
        //     }
        // }

        // Validate production date
        if (!empty($data['production_date'])) {
            $productionDate = \Carbon\Carbon::parse($data['production_date']);
            if ($productionDate->isFuture()) {
                $errors[] = 'Production date cannot be in the future';
            }
        }

        // Validate bundle product exists
        if (!empty($data['bundle_product_id'])) {
            $bundleProduct = DB::table('m_products')
                ->where('id', $data['bundle_product_id'])
                ->where('type', 'bundle')
                ->where('status', 'ACTIVE')
                ->first();

            if (!$bundleProduct) {
                $errors[] = 'Selected bundle product is not found, inactive, or not a bundle type';
            }
        }

        // Validate warehouse exists
        if (!empty($data['warehouse_id'])) {
            $warehouse = DB::table('m_warehouses')
                ->where('id', $data['warehouse_id'])
                ->where('status', 'ACTIVE')
                ->first();

            if (!$warehouse) {
                $errors[] = 'Selected warehouse is not found or inactive';
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    private function createRemainingBundleAdjustment(array $data, array $productionIds, float $stockBeforePacking): void
    {
        // "Sisa" = remaining qty from the production that wasn't consumed by packing
        $sisaInput = floatval($data['remaining_bundle_adjustment'] ?? 0);

        $companyCode = session('user_company_code');
        $warehouseId = $data['warehouse_id'];
        $bundleProductId = $data['bundle_product_id'];
        $adjustmentReason = $data['adjustment_reason'] ?? 'Stock adjustment after packing';

        // Get the production qty from the source production
        $productionQty = 0;
        if (!empty($data['source_production_id'])) {
            $productionQty = (float) DB::table('t_productions')
                ->where('id', $data['source_production_id'])
                ->value('quantity_produced') ?? 0;
        }

        // Get product and warehouse details
        $product = DB::table('m_products as p')
            ->join('m_units as u', 'p.f_unit_id', '=', 'u.id')
            ->where('p.id', $bundleProductId)
            ->select('p.code as product_code', 'p.name as product_name', 'u.code as unit_code', 'u.name as unit_name')
            ->first();

        $warehouse = DB::table('m_warehouses')
            ->where('id', $warehouseId)
            ->select('code as warehouse_code', 'name as warehouse_name')
            ->first();

        // Calculate desired final stock
        // Formula: stock_before_packing - production_qty + sisa_input
        // This is the stock level we want AFTER all adjustments
        $desiredFinalStock = $stockBeforePacking - $productionQty + $sisaInput;

        // Get current stock AFTER packing operations
        $latestMovement = DB::table('t_stock_movement_logs')
            ->where('product_id', $bundleProductId)
            ->where('warehouse_id', $warehouseId)
            ->where('company_code', $companyCode)
            ->orderBy('id', 'DESC')
            ->first();

        $stockAfterPacking = $latestMovement ? $latestMovement->stock_after : 0;

        // Calculate the actual adjustment needed: how much to add/remove to reach desired final stock
        $adjustmentNeeded = $desiredFinalStock - $stockAfterPacking;

        // If no adjustment needed, skip
        if ($adjustmentNeeded == 0) {
            return;
        }

        // Generate stock adjustment number
        $year = date('Y');
        $month = date('m');
        $adjustmentNumber = "ADJ-PACK-{$year}{$month}-" . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Positive = need to add stock, Negative = need to remove stock
        $movementType = $adjustmentNeeded > 0 ? 'ADJUSTMENT_IN' : 'ADJUSTMENT_OUT';
        $absoluteQty = abs($adjustmentNeeded);

        // Create stock movement log entry
        DB::table('t_stock_movement_logs')->insert([
            'company_code' => $companyCode,
            'movement_date' => $data['production_date'],
            'movement_type' => $movementType,
            'reference_type' => 'PACKING_ADJUSTMENT',
            'reference_number' => $adjustmentNumber,
            'warehouse_id' => $warehouseId,
            'warehouse_code' => $warehouse->warehouse_code ?? '',
            'warehouse_name' => $warehouse->warehouse_name ?? '',
            'product_id' => $bundleProductId,
            'product_code' => $product->product_code ?? '',
            'product_name' => $product->product_name ?? '',
            'unit' => $product->unit_name ?? '',
            'stock_before' => $stockAfterPacking,
            'movement_qty' => $absoluteQty,
            'stock_after' => $desiredFinalStock,
            'notes' => "Packing adjustment [sisa:{$sisaInput}] - {$adjustmentReason}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // If ADJUSTMENT_IN (shortage - need to add stock), create a stock batch
        if ($movementType === 'ADJUSTMENT_IN') {
            // Get latest HPP from existing batches for this product
            $latestBatch = DB::table('t_product_stock_batches')
                ->where('f_product_id', $bundleProductId)
                ->where('status', 'ACTIVE')
                ->orderBy('batch_date', 'DESC')
                ->first();

            $estimatedHpp = $latestBatch->estimated_hpp ?? $latestBatch->purchase_price ?? 0;

            DB::table('t_product_stock_batches')->insert([
                'company_code' => $companyCode,
                'f_product_id' => $bundleProductId,
                'batch_reference_code' => $adjustmentNumber,
                'source_type' => 'STOCK_ADJUSTMENT',
                'f_source_transaction_id' => $productionIds[0] ?? null,
                'batch_date' => $data['production_date'],
                'initial_qty' => $absoluteQty,
                'remaining_qty' => $absoluteQty,
                'raw_material_cost' => 0,
                'production_cost' => 0,
                'purchase_price' => 0,
                'estimated_hpp' => $estimatedHpp,
                'hpp_status' => 'UNSETTLED',
                'status' => 'ACTIVE',
                'created_at' => now(),
                'created_by' => Auth::id(),
                'updated_at' => now(),
            ]);
        }
        // If ADJUSTMENT_OUT (excess - need to remove stock), deduct from existing batches using FIFO
        else {
            $remainingToDeduct = $absoluteQty;
            $batches = DB::table('t_product_stock_batches')
                ->where('f_product_id', $bundleProductId)
                ->where('status', 'ACTIVE')
                ->where('remaining_qty', '>', 0)
                ->orderBy('batch_date', 'ASC')
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($batches as $batch) {
                if ($remainingToDeduct <= 0) break;

                $qtyToDeduct = min($batch->remaining_qty, $remainingToDeduct);

                DB::table('t_product_stock_batches')
                    ->where('id', $batch->id)
                    ->decrement('remaining_qty', $qtyToDeduct);

                $remainingToDeduct -= $qtyToDeduct;
            }
        }

        // Recalculate total_stock after adjustment
        app(\App\Domains\Product\Services\ProductService::class)->updateStock($bundleProductId);
    }
}
