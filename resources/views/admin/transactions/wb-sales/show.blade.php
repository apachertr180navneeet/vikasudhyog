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
        transition: background-color 0.15s ease;
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

    <!-- Executive Slip Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($wbSale->customer->name ?? 'WS', 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $wbSale->customer->name ?? 'Customer' }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Slip No Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="WB Slip Reference">
                            <i class="fa-solid fa-hashtag me-1"></i>{{ $wbSale->slip_no }}
                        </span>

                        <!-- Bill Type Pill -->
                        <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 4px; border: 1px solid #FDE68A;">
                            Without Bill
                        </span>

                        <!-- Order Urgency Pill -->
                        <span class="erp-profile-role-pill" title="Order Priority">
                            <i class="fa-solid fa-tag me-1"></i>{{ $wbSale->order_type }}
                        </span>

                        <!-- Entry Date Pill -->
                        <span class="erp-profile-role-pill" title="Dispatch Date">
                            <i class="fa-solid fa-calendar-day me-1"></i>{{ $wbSale->entry_date->format('d M Y') }}
                        </span>

                        <!-- Vehicle Pill -->
                        @if($wbSale->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Truck Registration">
                                <i class="fa-solid fa-truck me-1"></i>{{ $wbSale->vehicle_no }}
                            </span>
                        @endif

                        <!-- Customer Challan Pill -->
                        @if($wbSale->invoice_no)
                            <span class="erp-profile-role-pill font-monospace" title="Customer Challan / PO No">
                                <i class="fa-solid fa-receipt me-1"></i>{{ $wbSale->invoice_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Lifecycle Dropdown Manager -->
            <div>
                <div class="dropdown erp-status-dropdown" style="display: inline-block;">
                    @php
                        $st = $wbSale->status ?? 'dispatched';
                        $badgeBg = match($st) {
                            'ordered' => '#EFF6FF',
                            'dispatched' => '#F0FDF4',
                            'delivered' => '#F5F3FF',
                            'completed' => '#ECFDF5',
                            'cancelled' => '#FFF1F2',
                            default => '#F1F5F9',
                        };
                        $badgeColor = match($st) {
                            'ordered' => '#1D4ED8',
                            'dispatched' => '#15803D',
                            'delivered' => '#6D28D9',
                            'completed' => '#047857',
                            'cancelled' => '#BE123C',
                            default => '#475569',
                        };
                        $badgeBorder = match($st) {
                            'ordered' => '#BFDBFE',
                            'dispatched' => '#BBF7D0',
                            'delivered' => '#DDD6FE',
                            'completed' => '#A7F3D0',
                            'cancelled' => '#FECDD3',
                            default => '#CBD5E1',
                        };
                        $dotColor = match($st) {
                            'ordered' => '#3B82F6',
                            'dispatched' => '#16A34A',
                            'delivered' => '#8B5CF6',
                            'completed' => '#059669',
                            'cancelled' => '#E11D48',
                            default => '#64748B',
                        };
                        $statusIcon = match($st) {
                            'ordered' => 'fa-clock',
                            'dispatched' => 'fa-truck-fast',
                            'delivered' => 'fa-truck-ramp-box',
                            'completed' => 'fa-circle-check',
                            'cancelled' => 'fa-ban',
                            default => 'fa-circle-dot',
                        };
                    @endphp

                    <button type="button" class="erp-status-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }}; cursor: pointer; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.82rem; display: inline-flex; align-items: center; gap: 7px;" title="Click to manage status (Ordered, Dispatched, Delivered, Completed, Cancelled)">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                        <span><i class="fa-solid {{ $statusIcon }} me-1"></i>{{ ucfirst($st) }}</span>
                        <i class="fa-solid fa-chevron-down ms-1" style="font-size: 0.65rem; opacity: 0.7;"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius: 12px; border: 1px solid #E2E8F0; padding: 0.5rem; min-width: 195px; z-index: 1050;">
                        <li style="padding: 0.35rem 0.65rem; font-size: 0.7rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Change Status</li>
                        
                        <!-- Ordered -->
                        <li>
                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $wbSale) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ordered">
                                <button type="submit" class="dropdown-item {{ $st === 'ordered' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; padding: 0.45rem 0.65rem; border-radius: 6px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #3B82F6;"></span>
                                    <span style="font-weight: 600; color: #1E293B;"><i class="fa-solid fa-clock me-1 text-primary"></i>Ordered</span>
                                </button>
                            </form>
                        </li>

                        <!-- Dispatched -->
                        <li>
                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $wbSale) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="dispatched">
                                <button type="submit" class="dropdown-item {{ $st === 'dispatched' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; padding: 0.45rem 0.65rem; border-radius: 6px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16A34A;"></span>
                                    <span style="font-weight: 600; color: #1E293B;"><i class="fa-solid fa-truck-fast me-1 text-success"></i>Dispatched</span>
                                </button>
                            </form>
                        </li>

                        <!-- Delivered -->
                        <li>
                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $wbSale) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="delivered">
                                <button type="submit" class="dropdown-item {{ $st === 'delivered' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; padding: 0.45rem 0.65rem; border-radius: 6px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #8B5CF6;"></span>
                                    <span style="font-weight: 600; color: #1E293B;"><i class="fa-solid fa-truck-ramp-box me-1" style="color: #8B5CF6;"></i>Delivered</span>
                                </button>
                            </form>
                        </li>

                        <!-- Completed -->
                        <li>
                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $wbSale) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="dropdown-item {{ $st === 'completed' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; padding: 0.45rem 0.65rem; border-radius: 6px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #059669;"></span>
                                    <span style="font-weight: 600; color: #1E293B;"><i class="fa-solid fa-circle-check me-1" style="color: #059669;"></i>Completed</span>
                                </button>
                            </form>
                        </li>

                        <li style="border-top: 1px solid #F1F5F9; margin: 0.35rem 0;"></li>

                        <!-- Cancelled -->
                        <li>
                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $wbSale) }}" onsubmit="return confirm('Cancel weighbridge sales slip #{{ $wbSale->slip_no }}? Outward stock will be restored to inventory.');">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="dropdown-item text-danger {{ $st === 'cancelled' ? 'active' : '' }}" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; padding: 0.45rem 0.65rem; border-radius: 6px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #E11D48;"></span>
                                    <span style="font-weight: 600;"><i class="fa-solid fa-ban me-1 text-danger"></i>Cancelled</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 4-Stat KPI Metric Ribbon -->
        <div class="erp-kpi-ribbon">
            <div class="erp-kpi-ribbon-item">
                <span class="erp-kpi-ribbon-label">Gross Weighbridge Wt</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #64748B;">{{ number_format($wbSale->gross_weight, 3) }} KG</span>
            </div>
            <div class="erp-kpi-ribbon-item">
                <span class="erp-kpi-ribbon-label">Tare Deduction Wt</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #64748B;">{{ number_format($wbSale->tare_weight + $wbSale->deduction_weight, 3) }} KG</span>
            </div>
            <div class="erp-kpi-ribbon-item">
                <span class="erp-kpi-ribbon-label" style="color: #2563EB;">Net Outward Weight</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #2563EB;">{{ number_format($wbSale->net_weight, 3) }} KG</span>
            </div>
            <div class="erp-kpi-ribbon-item" style="border-left: 2px solid #E2E8F0;">
                <span class="erp-kpi-ribbon-label" style="color: #059669;">Total Outward Value</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #059669; font-size: 1.35rem;">₹{{ number_format($wbSale->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- 2-Column Dossier Detail Grid -->
    <div class="erp-dossier-grid">

        <!-- Column 1: Items Table (Full Width) -->
        <div class="card erp-form-section-card" style="grid-column: 1 / -1;">
            <div class="erp-form-section-header">
                <div class="erp-form-section-header-left">
                    <div class="erp-form-section-icon-box erp-form-icon-success">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="erp-form-section-title">Outward Dispatched Line Items (Direct Mandi Rate)</h3>
                        <p class="erp-form-section-desc">Raw materials and herbs outward consignment details at direct agreed rate (Without GST / UB)</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem; width: 45px; text-align: center;">#</th>
                            <th style="min-width: 240px;">Item Description</th>
                            <th>HSN</th>
                            <th style="text-align: right;">Net Outward Qty</th>
                            <th style="text-align: right;">Mandi Rate (₹)</th>
                            <th style="text-align: right;">Total Amount (₹)</th>
                            <th style="padding-right: 1.5rem;">Remarks / Quality Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($wbSale->items as $idx => $item)
                            <tr>
                                <td style="padding-left: 1.5rem; text-align: center;">
                                    <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">{{ $idx + 1 }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: #1E293B;">
                                        {{ $item->item->name ?? 'Unknown Item' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        Code: <span class="font-monospace">{{ $item->item->code ?? '--' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace" style="font-size: 0.8rem; color: #475569;">{{ $item->hsn_code ?? '--' }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="font-monospace" style="font-weight: 700; color: #0F172A;">
                                        {{ number_format($item->quantity, 3) }}
                                    </span>
                                    <span style="font-size: 0.72rem; color: #64748B;">{{ $item->unit }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="font-monospace text-dark font-weight-600">₹{{ number_format($item->rate, 2) }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <span class="font-monospace font-weight-700" style="color: #059669; font-size: 0.95rem;">₹{{ number_format($item->amount, 2) }}</span>
                                </td>
                                <td style="padding-right: 1.5rem;">
                                    <span style="font-size: 0.82rem; color: #64748B;">{{ $item->notes ?? '--' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Column 1: Logistics & Buyer Details -->
        <div class="card erp-form-section-card">
            <div class="erp-form-section-header">
                <div class="erp-form-section-header-left">
                    <div class="erp-form-section-icon-box erp-form-icon-primary">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <div>
                        <h3 class="erp-form-section-title">Customer &amp; Logistics Overview</h3>
                        <p class="erp-form-section-desc">Buyer coordinates, transport driver &amp; commission broker</p>
                    </div>
                </div>
            </div>

            <div class="erp-dossier-breakdown-list">
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Customer Name</span>
                    <span class="erp-dossier-breakdown-val font-weight-600">{{ $wbSale->customer->name ?? '--' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Customer Code</span>
                    <span class="erp-dossier-breakdown-val font-monospace">{{ $wbSale->customer->code ?? '--' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">City / Destination</span>
                    <span class="erp-dossier-breakdown-val">{{ $wbSale->customer->city ?? '--' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Commission Broker</span>
                    <span class="erp-dossier-breakdown-val">
                        @if($wbSale->broker)
                            <i class="fa-solid fa-handshake text-primary me-1"></i>{{ $wbSale->broker->name }} ({{ $wbSale->broker->commission_rate }}%)
                        @else
                            Direct Mandi (No Broker)
                        @endif
                    </span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Transport Vehicle</span>
                    <span class="erp-dossier-breakdown-val font-monospace">{{ $wbSale->vehicle_no ?? 'Direct Handover' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Driver Contact</span>
                    <span class="erp-dossier-breakdown-val">{{ $wbSale->driver_name ?? '--' }} {{ $wbSale->driver_phone ? '(' . $wbSale->driver_phone . ')' : '' }}</span>
                </div>
            </div>
        </div>

        <!-- Column 2: Financial Settlement -->
        <div class="card erp-form-section-card">
            <div class="erp-form-section-header">
                <div class="erp-form-section-header-left">
                    <div class="erp-form-section-icon-box erp-form-icon-purple">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <h3 class="erp-form-section-title">Cash Settlement &amp; Receivables</h3>
                        <p class="erp-form-section-desc">Mandi payments, received amounts and accounts receivable</p>
                    </div>
                </div>
            </div>

            <div class="erp-dossier-breakdown-list">
                <div class="erp-dossier-breakdown-item" style="border-top: 2px solid #E2E8F0; background: #F8FAFC; padding-top: 0.75rem; padding-bottom: 0.75rem;">
                    <span class="erp-dossier-breakdown-label" style="font-weight: 700; color: #0F172A; font-size: 0.95rem;">Total Outward Value</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-700" style="color: #059669; font-size: 1.25rem;">₹{{ number_format($wbSale->total_amount, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Payment Mode</span>
                    <span class="erp-dossier-breakdown-val font-weight-600">{{ $wbSale->payment_mode }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Amount Received</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-600" style="color: #059669;">₹{{ number_format($wbSale->paid_amount, 2) }}</span>
                </div>
                @php
                    $pendingBal = max(0, (float)$wbSale->total_amount - (float)$wbSale->paid_amount);
                @endphp
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label" style="color: #DC2626; font-weight: 600;">Pending Customer Balance</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-700" style="color: #DC2626; font-size: 1.15rem;">₹{{ number_format($pendingBal, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Settlement Status</span>
                    <span class="erp-dossier-breakdown-val">
                        <span class="badge" style="background: {{ $wbSale->payment_status === 'paid' ? '#ECFDF5' : '#FEF2F2' }}; color: {{ $wbSale->payment_status === 'paid' ? '#065F46' : '#B91C1C' }}; font-weight: 700; padding: 4px 8px; border-radius: 6px;">
                            {{ ucfirst($wbSale->payment_status) }}
                        </span>
                    </span>
                </div>
                @if($wbSale->notes)
                    <div class="erp-dossier-breakdown-item" style="flex-direction: column; align-items: flex-start; gap: 0.35rem;">
                        <span class="erp-dossier-breakdown-label">Weighbridge Notes</span>
                        <div style="font-size: 0.85rem; color: #475569; background: #F8FAFC; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #E2E8F0; width: 100%;">
                            {{ $wbSale->notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</section>

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
@endsection
