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
        @can('dashboard.view')
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Apps & Pages">Dashboard & Laporan</span>
            </li>
            <li class="menu-item @if (request()->routeIs(['dashboard.index'])) active @endif">
                <a href="{{ route('dashboard.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-home"></i>
                    <div data-i18n="Dashboard">Dashboard</div>
                </a>
            </li>
        @endcan
        {{-- <li class="menu-item @if (request()->routeIs(['dashboard.summary'])) active @endif">
            <a href="{{ route('dashboard.summary') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-pie"></i>
                <div data-i18n="Pencatatan Keuangan">Pencatatan Keuangan</div>
            </a>
        </li> --}}
        <!-- Temporary Hidden: ADIT ONLY -->
        @can('revenue_split.view')
            <li class="menu-item @if (request()->routeIs(['master_data.revenue_split.*'])) active @endif">
                <a href="{{ route('master_data.revenue_split.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-chart-pie"></i>
                    <div data-i18n="RevenueSpilt">Keuntungan Penjualan</div>
                </a>
            </li>
        @endcan
        {{-- <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-file-analytics"></i>
                <div data-i18n="Chat">Laporan - wip</div>
            </a>
        </li> --}}
        {{-- <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-wallet"></i>
                <div data-i18n="Chat">Anggaran - wip</div>
            </a>
        </li> --}}
        @if(Auth::check() && (Auth::user()->hasPermissionTo('sales_order.view') || Auth::user()->hasPermissionTo('sales_delivery.view') || Auth::user()->hasPermissionTo('sales_invoice.view') || Auth::user()->hasPermissionTo('purchase_order.view') || Auth::user()->hasPermissionTo('purchase_delivery.view') || Auth::user()->hasPermissionTo('purchase_invoice.view') || Auth::user()->hasPermissionTo('expense.view')))
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Apps & Pages">Transaksi</span>
            </li>
        @endif
        @if (session('CLIENT_CODE') == '001')
            <li class="menu-item">
                <a href="app-chat.html" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-building-bank"></i>
                    <div data-i18n="Chat">Kas & Bank - wip</div>
                </a>
            </li>
        @endif
        @if(Auth::check() && (Auth::user()->hasPermissionTo('sales_order.view') || Auth::user()->hasPermissionTo('sales_delivery.view') || Auth::user()->hasPermissionTo('sales_invoice.view')))
            <li class="menu-item @if (request()->routeIs([
                    'main_transaction.sales_order.*',
                    'main_transaction.sales_delivery.*',
                    'main_transaction.sales_invoice.*',
                ])) open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-cash-register"></i>
                    <div data-i18n="Chat">Penjualan</div>
                </a>
                <ul class="menu-sub">
                    @can('sales_order.view')
                        <li class="menu-item @if (request()->routeIs(['main_transaction.sales_order.*'])) active @endif">
                            <a href="{{ route('main_transaction.sales_order.index') }}" class="menu-link">
                                <div data-i18n="Chat">Sales Order</div>
                            </a>
                        </li>
                    @endcan
                    @can('sales_delivery.view')
                        <li class="menu-item @if (request()->routeIs(['main_transaction.sales_delivery.*'])) active @endif">
                            <a href="{{ route('main_transaction.sales_delivery.index') }}" class="menu-link">
                                <div data-i18n="Chat">Sales Delivery</div>
                            </a>
                        </li>
                    @endcan
                    @can('sales_invoice.view')
                        <li class="menu-item @if (request()->routeIs(['main_transaction.sales_invoice.*'])) active @endif">
                            <a href="{{ route('main_transaction.sales_invoice.index') }}" class="menu-link">
                                <div data-i18n="Chat">Sales Invoice</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
        @if(Auth::check() && (Auth::user()->hasPermissionTo('purchase_order.view') || Auth::user()->hasPermissionTo('purchase_delivery.view') || Auth::user()->hasPermissionTo('purchase_invoice.view')))
            <li class="menu-item @if (request()->routeIs([
                    'main_transaction.purchase_order.*',
                    'main_transaction.purchase_delivery.*',
                    'main_transaction.purchase_invoice.*',
                ])) open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-shopping-bag"></i>
                    <div data-i18n="Chat">Pembelian</div>
                </a>
                <ul class="menu-sub">
                    @can('purchase_order.view')
                        <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_order.*'])) active @endif">
                            <a href="{{ route('main_transaction.purchase_order.index') }}" class="menu-link">
                                <div data-i18n="Chat">Purchase Order</div>
                            </a>
                        </li>
                    @endcan
                    @can('purchase_delivery.view')
                        <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_delivery.*'])) active @endif">
                            <a href="{{ route('main_transaction.purchase_delivery.index') }}" class="menu-link">
                                <div data-i18n="Chat">Purchase Delivery</div>
                            </a>
                        </li>
                    @endcan
                    @can('purchase_invoice.view')
                        <li class="menu-item @if (request()->routeIs(['main_transaction.purchase_invoice.*'])) active @endif">
                            <a href="{{ route('main_transaction.purchase_invoice.index') }}" class="menu-link">
                                <div data-i18n="Chat">Purchase Invoice</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
        {{-- <li class="menu-item @if (request()->routeIs(['main_transaction.journal.*'])) active @endif">
            <a href="{{ route('main_transaction.journal.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-receipt-2"></i>
                <div data-i18n="Chat">Jurnal</div>
            </a>
        </li> --}}
        @can('expense.view')
            {{-- @if (session('CLIENT_CODE') == '001') --}}
                <li class="menu-item @if (request()->routeIs(['main_transaction.expense.*'])) active @endif">
                    <a href="{{ route('main_transaction.expense.index') }}" class="menu-link">
                        <i class="menu-icon icon-base ti tabler-credit-card"></i>
                        <div data-i18n="Chat">Pengeluaran</div>
                    </a>
                </li>
            {{-- @endif --}}
        @endcan

        @if(Auth::check() && (Auth::user()->hasPermissionTo('budget.view') || Auth::user()->hasPermissionTo('topup.view') || Auth::user()->hasPermissionTo('revenue_split.view') || Auth::user()->hasPermissionTo('kas_besar.view')))
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Apps & Pages">Finansial</span>
            </li>
        @endif
        
        @can('budget.view')
            <li class="menu-item @if (request()->routeIs(['master_data.budged.*'])) active @endif">
                <a href="{{ route('master_data.budged.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-chart-line"></i>
                    <div data-i18n="Chat">Anggaran</div>
                </a>
            </li>
        @endcan
        
        @can('kas_besar.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.kas_besar.*'])) active @endif">
                <a href="{{ route('main_transaction.kas_besar.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-cash-register"></i>
                    <div data-i18n="Kas Besar">Kas Besar</div>
                </a>
            </li>
        @endcan
        @can('topup.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.topup.*'])) active @endif">
                <a href="{{ route('main_transaction.topup.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-cash"></i>
                    <div data-i18n="Top Up">Top Up</div>
                </a>
            </li>
        @endcan
        @can('reimbursement.view')
        {{-- Reimbursement menu items (add permission permission if available) --}}
            <li class="menu-item @if (request()->routeIs(['main_transaction.reimbursement.*'])) active @endif">
                <a href="{{ route('main_transaction.reimbursement.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-receipt-refund"></i>
                    <div data-i18n="Reimbursement">Reimbursement</div>
                </a>
            </li>
        @endcan
        @can('reimbursement_approval.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.reimbursement_approval.*'])) active @endif">
                <a href="{{ route('main_transaction.reimbursement_approval.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-circle-check"></i>
                    <div data-i18n="Reimbursement Approval">Reimbursement Approval</div>
                </a>
            </li>
        @endcan

        @if(Auth::check() && (Auth::user()->hasPermissionTo('production.view') || Auth::user()->hasPermissionTo('packing.view') || Auth::user()->hasPermissionTo('stock_adjustment.view') || Auth::user()->hasPermissionTo('return_management.view') || Auth::user()->hasPermissionTo('stock_opname.view')))
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Apps & Pages">Operasional</span>
            </li>
        @endif

        @can('production.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.production.*'])) active @endif">
                <a href="{{ route('main_transaction.production.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-tools"></i>
                    <div data-i18n="Produksi">Produksi</div>
                </a>
            </li>
        @endcan

        @can('packing.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.packing.*'])) active @endif">
                <a href="{{ route('main_transaction.packing.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-package"></i>
                    <div data-i18n="Packing Produk">Packing Produk</div>
                </a>
            </li>
        @endcan

        @can('stock_adjustment.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.stock_adjustment.*'])) active @endif">
                <a href="{{ route('main_transaction.stock_adjustment.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-adjustments"></i>
                    <div data-i18n="Chat">Stock Adjustment</div>
                </a>
            </li>
        @endcan

        @can('return_management.view')
            <li class="menu-item @if (request()->routeIs(['main_transaction.return_management.*'])) active @endif">
                <a href="{{ route('main_transaction.return_management.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-arrow-back-up"></i>
                    <div data-i18n="Chat">Return Management</div>
                </a>
            </li>
        @endcan

        @can('stock_opname.view')
            <li class="menu-item @if (request()->routeIs(['master_data.stock_opname.*'])) active @endif">
                <a href="{{ route('master_data.stock_opname.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-clipboard-check"></i>
                    <div data-i18n="Chat">Stock Opname</div>
                </a>
            </li>
        @endcan
        

        @if(Auth::check() && (Auth::user()->hasPermissionTo('contact.view') || Auth::user()->hasPermissionTo('product.view') || Auth::user()->hasPermissionTo('warehouse.view') || Auth::user()->hasPermissionTo('inventory.view')))
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Apps & Pages">Master Data</span>
            </li>
        @endif
        
        @can('contact.view')
            <li class="menu-item @if (request()->routeIs(['master_data.contact.*'])) active @endif">
                <a href="{{ route('master_data.contact.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-address-book"></i>
                    <div data-i18n="Kontak">Kontak</div>
                </a>
            </li>
        @endcan
        
        @can('product.view')
            <li class="menu-item @if (request()->routeIs(['master_data.product.*'])) active @endif">
                <a href="{{ route('master_data.product.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-package"></i>
                    <div data-i18n="Produk">Produk</div>
                </a>
            </li>
        @endcan
        
        @can('warehouse.view')
            <li class="menu-item @if (request()->routeIs(['master_data.warehouse.*'])) active @endif">
                <a href="{{ route('master_data.warehouse.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-building-warehouse"></i>
                    <div data-i18n="Warehouse">Warehouse</div>
                </a>
            </li>
        @endcan
        {{-- <li class="menu-item @if (request()->routeIs(['main_transaction.overhead_absorption.*'])) active @endif">
            <a href="{{ route('main_transaction.overhead_absorption.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-chart-dots"></i>
                <div data-i18n="Absorpsi Overhead">Absorpsi Overhead</div>
            </a>
        </li> --}}
        @if (session('CLIENT_CODE') == '001')
            <li class="menu-item">
                <a href="app-chat.html" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-truck"></i>
                    <div data-i18n="Chat">Pemenuhan - wip</div>
                </a>
            </li>
        @endif

        @can('inventory.view')
            <li class="menu-item @if (request()->routeIs(['master_data.inventory.*'])) active @endif">
                <a href="{{ route('master_data.inventory.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-building-factory"></i>
                    <div data-i18n="Chat">Asset / Inventaris</div>
                </a>
            </li>
        @endcan
        
        @can('coa.view')
            <li class="menu-item @if (request()->routeIs(['master_data.coa.*'])) active @endif">
                <a href="{{ route('master_data.coa.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-notebook"></i>
                    <div data-i18n="Chat">Daftar Akun</div>
                </a>
            </li>
        @endcan
        @if (session('CLIENT_CODE') == '001' && (Auth::check() && (Auth::user()->hasPermissionTo('report_journal.view') || Auth::user()->hasPermissionTo('report_ledger.view') || Auth::user()->hasPermissionTo('report_balance_sheet.view') || Auth::user()->hasPermissionTo('report_profit_loss.view'))))
            <li class="menu-item @if (request()->routeIs(['report.jurnal_umum.*', 'report.buku_besar.*', 'report.laba_rugi.*', 'report.neraca.*'])) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-list"></i>
                    <div data-i18n="Chat">Laporan Keuangan</div>
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
        @endif
        
        @if(Auth::check() && (Auth::user()->hasPermissionTo('tax.view') || Auth::user()->hasPermissionTo('unit.view') || Auth::user()->hasPermissionTo('product_category.view') || Auth::user()->hasPermissionTo('tag.view') || Auth::user()->hasPermissionTo('sales_person.view') || Auth::user()->hasPermissionTo('payment_term.view') || Auth::user()->hasPermissionTo('transaction_number.view') || Auth::user()->hasPermissionTo('budget_category.view')))
            <li class="menu-header small">
                <span class="menu-header-text" data-i18n="Apps & Pages">Sistem & Pengaturan</span>
            </li>
        @endif
        
        @if(Auth::check() && (Auth::user()->hasPermissionTo('tax.view') || Auth::user()->hasPermissionTo('unit.view') || Auth::user()->hasPermissionTo('product_category.view') || Auth::user()->hasPermissionTo('tag.view') || Auth::user()->hasPermissionTo('sales_person.view') || Auth::user()->hasPermissionTo('payment_term.view') || Auth::user()->hasPermissionTo('transaction_number.view') || Auth::user()->hasPermissionTo('budget_category.view')))
            <li class="menu-item @if (request()->routeIs([
                    'master_data.tax.*',
                    'master_data.unit.*',
                    'master_data.category.*',
                    'master_data.tag.*',
                    'master_data.sales_person.*',
                    'master_data.payment_term.*',
                    'master_data.transaction_number.*',
                    'master_data.budget_category.*',
                ])) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-list"></i>
                    <div data-i18n="Chat">Daftar Lainnya</div>
                </a>
                <ul class="menu-sub">
                    @can('tax.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.tax.*'])) active @endif">
                            <a href="{{ route('master_data.tax.index') }}" class="menu-link">
                                <div data-i18n="Tax">Pajak</div>
                            </a>
                        </li>
                    @endcan
                    @can('unit.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.unit.*'])) active @endif">
                            <a href="{{ route('master_data.unit.index') }}" class="menu-link">
                                <div data-i18n="Unit">Unit</div>
                            </a>
                        </li>
                    @endcan
                    @can('product_category.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.category.*'])) active @endif">
                            <a href="{{ route('master_data.category.index') }}" class="menu-link">
                                <div data-i18n="Category">Kategori Produk</div>
                            </a>
                        </li>
                    @endcan
                    @can('tag.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.tag.*'])) active @endif">
                            <a href="{{ route('master_data.tag.index') }}" class="menu-link">
                                <div data-i18n="Tags">Tag Management</div>
                            </a>
                        </li>
                    @endcan
                    @can('budget_category.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.budget_category.*'])) active @endif">
                            <a href="{{ route('master_data.budget_category.index') }}" class="menu-link">
                                <div data-i18n="BudgetCategory">Kategori Anggaran</div>
                            </a>
                        </li>
                    @endcan
                    @can('sales_person.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.sales_person.*'])) active @endif">
                            <a href="{{ route('master_data.sales_person.index') }}" class="menu-link">
                                <div data-i18n="SalesPerson">Sales Person</div>
                            </a>
                        </li>
                    @endcan
                    @can('payment_term.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.payment_term.*'])) active @endif">
                            <a href="{{ route('master_data.payment_term.index') }}" class="menu-link">
                                <div data-i18n="PaymentTerm">Syarat Pembayaran</div>
                            </a>
                        </li>
                    @endcan
                    @can('transaction_number.view')
                        <li class="menu-item @if (request()->routeIs(['master_data.transaction_number.*'])) active @endif">
                            <a href="{{ route('master_data.transaction_number.index') }}" class="menu-link">
                                <div data-i18n="TransactionNumber">Nomor Transaksi</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endif
        
        @can('user.view')
            <li class="menu-item @if (request()->routeIs(['master_data.user.*'])) active @endif">
                <a href="{{ route('master_data.user.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-users"></i>
                    <div data-i18n="Users">Users</div>
                </a>
            </li>
        @endcan
        
        @can('role.view')
            <li class="menu-item @if (request()->routeIs(['master_data.role.*'])) active @endif">
                <a href="{{ route('master_data.role.index') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-shield-check"></i>
                    <div data-i18n="Roles">Role Management</div>
                </a>
            </li>
        @endcan
        @if (session('CLIENT_CODE') == '001')
            <li class="menu-item">
                <a href="app-chat.html" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-settings"></i>
                    <div data-i18n="Chat">Pengaturan - wip</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
