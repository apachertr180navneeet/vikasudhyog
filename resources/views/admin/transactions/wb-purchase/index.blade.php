@extends('admin.layouts.app')

@section('title', 'WB Purchase Entry (Without Bill) - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<section class="view-section active" id="view-txn-wb-purchase">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">WB Purchase Entry</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-scale-unbalanced text-primary"></i> WB Purchase Entry (Without Bill)
            </h1>
            <p class="erp-page-subtitle">
                Manage Weighbridge (WB) Mandi Cash Purchases, direct farmer arrivals, and gross-to-net tare weight deductions.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print WB Inward Register">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.transactions.wb-purchase-entry.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> New WB Purchase Entry
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
                <div class="erp-kpi-label">Total WB Slips</div>
                <div class="erp-kpi-val">{{ number_format($totalSlips) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Net Weight</div>
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
                <div class="erp-kpi-label">Total Procurement Value</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($totalAmount, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid {{ ($totalPendingAmount ?? 0) > 0 ? '#DC2626' : '#059669' }};">
            <div class="erp-kpi-icon-box" style="background: {{ ($totalPendingAmount ?? 0) > 0 ? 'rgba(220, 38, 38, 0.12)' : 'rgba(5, 150, 105, 0.12)' }}; color: {{ ($totalPendingAmount ?? 0) > 0 ? '#DC2626' : '#059669' }};">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Pending Collection</div>
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
        <!-- Filter and Search Header -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.transactions.wb-purchase-entry') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by slip no, vehicle, driver, supplier..." class="form-control erp-search-input">
                </div>

                <select name="vendor_id" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Suppliers / Farmers</option>
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
                    <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $wbPurchases->count() }}</strong> of <strong>{{ $wbPurchases->total() }}</strong> WB slips
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">WB Slip Identity</th>
                        <th>Date</th>
                        <th>Vendor / Supplier</th>
                        <th>Vehicle No</th>
                        <th style="text-align: right;">Net Weight</th>
                        <th style="text-align: right;">Total Value</th>
                        <th style="text-align: right;">Paid (₹)</th>
                        <th style="text-align: right;">Pending (₹)</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wbPurchases as $wb)
                        @php
                            $wbTotal = (float)$wb->total_amount;
                            $wbPaid = (float)($wb->paid_amount ?? 0);
                            $wbPending = max(0, $wbTotal - $wbPaid);
                        @endphp
                        <tr>
                            <!-- WB Slip Identity -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ strtoupper(substr($wb->vendor->name ?? 'W', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.transactions.wb-purchase-entry.show', $wb) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link font-monospace">
                                            {{ $wb->slip_no }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem; flex-wrap: wrap;">
                                            <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #FDE68A;">
                                                Without Bill
                                            </span>
                                            @if($wb->invoice_no)
                                                <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                    <i class="fa-solid fa-receipt me-1"></i>{{ $wb->invoice_no }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div style="font-weight: 500; color: #334155;">
                                    {{ $wb->entry_date->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.74rem; color: #64748B;">
                                    {{ $wb->entry_date->diffForHumans() }}
                                </div>
                            </td>

                            <!-- Vendor / Supplier -->
                            <td>
                                <div style="font-weight: 600; color: #1E293B;">
                                    {{ $wb->vendor->name ?? 'Supplier' }}
                                </div>
                                @if($wb->broker)
                                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.1rem;">
                                        <i class="fa-solid fa-handshake" style="color: #5B841E;"></i> {{ $wb->broker->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- Vehicle & Terms -->
                            <td>
                                <div class="font-monospace" style="font-weight: 600; color: #334155;">
                                    {{ $wb->vehicle_no ?: 'Direct / Trolley' }}
                                </div>
                                <div style="font-size: 0.74rem; color: #64748B;">
                                    {{ $wb->payment_terms ?? '30 Days' }}
                                </div>
                            </td>

                            <!-- Net Weight -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #1E293B; font-size: 0.95rem;">
                                    {{ number_format($wb->items->sum('quantity') ?: $wb->net_weight, 2) }} <span style="font-size: 0.75rem; color: #64748B; font-weight: normal;">KG</span>
                                </div>
                            </td>

                            <!-- Total Value -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #059669; font-size: 0.95rem;">
                                    ₹{{ number_format($wbTotal, 2) }}
                                </div>
                            </td>

                            <!-- Paid (₹) -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #2563EB; font-size: 0.9rem;">
                                    ₹{{ number_format($wbPaid, 2) }}
                                </div>
                            </td>

                            <!-- Pending (₹) -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: {{ $wbPending > 0 ? '#DC2626' : '#059669' }}; font-size: 0.9rem;">
                                    ₹{{ number_format($wbPending, 2) }}
                                </div>
                            </td>

                            <!-- Status Button -->
                            <td style="text-align: center;">
                                @if($wb->status === 'completed')
                                    <span class="erp-status-btn erp-status-btn-active" style="cursor: default; opacity: 0.95; user-select: none;" title="Slip Completed (Locked - Status cannot be changed)">
                                        <i class="fa-solid fa-lock me-1" style="font-size: 0.68rem;"></i> Completed
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.transactions.wb-purchase-entry.toggle-status', $wb) }}" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Complete Slip (Current: Received)">
                                            <span class="erp-status-dot-green"></span> Received
                                        </button>
                                    </form>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.transactions.wb-purchase-entry.show', $wb) }}" class="erp-table-action-icon" title="View WB Slip 360">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.wb-purchase-entry.edit', $wb) }}" class="erp-table-action-icon" title="Edit WB Slip">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteWBPurchase({{ $wb->id }}, '{{ $wb->slip_no }}')" title="Delete WB Slip">
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
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No WB Purchase Slips Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 420px; margin-bottom: 1.25rem;">
                                        Record your first weighbridge Mandi cash purchase or direct farmer arrival without bill.
                                    </p>
                                    @if(request('search') || request('vendor_id') || request('status'))
                                        <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.transactions.wb-purchase-entry.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Record First WB Slip
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
        @if($wbPurchases->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $wbPurchases->firstItem() }}</strong> to <strong>{{ $wbPurchases->lastItem() }}</strong> of <strong>{{ $wbPurchases->total() }}</strong> entries
                </div>
                <div>
                    {{ $wbPurchases->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete WB Slip Hidden Form -->
<form id="delete-wb-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteWBPurchase(slipId, slipNo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete WB Slip?',
                html: `Are you sure you want to delete weighbridge slip <strong>#${slipNo}</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">Reverting inward stock and adjusting supplier balance will take effect.</span>`,
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
                    const form = document.getElementById('delete-wb-form');
                    form.action = `{{ url('admin/transactions/wb-purchase-entry') }}/${slipId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to delete WB slip #${slipNo}? Inward stock will be reverted.`)) {
                const form = document.getElementById('delete-wb-form');
                form.action = `{{ url('admin/transactions/wb-purchase-entry') }}/${slipId}`;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
