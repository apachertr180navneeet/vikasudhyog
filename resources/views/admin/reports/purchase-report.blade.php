@extends('admin.layouts.app')

@section('title', 'Purchase Report - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-purchase')

@section('content')
<section class="view-section active" id="view-rpt-purchase">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Purchase Report</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i> Purchase Report &amp; Procurement Register
            </h1>
            <p class="erp-page-subtitle">
                Comprehensive procurement analytics, vendor expenditure register, raw material cost tracking &amp; input tax credit (ITC) log.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Procurement Register">
                <i class="fa-solid fa-print"></i> Print Register
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Filtered Register to CSV">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="btn btn-primary erp-btn-header-primary" title="Record New Purchase Invoice">
                <i class="fa-solid fa-plus"></i> New Purchase Entry
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
        <!-- 1. Total Purchases Amount -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #0F172A;">
            <div class="erp-kpi-icon-box" style="background: rgba(15, 23, 42, 0.08); color: #0F172A;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Procurement Spend</div>
                <div class="erp-kpi-val font-monospace" style="color: #0F172A;">
                    ₹{{ number_format($stats['total_grand'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Taxable: <strong class="font-monospace">₹{{ number_format($stats['total_taxable'] ?? 0, 2) }}</strong> &bull; {{ $stats['total_invoices'] ?? 0 }} Invoices
                </div>
            </div>
        </div>

        <!-- 2. Total Quantity Procured -->
        <div class="card erp-kpi-card erp-kpi-primary" style="border-left: 4px solid #5B841E;">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Quantity Procured</div>
                <div class="erp-kpi-val font-monospace" style="color: #5B841E;">
                    {{ number_format($stats['total_quantity'] ?? 0, 1) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Raw material units received across lines
                </div>
            </div>
        </div>

        <!-- 3. Total GST / Tax Paid -->
        <div class="card erp-kpi-card erp-kpi-success" style="border-left: 4px solid #059669;">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Input Tax Credit (GST)</div>
                <div class="erp-kpi-val font-monospace" style="color: #059669;">
                    ₹{{ number_format($stats['total_tax'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #059669; margin-top: 2px; font-weight: 600;">
                    Eligible Input Tax Credit (ITC)
                </div>
            </div>
        </div>

        <!-- 4. Outstanding Payables -->
        <div class="card erp-kpi-card" style="border-left: 4px solid {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#10B981' }};">
            <div class="erp-kpi-icon-box" style="background: {{ ($stats['total_pending'] ?? 0) > 0 ? 'rgba(220, 38, 38, 0.12)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#10B981' }};">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Pending Payables Balance</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ ($stats['total_pending'] ?? 0) > 0 ? '#DC2626' : '#059669' }};">
                    ₹{{ number_format($stats['total_pending'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Paid: <strong class="font-monospace" style="color: #059669;">₹{{ number_format($stats['total_paid'] ?? 0, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Card -->
    <div class="card erp-table-filter-header" style="margin-bottom: 1.25rem; padding: 1.15rem; background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0;">
        
        <!-- Date Preset Shortcut Pills -->
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
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'vendors']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'vendors' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-truck-field me-1"></i> Vendor-wise Summary
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.reports.purchase-report') }}" method="GET" class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
            <input type="hidden" name="view_mode" value="{{ $viewMode }}">
            <input type="hidden" name="date_preset" value="{{ $currentPreset === 'custom' ? 'custom' : $currentPreset }}">

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; flex: 1;">
                
                <!-- Keyword Search -->
                <div class="erp-search-wrap" style="min-width: 220px; flex: 1; max-width: 320px;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Invoice, Docket, Vendor..." class="form-control erp-search-input">
                </div>

                <!-- Custom Dates -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">FROM:</span>
                    <input type="date" name="from_date" class="form-control erp-filter-select" value="{{ $filters['from_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">TO:</span>
                    <input type="date" name="to_date" class="form-control erp-filter-select" value="{{ $filters['to_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                </div>

                <!-- Vendor Selector -->
                <select name="vendor_id" class="form-control erp-filter-select" style="min-width: 160px;">
                    <option value="">All Suppliers / Vendors</option>
                    @foreach($vendors as $vnd)
                        <option value="{{ $vnd->id }}" {{ ($filters['vendor_id'] ?? '') == $vnd->id ? 'selected' : '' }}>
                            {{ $vnd->name }} ({{ $vnd->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Item Selector -->
                <select name="item_id" class="form-control erp-filter-select" style="min-width: 160px;">
                    <option value="">All Herbal Products</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}" {{ ($filters['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->name }} ({{ $itm->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Bill Type Selector -->
                <select name="bill_type" class="form-control erp-filter-select" style="min-width: 140px;">
                    <option value="all">All Bill Types</option>
                    <option value="with_bill" {{ ($filters['bill_type'] ?? '') === 'with_bill' ? 'selected' : '' }}>Tax Invoice (With Bill)</option>
                    <option value="without_bill" {{ ($filters['bill_type'] ?? '') === 'without_bill' ? 'selected' : '' }}>Without Bill / WB</option>
                </select>

                <!-- Payment Status Selector -->
                <select name="payment_status" class="form-control erp-filter-select" style="min-width: 130px;">
                    <option value="all">All Payment Status</option>
                    <option value="paid" {{ ($filters['payment_status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partial" {{ ($filters['payment_status'] ?? '') === 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="unpaid" {{ ($filters['payment_status'] ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>

                <!-- Submit & Clear Buttons -->
                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filter Criteria">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || !empty($filters['vendor_id']) || !empty($filters['item_id']) || ($filters['bill_type'] ?? 'all') !== 'all' || ($filters['payment_status'] ?? 'all') !== 'all' || !empty($filters['from_date']) || !empty($filters['to_date']) || $currentPreset !== 'all')
                    <a href="{{ route('admin.reports.purchase-report', ['view_mode' => $viewMode]) }}" class="btn btn-outline erp-btn-filter-clear" title="Reset All Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </div>

            <div class="erp-table-summary-count" style="margin: 0; font-size: 0.8rem;">
                Matching <strong>{{ $stats['total_invoices'] }}</strong> invoices &bull; <strong>{{ number_format($stats['total_quantity'], 1) }}</strong> units
            </div>
        </form>
    </div>

    <!-- Main Table Card Container (Edge-to-Edge Design System) -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">

        <!-- ========================================================================= -->
        <!-- MODE 1: BILL-WISE PURCHASE REGISTER                                      -->
        <!-- ========================================================================= -->
        @if($viewMode === 'bills')
            <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 125px;">Date</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 160px;">Invoice &amp; Docket</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 220px;">Supplier / Vendor</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Procured Items</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 130px;">Taxable Amt</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 110px;">GST (ITC)</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 140px;">Grand Total</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 135px;">Paid / Balance</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center; width: 110px;">Payment</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center; width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($billsData as $bill)
                            @php
                                $vnd = $bill->vendor;
                                $balancePending = max(0, (float)$bill->grand_total - (float)$bill->paid_amount);
                                $itemsCount = $bill->items->count();
                            @endphp
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                
                                <!-- Date -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #0F172A; font-size: 0.85rem;">
                                        {{ $bill->invoice_date ? $bill->invoice_date->format('d M Y') : '-' }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #94A3B8;">
                                        {{ $bill->created_at ? $bill->created_at->format('h:i A') : '' }}
                                    </div>
                                </td>

                                <!-- Invoice & Docket -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #0F172A; font-size: 0.88rem;">
                                        <a href="{{ route('admin.transactions.purchase-entry.show', $bill->id) }}" style="text-decoration: none; color: inherit;" title="View Invoice Profile">
                                            {{ $bill->invoice_no ?: 'No Inv #' }}
                                        </a>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                        <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 1px 6px; border: 1px solid #E2E8F0;">
                                            {{ $bill->purchase_no }}
                                        </span>
                                        @if($bill->bill_type === 'without_bill')
                                            <span class="badge" style="background: #FEF3C7; color: #B45309; font-size: 0.68rem; padding: 1px 5px;">WB</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Supplier / Vendor -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; font-size: 0.82rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                                            {{ $vnd ? $vnd->initials : 'VD' }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #0F172A; font-size: 0.88rem;">
                                                @if($vnd)
                                                    <a href="{{ route('admin.masters.vendor.show', $vnd->id) }}" style="text-decoration: none; color: inherit;">
                                                        {{ $vnd->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Unlinked Vendor</span>
                                                @endif
                                            </div>
                                            <div style="font-size: 0.73rem; color: #64748B;">
                                                {{ $vnd ? ($vnd->city ?: 'Rajasthan') : '—' }}
                                                @if($vnd && $vnd->gstin)
                                                    &bull; <span class="font-monospace">{{ substr($vnd->gstin, 0, 8) }}...</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Procured Items Summary -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    @if($bill->items->count() > 0)
                                        <div style="display: flex; flex-direction: column; gap: 2px;">
                                            @foreach($bill->items->take(2) as $pi)
                                                <div style="font-size: 0.8rem; color: #334155;">
                                                    <strong>{{ $pi->item ? $pi->item->name : 'Item' }}</strong>:
                                                    <span class="font-monospace text-muted">{{ number_format($pi->quantity, 1) }} {{ $pi->unit }}</span>
                                                    <span class="font-monospace" style="color: #64748B;">(@ ₹{{ number_format($pi->actual_rate, 2) }})</span>
                                                </div>
                                            @endforeach
                                            @if($bill->items->count() > 2)
                                                <div style="font-size: 0.72rem; color: #5B841E; font-weight: 600;">
                                                    +{{ $bill->items->count() - 2 }} more line items
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.8rem;">No item rows</span>
                                    @endif
                                </td>

                                <!-- Taxable Subtotal -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-size: 0.88rem; font-weight: 600; color: #334155;">
                                        ₹{{ number_format($bill->subtotal, 2) }}
                                    </div>
                                    @if((float)$bill->under_billing_total > 0)
                                        <div class="font-monospace" style="font-size: 0.7rem; color: #D97706;" title="Under Billing Amount">
                                            UB: +₹{{ number_format($bill->under_billing_total, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <!-- GST Tax -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-size: 0.88rem; font-weight: 700; color: #059669;">
                                        ₹{{ number_format($bill->tax_amount, 2) }}
                                    </div>
                                    <div style="font-size: 0.7rem; color: #059669;">
                                        ITC Inward
                                    </div>
                                </td>

                                <!-- Grand Total -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($bill->grand_total, 2) }}
                                    </div>
                                    <div style="font-size: 0.7rem; color: #64748B;">
                                        Total Billed
                                    </div>
                                </td>

                                <!-- Paid / Balance -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-size: 0.85rem; font-weight: 700; color: #059669;">
                                        ₹{{ number_format($bill->paid_amount, 2) }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.74rem; font-weight: 700; color: {{ $balancePending > 0 ? '#DC2626' : '#64748B' }};">
                                        Bal: ₹{{ number_format($balancePending, 2) }}
                                    </div>
                                </td>

                                <!-- Payment Status -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    @if($bill->payment_status === 'paid')
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> Paid
                                        </span>
                                    @elseif($bill->payment_status === 'partial')
                                        <span class="badge" style="background: rgba(217, 119, 6, 0.12); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.25); font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Partial
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.25); font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Unpaid
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <a href="{{ route('admin.transactions.purchase-entry.show', $bill->id) }}" class="erp-table-action-icon" title="View Detailed Invoice Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Purchase Records Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0; max-width: 440px; margin-left: auto; margin-right: auto;">
                                        No purchase invoices match your filter criteria or selected date interval.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- Table Sticky Totals Footer -->
                    @if($billsData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #CBD5E1; font-weight: 700;">
                            <tr>
                                <td colspan="4" style="padding: 0.95rem 1.15rem; text-align: right; font-size: 0.85rem; color: #0F172A; text-transform: uppercase;">
                                    Filtered Totals ({{ $stats['total_invoices'] }} Bills):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; font-family: monospace; font-size: 0.95rem; color: #334155;">
                                    ₹{{ number_format($stats['total_taxable'], 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; font-family: monospace; font-size: 0.95rem; color: #059669;">
                                    ₹{{ number_format($stats['total_tax'], 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; font-family: monospace; font-size: 1.05rem; color: #0F172A; font-weight: 800;">
                                    ₹{{ number_format($stats['total_grand'], 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; font-family: monospace; font-size: 0.9rem; color: #DC2626;">
                                    Bal: ₹{{ number_format($stats['total_pending'], 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($billsData->hasPages())
                <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="font-size: 0.82rem; color: #64748B;">
                        Showing entries {{ $billsData->firstItem() }} to {{ $billsData->lastItem() }} of {{ $billsData->total() }}
                    </div>
                    <div>
                        {{ $billsData->links() }}
                    </div>
                </div>
            @endif

        <!-- ========================================================================= -->
        <!-- MODE 2: ITEM-WISE PROCUREMENT SUMMARY                                     -->
        <!-- ========================================================================= -->
        @elseif($viewMode === 'items')
            <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 30%;">Herbal Product</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 14%;">Category</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 15%;">Total Inward Qty</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 14%;">Avg Purchase Rate</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 15%;">Total Procurement Value</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 12%;">Top Supplier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsData as $itemRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                
                                <!-- Herbal Product -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            {{ substr($itemRow->item_name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem;">
                                                <a href="{{ route('admin.masters.item.show', $itemRow->item_id) }}" style="text-decoration: none; color: inherit;">
                                                    {{ $itemRow->item_name }}
                                                </a>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 1px;">
                                                <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 1px 6px; border: 1px solid #E2E8F0;">
                                                    {{ $itemRow->item_code }}
                                                </span>
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    {{ $itemRow->bills_count }} Bills
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.74rem; font-weight: 600; padding: 3px 8px; border-radius: 9999px;">
                                        {{ $itemRow->category }}
                                    </span>
                                </td>

                                <!-- Inward Quantity -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: #059669;">
                                        {{ number_format($itemRow->total_qty, 2) }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #64748B; font-weight: 600;">
                                        {{ $itemRow->unit }}
                                    </div>
                                </td>

                                <!-- Avg Rate -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.9rem; color: #334155;">
                                        ₹{{ number_format($itemRow->avg_rate, 2) }}
                                    </div>
                                    <div style="font-size: 0.7rem; color: #94A3B8;">
                                        per {{ $itemRow->unit }}
                                    </div>
                                </td>

                                <!-- Total Investment -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($itemRow->total_amount, 2) }}
                                    </div>
                                    <div style="font-size: 0.7rem; color: #64748B;">
                                        Total Spend
                                    </div>
                                </td>

                                <!-- Top Supplier -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-size: 0.8rem; font-weight: 600; color: #334155;" title="{{ $itemRow->top_supplier }}">
                                        <i class="fa-solid fa-truck-field me-1 text-muted"></i>
                                        {{ $itemRow->top_supplier }}
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-boxes-packing"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Purchased Items Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No item movements recorded in the selected period.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- ========================================================================= -->
        <!-- MODE 3: VENDOR-WISE PURCHASE SUMMARY                                      -->
        <!-- ========================================================================= -->
        @elseif($viewMode === 'vendors')
            <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 28%;">Vendor / Supplier Firm</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 14%;">City &amp; State</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 15%;">GSTIN &amp; Phone</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center; width: 10%;">Invoices</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 12%;">Inward Qty</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 15%;">Total Spend (₹)</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 13%;">Pending Bal (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendorsData as $vRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                
                                <!-- Vendor Firm -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            {{ substr($vRow->vendor_name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem;">
                                                @if($vRow->vendor_id)
                                                    <a href="{{ route('admin.masters.vendor.show', $vRow->vendor_id) }}" style="text-decoration: none; color: inherit;">
                                                        {{ $vRow->vendor_name }}
                                                    </a>
                                                @else
                                                    {{ $vRow->vendor_name }}
                                                @endif
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 1px;">
                                                <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 1px 6px; border: 1px solid #E2E8F0;">
                                                    {{ $vRow->vendor_code }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- City & State -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #334155; font-size: 0.85rem;">
                                        {{ $vRow->city }}
                                    </div>
                                </td>

                                <!-- GSTIN & Phone -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div class="font-monospace" style="font-size: 0.8rem; color: #334155;">
                                        {{ $vRow->gstin }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #64748B;">
                                        {{ $vRow->phone }}
                                    </div>
                                </td>

                                <!-- Invoices -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.82rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                        {{ $vRow->bills_count }}
                                    </span>
                                </td>

                                <!-- Quantity -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #059669;">
                                        {{ number_format($vRow->total_qty, 1) }}
                                    </div>
                                </td>

                                <!-- Total Spend -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($vRow->total_spend, 2) }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #059669;">
                                        Paid: ₹{{ number_format($vRow->total_paid, 2) }}
                                    </div>
                                </td>

                                <!-- Pending Balance -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.95rem; color: {{ $vRow->pending_balance > 0 ? '#DC2626' : '#059669' }};">
                                        ₹{{ number_format($vRow->pending_balance, 2) }}
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-truck-field"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Supplier Records Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No procurement registered for vendors under active filters.</p>
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
    #view-rpt-purchase, #view-rpt-purchase * {
        visibility: visible;
    }
    #view-rpt-purchase {
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
