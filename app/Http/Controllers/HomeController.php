<?php

namespace App\Http\Controllers;

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
                    'trend' => 'up'
                ],
                'total_expenses' => [
                    'value' => 'Rp 142.400.000',
                    'change' => '+12.8%',
                    'trend' => 'up'
                ],
                'net_profit' => [
                    'value' => 'Rp 143.350.000',
                    'change' => '+23.7%',
                    'trend' => 'up'
                ],
                'pending_invoices' => [
                    'value' => '18',
                    'change' => '-8.5%',
                    'trend' => 'down'
                ]
            ],
            'recent_transactions' => [
                [
                    'id' => 'INV-2025-0847',
                    'type' => 'sales',
                    'description' => 'Penjualan RIYO Water 600ml ke Toko Segar Mart',
                    'amount' => 'Rp 4.850.000',
                    'date' => '2025-07-17',
                    'status' => 'completed'
                ],
                [
                    'id' => 'PO-2025-0312',
                    'type' => 'purchase',
                    'description' => 'Pembelian Botol PET 600ml dari CV Plastik Prima',
                    'amount' => 'Rp 3.200.000',
                    'date' => '2025-07-16',
                    'status' => 'pending'
                ],
                [
                    'id' => 'INV-2025-0846',
                    'type' => 'sales',
                    'description' => 'Penjualan RIYO Water 1500ml ke Minimarket Berkah',
                    'amount' => 'Rp 2.750.000',
                    'date' => '2025-07-16',
                    'status' => 'completed'
                ],
                [
                    'id' => 'EXP-2025-0198',
                    'type' => 'expense',
                    'description' => 'Biaya Listrik & Mesin Produksi',
                    'amount' => 'Rp 1.850.000',
                    'date' => '2025-07-15',
                    'status' => 'completed'
                ],
                [
                    'id' => 'PO-2025-0311',
                    'type' => 'purchase',
                    'description' => 'Pembelian Label & Kemasan dari PT Grafika Mandiri',
                    'amount' => 'Rp 2.400.000',
                    'date' => '2025-07-15',
                    'status' => 'completed'
                ]
            ],
            'top_products' => [
                ['name' => 'RIYO Water 600ml', 'sales' => 'Rp 85.500.000', 'qty' => 28500],
                ['name' => 'RIYO Water 1500ml', 'sales' => 'Rp 72.000.000', 'qty' => 14400],
                ['name' => 'RIYO Water 330ml', 'sales' => 'Rp 45.800.000', 'qty' => 22900],
                ['name' => 'RIYO Water 19L Galon', 'sales' => 'Rp 58.200.000', 'qty' => 2910],
                ['name' => 'RIYO Water 240ml Cup', 'sales' => 'Rp 24.250.000', 'qty' => 19400]
            ],
            'low_stock_items' => [
                ['name' => 'Botol PET 600ml', 'current_stock' => 850, 'min_stock' => 2000],
                ['name' => 'Label RIYO Water 1500ml', 'current_stock' => 320, 'min_stock' => 1000],
                ['name' => 'Tutup Botol (Cap) 600ml', 'current_stock' => 1250, 'min_stock' => 3000],
                ['name' => 'Kardus Kemasan 24pcs', 'current_stock' => 180, 'min_stock' => 500]
            ]
        ];

        return view('pages.dashboard', $dashboardData);
    }
}
