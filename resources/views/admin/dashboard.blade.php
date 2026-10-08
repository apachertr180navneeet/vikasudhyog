@extends('admin.layouts.app')

@section('title', 'Executive Operations Dashboard - VIKAS UDHYOG ERP')
@section('page_code', 'dashboard')

@push('styles')
<style>
/* ==========================================================================
   VIKAS UDHYOG ERP - Executive Dashboard Stylesheet
   Adhering to ERP UI Design System & AGENTS.md Standards
   ========================================================================== */

/* 1. Executive Top Bar Enhancements */
.dash-topbar-wrapper {
    margin-bottom: 1.5rem;
}

/* 2. KPI 6-Grid System */
.dash-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 1.15rem;
    margin-bottom: 1.75rem;
}

.dash-kpi-card {
    background: #FFFFFF;
    border-radius: 14px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 2px 10px -2px rgba(0, 0, 0, 0.04);
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    position: relative;
    overflow: hidden;
}

.dash-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08);
}

/* Accent Left Borders matching Master/Inventory Standard */
.dash-kpi-card.kpi-primary { border-left: 4px solid #5B841E !important; }
.dash-kpi-card.kpi-amber   { border-left: 4px solid #D97706 !important; }
.dash-kpi-card.kpi-blue    { border-left: 4px solid #2563EB !important; }
.dash-kpi-card.kpi-rose    { border-left: 4px solid #E11D48 !important; }
.dash-kpi-card.kpi-purple  { border-left: 4px solid #7C3AED !important; }
.dash-kpi-card.kpi-danger  { border-left: 4px solid #DC2626 !important; }
.dash-kpi-card.kpi-success { border-left: 4px solid #059669 !important; }

/* KPI Icon Boxes */
.dash-kpi-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.dash-kpi-icon-box.icon-green  { background: rgba(91, 132, 30, 0.12); color: #5B841E; }
.dash-kpi-icon-box.icon-amber  { background: rgba(217, 119, 6, 0.12); color: #D97706; }
.dash-kpi-icon-box.icon-blue   { background: rgba(37, 99, 235, 0.12); color: #2563EB; }
.dash-kpi-icon-box.icon-rose   { background: rgba(225, 29, 72, 0.12); color: #E11D48; }
.dash-kpi-icon-box.icon-purple { background: rgba(124, 58, 237, 0.12); color: #7C3AED; }
.dash-kpi-icon-box.icon-red    { background: rgba(220, 38, 38, 0.12); color: #DC2626; }
.dash-kpi-icon-box.icon-emerald{ background: rgba(5, 150, 105, 0.12); color: #059669; }

/* KPI Text Content */
.dash-kpi-body {
    flex: 1;
    min-width: 0;
}

.dash-kpi-label {
    font-size: 0.73rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748B;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.dash-kpi-val {
    font-size: 1.45rem;
    font-weight: 800;
    color: #0F172A;
    font-family: Consolas, 'SFMono-Regular', Menlo, Monaco, monospace;
    font-variant-numeric: tabular-nums;
    line-height: 1.2;
}

.dash-kpi-val.val-blue   { color: #1D4ED8; }
.dash-kpi-val.val-rose   { color: #BE123C; }
.dash-kpi-val.val-purple { color: #6D28D9; }
.dash-kpi-val.val-red    { color: #B91C1C; }
.dash-kpi-val.val-green  { color: #059669; }

.dash-kpi-subtext {
    font-size: 0.73rem;
    color: #64748B;
    margin-top: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
}

.dash-kpi-subtext strong {
    color: inherit;
}

.dash-kpi-tag {
    font-size: 0.67rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    background: #F1F5F9;
    color: #475569;
}

/* 3. Section Cards & Grid Structure */
.dash-charts-grid {
    display: grid;
    grid-template-columns: 1.85fr 1.15fr;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
    align-items: stretch;
}

.dash-card {
    background: #FFFFFF;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.dash-card-header {
    padding: 1.15rem 1.4rem;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    background: #FFFFFF;
}

.dash-card-header-left {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.dash-card-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: rgba(91, 132, 30, 0.12);
    color: #5B841E;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
}

.dash-card-title {
    font-size: 0.98rem;
    font-weight: 700;
    color: #1E293B;
    margin: 0;
    line-height: 1.25;
}

.dash-card-subtitle {
    font-size: 0.74rem;
    color: #64748B;
    margin: 0.15rem 0 0 0;
}

.dash-card-body {
    padding: 1.35rem 1.4rem;
}

.dash-chart-canvas-wrap {
    height: 290px;
    position: relative;
    width: 100%;
}

.dash-chart-canvas-wrap.donut-wrap {
    height: 245px;
}

.dash-chart-legend {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.dash-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}

.dash-chart-footer-stat {
    margin-top: 1rem;
    padding-top: 0.85rem;
    border-top: 1px dashed #E2E8F0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.76rem;
    color: #64748B;
}

/* 4. Lower Operational Grid */
.dash-lower-grid {
    display: grid;
    grid-template-columns: 1.85fr 1.15fr;
    gap: 1.5rem;
    align-items: start;
    margin-bottom: 2rem;
}

/* Table Design System adhering to AGENTS.md */
.dash-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.dash-table thead th {
    background: #F8FAFC;
    padding: 0.9rem 1.15rem;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    border-bottom: 1px solid #E2E8F0;
    white-space: nowrap;
}

.dash-table tbody td {
    padding: 0.9rem 1.15rem;
    font-size: 0.82rem;
    color: #334155;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}

.dash-table tbody tr:last-child td {
    border-bottom: none;
}

.dash-table tbody tr:hover {
    background: #F9FBFA;
}

/* Circular Gradient Avatars mandated by AGENTS.md */
.dash-avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #5B841E, #3D5A12);
    color: #FFFFFF;
    font-weight: 700;
    font-size: 0.78rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);
    text-transform: uppercase;
}

.dash-party-cell {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}

.dash-party-name {
    font-weight: 600;
    color: #1E293B;
    line-height: 1.25;
}

.dash-txn-ref {
    font-weight: 700;
    font-size: 0.83rem;
    color: #1E293B;
    text-decoration: none;
    font-family: Consolas, monospace;
}

.dash-txn-ref:hover {
    color: #5B841E;
    text-decoration: underline;
}

.dash-txn-date {
    font-size: 0.71rem;
    color: #64748B;
    margin-top: 1px;
}

.dash-badge-type {
    font-weight: 700;
    font-size: 0.71rem;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

.dash-amount-val {
    font-family: Consolas, monospace;
    font-weight: 800;
    font-size: 0.88rem;
    color: #0F172A;
    text-align: right;
}

.dash-status-pill {
    font-weight: 700;
    font-size: 0.71rem;
    padding: 3px 9px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.dash-status-pill.success {
    background: #DCFCE7;
    color: #15803D;
}

.dash-status-pill.info {
    background: #E0E7FF;
    color: #3730A3;
}

.dash-table-empty {
    text-align: center;
    padding: 3rem 1.5rem;
    color: #94A3B8;
}

.dash-table-empty i {
    font-size: 2.2rem;
    color: #CBD5E1;
    margin-bottom: 0.5rem;
    display: block;
}

/* 5. Operational Sidebar Components */
.dash-sidebar-stack {
    display: flex;
    flex-direction: column;
    gap: 1.35rem;
}

/* Low Stock Watchlist */
.dash-stock-row {
    padding: 0.75rem 0.85rem;
    background: #F8FAFC;
    border-radius: 10px;
    border: 1px solid #E2E8F0;
    transition: background 0.15s ease;
}

.dash-stock-row + .dash-stock-row {
    margin-top: 0.65rem;
}

.dash-stock-row:hover {
    background: #F1F5F9;
}

.dash-stock-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.35rem;
}

.dash-stock-title-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 0;
}

.dash-stock-name {
    font-weight: 700;
    font-size: 0.82rem;
    color: #1E293B;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dash-stock-badge {
    font-size: 0.69rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 6px;
    font-family: Consolas, monospace;
    flex-shrink: 0;
}

.dash-stock-badge.critical { background: #FEE2E2; color: #991B1B; }
.dash-stock-badge.warning  { background: #FEF3C7; color: #92400E; }

.dash-stock-meta-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.71rem;
    color: #64748B;
    margin-bottom: 0.4rem;
}

.dash-stock-meta-row code {
    font-family: Consolas, monospace;
    background: #E2E8F0;
    color: #334155;
    padding: 1px 4px;
    border-radius: 3px;
    font-size: 0.69rem;
}

.dash-stock-progress-track {
    width: 100%;
    height: 5px;
    background: #E2E8F0;
    border-radius: 999px;
    overflow: hidden;
}

.dash-stock-progress-bar {
    height: 100%;
    border-radius: 999px;
    transition: width 0.6s ease;
}

.dash-stock-progress-bar.critical { background: #EF4444; }
.dash-stock-progress-bar.warning  { background: #F59E0B; }

/* Cash & Bank Turnover Box */
.dash-cashflow-split {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.85rem;
}

.dash-cashbox {
    padding: 0.85rem;
    border-radius: 12px;
}

.dash-cashbox.receipts {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
}

.dash-cashbox.payments {
    background: #FEF2F2;
    border: 1px solid #FECACA;
}

.dash-cashbox-title {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.dash-cashbox.receipts .dash-cashbox-title { color: #166534; }
.dash-cashbox.payments .dash-cashbox-title { color: #991B1B; }

.dash-cashbox-amount {
    font-size: 1.15rem;
    font-weight: 800;
    font-family: Consolas, monospace;
    margin-top: 0.3rem;
    line-height: 1.2;
}

.dash-cashbox.receipts .dash-cashbox-amount { color: #15803D; }
.dash-cashbox.payments .dash-cashbox-amount { color: #DC2626; }

.dash-cashbox-note {
    font-size: 0.68rem;
    color: #64748B;
    margin-top: 0.25rem;
}

/* Quick Launchpad Grid */
.dash-launchpad-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.dash-launchpad-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.8rem 0.95rem;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.dash-launchpad-btn:hover {
    background: #FFFFFF;
    border-color: #5B841E;
    box-shadow: 0 4px 12px rgba(91, 132, 30, 0.1);
    transform: translateY(-2px);
}

.dash-launchpad-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}

.dash-launchpad-text {
    flex: 1;
    min-width: 0;
}

.dash-launchpad-label {
    font-size: 0.79rem;
    font-weight: 700;
    color: #1E293B;
    line-height: 1.2;
}

.dash-launchpad-sub {
    font-size: 0.68rem;
    color: #64748B;
    margin-top: 1px;
}

/* ==========================================================================
   Responsive Breakpoints
   ========================================================================== */
@media (max-width: 1200px) {
    .dash-kpi-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1024px) {
    .dash-charts-grid,
    .dash-lower-grid {
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    .dash-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dash-kpi-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    .dash-kpi-card {
        padding: 1rem;
    }
    .dash-chart-canvas-wrap {
        height: 240px;
    }
    .dash-chart-canvas-wrap.donut-wrap {
        height: 210px;
    }
    .dash-card-header {
        padding: 1rem;
    }
    .dash-card-body {
        padding: 1rem;
    }
    .dash-table {
        min-width: 580px;
    }
    .dash-cashflow-split {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .dash-launchpad-grid {
        grid-template-columns: 1fr;
    }
    .dash-kpi-val {
        font-size: 1.25rem;
    }
}
</style>
@endpush

@section('content')
<section class="view-section active" id="view-dashboard">

    @php
        $hour = (int) date('H');
        if ($hour < 12) {
            $greeting = 'Good Morning';
        } elseif ($hour < 17) {
            $greeting = 'Good Afternoon';
        } else {
            $greeting = 'Good Evening';
        }
        $userName = auth()->user()->name ?? session('vu_user_name', 'Administrator');
    @endphp

    <!-- Executive Top Bar with Actions -->
    <div class="erp-page-top-bar dash-topbar-wrapper">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Vikas Udhyog ERP</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Executive Dashboard</span>
            </div>
            <h1 class="erp-page-title">
                <div class="erp-page-title-icon-box">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
                <span>Executive Operations Dashboard</span>
                <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-size: 0.74rem; font-weight: 700; padding: 4px 10px; border-radius: 999px; margin-left: 0.4rem;">
                    <i class="fa-solid fa-industry" style="margin-right: 4px;"></i>{{ $company->name ?? 'Vikas Udhyog' }}
                </span>
            </h1>
            <p class="erp-page-subtitle">
                {{ $greeting }}, <strong>{{ $userName }}</strong> &bull; Financial Year: <strong>{{ $company->financial_year ?? '2026-2027' }}</strong> &bull; {{ date('l, d F Y') }}
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.inventory.stock-overview') }}" class="btn btn-outline" style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-boxes-stacked" style="margin-right: 5px;"></i> Stock Overview
            </a>
            <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="btn btn-outline" style="border-radius: 8px; font-weight: 600;">
                <i class="fa-solid fa-cart-plus" style="margin-right: 5px;"></i> + Purchase Bill
            </a>
            <a href="{{ route('admin.transactions.sales-entry.create') }}" class="btn btn-primary erp-btn-header-primary" style="border-radius: 8px; font-weight: 700;">
                <i class="fa-solid fa-plus" style="margin-right: 5px;"></i> + Sales Invoice
            </a>
        </div>
    </div>

    <!-- 6-Card Executive KPI Grid (Matching ERP Master/Inventory UI Standard) -->
    <div class="dash-kpi-grid">

        <!-- KPI 1: Today's Sales -->
        <div class="dash-kpi-card kpi-primary">
            <div class="dash-kpi-icon-box icon-green">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
            <div class="dash-kpi-body">
                <div class="dash-kpi-label">
                    <span>Today's Sales</span>
                    <span class="dash-kpi-tag">{{ $salesCount }} Invoices</span>
                </div>
                <div class="dash-kpi-val">₹{{ number_format($todaySales, 2) }}</div>
                <div class="dash-kpi-subtext">
                    This Month: <strong style="color: #5B841E;">₹{{ number_format($monthSales, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- KPI 2: Today's Purchases -->
        <div class="dash-kpi-card kpi-amber">
            <div class="dash-kpi-icon-box icon-amber">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div class="dash-kpi-body">
                <div class="dash-kpi-label">
                    <span>Today's Purchase</span>
                </div>
                <div class="dash-kpi-val">₹{{ number_format($todayPurchases, 2) }}</div>
                <div class="dash-kpi-subtext">
                    This Month: <strong style="color: #D97706;">₹{{ number_format($monthPurchases, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- KPI 3: Receivables -->
        <div class="dash-kpi-card kpi-blue">
            <div class="dash-kpi-icon-box icon-blue">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div class="dash-kpi-body">
                <div class="dash-kpi-label">
                    <span>Total Receivable</span>
                </div>
                <div class="dash-kpi-val val-blue">₹{{ number_format($totalReceivable, 2) }}</div>
                <div class="dash-kpi-subtext">
                    Pending from <strong>{{ $receivableCustomersCount }}</strong> Customers
                </div>
            </div>
        </div>

        <!-- KPI 4: Payables -->
        <div class="dash-kpi-card kpi-rose">
            <div class="dash-kpi-icon-box icon-rose">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <div class="dash-kpi-body">
                <div class="dash-kpi-label">
                    <span>Total Payable</span>
                </div>
                <div class="dash-kpi-val val-rose">₹{{ number_format($totalPayable, 2) }}</div>
                <div class="dash-kpi-subtext">
                    Due to <strong>{{ $payableVendorsCount }}</strong> Vendors
                </div>
            </div>
        </div>

        <!-- KPI 5: Inventory Valuation -->
        <div class="dash-kpi-card kpi-purple">
            <div class="dash-kpi-icon-box icon-purple">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div class="dash-kpi-body">
                <div class="dash-kpi-label">
                    <span>Stock Valuation</span>
                </div>
                <div class="dash-kpi-val val-purple">₹{{ number_format($stockValuation, 2) }}</div>
                <div class="dash-kpi-subtext">
                    Across <strong>{{ $totalItems }}</strong> Catalog Products
                </div>
            </div>
        </div>

        <!-- KPI 6: Stock Alerts -->
        <div class="dash-kpi-card {{ $lowStockCount > 0 ? 'kpi-danger' : 'kpi-success' }}">
            <div class="dash-kpi-icon-box {{ $lowStockCount > 0 ? 'icon-red' : 'icon-emerald' }}">
                <i class="fa-solid {{ $lowStockCount > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check' }}"></i>
            </div>
            <div class="dash-kpi-body">
                <div class="dash-kpi-label">
                    <span>Stock Alerts</span>
                </div>
                <div class="dash-kpi-val {{ $lowStockCount > 0 ? 'val-red' : 'val-green' }}">
                    {{ $lowStockCount }} <span style="font-size: 0.85rem; font-weight: 600;">Items</span>
                </div>
                <div class="dash-kpi-subtext">
                    @if($lowStockCount > 0)
                        <a href="{{ route('admin.inventory.low-stock-alert') }}" style="color: #DC2626; font-weight: 700; text-decoration: none;">
                            View Reorder List &rarr;
                        </a>
                    @else
                        <span style="color: #059669; font-weight: 600;">All Buffers Optimal</span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Analytics Charts Grid -->
    <div class="dash-charts-grid">

        <!-- Chart 1: Financial Turnover Trend -->
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-header-left">
                    <div class="dash-card-icon-box">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>
                    <div>
                        <h3 class="dash-card-title">Financial Turnover Trend</h3>
                        <p class="dash-card-subtitle">6-Month consolidated Sales vs. Purchases (Regular + Weighbridge)</p>
                    </div>
                </div>
                <div class="dash-chart-legend">
                    <span style="display: flex; align-items: center; color: #5B841E;">
                        <span class="dash-legend-dot" style="background: #5B841E;"></span> Sales Invoiced
                    </span>
                    <span style="display: flex; align-items: center; color: #D97706;">
                        <span class="dash-legend-dot" style="background: #D97706;"></span> Purchases Inward
                    </span>
                </div>
            </div>
            <div class="dash-card-body">
                <div class="dash-chart-canvas-wrap">
                    <canvas id="chart-sales-purchases-trend"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Inventory Valuation by Category -->
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-header-left">
                    <div class="dash-card-icon-box" style="background: rgba(124, 58, 237, 0.12); color: #7C3AED;">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="dash-card-title">Stock Valuation Share</h3>
                        <p class="dash-card-subtitle">Category-wise inventory asset distribution</p>
                    </div>
                </div>
            </div>
            <div class="dash-card-body">
                <div class="dash-chart-canvas-wrap donut-wrap">
                    <canvas id="chart-stock-category-distribution"></canvas>
                </div>
                <div class="dash-chart-footer-stat">
                    <span>Total Physical Inventory Value:</span>
                    <strong style="color: #0F172A; font-family: Consolas, monospace; font-size: 0.88rem;">₹{{ number_format($stockValuation, 2) }}</strong>
                </div>
            </div>
        </div>

    </div>

    <!-- Lower Operational Grid: Recent Ledger Feed + Operational Sidebar -->
    <div class="dash-lower-grid">

        <!-- Recent Transactions Feed Card -->
        <div class="dash-card">
            <div class="dash-card-header">
                <div class="dash-card-header-left">
                    <div class="dash-card-icon-box">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <h3 class="dash-card-title">Recent Operations &amp; Invoices</h3>
                        <p class="dash-card-subtitle">Live ledger feed across sales, purchases &amp; payment vouchers</p>
                    </div>
                </div>
                <a href="{{ route('admin.reports.sales-report') }}" class="btn btn-sm btn-outline" style="font-size: 0.76rem; font-weight: 600; padding: 4px 12px; border-radius: 8px;">
                    Sales Register &rarr;
                </a>
            </div>

            <div class="table-responsive" style="margin: 0; padding: 0;">
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Voucher / Ref</th>
                            <th>Type</th>
                            <th>Party / Customer / Vendor</th>
                            <th style="text-align: right;">Amount (₹)</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                            @php
                                $partyName = $tx['party'] ?? 'Walk-in Party';
                                $words = explode(' ', trim($partyName));
                                $initials = strtoupper(substr($words[0] ?? 'V', 0, 1) . substr($words[1] ?? '', 0, 1));
                                if (empty(trim($initials))) { $initials = 'VU'; }
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ $tx['url'] }}" class="dash-txn-ref">{{ $tx['no'] }}</a>
                                    <div class="dash-txn-date">{{ $tx['date']->format('d M Y') }}</div>
                                </td>
                                <td>
                                    <span class="dash-badge-type" style="background: {{ $tx['type_badge'] }}; color: {{ $tx['type_color'] }};">
                                        <i class="fa-solid {{ $tx['icon'] }}"></i> {{ $tx['type'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dash-party-cell">
                                        <div class="dash-avatar-circle" title="{{ $partyName }}">
                                            {{ $initials }}
                                        </div>
                                        <div class="dash-party-name">
                                            {{ Str::limit($partyName, 26) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="dash-amount-val">
                                    ₹{{ number_format($tx['amount'], 2) }}
                                </td>
                                <td style="text-align: center;">
                                    <span class="dash-status-pill success">
                                        <i class="fa-solid fa-circle" style="font-size: 5px;"></i> {{ ucfirst($tx['status']) }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ $tx['url'] }}" class="btn btn-sm btn-outline" style="padding: 3px 8px; border-radius: 6px; font-size: 0.72rem;" title="View Details">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="dash-table-empty">
                                    <i class="fa-solid fa-receipt"></i>
                                    <div style="font-weight: 700; color: #475569; font-size: 0.95rem;">No Recent Transactions Found</div>
                                    <div style="font-size: 0.78rem;">Create a sales invoice or purchase bill to populate real-time activity.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Operational Sidebar: Stock Watchlist + Cash & Bank + Launchpad -->
        <div class="dash-sidebar-stack">

            <!-- Card 1: Low Stock Watchlist -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-header-left">
                        <div class="dash-card-icon-box" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <h4 class="dash-card-title">Stock Reorder Watchlist</h4>
                            <p class="dash-card-subtitle">Items requiring replenishment attention</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.inventory.low-stock-alert') }}" class="btn btn-sm btn-outline" style="font-size: 0.72rem; padding: 3px 8px; border-radius: 6px;">
                        Manage
                    </a>
                </div>
                <div class="dash-card-body" style="padding: 1rem 1.15rem;">
                    @forelse($lowStockItems as $item)
                        @php
                            $curr = (float) $item->current_stock;
                            $min = (float) ($item->min_stock_alert > 0 ? $item->min_stock_alert : 100);
                            $pct = min(100, round(($curr / max(1, $min)) * 100, 1));
                            $isCritical = $curr <= $min;
                        @endphp
                        <div class="dash-stock-row">
                            <div class="dash-stock-top-row">
                                <div class="dash-stock-title-wrap">
                                    <span class="dash-stock-name" title="{{ $item->name }}">{{ $item->name }}</span>
                                </div>
                                <span class="dash-stock-badge {{ $isCritical ? 'critical' : 'warning' }}">
                                    {{ $curr }} {{ $item->unit ?? 'Kg' }}
                                </span>
                            </div>
                            <div class="dash-stock-meta-row">
                                <span>Code: <code>{{ $item->code }}</code></span>
                                <span>Threshold: {{ $min }} {{ $item->unit ?? 'Kg' }}</span>
                            </div>
                            <div class="dash-stock-progress-track">
                                <div class="dash-stock-progress-bar {{ $isCritical ? 'critical' : 'warning' }}" style="width: {{ $pct }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 1.5rem 0.5rem; color: #059669;">
                            <i class="fa-solid fa-circle-check" style="font-size: 1.8rem; margin-bottom: 0.4rem; display: block;"></i>
                            <div style="font-weight: 700; font-size: 0.88rem;">Stock Buffers Healthy</div>
                            <div style="font-size: 0.74rem; color: #64748B;">All catalog items are currently above alert thresholds.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Card 2: Cash & Bank Monthly Turnover -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-header-left">
                        <div class="dash-card-icon-box" style="background: rgba(91, 132, 30, 0.12); color: #5B841E;">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div>
                            <h4 class="dash-card-title">Cash &amp; Bank Liquidity</h4>
                            <p class="dash-card-subtitle">Month-to-date inflows &amp; outflows</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.cash-bank-register') }}" style="color: #5B841E; font-size: 0.75rem; font-weight: 700; text-decoration: none;">
                        Register &rarr;
                    </a>
                </div>
                <div class="dash-card-body" style="padding: 1.15rem;">
                    <div class="dash-cashflow-split">
                        <div class="dash-cashbox receipts">
                            <div class="dash-cashbox-title">
                                <i class="fa-solid fa-arrow-down-left"></i> Receipts Inflow
                            </div>
                            <div class="dash-cashbox-amount">₹{{ number_format($monthReceipts, 2) }}</div>
                            <div class="dash-cashbox-note">Customer collections</div>
                        </div>
                        <div class="dash-cashbox payments">
                            <div class="dash-cashbox-title">
                                <i class="fa-solid fa-arrow-up-right"></i> Payments Outflow
                            </div>
                            <div class="dash-cashbox-amount">₹{{ number_format($monthPayments, 2) }}</div>
                            <div class="dash-cashbox-note">Vendor disbursements</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Quick Action Launchpad -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <div class="dash-card-header-left">
                        <div class="dash-card-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <div>
                            <h4 class="dash-card-title">Operational Shortcuts</h4>
                            <p class="dash-card-subtitle">Fast ERP transaction launchpad</p>
                        </div>
                    </div>
                </div>
                <div class="dash-card-body" style="padding: 1rem 1.15rem;">
                    <div class="dash-launchpad-grid">
                        <a href="{{ route('admin.transactions.wb-purchase-entry.create') }}" class="dash-launchpad-btn">
                            <div class="dash-launchpad-icon" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                                <i class="fa-solid fa-truck-ramp-box"></i>
                            </div>
                            <div class="dash-launchpad-text">
                                <div class="dash-launchpad-label">WB Purchase</div>
                                <div class="dash-launchpad-sub">Weighbridge Inward</div>
                            </div>
                        </a>
                        <a href="{{ route('admin.inventory.stock-adjustment.create') }}" class="dash-launchpad-btn">
                            <div class="dash-launchpad-icon" style="background: rgba(91, 132, 30, 0.12); color: #5B841E;">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="dash-launchpad-text">
                                <div class="dash-launchpad-label">Stock Audit</div>
                                <div class="dash-launchpad-sub">Physical Reconcile</div>
                            </div>
                        </a>
                        <a href="{{ route('admin.transactions.receipt-voucher.create') }}" class="dash-launchpad-btn">
                            <div class="dash-launchpad-icon" style="background: rgba(5, 150, 105, 0.12); color: #059669;">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="dash-launchpad-text">
                                <div class="dash-launchpad-label">Receipt Voucher</div>
                                <div class="dash-launchpad-sub">Record Inflow</div>
                            </div>
                        </a>
                        <a href="{{ route('admin.reports.order-report') }}" class="dash-launchpad-btn">
                            <div class="dash-launchpad-icon" style="background: rgba(124, 58, 237, 0.12); color: #7C3AED;">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div class="dash-launchpad-text">
                                <div class="dash-launchpad-label">Order Report</div>
                                <div class="dash-launchpad-sub">Dispatch Status</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- Dynamic Chart Initialization Script -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDashboardCharts();
});

function initDashboardCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(initDashboardCharts, 150);
        return;
    }

    // Chart 1: 6-Month Sales vs Purchases Trend
    const salesCanvas = document.getElementById('chart-sales-purchases-trend');
    if (salesCanvas) {
        const months = @json($chartMonths);
        const salesData = @json($chartSales);
        const purchaseData = @json($chartPurchases);

        new Chart(salesCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Sales Invoiced',
                        data: salesData,
                        backgroundColor: 'rgba(91, 132, 30, 0.88)',
                        borderColor: '#5B841E',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    },
                    {
                        label: 'Purchases Inward',
                        data: purchaseData,
                        backgroundColor: 'rgba(217, 119, 6, 0.88)',
                        borderColor: '#D97706',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        titleColor: '#F8FAFC',
                        bodyColor: '#F8FAFC',
                        cornerRadius: 8,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₹' + Number(context.raw).toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#64748B' }
                    },
                    y: {
                        grid: { color: '#F1F5F9' },
                        ticks: {
                            font: { size: 11, family: "Consolas, monospace" },
                            color: '#64748B',
                            callback: function(value) {
                                if (value >= 10000000) return '₹' + (value / 10000000).toFixed(1) + 'Cr';
                                if (value >= 100000) return '₹' + (value / 100000).toFixed(1) + 'L';
                                if (value >= 1000) return '₹' + (value / 1000).toFixed(0) + 'k';
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
    }

    // Chart 2: Category Valuation Share
    const catCanvas = document.getElementById('chart-stock-category-distribution');
    if (catCanvas) {
        const catLabels = @json($chartCategoryLabels);
        const catValues = @json($chartCategoryValues);

        new Chart(catCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: [
                        '#5B841E',
                        '#2563EB',
                        '#D97706',
                        '#7C3AED',
                        '#E11D48',
                        '#059669',
                        '#0891B2'
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 11, family: "'Inter', sans-serif" },
                            color: '#475569',
                            padding: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        cornerRadius: 8,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ₹' + Number(context.raw).toLocaleString('en-IN');
                            }
                        }
                    }
                }
            }
        });
    }
}
</script>
@endpush
@endsection
