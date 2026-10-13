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
            @if($purchase->invoice_attachment)
                <a href="{{ asset('storage/' . $purchase->invoice_attachment) }}" target="_blank" class="btn btn-outline" title="View Supplier Invoice">
                    <i class="fa-solid fa-paperclip text-primary"></i> View Attached Invoice
                </a>
            @endif
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

                        <!-- GST Mode Pill -->
                        @if($purchase->is_gst_inclusive)
                            <span class="badge" style="background: #ECFDF5; color: #059669; font-size: 0.74rem; padding: 4px 9px; border-radius: 9999px; font-weight: 600; border: 1px solid #A7F3D0;" title="GST Inclusive Pricing Mode">
                                <i class="fa-solid fa-percent me-1"></i> GST-Inclusive
                            </span>
                        @endif

                        <!-- Transport Name Pill -->
                        @if($purchase->transporter || $purchase->transport_name)
                            <span class="erp-profile-role-pill" title="Transport Carrier">
                                <i class="fa-solid fa-truck-fast me-1"></i>{{ $purchase->transporter->name ?? $purchase->transport_name }}
                            </span>
                        @endif

                        <!-- Vehicle Pill -->
                        @if($purchase->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Transport Vehicle No">
                                <i class="fa-solid fa-truck me-1"></i>{{ $purchase->vehicle_no }}
                            </span>
                        @endif

                        <!-- LR / Bilty Pill -->
                        @if($purchase->lr_no)
                            <span class="erp-profile-username-pill font-monospace" title="LR / Bilty Number">
                                <i class="fa-solid fa-file-lines me-1"></i>LR #{{ $purchase->lr_no }}
                            </span>
                        @endif

                        <!-- E-Way Bill Pill -->
                        @if($purchase->e_way_bill_no)
                            <span class="erp-profile-username-pill font-monospace" title="E-Way Bill Number" style="background: #EFF6FF; color: #1D4ED8; border-color: #BFDBFE;">
                                <i class="fa-solid fa-road me-1"></i>EWB #{{ $purchase->e_way_bill_no }}
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
                <div class="erp-kpi-val font-monospace">{{ number_format($purchase->items->sum('quantity'), 3) }} KG</div>
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
                    @if($purchase->is_gst_inclusive)
                        <span class="badge" style="background: #ECFDF5; color: #059669; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; border: 1px solid #A7F3D0;">
                            <i class="fa-solid fa-check me-1"></i> Bill Rates Inclusive of GST
                        </span>
                    @endif
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
                                    <th style="text-align: right;">BILL TOTAL (₹)</th>
                                    <th style="text-align: right;">U-B TOTAL (₹)</th>
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
                                        <td style="text-align: right;" class="font-monospace font-weight-600 text-dark">
                                            ₹{{ number_format($line->bill_rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace" style="color: #D97706;">
                                            ₹{{ number_format($line->ub_rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace font-weight-600" style="color: #2563EB;">
                                            ₹{{ number_format($line->quantity * $line->bill_rate, 2) }}
                                        </td>
                                        <td style="text-align: right;" class="font-monospace" style="color: #D97706;">
                                            ₹{{ number_format($line->under_amount, 2) }}
                                        </td>
                                        <td style="text-align: right; padding-right: 1.25rem;" class="font-monospace font-weight-700" style="color: #059669;">
                                            ₹{{ number_format(($line->quantity * $line->bill_rate) + $line->under_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Consignment Notes Card -->
            @if($purchase->notes)
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-note-sticky"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">Consignment Notes &amp; Remarks</h3>
                                <p class="erp-form-section-desc">Warehouse observation and inward quality instructions</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-form-section-body" style="padding: 1.25rem 1.5rem;">
                        <p style="margin: 0; color: #334155; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;">{{ $purchase->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Sidebar Column -->
        <div class="erp-form-sidebar-col">

            <!-- Attached Invoice Document Card -->
            @if($purchase->invoice_attachment)
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <div style="font-weight: 700; color: #1E293B; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-paperclip" style="color: #5B841E;"></i> Attached Supplier Invoice
                        </div>
                        <span class="badge" style="background: #F1F5F9; color: #475569; font-size: 0.7rem;">Verified</span>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div style="min-width: 0;">
                                <div class="font-weight-600 text-dark text-truncate" style="font-size: 0.82rem;">{{ basename($purchase->invoice_attachment) }}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.7rem;">Vendor Bill Document</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.4rem; flex-shrink: 0;">
                            <a href="{{ asset('storage/' . $purchase->invoice_attachment) }}" target="_blank" class="btn btn-sm btn-primary" style="padding: 4px 10px; font-size: 0.76rem;" title="View in new tab">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            <a href="{{ asset('storage/' . $purchase->invoice_attachment) }}" download class="btn btn-sm btn-outline" style="padding: 4px 10px; font-size: 0.76rem;" title="Download file">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

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

            <!-- Transport & Logistics Coordinates -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-truck-moving" style="color: #5B841E;"></i> Transport &amp; Logistics
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Transporter / Carrier:</span>
                        <strong class="text-dark">{{ $purchase->transport_name ?: ($purchase->transporter->name ?? 'Self / Direct Inward') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Vehicle / Truck No:</span>
                        <span class="font-monospace text-dark font-weight-600">{{ $purchase->vehicle_no ?: '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">LR / Bilty No:</span>
                        <span class="font-monospace text-dark">{{ $purchase->lr_no ?: '—' }}</span>
                    </div>
                    @if($purchase->e_way_bill_no)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">E-Way Bill No:</span>
                            <span class="font-monospace text-primary font-weight-600">{{ $purchase->e_way_bill_no }}</span>
                        </div>
                    @endif
                    @if($purchase->transport_date)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Bilty / Transport Date:</span>
                            <span>{{ \Carbon\Carbon::parse($purchase->transport_date)->format('d M Y') }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                        <span class="text-muted">Inward Freight Charges:</span>
                        <strong class="font-monospace" style="color: #2563EB;">₹{{ number_format($purchase->freight_amount ?? 0, 2) }}</strong>
                    </div>
                    @if($purchase->transport_notes)
                        <div style="border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                            <span class="text-muted" style="display: block; font-size: 0.75rem; margin-bottom: 2px;">Logistics Remarks:</span>
                            <span style="color: #334155; font-size: 0.82rem;">{{ $purchase->transport_notes }}</span>
                        </div>
                    @endif
                </div>
            </div>

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
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Inward Freight / Cartage:</span>
                        <span class="font-monospace text-primary font-weight-600">₹{{ number_format($purchase->freight_amount ?? 0, 2) }}</span>
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
                    @if($purchase->advance_amount > 0)
                        <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.5rem;">
                            <span class="text-muted">Advance Paid:</span>
                            <span class="font-monospace font-weight-600" style="color: #5B841E;">₹{{ number_format($purchase->advance_amount, 2) }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.65rem; margin-top: 0.2rem;">
                        <span class="text-muted">Total Paid / Settled:</span>
                        <span class="font-monospace font-weight-600 text-dark">₹{{ number_format($purchase->paid_amount, 2) }}</span>
                    </div>
                    @php
                        $pendingCollection = max(0, (float)$purchase->grand_total - (float)$purchase->paid_amount);
                    @endphp
                    <div style="display: flex; justify-content: space-between;">
                        <span class="font-weight-600" style="color: {{ $pendingCollection > 0.01 ? '#DC2626' : '#059669' }};">Pending Payable / Due:</span>
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
