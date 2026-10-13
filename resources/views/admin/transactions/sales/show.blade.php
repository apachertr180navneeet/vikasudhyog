@extends('admin.layouts.app')

@section('title', 'Sales Invoice ' . $sale->sale_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-sales-order')

@push('styles')
<style>
    .erp-status-dropdown {
        position: relative;
        display: inline-block;
    }
    .erp-status-dropdown .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 6px;
        background: #FFFFFF;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        z-index: 1050;
        list-style: none;
        margin-bottom: 0;
        padding: 0.5rem;
        min-width: 190px;
    }
    .erp-status-dropdown .dropdown-menu.show {
        display: block !important;
    }
    .erp-status-dropdown .dropdown-item {
        width: 100%;
        border: none;
        background: transparent;
        text-align: left;
        cursor: pointer;
        padding: 0.45rem 0.75rem;
        font-size: 0.82rem;
        border-radius: 6px;
        transition: background-color 0.15s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .erp-status-dropdown .dropdown-item:hover {
        background-color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<section class="view-section active" id="view-sales-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.sales-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.sales-entry') }}">Sales Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $sale->sale_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-invoice text-primary"></i> Sales Invoice Dossier
            </h1>
            <p class="erp-page-subtitle">
                Complete outward sales consignment profile, dual-rate billing breakdown, customer ledger impact &amp; logistics dispatch.
            </p>
        </div>

        <div class="erp-header-actions">
            @if($sale->invoice_attachment)
                <a href="{{ asset('storage/' . $sale->invoice_attachment) }}" target="_blank" class="btn btn-outline" title="View Attached Document">
                    <i class="fa-solid fa-paperclip text-primary"></i> View Attachment
                </a>
            @endif
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Sales Invoice">
                <i class="fa-solid fa-print"></i> Print Invoice
            </button>
            <a href="{{ route('admin.transactions.sales-entry.edit', $sale) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Invoice
            </a>
            <a href="{{ route('admin.transactions.sales-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Sales
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

    @if(session('error'))
        <div class="alert erp-alert-danger" style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; padding: 0.85rem 1.25rem; border-radius: 12px; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div class="erp-alert-content" style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-exclamation" style="color: #DC2626;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Executive Voucher Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($sale->customer->name ?? 'SE', 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $sale->customer->name ?? 'Direct Customer Consignment' }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Invoice Code Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Sales Invoice Reference">
                            <i class="fa-solid fa-hashtag me-1"></i>{{ $sale->sale_no }}
                        </span>

                        <!-- Order Urgency Pill -->
                        <span class="erp-profile-role-pill" title="Order Type">
                            <i class="fa-solid fa-tag me-1"></i>{{ $sale->order_type }}
                        </span>

                        <!-- Invoice Date Pill -->
                        <span class="erp-profile-role-pill" title="Dispatch Date">
                            <i class="fa-solid fa-calendar-day me-1"></i>{{ $sale->sale_date->format('d M Y') }}
                        </span>

                        <!-- Buyer PO Ref Pill -->
                        @if($sale->invoice_no)
                            <span class="erp-profile-username-pill font-monospace" title="Buyer PO / Ref No">
                                <i class="fa-solid fa-receipt me-1"></i>Ref #{{ $sale->invoice_no }}
                            </span>
                        @endif

                        <!-- GST Mode Pill -->
                        @if($sale->is_gst_inclusive)
                            <span class="badge" style="background: rgba(16, 185, 129, 0.25); color: #ECFDF5; font-size: 0.78rem; padding: 4px 10px; border-radius: 6px; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.4); backdrop-filter: blur(6px); display: inline-flex; align-items: center;" title="GST Inclusive Pricing Mode">
                                <i class="fa-solid fa-percent me-1"></i> GST-Inclusive
                            </span>
                        @endif

                        <!-- Transport Carrier Pill -->
                        @if($sale->transporter || $sale->transport_name)
                            <span class="erp-profile-role-pill" title="Transport Carrier">
                                <i class="fa-solid fa-truck-fast me-1"></i>{{ $sale->transporter->name ?? $sale->transport_name }}
                            </span>
                        @endif

                        <!-- Vehicle Pill -->
                        @if($sale->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Transport Vehicle No">
                                <i class="fa-solid fa-truck me-1"></i>{{ $sale->vehicle_no }}
                            </span>
                        @endif

                        <!-- LR / Bilty Pill -->
                        @if($sale->lr_no)
                            <span class="erp-profile-role-pill font-monospace" title="LR / Bilty No">
                                <i class="fa-solid fa-file-lines me-1"></i>LR #{{ $sale->lr_no }}
                            </span>
                        @endif

                        <!-- E-Way Bill Pill -->
                        @if($sale->e_way_bill_no)
                            <span class="erp-profile-username-pill font-monospace" title="E-Way Bill Number" style="background: rgba(59, 130, 246, 0.25); color: #DBEAFE; border: 1px solid rgba(59, 130, 246, 0.4); backdrop-filter: blur(6px); display: inline-flex; align-items: center;">
                                <i class="fa-solid fa-road me-1"></i>EWB #{{ $sale->e_way_bill_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Dropdown & Customer Actions -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                @php
                    $curStatus = strtolower($sale->status ?? 'dispatched');
                    $stBadgeColor = match($curStatus) {
                        'ordered' => ['bg' => 'rgba(59, 130, 246, 0.25)', 'text' => '#DBEAFE', 'dot' => '#60A5FA', 'icon' => 'fa-clock'],
                        'dispatched' => ['bg' => 'rgba(16, 185, 129, 0.25)', 'text' => '#ECFDF5', 'dot' => '#34D399', 'icon' => 'fa-truck-fast'],
                        'delivered' => ['bg' => 'rgba(139, 92, 246, 0.25)', 'text' => '#EDE9FE', 'dot' => '#A78BFA', 'icon' => 'fa-truck-ramp-box'],
                        'completed' => ['bg' => 'rgba(5, 150, 105, 0.35)', 'text' => '#D1FAE5', 'dot' => '#10B981', 'icon' => 'fa-circle-check'],
                        'cancelled' => ['bg' => 'rgba(239, 68, 68, 0.25)', 'text' => '#FEE2E2', 'dot' => '#F87171', 'icon' => 'fa-ban'],
                        default => ['bg' => 'rgba(255, 255, 255, 0.2)', 'text' => '#FFFFFF', 'dot' => '#34D399', 'icon' => 'fa-circle-dot'],
                    };
                @endphp

                <!-- Status Dropdown Trigger -->
                <div class="erp-status-dropdown">
                    <button type="button" class="btn dropdown-toggle" style="background: {{ $stBadgeColor['bg'] }}; color: {{ $stBadgeColor['text'] }}; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 20px; padding: 6px 14px; font-weight: 700; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 7px; backdrop-filter: blur(6px); cursor: pointer;" title="Change Consignment Status">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $stBadgeColor['dot'] }}; display: inline-block;"></span>
                        <span><i class="fa-solid {{ $stBadgeColor['icon'] }} me-1"></i>{{ ucfirst($curStatus) }}</span>
                        <i class="fa-solid fa-chevron-down ms-1" style="font-size: 0.65rem; opacity: 0.8;"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li style="padding: 0.35rem 0.65rem; font-size: 0.7rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Update Order Status</li>
                        @php
                            $availableStatuses = [
                                'ordered' => ['label' => 'Ordered / Pending', 'icon' => 'fa-clock', 'color' => '#3B82F6'],
                                'dispatched' => ['label' => 'Dispatched', 'icon' => 'fa-truck-fast', 'color' => '#16A34A'],
                                'delivered' => ['label' => 'Delivered to Buyer', 'icon' => 'fa-truck-ramp-box', 'color' => '#8B5CF6'],
                                'completed' => ['label' => 'Completed & Closed', 'icon' => 'fa-circle-check', 'color' => '#059669'],
                                'cancelled' => ['label' => 'Cancelled / Void', 'icon' => 'fa-ban', 'color' => '#DC2626'],
                            ];
                        @endphp
                        @foreach($availableStatuses as $stKey => $stInfo)
                            @if($stKey !== $curStatus)
                                <li>
                                    <form action="{{ route('admin.transactions.sales-entry.update-status', $sale) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $stKey }}">
                                        <button type="submit" class="dropdown-item">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $stInfo['color'] }};"></span>
                                            <span style="font-weight: 600; color: #1E293B;"><i class="fa-solid {{ $stInfo['icon'] }} me-1" style="color: {{ $stInfo['color'] }};"></i>{{ $stInfo['label'] }}</span>
                                        </button>
                                    </form>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <!-- Customer Quick Connect Buttons (WhatsApp & Call) -->
                @if($sale->customer && $sale->customer->phone)
                    @php
                        $rawPhone = preg_replace('/[^0-9]/', '', $sale->customer->phone);
                        $waPhone = strlen($rawPhone) === 10 ? '91' . $rawPhone : $rawPhone;
                        $waMsg = urlencode("Hello {$sale->customer->name}, Regarding your Sales Invoice #{$sale->sale_no} for amount Rs. " . number_format($sale->grand_total, 2));
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <a href="tel:{{ $sale->customer->phone }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Call Customer Contact">
                            <i class="fa-solid fa-phone me-1"></i> Call
                        </a>
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" class="btn" style="background: #25D366; color: #FFFFFF; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 700; box-shadow: 0 4px 10px rgba(37,211,102,0.3);" title="Message on WhatsApp">
                            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                        </a>
                    </div>
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
                <div class="erp-kpi-label">Grand Total Outward</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($sale->grand_total, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Official Invoiced Bill</div>
                <div class="erp-kpi-val font-monospace" style="color: #2563EB;">₹{{ number_format($sale->bill_total, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #D97706;">
            <div class="erp-kpi-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Under-Billing (U-B) Total</div>
                <div class="erp-kpi-val font-monospace" style="color: #D97706;">₹{{ number_format($sale->under_billing_total, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Outward Quantity</div>
                <div class="erp-kpi-val font-monospace">{{ number_format($sale->items->sum('quantity'), 3) }}</div>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div class="erp-form-layout-2col">
        <!-- Left Main Column: Line Items Table & Notes -->
        <div class="erp-form-main-col">

            <!-- Outward Product Line Items Card -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-primary">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">Outward Product Line Items</h3>
                            <p class="erp-form-section-desc">{{ count($sale->items) }} line item(s) dispatched from warehouse</p>
                        </div>
                    </div>
                    @if($sale->is_gst_inclusive)
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
                                    <th>ITEM NAME</th>
                                    <th>HSN</th>
                                    <th style="text-align: center;">UNIT TYPE</th>
                                    <th style="text-align: right;">DISPATCH QTY</th>
                                    <th style="text-align: right;">BILL RATE (₹)</th>
                                    <th style="text-align: center;">GST %</th>
                                    <th style="text-align: right;">BILL TOTAL (₹)</th>
                                    <th style="text-align: right;">U-B RATE (₹)</th>
                                    <th style="text-align: right;">U-B TOTAL (₹)</th>
                                    <th style="text-align: right; padding-right: 1.25rem;">TOTAL VALUE (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $idx => $item)
                                    <tr>
                                        <td style="padding-left: 1.25rem; text-align: center;" class="font-monospace text-muted font-weight-600">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1E293B;">
                                                {{ $item->item->name ?? 'Consignment Item' }}
                                            </div>
                                            <div style="font-size: 0.72rem; color: #64748B;">
                                                <span class="font-monospace">{{ $item->item->code ?? '' }}</span>
                                                @if($item->batch_no)
                                                    &bull; Batch: <span class="font-monospace">{{ $item->batch_no }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="font-monospace" style="font-size: 0.8rem; color: #64748B;">
                                            {{ $item->hsn_code ?: '—' }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600;">
                                                {{ $item->unit }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;" class="font-monospace font-weight-600 text-dark">
                                            {{ number_format($item->quantity, 3) }}
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace text-dark">₹{{ number_format($item->bill_rate, 2) }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 600;">{{ number_format($item->gst_percent, 1) }}%</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace font-weight-600" style="color: #2563EB;">₹{{ number_format($item->bill_amount, 2) }}</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace" style="color: #D97706;">₹{{ number_format($item->ub_rate, 2) }}</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace font-weight-600" style="color: #D97706;">₹{{ number_format($item->under_amount, 2) }}</span>
                                        </td>
                                        <td style="text-align: right; padding-right: 1.25rem;">
                                            <span class="font-monospace font-weight-700" style="color: #059669; font-size: 0.95rem;">₹{{ number_format($item->total_amount, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                                    <td colspan="4" style="padding-left: 1.25rem; font-weight: 700; color: #1E293B;">Consignment Total Summary</td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700 text-dark">{{ number_format($sale->items->sum('quantity'), 3) }}</td>
                                    <td colspan="2"></td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700" style="color: #2563EB;">₹{{ number_format($sale->bill_total, 2) }}</td>
                                    <td></td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700" style="color: #D97706;">₹{{ number_format($sale->under_billing_total, 2) }}</td>
                                    <td style="text-align: right; padding-right: 1.25rem;" class="font-monospace font-weight-700" style="color: #059669; font-size: 1.05rem;">₹{{ number_format($sale->grand_total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dispatch Notes & Instructions Card -->
            @if($sale->notes)
                <div class="card erp-form-section-card" style="margin-top: 1.5rem;">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-note-sticky"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">Dispatch Remarks &amp; Consignment Notes</h3>
                                <p class="erp-form-section-desc">Special buyer instructions, terms &amp; warehouse delivery notes</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-form-section-body" style="padding: 1.25rem 1.5rem;">
                        <p style="margin: 0; color: #334155; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;">{{ $sale->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Sidebar Column: Settlement, Customer, Broker, Transport, Attachment -->
        <div class="erp-form-sidebar-col" style="display: flex; flex-direction: column; gap: 1.25rem;">

            <!-- Attached Invoice Document Card -->
            @if($sale->invoice_attachment)
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <div style="font-weight: 700; color: #1E293B; margin-bottom: 0.75rem; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-paperclip" style="color: #5B841E;"></i> Attached Order Document
                        </div>
                        <span class="badge" style="background: #F1F5F9; color: #475569; font-size: 0.7rem;">Verified</span>
                    </div>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.6rem; min-width: 0;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div style="min-width: 0;">
                                <div class="font-weight-600 text-dark text-truncate" style="font-size: 0.82rem;">{{ basename($sale->invoice_attachment) }}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.7rem;">Attached Bill / PO</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.4rem; flex-shrink: 0;">
                            <a href="{{ asset('storage/' . $sale->invoice_attachment) }}" target="_blank" class="btn btn-sm btn-primary" style="padding: 4px 10px; font-size: 0.76rem;" title="View in new tab">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            <a href="{{ asset('storage/' . $sale->invoice_attachment) }}" download class="btn btn-sm btn-outline" style="padding: 4px 10px; font-size: 0.76rem;" title="Download file">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Settlement & Financial Breakdown Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-receipt" style="color: #10B981;"></i> Settlement Breakdown
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Taxable Subtotal:</span>
                        <span class="font-monospace font-weight-600 text-dark">₹{{ number_format($sale->subtotal, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">GST Tax Amount:</span>
                        <span class="font-monospace text-dark">₹{{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    @if((float)($sale->freight_amount ?? 0) > 0)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Freight / Cartage:</span>
                            <span class="font-monospace text-primary font-weight-600">+₹{{ number_format($sale->freight_amount, 2) }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                        <span class="text-muted font-weight-600">Official Bill Total:</span>
                        <strong class="font-monospace" style="color: #2563EB;">₹{{ number_format($sale->bill_total, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted font-weight-600" style="color: #D97706;">Under-Billing Spread:</span>
                        <strong class="font-monospace" style="color: #D97706;">₹{{ number_format($sale->under_billing_total, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 2px solid #E2E8F0; padding-top: 0.65rem; margin-top: 0.2rem;">
                        <strong class="text-dark">Total Receivable Value:</strong>
                        <strong class="font-monospace" style="color: #059669; font-size: 1.15rem;">₹{{ number_format($sale->grand_total, 2) }}</strong>
                    </div>
                    @if($sale->advance_amount > 0)
                        <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.5rem;">
                            <span class="text-muted">Advance Received:</span>
                            <span class="font-monospace font-weight-600" style="color: #5B841E;">₹{{ number_format($sale->advance_amount, 2) }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.65rem; margin-top: 0.2rem;">
                        <span class="text-muted">Payment Received:</span>
                        <span class="font-monospace font-weight-600 text-dark">₹{{ number_format($sale->paid_amount, 2) }}</span>
                    </div>
                    @php
                        $pendingBal = max(0, (float)$sale->grand_total - (float)$sale->paid_amount);
                    @endphp
                    <div style="display: flex; justify-content: space-between;">
                        <span class="font-weight-600" style="color: {{ $pendingBal > 0.01 ? '#DC2626' : '#059669' }};">Pending Customer Balance:</span>
                        <strong class="font-monospace" style="color: {{ $pendingBal > 0.01 ? '#DC2626' : '#059669' }}; font-size: 1.05rem;">
                            ₹{{ number_format($pendingBal, 2) }}
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Payment Status:</span>
                        <span class="badge" style="background: {{ $sale->payment_status === 'paid' ? '#DCFCE7' : ($sale->payment_status === 'partial' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $sale->payment_status === 'paid' ? '#15803D' : ($sale->payment_status === 'partial' ? '#B45309' : '#B91C1C') }}; font-weight: 700; padding: 4px 8px; border-radius: 6px;">
                            {{ ucfirst($sale->payment_status ?? 'unpaid') }}
                        </span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Agreed Payment Terms:</span>
                        <span class="font-weight-600 text-dark">{{ $sale->payment_terms ?: 'Cash' }}</span>
                    </div>
                </div>
            </div>

            <!-- Customer Commercial Coordinates Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-user-tie" style="color: #5B841E;"></i> Customer Coordinates
                    </div>
                    @if($sale->customer)
                        <a href="{{ route('admin.masters.customer.show', $sale->customer) }}" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.72rem;" title="View Customer Profile">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Profile
                        </a>
                    @endif
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Customer Name:</span>
                        <strong class="text-dark">{{ $sale->customer->name ?? 'Direct Sale' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Customer Code:</span>
                        <strong class="font-monospace text-dark">{{ $sale->customer->code ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">City / Destination:</span>
                        <span>{{ $sale->customer->city ?? '—' }}{{ $sale->customer && $sale->customer->state ? ', ' . $sale->customer->state : '' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Customer GSTIN:</span>
                        <span class="font-monospace text-dark">{{ $sale->customer->gstin ?: 'Unregistered' }}</span>
                    </div>
                    @if($sale->customer && isset($sale->customer->current_balance))
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.6rem;">
                            <span class="text-muted">Current Ledger Balance:</span>
                            <strong class="font-monospace" style="color: {{ $sale->customer->current_balance > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($sale->customer->current_balance, 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Broker Commission Coordinates -->
            @if($sale->broker)
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-handshake" style="color: #3B82F6;"></i> Commission Broker
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker Name:</span>
                            <strong class="text-dark">{{ $sale->broker->name }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker Code:</span>
                            <strong class="font-monospace text-dark">{{ $sale->broker->code }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Commission Rate:</span>
                            <strong class="font-monospace text-primary">{{ $sale->broker->commission_rate }}%</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Est. Commission:</span>
                            <strong class="font-monospace text-dark">₹{{ number_format(($sale->subtotal * $sale->broker->commission_rate) / 100, 2) }}</strong>
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
                        <strong class="text-dark">{{ $sale->transport_name ?: ($sale->transporter->name ?? 'Self / Direct Dispatch') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Vehicle / Truck No:</span>
                        <span class="font-monospace text-dark font-weight-600">{{ $sale->vehicle_no ?: '—' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">LR / Bilty No:</span>
                        <span class="font-monospace text-dark">{{ $sale->lr_no ?: '—' }}</span>
                    </div>
                    @if($sale->e_way_bill_no)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">E-Way Bill No:</span>
                            <span class="font-monospace text-primary font-weight-600">{{ $sale->e_way_bill_no }}</span>
                        </div>
                    @endif
                    @if($sale->transport_date)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Bilty / Transport Date:</span>
                            <span>{{ \Carbon\Carbon::parse($sale->transport_date)->format('d M Y') }}</span>
                        </div>
                    @endif
                    @if($sale->transport_notes)
                        <div style="border-top: 1px solid #F1F5F9; padding-top: 0.6rem; display: flex; flex-direction: column; gap: 0.25rem;">
                            <span class="text-muted" style="font-size: 0.76rem;">Logistics Instructions:</span>
                            <span style="color: #475569; font-size: 0.8rem; background: #F8FAFC; padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $sale->transport_notes }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ledger Audit Trail Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                <div style="font-weight: 700; color: #334155; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #3B82F6;"></i> Audit Coordinates
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Voucher ID:</span>
                    <strong class="font-monospace text-dark">#{{ $sale->id }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Created Date:</span>
                    <strong class="text-dark">{{ $sale->created_at->format('d M Y, h:i A') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Last Updated:</span>
                    <strong class="text-dark">{{ $sale->updated_at->format('d M Y, h:i A') }}</strong>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('click', function(e) {
        const toggle = e.target.closest('.erp-status-dropdown .dropdown-toggle');
        const allDropdowns = document.querySelectorAll('.erp-status-dropdown .dropdown-menu');

        if (toggle) {
            e.preventDefault();
            e.stopPropagation();
            const menu = toggle.nextElementSibling;
            const isOpen = menu && menu.classList.contains('show');
            allDropdowns.forEach(m => m.classList.remove('show'));
            if (!isOpen && menu) {
                menu.classList.add('show');
            }
        } else {
            allDropdowns.forEach(m => m.classList.remove('show'));
        }
    });
</script>
@endpush
