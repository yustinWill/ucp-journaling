<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="{{ route('home.view') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-3">RIYO WATER</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item @if (request()->routeIs(['master_data.*'])) active open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-database"></i>
                <div data-i18n="Dashboards">Master Data</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (request()->routeIs(['master_data.contact.*'])) active @endif">
                    <a href="{{ route('master_data.contact.index') }}" class="menu-link">
                        <div>Kontak</div>
                    </a>
                </li>
                <li class="menu-item @if (request()->routeIs(['master_data.storage.*'])) active @endif">
                    <a href="{{ route('master_data.storage.index') }}" class="menu-link">
                        <div>Gudang</div>
                    </a>
                </li>
                <li class="menu-item @if (request()->routeIs(['master_data.product.*'])) active @endif">
                    <a href="{{ route('master_data.product.index') }}" class="menu-link">
                        <div>Produk</div>
                    </a>
                </li>
                <li class="menu-item @if (request()->routeIs(['master_data.coa.*'])) active @endif">
                    <a href="{{ route('master_data.coa.index') }}" class="menu-link">
                        <div>COA</div>
                    </a>
                </li>
                <li class="menu-item @if (request()->routeIs(['master_data.user.*'])) active @endif"">
                    <a href="{{ route('master_data.user.index') }}" class="menu-link">
                        <div data-i18n="Logistics">User</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item @if (request()->routeIs(['main_transaction.*'])) active open @endif">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base ti tabler-cash-register"></i>
                <div data-i18n="Dashboards">Transaksi Utama</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item @if (request()->routeIs(['main_transaction.purchase.*'])) active @endif">
                    <a href="{{ route('main_transaction.purchase.index') }}" class="menu-link">
                        <div data-i18n="Analytics">Pembelian</div>
                    </a>
                </li>
                <li class="menu-item @if (request()->routeIs(['main_transaction.sales.*'])) active @endif">
                    <a href="{{ route('main_transaction.sales.index') }}" class="menu-link">
                        <div data-i18n="CRM">Penjualan</div>
                    </a>
                </li>
                 <li class="menu-item @if (request()->routeIs(['main_transaction.jurnal.*'])) active @endif">
                    <a href="{{ route('main_transaction.jurnal.index') }}" class="menu-link">
                        <div data-i18n="Analytics">Jurnal</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-ecommerce-dashboard.html" class="menu-link">
                        <div data-i18n="eCommerce">Asset / Inventaris</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="app-logistics-dashboard.html" class="menu-link">
                        <div data-i18n="Logistics">Stock Opname</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Menu A</span>
        </li>
        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-home"></i>
                <div data-i18n="Email">Beranda</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
                <div data-i18n="Chat">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-file-analytics"></i>
                <div data-i18n="Chat">Laporan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-wallet"></i>
                <div data-i18n="Chat">Anggaran</div>
            </a>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Menu B</span>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-bank"></i>
                <div data-i18n="Chat">Kas & Bank</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['main_transaction.sales.*'])) active @endif">
            <a href="{{ route('main_transaction.sales.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-shopping-cart"></i>
                <div data-i18n="Chat">Penjualan</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['main_transaction.purchase.*'])) active @endif">
            <a href="{{ route('main_transaction.purchase.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-shopping-bag"></i>
                <div data-i18n="Chat">Pembelian</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-receipt-2"></i>
                <div data-i18n="Chat">Biaya</div>
            </a>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Menu C</span>
        </li>
        <li class="menu-item @if (request()->routeIs(['master_data.contact.*'])) active @endif">
            <a href="{{ route('master_data.contact.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-address-book"></i>
                <div data-i18n="Kontak">Kontak</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-package"></i>
                <div data-i18n="Chat">Produk</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-tools"></i>
                <div data-i18n="Chat">Produksi</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-truck"></i>
                <div data-i18n="Chat">Pemenuhan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-factory"></i>
                <div data-i18n="Chat">Asset</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['master_data.coa.*'])) active @endif">
            <a href="{{ route('master_data.coa.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-notebook"></i>
                <div data-i18n="Chat">Daftar Akun</div>
            </a>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Menu D</span>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-list"></i>
                <div data-i18n="Chat">Daftar Lainnnya</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['master_data.user.*'])) active @endif">
            <a href="{{ route('master_data.user.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-users"></i>
                <div data-i18n="Users">Users</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-settings"></i>
                <div data-i18n="Chat">Pengaturan</div>
            </a>
        </li>
    </ul>
</aside>
