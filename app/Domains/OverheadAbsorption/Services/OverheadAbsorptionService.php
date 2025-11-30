<?php

namespace App\Domains\OverheadAbsorption\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverheadAbsorptionService
{
    private $header_table = 't_overhead_absorptions';
    private $production_detail_table = 't_overhead_absorption_productions';
    private $expense_detail_table = 't_overhead_absorption_expenses';
    private $production_table = 't_productions';
    private $expense_table = 't_expenses';

    /**
     * Get production data for a specific period (month-year)
     */
    public function getProductionDataByPeriod(string $period): array
    {
        // Parse period (format: YYYY-MM)
        [$year, $month] = explode('-', $period);

        $productions = DB::table($this->production_table . ' as p')
            ->select(
                'p.id',
                'p.production_date',
                'p.production_number',
                'p.bundle_product_id',
                'bp.name as product_name',
                'bp.code as product_code',
                'p.quantity_planned as qty',
                'p.total_component_cost_unit as direct_cost',
                'p.bundle_price as production_overhead',
                DB::raw('(p.total_component_cost_unit + p.bundle_price) as total_cost'),
                DB::raw('ROUND((p.total_component_cost_unit + p.bundle_price) / p.quantity_planned, 2) as cost_per_unit'),
                'hpp_production'
            )
            ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->where('p.company_code', session('user_company_code'))
            ->where('p.status', 'COMPLETED')
            ->where('p.is_overhead_absorbed', false)
            ->whereYear('p.production_date', $year)
            ->whereMonth('p.production_date', $month)
            ->orderBy('p.production_date', 'ASC')
            ->get()
            ->toArray();

        // Calculate totals
        $totalQty = array_sum(array_column($productions, 'qty'));
        $totalDirectCost = array_sum(array_column($productions, 'direct_cost'));
        $totalOverhead = array_sum(array_column($productions, 'production_overhead'));

        return [
            'productions' => $productions,
            'summary' => [
                'total_qty' => $totalQty,
                'total_direct_cost' => $totalDirectCost,
                'total_production_overhead' => $totalOverhead,
                'batch_count' => count($productions),
            ],
        ];
    }

    /**
     * Get overhead expenses for the period
     */
    public function getOverheadExpensesByPeriod(string $period): array
    {
        // Parse period (format: YYYY-MM)
        [$year, $month] = explode('-', $period);

        $expenses = DB::table($this->expense_table . ' as e')
            ->select(
                'e.id',
                'e.transaction_no',
                'e.transaction_date',
                'e.description',
                'e.total_amount',
                'e.memo'
            )
            ->where('e.company_code', session('user_company_code'))
            ->where('e.category_type', 'OVERHEAD')
            ->where('e.is_overhead_absorbed', false)
            ->whereYear('e.transaction_date', $year)
            ->whereMonth('e.transaction_date', $month)
            ->orderBy('e.transaction_date', 'ASC')
            ->get()
            ->toArray();

        $totalOverheadExpenses = array_sum(array_column($expenses, 'total_amount'));

        return [
            'expenses' => $expenses,
            'total' => $totalOverheadExpenses,
        ];
    }

    /**
     * Calculate overhead absorption preview
     */
    public function calculatePreview(string $period): array
    {
        // Check if already settled for this period
        $existingSettlement = DB::table($this->header_table)
            ->where('company_code', session('user_company_code'))
            ->where('period', $period)
            ->where('is_settled', true)
            ->first();

        if ($existingSettlement) {
            // Period already settled, return data from settlement record
            return $this->getSettledPeriodData($existingSettlement->id);
        }

        // Not settled yet, get fresh data from production and expense tables
        $productionData = $this->getProductionDataByPeriod($period);
        $expenseData = $this->getOverheadExpensesByPeriod($period);

        $totalQty = $productionData['summary']['total_qty'];
        $totalProductionOverhead = $productionData['summary']['total_production_overhead'];
        $totalExpenseOverhead = $expenseData['total'];
        $grandTotalOverhead = $totalProductionOverhead + $totalExpenseOverhead;

        if ($totalQty == 0) {
            return [
                'error' => 'No production found for the selected period',
                'can_settle' => false,
                'is_settled' => false,
            ];
        }

        // Calculate overhead per unit using only expense overhead (End-of-Month Overhead)
        // Production overhead is already included in hpp_production
        $overheadPerUnit = $totalQty > 0 ? $totalExpenseOverhead / $totalQty : 0;

        // Calculate HPP for each production (preview only - no overhead/hpp shown)
        $productionsWithHPP = [];
        foreach ($productionData['productions'] as $production) {
            $allocatedOverhead = $production->qty * $overheadPerUnit;
            $hppSementara = $production->hpp_production ?? 0;
            $hppPerUnit = $hppSementara + $overheadPerUnit;

            $productionsWithHPP[] = [
                'production_id' => $production->id,
                'production_number' => $production->production_number,
                'product_name' => $production->product_name,
                'product_code' => $production->product_code,
                'production_date' => $production->production_date,
                'qty' => $production->qty,
                'direct_cost' => $production->direct_cost,
                'production_overhead' => $production->production_overhead,
                'hpp_sementara' => $hppSementara, // HPP from production table before overhead
                'allocated_overhead' => round($allocatedOverhead, 2),
                'hpp_per_unit' => round($hppPerUnit, 2),
            ];
        }

        return [
            'period' => $period,
            'total_qty' => $totalQty,
            'total_production_overhead' => $totalProductionOverhead,
            'total_expense_overhead' => $totalExpenseOverhead,
            'grand_total_overhead' => $grandTotalOverhead,
            'overhead_per_unit' => round($overheadPerUnit, 2),
            'productions' => $productionsWithHPP,
            'expenses' => $expenseData['expenses'],
            'batch_count' => $productionData['summary']['batch_count'],
            'can_settle' => true,
            'is_settled' => false,
        ];
    }

    /**
     * Get settled period data
     */
    private function getSettledPeriodData(int $settlementId): array
    {
        $details = $this->getSettlementDetails($settlementId);
        $settlement = $details['settlement'];

        // Format productions with settled data - get production dates
        $productionsFormatted = [];
        $totalProductionOverhead = 0;
        foreach ($details['productions'] as $prod) {
            // Get production date from actual production table
            $production = DB::table($this->production_table)
                ->where('id', $prod->f_production_id)
                ->first();

            // Calculate production overhead from direct cost
            $prodOverhead = $production ? $production->bundle_price : 0;
            $totalProductionOverhead += $prodOverhead;

            // Get HPP Sementara from production table (hpp_production field)
            $hppSementara = $production ? ($production->hpp_production ?? 0) : 0;

            $productionsFormatted[] = [
                'production_id' => $prod->f_production_id,
                'production_number' => $prod->production_number,
                'product_name' => $prod->product_name,
                'product_code' => $prod->product_code ?? null,
                'production_date' => $production ? $production->production_date : null,
                'qty' => $prod->production_qty,
                'direct_cost' => $production ? $production->total_component_cost_unit : 0,
                'production_overhead' => $prodOverhead,
                'hpp_sementara' => $hppSementara, // HPP from production table before overhead
                'allocated_overhead' => $prod->overhead_allocated,
                'hpp_per_unit' => $prod->hpp_per_unit,
            ];
        }

        $totalExpenseOverhead = array_sum(array_column($details['expenses'], 'expense_amount'));

        return [
            'period' => $settlement->period,
            'total_qty' => $settlement->total_production_qty,
            'total_production_overhead' => $totalProductionOverhead,
            'total_expense_overhead' => $totalExpenseOverhead,
            'grand_total_overhead' => $settlement->total_overhead_cost,
            'overhead_per_unit' => $settlement->overhead_per_unit,
            'productions' => $productionsFormatted,
            'expenses' => $details['expenses'],
            'batch_count' => count($productionsFormatted),
            'can_settle' => false,
            'is_settled' => true,
            'settlement_id' => $settlementId,
            'settlement_no' => $settlement->transaction_no,
            'settlement_date' => $settlement->settlement_date,
        ];
    }

    /**
     * Settle overhead absorption
     */
    public function settleOverhead(string $period, ?string $notes = null): array
    {
        return DB::transaction(function () use ($period, $notes) {
            $preview = $this->calculatePreview($period);

            if (!$preview['can_settle']) {
                throw new \Exception($preview['error'] ?? 'Cannot settle overhead');
            }

            // Check if already settled
            $existing = DB::table($this->header_table)
                ->where('company_code', session('user_company_code'))
                ->where('period', $period)
                ->where('is_settled', true)
                ->first();

            if ($existing) {
                throw new \Exception('Overhead for this period has already been settled');
            }

            // Generate transaction number
            $transactionNo = $this->generateTransactionNumber($period);

            // Create header record
            $headerId = DB::table($this->header_table)->insertGetId([
                'company_code' => session('user_company_code'),
                'transaction_no' => $transactionNo,
                'period' => $period,
                'settlement_date' => now(),
                'total_production_qty' => $preview['total_qty'],
                'total_overhead_cost' => $preview['grand_total_overhead'],
                'overhead_per_unit' => $preview['overhead_per_unit'],
                'is_settled' => true,
                'notes' => $notes,
                'created_by' => Auth::id(),
                'created_at' => now(),
            ]);

            // Create production detail records and update productions
            foreach ($preview['productions'] as $prod) {
                // Insert detail
                DB::table($this->production_detail_table)->insert([
                    'f_overhead_absorption_id' => $headerId,
                    'f_production_id' => $prod['production_id'],
                    'production_qty' => $prod['qty'],
                    'direct_cost' => $prod['direct_cost'] + $prod['production_overhead'],
                    'overhead_allocated' => $prod['allocated_overhead'],
                    'hpp_per_unit' => $prod['hpp_per_unit'],
                    'created_at' => now(),
                ]);

                // Update production
                DB::table($this->production_table)
                    ->where('id', $prod['production_id'])
                    ->update([
                        'is_overhead_absorbed' => true,
                        'f_overhead_absorption_id' => $headerId,
                        'allocated_overhead' => $prod['allocated_overhead'],
                        'hpp_with_overhead' => $prod['hpp_per_unit'],
                        'updated_by' => Auth::id(),
                        'updated_at' => now(),
                    ]);
                DB::table('t_product_stock_batches')
                    ->where('f_source_transaction_id', $prod['production_id'])
                    ->update([
                        'final_hpp' => $prod['hpp_per_unit'],
                        'hpp_status' => 'SETTLED',
                        'updated_at' => now(),
                    ]);
            }

            // Create expense detail records and update expenses
            foreach ($preview['expenses'] as $expense) {
                // Insert detail
                DB::table($this->expense_detail_table)->insert([
                    'f_overhead_absorption_id' => $headerId,
                    'f_expense_id' => $expense->id,
                    'expense_amount' => $expense->total_amount,
                    'created_at' => now(),
                ]);

                // Update expense
                DB::table($this->expense_table)
                    ->where('id', $expense->id)
                    ->update([
                        'is_overhead_absorbed' => true,
                        'f_overhead_absorption_id' => $headerId,
                        'updated_at' => now(),
                    ]);
            }
            return [
                'header_id' => $headerId,
                'transaction_no' => $transactionNo,
                'productions_count' => count($preview['productions']),
                'expenses_count' => count($preview['expenses']),
                'total_overhead' => $preview['grand_total_overhead'],
            ];
        });
    }

    /**
     * Get settlement history
     */
    public function getSettlementHistory(): array
    {
        $settlements = DB::table($this->header_table . ' as oa')
            ->select(
                'oa.*',
                'u.name as created_by_name'
            )
            ->leftJoin('users as u', 'oa.created_by', '=', 'u.id')
            ->where('oa.company_code', session('user_company_code'))
            ->orderBy('oa.period', 'DESC')
            ->get()
            ->toArray();

        return $settlements;
    }

    /**
     * Get settlement details by ID
     */
    public function getSettlementDetails(int $id): array
    {
        $settlement = DB::table($this->header_table . ' as oa')
            ->select('oa.*', 'u.name as created_by_name')
            ->leftJoin('users as u', 'oa.created_by', '=', 'u.id')
            ->where('oa.id', $id)
            ->where('oa.company_code', session('user_company_code'))
            ->first();

        if (!$settlement) {
            throw new \Exception('Settlement not found');
        }

        $productions = DB::table($this->production_detail_table . ' as oaprod')
            ->select(
                'oaprod.*',
                'p.production_number',
                'bp.name as product_name',
                'bp.code as product_code'
            )
            ->leftJoin('t_productions as p', 'oaprod.f_production_id', '=', 'p.id')
            ->leftJoin('m_products as bp', 'p.bundle_product_id', '=', 'bp.id')
            ->where('oaprod.f_overhead_absorption_id', $id)
            ->get()
            ->toArray();

        $expenses = DB::table($this->expense_detail_table . ' as oaexp')
            ->select(
                'oaexp.*',
                'e.transaction_no',
                'e.transaction_date',
                'e.description',
                'e.total_amount'
            )
            ->leftJoin('t_expenses as e', 'oaexp.f_expense_id', '=', 'e.id')
            ->where('oaexp.f_overhead_absorption_id', $id)
            ->get()
            ->toArray();

        return [
            'settlement' => $settlement,
            'productions' => $productions,
            'expenses' => $expenses,
        ];
    }

    /**
     * Generate transaction number
     */
    private function generateTransactionNumber(string $period): string
    {
        $prefix = 'OHA';
        $periodCode = str_replace('-', '', $period);

        $lastNumber = DB::table($this->header_table)
            ->where('company_code', session('user_company_code'))
            ->where('transaction_no', 'like', $prefix . '/' . $periodCode . '/%')
            ->orderBy('id', 'desc')
            ->value('transaction_no');

        if ($lastNumber) {
            $parts = explode('/', $lastNumber);
            $sequence = intval(end($parts)) + 1;
        } else {
            $sequence = 1;
        }

        return sprintf('%s/%s/%04d', $prefix, $periodCode, $sequence);
    }
}
