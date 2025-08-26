<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Services\ProductionService;
use App\Domains\Production\Services\ProductionItemService;
use App\Domains\Production\Services\ProductionExpenseService;
use App\Domains\Production\Services\ProductionAuditLogService;
use App\Domains\Production\Services\ProductionStockService;
use Illuminate\Support\Facades\DB;

class UpdateProductionAction
{
    public function execute(int $productionId, array $data): bool
    {
        return DB::transaction(function () use ($productionId, $data) {
            // 1. Get existing production data for comparison
            $existingProduction = app(ProductionService::class)->findOne($productionId);
            if (!$existingProduction) {
                throw new \Exception('Production not found');
            }

            // 2. Validate update constraints
            $this->validateUpdateConstraints($existingProduction, $data);

            // 3. Detect changes for audit logging
            $changes = $this->detectChanges($existingProduction, $data);

            // 4. Calculate new totals
            $totals = $this->calculateTotals($data);

            // 5. Update main production record
            $productionData = array_merge($data, $totals);
            $updated = app(ProductionService::class)->update($productionId, $productionData);

            if (!$updated) {
                throw new \Exception('Failed to update production');
            }

            // 6. Update production items if provided
            if (isset($data['items'])) {
                app(ProductionItemService::class)->updateBulk($productionId, $data['items']);
            }

            // 7. Update production expenses if provided
            if (isset($data['expenses'])) {
                app(ProductionExpenseService::class)->updateBulk($productionId, $data['expenses']);
            }

            // 8. Handle status changes with stock implications
            if (isset($changes['status'])) {
                $this->handleStatusChange(
                    $productionId, 
                    $changes['status']['from'], 
                    $changes['status']['to'], 
                    $data
                );
            }

            // 9. Create audit log
            app(ProductionAuditLogService::class)->logUpdate(
                $productionId,
                (array) $existingProduction,
                $productionData,
                $changes
            );

            return true;
        });
    }

    private function validateUpdateConstraints($existingProduction, array $data): void
    {
        // Cannot update completed productions except for certain fields
        if ($existingProduction->status === 'COMPLETED') {
            $allowedFields = ['notes', 'description', 'f_tag_codes'];
            $restrictedFields = array_diff(array_keys($data), $allowedFields);
            
            if (!empty($restrictedFields)) {
                throw new \Exception(
                    'Cannot modify completed production. Only notes, description, and tags can be updated.'
                );
            }
        }

        // Cannot update cancelled productions
        if ($existingProduction->status === 'CANCELLED') {
            throw new \Exception('Cannot modify cancelled production');
        }

        // Validate stock availability if items are being updated
        if (!empty($data['items']) && $existingProduction->status !== 'COMPLETED') {
            $itemService = app(ProductionItemService::class);
            $validationResult = $itemService->validateStockAvailability(
                $data['items'], 
                $data['warehouse_id'] ?? $existingProduction->warehouse_id
            );

            $insufficientItems = array_filter($validationResult, function ($item) {
                return !$item['is_available'];
            });

            if (!empty($insufficientItems)) {
                $itemNames = array_column($insufficientItems, 'product_name');
                throw new \Exception(
                    'Insufficient stock for raw materials: ' . implode(', ', $itemNames)
                );
            }
        }
    }

    private function detectChanges($existingProduction, array $data): array
    {
        $changes = [];
        $trackableFields = [
            'production_date', 'bundle_product_id', 'warehouse_id', 'pic_id',
            'quantity_planned', 'quantity_produced', 'status', 'description', 'notes'
        ];

        foreach ($trackableFields as $field) {
            if (isset($data[$field]) && $data[$field] != $existingProduction->$field) {
                $changes[$field] = [
                    'from' => $existingProduction->$field,
                    'to' => $data[$field]
                ];
            }
        }

        return $changes;
    }

    private function calculateTotals(array $data): array
    {
        $totalRawMaterialCost = 0;
        $totalProductionExpense = 0;
        $totalItemsConsumed = 0;

        // Calculate raw material costs if items are provided
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $totalRawMaterialCost += ($item['quantity_consumed'] ?? $item['quantity_required']) * ($item['unit_cost'] ?? 0);
            }
            $totalItemsConsumed = count($data['items']);
        }

        // Calculate production expenses if expenses are provided
        if (!empty($data['expenses'])) {
            foreach ($data['expenses'] as $expense) {
                $totalProductionExpense += $expense['actual_amount'] ?? 0;
            }
        }

        return [
            'total_raw_material_cost' => $totalRawMaterialCost,
            'total_production_expense' => $totalProductionExpense,
            'total_production_cost' => $totalRawMaterialCost + $totalProductionExpense,
            'total_items_consumed' => $totalItemsConsumed
        ];
    }

    private function handleStatusChange(int $productionId, string $fromStatus, string $toStatus, array $data): void
    {
        $auditService = app(ProductionAuditLogService::class);

        // Log the status change
        $auditService->logStatusChange($productionId, $fromStatus, $toStatus, $data['status_change_reason'] ?? null);

        // Handle stock movements for status changes
        switch ($toStatus) {
            case 'COMPLETED':
                if ($fromStatus !== 'COMPLETED') {
                    $this->completeProduction($productionId, $data);
                }
                break;

            case 'CANCELLED':
                if ($fromStatus === 'COMPLETED') {
                    // If was completed, need to reverse stock movements
                    $this->reverseCompletedProduction($productionId, $data);
                }
                break;
        }
    }

    private function completeProduction(int $productionId, array $data): void
    {
        // Process stock movements for completion
        $stockService = app(ProductionStockService::class);
        
        $productionService = app(ProductionService::class);
        $production = $productionService->findOne($productionId);
        
        $items = app(ProductionItemService::class)->findByProductionId($productionId);
        
        $productionData = [
            'production_id' => $productionId,
            'production_number' => $production->production_number,
            'production_date' => $production->production_date,
            'bundle_product_id' => $production->bundle_product_id,
            'warehouse_id' => $production->warehouse_id,
            'quantity_produced' => $data['quantity_produced'] ?? $production->quantity_planned,
            'total_production_cost' => $production->total_production_cost
        ];

        $stockResult = $stockService->createProductionStockMovements(
            $productionId,
            $items,
            $productionData
        );

        // Log completion
        app(ProductionAuditLogService::class)->logCompletion($productionId, [
            'quantity_produced' => $productionData['quantity_produced'],
            'stock_movements' => $stockResult
        ]);
    }

    private function reverseCompletedProduction(int $productionId, array $data): void
    {
        $stockService = app(ProductionStockService::class);
        $productionService = app(ProductionService::class);
        
        $production = $productionService->findOne($productionId);
        $items = app(ProductionItemService::class)->findByProductionId($productionId);

        // Check if reversal is possible
        $canReverse = $productionService->canBeReversed($productionId);
        if (!$canReverse['can_reverse']) {
            throw new \Exception($canReverse['reason']);
        }

        $reversalData = [
            'production' => (array) $production,
            'items' => $items,
            'quantity_to_reverse' => $production->quantity_produced,
            'reason' => 'Production cancelled - reversing stock movements'
        ];

        $stockResult = $stockService->reverseProductionStockMovements($productionId, $reversalData);

        // Log the automatic reversal
        app(ProductionAuditLogService::class)->logReversal($productionId, [
            'quantity_reversed' => $production->quantity_produced,
            'stock_movements' => $stockResult,
            'reason' => 'Automatic reversal due to production cancellation'
        ]);
    }

    public function validateUpdateData(int $productionId, array $data): array
    {
        $errors = [];

        // Get existing production for validation context
        $existingProduction = app(ProductionService::class)->findOne($productionId);
        if (!$existingProduction) {
            $errors[] = 'Production not found';
            return ['is_valid' => false, 'errors' => $errors];
        }

        // Business logic validation
        if (isset($data['quantity_planned']) && $data['quantity_planned'] <= 0) {
            $errors[] = 'Quantity planned must be greater than 0';
        }

        if (isset($data['quantity_produced']) && $data['quantity_produced'] < 0) {
            $errors[] = 'Quantity produced cannot be negative';
        }

        if (isset($data['production_date'])) {
            $productionDate = \Carbon\Carbon::parse($data['production_date']);
            if ($productionDate->isFuture()) {
                $errors[] = 'Production date cannot be in the future';
            }
        }

        // Status transition validation
        if (isset($data['status'])) {
            $validTransitions = $this->getValidStatusTransitions($existingProduction->status);
            if (!in_array($data['status'], $validTransitions)) {
                $errors[] = "Cannot change status from {$existingProduction->status} to {$data['status']}";
            }
        }

        // Validate items if provided
        if (isset($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                $itemErrors = $this->validateItemData($item, $index);
                $errors = array_merge($errors, $itemErrors);
            }
        }

        // Validate expenses if provided
        if (isset($data['expenses'])) {
            $expenseService = app(ProductionExpenseService::class);
            $expenseValidation = $expenseService->validateExpenseData($data['expenses']);
            
            foreach ($expenseValidation as $index => $validation) {
                if (!$validation['is_valid']) {
                    $errors[] = "Expense #{$index}: " . implode(', ', $validation['errors']);
                }
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }

    private function getValidStatusTransitions(string $currentStatus): array
    {
        $transitions = [
            'DRAFT' => ['PLANNED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'],
            'PLANNED' => ['IN_PROGRESS', 'COMPLETED', 'CANCELLED'],
            'IN_PROGRESS' => ['COMPLETED', 'CANCELLED'],
            'COMPLETED' => ['CANCELLED'], // Can be cancelled (will trigger reversal)
            'CANCELLED' => [] // Cannot transition from cancelled
        ];

        return $transitions[$currentStatus] ?? [];
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
}