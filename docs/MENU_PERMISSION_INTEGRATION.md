<?php

/**
 * HOW TO INTEGRATE PERMISSIONS INTO THE MENU SYSTEM
 * 
 * Add permission checks to menu.blade.php to show/hide menu items
 * based on user permissions
 */

/**
 * IMPLEMENTATION EXAMPLES FOR menu.blade.php
 * 
 * Current state: All menu items show for all authenticated users
 * After implementation: Menu items show only if user has permission
 */

// ============================================================================
// EXAMPLE 1: Simple Permission Check for Single Menu Item
// ============================================================================

/**
 * <!-- BEFORE -->
 * <li class="menu-item">
 *     <a href="{{ route('dashboard.index') }}" class="menu-link">
 *         <i class="menu-icon icon-base ti tabler-home"></i>
 *         <div data-i18n="Dashboard">Dashboard</div>
 *     </a>
 * </li>
 * 
 * <!-- AFTER - With Permission Check -->
 * @can('dashboard.view')
 * <li class="menu-item">
 *     <a href="{{ route('dashboard.index') }}" class="menu-link">
 *         <i class="menu-icon icon-base ti tabler-home"></i>
 *         <div data-i18n="Dashboard">Dashboard</div>
 *     </a>
 * </li>
 * @endcan
 */

// ============================================================================
// EXAMPLE 2: Permission Check for Dropdown Menu Group
// ============================================================================

/**
 * <!-- BEFORE -->
 * <li class="menu-item">
 *     <a href="javascript:void(0);" class="menu-link menu-toggle">
 *         <i class="menu-icon icon-base ti tabler-cash-register"></i>
 *         <div data-i18n="Penjualan">Penjualan</div>
 *     </a>
 *     <ul class="menu-sub">
 *         <li class="menu-item">
 *             <a href="{{ route('main_transaction.sales_order.index') }}" class="menu-link">
 *                 <div>Sales Order</div>
 *             </a>
 *         </li>
 *         <li class="menu-item">
 *             <a href="{{ route('main_transaction.sales_delivery.index') }}" class="menu-link">
 *                 <div>Sales Delivery</div>
 *             </a>
 *         </li>
 *     </ul>
 * </li>
 * 
 * <!-- AFTER - With Permission Checks -->
 * @can('sales_order.view')
 * <li class="menu-item">
 *     <a href="javascript:void(0);" class="menu-link menu-toggle">
 *         <i class="menu-icon icon-base ti tabler-cash-register"></i>
 *         <div data-i18n="Penjualan">Penjualan</div>
 *     </a>
 *     <ul class="menu-sub">
 *         @can('sales_order.view')
 *         <li class="menu-item">
 *             <a href="{{ route('main_transaction.sales_order.index') }}" class="menu-link">
 *                 <div>Sales Order</div>
 *             </a>
 *         </li>
 *         @endcan
 *         
 *         @can('sales_delivery.view')
 *         <li class="menu-item">
 *             <a href="{{ route('main_transaction.sales_delivery.index') }}" class="menu-link">
 *                 <div>Sales Delivery</div>
 *             </a>
 *         </li>
 *         @endcan
 *     </ul>
 * </li>
 * @endcan
 */

// ============================================================================
// EXAMPLE 3: Complete Menu Integration for All Sections
// ============================================================================

/**
 * Here's the complete structure for menu.blade.php with permissions:
 */

?>

<!-- EXAMPLE: Menu with Permission Integration -->

<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ route('home.view') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-3">COMPANY</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- DASHBOARD & REPORTING SECTION -->
        <li class="menu-header small">
            <span class="menu-header-text">Dashboard & Laporan</span>
        </li>

        @can('dashboard.view')
        <li class="menu-item @if (request()->routeIs(['dashboard.index'])) active @endif">
            <a href="{{ route('dashboard.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-home"></i>
                <div>Dashboard</div>
            </a>
        </li>
        @endcan

        @can('financial_recording.view')
        <li class="menu-item @if (request()->routeIs(['dashboard.summary'])) active @endif">
            <a href="{{ route('dashboard.summary') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-pie"></i>
                <div>Pencatatan Keuangan</div>
            </a>
        </li>
        @endcan

        <!-- TRANSAKSI SECTION -->
        <li class="menu-header small">
            <span class="menu-header-text">Transaksi</span>
        </li>

        <!-- PENJUALAN (Sales) -->
        @can('sales_order.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.sales_order.*', 'main_transaction.sales_delivery.*', 'main_transaction.sales_invoice.*'])) open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-cash-register"></i>
                <div>Penjualan</div>
            </a>
            <ul class="menu-sub">
                @can('sales_order.view')
                <li class="menu-item @if (request()->routeIs(['main_transaction.sales_order.*'])) active @endif">
                    <a href="{{ route('main_transaction.sales_order.index') }}" class="menu-link">
                        <div>Sales Order</div>
                    </a>
                </li>
                @endcan

                @can('sales_delivery.view')
                <li class="menu-item @if (request()->routeIs(['main_transaction.sales_delivery.*'])) active @endif">
                    <a href="{{ route('main_transaction.sales_delivery.index') }}" class="menu-link">
                        <div>Sales Delivery</div>
                    </a>
                </li>
                @endcan

                @can('sales_invoice.view')
                <li class="menu-item @if (request()->routeIs(['main_transaction.sales_invoice.*'])) active @endif">
                    <a href="{{ route('main_transaction.sales_invoice.index') }}" class="menu-link">
                        <div>Sales Invoice</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        <!-- PEMBELIAN (Purchase) -->
        @can('purchase_order.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_order.*', 'main_transaction.purchase_delivery.*', 'main_transaction.purchase_invoice.*'])) open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-shopping-bag"></i>
                <div>Pembelian</div>
            </a>
            <ul class="menu-sub">
                @can('purchase_order.view')
                <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_order.*'])) active @endif">
                    <a href="{{ route('main_transaction.purchase_order.index') }}" class="menu-link">
                        <div>Purchase Order</div>
                    </a>
                </li>
                @endcan

                @can('purchase_delivery.view')
                <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_delivery.*'])) active @endif">
                    <a href="{{ route('main_transaction.purchase_delivery.index') }}" class="menu-link">
                        <div>Purchase Delivery</div>
                    </a>
                </li>
                @endcan

                @can('purchase_invoice.view')
                <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_invoice.*'])) active @endif">
                    <a href="{{ route('main_transaction.purchase_invoice.index') }}" class="menu-link">
                        <div>Purchase Invoice</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        <!-- PENGELUARAN (Expense) -->
        @can('expense.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.expense.*'])) active @endif">
            <a href="{{ route('main_transaction.expense.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-credit-card"></i>
                <div>Pengeluaran</div>
            </a>
        </li>
        @endcan

        <!-- STOCK ADJUSTMENT -->
        @can('stock_adjustment.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.stock_adjustment.*'])) active @endif">
            <a href="{{ route('main_transaction.stock_adjustment.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-adjustments"></i>
                <div>Stock Adjustment</div>
            </a>
        </li>
        @endcan

        <!-- RETURN MANAGEMENT -->
        @can('return_management.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.return_management.*'])) active @endif">
            <a href="{{ route('main_transaction.return_management.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-arrow-back-up"></i>
                <div>Return Management</div>
            </a>
        </li>
        @endcan

        <!-- MASTER DATA & OPERASIONAL SECTION -->
        <li class="menu-header small">
            <span class="menu-header-text">Master Data & Operasional</span>
        </li>

        <!-- REVENUE SPLIT -->
        @can('revenue_split.view')
        <li class="menu-item @if (request()->routeIs(['master_data.revenue_split.*'])) active @endif">
            <a href="{{ route('master_data.revenue_split.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-pie"></i>
                <div>Keuntungan Penjualan</div>
            </a>
        </li>
        @endcan

        <!-- KONTACT -->
        @can('contact.view')
        <li class="menu-item @if (request()->routeIs(['master_data.contact.*'])) active @endif">
            <a href="{{ route('master_data.contact.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-address-book"></i>
                <div>Kontak</div>
            </a>
        </li>
        @endcan

        <!-- PRODUCT -->
        @can('product.view')
        <li class="menu-item @if (request()->routeIs(['master_data.product.*'])) active @endif">
            <a href="{{ route('master_data.product.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-package"></i>
                <div>Produk</div>
            </a>
        </li>
        @endcan

        <!-- WAREHOUSE -->
        @can('warehouse.view')
        <li class="menu-item @if (request()->routeIs(['master_data.warehouse.*'])) active @endif">
            <a href="{{ route('master_data.warehouse.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-warehouse"></i>
                <div>Warehouse</div>
            </a>
        </li>
        @endcan

        <!-- PRODUCTION -->
        @can('production.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.production.*'])) active @endif">
            <a href="{{ route('main_transaction.production.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-tools"></i>
                <div>Produksi</div>
            </a>
        </li>
        @endcan

        <!-- PACKING -->
        @can('packing.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.packing.*'])) active @endif">
            <a href="{{ route('main_transaction.packing.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-package"></i>
                <div>Packing Produk</div>
            </a>
        </li>
        @endcan

        <!-- TOPUP -->
        @can('topup.view')
        <li class="menu-item @if (request()->routeIs(['main_transaction.topup.*'])) active @endif">
            <a href="{{ route('main_transaction.topup.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-cash"></i>
                <div>Top Up</div>
            </a>
        </li>
        @endcan

        <!-- STOCK OPNAME -->
        @can('stock_opname.view')
        <li class="menu-item @if (request()->routeIs(['master_data.stock_opname.*'])) active @endif">
            <a href="{{ route('master_data.stock_opname.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-clipboard-check"></i>
                <div>Stock Opname</div>
            </a>
        </li>
        @endcan

        <!-- BUDGET -->
        @can('budget.view')
        <li class="menu-item @if (request()->routeIs(['master_data.budged.*'])) active @endif">
            <a href="{{ route('master_data.budged.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-line"></i>
                <div>Anggaran</div>
            </a>
        </li>
        @endcan

        <!-- INVENTORY -->
        @can('inventory.view')
        <li class="menu-item @if (request()->routeIs(['master_data.inventory.*'])) active @endif">
            <a href="{{ route('master_data.inventory.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-factory"></i>
                <div>Asset / Inventaris</div>
            </a>
        </li>
        @endcan

        <!-- FINANCIAL REPORTS -->
        @can('report_journal.view')
        <li class="menu-item @if (request()->routeIs(['report.jurnal_umum.*', 'report.buku_besar.*', 'report.laba_rugi.*', 'report.neraca.*'])) active open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-list"></i>
                <div>Laporan Keuangan</div>
            </a>
            <ul class="menu-sub">
                @can('report_journal.view')
                <li class="menu-item @if (request()->routeIs(['report.jurnal_umum.*'])) active @endif">
                    <a href="{{ route('report.jurnal_umum.index') }}" class="menu-link">
                        <div>Jurnal Umum</div>
                    </a>
                </li>
                @endcan

                @can('report_ledger.view')
                <li class="menu-item @if (request()->routeIs(['report.buku_besar.*'])) active @endif">
                    <a href="{{ route('report.buku_besar.index') }}" class="menu-link">
                        <div>Buku Besar</div>
                    </a>
                </li>
                @endcan

                @can('report_balance_sheet.view')
                <li class="menu-item @if (request()->routeIs(['report.neraca.*'])) active @endif">
                    <a href="{{ route('report.neraca.index') }}" class="menu-link">
                        <div>Neraca</div>
                    </a>
                </li>
                @endcan

                @can('report_profit_loss.view')
                <li class="menu-item @if (request()->routeIs(['report.laba_rugi.*'])) active @endif">
                    <a href="{{ route('report.laba_rugi.index') }}" class="menu-link">
                        <div>Laba Rugi</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>
        @endcan

        <!-- SYSTEM & SETTINGS -->
        <li class="menu-header small">
            <span class="menu-header-text">Sistem & Pengaturan</span>
        </li>

        <!-- DAFTAR LAINNYA -->
        <li class="menu-item @if (request()->routeIs(['master_data.tax.*', 'master_data.unit.*', 'master_data.category.*', 'master_data.tag.*', 'master_data.sales_person.*', 'master_data.payment_term.*', 'master_data.transaction_number.*', 'master_data.budget_category.*'])) active open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-list"></i>
                <div>Daftar Lainnya</div>
            </a>
            <ul class="menu-sub">
                @can('tax.view')
                <li class="menu-item @if (request()->routeIs(['master_data.tax.*'])) active @endif">
                    <a href="{{ route('master_data.tax.index') }}" class="menu-link">
                        <div>Pajak</div>
                    </a>
                </li>
                @endcan

                @can('unit.view')
                <li class="menu-item @if (request()->routeIs(['master_data.unit.*'])) active @endif">
                    <a href="{{ route('master_data.unit.index') }}" class="menu-link">
                        <div>Unit</div>
                    </a>
                </li>
                @endcan

                @can('product_category.view')
                <li class="menu-item @if (request()->routeIs(['master_data.category.*'])) active @endif">
                    <a href="{{ route('master_data.category.index') }}" class="menu-link">
                        <div>Kategori Produk</div>
                    </a>
                </li>
                @endcan

                @can('tag.view')
                <li class="menu-item @if (request()->routeIs(['master_data.tag.*'])) active @endif">
                    <a href="{{ route('master_data.tag.index') }}" class="menu-link">
                        <div>Tag Management</div>
                    </a>
                </li>
                @endcan

                @can('budget_category.view')
                <li class="menu-item @if (request()->routeIs(['master_data.budget_category.*'])) active @endif">
                    <a href="{{ route('master_data.budget_category.index') }}" class="menu-link">
                        <div>Kategori Anggaran</div>
                    </a>
                </li>
                @endcan

                @can('sales_person.view')
                <li class="menu-item @if (request()->routeIs(['master_data.sales_person.*'])) active @endif">
                    <a href="{{ route('master_data.sales_person.index') }}" class="menu-link">
                        <div>Sales Person</div>
                    </a>
                </li>
                @endcan

                @can('payment_term.view')
                <li class="menu-item @if (request()->routeIs(['master_data.payment_term.*'])) active @endif">
                    <a href="{{ route('master_data.payment_term.index') }}" class="menu-link">
                        <div>Syarat Pembayaran</div>
                    </a>
                </li>
                @endcan

                @can('transaction_number.view')
                <li class="menu-item @if (request()->routeIs(['master_data.transaction_number.*'])) active @endif">
                    <a href="{{ route('master_data.transaction_number.index') }}" class="menu-link">
                        <div>Nomor Transaksi</div>
                    </a>
                </li>
                @endcan
            </ul>
        </li>

        <!-- USER MANAGEMENT -->
        @can('user.view')
        <li class="menu-item @if (request()->routeIs(['master_data.user.*'])) active @endif">
            <a href="{{ route('master_data.user.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div>Users</div>
            </a>
        </li>
        @endcan

        <!-- ROLE MANAGEMENT -->
        @can('role.view')
        <li class="menu-item @if (request()->routeIs(['master_data.role.*'])) active @endif">
            <a href="{{ route('master_data.role.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-shield-check"></i>
                <div>Role Management</div>
            </a>
        </li>
        @endcan
    </ul>
</aside>

<?php

/**
 * IMPLEMENTATION TIPS:
 * 
 * 1. Replace current menu.blade.php with this version
 * 2. Wrap each menu item/group with @can() directive
 * 3. Test with different user roles
 * 4. Clear cache if permissions don't show correctly
 * 
 * PERMISSION NAMES TO USE:
 * - dashboard.view
 * - sales_order.view
 * - purchase_order.view
 * - product.view
 * - contact.view
 * - warehouse.view
 * - production.view
 * - packing.view
 * - topup.view
 * - stock_opname.view
 * - budget.view
 * - inventory.view
 * - report_journal.view, report_ledger.view, etc.
 * - tax.view, unit.view, product_category.view, etc.
 * - user.view
 * - role.view
 * 
 * For complete list, see: app/Permissions/PermissionReference.php
 */

?>
