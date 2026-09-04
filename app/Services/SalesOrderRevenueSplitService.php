<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SalesOrderRevenueSplitService
{
    private string $tableName = 't_sales_order_revenue_splits';

    private string $configTableName = 'm_product_revenue_split_configs';

    public function __construct(
        private readonly RevenueSplitCalculator $calculator
    ) {}

    /**
     * Create one aggregate split per product in a sales order.
     */
    public function createRevenueSplitsForSalesOrder($salesOrderId, $salesOrderProducts)
    {
        try {
            return DB::transaction(function () use ($salesOrderId, $salesOrderProducts) {
                $companyCode = session('user_company_code');
                $userId = Auth::id();
                $processedProductIds = [];

                $this->deleteRevenueSplitsForSalesOrder($salesOrderId);

                foreach ($salesOrderProducts as $product) {
                    $product = (array) $product;
                    $productId = $product['f_product_id'];

                    if (isset($processedProductIds[$productId])) {
                        continue;
                    }

                    $this->createRevenueSplitForProduct(
                        $salesOrderId,
                        $product,
                        $companyCode,
                        $userId
                    );
                    $processedProductIds[$productId] = true;
                }

                return true;
            });
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception('Failed to create revenue splits: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Aggregate regular and bonus rows for one product, then apply the
     * specification formula. Bonus units add to the guaranteed factory
     * obligation; Owner and Company receive the remaining available value.
     */
    public function createRevenueSplitForProduct($salesOrderId, $product, $companyCode, $userId)
    {
        $product = (array) $product;
        $productId = $product['f_product_id'];
        $allProductRows = DB::table('t_sales_order_products')
            ->where('f_sales_order_id', $salesOrderId)
            ->where('f_product_id', $productId)
            ->select('quantity', 'unit_price', 'is_bonus', 'id')
            ->get();

        $totalQuantity = 0;
        $regularQuantity = 0;
        $bonusQuantity = 0;
        $unitPrice = $product['unit_price'] ?? 0;
        $firstRowId = $product['id'] ?? null;

        foreach ($allProductRows as $row) {
            $qty = $this->calculator->wholeQuantity($row->quantity, 'Qty produk');
            $totalQuantity += $qty;

            if ($row->is_bonus) {
                $bonusQuantity += $qty;
            } else {
                $regularQuantity += $qty;
                $unitPrice = $row->unit_price;
                $firstRowId = $row->id;
            }
        }

        if ($allProductRows->isEmpty()) {
            $regularQuantity = $this->calculator->wholeQuantity(
                $product['quantity'] ?? 0,
                'Qty produk'
            );
            $totalQuantity = $regularQuantity;
        }

        $originalTotalAmount = $this->calculator->grossTotal($unitPrice, $totalQuantity);
        $config = $this->findActiveConfig($companyCode, $productId);

        if ($config) {
            $split = $this->calculator->calculate([
                'qty_total' => $totalQuantity,
                'qty_anulir' => 0,
                'qty_bonus' => $bonusQuantity,
                'rate_pabrik' => $config->porsi_pabrik,
                'rate_owner' => $config->porsi_owner,
                'rate_perusahaan' => $config->porsi_perusahaan,
                'total_produk' => $originalTotalAmount,
                'sisa_ke' => $config->remaining_allocation,
            ]);
            $this->calculator->assertSavable($split);
            $configId = $config->id;
        } else {
            $effectiveTotal = $this->calculator->grossTotal($unitPrice, $regularQuantity);
            $split = [
                'qty_efektif' => $regularQuantity,
                'total_efektif' => $effectiveTotal,
                'porsi_perusahaan' => $effectiveTotal,
                'porsi_owner' => 0,
                'porsi_pabrik' => 0,
                'remaining_allocation' => 'perusahaan',
                'remaining_amount' => 0,
            ];
            $configId = null;
        }

        DB::table($this->tableName)->insert([
            'company_code' => $companyCode,
            'f_sales_order_id' => $salesOrderId,
            'f_sales_order_product_id' => $firstRowId,
            'f_product_id' => $productId,
            'product_unit_price' => $this->calculator->rupiah($unitPrice),
            'product_quantity' => $totalQuantity,
            'return_qty' => 0,
            'effective_qty' => $split['qty_efektif'],
            'product_total_amount' => $split['total_efektif'],
            'porsi_perusahaan' => $split['porsi_perusahaan'],
            'porsi_owner' => $split['porsi_owner'],
            'porsi_pabrik' => $split['porsi_pabrik'],
            'remaining_allocation' => $split['remaining_allocation'],
            'remaining_amount' => $split['remaining_amount'],
            'f_revenue_split_config_id' => $configId,
            'is_custom_split' => false,
            'created_by' => $userId,
            'updated_by' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $split;
    }

    public function getRevenueSplitsForSalesOrder($salesOrderId)
    {
        try {
            $companyCode = session('user_company_code');

            return DB::table($this->tableName.' as rs')
                ->leftJoin('m_products as p', 'rs.f_product_id', '=', 'p.id')
                ->leftJoin('m_units as u', 'p.f_unit_id', '=', 'u.id')
                ->leftJoin($this->configTableName.' as rc', 'rs.f_revenue_split_config_id', '=', 'rc.id')
                ->where('rs.company_code', $companyCode)
                ->where('rs.f_sales_order_id', $salesOrderId)
                ->select(
                    'rs.*',
                    'p.code as product_code',
                    'p.name as product_name',
                    'u.name as unit_name',
                    'rc.base_price as config_base_price',
                    'rc.porsi_pabrik as config_pabrik',
                    'rc.porsi_owner as config_owner',
                    'rc.porsi_perusahaan as config_perusahaan',
                    DB::raw('(
                        SELECT COALESCE(SUM(sop_bonus.quantity), 0)
                        FROM t_sales_order_products AS sop_bonus
                        WHERE sop_bonus.f_sales_order_id = rs.f_sales_order_id
                            AND sop_bonus.f_product_id = rs.f_product_id
                            AND sop_bonus.is_bonus = 1
                    ) AS bonus_qty'),
                    DB::raw('(rs.product_unit_price * rs.product_quantity) AS original_total_amount')
                )
                ->orderBy('rs.id')
                ->get();
        } catch (Exception $e) {
            throw new Exception('Failed to get revenue splits: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Save a custom override after recalculating all trusted quantity and total
     * fields on the server.
     */
    public function updateRevenueSplit($salesOrderId, $productId, $splitData)
    {
        $companyCode = session('user_company_code');
        $userId = Auth::id();
        $existingSplit = DB::table($this->tableName)
            ->where('company_code', $companyCode)
            ->where('f_sales_order_id', $salesOrderId)
            ->where('f_product_id', $productId)
            ->first();

        if (! $existingSplit) {
            throw new InvalidArgumentException('Revenue split produk tidak ditemukan.');
        }

        $config = $existingSplit->f_revenue_split_config_id
            ? DB::table($this->configTableName)
                ->where('id', $existingSplit->f_revenue_split_config_id)
                ->first()
            : null;
        $config ??= $this->findActiveConfig($companyCode, $productId);

        if (! $config) {
            throw new InvalidArgumentException('Konfigurasi revenue split produk tidak ditemukan.');
        }

        $returnQty = $this->calculator->wholeQuantity(
            $splitData['return_qty'] ?? 0,
            'Qty anulir'
        );
        $bonusQuantity = DB::table('t_sales_order_products')
            ->where('f_sales_order_id', $salesOrderId)
            ->where('f_product_id', $productId)
            ->where('is_bonus', true)
            ->sum('quantity');
        $originalTotalAmount = $this->calculator->grossTotal(
            $existingSplit->product_unit_price,
            $existingSplit->product_quantity
        );

        $calculation = $this->calculator->calculate([
            'qty_total' => $existingSplit->product_quantity,
            'qty_anulir' => $returnQty,
            'qty_bonus' => $bonusQuantity,
            'rate_pabrik' => $config->porsi_pabrik,
            'rate_owner' => $config->porsi_owner,
            'rate_perusahaan' => $config->porsi_perusahaan,
            'total_produk' => $originalTotalAmount,
            'sisa_ke' => $splitData['remaining_allocation'],
        ]);
        $this->calculator->assertSavable($calculation);

        $submittedPabrik = $this->calculator->rupiah($splitData['porsi_pabrik']);
        $submittedOwner = $this->calculator->rupiah($splitData['porsi_owner']);
        $submittedPerusahaan = $this->calculator->rupiah($splitData['porsi_perusahaan']);
        $submittedTotal = $submittedPabrik + $submittedOwner + $submittedPerusahaan;

        if ($submittedPabrik < 0 || $submittedOwner < 0 || $submittedPerusahaan < 0) {
            throw new InvalidArgumentException('Porsi revenue tidak boleh negatif.');
        }

        if ($submittedPabrik < $calculation['factory_obligation']) {
            throw new InvalidArgumentException(sprintf(
                'Porsi Pabrik minimal Rp %s.',
                number_format($calculation['factory_obligation'], 0, ',', '.')
            ));
        }

        if ($submittedTotal !== $calculation['required_split_total']) {
            throw new InvalidArgumentException(sprintf(
                'Total porsi harus sama dengan total wajib dibagikan Rp %s.',
                number_format($calculation['required_split_total'], 0, ',', '.')
            ));
        }

        DB::table($this->tableName)
            ->where('id', $existingSplit->id)
            ->update([
                'product_total_amount' => $calculation['total_efektif'],
                'porsi_perusahaan' => $submittedPerusahaan,
                'porsi_owner' => $submittedOwner,
                'porsi_pabrik' => $submittedPabrik,
                'remaining_allocation' => $calculation['remaining_allocation'],
                'remaining_amount' => $calculation['remaining_amount'],
                'return_qty' => $calculation['qty_anulir'],
                'effective_qty' => $calculation['qty_efektif'],
                'is_custom_split' => true,
                'split_notes' => $splitData['split_notes'] ?? null,
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);

        return $calculation;
    }

    public function deleteRevenueSplitsForSalesOrder($salesOrderId)
    {
        try {
            DB::table($this->tableName)
                ->where('company_code', session('user_company_code'))
                ->where('f_sales_order_id', $salesOrderId)
                ->delete();

            return true;
        } catch (Exception $e) {
            throw new Exception('Failed to delete revenue splits: '.$e->getMessage(), 0, $e);
        }
    }

    public function getRevenueSplitSummary($salesOrderId)
    {
        try {
            return DB::table($this->tableName)
                ->where('company_code', session('user_company_code'))
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
        } catch (Exception $e) {
            throw new Exception('Failed to get revenue split summary: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Rebuild splits from the current sales order and active configurations.
     * Custom portions are intentionally not carried across changed quantities.
     */
    public function recalculateRevenueSplits($salesOrderId, $updatedProducts)
    {
        return $this->createRevenueSplitsForSalesOrder($salesOrderId, $updatedProducts);
    }

    private function findActiveConfig($companyCode, $productId)
    {
        return DB::table($this->configTableName)
            ->where('company_code', $companyCode)
            ->where('f_product_id', $productId)
            ->where('status', 'ACTIVE')
            ->first();
    }
}
