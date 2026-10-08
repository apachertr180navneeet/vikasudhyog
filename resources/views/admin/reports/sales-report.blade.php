@extends('admin.layouts.app')

@section('title', 'Sales Report - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-sales')

@section('content')
<section class="view-section active" id="view-rpt-sales">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Sales Report</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-chart-line text-primary"></i> Sales Report &amp; Outward Register
            </h1>
            <p class="erp-page-subtitle">
                Comprehensive customer sales analytics, finished goods outward dispatches, tax invoice register &amp; output GST liability log.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Sales Register">
                <i class="fa-solid fa-print"></i> Print Register
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Filtered Register to CSV">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.transactions.sales-entry.create') }}" class="btn btn-primary erp-btn-header-primary" title="Record New Sales Invoice">
                <i class="fa-solid fa-plus"></i> New Sales Entry
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success" style="margin-bottom: 1.25rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- 4-Card KPI Analytics Grid -->
    <div class="erp-kpi-grid">
        <!-- 1. Total Sales Turnover -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #0F172A;">
            <div class="erp-kpi-icon-box" style="background: rgba(15, 23, 42, 0.08); color: #0F172A;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Sales Turnover</div>
                <div class="erp-kpi-val font-monospace" style="color: #0F172A;">
                    ₹{{ number_format($stats['total_grand'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Taxable: <strong class="font-monospace">₹{{ number_format($stats['total_taxable'] ?? 0, 2) }}</strong> &bull; {{ $stats['total_invoices'] ?? 0 }} Invoices
                </div>
            </div>
        </div>

        <!-- 2. Total Quantity Dispatched -->
        <div class="card erp-kpi-card erp-kpi-primary" style="border-left: 4px solid #5B841E;">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Outward Quantity</div>
                <div class="erp-kpi-val font-monospace" style="color: #5B841E;">
                    {{ number_format($stats['total_quantity'] ?? 0, 1) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Finished products dispatched across orders
                </div>
            </div>
        </div>

        <!-- 3. Output GST Collected -->
        <div class="card erp-kpi-card erp-kpi-success" style="border-left: 4px solid #059669;">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Output GST Liability</div>
                <div class="erp-kpi-val font-monospace" style="color: #059669;">
                    ₹{{ number_format($stats['total_tax'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #059669; margin-top: 2px; font-weight: 600;">
                    Tax collected on taxable supply
                </div>
            </div>
        </div>

        <!-- 4. Outstanding Receivables -->
        <div class="card erp-kpi-card" style="border-left: 4px solid {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#10B981' }};">
            <div class="erp-kpi-icon-box" style="background: {{ ($stats['total_pending'] ?? 0) > 0 ? 'rgba(220, 38, 38, 0.12)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#10B981' }};">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Pending Receivables Balance</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#059669' }};">
                    ₹{{ number_format($stats['total_pending'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Collected: <strong class="font-monospace" style="color: #059669;">₹{{ number_format($stats['total_paid'] ?? 0, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Card -->
    <div class="card erp-table-filter-header" style="margin-bottom: 1.25rem; padding: 1.15rem; background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0;">
        
        <!-- Date Preset Shortcut Pills & Mode Switcher -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em; margin-right: 4px;">
                    <i class="fa-regular fa-calendar-check me-1"></i> Quick Presets:
                </span>
                @php
                    $presets = [
                        'all'         => 'All Past',
                        'today'       => 'Today',
                        'this_week'   => 'This Week',
                        'this_month'  => 'This Month',
                        'last_month'  => 'Last Month',
                        'this_quarter'=> 'This Quarter',
                        'this_fy'     => 'This FY',
                    ];
                    $currentPreset = $filters['date_preset'] ?? 'all';
                @endphp
                @foreach($presets as $pKey => $pLabel)
                    <a href="{{ request()->fullUrlWithQuery(['date_preset' => $pKey, 'from_date' => null, 'to_date' => null]) }}" 
                       class="btn btn-sm {{ $currentPreset === $pKey ? 'btn-primary' : 'btn-outline' }}" 
                       style="padding: 3px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                        {{ $pLabel }}
                    </a>
                @endforeach
            </div>

            <!-- View Mode Tabs (Pill Switcher) -->
            <div style="display: inline-flex; background: #F1F5F9; padding: 3px; border-radius: 10px; border: 1px solid #E2E8F0;">
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'bills']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'bills' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-list-check me-1"></i> Bill-wise Register
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'items']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'items' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-boxes-packing me-1"></i> Item-wise Analysis
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'customers']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'customers' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-users-viewfinder me-1"></i> Customer Summary
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.reports.sales-report') }}" method="GET" class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
            <input type="hidden" name="view_mode" value="{{ $viewMode }}">
            <input type="hidden" name="date_preset" value="{{ $currentPreset === 'custom' ? 'custom' : $currentPreset }}">

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; flex: 1;">
                
                <!-- Keyword Search -->
                <div class="erp-search-wrap" style="min-width: 220px; flex: 1; max-width: 300px;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Invoice, Customer, Docket..." class="form-control erp-search-input">
                </div>

                <!-- Custom Dates -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">FROM:</span>
                    <input type="date" name="from_date" class="form-control erp-filter-select" value="{{ $filters['from_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">TO:</span>
                    <input type="date" name="to_date" class="form-control erp-filter-select" value="{{ $filters['to_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                </div>

                <!-- Customer Selector -->
                <select name="customer_id" class="form-control erp-filter-select" style="min-width: 160px;">
                    <option value="">All Customers</option>
                    @foreach($customers as $cst)
                        <option value="{{ $cst->id }}" {{ ($filters['customer_id'] ?? '') == $cst->id ? 'selected' : '' }}>
                            {{ $cst->name }} ({{ $cst->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Item Selector -->
                <select name="item_id" class="form-control erp-filter-select" style="min-width: 160px;">
                    <option value="">All Finished Goods / Products</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}" {{ ($filters['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->name }} ({{ $itm->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Broker Selector -->
                <select name="broker_id" class="form-control erp-filter-select" style="min-width: 140px;">
                    <option value="">All Brokers</option>
                    @foreach($brokers as $brk)
                        <option value="{{ $brk->id }}" {{ ($filters['broker_id'] ?? '') == $brk->id ? 'selected' : '' }}>
                            {{ $brk->name }} ({{ $brk->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Bill Type Selector -->
                <select name="bill_type" class="form-control erp-filter-select" style="min-width: 135px;">
                    <option value="all">All Bill Types</option>
                    <option value="with_bill" {{ ($filters['bill_type'] ?? '') === 'with_bill' ? 'selected' : '' }}>Tax Invoice (With Bill)</option>
                    <option value="without_bill" {{ ($filters['bill_type'] ?? '') === 'without_bill' ? 'selected' : '' }}>Without Bill / WB</option>
                </select>

                <!-- Order Type Selector -->
                <select name="order_type" class="form-control erp-filter-select" style="min-width: 130px;">
                    <option value="all">All Order Types</option>
                    <option value="Medium" {{ ($filters['order_type'] ?? '') === 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="Urgent" {{ ($filters['order_type'] ?? '') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="Fast" {{ ($filters['order_type'] ?? '') === 'Fast' ? 'selected' : '' }}>Fast</option>
                    <option value="Ready Delivery" {{ ($filters['order_type'] ?? '') === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                </select>

                <!-- Payment Status Selector -->
                <select name="payment_status" class="form-control erp-filter-select" style="min-width: 130px;">
                    <option value="all">All Payment Status</option>
                    <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>

                <!-- Document Status Selector -->
                <select name="status" class="form-control erp-filter-select" style="min-width: 125px;">
                    <option value="all">All Status</option>
                    <option value="dispatched" {{ ($filters['status'] ?? '') === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: auto;">
                <button type="submit" class="btn btn-primary btn-sm erp-btn-filter" style="padding: 0.45rem 1rem;">
                    <i class="fa-solid fa-filter me-1"></i> Apply Filter
                </button>
                <a href="{{ route('admin.reports.sales-report', ['view_mode' => $viewMode]) }}" class="btn btn-outline btn-sm erp-btn-filter-clear" style="padding: 0.45rem 0.85rem;" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

    </div>

    <!-- Edge-to-Edge Data Card -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); background: #FFFFFF;">

        <!-- MODE 1: BILL-WISE REGISTER TABLE -->
        @if($viewMode === 'bills')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Date &amp; Invoice</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer &amp; Destination</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Broker &amp; Transport</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Bill Type</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Taxable Amount</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Output GST</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Grand Total</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Payment</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Status</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;" class="erp-actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billsData as $sale)
                            @php
                                $balanceDue = max(0, (float)$sale->grand_total - (float)$sale->paid_amount);
                            @endphp
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Date & Invoice -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.88rem; color: #0F172A;">
                                        {{ $sale->sale_date ? $sale->sale_date->format('d M Y') : '—' }}
                                    </div>
                                    <div style="font-size: 0.78rem; font-weight: 600; color: #5B841E; margin-top: 2px;">
                                        Inv #{{ $sale->invoice_no ?: 'Pending' }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #64748B;">
                                        {{ $sale->sale_no }}
                                    </div>
                                </td>

                                <!-- Customer & Destination -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; flex-shrink: 0; box-shadow: 0 2px 5px rgba(91, 132, 30, 0.25);">
                                            {{ $sale->customer ? $sale->customer->initials : 'C' }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                                {{ $sale->customer ? $sale->customer->name : 'N/A' }}
                                            </div>
                                            <div style="font-size: 0.74rem; color: #64748B;">
                                                <span class="font-monospace" style="font-weight: 600; color: #475569;">{{ $sale->customer ? $sale->customer->code : '—' }}</span>
                                                @if($sale->customer && $sale->customer->city)
                                                    &bull; <i class="fa-solid fa-location-dot me-1" style="font-size: 0.7rem;"></i>{{ $sale->customer->city }}
                                                @endif
                                            </div>
                                            @if($sale->customer && $sale->customer->gstin)
                                                <div class="font-monospace" style="font-size: 0.7rem; color: #64748B;">
                                                    GSTIN: {{ $sale->customer->gstin }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Broker & Transport -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-size: 0.84rem; font-weight: 600; color: #334155;">
                                        {{ $sale->broker ? $sale->broker->name : 'Direct Sale' }}
                                    </div>
                                    <div style="margin-top: 3px; display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                        <span class="badge" style="font-size: 0.68rem; background: #EEF2F6; color: #475569; font-weight: 600; padding: 2px 6px;">
                                            {{ $sale->order_type ?: 'Standard' }}
                                        </span>
                                        @if($sale->vehicle_no)
                                            <span class="font-monospace" style="font-size: 0.7rem; color: #64748B;">
                                                <i class="fa-solid fa-truck-moving me-1"></i>{{ $sale->vehicle_no }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Bill Type -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    @if($sale->bill_type === 'with_bill')
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(91, 132, 30, 0.25);">
                                            <i class="fa-solid fa-file-invoice me-1"></i> Tax Invoice
                                        </span>
                                    @else
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                            <i class="fa-solid fa-receipt me-1"></i> Without Bill
                                        </span>
                                    @endif
                                </td>

                                <!-- Taxable Subtotal -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #1E293B;">
                                        ₹{{ number_format((float)$sale->subtotal, 2) }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #64748B;">
                                        {{ $sale->items->count() }} line items
                                    </div>
                                </td>

                                <!-- Output GST -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #059669;">
                                        ₹{{ number_format((float)$sale->tax_amount, 2) }}
                                    </div>
                                </td>

                                <!-- Grand Total -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format((float)$sale->grand_total, 2) }}
                                    </div>
                                    @if((float)$sale->round_off != 0)
                                        <div class="font-monospace" style="font-size: 0.7rem; color: #64748B;">
                                            R/O: {{ number_format((float)$sale->round_off, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Payment Status & Pending Balance -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    @if($sale->payment_status === 'paid' || $balanceDue <= 0.01)
                                        <span class="badge" style="background: rgba(5, 150, 105, 0.12); color: #059669; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Paid
                                        </span>
                                    @elseif($sale->payment_status === 'partial')
                                        <span class="badge" style="background: rgba(217, 119, 6, 0.12); color: #D97706; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Partial
                                        </span>
                                        <div class="font-monospace" style="font-size: 0.72rem; color: #DC2626; margin-top: 3px; font-weight: 700;">
                                            Due: ₹{{ number_format($balanceDue, 2) }}
                                        </div>
                                    @else
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i> Unpaid
                                        </span>
                                        <div class="font-monospace" style="font-size: 0.72rem; color: #DC2626; margin-top: 3px; font-weight: 700;">
                                            Due: ₹{{ number_format($balanceDue, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Dispatch Status -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    @if($sale->status === 'completed')
                                        <span class="badge" style="background: rgba(5, 150, 105, 0.12); color: #059669; font-weight: 700; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">
                                            Completed
                                        </span>
                                    @elseif($sale->status === 'dispatched')
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-weight: 700; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">
                                            Dispatched
                                        </span>
                                    @elseif($sale->status === 'cancelled')
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-weight: 700; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;" class="erp-actions-cell">
                                    <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <a href="{{ route('admin.transactions.sales-entry.show', $sale->id) }}" 
                                           class="btn btn-sm btn-icon" 
                                           style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #5B841E; display: flex; align-items: center; justify-content: center;" 
                                           title="View Sales Invoice Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.transactions.sales-entry.edit', $sale->id) }}" 
                                           class="btn btn-sm btn-icon" 
                                           style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #0284C7; display: flex; align-items: center; justify-content: center;" 
                                           title="Edit Sales Entry">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Sales Invoices Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Try adjusting your date presets, customer filter, or search query.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($billsData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                            <tr>
                                <td colspan="4" style="padding: 0.95rem 1.15rem; color: #1E293B; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.04em;">
                                    Filtered Totals ({{ $billsData->total() }} Invoices):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #1E293B;" class="font-monospace">
                                    ₹{{ number_format($stats['total_taxable'] ?? 0, 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #059669;" class="font-monospace">
                                    ₹{{ number_format($stats['total_tax'] ?? 0, 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #0F172A; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($stats['total_grand'] ?? 0, 2) }}
                                </td>
                                <td colspan="3" style="padding: 0.95rem 1.15rem; text-align: center; color: #64748B; font-size: 0.8rem;">
                                    Outstanding: <strong class="font-monospace" style="color: {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($stats['total_pending'] ?? 0, 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($billsData->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FFFFFF; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="font-size: 0.82rem; color: #64748B;">
                        Showing {{ $billsData->firstItem() ?? 0 }} to {{ $billsData->lastItem() ?? 0 }} of {{ $billsData->total() }} sales invoices
                    </div>
                    <div>
                        {{ $billsData->links() }}
                    </div>
                </div>
            @endif

        <!-- MODE 2: ITEM-WISE ANALYSIS TABLE -->
        @elseif($viewMode === 'items')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Item Code</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Product Name &amp; Category</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Qty Sold</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Unit</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Avg Selling Rate</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Sales Turnover</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Bills Count</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Top Purchasing Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsData as $itemRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Item Code -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                        {{ $itemRow->item_code }}
                                    </span>
                                </td>

                                <!-- Product Name & Category -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                        {{ $itemRow->item_name }}
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.7rem; font-weight: 600; padding: 2px 6px;">
                                            {{ $itemRow->category }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Total Quantity Sold -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #5B841E;">
                                        {{ number_format($itemRow->total_qty, 2) }}
                                    </div>
                                </td>

                                <!-- Unit -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge" style="background: #EEF2F6; color: #475569; font-weight: 700; font-size: 0.74rem;">
                                        {{ $itemRow->unit }}
                                    </span>
                                </td>

                                <!-- Avg Rate -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #1E293B;">
                                        ₹{{ number_format($itemRow->avg_rate, 2) }}
                                    </div>
                                </td>

                                <!-- Total Turnover -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($itemRow->total_amount, 2) }}
                                    </div>
                                </td>

                                <!-- Bills Count -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; font-size: 0.76rem; font-weight: 700; padding: 3px 8px;">
                                        {{ $itemRow->bills_count }} Bills
                                    </span>
                                </td>

                                <!-- Top Customer -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #1E293B; font-size: 0.86rem;">
                                        <i class="fa-solid fa-building-user text-primary me-1" style="font-size: 0.75rem;"></i>
                                        {{ $itemRow->top_customer }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Product Sales Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No sales line items match the selected filter criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- MODE 3: CUSTOMER-WISE SUMMARY TABLE -->
        @elseif($viewMode === 'customers')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer Code</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer Firm Name</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Location &amp; GSTIN</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Invoices Count</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Qty Sold</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Turnover</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Pending Receivables</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customersData as $cRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Customer Code -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                        {{ $cRow->customer_code }}
                                    </span>
                                </td>

                                <!-- Customer Firm Name -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.94rem;">
                                        {{ $cRow->customer_name }}
                                    </div>
                                    @if($cRow->phone && $cRow->phone !== '—')
                                        <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                                            <i class="fa-solid fa-phone me-1" style="font-size: 0.7rem;"></i>{{ $cRow->phone }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Location & GSTIN -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #334155; font-size: 0.84rem;">
                                        <i class="fa-solid fa-location-dot me-1 text-muted" style="font-size: 0.75rem;"></i>{{ $cRow->city }}
                                    </div>
                                    @if($cRow->gstin && $cRow->gstin !== '—')
                                        <div class="font-monospace" style="font-size: 0.7rem; color: #64748B;">
                                            {{ $cRow->gstin }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Invoices Count -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 8px;">
                                        {{ $cRow->bills_count }} Bills
                                    </span>
                                </td>

                                <!-- Total Qty Sold -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.95rem; color: #5B841E;">
                                        {{ number_format($cRow->total_qty, 2) }}
                                    </div>
                                </td>

                                <!-- Total Turnover -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($cRow->total_spend, 2) }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #059669;">
                                        Paid: ₹{{ number_format($cRow->total_paid, 2) }}
                                    </div>
                                </td>

                                <!-- Pending Receivables -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.95rem; color: {{ $cRow->pending_balance > 0 ? '#DC2626' : '#059669' }};">
                                        ₹{{ number_format($cRow->pending_balance, 2) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Customer Records Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No sales registered for customers under active filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>

</section>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #view-rpt-sales, #view-rpt-sales * {
        visibility: visible;
    }
    #view-rpt-sales {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .sidebar, .navbar, .erp-page-top-bar .erp-header-actions, .erp-table-filter-header, .erp-actions-cell, .pagination {
        display: none !important;
    }
    .erp-main-card {
        box-shadow: none !important;
        border: 1px solid #CBD5E1 !important;
    }
}
</style>
@endsection
