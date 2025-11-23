<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Services\ProductionAuditLogService;
use App\Domains\Production\Services\ProductionExpenseService;
use App\Domains\Production\Services\ProductionItemService;
use App\Domains\Production\Services\ProductionService;
use App\Domains\Production\Services\ProductionStockService;
use App\Enums\ErrorCodes;
use App\Exceptions\AppException;
use Illuminate\Support\Facades\DB;

class CreateProductionAction
{
    public function execute(array $data): int
    {
        return DB::transaction(function () use ($data) {

            // 1. Validate stock availability before creating production
            $this->validateStockAvailability($data);

            // 2. Calculate totals
            $totals = $this->calculateTotals($data);

            if (isset($data['attachment']) && $data['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $path = $data['attachment']->store('productions', 'public');
                $data['attachment'] = $path;
            }
            // dd($totals);
            // 3. Create main production record
            $productionData = array_merge($data, $totals);
            $productionId = app(ProductionService::class)->create($productionData);

            // 4. Create production items (raw materials)
            if (! empty($data['items'])) {
                app(ProductionItemService::class)->createBulk($productionId, $data['items']);
            }

            // IMPORTANT
            // 5. Create production expenses
            // if (!empty($data['expenses'])) {
            //     app(ProductionExpenseService::class)->createBulk($productionId, $data['expenses']);
            // }

            // 6. If status is COMPLETED, process stock movements
            $stockResult = $this->processStockMovements($productionId, $data);

            // Update production with stock processing results
            // app(ProductionService::class)->update($productionId, [
            //     'quantity_produced' => $data['quantity_produced'] ?? $data['quantity_planned'],
            //     'status' => 'COMPLETED'
            // ] + $productionData);

            // 7. Create audit log
            app(ProductionAuditLogService::class)->logCreation(
                $productionId,
                $productionData,
                $data['items'] ?? [],
                $data['expenses'] ?? []
            );

            return $productionId;
        });
    }

    private function validateStockAvailability(array $data): void
    {
        if (empty($data['items'])) {
            return;
        }
        $itemService = app(ProductionItemService::class);
        $validationResult = $itemService->validateStockAvailability(
            $data['items'],
            $data['warehouse_id']
        );

        $insufficientItems = array_filter($validationResult, function ($item) {
            return ! $item['is_available'];
        });
        if (! empty($insufficientItems)) {
            $itemNames = array_column($insufficientItems, 'product_name');
            throw new AppException(
                ErrorCodes::INSUFFICIENT_STOCK, // Use your actual error code here
                'Insufficient stock for raw materials: '.implode(', ', $itemNames)
            );
        }
    }

    private function calculateTotals(array $data): array
    {
        $totalRawMaterialCost = 0;
        $totalProductionExpense = 0;
        $totalItemsConsumed = 0;

        // Calculate raw material costs
        if (! empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $totalRawMaterialCost += ($item['quantity_consumed'] ?? $item['quantity_required']) * ($item['unit_cost'] ?? 0);
            }
            $totalItemsConsumed = count($data['items']);
        }

        // Calculate production expenses
        if (! empty($data['expenses'])) {
            foreach ($data['expenses'] as $expense) {
                $totalProductionExpense += $expense['actual_amount'] ?? 0;
            }
        }

        return [
            'total_raw_material_cost' => $totalRawMaterialCost,
            'total_production_expense' => $totalProductionExpense,
            'total_production_cost' => $totalRawMaterialCost + $totalProductionExpense,
            'total_items_consumed' => $totalItemsConsumed,
        ];
    }

    private function processStockMovements(int $productionId, array $data): array
    {
        $stockService = app(ProductionStockService::class);

        $productionData = [
            'production_id' => $productionId,
            'production_number' => $data['production_number'],
            'production_date' => $data['production_date'],
            'bundle_product_id' => $data['bundle_product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'quantity_produced' => $data['quantity_produced'] ?? $data['quantity_planned'],
            'total_production_cost' => $data['total_production_cost'] ?? 0,
        ];

        return $stockService->createProductionStockMovements(
            $productionId,
            $data['items'] ?? [],
            $productionData
        );
    }

    public function validateProductionData(array $data): array
    {
        $errors = [];

        // Required fields validation
        $requiredFields = [
            'production_date' => 'Production date is required',
            'production_number' => 'Production number is required',
            'bundle_product_id' => 'Bundle product is required',
            'warehouse_id' => 'Warehouse is required',
            'pic_id' => 'PIC is required',
            'quantity_planned' => 'Quantity planned is required',
        ];

        foreach ($requiredFields as $field => $message) {
            if (empty($data[$field])) {
                $errors[] = $message;
            }
        }

        // CRITICAL: Validate unique production number
        if (! empty($data['production_number'])) {
            $existingProduction = DB::table('t_productions')
                ->where('production_number', $data['production_number'])
                ->where('company_code', session('user_company_code'))
                ->first();

            if ($existingProduction) {
                $errors[] = 'Production number already exists: ' . $data['production_number'];
            }
        }

        // Business logic validation
        if (! empty($data['quantity_planned']) && $data['quantity_planned'] <= 0) {
            $errors[] = 'Quantity planned must be greater than 0';
        }

        if (! empty($data['production_date'])) {
            $productionDate = \Carbon\Carbon::parse($data['production_date']);
            if ($productionDate->isFuture()) {
                $errors[] = 'Production date cannot be in the future';
            }
        }

        // Validate bundle product exists and is of type bundle
        if (! empty($data['bundle_product_id'])) {
            $bundleProduct = DB::table('m_products')
                ->where('id', $data['bundle_product_id'])
                ->where('status', 'ACTIVE')
                ->first();

            if (! $bundleProduct) {
                $errors[] = 'Selected bundle product is not found or inactive';
            }
        }

        // Validate warehouse exists
        if (! empty($data['warehouse_id'])) {
            $warehouse = DB::table('m_warehouses')
                ->where('id', $data['warehouse_id'])
                ->where('status', 'ACTIVE')
                ->first();

            if (! $warehouse) {
                $errors[] = 'Selected warehouse is not found or inactive';
            }
        }

        // Validate items
        if (! empty($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                $itemErrors = $this->validateItemData($item, $index);
                $errors = array_merge($errors, $itemErrors);
            }
        }

        // Validate expenses
        if (! empty($data['expenses'])) {
            $expenseService = app(ProductionExpenseService::class);
            $expenseValidation = $expenseService->validateExpenseData($data['expenses']);

            foreach ($expenseValidation as $index => $validation) {
                if (! $validation['is_valid']) {
                    $errors[] = "Expense #{$index}: ".implode(', ', $validation['errors']);
                }
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    private function validateItemData(array $item, int $index): array
    {
        $errors = [];

        if (empty($item['raw_material_product_id'])) {
            $errors[] = "Item #{$index}: Raw material product is required";
        }

        if (empty($item['quantity_required']) || $item['quantity_required'] <= 0) {
            $errors[] = "Item #{$index}: Quantity required must be greater than 0";
        }

        if (isset($item['quantity_consumed']) && $item['quantity_consumed'] < 0) {
            $errors[] = "Item #{$index}: Quantity consumed cannot be negative";
        }

        return $errors;
    }

    public function prepareItemsFromFormula(int $bundleProductId, int $quantityPlanned): array
    {
        // Get product compositions using correct table and field names
        $compositions = DB::table('m_product_compositions as pc')
            ->join('m_products as p', 'pc.f_product_composition_product_id', '=', 'p.id')
            ->join('m_units as u', 'pc.f_unit_id', '=', 'u.id')
            ->where('pc.f_product_id', $bundleProductId)
            ->select(
                'pc.f_product_composition_product_id as raw_material_product_id',
                'pc.qty as quantity_per_unit',
                'p.name as product_name',
                'p.average_price',
                'u.code as unit_code'
            )
            ->get();

        return $compositions->map(function ($element) use ($quantityPlanned) {
            $quantityRequired = $element->quantity_per_unit * $quantityPlanned;
            $unitCost = $element->average_price ?? 0;

            return [
                'raw_material_product_id' => $element->raw_material_product_id,
                'quantity_required' => $quantityRequired,
                // IMPORTANT
                'quantity_consumed' => $quantityRequired,
                'unit_code' => $element->unit_code,
                'unit_cost' => $unitCost,
                'total_cost' => $quantityRequired * $unitCost,
                'product_name' => $element->product_name,
            ];
        })->toArray();
    }

    public function prepareItemsFromMaterialInputs(int $bundleProductId, int $quantityProduced, array $materialInputs): array
    {
        // Material-Based mode: Use actual material inputs provided by user
        // This mode is used when user inputs available materials and system calculates max production

        $items = [];

        foreach ($materialInputs as $input) {
            // Get product details
            $product = DB::table('m_products')
                ->where('id', $input['product_id'])
                ->first();

            if (!$product) {
                continue;
            }

            // Calculate quantity consumed based on actual production
            $qtyPerBundle = $input['qty_per_bundle'];
            $quantityConsumed = $qtyPerBundle * $quantityProduced;

            // Note: material_input might be higher than quantity_consumed
            // The difference is the leftover (already calculated in frontend with 4 decimals)

            $unitCost = $product->average_price ?? 0;

            // In Material-Based mode, we validate against material_input (what user inputs)
            // But we only consume quantityConsumed (what's needed for production)
            $materialInput = $input['material_input'];

            if (isset($input['material_input'])) {
                $neededQuantity = $materialInput;
            }else {
                $neededQuantity = $quantityRequired;
            }

            $items[] = [
                'raw_material_product_id' => $input['product_id'],
                'quantity_required' => $neededQuantity, // For stock validation - what user is providing
                'quantity_consumed' => $neededQuantity, // What's actually used in production
                'unit_code' => $input['unit'],
                'unit_cost' => $unitCost,
                'total_cost' => $quantityConsumed * $unitCost,
                'product_name' => $input['product_name'],
                // Store original material input and leftover for audit trail
                'material_input' => $materialInput,
                'leftover' => $input['leftover'],
            ];
        }

        return $items;
    }

    public function prepareExpensesFromProduct(int $bundleProductId): array
    {
        $expenseService = app(ProductionExpenseService::class);

        return $expenseService->getDefaultExpensesForProduct($bundleProductId);
    }
}
