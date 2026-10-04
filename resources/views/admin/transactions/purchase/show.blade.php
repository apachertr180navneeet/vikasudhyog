@extends('admin.layouts.app')

@section('title', 'Purchase Voucher ' . $purchase->purchase_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<section class="view-section active" id="view-purchase-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.purchase-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.purchase-entry') }}">Purchase Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $purchase->purchase_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-invoice text-primary"></i> Purchase Voucher Dossier
            </h1>
            <p class="erp-page-subtitle">
                Complete inward consignment profile, dual-rate billing breakdown, supplier ledger impact &amp; physical stock receipt.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Voucher">
                <i class="fa-solid fa-print"></i> Print Voucher
            </button>
            <a href="{{ route('admin.transactions.purchase-entry.edit', $purchase) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Voucher
            </a>
            <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchases
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

    <!-- Executive Voucher Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($purchase->vendor->name ?? 'PE', 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $purchase->vendor->name ?? 'Direct Purchase' }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Voucher Code Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Purchase Voucher Reference">
                            <i class="fa-solid fa-hashtag me-1"></i>{{ $purchase->purchase_no }}
                        </span>

                        <!-- Order Urgency Pill -->
                        <span class="erp-profile-role-pill" title="Order Urgency">
                            <i class="fa-solid fa-tag me-1"></i>{{ $purchase->order_type }}
                        </span>

                        <!-- Invoice Date Pill -->
                        <span class="erp-profile-role-pill" title="Invoice Date">
                            <i class="fa-solid fa-calendar-day me-1"></i>{{ $purchase->invoice_date->format('d M Y') }}
                        </span>

                        <!-- Supplier Invoice Ref Pill -->
                        @if($purchase->invoice_no)
                            <span class="erp-profile-username-pill font-monospace" title="Supplier Original Invoice No">
                                <i class="fa-solid fa-receipt me-1"></i>Inv #{{ $purchase->invoice_no }}
                            </span>
                        @endif

                        <!-- Vehicle Pill -->
                        @if($purchase->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Transport Vehicle No">
                                <i class="fa-solid fa-truck me-1"></i>{{ $purchase->vehicle_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Pill -->
            <div>
                @if($purchase->status === 'completed')
                    <span class="erp-status-btn erp-status-btn-active" style="cursor: default; opacity: 0.95; user-select: none;" title="Voucher Completed (Locked - Status cannot be changed)">
                        <i class="fa-solid fa-lock me-1" style="font-size: 0.68rem;"></i> Completed
                    </span>
                @else
                    <form action="{{ route('admin.transactions.purchase-entry.toggle-status', $purchase) }}" method="POST" style="display: inline-block;">
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
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Grand Total Procured</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($purchase->grand_total, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Official Invoiced Bill</div>
                <div class="erp-kpi-val font-monospace" style="color: #2563EB;">₹{{ number_format($purchase->bill_total, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #D97706;">
            <div class="erp-kpi-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Under-Billing (U-B) Total</div>
                <div class="erp-kpi-val font-monospace" style="color: #D97706;">₹{{ number_format($purchase->under_billing_total, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Inward Weight</div>
                <div class="erp-kpi-val font-monospace">{{ number_format($purchase->items->sum('quantity'), 3) }}</div>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div class="erp-form-layout-2col">
        <!-- Left Main Column: Line Items Table & Notes -->
        <div class="erp-form-main-col">

            <!-- Inward Product Line Items (Matching Ledger Table) -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-primary">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">Inward Product Line Items</h3>
                            <p class="erp-form-section-desc">{{ count($purchase->items) }} verified item consignment(s) received into inventory</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body p-0" style="padding: 0 !important;">
                    <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                        <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                            <thead>
                                <tr>
                                    <th style="padding-left: 1.25rem; width: 45px; text-align: center;">S.NO</th>
                                    <th>ITEM</th>
                                    <th>HSN</th>
                                    <th style="text-align: center;">GST</th>
                                    <th style="text-align: center;">UNIT TYPE</th>
                                    <th style="text-align: right;">NET WT</th>
                                    <th style="text-align: right;">BILL RATE (₹)</th>
                                    <th style="text-align: right;">U-B RATE (₹)</th>
                                    <th style="text-align: right;">BILL ARNT (₹)</th>
                                    <th style="text-align: right;">U-B ARNT (₹)</th>
                                    <th style="text-align: right; padding-right: 1.25rem;">TOTAL VALUE (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $idx => $line)
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
                                                @if($line->batch_no)
                                                    &bull; Batch: <span class="font-monospace">{{ $line->batch_no }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="font-monospace" style="font-size: 0.8rem; color: #64748B;">
                                            {{ $line->hsn_code ?: '—' }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: #F1F5F9; color: #475569; font-size: 0.75rem; border: 1px solid #E2E8F0;">
                                                {{ number_format($line->gst_percent, 1) }}%
                                            </span>
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
                                            ₹{{ number_format($line->bill_rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace" style="color: #D97706;">
                                            ₹{{ number_format($line->ub_rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace font-weight-600 text-dark">
                                            ₹{{ number_format($line->quantity * $line->bill_rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace font-weight-600" style="color: #D97706;">
                                            ₹{{ number_format($line->under_amount, 2) }}
                                        </td>
                                        <td style="text-align: right; padding-right: 1.25rem;" class="font-monospace font-weight-700 text-dark">
                                            ₹{{ number_format($line->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                                <tr>
                                    <td colspan="5" style="padding-left: 1.25rem; font-weight: 700; color: #334155; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em;">
                                        Total Consignment Inward Summary
                                    </td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700 text-dark">
                                        {{ number_format($purchase->items->sum('quantity'), 3) }}
                                    </td>
                                    <td colspan="2"></td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700 text-dark">
                                        ₹{{ number_format($purchase->subtotal, 2) }}
                                    </td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700" style="color: #D97706;">
                                        ₹{{ number_format($purchase->under_billing_total, 2) }}
                                    </td>
                                    <td style="text-align: right; padding-right: 1.25rem;" class="font-monospace font-weight-800" style="color: #059669; font-size: 1.05rem;">
                                        ₹{{ number_format($purchase->grand_total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Consignment Remarks / Gate Observations -->
            @if($purchase->notes)
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-purple">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">Consignment Inward Notes</h3>
                                <p class="erp-form-section-desc">Warehouse inspection &amp; procurement observations</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-form-section-body">
                        <p style="margin: 0; color: #334155; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;">{{ $purchase->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Sidebar Column -->
        <div class="erp-form-sidebar-col">

            <!-- Supplier Commercial Coordinates Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-truck-field" style="color: #5B841E;"></i> Supplier Firm Profile
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Vendor Name:</span>
                        <strong class="text-dark">{{ $purchase->vendor->name ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Vendor Code:</span>
                        <strong class="font-monospace text-dark">{{ $purchase->vendor->code ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">City / Location:</span>
                        <span>{{ $purchase->vendor->city ?? 'Rajasthan' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">GSTIN:</span>
                        <span class="font-monospace text-dark">{{ $purchase->vendor->gstin ?: '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.6rem;">
                        <span class="text-muted">Current Ledger Balance:</span>
                        <strong class="font-monospace" style="color: #B91C1C;">₹{{ number_format($purchase->vendor->current_balance ?? 0, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Broker Commission Coordinates -->
            @if($purchase->broker)
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-handshake" style="color: #3B82F6;"></i> Commission Broker
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker Name:</span>
                            <strong class="text-dark">{{ $purchase->broker->name }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker Code:</span>
                            <strong class="font-monospace text-dark">{{ $purchase->broker->code }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Commission Rate:</span>
                            <strong class="font-monospace text-primary">{{ $purchase->broker->commission_rate }}%</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Est. Commission:</span>
                            <strong class="font-monospace text-dark">₹{{ number_format(($purchase->subtotal * $purchase->broker->commission_rate) / 100, 2) }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Settlement & Financial Breakdown -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-receipt" style="color: #10B981;"></i> Settlement Breakdown
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Bill Subtotal:</span>
                        <span class="font-monospace font-weight-600 text-dark">₹{{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">GST Tax Amount:</span>
                        <span class="font-monospace text-dark">₹{{ number_format($purchase->tax_amount, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                        <span class="text-muted font-weight-600">Official Bill Total:</span>
                        <strong class="font-monospace" style="color: #2563EB;">₹{{ number_format($purchase->bill_total, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted font-weight-600" style="color: #D97706;">Under-Billing Spread:</span>
                        <strong class="font-monospace" style="color: #D97706;">₹{{ number_format($purchase->under_billing_total, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 2px solid #E2E8F0; padding-top: 0.65rem; margin-top: 0.2rem;">
                        <strong class="text-dark">Grand Total Inward:</strong>
                        <strong class="font-monospace" style="color: #059669; font-size: 1.15rem;">₹{{ number_format($purchase->grand_total, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.65rem; margin-top: 0.2rem;">
                        <span class="text-muted">Paid / Settled:</span>
                        <span class="font-monospace font-weight-600 text-dark">₹{{ number_format($purchase->paid_amount, 2) }}</span>
                    </div>
                    @php
                        $pendingCollection = max(0, (float)$purchase->grand_total - (float)$purchase->paid_amount);
                    @endphp
                    <div style="display: flex; justify-content: space-between;">
                        <span class="font-weight-600" style="color: {{ $pendingCollection > 0.01 ? '#DC2626' : '#059669' }};">Pending Collection / Due:</span>
                        <strong class="font-monospace" style="color: {{ $pendingCollection > 0.01 ? '#DC2626' : '#059669' }}; font-size: 1.05rem;">
                            ₹{{ number_format($pendingCollection, 2) }}
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Payment Status:</span>
                        <span class="badge" style="background: {{ $purchase->payment_status === 'paid' ? '#DCFCE7' : ($purchase->payment_status === 'partial' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $purchase->payment_status === 'paid' ? '#15803D' : ($purchase->payment_status === 'partial' ? '#B45309' : '#B91C1C') }}; font-weight: 700;">
                            {{ ucfirst($purchase->payment_status ?? 'unpaid') }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Payment Terms:</span>
                        <span>{{ $purchase->payment_terms ?: 'Cash' }}</span>
                    </div>
                </div>
            </div>

            <!-- Ledger Audit Trail Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                <div style="font-weight: 700; color: #334155; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #3B82F6;"></i> Audit Coordinates
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Voucher ID:</span>
                    <strong class="font-monospace text-dark">#{{ $purchase->id }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Created Date:</span>
                    <strong class="text-dark">{{ $purchase->created_at->format('d M Y, h:i A') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Last Updated:</span>
                    <strong class="text-dark">{{ $purchase->updated_at->format('d M Y, h:i A') }}</strong>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
