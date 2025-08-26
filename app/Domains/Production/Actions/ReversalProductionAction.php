<?php

namespace App\Domains\Production\Actions;

use App\Domains\Production\Services\ProductionService;
use App\Domains\Production\Services\ProductionItemService;
use App\Domains\Production\Services\ProductionExpenseService;
use App\Domains\Production\Services\ProductionAuditLogService;
use App\Domains\Production\Services\ProductionStockService;
use Illuminate\Support\Facades\DB;

class ReversalProductionAction
{
    public function execute(int $productionId, array $reversalData): bool
    {
        return DB::transaction(function () use ($productionId, $reversalData) {
            // 1. Validate reversal is possible
            $validationResult = $this->validateReversal($productionId, $reversalData);
            if (!$validationResult['can_reverse']) {
                throw new \Exception($validationResult['reason']);
            }

            // 2. Get production and related data
            $production = app(ProductionService::class)->findOne($productionId);
            $items = app(ProductionItemService::class)->findByProductionId($productionId);

            // 3. Calculate reversal quantities
            $quantityToReverse = $reversalData['quantity_to_reverse'] ?? $production->quantity_produced;
            
            // 4. Process stock movements reversal
            $stockResult = $this->processReversalStockMovements($productionId, $production, $items, $quantityToReverse);

            // 5. Update production status and quantities
            $this->updateProductionAfterReversal($productionId, $production, $quantityToReverse, $reversalData);

            // 6. Create audit log
            app(ProductionAuditLogService::class)->logReversal($productionId, [
                'quantity_reversed' => $quantityToReverse,
                'original_quantity' => $production->quantity_produced,
                'reason' => $reversalData['reason'] ?? 'Manual reversal',
                'stock_movements' => $stockResult,
                'reversal_date' => now()->toISOString(),
                'reversed_by' => auth()->user()->name ?? 'System'
            ]);

            return true;
        });
    }

    public function validateReversal(int $productionId, array $reversalData = []): array
    {
        $production = app(ProductionService::class)->findOne($productionId);
        
        if (!$production) {
            return ['can_reverse' => false, 'reason' => 'Production not found'];
        }

        // Check if production is completed
        if ($production->status !== 'COMPLETED') {
            return ['can_reverse' => false, 'reason' => 'Only completed productions can be reversed'];
        }

        // Check if there's quantity to reverse
        if ($production->quantity_produced <= 0) {
            return ['can_reverse' => false, 'reason' => 'No quantity produced to reverse'];
        }

        $quantityToReverse = $reversalData['quantity_to_reverse'] ?? $production->quantity_produced;

        // Validate reversal quantity
        if ($quantityToReverse <= 0) {
            return ['can_reverse' => false, 'reason' => 'Reversal quantity must be greater than 0'];
        }

        if ($quantityToReverse > $production->quantity_produced) {
            return [
                'can_reverse' => false, 
                'reason' => 'Cannot reverse more than produced quantity',
                'max_reversible' => $production->quantity_produced
            ];
        }

        // Check bundle product stock availability
        $bundleProduct = DB::table('m_products')
            ->where('id', $production->bundle_product_id)
            ->first();

        if (!$bundleProduct || $bundleProduct->total_stock < $quantityToReverse) {
            return [
                'can_reverse' => false,
                'reason' => 'Insufficient bundle product stock for reversal',
                'available_stock' => $bundleProduct->total_stock ?? 0,
                'required_stock' => $quantityToReverse
            ];
        }

        // Check if production batch has sufficient quantity (FIFO validation)
        $productionBatch = DB::table('t_product_stock_batches')
            ->where('product_id', $production->bundle_product_id)
            ->where('warehouse_id', $production->warehouse_id)
            ->where('reference_type', 'PRODUCTION')
            ->where('reference_id', $productionId)
            ->first();

        if (!$productionBatch || $productionBatch->remaining_quantity < $quantityToReverse) {
            return [
                'can_reverse' => false,
                'reason' => 'Production batch has insufficient quantity for reversal',
                'batch_remaining' => $productionBatch->remaining_quantity ?? 0,
                'required_quantity' => $quantityToReverse
            ];
        }

        // Get reversal impact summary
        $impactSummary = $this->getReversalImpactSummary($productionId, $quantityToReverse);

        return [
            'can_reverse' => true,
            'reversal_details' => [
                'production_number' => $production->production_number,
                'bundle_product_name' => $production->bundle_product_name,
                'original_quantity' => $production->quantity_produced,
                'quantity_to_reverse' => $quantityToReverse,
                'impact_summary' => $impactSummary
            ]
        ];
    }

    public function getReversalImpactSummary(int $productionId, float $quantityToReverse): array
    {
        $production = app(ProductionService::class)->findOne($productionId);
        $items = app(ProductionItemService::class)->findByProductionId($productionId);

        $impactSummary = [
            'bundle_product' => [
                'name' => $production->bundle_product_name,
                'quantity_will_be_removed' => $quantityToReverse,
                'current_stock' => $this->getCurrentStock($production->bundle_product_id),
                'stock_after_reversal' => $this->getCurrentStock($production->bundle_product_id) - $quantityToReverse
            ],
            'raw_materials' => [],
            'financial_impact' => $this->calculateFinancialImpact($production, $quantityToReverse)
        ];

        // Calculate raw material restoration impact
        foreach ($items as $item) {
            $restorationQuantity = ($item->quantity_consumed / $production->quantity_produced) * $quantityToReverse;
            
            $impactSummary['raw_materials'][] = [
                'name' => $item->product_name,
                'quantity_will_be_restored' => $restorationQuantity,
                'current_stock' => $this->getCurrentStock($item->raw_material_product_id),
                'stock_after_reversal' => $this->getCurrentStock($item->raw_material_product_id) + $restorationQuantity
            ];
        }

        return $impactSummary;
    }

    private function processReversalStockMovements(int $productionId, $production, array $items, float $quantityToReverse): array
    {
        $stockService = app(ProductionStockService::class);

        // Prepare reversal data
        $reversalData = [
            'production' => (array) $production,
            'items' => $items,
            'quantity_to_reverse' => $quantityToReverse
        ];

        return $stockService->reverseProductionStockMovements($productionId, $reversalData);
    }

    private function updateProductionAfterReversal(int $productionId, $production, float $quantityToReverse, array $reversalData): void
    {
        $newQuantityProduced = $production->quantity_produced - $quantityToReverse;
        $newStatus = $newQuantityProduced > 0 ? 'COMPLETED' : 'CANCELLED';

        // Calculate proportional cost adjustments
        $costRatio = $newQuantityProduced > 0 ? ($newQuantityProduced / $production->quantity_produced) : 0;

        $updateData = [
            'quantity_produced' => $newQuantityProduced,
            'status' => $newStatus,
            'total_raw_material_cost' => $production->total_raw_material_cost * $costRatio,
            'total_production_expense' => $production->total_production_expense * $costRatio,
            'total_production_cost' => $production->total_production_cost * $costRatio,
            'notes' => ($production->notes ? $production->notes . "\n\n" : '') 
                     . "REVERSAL: {$quantityToReverse} units reversed on " . now()->format('Y-m-d H:i:s') 
                     . ". Reason: " . ($reversalData['reason'] ?? 'Manual reversal')
        ];

        app(ProductionService::class)->update($productionId, $updateData);

        // If fully reversed, update items and expenses proportionally
        if ($newQuantityProduced == 0) {
            $this->clearProductionItemsAndExpenses($productionId);
        } else {
            $this->adjustProductionItemsAndExpenses($productionId, $costRatio);
        }
    }

    private function clearProductionItemsAndExpenses(int $productionId): void
    {
        // Set quantities to zero instead of deleting for audit trail
        DB::table('t_production_items')
            ->where('production_id', $productionId)
            ->update([
                'quantity_consumed' => 0,
                'total_cost' => 0,
                'updated_at' => now()
            ]);

        DB::table('t_production_expenses')
            ->where('production_id', $productionId)
            ->update([
                'actual_amount' => 0,
                'updated_at' => now()
            ]);
    }

    private function adjustProductionItemsAndExpenses(int $productionId, float $costRatio): void
    {
        // Proportionally adjust item quantities and costs
        $items = DB::table('t_production_items')->where('production_id', $productionId)->get();
        foreach ($items as $item) {
            DB::table('t_production_items')
                ->where('id', $item->id)
                ->update([
                    'quantity_consumed' => $item->quantity_consumed * $costRatio,
                    'total_cost' => $item->total_cost * $costRatio,
                    'updated_at' => now()
                ]);
        }

        // Proportionally adjust expense amounts
        $expenses = DB::table('t_production_expenses')->where('production_id', $productionId)->get();
        foreach ($expenses as $expense) {
            DB::table('t_production_expenses')
                ->where('id', $expense->id)
                ->update([
                    'actual_amount' => $expense->actual_amount * $costRatio,
                    'updated_at' => now()
                ]);
        }
    }

    private function getCurrentStock(int $productId): float
    {
        $product = DB::table('m_products')->where('id', $productId)->first();
        return $product ? $product->total_stock : 0;
    }

    private function calculateFinancialImpact($production, float $quantityToReverse): array
    {
        $impactRatio = $quantityToReverse / $production->quantity_produced;

        return [
            'raw_material_cost_impact' => $production->total_raw_material_cost * $impactRatio,
            'production_expense_impact' => $production->total_production_expense * $impactRatio,
            'total_cost_impact' => $production->total_production_cost * $impactRatio,
            'unit_cost_impact' => $production->total_production_cost / $production->quantity_produced
        ];
    }

    public function getReversalConfirmationData(int $productionId, array $reversalData = []): array
    {
        $validationResult = $this->validateReversal($productionId, $reversalData);
        
        if (!$validationResult['can_reverse']) {
            return $validationResult;
        }

        $production = app(ProductionService::class)->findOne($productionId);
        $quantityToReverse = $reversalData['quantity_to_reverse'] ?? $production->quantity_produced;

        return [
            'can_reverse' => true,
            'production' => $production,
            'reversal_quantity' => $quantityToReverse,
            'impact_summary' => $validationResult['reversal_details']['impact_summary'],
            'warnings' => $this->getReversalWarnings($production, $quantityToReverse),
            'confirmation_required' => true
        ];
    }

    private function getReversalWarnings($production, float $quantityToReverse): array
    {
        $warnings = [];

        // Check for high-value reversal
        $reversalValue = ($production->total_production_cost / $production->quantity_produced) * $quantityToReverse;
        if ($reversalValue > 100000) { // Configurable threshold
            $warnings[] = "High-value reversal: Rp " . number_format($reversalValue, 0, ',', '.');
        }

        // Check if this is a full reversal
        if ($quantityToReverse >= $production->quantity_produced) {
            $warnings[] = "This is a complete production reversal. All produced quantity will be removed.";
        }

        // Check if production is recent
        $productionDate = \Carbon\Carbon::parse($production->production_date);
        if ($productionDate->diffInDays(now()) > 30) {
            $warnings[] = "This production is over 30 days old. Please ensure reversal is still appropriate.";
        }

        return $warnings;
    }
}