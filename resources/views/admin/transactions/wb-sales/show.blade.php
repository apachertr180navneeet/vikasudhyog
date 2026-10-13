@extends('admin.layouts.app')

@section('title', 'WB Sales Slip ' . $wbSale->slip_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-sales')

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
        min-width: 195px;
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
<section class="view-section active" id="view-wb-sales-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-sales-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-sales-entry') }}">WB Sales Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $wbSale->slip_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-receipt text-primary"></i> WB Sales Slip Dossier (Without Bill Outward)
            </h1>
            <p class="erp-page-subtitle">
                Comprehensive outward slip profile, direct mandi rates, customer settlement &amp; physical inventory dispatch.
            </p>
        </div>

        <div class="erp-header-actions">
            @if($wbSale->invoice_attachment)
                <a href="{{ asset('storage/' . $wbSale->invoice_attachment) }}" target="_blank" class="btn btn-outline" title="View Attached Document">
                    <i class="fa-solid fa-paperclip text-primary"></i> View Attachment
                </a>
            @endif
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print WB Slip">
                <i class="fa-solid fa-print"></i> Print Slip
            </button>
            <a href="{{ route('admin.transactions.wb-sales-entry.edit', $wbSale) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Slip
            </a>
            <a href="{{ route('admin.transactions.wb-sales-entry') }}" class="btn btn-outline">
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

    @if(session('error'))
        <div class="alert erp-alert-danger" style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; padding: 0.85rem 1.25rem; border-radius: 12px; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div class="erp-alert-content" style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-exclamation" style="color: #DC2626;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Executive Slip Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($wbSale->customer->name ?? 'WS', 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $wbSale->customer->name ?? 'Direct Customer Consignment' }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Slip No Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="WB Slip Reference">
                            <i class="fa-solid fa-hashtag me-1"></i>{{ $wbSale->slip_no }}
                        </span>

                        <!-- Bill Type Pill -->
                        <span class="badge" style="background: rgba(245, 158, 11, 0.25); color: #FEF3C7; font-size: 0.76rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(245, 158, 11, 0.4); backdrop-filter: blur(6px);">
                            <i class="fa-solid fa-file-invoice me-1"></i> Without Bill
                        </span>

                        <!-- Order Urgency Pill -->
                        <span class="erp-profile-role-pill" title="Order Type">
                            <i class="fa-solid fa-tag me-1"></i>{{ $wbSale->order_type }}
                        </span>

                        <!-- Entry Date Pill -->
                        <span class="erp-profile-role-pill" title="Dispatch Date">
                            <i class="fa-solid fa-calendar-day me-1"></i>{{ $wbSale->entry_date->format('d M Y') }}
                        </span>

                        <!-- Transport Name Pill -->
                        @if($wbSale->transporter || $wbSale->transport_name)
                            <span class="erp-profile-role-pill" title="Transporter Name">
                                <i class="fa-solid fa-truck-fast me-1"></i>{{ $wbSale->transporter->name ?? $wbSale->transport_name }}
                            </span>
                        @endif

                        <!-- Vehicle Pill -->
                        @if($wbSale->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Truck Registration">
                                <i class="fa-solid fa-truck me-1"></i>{{ $wbSale->vehicle_no }}
                            </span>
                        @endif

                        <!-- LR / Bilty Pill -->
                        @if($wbSale->lr_no)
                            <span class="erp-profile-role-pill font-monospace" title="LR / Bilty No">
                                <i class="fa-solid fa-file-lines me-1"></i>LR #{{ $wbSale->lr_no }}
                            </span>
                        @endif

                        <!-- Customer Challan Pill -->
                        @if($wbSale->invoice_no)
                            <span class="erp-profile-username-pill font-monospace" title="Customer Challan / PO No">
                                <i class="fa-solid fa-receipt me-1"></i>Ref #{{ $wbSale->invoice_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Dropdown & Customer Actions -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                @php
                    $curStatus = strtolower($wbSale->status ?? 'dispatched');
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
                                    <form action="{{ route('admin.transactions.wb-sales-entry.update-status', $wbSale) }}" method="POST">
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
                @if($wbSale->customer && $wbSale->customer->phone)
                    @php
                        $rawPhone = preg_replace('/[^0-9]/', '', $wbSale->customer->phone);
                        $waPhone = strlen($rawPhone) === 10 ? '91' . $rawPhone : $rawPhone;
                        $waMsg = urlencode("Hello {$wbSale->customer->name}, Regarding your WB Sales Slip #{$wbSale->slip_no} for amount Rs. " . number_format($wbSale->total_amount, 2));
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <a href="tel:{{ $wbSale->customer->phone }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Call Customer Contact">
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
    @php
        $totalWeight = $wbSale->net_weight ?: $wbSale->items->sum('quantity');
        $totalAmt = (float)$wbSale->total_amount;
        $paidAmt = (float)($wbSale->paid_amount ?? 0);
        $pendingBal = max(0, $totalAmt - $paidAmt);
    @endphp
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Outward Value</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($totalAmt, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-weight-hanging"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Net Outward Weight</div>
                <div class="erp-kpi-val font-monospace" style="color: #2563EB;">{{ number_format($totalWeight, 3) }} <span style="font-size: 0.85rem; font-weight: normal;">KG</span></div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #D97706;">
            <div class="erp-kpi-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Gross Weighbridge Wt</div>
                <div class="erp-kpi-val font-monospace" style="color: #D97706;">{{ number_format($wbSale->gross_weight, 3) }} <span style="font-size: 0.85rem; font-weight: normal;">KG</span></div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-scale-unbalanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Tare &amp; Deductions</div>
                <div class="erp-kpi-val font-monospace">{{ number_format($wbSale->tare_weight + $wbSale->deduction_weight, 3) }} <span style="font-size: 0.85rem; font-weight: normal;">KG</span></div>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div class="erp-form-layout-2col">
        <!-- Left Main Column: Items Table & Notes -->
        <div class="erp-form-main-col">

            <!-- Outward Dispatched Line Items Card -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-success">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">Outward Dispatched Line Items (Direct Mandi Rate)</h3>
                            <p class="erp-form-section-desc">{{ count($wbSale->items) }} line item(s) dispatched at agreed direct mandi rates</p>
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
                                    <th>HSN</th>
                                    <th style="text-align: right;">NET QTY</th>
                                    <th style="text-align: right;">MANDI RATE (₹)</th>
                                    <th style="text-align: right;">TOTAL AMOUNT (₹)</th>
                                    <th style="padding-right: 1.25rem;">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wbSale->items as $idx => $item)
                                    <tr>
                                        <td style="padding-left: 1.25rem; text-align: center;" class="font-monospace text-muted font-weight-600">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #1E293B;">
                                                {{ $item->item->name ?? 'Raw Material Consignment' }}
                                            </div>
                                            <div style="font-size: 0.72rem; color: #64748B;">
                                                <span class="font-monospace">{{ $item->item->code ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="font-monospace" style="font-size: 0.8rem; color: #64748B;">
                                            {{ $item->hsn_code ?: '—' }}
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace font-weight-700 text-dark">
                                                {{ number_format($item->quantity, 3) }}
                                            </span>
                                            <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.72rem; margin-left: 3px;">{{ $item->unit }}</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace text-dark font-weight-600">₹{{ number_format($item->rate, 2) }}</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace font-weight-700" style="color: #059669; font-size: 0.95rem;">₹{{ number_format($item->amount, 2) }}</span>
                                        </td>
                                        <td style="padding-right: 1.25rem; font-size: 0.82rem; color: #64748B;">
                                            {{ $item->notes ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                                    <td colspan="3" style="padding-left: 1.25rem; font-weight: 700; color: #1E293B;">Consignment Total Summary</td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700 text-dark">{{ number_format($wbSale->items->sum('quantity'), 3) }}</td>
                                    <td></td>
                                    <td style="text-align: right;" class="font-monospace font-weight-700" style="color: #059669; font-size: 1.05rem;">₹{{ number_format($wbSale->total_amount, 2) }}</td>
                                    <td style="padding-right: 1.25rem;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Weighbridge Notes & Observations Card -->
            @if($wbSale->notes)
                <div class="card erp-form-section-card" style="margin-top: 1.5rem;">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-note-sticky"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">Weighbridge Notes &amp; Observations</h3>
                                <p class="erp-form-section-desc">Slip notes, weighing conditions and special observations</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-form-section-body" style="padding: 1.25rem 1.5rem;">
                        <p style="margin: 0; color: #334155; font-size: 0.9rem; line-height: 1.6; white-space: pre-wrap;">{{ $wbSale->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Sidebar Column: Financial Settlement, Customer, Broker, Transport, Weights, Audit -->
        <div class="erp-form-sidebar-col" style="display: flex; flex-direction: column; gap: 1.25rem;">

            <!-- Attached Invoice Document Card -->
            @if($wbSale->invoice_attachment)
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
                                <div class="font-weight-600 text-dark text-truncate" style="font-size: 0.82rem;">{{ basename($wbSale->invoice_attachment) }}</div>
                                <div class="text-muted font-monospace" style="font-size: 0.7rem;">Attached Bill / Challan</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.4rem; flex-shrink: 0;">
                            <a href="{{ asset('storage/' . $wbSale->invoice_attachment) }}" target="_blank" class="btn btn-sm btn-primary" style="padding: 4px 10px; font-size: 0.76rem;" title="View in new tab">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                            <a href="{{ asset('storage/' . $wbSale->invoice_attachment) }}" download class="btn btn-sm btn-outline" style="padding: 4px 10px; font-size: 0.76rem;" title="Download file">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cash Settlement Breakdown Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-receipt" style="color: #10B981;"></i> Cash Settlement Breakdown
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    @if((float)($wbSale->freight_amount ?? 0) > 0)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Freight / Cartage:</span>
                            <span class="font-monospace text-primary font-weight-600">+₹{{ number_format($wbSale->freight_amount, 2) }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                        <strong class="text-dark">Total Outward Value:</strong>
                        <strong class="font-monospace" style="color: #059669; font-size: 1.15rem;">₹{{ number_format($wbSale->total_amount, 2) }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Payment Mode:</span>
                        <strong class="text-dark">{{ $wbSale->payment_mode }}</strong>
                    </div>
                    @if($wbSale->payment_terms)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Agreed Payment Terms:</span>
                            <span class="font-weight-600 text-dark">{{ $wbSale->payment_terms }}</span>
                        </div>
                    @endif
                    @if((float)($wbSale->advance_amount ?? 0) > 0)
                        <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.5rem;">
                            <span class="text-muted">Advance Received:</span>
                            <span class="font-monospace font-weight-600" style="color: #5B841E;">₹{{ number_format($wbSale->advance_amount, 2) }}</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; border-top: 1px dashed #CBD5E1; padding-top: 0.5rem;">
                        <span class="text-muted">Amount Received:</span>
                        <span class="font-monospace font-weight-600 text-dark">₹{{ number_format($wbSale->paid_amount, 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="font-weight-600" style="color: {{ $pendingBal > 0.01 ? '#DC2626' : '#059669' }};">Pending Customer Balance:</span>
                        <strong class="font-monospace" style="color: {{ $pendingBal > 0.01 ? '#DC2626' : '#059669' }}; font-size: 1.05rem;">
                            ₹{{ number_format($pendingBal, 2) }}
                        </strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Settlement Status:</span>
                        <span class="badge" style="background: {{ $wbSale->payment_status === 'paid' ? '#DCFCE7' : ($wbSale->payment_status === 'partial' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $wbSale->payment_status === 'paid' ? '#15803D' : ($wbSale->payment_status === 'partial' ? '#B45309' : '#B91C1C') }}; font-weight: 700; padding: 4px 8px; border-radius: 6px;">
                            {{ ucfirst($wbSale->payment_status ?? 'unpaid') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer Coordinates Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-user-tie" style="color: #5B841E;"></i> Customer Coordinates
                    </div>
                    @if($wbSale->customer)
                        <a href="{{ route('admin.masters.customer.show', $wbSale->customer) }}" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.72rem;" title="View Customer Profile">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Profile
                        </a>
                    @endif
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Customer Name:</span>
                        <strong class="text-dark">{{ $wbSale->customer->name ?? 'Direct Sale' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Customer Code:</span>
                        <strong class="font-monospace text-dark">{{ $wbSale->customer->code ?? '—' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">City / Destination:</span>
                        <span>{{ $wbSale->customer->city ?? '—' }}{{ $wbSale->customer && $wbSale->customer->state ? ', ' . $wbSale->customer->state : '' }}</span>
                    </div>
                    @if($wbSale->customer && isset($wbSale->customer->current_balance))
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.6rem;">
                            <span class="text-muted">Current Ledger Balance:</span>
                            <strong class="font-monospace" style="color: {{ $wbSale->customer->current_balance > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($wbSale->customer->current_balance, 2) }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Commission Broker Coordinates Card -->
            @if($wbSale->broker)
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-handshake" style="color: #3B82F6;"></i> Commission Broker
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker Name:</span>
                            <strong class="text-dark">{{ $wbSale->broker->name }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Broker Code:</span>
                            <strong class="font-monospace text-dark">{{ $wbSale->broker->code }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Commission Rate:</span>
                            <strong class="font-monospace text-primary">{{ $wbSale->broker->commission_rate }}%</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Est. Commission:</span>
                            <strong class="font-monospace text-dark">₹{{ number_format(($wbSale->total_amount * $wbSale->broker->commission_rate) / 100, 2) }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Transport & Logistics Coordinates Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-truck-moving" style="color: #5B841E;"></i> Transport &amp; Logistics
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Transporter Name:</span>
                        <strong class="text-dark">{{ $wbSale->transporter->name ?? ($wbSale->transport_name ?: 'Self / Direct Mandi') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Vehicle Number:</span>
                        <span class="font-monospace text-dark font-weight-600">{{ $wbSale->vehicle_no ?? 'Direct Handover' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">LR / Bilty Number:</span>
                        <span class="font-monospace text-dark">{{ $wbSale->lr_no ?: '—' }}</span>
                    </div>
                    @if($wbSale->driver_name)
                        <div style="display: flex; justify-content: space-between;">
                            <span class="text-muted">Driver Contact:</span>
                            <span>{{ $wbSale->driver_name }} {{ $wbSale->driver_phone ? '(' . $wbSale->driver_phone . ')' : '' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Weighbridge Weight Breakdown Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                <div style="font-weight: 700; color: #1E293B; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-scale-balanced" style="color: #64748B;"></i> Weighbridge Scale Coordinates
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.84rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Gross Truck Weight:</span>
                        <span class="font-monospace text-dark">{{ number_format($wbSale->gross_weight, 3) }} KG</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Tare Weight:</span>
                        <span class="font-monospace text-dark">{{ number_format($wbSale->tare_weight, 3) }} KG</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">Deduction Weight:</span>
                        <span class="font-monospace text-dark">{{ number_format($wbSale->deduction_weight, 3) }} KG</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid #F1F5F9; padding-top: 0.5rem;">
                        <strong class="text-dark">Net Outward Weight:</strong>
                        <strong class="font-monospace" style="color: #2563EB; font-size: 1rem;">{{ number_format($wbSale->net_weight, 3) }} KG</strong>
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
                    <strong class="font-monospace text-dark">#{{ $wbSale->id }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Created Date:</span>
                    <strong class="text-dark">{{ $wbSale->created_at->format('d M Y, h:i A') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Last Updated:</span>
                    <strong class="text-dark">{{ $wbSale->updated_at->format('d M Y, h:i A') }}</strong>
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
