<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class SalesOrderRevenueSplitService
{
    private $tableName = 't_sales_order_revenue_splits';
    private $configTableName = 'm_product_revenue_split_configs';

    /**
     * Create revenue splits for all products in a sales order
     */
    public function createRevenueSplitsForSalesOrder($salesOrderId, $salesOrderProducts)
    {
        try {
            $companyCode = session('user_company_code');
            $userId = Auth::id();

            // Delete existing splits if any
            $this->deleteRevenueSplitsForSalesOrder($salesOrderId);

            foreach ($salesOrderProducts as $product) {
                $this->createRevenueSplitForProduct($salesOrderId, $product, $companyCode, $userId);
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to create revenue splits: ' . $e->getMessage());
        }
    }

    /**
     * Create revenue split for a single product in sales order
     */
    public function createRevenueSplitForProduct($salesOrderId, $product, $companyCode, $userId)
    {
        // Get product revenue split configuration
        $config = DB::table($this->configTableName)
            ->where('company_code', $companyCode)
            ->where('f_product_id', $product['f_product_id'])
            ->where('status', 'ACTIVE')
            ->first();

        $unitPrice = $product['unit_price'];
        $quantity = $product['quantity'];
        $totalAmount = $unitPrice * $quantity;

        if ($config) {
            // Use configuration to calculate split
            $split = $this->calculateSplitFromConfig($config, $unitPrice, $quantity);
            $configId = $config->id;
            $isCustom = false;
        } else {
            // Default split if no configuration exists (all goes to company)
            $split = [
                'porsi_perusahaan' => $totalAmount,
                'porsi_owner' => 0,
                'porsi_pabrik' => 0,
                'remaining_allocation' => 'perusahaan',
                'remaining_amount' => 0
            ];
            $configId = null;
            $isCustom = false;
        }

        // Insert revenue split record
        DB::table($this->tableName)->insert([
            'company_code' => $companyCode,
            'f_sales_order_id' => $salesOrderId,
            'f_sales_order_product_id' => $product['id'] ?? null,
            'f_product_id' => $product['f_product_id'],
            'product_unit_price' => $unitPrice,
            'product_quantity' => $quantity,
            'product_total_amount' => $totalAmount,
            'porsi_perusahaan' => $split['porsi_perusahaan'],
            'porsi_owner' => $split['porsi_owner'],
            'porsi_pabrik' => $split['porsi_pabrik'],
            'remaining_allocation' => $split['remaining_allocation'],
            'remaining_amount' => $split['remaining_amount'],
            'f_revenue_split_config_id' => $configId,
            'is_custom_split' => $isCustom,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Calculate revenue split based on configuration
     */
    private function calculateSplitFromConfig($config, $unitPrice, $quantity)
    {
        $totalAmount = $unitPrice * $quantity;
        $basePrice = $config->base_price;
        $ratio = $unitPrice / $basePrice;
        // Calculate proportional splits
        $porsiPerusahaan = $config->porsi_perusahaan * $ratio * $quantity;
        $porsiOwner = $config->porsi_owner * $ratio * $quantity;
        $porsiPabrik = $config->porsi_pabrik * $ratio * $quantity;

        // Calculate remaining amount
        $totalFixed = $porsiPerusahaan + $porsiOwner + $porsiPabrik;
        $remainingAmount = $totalAmount - $totalFixed;

        // Allocate remaining to specified category
        switch ($config->remaining_allocation) {
            case 'perusahaan':
                $porsiPerusahaan += $remainingAmount;
                break;
            case 'owner':
                $porsiOwner += $remainingAmount;
                break;
            case 'pabrik':
                $porsiPabrik += $remainingAmount;
                break;
        }

        return [
            'porsi_perusahaan' => round($porsiPerusahaan, 2),
            'porsi_owner' => round($porsiOwner, 2),
            'porsi_pabrik' => round($porsiPabrik, 2),
            'remaining_allocation' => $config->remaining_allocation,
            'remaining_amount' => round($remainingAmount, 2)
        ];
    }

    /**
     * Get revenue splits for a sales order
     */
    public function getRevenueSplitsForSalesOrder($salesOrderId)
    {
        try {
            $companyCode = session('user_company_code');

            return DB::table($this->tableName . ' as rs')
                ->leftJoin('m_products as p', 'rs.f_product_id', '=', 'p.id')
                ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
                ->leftJoin($this->configTableName . ' as rc', 'rs.f_revenue_split_config_id', '=', 'rc.id')
                ->where('rs.company_code', $companyCode)
                ->where('rs.f_sales_order_id', $salesOrderId)
                ->select(
                    'rs.*',
                    'p.code as product_code',
                    'p.name as product_name',
                    'u.name as unit_name',
                    'rc.base_price as config_base_price'
                )
                ->orderBy('rs.id')
                ->get();
        } catch (Exception $e) {
            throw new Exception('Failed to get revenue splits: ' . $e->getMessage());
        }
    }

    /**
     * Update revenue split for a specific product (custom override)
     */
    public function updateRevenueSplit($salesOrderId, $productId, $splitData)
    {
        try {
            $companyCode = session('user_company_code');
            $userId = Auth::id();

            // Validate that split totals match product total
            $totalSplit = $splitData['porsi_perusahaan'] + $splitData['porsi_owner'] + $splitData['porsi_pabrik'];
            $productTotal = $splitData['product_total_amount'];

            if (abs($totalSplit - $productTotal) > 0.01) {
                throw new Exception('Total revenue split must equal product total amount');
            }

            DB::table($this->tableName)->updateOrInsert(
                [
                    'company_code' => $companyCode,
                    'f_sales_order_id' => $salesOrderId,
                    'f_product_id' => $productId
                ],
                [
                    'porsi_perusahaan' => $splitData['porsi_perusahaan'],
                    'porsi_owner' => $splitData['porsi_owner'],
                    'porsi_pabrik' => $splitData['porsi_pabrik'],
                    'remaining_allocation' => $splitData['remaining_allocation'],
                    'remaining_amount' => $splitData['remaining_amount'] ?? 0,
                    'is_custom_split' => true,
                    'split_notes' => $splitData['split_notes'] ?? null,
                    'updated_by' => $userId,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to update revenue split: ' . $e->getMessage());
        }
    }

    /**
     * Delete revenue splits for a sales order
     */
    public function deleteRevenueSplitsForSalesOrder($salesOrderId)
    {
        try {
            $companyCode = session('user_company_code');

            DB::table($this->tableName)
                ->where('company_code', $companyCode)
                ->where('f_sales_order_id', $salesOrderId)
                ->delete();

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete revenue splits: ' . $e->getMessage());
        }
    }

    /**
     * Get revenue split summary for a sales order
     */
    public function getRevenueSplitSummary($salesOrderId)
    {
        try {
            $companyCode = session('user_company_code');

            $summary = DB::table($this->tableName)
                ->where('company_code', $companyCode)
                ->where('f_sales_order_id', $salesOrderId)
                ->selectRaw('
                    SUM(porsi_perusahaan) as total_perusahaan,
                    SUM(porsi_owner) as total_owner,
                    SUM(porsi_pabrik) as total_pabrik,
                    SUM(product_total_amount) as total_amount,
                    COUNT(*) as total_products,
                    SUM(CASE WHEN is_custom_split = 1 THEN 1 ELSE 0 END) as custom_splits
                ')
                ->first();

            return $summary;
        } catch (Exception $e) {
            throw new Exception('Failed to get revenue split summary: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate revenue splits when sales order is updated
     */
    public function recalculateRevenueSplits($salesOrderId, $updatedProducts)
    {
        try {
            // Get existing splits to preserve custom overrides
            $existingSplits = $this->getRevenueSplitsForSalesOrder($salesOrderId);
            $existingSplitsById = $existingSplits->keyBy('f_product_id');

            // Delete all existing splits
            $this->deleteRevenueSplitsForSalesOrder($salesOrderId);

            $companyCode = session('user_company_code');
            $userId = Auth::id();

            // Recreate splits, preserving custom ones
            foreach ($updatedProducts as $product) {
                $productId = $product['f_product_id'];
                $existingSplit = $existingSplitsById->get($productId);

                if ($existingSplit && $existingSplit->is_custom_split) {
                    // Preserve custom split but update quantities/amounts
                    $newTotalAmount = $product['unit_price'] * $product['quantity'];
                    $ratio = $newTotalAmount / $existingSplit->product_total_amount;

                    $splitData = [
                        'porsi_perusahaan' => round($existingSplit->porsi_perusahaan * $ratio, 2),
                        'porsi_owner' => round($existingSplit->porsi_owner * $ratio, 2),
                        'porsi_pabrik' => round($existingSplit->porsi_pabrik * $ratio, 2),
                        'remaining_allocation' => $existingSplit->remaining_allocation,
                        'remaining_amount' => round($existingSplit->remaining_amount * $ratio, 2),
                        'split_notes' => $existingSplit->split_notes,
                        'product_total_amount' => $newTotalAmount
                    ];

                    // Manually insert the preserved custom split
                    DB::table($this->tableName)->insert([
                        'company_code' => $companyCode,
                        'f_sales_order_id' => $salesOrderId,
                        'f_sales_order_product_id' => $product['id'] ?? null,
                        'f_product_id' => $productId,
                        'product_unit_price' => $product['unit_price'],
                        'product_quantity' => $product['quantity'],
                        'product_total_amount' => $newTotalAmount,
                        'porsi_perusahaan' => $splitData['porsi_perusahaan'],
                        'porsi_owner' => $splitData['porsi_owner'],
                        'porsi_pabrik' => $splitData['porsi_pabrik'],
                        'remaining_allocation' => $splitData['remaining_allocation'],
                        'remaining_amount' => $splitData['remaining_amount'],
                        'f_revenue_split_config_id' => $existingSplit->f_revenue_split_config_id,
                        'is_custom_split' => true,
                        'split_notes' => $splitData['split_notes'],
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    // Create new split based on current configuration
                    $this->createRevenueSplitForProduct($salesOrderId, $product, $companyCode, $userId);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to recalculate revenue splits: ' . $e->getMessage());
        }
    }
}