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
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Dashboard & Laporan</span>
        </li>
        <li class="menu-item">
            <a href="app-email.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-home"></i>
                <div data-i18n="Email">Beranda - wip</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-layout-dashboard"></i>
                <div data-i18n="Chat">Dashboard - wip</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-file-analytics"></i>
                <div data-i18n="Chat">Laporan - wip</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-wallet"></i>
                <div data-i18n="Chat">Anggaran - wip</div>
            </a>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Transaksi</span>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-bank"></i>
                <div data-i18n="Chat">Kas & Bank - wip</div>
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
        <li class="menu-item @if (request()->routeIs(['main_transaction.jurnal.*'])) active @endif">
            <a href="{{ route('main_transaction.jurnal.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-receipt-2"></i>
                <div data-i18n="Chat">Jurnal</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['main_transaction.expense.*'])) active @endif">
            <a href="{{ route('main_transaction.expense.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-credit-card"></i>
                <div data-i18n="Chat">Pengeluaran</div>
            </a>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Master Data & Operasional</span>
        </li>
        <li class="menu-item @if (request()->routeIs(['master_data.contact.*'])) active @endif">
            <a href="{{ route('master_data.contact.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-address-book"></i>
                <div data-i18n="Kontak">Kontak</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['master_data.product.*'])) active @endif">
            <a href="{{ route('master_data.product.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-package"></i>
                <div data-i18n="Chat">Produk</div>
            </a>
        </li>
        <!-- <li class="menu-item @if (request()->routeIs(['master_data.storage.*'])) active @endif">
            <a href="{{ route('master_data.storage.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-warehouse"></i>
                <div data-i18n="Chat">Gudang</div>
            </a>
        </li> -->
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
            <a href="app-logistics-dashboard.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-clipboard-check"></i>
                <div data-i18n="Chat">Stock Opname - wip</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="app-ecommerce-dashboard.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-building-factory"></i>
                <div data-i18n="Chat">Asset / Inventaris - wip</div>
            </a>
        </li>
        <li class="menu-item @if (request()->routeIs(['master_data.coa.*'])) active @endif">
            <a href="{{ route('master_data.coa.index') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-notebook"></i>
                <div data-i18n="Chat">Daftar Akun</div>
            </a>
        </li>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="Apps & Pages">Sistem & Pengaturan</span>
        </li>
        <li class="menu-item">
            <a href="app-chat.html" class="menu-link">
                <i class="menu-icon icon-base ti tabler-list"></i>
                <div data-i18n="Chat">Daftar Lainnnya - wip</div>
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
                <div data-i18n="Chat">Pengaturan - wip</div>
            </a>
        </li>
    </ul>
</aside>
