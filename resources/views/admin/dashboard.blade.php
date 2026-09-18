@extends('admin.layouts.app')

@section('title', 'Dashboard - VIKAS UDHYOG ERP')
@section('page_code', 'dashboard')

@section('content')
<section class="view-section active" id="view-dashboard">
    <div class="page-header">
        <div>
            <h1 class="page-title">Good Morning, {{ session('vu_user_name', 'Admin') }}</h1>
            <p class="page-subtitle">Here's what's happening with Vikas Udhyog today.</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-outline" onclick="App.navigateTo('inv-overview')"><i class="fa-solid fa-boxes-stacked"></i> Inventory Overview</button>
            <button class="btn btn-primary" onclick="App.openModal('modal-sales-invoice')"><i class="fa-solid fa-plus"></i> New Sales / Purchase Order</button>
        </div>
    </div>

    <!-- KPI Cards Grid -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-sales">
            <div class="kpi-top">
                <span class="kpi-title">Today's Sales</span>
                <div class="kpi-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div>
            </div>
            <div class="kpi-value" id="kpi-sales-val">â‚¹1,28,450</div>
            <div class="kpi-bottom"><span class="trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +14.2%</span> <span class="kpi-subtext">vs yesterday</span></div>
        </div>

        <div class="kpi-card kpi-purchase">
            <div class="kpi-top">
                <span class="kpi-title">Today's Purchase</span>
                <div class="kpi-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            </div>
            <div class="kpi-value" id="kpi-purchase-val">â‚¹84,200</div>
            <div class="kpi-bottom"><span class="trend-up"><i class="fa-solid fa-arrow-trend-up"></i> +8.5%</span> <span class="kpi-subtext">vs last week</span></div>
        </div>

        <div class="kpi-card kpi-receivable">
            <div class="kpi-top">
                <span class="kpi-title">Total Receivable</span>
                <div class="kpi-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            </div>
            <div class="kpi-value" id="kpi-receivable-val">â‚¹4,82,600</div>
            <div class="kpi-bottom"><span class="kpi-subtext">From 5 Customers</span></div>
        </div>

        <div class="kpi-card kpi-payable">
            <div class="kpi-top">
                <span class="kpi-title">Total Payable</span>
                <div class="kpi-icon"><i class="fa-solid fa-credit-card"></i></div>
            </div>
            <div class="kpi-value" id="kpi-payable-val">â‚¹2,74,350</div>
            <div class="kpi-bottom"><span class="kpi-subtext">To 4 Vendors</span></div>
        </div>

        <div class="kpi-card kpi-stock">
            <div class="kpi-top">
                <span class="kpi-title">Current Stock Value</span>
                <div class="kpi-icon"><i class="fa-solid fa-warehouse"></i></div>
            </div>
            <div class="kpi-value" id="kpi-stock-val">â‚¹12,45,800</div>
            <div class="kpi-bottom"><span class="kpi-subtext">8 Product Categories</span></div>
        </div>

        <div class="kpi-card kpi-orders">
            <div class="kpi-top">
                <span class="kpi-title">Pending Orders</span>
                <div class="kpi-icon"><i class="fa-solid fa-clock"></i></div>
            </div>
            <div class="kpi-value" id="kpi-orders-val">24</div>
            <div class="kpi-bottom"><span class="kpi-subtext">Needs Dispatch</span></div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="charts-grid">
        <div class="card chart-card">
            <div class="card-header">
                <h3 class="card-title">Sales vs Purchase</h3>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Monthly Comparison</span>
            </div>
            <div class="chart-container"><canvas id="chart-sales-vs-purchase"></canvas></div>
        </div>

        <div class="card chart-card">
            <div class="card-header">
                <h3 class="card-title">Sales Trend</h3>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Current Month</span>
            </div>
            <div class="chart-container"><canvas id="chart-sales-trend"></canvas></div>
        </div>

        <div class="card chart-card">
            <div class="card-header">
                <h3 class="card-title">Top Selling Products</h3>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Volume (KG)</span>
            </div>
            <div class="chart-container"><canvas id="chart-top-products"></canvas></div>
        </div>

        <div class="card chart-card">
            <div class="card-header">
                <h3 class="card-title">Stock Category Distribution</h3>
                <span style="font-size: 0.8rem; color: var(--text-muted);">Share %</span>
            </div>
            <div class="chart-container"><canvas id="chart-stock-distribution"></canvas></div>
        </div>
    </div>

    <!-- Lower Dashboard Tables Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
                <button class="btn btn-sm btn-outline" onclick="App.navigateTo('txn-sales-invoice')">View All</button>
            </div>
            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Party Name</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="recent-transactions-list"></tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="color: var(--status-danger);"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock Alerts</h3>
                <button class="btn btn-sm btn-outline" onclick="App.navigateTo('inv-low-stock')">Manage</button>
            </div>
            <div id="low-stock-alert-list"></div>
        </div>
    </div>
</section>
@endsection

