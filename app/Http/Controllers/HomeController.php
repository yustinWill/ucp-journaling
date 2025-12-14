<?php

namespace App\Http\Controllers;

use App\Domains\RevenueSplit\Services\RevenueSplitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $req): View
    {
        return view('pages.home');
    }

    public function dashboard(Request $req): View
    {
        // Mock dashboard data for RIYO Water - Mineral Water Production Company
        $dashboardData = [
            'summary_cards' => [
                'total_revenue' => [
                    'value' => 'Rp 285.750.000',
                    'change' => '+18.2%',
                    'trend' => 'up',
                ],
                'total_expenses' => [
                    'value' => 'Rp 142.400.000',
                    'change' => '+12.8%',
                    'trend' => 'up',
                ],
                'net_profit' => [
                    'value' => 'Rp 143.350.000',
                    'change' => '+23.7%',
                    'trend' => 'up',
                ],
                'pending_invoices' => [
                    'value' => '18',
                    'change' => '-8.5%',
                    'trend' => 'down',
                ],
            ],
            'recent_transactions' => [
                [
                    'id' => 'INV-2025-0847',
                    'type' => 'sales',
                    'description' => 'Penjualan RIYO Water 600ml ke Toko Segar Mart',
                    'amount' => 'Rp 4.850.000',
                    'date' => '2025-07-17',
                    'status' => 'completed',
                ],
                [
                    'id' => 'PO-2025-0312',
                    'type' => 'purchase',
                    'description' => 'Pembelian Botol PET 600ml dari CV Plastik Prima',
                    'amount' => 'Rp 3.200.000',
                    'date' => '2025-07-16',
                    'status' => 'pending',
                ],
                [
                    'id' => 'INV-2025-0846',
                    'type' => 'sales',
                    'description' => 'Penjualan RIYO Water 1500ml ke Minimarket Berkah',
                    'amount' => 'Rp 2.750.000',
                    'date' => '2025-07-16',
                    'status' => 'completed',
                ],
                [
                    'id' => 'EXP-2025-0198',
                    'type' => 'expense',
                    'description' => 'Biaya Listrik & Mesin Produksi',
                    'amount' => 'Rp 1.850.000',
                    'date' => '2025-07-15',
                    'status' => 'completed',
                ],
                [
                    'id' => 'PO-2025-0311',
                    'type' => 'purchase',
                    'description' => 'Pembelian Label & Kemasan dari PT Grafika Mandiri',
                    'amount' => 'Rp 2.400.000',
                    'date' => '2025-07-15',
                    'status' => 'completed',
                ],
            ],
            'top_products' => [
                ['name' => 'RIYO Water 600ml', 'sales' => 'Rp 85.500.000', 'qty' => 28500],
                ['name' => 'RIYO Water 1500ml', 'sales' => 'Rp 72.000.000', 'qty' => 14400],
                ['name' => 'RIYO Water 330ml', 'sales' => 'Rp 45.800.000', 'qty' => 22900],
                ['name' => 'RIYO Water 19L Galon', 'sales' => 'Rp 58.200.000', 'qty' => 2910],
                ['name' => 'RIYO Water 240ml Cup', 'sales' => 'Rp 24.250.000', 'qty' => 19400],
            ],
            'low_stock_items' => [
                ['name' => 'Botol PET 600ml', 'current_stock' => 850, 'min_stock' => 2000],
                ['name' => 'Label RIYO Water 1500ml', 'current_stock' => 320, 'min_stock' => 1000],
                ['name' => 'Tutup Botol (Cap) 600ml', 'current_stock' => 1250, 'min_stock' => 3000],
                ['name' => 'Kardus Kemasan 24pcs', 'current_stock' => 180, 'min_stock' => 500],
            ],
        ];

        // Add Revenue Split data for Dashboard Keuntungan Penjualan section
        $revenueSplitData = [
            'current_cycle' => app(RevenueSplitService::class)->findAll()->where('status', 'ACTIVE')->first(),
            'revenue_split_summary' => app(RevenueSplitService::class)->getSummaryCards(),
        ];

        return view('pages.dashboard', array_merge($dashboardData, $revenueSplitData));
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
