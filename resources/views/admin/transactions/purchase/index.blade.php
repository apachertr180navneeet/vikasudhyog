@extends('admin.layouts.app')

@section('title', 'Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<div class="erp-module-wrapper">
    <!-- Top Bar & Breadcrumb -->
    <div class="erp-header-toolbar">
        <div class="erp-breadcrumb-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="#">Transactions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Purchase Entry</li>
                </ol>
            </nav>
            <h1 class="erp-page-title">Purchase Entry</h1>
            <p class="erp-page-desc">Record raw materials, herbs, and packaging purchases with dual-rate bill and under-billing calculations</p>
        </div>
        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="erp-btn-primary">
                <i class="fa-solid fa-plus"></i> New Purchase Entry
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Ribbon -->
    <div class="erp-kpi-grid">
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(91, 132, 30, 0.12); color: #5B841E;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Total Purchases</span>
                <h3 class="erp-kpi-value font-monospace">{{ number_format($totalPurchases) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-layer-group"></i> Recorded Vouchers</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Received Batches</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #059669;">{{ number_format($completedCount) }}</h3>
                <span class="erp-kpi-meta text-success"><i class="fa-solid fa-circle-check"></i> In Stock & Verified</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Official Billing Total</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #2563EB;">₹{{ number_format($totalBillAmount, 2) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-scale-balanced"></i> Tax Invoiced Amount</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Grand Total Procured</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #0F172A;">₹{{ number_format($totalGrandAmount, 2) }}</h3>
                <span class="erp-kpi-meta" style="color: #D97706;"><i class="fa-solid fa-coins"></i> Incl. ₹{{ number_format($totalUBAmount, 2) }} Under-Bill</span>
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
            <form method="GET" action="{{ route('admin.transactions.purchase-entry') }}" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" class="form-control erp-search-input" placeholder="Search by voucher no, invoice, vendor, vehicle..." value="{{ request('search') }}">
                </div>

                <div class="erp-select-wrap">
                    <select name="vendor_id" class="form-select erp-filter-select" onchange="this.form.submit()">
                        <option value="">All Vendors</option>
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
                    <a href="{{ route('admin.transactions.purchase-entry') }}" class="erp-btn-reset" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $purchases->firstItem() ?? 0 }}-{{ $purchases->lastItem() ?? 0 }}</strong> of <strong>{{ $purchases->total() }}</strong> vouchers
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="table erp-data-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">Voucher No</th>
                        <th style="width: 110px;">Date</th>
                        <th style="min-width: 220px;">Vendor Firm & Broker</th>
                        <th style="width: 110px;">Order Type</th>
                        <th style="width: 130px; text-align: right;">Bill Subtotal</th>
                        <th style="width: 110px; text-align: right;">GST Tax</th>
                        <th style="width: 130px; text-align: right;">Under Billing</th>
                        <th style="width: 140px; text-align: right;">Grand Total</th>
                        <th style="width: 110px; text-align: center;">Status</th>
                        <th style="width: 110px; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $pur)
                        <tr>
                            <!-- Voucher No & Bill -->
                            <td>
                                <a href="{{ route('admin.transactions.purchase-entry.show', $pur) }}" class="erp-table-code-link font-monospace font-weight-bold">
                                    {{ $pur->purchase_no }}
                                </a>
                                @if($pur->invoice_no)
                                    <div class="erp-table-subtext font-monospace"><i class="fa-solid fa-receipt me-1"></i>{{ $pur->invoice_no }}</div>
                                @endif
                            </td>

                            <!-- Invoice Date -->
                            <td>
                                <span class="text-dark font-weight-500">{{ $pur->invoice_date->format('d M Y') }}</span>
                                <div class="erp-table-subtext">{{ $pur->invoice_date->diffForHumans() }}</div>
                            </td>

                            <!-- Vendor & Broker -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="erp-avatar-initials me-2" style="background: linear-gradient(135deg, #5B841E, #3D5A12); width: 36px; height: 36px; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 700; flex-shrink: 0;">
                                        {{ strtoupper(substr($pur->vendor->name ?? 'V', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="erp-table-title font-weight-600 text-dark">{{ $pur->vendor->name ?? 'Direct Vendor' }}</div>
                                        @if($pur->broker)
                                            <div class="erp-table-subtext text-muted">
                                                <i class="fa-solid fa-handshake me-1" style="color: #64748B;"></i> {{ $pur->broker->name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Order Type -->
                            <td>
                                <span class="badge {{ $pur->order_type === 'Urgent' ? 'bg-danger' : ($pur->order_type === 'Fast' ? 'bg-warning text-dark' : 'bg-light text-dark border') }} font-weight-600 px-2 py-1" style="font-size: 0.75rem;">
                                    {{ $pur->order_type }}
                                </span>
                            </td>

                            <!-- Bill Subtotal -->
                            <td style="text-align: right;">
                                <span class="font-monospace text-dark font-weight-600">₹{{ number_format($pur->subtotal, 2) }}</span>
                            </td>

                            <!-- GST Tax -->
                            <td style="text-align: right;">
                                <span class="font-monospace text-muted">₹{{ number_format($pur->tax_amount, 2) }}</span>
                            </td>

                            <!-- Under Billing Amount -->
                            <td style="text-align: right;">
                                <span class="font-monospace font-weight-600" style="color: #D97706;">
                                    ₹{{ number_format($pur->under_billing_total, 2) }}
                                </span>
                            </td>

                            <!-- Grand Total -->
                            <td style="text-align: right;">
                                <strong class="font-monospace text-dark" style="font-size: 0.95rem;">
                                    ₹{{ number_format($pur->grand_total, 2) }}
                                </strong>
                            </td>

                            <!-- Status Button -->
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('admin.transactions.purchase-entry.toggle-status', $pur) }}" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="erp-status-toggle-btn {{ $pur->status === 'completed' ? 'erp-status-btn-active' : 'erp-status-btn-inactive' }}" title="Click to toggle status">
                                        <i class="fa-solid {{ $pur->status === 'completed' ? 'fa-circle-check' : 'fa-clock' }}"></i>
                                        <span>{{ ucfirst($pur->status) }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: center;">
                                <div class="erp-action-group">
                                    <a href="{{ route('admin.transactions.purchase-entry.show', $pur) }}" class="erp-action-btn erp-action-view" title="View Voucher 360">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.purchase-entry.edit', $pur) }}" class="erp-action-btn erp-action-edit" title="Edit Purchase Voucher">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.transactions.purchase-entry.destroy', $pur) }}" onsubmit="return confirm('Are you sure you want to delete purchase voucher #{{ $pur->purchase_no }}? Reverting stock will take effect.')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="erp-action-btn erp-action-delete" title="Delete Purchase Voucher">
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
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </div>
                                    <h5 class="text-dark font-weight-600">No Purchase Entries Found</h5>
                                    <p class="text-muted mb-3">Record your first raw material or herbs purchase entry to update inventory stock</p>
                                    <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="erp-btn-primary">
                                        <i class="fa-solid fa-plus me-1"></i> New Purchase Entry
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($purchases->hasPages())
            <div class="erp-table-pagination-footer p-3 border-top d-flex justify-content-between align-items-center">
                <div class="text-muted font-size-sm">
                    Showing {{ $purchases->firstItem() }} to {{ $purchases->lastItem() }} of {{ $purchases->total() }} entries
                </div>
                <div>
                    {{ $purchases->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
