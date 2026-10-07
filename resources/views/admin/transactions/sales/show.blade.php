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
                Complete outward sales consignment profile, dual-rate billing breakdown, customer ledger impact &amp; stock dispatch.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Invoice">
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

    <!-- Executive Voucher Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($sale->customer->name ?? 'SE', 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $sale->customer->name ?? 'Direct Sale' }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Invoice Code Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Sales Invoice Reference">
                            <i class="fa-solid fa-hashtag me-1"></i>{{ $sale->sale_no }}
                        </span>

                        <!-- Order Urgency Pill -->
                        <span class="erp-profile-role-pill" title="Order Priority">
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

                        <!-- Vehicle Pill -->
                        @if($sale->vehicle_no)
                            <span class="erp-profile-role-pill font-monospace" title="Transport Vehicle No">
                                <i class="fa-solid fa-truck me-1"></i>{{ $sale->vehicle_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Lifecycle Dropdown Manager -->
            <div>
                <div class="dropdown erp-status-dropdown" style="display: inline-block;">
                    @php
                        $st = $sale->status ?? 'dispatched';
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
                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sale) }}">
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
                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sale) }}">
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
                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sale) }}">
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
                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sale) }}">
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
                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sale) }}" onsubmit="return confirm('Cancel sales order #{{ $sale->sale_no }}? Outward stock will be restored to inventory.');">
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
                <span class="erp-kpi-ribbon-label">Official Bill Subtotal</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #1E293B;">₹{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div class="erp-kpi-ribbon-item">
                <span class="erp-kpi-ribbon-label">GST Tax Total</span>
                <span class="erp-kpi-ribbon-val font-monospace text-muted">₹{{ number_format($sale->tax_amount, 2) }}</span>
            </div>
            <div class="erp-kpi-ribbon-item">
                <span class="erp-kpi-ribbon-label" style="color: #D97706;">Under-Billing Total</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #D97706;">₹{{ number_format($sale->under_billing_total, 2) }}</span>
            </div>
            <div class="erp-kpi-ribbon-item" style="border-left: 2px solid #E2E8F0;">
                <span class="erp-kpi-ribbon-label" style="color: #059669;">Grand Total Outward</span>
                <span class="erp-kpi-ribbon-val font-monospace" style="color: #059669; font-size: 1.35rem;">₹{{ number_format($sale->grand_total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- 2-Column Dossier Detail Grid -->
    <div class="erp-dossier-grid">

        <!-- Column 1: Outward Items Table (Full Width) -->
        <div class="card erp-form-section-card" style="grid-column: 1 / -1;">
            <div class="erp-form-section-header">
                <div class="erp-form-section-header-left">
                    <div class="erp-form-section-icon-box erp-form-icon-success">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="erp-form-section-title">Outward Dispatched Consignment Line Items</h3>
                        <p class="erp-form-section-desc">Itemised official bill rate, under-billing rate, tax split &amp; stock outward deduction</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.5rem; width: 45px; text-align: center;">#</th>
                            <th style="min-width: 200px;">Item Description</th>
                            <th>HSN</th>
                            <th style="text-align: right;">Dispatched Qty</th>
                            <th style="text-align: right;">Bill Rate (₹)</th>
                            <th style="text-align: right;">GST Rate</th>
                            <th style="text-align: right;">Bill Amount (₹)</th>
                            <th style="text-align: right;">U-B Rate (₹)</th>
                            <th style="text-align: right;">U-B Amount (₹)</th>
                            <th style="text-align: right; padding-right: 1.5rem;">Line Total (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $idx => $item)
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
                                        @if($item->batch_no)
                                            &bull; Batch: <span class="font-monospace">{{ $item->batch_no }}</span>
                                        @endif
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
                                    <span class="font-monospace text-dark">₹{{ number_format($item->bill_rate, 2) }}</span>
                                </td>
                                <td style="text-align: right;">
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
                                <td style="text-align: right; padding-right: 1.5rem;">
                                    <span class="font-monospace font-weight-700" style="color: #059669; font-size: 0.95rem;">₹{{ number_format($item->total_amount, 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Column 1: Customer & Logistics Dossier -->
        <div class="card erp-form-section-card">
            <div class="erp-form-section-header">
                <div class="erp-form-section-header-left">
                    <div class="erp-form-section-icon-box erp-form-icon-primary">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div>
                        <h3 class="erp-form-section-title">Customer &amp; Logistics Overview</h3>
                        <p class="erp-form-section-desc">Buyer coordinates, transport dispatch &amp; commission broker</p>
                    </div>
                </div>
            </div>

            <div class="erp-dossier-breakdown-list">
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Customer Legal Name</span>
                    <span class="erp-dossier-breakdown-val font-weight-600">{{ $sale->customer->name ?? '--' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Customer Code</span>
                    <span class="erp-dossier-breakdown-val font-monospace">{{ $sale->customer->code ?? '--' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">City / Destination</span>
                    <span class="erp-dossier-breakdown-val">{{ $sale->customer->city ?? '--' }}, {{ $sale->customer->state ?? '--' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Customer GSTIN</span>
                    <span class="erp-dossier-breakdown-val font-monospace">{{ $sale->customer->gstin ?? 'Unregistered' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Commission Broker</span>
                    <span class="erp-dossier-breakdown-val">
                        @if($sale->broker)
                            <i class="fa-solid fa-handshake text-primary me-1"></i>{{ $sale->broker->name }} ({{ $sale->broker->commission_rate }}%)
                        @else
                            Direct Sales (No Broker)
                        @endif
                    </span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Transport Vehicle</span>
                    <span class="erp-dossier-breakdown-val font-monospace">{{ $sale->vehicle_no ?? 'Direct Handover / Self Transport' }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Agreed Payment Terms</span>
                    <span class="erp-dossier-breakdown-val">{{ $sale->payment_terms }}</span>
                </div>
            </div>
        </div>

        <!-- Column 2: Financial Reconciliation & Payment Tracking -->
        <div class="card erp-form-section-card">
            <div class="erp-form-section-header">
                <div class="erp-form-section-header-left">
                    <div class="erp-form-section-icon-box erp-form-icon-purple">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <div>
                        <h3 class="erp-form-section-title">Financial Settlement &amp; Receivables</h3>
                        <p class="erp-form-section-desc">Invoice values, receipts, pending receivables and accounts status</p>
                    </div>
                </div>
            </div>

            <div class="erp-dossier-breakdown-list">
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Official Taxable Subtotal</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-600">₹{{ number_format($sale->subtotal, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">GST Tax Total</span>
                    <span class="erp-dossier-breakdown-val font-monospace text-muted">₹{{ number_format($sale->tax_amount, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Official Bill Amount</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-600" style="color: #2563EB;">₹{{ number_format($sale->bill_total, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label" style="color: #D97706;">Under-Billing (Cash Portion)</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-700" style="color: #D97706;">₹{{ number_format($sale->under_billing_total, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item" style="border-top: 2px solid #E2E8F0; background: #F8FAFC; padding-top: 0.75rem; padding-bottom: 0.75rem;">
                    <span class="erp-dossier-breakdown-label" style="font-weight: 700; color: #0F172A; font-size: 0.95rem;">Total Receivable Grand Value</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-700" style="color: #059669; font-size: 1.25rem;">₹{{ number_format($sale->grand_total, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Payment Received from Buyer</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-600" style="color: #059669;">₹{{ number_format($sale->paid_amount, 2) }}</span>
                </div>
                @php
                    $pendingBal = max(0, (float)$sale->grand_total - (float)$sale->paid_amount);
                @endphp
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label" style="color: #DC2626; font-weight: 600;">Pending Customer Balance</span>
                    <span class="erp-dossier-breakdown-val font-monospace font-weight-700" style="color: #DC2626; font-size: 1.15rem;">₹{{ number_format($pendingBal, 2) }}</span>
                </div>
                <div class="erp-dossier-breakdown-item">
                    <span class="erp-dossier-breakdown-label">Payment Status</span>
                    <span class="erp-dossier-breakdown-val">
                        <span class="badge" style="background: {{ $sale->payment_status === 'paid' ? '#ECFDF5' : ($sale->payment_status === 'partial' ? '#FEF3C7' : '#FEF2F2') }}; color: {{ $sale->payment_status === 'paid' ? '#065F46' : ($sale->payment_status === 'partial' ? '#92400E' : '#B91C1C') }}; font-weight: 700; padding: 4px 8px; border-radius: 6px;">
                            {{ ucfirst($sale->payment_status) }}
                        </span>
                    </span>
                </div>
                @if($sale->notes)
                    <div class="erp-dossier-breakdown-item" style="flex-direction: column; align-items: flex-start; gap: 0.35rem;">
                        <span class="erp-dossier-breakdown-label">Dispatch Remarks / Notes</span>
                        <div style="font-size: 0.85rem; color: #475569; background: #F8FAFC; padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #E2E8F0; width: 100%;">
                            {{ $sale->notes }}
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
