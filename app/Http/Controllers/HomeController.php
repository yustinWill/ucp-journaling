<?php

namespace App\Http\Controllers;

use App\Domains\Budged\Services\BudgedService;
use App\Domains\RevenueSplit\Services\RevenueSplitService;
use App\Domains\StockMovement\Services\StockMovementLogsService;
use App\Domains\Warehouse\Services\WarehouseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $req): View
    {
        return view('pages.home');
    }

    public function dashboard(Request $req): View
    {
        $companyCode = session('user_company_code');

        // Parse date filter (default: 30 days before)
        $isLifetime = $req->boolean('lifetime', false);
        $dateFrom = $req->input('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $req->input('date_to', now()->format('Y-m-d'));

        // ============================================================
        // Section 1: SO / SD / SI Summary Cards (filtered by period)
        // ============================================================
        $soQuery = DB::table('t_sales_orders')->where('company_code', $companyCode);
        $sdQuery = DB::table('t_sales_deliveries')
            ->join('t_sales_orders as so', 't_sales_deliveries.sales_order_id', '=', 'so.id')
            ->where('so.company_code', $companyCode);
        $siQuery = DB::table('t_sales_invoices as si')
            ->join('t_sales_orders as so', 'si.f_sales_order_id', '=', 'so.id')
            ->where('so.company_code', $companyCode);

        if (!$isLifetime) {
            $soQuery->whereBetween('transaction_date', [$dateFrom, $dateTo]);
            $sdQuery->whereBetween('t_sales_deliveries.delivery_date', [$dateFrom, $dateTo]);
            $siQuery->whereBetween('si.invoice_date', [$dateFrom, $dateTo]);
        }

        $soTotal = $soQuery->count();

        $sdCounts = (clone $sdQuery)->selectRaw("
            SUM(CASE WHEN t_sales_deliveries.is_confirmed = '0' OR t_sales_deliveries.is_confirmed IS NULL THEN 1 ELSE 0 END) as non_confirm,
            SUM(CASE WHEN t_sales_deliveries.is_confirmed = '1' THEN 1 ELSE 0 END) as confirmed
        ")->first();
        $sdNonConfirm = (int) ($sdCounts->non_confirm ?? 0);
        $sdConfirm = (int) ($sdCounts->confirmed ?? 0);

        $siAmounts = (clone $siQuery)->selectRaw("
            COALESCE(SUM(si.grand_total), 0) as expected,
            COALESCE(SUM(CASE WHEN si.status = 'PAID' THEN si.grand_total ELSE 0 END), 0) as settled
        ")->first();
        $siExpected = 'Rp ' . number_format($siAmounts->expected ?? 0, 0, ',', '.');
        $siSettled = 'Rp ' . number_format($siAmounts->settled ?? 0, 0, ',', '.');

        // ============================================================
        // Section 2: Daily Sales Chart (filtered by period)
        // ============================================================
        $chartFrom = $isLifetime
            ? DB::table('t_sales_orders')->where('company_code', $companyCode)->min('transaction_date') ?? now()->subDays(30)->format('Y-m-d')
            : $dateFrom;
        $chartTo = $isLifetime ? now()->format('Y-m-d') : $dateTo;

        // Get daily amounts from SO
        $dailyAmounts = DB::table('t_sales_orders')
            ->where('company_code', $companyCode)
            ->whereBetween('transaction_date', [$chartFrom, $chartTo])
            ->groupBy('transaction_date')
            ->orderBy('transaction_date')
            ->select([
                'transaction_date as date',
                DB::raw('COALESCE(SUM(grand_price), 0) as amount'),
            ])
            ->get()
            ->keyBy('date');

        // Get daily qty from SO products
        $dailyQty = DB::table('t_sales_order_products as sop')
            ->join('t_sales_orders as so', 'sop.f_sales_order_id', '=', 'so.id')
            ->where('so.company_code', $companyCode)
            ->whereBetween('so.transaction_date', [$chartFrom, $chartTo])
            ->groupBy('so.transaction_date')
            ->select([
                'so.transaction_date as date',
                DB::raw('COALESCE(SUM(sop.quantity), 0) as qty'),
            ])
            ->get()
            ->keyBy('date');

        // Build continuous date array (fill missing dates with 0)
        $dailySales = [];
        $period = new \DatePeriod(
            new \DateTime($chartFrom),
            new \DateInterval('P1D'),
            (new \DateTime($chartTo))->modify('+1 day')
        );
        foreach ($period as $day) {
            $dateKey = $day->format('Y-m-d');
            $amountRow = $dailyAmounts->get($dateKey);
            $qtyRow = $dailyQty->get($dateKey);
            $dailySales[] = [
                'date' => $dateKey,
                'label' => $day->format('d M'),
                'qty' => (int) ($qtyRow->qty ?? 0),
                'amount' => (float) ($amountRow->amount ?? 0),
            ];
        }

        // ============================================================
        // Section 3: Top Up Cards (always lifetime)
        // ============================================================
        $topupStats = DB::table('t_topup')
            ->where('company_code', $companyCode)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN status IN ('OPEN', 'PARTIAL') THEN remaining_balance ELSE 0 END), 0) as belum_bayar,
                COALESCE(SUM(allocated_amount), 0) as terbayar
            ")
            ->first();
        $topupBelumBayar = 'Rp ' . number_format($topupStats->belum_bayar ?? 0, 0, ',', '.');
        $topupTerbayar = 'Rp ' . number_format($topupStats->terbayar ?? 0, 0, ',', '.');

        // ============================================================
        // Section 4: Target Keuntungan (Revenue Split Cycles)
        // ============================================================
        $revenueSplitService = app(RevenueSplitService::class);
        $allCycles = $revenueSplitService->findAll();

        $revenueSplitOptions = $allCycles->map(function ($cycle) {
            $statusLabel = $cycle->status === 'ACTIVE' ? ' (ACTIVE)' : '';
            return ['id' => $cycle->id, 'label' => $cycle->cycle_name . $statusLabel];
        })->values()->toArray();

        // Default to the selected cycle or the first one
        $selectedCycleId = $req->input('revenue_split_id', $allCycles->first()->id ?? null);
        $selectedCycle = $selectedCycleId ? $revenueSplitService->findOne($selectedCycleId) : null;

        $activeRevenueSplit = null;
        if ($selectedCycle) {
            // Get accumulated revenue splits within this cycle's date range
            $accumulated = DB::table('t_sales_order_revenue_splits as rs')
                ->join('t_sales_orders as so', 'rs.f_sales_order_id', '=', 'so.id')
                ->where('rs.company_code', $companyCode)
                ->whereBetween('so.transaction_date', [$selectedCycle->start_date, $selectedCycle->end_date])
                ->selectRaw("
                    COALESCE(SUM(rs.porsi_pabrik), 0) as current_pabrik,
                    COALESCE(SUM(rs.porsi_owner), 0) as current_owner,
                    COALESCE(SUM(rs.porsi_perusahaan), 0) as current_perusahaan
                ")
                ->first();

            $activeRevenueSplit = (object) [
                'id' => $selectedCycle->id,
                'cycle_name' => $selectedCycle->cycle_name,
                'start_date' => $selectedCycle->start_date,
                'end_date' => $selectedCycle->end_date,
                'status' => $selectedCycle->status,
                'target_pabrik' => $selectedCycle->target_pabrik,
                'target_owner' => $selectedCycle->target_owner,
                'target_perusahaan' => $selectedCycle->target_perusahaan,
                'total_target' => $selectedCycle->total_target,
                'current_pabrik' => (float) ($accumulated->current_pabrik ?? 0),
                'current_owner' => (float) ($accumulated->current_owner ?? 0),
                'current_perusahaan' => (float) ($accumulated->current_perusahaan ?? 0),
            ];
        }

        // ============================================================
        // Section 5: Anggaran (Budget) - Owner & Kingdom
        // ============================================================
        $budgedService = app(BudgedService::class);

        // Helper to build budget card data for a given type
        $buildBudgetData = function ($type) use ($req, $budgedService) {
            $paramKey = 'budget_' . $type . '_id';
            $periods = DB::table('m_budget_periods')
                ->where('type', $type)
                ->orderBy('created_at', 'desc')
                ->get();

            $options = $periods->map(function ($p) {
                return [
                    'id' => $p->id,
                    'label' => date('d M Y', strtotime($p->period_start_date)) . ' s/d ' . date('d M Y', strtotime($p->period_end_date)),
                ];
            })->values()->toArray();

            $selectedId = $req->input($paramKey, $periods->first()->id ?? null);
            $data = null;

            if ($selectedId) {
                $period = $budgedService->findPeriodById($selectedId);
                $budgets = $budgedService->findByPeriodId($selectedId);

                if ($budgets->isNotEmpty()) {
                    $categories = $budgets->map(function ($b) {
                        $used = $b->budged_limit_value - $b->budged_remaining_value;
                        $catName = $b->budged_name;
                        if ($b->f_budget_category_id) {
                            $cat = DB::table('m_budget_categories')->where('id', $b->f_budget_category_id)->first();
                            if ($cat) $catName = $cat->category_name;
                        }
                        return (object) [
                            'name' => $catName,
                            'limit' => (float) $b->budged_limit_value,
                            'used' => (float) $used,
                        ];
                    })->values()->toArray();

                    $data = (object) [
                        'id' => $period->id,
                        'type' => $type,
                        'period_start_date' => $period->period_start_date,
                        'period_end_date' => $period->period_end_date,
                        'categories' => $categories,
                    ];
                }
            }

            return [
                'options' => $options,
                'selectedId' => $selectedId,
                'data' => $data,
            ];
        };

        $budgetOwner = $buildBudgetData('owner');
        $budgetKingdom = $buildBudgetData('kingdom');

        // ============================================================
        // Section 6: Warehouse Stock
        // ============================================================
        $warehouseService = app(WarehouseService::class);
        $warehouses = $warehouseService->findAll();

        $warehouseOptions = $warehouses->map(function ($w) {
            return ['id' => $w->id, 'name' => $w->name];
        })->values()->toArray();

        $selectedWarehouseId = $req->input('warehouse_id', $warehouses->first()->id ?? null);
        $warehouseStock = [];

        if ($selectedWarehouseId) {
            $stockData = app(StockMovementLogsService::class)->findAllLatestStockPerWarehouse($selectedWarehouseId);
            $warehouseStock = $stockData->map(function ($item) {
                return [
                    'name' => $item->name,
                    'qty' => (float) $item->system_stock,
                    'minimum_stock' => (float) ($item->minimum_stock ?? 0),
                ];
            })->values()->toArray();
        }

        return view('pages.dashboard', compact(
            'soTotal', 'sdNonConfirm', 'sdConfirm', 'siExpected', 'siSettled',
            'dailySales',
            'topupBelumBayar', 'topupTerbayar',
            'revenueSplitOptions', 'activeRevenueSplit',
            'budgetOwner', 'budgetKingdom',
            'warehouseOptions', 'warehouseStock',
            'dateFrom', 'dateTo', 'isLifetime',
            'selectedCycleId', 'selectedWarehouseId'
        ));
    }

    public function dashboardSummary(Request $req): View
    {
        // Get period from request or default to monthly
        $period = $req->input('period', 'monthly');
        $dateFrom = $req->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $req->input('date_to', now()->endOfMonth()->format('Y-m-d'));

        // Mock data for PABRIK Tab
        $sales_pabrik_data = collect([
            (object)[
                'date' => '2025-12-12',
                'destination' => 'Kingdom',
                'product' => 'RIYO Water 600ml',
                'qty' => 1000,
                'transfer_price' => 15000,
                'total_value' => 15000000,
                'status' => 'PAID',
            ],
            (object)[
                'date' => '2025-12-11',
                'destination' => 'Owner',
                'product' => 'RIYO Water 1500ml',
                'qty' => 500,
                'transfer_price' => 40000,
                'total_value' => 20000000,
                'status' => 'OPEN',
            ],
            (object)[
                'date' => '2025-12-10',
                'destination' => 'Kingdom',
                'product' => 'RIYO Water 330ml',
                'qty' => 2000,
                'transfer_price' => 4000,
                'total_value' => 8000000,
                'status' => 'PARTIAL',
            ],
            (object)[
                'date' => '2025-12-09',
                'destination' => 'Owner',
                'product' => 'RIYO Water 19L Galon',
                'qty' => 300,
                'transfer_price' => 20000,
                'total_value' => 6000000,
                'status' => 'PAID',
            ],
            (object)[
                'date' => '2025-12-08',
                'destination' => 'Kingdom',
                'product' => 'RIYO Water 240ml Cup',
                'qty' => 1500,
                'transfer_price' => 3500,
                'total_value' => 5250000,
                'status' => 'PAID',
            ],
        ]);

        $piutang_pabrik_data = collect([
            (object)[
                'source_entity' => 'Kingdom',
                'ref_sales' => 'SK-001',
                'receivable_amount' => 15000000,
                'paid_amount' => 15000000,
                'remaining_amount' => 0,
                'status' => 'PAID',
            ],
            (object)[
                'source_entity' => 'Owner',
                'ref_sales' => 'SO-002',
                'receivable_amount' => 20000000,
                'paid_amount' => 0,
                'remaining_amount' => 20000000,
                'status' => 'OPEN',
            ],
            (object)[
                'source_entity' => 'Kingdom',
                'ref_sales' => 'SK-003',
                'receivable_amount' => 8000000,
                'paid_amount' => 3000000,
                'remaining_amount' => 5000000,
                'status' => 'PARTIAL',
            ],
            (object)[
                'source_entity' => 'Owner',
                'ref_sales' => 'SO-004',
                'receivable_amount' => 6000000,
                'paid_amount' => 6000000,
                'remaining_amount' => 0,
                'status' => 'PAID',
            ],
        ]);

        // Mock data for KINGDOM Tab
        $sales_kingdom_data = collect([
            (object)[
                'date' => '2025-12-13',
                'customer' => 'Toko Segar Mart',
                'product' => 'RIYO Water 600ml',
                'qty' => 800,
                'price' => 25000,
                'total' => 20000000,
                'status' => 'PAID',
            ],
            (object)[
                'date' => '2025-12-12',
                'customer' => 'Minimarket Berkah',
                'product' => 'RIYO Water 1500ml',
                'qty' => 400,
                'price' => 65000,
                'total' => 26000000,
                'status' => 'PARTIAL',
            ],
            (object)[
                'date' => '2025-12-11',
                'customer' => 'Warung Bu Ita',
                'product' => 'RIYO Water 330ml',
                'qty' => 1200,
                'price' => 8500,
                'total' => 10200000,
                'status' => 'OPEN',
            ],
        ]);

        // Mock data for OWNER Tab
        $sales_owner_data = collect([
            (object)[
                'date' => '2025-12-13',
                'customer' => 'Hotel Grand Deluxe',
                'product' => 'RIYO Water 330ml',
                'qty' => 2000,
                'price' => 8500,
                'total' => 17000000,
                'status' => 'PAID',
            ],
            (object)[
                'date' => '2025-12-12',
                'customer' => 'Restoran Seafood Asli',
                'product' => 'RIYO Water 600ml',
                'qty' => 600,
                'price' => 25000,
                'total' => 15000000,
                'status' => 'PARTIAL',
            ],
            (object)[
                'date' => '2025-12-10',
                'customer' => 'Kantin Pabrik ABC',
                'product' => 'RIYO Water 19L Galon',
                'qty' => 200,
                'price' => 35000,
                'total' => 7000000,
                'status' => 'OPEN',
            ],
        ]);

        // Calculate summary values
        $sales_pabrik = $sales_pabrik_data->sum('total_value');
        $piutang_pabrik = $piutang_pabrik_data->sum('remaining_amount');
        $piutang_count = $piutang_pabrik_data->where('status', '!=', 'PAID')->count();

        $sales_kingdom = $sales_kingdom_data->sum('total');
        $pengeluaran_kingdom = 12000000; // Mock operational expenses

        $sales_owner = $sales_owner_data->sum('total');
        $pengeluaran_owner = 8500000; // Mock operational expenses

        $summaryData = [
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,

            // PABRIK Tab Data
            'sales_pabrik' => $sales_pabrik,
            'piutang_pabrik' => $piutang_pabrik,
            'piutang_count' => $piutang_count,
            'sales_pabrik_data' => $sales_pabrik_data,
            'piutang_pabrik_data' => $piutang_pabrik_data,

            // KINGDOM Tab Data
            'sales_kingdom' => $sales_kingdom,
            'pengeluaran_kingdom' => $pengeluaran_kingdom,
            'sales_kingdom_data' => $sales_kingdom_data,

            // OWNER Tab Data
            'sales_owner' => $sales_owner,
            'pengeluaran_owner' => $pengeluaran_owner,
            'sales_owner_data' => $sales_owner_data,
        ];

        return view('pages.dashboard_summary', $summaryData);
    }
}
