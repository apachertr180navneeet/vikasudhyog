@extends('admin.layouts.app')

@section('title', 'WB Sales Entry (Without Bill) - VIKAS UDHYOG ERP')
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
<section class="view-section active" id="view-txn-wb-sales">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">WB Sales Entry</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-scale-unbalanced text-primary"></i> WB Sales Entry (Without Bill Outward)
            </h1>
            <p class="erp-page-subtitle">
                Manage Weighbridge (WB) Mandi Cash Outward Sales, direct buyer deliveries, and outward stock deduction at agreed rates.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print WB Sales Register">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.transactions.wb-sales-entry.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> New WB Sales Entry
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
                <div class="erp-kpi-label">Total WB Sales Slips</div>
                <div class="erp-kpi-val">{{ number_format($totalSlips) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Outward Net Weight</div>
                <div class="erp-kpi-val font-monospace" style="color: #2563EB;">
                    {{ number_format($totalNetWeight, 2) }} <span style="font-size: 0.85rem; font-weight: normal; color: #64748B;">KG</span>
                </div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Outward Value</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($totalAmount, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid {{ ($totalPendingAmount ?? 0) > 0 ? '#DC2626' : '#059669' }};">
            <div class="erp-kpi-icon-box" style="background: {{ ($totalPendingAmount ?? 0) > 0 ? 'rgba(220, 38, 38, 0.12)' : 'rgba(5, 150, 105, 0.12)' }}; color: {{ ($totalPendingAmount ?? 0) > 0 ? '#DC2626' : '#059669' }};">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Pending Customer Due</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ ($totalPendingAmount ?? 0) > 0 ? '#DC2626' : '#059669' }};">₹{{ number_format($totalPendingAmount ?? 0, 2) }}</div>
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
        <!-- Filter Header -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.transactions.wb-sales-entry') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by slip no, vehicle, driver, customer..." class="form-control erp-search-input">
                </div>

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

                @if(request('search') || request('customer_id') || request('status'))
                    <a href="{{ route('admin.transactions.wb-sales-entry') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $wbSales->count() }}</strong> of <strong>{{ $wbSales->total() }}</strong> slips
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Slip Identity</th>
                        <th>Date</th>
                        <th>Customer Firm &amp; Broker</th>
                        <th>Order Type</th>
                        <th style="text-align: right;">Gross Weight</th>
                        <th style="text-align: right;">Tare Weight</th>
                        <th style="text-align: right;">Net Outward Wt</th>
                        <th style="text-align: right;">Total Outward Value</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wbSales as $slip)
                        <tr>
                            <!-- Slip Identity -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ strtoupper(substr($slip->customer->name ?? 'W', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.transactions.wb-sales-entry.show', $slip) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link font-monospace">
                                            {{ $slip->slip_no }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem; flex-wrap: wrap;">
                                            <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #FDE68A;">
                                                Without Bill
                                            </span>
                                            @if($slip->vehicle_no)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    <i class="fa-solid fa-truck" style="font-size: 0.65rem;"></i> {{ $slip->vehicle_no }}
                                                </span>
                                            @endif
                                            @if($slip->driver_name)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    &bull; {{ $slip->driver_name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div style="font-weight: 500; color: #334155;">
                                    {{ $slip->entry_date->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.74rem; color: #64748B;">
                                    {{ $slip->entry_date->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Customer & Broker -->
                            <td>
                                <div style="font-weight: 600; color: #1E293B;">
                                    {{ $slip->customer->name ?? 'Direct Customer' }}
                                </div>
                                @if($slip->broker)
                                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.1rem;">
                                        <i class="fa-solid fa-handshake" style="color: #5B841E;"></i> {{ $slip->broker->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Order Type -->
                            <td>
                                <span class="badge" style="background: {{ $slip->order_type === 'Urgent' ? 'rgba(239, 68, 68, 0.1)' : ($slip->order_type === 'Fast' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(91, 132, 30, 0.1)') }}; color: {{ $slip->order_type === 'Urgent' ? '#EF4444' : ($slip->order_type === 'Fast' ? '#D97706' : '#5B841E') }}; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid {{ $slip->order_type === 'Urgent' ? 'rgba(239, 68, 68, 0.2)' : ($slip->order_type === 'Fast' ? 'rgba(245, 158, 11, 0.2)' : 'rgba(91, 132, 30, 0.2)') }};">
                                    {{ $slip->order_type }}
                                </span>
                            </td>

                            <!-- Gross Weight -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 500; color: #64748B;">
                                    {{ number_format($slip->gross_weight, 3) }} <span style="font-size: 0.72rem;">KG</span>
                                </div>
                            </td>

                            <!-- Tare Weight -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 500; color: #64748B;">
                                    {{ number_format($slip->tare_weight, 3) }} <span style="font-size: 0.72rem;">KG</span>
                                </div>
                            </td>

                            <!-- Net Outward Weight -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #2563EB; font-size: 0.95rem;">
                                    {{ number_format($slip->net_weight, 3) }} <span style="font-size: 0.75rem;">KG</span>
                                </div>
                            </td>

                            <!-- Total Outward Value -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #059669; font-size: 0.95rem;">
                                    ₹{{ number_format($slip->total_amount, 2) }}
                                </div>
                                @php
                                    $pendingBal = max(0, (float)$slip->total_amount - (float)$slip->paid_amount);
                                @endphp
                                @if($pendingBal > 0.01)
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #DC2626; font-weight: 600;" title="Pending Customer Receivable">
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
                                        $st = $slip->status ?? 'dispatched';
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
                                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $slip) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $slip) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $slip) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $slip) }}">
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
                                            <form method="POST" action="{{ route('admin.transactions.wb-sales-entry.update-status', $slip) }}" onsubmit="return confirm('Cancel weighbridge sales slip #{{ $slip->slip_no }}? Outward stock will be restored to inventory.');">
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
                                    <a href="{{ route('admin.transactions.wb-sales-entry.show', $slip) }}" class="erp-table-action-icon" title="View WB Slip Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.wb-sales-entry.edit', $slip) }}" class="erp-table-action-icon" title="Edit WB Slip">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteWBSale({{ $slip->id }}, '{{ $slip->slip_no }}')" title="Delete WB Slip">
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
                                        <i class="fa-solid fa-scale-unbalanced"></i>
                                    </div>
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No WB Sales Slips Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 420px; margin-bottom: 1.25rem;">
                                        Record your first outward weighbridge slip to deduct stock and generate buyer cash slip.
                                    </p>
                                    @if(request('search') || request('customer_id') || request('status'))
                                        <a href="{{ route('admin.transactions.wb-sales-entry') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.transactions.wb-sales-entry.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Record First WB Sale
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
        @if($wbSales->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $wbSales->firstItem() }}</strong> to <strong>{{ $wbSales->lastItem() }}</strong> of <strong>{{ $wbSales->total() }}</strong> entries
                </div>
                <div>
                    {{ $wbSales->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete WB Sale Hidden Form -->
<form id="delete-wb-sale-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteWBSale(slipId, slipNo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete WB Sales Slip?',
                html: `Are you sure you want to delete WB sales slip <strong>#${slipNo}</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">Outward stock will be restored back to inventory.</span>`,
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
                    const form = document.getElementById('delete-wb-sale-form');
                    form.action = `{{ url('admin/transactions/wb-sales-entry') }}/${slipId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to delete WB sales slip #${slipNo}? Outward stock will be restored.`)) {
                const form = document.getElementById('delete-wb-sale-form');
                form.action = `{{ url('admin/transactions/wb-sales-entry') }}/${slipId}`;
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
