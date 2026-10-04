@extends('admin.layouts.app')

@section('title', 'WB Purchase Slip ' . $wbPurchase->slip_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<section class="view-section active" id="view-wb-purchase-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-purchase-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-purchase-entry') }}">WB Purchase Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $wbPurchase->slip_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-receipt text-primary"></i> WB Purchase Slip Dossier (Without Bill)
            </h1>
            <p class="erp-page-subtitle">
                Comprehensive inward voucher details, raw material line items &amp; supplier settlement without official GST billing.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print WB Slip">
                <i class="fa-solid fa-print"></i> Print Slip
            </button>
            <a href="{{ route('admin.transactions.wb-purchase-entry.edit', $wbPurchase) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Slip
            </a>
            <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to WB Slips
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Executive Slip Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($wbPurchase->vendor->name ?? 'WB', 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $wbPurchase->vendor->name ?? 'Supplier' }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Slip No Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="WB Slip Reference">
                            <i class="fa-solid fa-hashtag me-1"></i>{{ $wbPurchase->slip_no }}
                        </span>

                        <!-- Bill Type Pill -->
                        <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 4px; border: 1px solid #FDE68A;">
                            Without Bill
                        </span>

                        <!-- Order Urgency Pill -->
                        <span class="erp-profile-role-pill" title="Order Priority">
                            <i class="fa-solid fa-tag me-1"></i>{{ $wbPurchase->order_type }}
                        </span>

                        <!-- Entry Date Pill -->
                        <span class="erp-profile-role-pill" title="Arrival Date">
                            <i class="fa-solid fa-calendar-day me-1"></i>{{ $wbPurchase->entry_date->format('d M Y') }}
                        </span>

                        <!-- Vehicle Pill -->
                        @if($wbPurchase->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Truck Registration">
                                <i class="fa-solid fa-truck me-1"></i>{{ $wbPurchase->vehicle_no }}
                            </span>
                        @endif

                        <!-- Supplier Challan Pill -->
                        @if($wbPurchase->invoice_no)
                            <span class="erp-profile-role-pill font-monospace" title="Supplier Slip/Challan No">
                                <i class="fa-solid fa-receipt me-1"></i>{{ $wbPurchase->invoice_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Pill -->
            <div>
                @if($wbPurchase->status === 'completed')
                    <span class="erp-status-btn erp-status-btn-active" style="cursor: default; opacity: 0.95; user-select: none;" title="Slip Completed">
                        <i class="fa-solid fa-lock me-1" style="font-size: 0.68rem;"></i> Completed
                    </span>
                @else
                    <form action="{{ route('admin.transactions.wb-purchase-entry.toggle-status', $wbPurchase) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Mark as Completed">
                            <span class="erp-status-dot-green"></span> Received
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- 4-Stat KPI Summary Ribbon -->
    @php
        $totalWeight = $wbPurchase->items->sum('quantity') ?: $wbPurchase->net_weight;
        $totalAmt = (float)$wbPurchase->total_amount;
        $paidAmt = (float)($wbPurchase->paid_amount ?? 0);
        $pendingAmt = max(0, $totalAmt - $paidAmt);
    @endphp
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Net Weight</div>
                <div class="erp-kpi-val font-monospace">{{ number_format($totalWeight, 3) }} <span style="font-size: 0.85rem; font-weight: normal;">KG</span></div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Procurement Value</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($totalAmt, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Disbursed / Paid Amount</div>
                <div class="erp-kpi-val font-monospace">₹{{ number_format($paidAmt, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid {{ $pendingAmt > 0 ? '#DC2626' : '#059669' }};">
            <div class="erp-kpi-icon-box" style="background: {{ $pendingAmt > 0 ? 'rgba(220, 38, 38, 0.12)' : 'rgba(5, 150, 105, 0.12)' }}; color: {{ $pendingAmt > 0 ? '#DC2626' : '#059669' }};">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Pending Collection</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ $pendingAmt > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($pendingAmt, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div class="erp-form-layout-2col">
        <!-- Left Main Column -->
        <div class="erp-form-main-col">

            <!-- Inward Product Line Items (Without Tax/UB Calculation) -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-success">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">Inward Product Line Items</h3>
                            <p class="erp-form-section-desc">{{ count($wbPurchase->items) }} item(s) received at agreed direct rates without tax calculations</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body p-0" style="padding: 0 !important;">
                    <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                        <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr>
                                    <th style="padding-left: 1.25rem; width: 45px; text-align: center;">S.NO</th>
                                    <th>ITEM DESCRIPTION</th>
                                    <th style="text-align: center;">HSN</th>
                                    <th style="text-align: center;">UNIT</th>
                                    <th style="text-align: right;">NET WT</th>
                                    <th style="text-align: right;">RATE (₹)</th>
                                    <th style="text-align: right;">LINE TOTAL (₹)</th>
                                    <th style="padding-right: 1.25rem;">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wbPurchase->items as $idx => $line)
                                    <tr>
                                        <td style="padding-left: 1.25rem; text-align: center;" class="font-monospace text-muted font-weight-600">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1E293B;">
                                                {{ $line->item->name ?? 'Raw Material' }}
                                            </div>
                                            <div style="font-size: 0.72rem; color: #64748B;">
                                                <span class="font-monospace">{{ $line->item->code ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td style="text-align: center;" class="font-monospace text-muted">
                                            {{ $line->hsn_code ?: ($line->item->hsn_code ?: '—') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600;">
                                                {{ $line->unit }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;" class="font-monospace font-weight-600 text-dark">
                                            {{ number_format($line->quantity, 3) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace">
                                            ₹{{ number_format($line->rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace font-weight-700 text-dark">
                                            ₹{{ number_format($line->amount ?: ($line->quantity * $line->rate), 2) }}
                                        </td>
                                        <td style="padding-right: 1.25rem; font-size: 0.82rem; color: #64748B;">
                                            {{ $line->notes ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                                <tr>
                                    <td colspan="4" style="padding-left: 1.25rem; font-weight: 700; color: #334155; text-transform: uppercase; font-size: 0.75rem;">
                                        Total Inward Procurement
                                    </td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700 text-dark">
                                        {{ number_format($totalWeight, 3) }}
                                    </td>
                                    <td></td>
                                    <td style="text-align: right;" class="font-monospace font-weight-800" style="color: #059669; font-size: 1.1rem;">
                                        ₹{{ number_format($totalAmt, 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Inward Observations -->
            @if($wbPurchase->notes)
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">Consignment Inward Notes</h3>
                                <p class="erp-form-section-desc">Warehouse inspection &amp; inward observations</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-form-section-body">
                        <p style="margin: 0; color: #334155; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;">{{ $wbPurchase->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Sidebar Column -->
        <div class="erp-form-sidebar-col">

            <!-- Supplier Profile -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-user-tag" style="color: #5B841E;"></i> Supplier Coordinates
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Vendor Firm:</span>
                        <strong class="text-dark">{{ $wbPurchase->vendor->name ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Code:</span>
                        <strong class="font-monospace text-dark">{{ $wbPurchase->vendor->code ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">City / Location:</span>
                        <span>{{ $wbPurchase->vendor->city ?? 'Rajasthan' }}</span>
                    </div>
                    @if($wbPurchase->broker)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker / Agent:</span>
                            <span>{{ $wbPurchase->broker->name }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.6rem;">
                        <span class="text-muted">Current Ledger Balance:</span>
                        <strong class="font-monospace" style="color: #B91C1C;">₹{{ number_format($wbPurchase->vendor->current_balance ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Settlement Details -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-wallet" style="color: #059669;"></i> Settlement &amp; Terms
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Payment Terms:</span>
                        <strong class="text-dark">{{ $wbPurchase->payment_terms ?: '30 Days' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Settlement Status:</span>
                        <span class="badge" style="background: {{ $wbPurchase->payment_status === 'paid' ? '#DCFCE7' : ($wbPurchase->payment_status === 'partial' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $wbPurchase->payment_status === 'paid' ? '#15803D' : ($wbPurchase->payment_status === 'partial' ? '#B45309' : '#B91C1C') }}; font-weight: 700;">
                            {{ ucfirst($wbPurchase->payment_status ?? 'unpaid') }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.6rem;">
                        <span class="text-muted">Total Grand Value:</span>
                        <strong class="font-monospace" style="color: #059669; font-size: 1.15rem;">₹{{ number_format($totalAmt, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Disbursed / Paid:</span>
                        <strong class="font-monospace text-dark">₹{{ number_format($paidAmt, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Pending Balance:</span>
                        <strong class="font-monospace" style="color: {{ $pendingAmt > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($pendingAmt, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Audit Coordinates -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                <div style="font-weight: 700; color: #334155; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #3B82F6;"></i> Audit Coordinates
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>WB Slip ID:</span>
                    <strong class="font-monospace text-dark">#{{ $wbPurchase->id }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Created Date:</span>
                    <strong class="text-dark">{{ $wbPurchase->created_at->format('d M Y, h:i A') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Last Updated:</span>
                    <strong class="text-dark">{{ $wbPurchase->updated_at->format('d M Y, h:i A') }}</strong>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
