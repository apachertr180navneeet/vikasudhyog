<!-- Sidebar Navigation Drawer -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-brand-link">
            <img src="{{ asset('admin/images/logo.png') }}" alt="Vikas Udhyog Logo" class="sidebar-logo">
            <div class="sidebar-brand">
                <span class="sidebar-brand-title">VIKAS UDHYOG</span>
                <span class="sidebar-brand-subtitle">HERBAL ERP v2.4</span>
            </div>
        </a>
        <button type="button" class="sidebar-mobile-close" id="sidebar-close-btn" title="Close Menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <ul class="sidebar-menu">
        <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="nav-link" data-view="dashboard">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Masters Submenu -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.masters.*') ? 'open active' : '' }}">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-folder-tree"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.masters.company') }}" class="submenu-link {{ request()->routeIs('admin.masters.company*') ? 'active' : '' }}" data-view="master-company">Company Master</a></li>
                <li><a href="{{ route('admin.masters.user') }}" class="submenu-link {{ request()->routeIs('admin.masters.user*') ? 'active' : '' }}" data-view="master-user">User Master</a></li>
                <li><a href="{{ route('admin.masters.access-level') }}" class="submenu-link {{ request()->routeIs('admin.masters.access-level') ? 'active' : '' }}" data-view="master-access">Access Level</a></li>
                <li><a href="{{ route('admin.masters.vendor') }}" class="submenu-link {{ request()->routeIs('admin.masters.vendor') ? 'active' : '' }}" data-view="master-vendor">Vendor Master</a></li>
                <li><a href="{{ route('admin.masters.customer') }}" class="submenu-link {{ request()->routeIs('admin.masters.customer') ? 'active' : '' }}" data-view="master-customer">Customer Master</a></li>
                <li><a href="{{ route('admin.masters.broker') }}" class="submenu-link {{ request()->routeIs('admin.masters.broker') ? 'active' : '' }}" data-view="master-broker">Broker Master</a></li>
                <li><a href="{{ route('admin.masters.item') }}" class="submenu-link {{ request()->routeIs('admin.masters.item') ? 'active' : '' }}" data-view="master-item">Item Master</a></li>
                <li><a href="{{ route('admin.masters.unit') }}" class="submenu-link {{ request()->routeIs('admin.masters.unit') ? 'active' : '' }}" data-view="master-unit">Unit Master</a></li>
                <li><a href="{{ route('admin.masters.account') }}" class="submenu-link {{ request()->routeIs('admin.masters.account') ? 'active' : '' }}" data-view="master-account">Account Master</a></li>
            </ul>
        </li>

        <!-- Transactions Submenu -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.transactions.*') ? 'open active' : '' }}">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-right-left"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.transactions.purchase-entry') }}" class="submenu-link {{ request()->routeIs('admin.transactions.purchase-entry') ? 'active' : '' }}" data-view="txn-purchase">Purchase Entry</a></li>
                <li><a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="submenu-link {{ request()->routeIs('admin.transactions.wb-purchase-entry') ? 'active' : '' }}" data-view="txn-wb-purchase">WB Purchase Entry</a></li>
                <li><a href="{{ route('admin.transactions.sales-entry') }}" class="submenu-link {{ request()->routeIs('admin.transactions.sales-entry') ? 'active' : '' }}" data-view="txn-sales-order">Sales Entry</a></li>
                <li><a href="{{ route('admin.transactions.wb-sales-entry') }}" class="submenu-link {{ request()->routeIs('admin.transactions.wb-sales-entry') ? 'active' : '' }}" data-view="txn-wb-sales">WB Sales Entry</a></li>
                <li><a href="{{ route('admin.transactions.order-dispatch') }}" class="submenu-link {{ request()->routeIs('admin.transactions.order-dispatch') ? 'active' : '' }}" data-view="txn-order-dispatch">Order Dispatch</a></li>
                <li><a href="{{ route('admin.transactions.sales-purchase-order') }}" class="submenu-link {{ request()->routeIs('admin.transactions.sales-purchase-order') ? 'active' : '' }}" data-view="txn-sales-invoice">Sales / Purchase Order</a></li>
                <li><a href="{{ route('admin.transactions.receipt-voucher') }}" class="submenu-link {{ request()->routeIs('admin.transactions.receipt-voucher') ? 'active' : '' }}" data-view="txn-receipt">Receipt Voucher</a></li>
                <li><a href="{{ route('admin.transactions.payment-voucher') }}" class="submenu-link {{ request()->routeIs('admin.transactions.payment-voucher') ? 'active' : '' }}" data-view="txn-payment">Payment Voucher</a></li>
            </ul>
        </li>

        <!-- Inventory Submenu -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.inventory.*') ? 'open active' : '' }}">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventory</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.inventory.stock-overview') }}" class="submenu-link {{ request()->routeIs('admin.inventory.stock-overview') ? 'active' : '' }}" data-view="inv-overview">Stock Overview</a></li>
                <li><a href="{{ route('admin.inventory.item-ledger') }}" class="submenu-link {{ request()->routeIs('admin.inventory.item-ledger') ? 'active' : '' }}" data-view="inv-ledger">Item Ledger</a></li>
                <li><a href="{{ route('admin.inventory.stock-adjustment') }}" class="submenu-link {{ request()->routeIs('admin.inventory.stock-adjustment') ? 'active' : '' }}" data-view="inv-adjustment">Stock Adjustment</a></li>
                <li><a href="{{ route('admin.inventory.low-stock-alert') }}" class="submenu-link {{ request()->routeIs('admin.inventory.low-stock-alert') ? 'active' : '' }}" data-view="inv-low-stock">Low Stock Alert</a></li>
            </ul>
        </li>

        <!-- Reports Submenu -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.reports.*') ? 'open active' : '' }}">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-file-contract"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.reports.purchase-report') }}" class="submenu-link {{ request()->routeIs('admin.reports.purchase-report') ? 'active' : '' }}" data-view="rpt-purchase">Purchase Report</a></li>
                <li><a href="{{ route('admin.reports.sales-report') }}" class="submenu-link {{ request()->routeIs('admin.reports.sales-report') ? 'active' : '' }}" data-view="rpt-sales">Sales Report</a></li>
                <li><a href="{{ route('admin.reports.order-report') }}" class="submenu-link {{ request()->routeIs('admin.reports.order-report') ? 'active' : '' }}" data-view="rpt-order">Order Report</a></li>
                <li><a href="{{ route('admin.reports.cash-bank-register') }}" class="submenu-link {{ request()->routeIs('admin.reports.cash-bank-register') ? 'active' : '' }}" data-view="rpt-cash-reg">Cash & Bank Register</a></li>
            </ul>
        </li>

        <!-- Settings Submenu -->
        <li class="nav-item has-submenu {{ request()->routeIs('admin.settings.*') ? 'open active' : '' }}">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-gears"></i>
                <span>Settings</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="{{ route('admin.settings.company') }}" class="submenu-link {{ request()->routeIs('admin.settings.company') ? 'active' : '' }}" data-view="set-company">Company Information</a></li>
                <li><a href="{{ route('admin.settings.whatsapp') }}" class="submenu-link {{ request()->routeIs('admin.settings.whatsapp') ? 'active' : '' }}" data-view="set-whatsapp">WhatsApp Business API</a></li>
                <li><a href="{{ route('admin.settings.backup-restore') }}" class="submenu-link {{ request()->routeIs('admin.settings.backup-restore') ? 'active' : '' }}" data-view="set-backup">Backup / Restore</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <span class="sidebar-footer-badge">ISO 9001:2015</span>
        <span class="sidebar-footer-text">Herbal Manufacturing</span>
    </div>
</aside>
