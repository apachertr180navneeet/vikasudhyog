@extends('admin.layouts.app')

@section('title', 'WB Purchase Entry (Without Bill) - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<div class="erp-module-wrapper">
    <!-- Top Bar & Breadcrumb -->
    <div class="erp-header-toolbar">
        <div class="erp-breadcrumb-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Transactions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">WB Purchase Entry</li>
                </ol>
            </nav>
            <h1 class="erp-page-title">WB Purchase Entry (Without Bill)</h1>
            <p class="erp-page-desc">Manage Weighbridge (WB) Mandi Cash Purchases, direct farmer arrivals, and gross-to-net tare weight deductions</p>
        </div>
        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.wb-purchase-entry.create') }}" class="erp-btn-primary">
                <i class="fa-solid fa-plus"></i> New WB Purchase Entry
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Ribbon -->
    <div class="erp-kpi-grid">
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(91, 132, 30, 0.12); color: #5B841E;">
                <i class="fa-solid fa-scale-unbalanced"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Total WB Slips</span>
                <h3 class="erp-kpi-value font-monospace">{{ number_format($totalSlips) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-truck-ramp-box"></i> Mandi Receipts</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Received Consignments</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #059669;">{{ number_format($completedCount) }}</h3>
                <span class="erp-kpi-meta text-success"><i class="fa-solid fa-warehouse"></i> Stock Unloaded</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Total Net Weight</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #2563EB;">{{ number_format($totalNetWeight, 2) }} <span style="font-size: 0.9rem; font-weight: 500;">KG</span></h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-truck-moving"></i> Net Weighed Herbal Raw Material</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Total Mandi Cash Amount</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #D97706;">₹{{ number_format($totalAmount, 2) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-coins"></i> Total Unbilled Procurement</span>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success erp-alert alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger erp-alert alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card & Data Table -->
    <div class="card erp-main-card">
        <!-- Filter Header -->
        <div class="erp-table-filter-header">
            <form method="GET" action="{{ route('admin.transactions.wb-purchase-entry') }}" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" class="form-control erp-search-input" placeholder="Search by slip no, vehicle, driver, farmer/vendor..." value="{{ request('search') }}">
                </div>

                <div class="erp-select-wrap">
                    <select name="vendor_id" class="form-select erp-filter-select" onchange="this.form.submit()">
                        <option value="">All Suppliers</option>
                        @foreach($vendors as $vnd)
                            <option value="{{ $vnd->id }}" {{ request('vendor_id') == $vnd->id ? 'selected' : '' }}>{{ $vnd->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="erp-select-wrap">
                    <select name="status" class="form-select erp-filter-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                @if(request('search') || request('vendor_id') || request('status'))
                    <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="erp-btn-reset" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $wbPurchases->firstItem() ?? 0 }}-{{ $wbPurchases->lastItem() ?? 0 }}</strong> of <strong>{{ $wbPurchases->total() }}</strong> WB slips
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="table erp-data-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">WB Slip No</th>
                        <th style="width: 110px;">Date</th>
                        <th style="min-width: 220px;">Vendor / Supplier</th>
                        <th style="width: 130px;">Vehicle & Driver</th>
                        <th style="width: 110px; text-align: right;">Gross (KG)</th>
                        <th style="width: 110px; text-align: right;">Tare (KG)</th>
                        <th style="width: 130px; text-align: right;">Net Weight</th>
                        <th style="width: 140px; text-align: right;">WB Amount</th>
                        <th style="width: 110px; text-align: center;">Status</th>
                        <th style="width: 110px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wbPurchases as $wb)
                        <tr>
                            <!-- Slip No -->
                            <td>
                                <a href="{{ route('admin.transactions.wb-purchase-entry.show', $wb) }}" class="erp-table-code-link font-monospace font-weight-bold">
                                    {{ $wb->slip_no }}
                                </a>
                                <div class="erp-table-subtext font-monospace">{{ $wb->payment_mode }}</div>
                            </td>

                            <!-- Date -->
                            <td>
                                <span class="text-dark font-weight-500">{{ $wb->entry_date->format('d M Y') }}</span>
                                <div class="erp-table-subtext">{{ $wb->entry_date->diffForHumans() }}</div>
                            </td>

                            <!-- Vendor / Farmer -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="erp-avatar-initials me-2" style="background: linear-gradient(135deg, #5B841E, #3D5A12); width: 36px; height: 36px; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 700; flex-shrink: 0;">
                                        {{ strtoupper(substr($wb->vendor->name ?? 'V', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="erp-table-title font-weight-600 text-dark">{{ $wb->vendor->name ?? 'Direct Farmer' }}</div>
                                        @if($wb->broker)
                                            <div class="erp-table-subtext text-muted">
                                                <i class="fa-solid fa-handshake me-1" style="color: #64748B;"></i> {{ $wb->broker->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Vehicle & Driver -->
                            <td>
                                <div class="font-monospace text-dark font-weight-600 font-size-sm">
                                    {{ $wb->vehicle_no ? $wb->vehicle_no : 'Trolley / Direct' }}
                                </div>
                                @if($wb->driver_name)
                                    <div class="erp-table-subtext text-muted">
                                        <i class="fa-solid fa-user me-1"></i> {{ $wb->driver_name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Gross Weight -->
                            <td style="text-align: right;">
                                <span class="font-monospace text-muted">{{ number_format($wb->gross_weight, 2) }}</span>
                            </td>

                            <!-- Tare Weight -->
                            <td style="text-align: right;">
                                <span class="font-monospace text-muted">{{ number_format($wb->tare_weight, 2) }}</span>
                            </td>

                            <!-- Net Weight -->
                            <td style="text-align: right;">
                                <strong class="font-monospace text-dark font-size-base">
                                    {{ number_format($wb->net_weight, 2) }} <span class="text-muted font-size-xs">KG</span>
                                </strong>
                            </td>

                            <!-- Total Amount -->
                            <td style="text-align: right;">
                                <strong class="font-monospace font-size-base" style="color: #D97706;">
                                    ₹{{ number_format($wb->total_amount, 2) }}
                                </strong>
                            </td>

                            <!-- Status Button -->
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('admin.transactions.wb-purchase-entry.toggle-status', $wb) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="erp-status-toggle-btn {{ $wb->status === 'completed' ? 'erp-status-btn-active' : 'erp-status-btn-inactive' }}" title="Click to toggle status">
                                        <i class="fa-solid {{ $wb->status === 'completed' ? 'fa-circle-check' : 'fa-clock' }}"></i>
                                        <span>{{ ucfirst($wb->status) }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: center;">
                                <div class="erp-action-group">
                                    <a href="{{ route('admin.transactions.wb-purchase-entry.show', $wb) }}" class="erp-action-btn erp-action-view" title="View WB Slip 360">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.wb-purchase-entry.edit', $wb) }}" class="erp-action-btn erp-action-edit" title="Edit WB Slip">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.transactions.wb-purchase-entry.destroy', $wb) }}" onsubmit="return confirm('Are you sure you want to delete WB slip #{{ $wb->slip_no }}? Reverting stock will take effect.')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="erp-action-btn erp-action-delete" title="Delete WB Slip">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="erp-empty-state">
                                    <div class="erp-empty-icon mb-3" style="font-size: 2.5rem; color: #94A3B8;">
                                        <i class="fa-solid fa-scale-unbalanced"></i>
                                    </div>
                                    <h5 class="text-dark font-weight-600">No WB Purchase Slips Found</h5>
                                    <p class="text-muted mb-3">Record your first weighbridge Mandi receipt to track gross/tare stock weight</p>
                                    <a href="{{ route('admin.transactions.wb-purchase-entry.create') }}" class="erp-btn-primary">
                                        <i class="fa-solid fa-plus me-1"></i> New WB Purchase Entry
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($wbPurchases->hasPages())
            <div class="erp-table-pagination-footer p-3 border-top d-flex justify-content-between align-items-center">
                <div class="text-muted font-size-sm">
                    Showing {{ $wbPurchases->firstItem() }} to {{ $wbPurchases->lastItem() }} of {{ $wbPurchases->total() }} entries
                </div>
                <div>
                    {{ $wbPurchases->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
