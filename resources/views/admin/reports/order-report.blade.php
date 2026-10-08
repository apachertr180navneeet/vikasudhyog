@extends('admin.layouts.app')

@section('title', 'Order Report - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-order')

@section('content')
<section class="view-section active" id="view-rpt-order">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Order Report</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-clipboard-list text-primary"></i> Order Report &amp; Fulfillment Register
            </h1>
            <p class="erp-page-subtitle">
                Track sales order bookings, delivery schedules, fulfillment velocity, priority dispatches &amp; logistics pipeline.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Order Register">
                <i class="fa-solid fa-print"></i> Print Register
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Filtered Register to CSV">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.transactions.order-dispatch') }}" class="btn btn-primary erp-btn-header-primary" title="Open Dispatch & Logistics Operations">
                <i class="fa-solid fa-truck-fast"></i> Dispatch Hub
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
        <!-- 1. Total Orders Booked -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #0F172A;">
            <div class="erp-kpi-icon-box" style="background: rgba(15, 23, 42, 0.08); color: #0F172A;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Orders Booked</div>
                <div class="erp-kpi-val font-monospace" style="color: #0F172A;">
                    {{ number_format($stats['total_orders'] ?? 0) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Pipeline Value: <strong class="font-monospace">₹{{ number_format($stats['total_value'] ?? 0, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- 2. Pending Fulfillment & Transit -->
        <div class="card erp-kpi-card erp-kpi-primary" style="border-left: 4px solid #EAB308;">
            <div class="erp-kpi-icon-box" style="background: rgba(234, 179, 8, 0.12); color: #CA8A04;">
                <i class="fa-solid fa-dolly"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active / In-Transit</div>
                <div class="erp-kpi-val font-monospace" style="color: #CA8A04;">
                    {{ number_format(($stats['pending_count'] ?? 0) + ($stats['dispatched_count'] ?? 0)) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    {{ $stats['pending_count'] ?? 0 }} Pending Booking &bull; {{ $stats['dispatched_count'] ?? 0 }} In Transit
                </div>
            </div>
        </div>

        <!-- 3. Fulfilled & Completed -->
        <div class="card erp-kpi-card erp-kpi-success" style="border-left: 4px solid #059669;">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Fulfilled Orders</div>
                <div class="erp-kpi-val font-monospace" style="color: #059669;">
                    {{ number_format($stats['completed_count'] ?? 0) }}
                </div>
                <div style="font-size: 0.74rem; color: #059669; margin-top: 2px; font-weight: 600;">
                    Fulfillment Rate: {{ $stats['fulfillment_rate'] ?? 0 }}%
                </div>
            </div>
        </div>

        <!-- 4. Total Ordered Volume -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #5B841E;">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Demand Volume</div>
                <div class="erp-kpi-val font-monospace" style="color: #5B841E;">
                    {{ number_format($stats['total_volume'] ?? 0, 1) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Units / KG booked across orders
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Card -->
    <div class="card erp-table-filter-header" style="margin-bottom: 1.25rem; padding: 1.15rem; background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0;">
        
        <!-- Date Preset Shortcut Pills & Mode Switcher -->
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
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'orders']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'orders' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-list-check me-1"></i> Order Register
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'priority']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'priority' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Priority Analysis
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'items']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'items' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-boxes-packing me-1"></i> Item Demand
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'customers']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'customers' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-users-viewfinder me-1"></i> Customer Demand
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.reports.order-report') }}" method="GET" class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
            <input type="hidden" name="view_mode" value="{{ $viewMode }}">
            <input type="hidden" name="date_preset" value="{{ $currentPreset === 'custom' ? 'custom' : $currentPreset }}">

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; flex: 1;">
                
                <!-- Keyword Search -->
                <div class="erp-search-wrap" style="min-width: 220px; flex: 1; max-width: 300px;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Order No, Customer, Vehicle..." class="form-control erp-search-input">
                </div>

                <!-- Custom Dates -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">FROM:</span>
                    <input type="date" name="from_date" class="form-control erp-filter-select" value="{{ $filters['from_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">TO:</span>
                    <input type="date" name="to_date" class="form-control erp-filter-select" value="{{ $filters['to_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                </div>

                <!-- Customer Selector -->
                <select name="customer_id" class="form-control erp-filter-select" style="min-width: 160px;">
                    <option value="">All Customers</option>
                    @foreach($customers as $cst)
                        <option value="{{ $cst->id }}" {{ ($filters['customer_id'] ?? '') == $cst->id ? 'selected' : '' }}>
                            {{ $cst->name }} ({{ $cst->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Priority Selector -->
                <select name="order_type" class="form-control erp-filter-select" style="min-width: 135px;">
                    <option value="all">All Priorities</option>
                    <option value="Urgent" {{ ($filters['order_type'] ?? '') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="Fast" {{ ($filters['order_type'] ?? '') === 'Fast' ? 'selected' : '' }}>Fast</option>
                    <option value="Ready Delivery" {{ ($filters['order_type'] ?? '') === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                    <option value="Medium" {{ ($filters['order_type'] ?? '') === 'Medium' ? 'selected' : '' }}>Medium</option>
                </select>

                <!-- Fulfillment Status Selector -->
                <select name="status" class="form-control erp-filter-select" style="min-width: 140px;">
                    <option value="all">All Statuses</option>
                    <option value="ordered" {{ ($filters['status'] ?? '') === 'ordered' ? 'selected' : '' }}>Ordered / Pending</option>
                    <option value="dispatched" {{ ($filters['status'] ?? '') === 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                    <option value="delivered" {{ ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <!-- Item Selector -->
                <select name="item_id" class="form-control erp-filter-select" style="min-width: 150px;">
                    <option value="">All Products</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}" {{ ($filters['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->name }} ({{ $itm->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Broker Selector -->
                <select name="broker_id" class="form-control erp-filter-select" style="min-width: 130px;">
                    <option value="">All Brokers</option>
                    @foreach($brokers as $brk)
                        <option value="{{ $brk->id }}" {{ ($filters['broker_id'] ?? '') == $brk->id ? 'selected' : '' }}>
                            {{ $brk->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: auto;">
                <button type="submit" class="btn btn-primary btn-sm erp-btn-filter" style="padding: 0.45rem 1rem;">
                    <i class="fa-solid fa-filter me-1"></i> Apply Filter
                </button>
                <a href="{{ route('admin.reports.order-report', ['view_mode' => $viewMode]) }}" class="btn btn-outline btn-sm erp-btn-filter-clear" style="padding: 0.45rem 0.85rem;" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

    </div>

    <!-- Edge-to-Edge Data Card -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); background: #FFFFFF;">

        <!-- MODE 1: ORDER REGISTER TABLE -->
        @if($viewMode === 'orders')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Date &amp; Order No</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer &amp; Destination</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Priority</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Broker &amp; Logistics</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Qty</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Order Value</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Fulfillment</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Payment</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;" class="erp-actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ordersData as $order)
                            @php
                                $totalQty = (float)$order->items->sum('quantity');
                                $prio = $order->order_type ?: 'Medium';
                            @endphp
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Date & Order No -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.88rem; color: #0F172A;">
                                        {{ $order->sale_date ? $order->sale_date->format('d M Y') : '—' }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.78rem; font-weight: 700; color: #5B841E; margin-top: 2px;">
                                        {{ $order->sale_no }}
                                    </div>
                                    @if($order->invoice_no)
                                        <div style="font-size: 0.7rem; color: #64748B;">
                                            Inv: #{{ $order->invoice_no }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Customer & Destination -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; flex-shrink: 0; box-shadow: 0 2px 5px rgba(91, 132, 30, 0.25);">
                                            {{ $order->customer ? $order->customer->initials : 'C' }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                                {{ $order->customer ? $order->customer->name : 'N/A' }}
                                            </div>
                                            <div style="font-size: 0.74rem; color: #64748B;">
                                                <span class="font-monospace" style="font-weight: 600; color: #475569;">{{ $order->customer ? $order->customer->code : '—' }}</span>
                                                @if($order->customer && $order->customer->city)
                                                    &bull; <i class="fa-solid fa-location-dot me-1" style="font-size: 0.7rem;"></i>{{ $order->customer->city }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Priority -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    @if($prio === 'Urgent')
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(220, 38, 38, 0.3);">
                                            <i class="fa-solid fa-fire me-1"></i> Urgent
                                        </span>
                                    @elseif($prio === 'Fast')
                                        <span class="badge" style="background: rgba(217, 119, 6, 0.12); color: #D97706; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(217, 119, 6, 0.3);">
                                            <i class="fa-solid fa-bolt me-1"></i> Fast
                                        </span>
                                    @elseif($prio === 'Ready Delivery')
                                        <span class="badge" style="background: rgba(2, 132, 199, 0.12); color: #0284C7; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(2, 132, 199, 0.3);">
                                            <i class="fa-solid fa-truck-ramp-box me-1"></i> Ready Delivery
                                        </span>
                                    @else
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                            Medium
                                        </span>
                                    @endif
                                </td>

                                <!-- Broker & Logistics -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-size: 0.84rem; font-weight: 600; color: #334155;">
                                        {{ $order->broker ? $order->broker->name : 'Direct Booking' }}
                                    </div>
                                    @if($order->vehicle_no)
                                        <div class="font-monospace" style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">
                                            <i class="fa-solid fa-truck-moving me-1"></i>{{ $order->vehicle_no }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Total Quantity -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.94rem; color: #5B841E;">
                                        {{ number_format($totalQty, 2) }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #64748B;">
                                        {{ $order->items->count() }} items
                                    </div>
                                </td>

                                <!-- Order Value -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: #0F172A;">
                                        ₹{{ number_format((float)$order->grand_total, 2) }}
                                    </div>
                                </td>

                                <!-- Fulfillment Status -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    @if(in_array($order->status, ['delivered', 'completed']))
                                        <span class="badge" style="background: rgba(5, 150, 105, 0.12); color: #059669; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-circle-check me-1"></i> {{ ucfirst($order->status) }}
                                        </span>
                                    @elseif(in_array($order->status, ['dispatched', 'in_transit']))
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-truck-fast me-1"></i> Dispatched
                                        </span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            Cancelled
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(234, 179, 8, 0.15); color: #B45309; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 9999px;">
                                            <i class="fa-solid fa-clock me-1"></i> Ordered
                                        </span>
                                    @endif
                                </td>

                                <!-- Payment Status -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    @if($order->payment_status === 'paid')
                                        <span class="badge" style="background: rgba(5, 150, 105, 0.1); color: #059669; font-weight: 700; font-size: 0.72rem; padding: 3px 6px; border-radius: 6px;">
                                            Paid
                                        </span>
                                    @elseif($order->payment_status === 'partial')
                                        <span class="badge" style="background: rgba(217, 119, 6, 0.1); color: #D97706; font-weight: 700; font-size: 0.72rem; padding: 3px 6px; border-radius: 6px;">
                                            Partial
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.1); color: #DC2626; font-weight: 700; font-size: 0.72rem; padding: 3px 6px; border-radius: 6px;">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;" class="erp-actions-cell">
                                    <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                        <a href="{{ route('admin.transactions.sales-entry.show', $order->id) }}" 
                                           class="btn btn-sm btn-icon" 
                                           style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #5B841E; display: flex; align-items: center; justify-content: center;" 
                                           title="View Order Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Orders Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">Try adjusting your date presets, priority, or search query.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($ordersData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                            <tr>
                                <td colspan="4" style="padding: 0.95rem 1.15rem; color: #1E293B; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.04em;">
                                    Total Booked Orders ({{ $ordersData->total() }} Records):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #5B841E;" class="font-monospace">
                                    {{ number_format($stats['total_volume'] ?? 0, 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #0F172A; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($stats['total_value'] ?? 0, 2) }}
                                </td>
                                <td colspan="3" style="padding: 0.95rem 1.15rem; text-align: center; color: #64748B; font-size: 0.8rem;">
                                    Delivered: <strong class="font-monospace" style="color: #059669;">{{ $stats['completed_count'] ?? 0 }}</strong> &bull; Pending: <strong class="font-monospace" style="color: #CA8A04;">{{ $stats['pending_count'] ?? 0 }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($ordersData->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FFFFFF; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="font-size: 0.82rem; color: #64748B;">
                        Showing {{ $ordersData->firstItem() ?? 0 }} to {{ $ordersData->lastItem() ?? 0 }} of {{ $ordersData->total() }} orders
                    </div>
                    <div>
                        {{ $ordersData->links() }}
                    </div>
                </div>
            @endif

        <!-- MODE 2: PRIORITY & URGENCY ANALYSIS TABLE -->
        @elseif($viewMode === 'priority')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Priority Level</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Total Orders</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Volume</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Value</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Pending</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Dispatched</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Completed</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Fulfillment Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($priorityData as $pRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Priority Level -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    @if($pRow->priority === 'Urgent')
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-weight: 700; font-size: 0.8rem; padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(220, 38, 38, 0.3);">
                                            <i class="fa-solid fa-fire me-1"></i> Urgent Priority
                                        </span>
                                    @elseif($pRow->priority === 'Fast')
                                        <span class="badge" style="background: rgba(217, 119, 6, 0.12); color: #D97706; font-weight: 700; font-size: 0.8rem; padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(217, 119, 6, 0.3);">
                                            <i class="fa-solid fa-bolt me-1"></i> Fast Track
                                        </span>
                                    @elseif($pRow->priority === 'Ready Delivery')
                                        <span class="badge" style="background: rgba(2, 132, 199, 0.12); color: #0284C7; font-weight: 700; font-size: 0.8rem; padding: 5px 10px; border-radius: 6px; border: 1px solid rgba(2, 132, 199, 0.3);">
                                            <i class="fa-solid fa-truck-ramp-box me-1"></i> Ready Delivery
                                        </span>
                                    @else
                                        <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 5px 10px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                            Medium Standard
                                        </span>
                                    @endif
                                </td>

                                <!-- Total Orders -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.84rem; font-weight: 800; padding: 4px 10px; border-radius: 6px;">
                                        {{ $pRow->total_orders }} Orders
                                    </span>
                                </td>

                                <!-- Total Volume -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.95rem; color: #5B841E;">
                                        {{ number_format($pRow->total_volume, 2) }}
                                    </div>
                                </td>

                                <!-- Total Value -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($pRow->total_value, 2) }}
                                    </div>
                                </td>

                                <!-- Pending -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(234, 179, 8, 0.15); color: #B45309; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                        {{ $pRow->pending_count }}
                                    </span>
                                </td>

                                <!-- Dispatched -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                        {{ $pRow->dispatched_count }}
                                    </span>
                                </td>

                                <!-- Completed -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(5, 150, 105, 0.12); color: #059669; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                        {{ $pRow->completed_count }}
                                    </span>
                                </td>

                                <!-- Fulfillment Progress -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                                        <div style="flex: 1; max-width: 90px; height: 6px; background: #E2E8F0; border-radius: 9999px; overflow: hidden;">
                                            <div style="width: {{ $pRow->completion_rate }}%; height: 100%; background: #059669; border-radius: 9999px;"></div>
                                        </div>
                                        <span class="font-monospace" style="font-weight: 700; font-size: 0.8rem; color: #059669;">
                                            {{ $pRow->completion_rate }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <p style="font-size: 0.9rem; color: #64748B; margin: 0;">No priority grouping records available.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- MODE 3: ITEM DEMAND ANALYSIS TABLE -->
        @elseif($viewMode === 'items')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Item Code</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Product Name &amp; Category</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Ordered Qty</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Unit</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Avg Rate</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Demand Value</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Order Frequency</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Primary Purchaser</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsData as $iRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Item Code -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                        {{ $iRow->item_code }}
                                    </span>
                                </td>

                                <!-- Product Name & Category -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                        {{ $iRow->item_name }}
                                    </div>
                                    <div style="margin-top: 2px;">
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.7rem; font-weight: 600; padding: 2px 6px;">
                                            {{ $iRow->category }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Total Ordered Qty -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #5B841E;">
                                        {{ number_format($iRow->total_qty, 2) }}
                                    </div>
                                </td>

                                <!-- Unit -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge" style="background: #EEF2F6; color: #475569; font-weight: 700; font-size: 0.74rem;">
                                        {{ $iRow->unit }}
                                    </span>
                                </td>

                                <!-- Avg Rate -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #1E293B;">
                                        ₹{{ number_format($iRow->avg_rate, 2) }}
                                    </div>
                                </td>

                                <!-- Total Demand Value -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($iRow->total_amount, 2) }}
                                    </div>
                                </td>

                                <!-- Order Frequency -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; font-size: 0.76rem; font-weight: 700; padding: 3px 8px;">
                                        {{ $iRow->orders_count }} Orders
                                    </span>
                                </td>

                                <!-- Primary Purchaser -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #1E293B; font-size: 0.86rem;">
                                        <i class="fa-solid fa-building-user text-primary me-1" style="font-size: 0.75rem;"></i>
                                        {{ $iRow->top_customer }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No items found in active order records.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        <!-- MODE 4: CUSTOMER DEMAND TABLE -->
        @elseif($viewMode === 'customers')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer Code</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer Firm Name</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">City</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Total Orders</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Total Volume</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Order Value</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Delivered</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">In Pipeline</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customersData as $cRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Customer Code -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                        {{ $cRow->customer_code }}
                                    </span>
                                </td>

                                <!-- Customer Firm Name -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.94rem;">
                                        {{ $cRow->customer_name }}
                                    </div>
                                </td>

                                <!-- City -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #334155; font-size: 0.84rem;">
                                        <i class="fa-solid fa-location-dot me-1 text-muted" style="font-size: 0.75rem;"></i>{{ $cRow->city }}
                                    </div>
                                </td>

                                <!-- Total Orders -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 8px;">
                                        {{ $cRow->orders_count }} Orders
                                    </span>
                                </td>

                                <!-- Total Volume -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.95rem; color: #5B841E;">
                                        {{ number_format($cRow->total_qty, 2) }}
                                    </div>
                                </td>

                                <!-- Order Value -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($cRow->total_value, 2) }}
                                    </div>
                                </td>

                                <!-- Delivered -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(5, 150, 105, 0.12); color: #059669; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                        {{ $cRow->completed_count }}
                                    </span>
                                </td>

                                <!-- In Pipeline -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                    <span class="badge font-monospace" style="background: rgba(234, 179, 8, 0.15); color: #B45309; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                        {{ $cRow->pending_count }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No customer order volume records available.</p>
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
    #view-rpt-order, #view-rpt-order * {
        visibility: visible;
    }
    #view-rpt-order {
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
