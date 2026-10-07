@extends('admin.layouts.app')

@section('title', 'Sales Entry - VIKAS UDHYOG ERP')
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
        margin-top: 4px;
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
<section class="view-section active" id="view-txn-sales">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Sales Entry</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-invoice text-primary"></i> Sales Entry (Outward Stock)
            </h1>
            <p class="erp-page-subtitle">
                Record outward sales invoices for buyers &amp; distributors with real-time stock deduction, dual-rate official billing, and under-billing calculations.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Sales List">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.transactions.sales-entry.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> New Sales Entry
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Sales Invoices</div>
                <div class="erp-kpi-val">{{ number_format($totalSales) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Dispatched Consignments</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ number_format($completedCount) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #059669;">
            <div class="erp-kpi-icon-box" style="background: rgba(5, 150, 105, 0.12); color: #059669;">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Grand Total Sales Value</div>
                <div class="erp-kpi-val font-monospace" style="color: #059669;">₹{{ number_format($totalGrandAmount, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #DC2626;">
            <div class="erp-kpi-icon-box" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Pending Customer Receivable</div>
                <div class="erp-kpi-val font-monospace" style="color: #DC2626;">₹{{ number_format($totalPendingAmount, 2) }}</div>
            </div>
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
        <div class="alert erp-alert-danger">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-exclamation erp-alert-icon-danger"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Main Table Container -->
    <div class="card erp-main-card">
        <!-- Filter and Search Header -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.transactions.sales-entry') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by invoice no, customer, vehicle..." class="form-control erp-search-input">
                </div>

                <select name="bill_type" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Bill Types</option>
                    <option value="with_bill" {{ request('bill_type', 'with_bill') == 'with_bill' ? 'selected' : '' }}>With Bill (Default)</option>
                    <option value="without_bill" {{ request('bill_type') == 'without_bill' ? 'selected' : '' }}>Without Bill</option>
                </select>

                <select name="customer_id" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Customers</option>
                    @foreach($customers as $cst)
                        <option value="{{ $cst->id }}" {{ request('customer_id') == $cst->id ? 'selected' : '' }}>{{ $cst->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                    <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filters">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(request('search') || request('customer_id') || request('status') || (request('bill_type') && request('bill_type') !== 'with_bill'))
                    <a href="{{ route('admin.transactions.sales-entry') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $sales->count() }}</strong> of <strong>{{ $sales->total() }}</strong> sales
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Invoice Identity</th>
                        <th>Date</th>
                        <th>Customer Firm &amp; Broker</th>
                        <th>Order Type</th>
                        <th style="text-align: right;">Bill Subtotal</th>
                        <th style="text-align: right;">GST Tax</th>
                        <th style="text-align: right;">Under Billing</th>
                        <th style="text-align: right;">Grand Total</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sal)
                        <tr>
                            <!-- Invoice Identity -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ strtoupper(substr($sal->customer->name ?? 'C', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.transactions.sales-entry.show', $sal) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link font-monospace">
                                            {{ $sal->sale_no }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem; flex-wrap: wrap;">
                                            @if(($sal->bill_type ?? 'with_bill') === 'without_bill')
                                                <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #FDE68A;">
                                                    Without Bill
                                                </span>
                                            @else
                                                <span class="badge" style="background: #EFF6FF; color: #1D4ED8; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #BFDBFE;">
                                                    With Bill
                                                </span>
                                            @endif
                                            @if($sal->invoice_no)
                                                <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                    <i class="fa-solid fa-receipt me-1"></i>{{ $sal->invoice_no }}
                                                </span>
                                            @endif
                                            @if($sal->vehicle_no)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    <i class="fa-solid fa-truck" style="font-size: 0.65rem;"></i> {{ $sal->vehicle_no }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div style="font-weight: 500; color: #334155;">
                                    {{ $sal->sale_date->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.74rem; color: #64748B;">
                                    {{ $sal->sale_date->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Customer & Broker -->
                            <td>
                                <div style="font-weight: 600; color: #1E293B;">
                                    {{ $sal->customer->name ?? 'Direct Customer' }}
                                </div>
                                @if($sal->broker)
                                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.1rem;">
                                        <i class="fa-solid fa-handshake" style="color: #5B841E;"></i> {{ $sal->broker->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Order Type -->
                            <td>
                                <span class="badge" style="background: {{ $sal->order_type === 'Urgent' ? 'rgba(239, 68, 68, 0.1)' : ($sal->order_type === 'Fast' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(91, 132, 30, 0.1)') }}; color: {{ $sal->order_type === 'Urgent' ? '#EF4444' : ($sal->order_type === 'Fast' ? '#D97706' : '#5B841E') }}; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid {{ $sal->order_type === 'Urgent' ? 'rgba(239, 68, 68, 0.2)' : ($sal->order_type === 'Fast' ? 'rgba(245, 158, 11, 0.2)' : 'rgba(91, 132, 30, 0.2)') }};">
                                    {{ $sal->order_type }}
                                </span>
                            </td>

                            <!-- Bill Subtotal -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #334155;">
                                    ₹{{ number_format($sal->subtotal, 2) }}
                                </div>
                            </td>

                            <!-- GST Tax -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-size: 0.85rem; color: #64748B;">
                                    ₹{{ number_format($sal->tax_amount, 2) }}
                                </div>
                            </td>

                            <!-- Under Billing Amount -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #D97706;">
                                    ₹{{ number_format($sal->under_billing_total, 2) }}
                                </div>
                            </td>

                            <!-- Grand Total -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #059669; font-size: 0.95rem;">
                                    ₹{{ number_format($sal->grand_total, 2) }}
                                </div>
                                @php
                                    $pendingBal = max(0, (float)$sal->grand_total - (float)$sal->paid_amount);
                                @endphp
                                @if($pendingBal > 0.01)
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #DC2626; font-weight: 600;" title="Pending Receivable from Customer">
                                        <i class="fa-solid fa-hourglass-half me-1"></i>Due: ₹{{ number_format($pendingBal, 2) }}
                                    </div>
                                @else
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #059669; font-weight: 600;" title="Payment Fully Received">
                                        <i class="fa-solid fa-check me-1"></i>Settled
                                    </div>
                                @endif
                            </td>

                            <!-- Status Lifecycle Dropdown Manager -->
                            <td style="text-align: center;">
                                <div class="dropdown erp-status-dropdown" style="display: inline-block;">
                                    @php
                                        $st = $sal->status ?? 'dispatched';
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

                                    <button type="button" class="erp-status-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }}; cursor: pointer; padding: 4px 10px; border-radius: 20px; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 6px;" title="Click to manage status (Ordered, Dispatched, Delivered, Completed, Cancelled)">
                                        <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $dotColor }}; display: inline-block;"></span>
                                        <span><i class="fa-solid {{ $statusIcon }} me-1" style="font-size: 0.7rem;"></i>{{ ucfirst($st) }}</span>
                                        <i class="fa-solid fa-chevron-down ms-1" style="font-size: 0.62rem; opacity: 0.6;"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="border-radius: 12px; border: 1px solid #E2E8F0; padding: 0.5rem; min-width: 190px; z-index: 1050;">
                                        <li style="padding: 0.35rem 0.65rem; font-size: 0.7rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em;">Change Status</li>
                                        
                                        <!-- Ordered -->
                                        <li>
                                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sal) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sal) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sal) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sal) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.sales-entry.update-status', $sal) }}" onsubmit="return confirm('Cancel sales order #{{ $sal->sale_no }}? Outward stock will be restored to inventory.');">
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
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.transactions.sales-entry.show', $sal) }}" class="erp-table-action-icon" title="View Sales Profile 360">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.sales-entry.edit', $sal) }}" class="erp-table-action-icon" title="Edit Sales Voucher">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteSale({{ $sal->id }}, '{{ $sal->sale_no }}')" title="Delete Sales Voucher">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 3.5rem 1.5rem;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1.6rem; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No Sales Entries Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 420px; margin-bottom: 1.25rem;">
                                        Record your first outward sales consignment to dispatch inventory items and bill customers.
                                    </p>
                                    @if(request('search') || request('customer_id') || request('status'))
                                        <a href="{{ route('admin.transactions.sales-entry') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.transactions.sales-entry.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Record First Sale
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($sales->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $sales->firstItem() }}</strong> to <strong>{{ $sales->lastItem() }}</strong> of <strong>{{ $sales->total() }}</strong> entries
                </div>
                <div>
                    {{ $sales->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Sale Hidden Form -->
<form id="delete-sale-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteSale(saleId, saleNo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Sales Voucher?',
                html: `Are you sure you want to delete sales voucher <strong>#${saleNo}</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">Outward stock will be restored back to inventory and customer balance will be adjusted.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-sale-form');
                    form.action = `{{ url('admin/transactions/sales-entry') }}/${saleId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to delete sales voucher #${saleNo}? Outward stock will be restored to inventory.`)) {
                const form = document.getElementById('delete-sale-form');
                form.action = `{{ url('admin/transactions/sales-entry') }}/${saleId}`;
                form.submit();
            }
        }
    }

    // Interactive Status Lifecycle Dropdown Trigger
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
