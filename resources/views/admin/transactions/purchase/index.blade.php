@extends('admin.layouts.app')

@section('title', 'Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<section class="view-section active" id="view-txn-purchase">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Purchase Entry</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i> Purchase Entry
            </h1>
            <p class="erp-page-subtitle">
                Record raw materials, herbs, and packaging purchases with dual-rate bill and under-billing calculations.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Purchase List">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> New Purchase Entry
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Purchases</div>
                <div class="erp-kpi-val">{{ number_format($totalPurchases) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Received Batches</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ number_format($completedCount) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Official Billing Total</div>
                <div class="erp-kpi-val font-monospace">₹{{ number_format($totalBillAmount, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #D97706;">
            <div class="erp-kpi-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Grand Total Procured</div>
                <div class="erp-kpi-val font-monospace" style="color: #0F172A;">₹{{ number_format($totalGrandAmount, 2) }}</div>
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
            <form action="{{ route('admin.transactions.purchase-entry') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by voucher no, invoice, vendor, vehicle..." class="form-control erp-search-input">
                </div>

                <select name="vendor_id" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $vnd)
                        <option value="{{ $vnd->id }}" {{ request('vendor_id') == $vnd->id ? 'selected' : '' }}>{{ $vnd->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filters">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(request('search') || request('vendor_id') || request('status'))
                    <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $purchases->count() }}</strong> of <strong>{{ $purchases->total() }}</strong> vouchers
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Voucher Identity</th>
                        <th>Date</th>
                        <th>Vendor Firm &amp; Broker</th>
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
                    @forelse($purchases as $pur)
                        <tr>
                            <!-- Voucher Identity -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ strtoupper(substr($pur->vendor->name ?? 'V', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.transactions.purchase-entry.show', $pur) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link font-monospace">
                                            {{ $pur->purchase_no }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem;">
                                            @if($pur->invoice_no)
                                                <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                    <i class="fa-solid fa-receipt me-1"></i>{{ $pur->invoice_no }}
                                                </span>
                                            @endif
                                            @if($pur->vehicle_no)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    <i class="fa-solid fa-truck" style="font-size: 0.65rem;"></i> {{ $pur->vehicle_no }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div style="font-weight: 500; color: #334155;">
                                    {{ $pur->invoice_date->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.74rem; color: #64748B;">
                                    {{ $pur->invoice_date->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Vendor & Broker -->
                            <td>
                                <div style="font-weight: 600; color: #1E293B;">
                                    {{ $pur->vendor->name ?? 'Direct Vendor' }}
                                </div>
                                @if($pur->broker)
                                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.1rem;">
                                        <i class="fa-solid fa-handshake" style="color: #5B841E;"></i> {{ $pur->broker->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Order Type -->
                            <td>
                                <span class="badge" style="background: {{ $pur->order_type === 'Urgent' ? 'rgba(239, 68, 68, 0.1)' : ($pur->order_type === 'Fast' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(91, 132, 30, 0.1)') }}; color: {{ $pur->order_type === 'Urgent' ? '#EF4444' : ($pur->order_type === 'Fast' ? '#D97706' : '#5B841E') }}; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid {{ $pur->order_type === 'Urgent' ? 'rgba(239, 68, 68, 0.2)' : ($pur->order_type === 'Fast' ? 'rgba(245, 158, 11, 0.2)' : 'rgba(91, 132, 30, 0.2)') }};">
                                    {{ $pur->order_type }}
                                </span>
                            </td>

                            <!-- Bill Subtotal -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #334155;">
                                    ₹{{ number_format($pur->subtotal, 2) }}
                                </div>
                            </td>

                            <!-- GST Tax -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-size: 0.85rem; color: #64748B;">
                                    ₹{{ number_format($pur->tax_amount, 2) }}
                                </div>
                            </td>

                            <!-- Under Billing Amount -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #D97706;">
                                    ₹{{ number_format($pur->under_billing_total, 2) }}
                                </div>
                            </td>

                            <!-- Grand Total -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #059669; font-size: 0.95rem;">
                                    ₹{{ number_format($pur->grand_total, 2) }}
                                </div>
                            </td>

                            <!-- Status Button -->
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('admin.transactions.purchase-entry.toggle-status', $pur) }}" style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if(in_array($pur->status, ['received', 'completed']))
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Change Status (Current: {{ ucfirst($pur->status) }})">
                                            <span class="erp-status-dot-green"></span> {{ ucfirst($pur->status) }}
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Change Status (Current: {{ ucfirst($pur->status) }})">
                                            <span class="erp-status-dot-red"></span> {{ ucfirst($pur->status) }}
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.transactions.purchase-entry.show', $pur) }}" class="erp-table-action-icon" title="View Voucher 360">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.purchase-entry.edit', $pur) }}" class="erp-table-action-icon" title="Edit Purchase Voucher">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeletePurchase({{ $pur->id }}, '{{ $pur->purchase_no }}')" title="Delete Purchase Voucher">
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
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No Purchase Entries Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 420px; margin-bottom: 1.25rem;">
                                        Record your first raw material, herb or packaging consignment to update inventory stock inward and supplier ledgers.
                                    </p>
                                    @if(request('search') || request('vendor_id') || request('status'))
                                        <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Record First Purchase
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
        @if($purchases->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $purchases->firstItem() }}</strong> to <strong>{{ $purchases->lastItem() }}</strong> of <strong>{{ $purchases->total() }}</strong> entries
                </div>
                <div>
                    {{ $purchases->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Purchase Hidden Form -->
<form id="delete-purchase-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeletePurchase(purchaseId, purchaseNo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Purchase Voucher?',
                html: `Are you sure you want to delete purchase voucher <strong>#${purchaseNo}</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">Reverting inward stock and adjusting supplier balance will take effect.</span>`,
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
                    const form = document.getElementById('delete-purchase-form');
                    form.action = `{{ url('admin/transactions/purchase-entry') }}/${purchaseId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to delete purchase voucher #${purchaseNo}? Inward stock will be reverted.`)) {
                const form = document.getElementById('delete-purchase-form');
                form.action = `{{ url('admin/transactions/purchase-entry') }}/${purchaseId}`;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
