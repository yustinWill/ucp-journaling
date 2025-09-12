<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BatchManagementService
{
    private $batch_table = 't_product_stock_batches';
    private $transaction_table = 't_product_batch_transactions';

    /**
     * Create a new batch for stock increases
     */
    public function createBatch(array $data): int
    {
        $batchReference = $this->generateBatchReference($data['source_type'], $data['f_source_transaction_id'], $data['f_product_id']);
        
        return DB::table($this->batch_table)->insertGetId([
            'company_code' => $data['company_code'] ?? session('user_company_code'),
            'f_product_id' => $data['f_product_id'],
            'batch_reference_code' => $batchReference,
            'source_type' => $data['source_type'] ?? 'PURCHASE',
            'f_source_transaction_id' => $data['f_source_transaction_id'],
            'batch_date' => $data['batch_date'] ?? now()->format('Y-m-d'),
            'initial_qty' => $data['quantity'],
            'remaining_qty' => $data['quantity'],
            'raw_material_cost' => $data['raw_material_cost'] ?? 0.00,
            'production_cost' => $data['production_cost'] ?? 0.00,
            'purchase_price' => $data['purchase_price'] ?? 0.00,
            'estimated_hpp' => $data['estimated_hpp'] ?? ($data['purchase_price'] ?? 0.00),
            'final_hpp' => $data['final_hpp'] ?? null,
            'hpp_status' => $data['hpp_status'] ?? 'UNSETTLED',
            'status' => 'ACTIVE',
            'created_at' => now(),
            'created_by' => Auth::id(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Consume batches using FIFO method for stock decreases
     */
    public function consumeBatches(int $productId, float $quantityToConsume, array $transactionData): array
    {
        // Get available batches ordered by date (FIFO)
        $batches = DB::table($this->batch_table)
            ->where('f_product_id', $productId)
            ->where('remaining_qty', '>', 0)
            ->where('status', 'ACTIVE')
            ->orderBy('batch_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $remainingToConsume = $quantityToConsume;
        $consumedBatches = [];

        foreach ($batches as $batch) {
            if ($remainingToConsume <= 0) {
                break;
            }

            if (!isset($batch->id) || !isset($batch->remaining_qty)) {
                continue;
            }

            $availableQty = $batch->remaining_qty;
            $consumeFromThisBatch = min($remainingToConsume, $availableQty);

            // Update batch remaining quantity
            DB::table($this->batch_table)
                ->where('id', $batch->id)
                ->update([
                    'remaining_qty' => $availableQty - $consumeFromThisBatch,
                    'updated_at' => now(),
                    'updated_by' => Auth::id(),
                ]);

            // Record batch consumption transaction
            DB::table($this->transaction_table)->insert([
                'company_code' => $transactionData['company_code'] ?? session('user_company_code'),
                'f_product_batch_id' => $batch->id,
                'f_product_id' => $productId,
                'transaction_type' => 'OUT',
                'source_type' => 'SALES_DELIVERY', // Use accepted value
                'f_source_transaction_id' => $transactionData['f_source_transaction_id'],
                'transaction_date' => $transactionData['transaction_date'] ?? now(),
                'qty_moved' => $consumeFromThisBatch,
                'reference_code' => $transactionData['reference_code'],
                'notes' => $transactionData['notes'] ?? 'Stock consumed',
                'created_at' => now(),
                'created_by' => Auth::id(),
            ]);

            $consumedBatches[] = [
                'batch_id' => $batch->id,
                'batch_reference' => $batch->batch_reference_code,
                'quantity_consumed' => $consumeFromThisBatch,
                'remaining_after' => $availableQty - $consumeFromThisBatch,
                'cost_per_unit' => $batch->estimated_hpp,
                'total_cost' => $consumeFromThisBatch * $batch->estimated_hpp,
            ];

            $remainingToConsume -= $consumeFromThisBatch;
        }

        // Log warning if we couldn't consume all required quantity
        if ($remainingToConsume > 0) {
            Log::warning("Insufficient stock batches for product {$productId}. Could not consume {$remainingToConsume} units.");
        }

        return $consumedBatches;
    }

    /**
     * Get current batch information for a product
     */
    public function getProductBatchInfo(int $productId, bool $includeEmptyBatches = false): array
    {
        $query = DB::table($this->batch_table)
            ->where('f_product_id', $productId)
            ->where('status', 'ACTIVE')
            ->orderBy('batch_date', 'asc');

        if (!$includeEmptyBatches) {
            $query->where('remaining_qty', '>', 0);
        }

        $batches = $query->get();

        $totalQuantity = 0;
        $totalValue = 0;
        $batchDetails = [];

        foreach ($batches as $batch) {
            $remainingQty = $batch->remaining_qty;
            $estimatedHpp = $batch->estimated_hpp ?? 0;
            $batchValue = $remainingQty * $estimatedHpp;

            $totalQuantity += $remainingQty;
            $totalValue += $batchValue;

            $batchDetails[] = [
                'batch_id' => $batch->id,
                'batch_reference' => $batch->batch_reference_code,
                'source_type' => $batch->source_type,
                'batch_date' => $batch->batch_date,
                'initial_qty' => $batch->initial_qty,
                'remaining_qty' => $remainingQty,
                'purchase_price' => $batch->purchase_price,
                'production_cost' => $batch->production_cost,
                'estimated_hpp' => $estimatedHpp,
                'final_hpp' => $batch->final_hpp,
                'hpp_status' => $batch->hpp_status,
                'batch_value' => $batchValue,
            ];
        }

        return [
            'product_id' => $productId,
            'total_batches' => count($batchDetails),
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'weighted_average_cost' => $totalQuantity > 0 ? $totalValue / $totalQuantity : 0,
            'batches' => $batchDetails,
        ];
    }

    /**
     * Get COGS for consumed quantity using FIFO
     */
    public function calculateCOGS(int $productId, float $quantity): array
    {
        $batches = DB::table($this->batch_table)
            ->where('f_product_id', $productId)
            ->where('remaining_qty', '>', 0)
            ->where('status', 'ACTIVE')
            ->orderBy('batch_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $remainingQty = $quantity;
        $totalCOGS = 0;
        $batchesUsed = [];

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) {
                break;
            }

            $availableQty = $batch->remaining_qty;
            $useFromThisBatch = min($remainingQty, $availableQty);
            $batchCost = $useFromThisBatch * ($batch->estimated_hpp ?? 0);

            $totalCOGS += $batchCost;
            $batchesUsed[] = [
                'batch_id' => $batch->id,
                'batch_reference' => $batch->batch_reference_code,
                'quantity_used' => $useFromThisBatch,
                'cost_per_unit' => $batch->estimated_hpp ?? 0,
                'total_cost' => $batchCost,
            ];

            $remainingQty -= $useFromThisBatch;
        }

        return [
            'requested_quantity' => $quantity,
            'calculated_quantity' => $quantity - $remainingQty,
            'total_cogs' => $totalCOGS,
            'average_cost_per_unit' => $quantity > 0 ? $totalCOGS / ($quantity - $remainingQty) : 0,
            'batches_used' => $batchesUsed,
            'insufficient_stock' => $remainingQty > 0,
            'shortfall' => $remainingQty,
        ];
    }

    /**
     * Generate batch reference code
     */
    private function generateBatchReference(string $sourceType, int $transactionId, int $productId): string
    {
        $prefix = match($sourceType) {
            'PURCHASE' => 'PD',
            'PRODUCTION' => 'PROD',
            'ADJUSTMENT' => 'ADJ',
            'ADJUSTMENT_REVERSAL' => 'ADJ-REV',
            default => 'BATCH'
        };

        return $prefix . '-' . $transactionId . '-' . $productId . '-' . time();
    }
}