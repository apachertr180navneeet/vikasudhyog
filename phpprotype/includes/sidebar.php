<?php
$currentPage = $pageCode ?? 'dashboard';

$isMaster = (strpos($currentPage, 'master-') === 0);
$isTxn = (strpos($currentPage, 'txn-') === 0);
$isInv = (strpos($currentPage, 'inv-') === 0);
$isRpt = (strpos($currentPage, 'rpt-') === 0);
$isSet = (strpos($currentPage, 'set-') === 0);
?>
<!-- Sidebar Navigation Drawer -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="index.php" style="display: flex; align-items: center; gap: 0.85rem; text-decoration: none;">
            <img src="assets/images/logo.png" alt="Vikas Udhyog Logo" class="sidebar-logo">
            <div class="sidebar-brand">
                <span class="sidebar-brand-title">VIKAS UDHYOG</span>
                <span class="sidebar-brand-subtitle">HERBAL ERP v2.4</span>
            </div>
        </a>
    </div>

    <ul class="sidebar-menu">
        <li class="nav-item <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
            <a href="index.php" class="nav-link" data-view="dashboard">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Masters Submenu -->
        <li class="nav-item has-submenu <?= $isMaster ? 'open active' : '' ?>">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-folder-tree"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="company-master.php" class="submenu-link <?= ($currentPage === 'master-company') ? 'active' : '' ?>" data-view="master-company">Company Master</a></li>
                <li><a href="user-master.php" class="submenu-link <?= ($currentPage === 'master-user') ? 'active' : '' ?>" data-view="master-user">User Master</a></li>
                <li><a href="access-level.php" class="submenu-link <?= ($currentPage === 'master-access') ? 'active' : '' ?>" data-view="master-access">Access Level</a></li>
                <li><a href="vendor-master.php" class="submenu-link <?= ($currentPage === 'master-vendor') ? 'active' : '' ?>" data-view="master-vendor">Vendor Master</a></li>
                <li><a href="customer-master.php" class="submenu-link <?= ($currentPage === 'master-customer') ? 'active' : '' ?>" data-view="master-customer">Customer Master</a></li>
                <li><a href="broker-master.php" class="submenu-link <?= ($currentPage === 'master-broker') ? 'active' : '' ?>" data-view="master-broker">Broker Master</a></li>
                <li><a href="item-master.php" class="submenu-link <?= ($currentPage === 'master-item') ? 'active' : '' ?>" data-view="master-item">Item Master</a></li>
                <li><a href="unit-master.php" class="submenu-link <?= ($currentPage === 'master-unit') ? 'active' : '' ?>" data-view="master-unit">Unit Master</a></li>
                <li><a href="account-master.php" class="submenu-link <?= ($currentPage === 'master-account') ? 'active' : '' ?>" data-view="master-account">Account Master</a></li>
            </ul>
        </li>

        <!-- Transactions Submenu -->
        <li class="nav-item has-submenu <?= $isTxn ? 'open active' : '' ?>">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-right-left"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="purchase-entry.php" class="submenu-link <?= ($currentPage === 'txn-purchase') ? 'active' : '' ?>" data-view="txn-purchase">Purchase Entry</a></li>
                <li><a href="wb-purchase-entry.php" class="submenu-link <?= ($currentPage === 'txn-wb-purchase') ? 'active' : '' ?>" data-view="txn-wb-purchase">WB Purchase Entry</a></li>
                <li><a href="sales-entry.php" class="submenu-link <?= ($currentPage === 'txn-sales-order') ? 'active' : '' ?>" data-view="txn-sales-order">Sales Entry</a></li>
                <li><a href="wb-sales-entry.php" class="submenu-link <?= ($currentPage === 'txn-wb-sales') ? 'active' : '' ?>" data-view="txn-wb-sales">WB Sales Entry</a></li>
                <li><a href="order-dispatch.php" class="submenu-link <?= ($currentPage === 'txn-order-dispatch') ? 'active' : '' ?>" data-view="txn-order-dispatch">Order Dispatch</a></li>
                <li><a href="sales-purchase-order.php" class="submenu-link <?= ($currentPage === 'txn-sales-invoice') ? 'active' : '' ?>" data-view="txn-sales-invoice">Sales / Purchase Order</a></li>
                <li><a href="receipt-voucher.php" class="submenu-link <?= ($currentPage === 'txn-receipt') ? 'active' : '' ?>" data-view="txn-receipt">Receipt Voucher</a></li>
                <li><a href="payment-voucher.php" class="submenu-link <?= ($currentPage === 'txn-payment') ? 'active' : '' ?>" data-view="txn-payment">Payment Voucher</a></li>
            </ul>
        </li>

        <!-- Inventory Submenu -->
        <li class="nav-item has-submenu <?= $isInv ? 'open active' : '' ?>">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Inventory</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="stock-overview.php" class="submenu-link <?= ($currentPage === 'inv-overview') ? 'active' : '' ?>" data-view="inv-overview">Stock Overview</a></li>
                <li><a href="item-ledger.php" class="submenu-link <?= ($currentPage === 'inv-ledger') ? 'active' : '' ?>" data-view="inv-ledger">Item Ledger</a></li>
                <li><a href="stock-adjustment.php" class="submenu-link <?= ($currentPage === 'inv-adjustment') ? 'active' : '' ?>" data-view="inv-adjustment">Stock Adjustment</a></li>
                <li><a href="low-stock-alert.php" class="submenu-link <?= ($currentPage === 'inv-low-stock') ? 'active' : '' ?>" data-view="inv-low-stock">Low Stock Alert</a></li>
            </ul>
        </li>

        <!-- Reports Submenu -->
        <li class="nav-item has-submenu <?= $isRpt ? 'open active' : '' ?>">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-file-contract"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="purchase-report.php" class="submenu-link <?= ($currentPage === 'rpt-purchase') ? 'active' : '' ?>" data-view="rpt-purchase">Purchase Report</a></li>
                <li><a href="sales-report.php" class="submenu-link <?= ($currentPage === 'rpt-sales') ? 'active' : '' ?>" data-view="rpt-sales">Sales Report</a></li>
                <li><a href="order-report.php" class="submenu-link <?= ($currentPage === 'rpt-order') ? 'active' : '' ?>" data-view="rpt-order">Order Report</a></li>
                <li><a href="cash-bank-register.php" class="submenu-link <?= ($currentPage === 'rpt-cash-reg') ? 'active' : '' ?>" data-view="rpt-cash-reg">Cash & Bank Register</a></li>
            </ul>
        </li>

        <!-- Settings Submenu -->
        <li class="nav-item has-submenu <?= $isSet ? 'open active' : '' ?>">
            <a href="#" class="nav-link">
                <i class="fa-solid fa-gears"></i>
                <span>Settings</span>
                <i class="fa-solid fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="submenu">
                <li><a href="company-settings.php" class="submenu-link <?= ($currentPage === 'set-company') ? 'active' : '' ?>" data-view="set-company">Company Information</a></li>
                <li><a href="whatsapp-settings.php" class="submenu-link <?= ($currentPage === 'set-whatsapp') ? 'active' : '' ?>" data-view="set-whatsapp">WhatsApp Business API</a></li>
                <li><a href="backup-restore.php" class="submenu-link <?= ($currentPage === 'set-backup') ? 'active' : '' ?>" data-view="set-backup">Backup / Restore</a></li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <span class="sidebar-footer-badge">ISO 9001:2015</span>
        <span style="font-size: 0.72rem; color: rgba(255,255,255,0.6);">Herbal Manufacturing</span>
    </div>
</aside>
