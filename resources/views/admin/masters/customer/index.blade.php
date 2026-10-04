@extends('admin.layouts.app')

@section('title', 'Customer Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-customer')

@section('content')
<section class="view-section active" id="view-master-customer">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Customer Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-users text-primary"></i> Customer Master
            </h1>
            <p class="erp-page-subtitle">
                Client directory, distributors, wholesalers, retailers, credit limits &amp; outstanding receivables ledger.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Customer Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.customer.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Customer
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Customers</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Clients</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Receivables</div>
                <div class="erp-kpi-val font-monospace">₹{{ number_format($stats['total_receivables'] ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Credit Pool</div>
                <div class="erp-kpi-val font-monospace">₹{{ number_format($stats['total_credit_pool'] ?? 0, 2) }}</div>
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
            <form action="{{ route('admin.masters.customer') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search customer name, code, contact..." class="form-control erp-search-input">
                </div>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="type" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' }}>All Client Types</option>
                    @foreach($types as $typeOption)
                        <option value="{{ $typeOption }}" {{ ($filters['type'] ?? '') === $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                    @endforeach
                </select>

                @if(!empty($cities) && $cities->count() > 0)
                    <select name="city" class="form-control erp-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($filters['city'] ?? 'all') === 'all' ? 'selected' : '' }}>All Cities</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ ($filters['city'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filters">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['type'] ?? 'all') !== 'all' || ($filters['city'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.customer') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $customers->count() }}</strong> of <strong>{{ $customers->total() }}</strong> customers
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Customer Identity</th>
                        <th>Client Type</th>
                        <th>Contact Person &amp; Phone</th>
                        <th>GSTIN / PAN</th>
                        <th>City &amp; State</th>
                        <th style="text-align: right;">Credit Limit</th>
                        <th style="text-align: right;">Outstanding</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <!-- Customer Identity -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $customer->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.customer.show', $customer->id) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link">
                                            {{ $customer->name }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem;">
                                            <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                {{ $customer->code }}
                                            </span>
                                            @if($customer->company)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    <i class="fa-solid fa-building" style="font-size: 0.65rem;"></i> {{ $customer->company->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Client Type -->
                            <td>
                                <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                    {{ $customer->customer_type }}
                                </span>
                            </td>

                            <!-- Contact Person & Phone -->
                            <td>
                                <div style="font-weight: 500; color: #334155;">
                                    {{ $customer->contact_person ?: '—' }}
                                </div>
                                @if($customer->phone)
                                    <div style="font-size: 0.78rem; color: #64748B; margin-top: 0.1rem;">
                                        <a href="tel:{{ $customer->phone }}" style="color: inherit; text-decoration: none;">
                                            <i class="fa-solid fa-phone" style="font-size: 0.7rem; color: #5B841E;"></i> {{ $customer->phone }}
                                        </a>
                                    </div>
                                @endif
                            </td>

                            <!-- GSTIN / PAN -->
                            <td>
                                @if($customer->gstin)
                                    <div class="font-monospace" style="font-size: 0.78rem; color: #1E293B; font-weight: 500;">
                                        {{ $customer->gstin }}
                                    </div>
                                @endif
                                @if($customer->pan)
                                    <div class="font-monospace" style="font-size: 0.72rem; color: #64748B; margin-top: 0.1rem;">
                                        PAN: {{ $customer->pan }}
                                    </div>
                                @elseif(!$customer->gstin)
                                    <span style="color: #94A3B8; font-size: 0.8rem;">—</span>
                                @endif
                            </td>

                            <!-- City & State -->
                            <td>
                                <div style="font-weight: 500; color: #334155;">
                                    {{ $customer->city ?: '—' }}
                                </div>
                                <div style="font-size: 0.74rem; color: #64748B;">
                                    {{ $customer->state ?: 'Rajasthan' }}
                                </div>
                            </td>

                            <!-- Credit Limit -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #334155;">
                                    ₹{{ number_format($customer->credit_limit, 2) }}
                                </div>
                                <div style="font-size: 0.72rem; color: #64748B;">
                                    {{ $customer->payment_terms ?: '30 Days' }}
                                </div>
                            </td>

                            <!-- Current Outstanding Balance -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: {{ $customer->current_balance > 0 ? '#B91C1C' : '#059669' }};">
                                    ₹{{ number_format($customer->current_balance, 2) }}
                                </div>
                                <span style="font-size: 0.7rem; color: {{ $customer->current_balance > 0 ? '#DC2626' : '#64748B' }};">
                                    {{ $customer->current_balance > 0 ? 'Receivable' : 'Settled' }}
                                </span>
                            </td>

                            <!-- Status Toggle Button -->
                            <td style="text-align: center;">
                                <form action="{{ route('admin.masters.customer.toggle-status', $customer->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($customer->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Customer">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Customer">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.masters.customer.show', $customer->id) }}" class="erp-table-action-icon" title="View Profile">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.customer.edit', $customer->id) }}" class="erp-table-action-icon" title="Edit Customer">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->code }}')" title="Archive Customer">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3.5rem 1.5rem;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1.6rem; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-users-slash"></i>
                                    </div>
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No Customers Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 380px; margin-bottom: 1.25rem;">
                                        No customer records match your filter criteria or search query.
                                    </p>
                                    @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['type'] ?? 'all') !== 'all' || ($filters['city'] ?? 'all') !== 'all')
                                        <a href="{{ route('admin.masters.customer') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.masters.customer.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Add First Customer
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
        @if($customers->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $customers->firstItem() }}</strong> to <strong>{{ $customers->lastItem() }}</strong> of <strong>{{ $customers->total() }}</strong> entries
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Customer Hidden Form -->
<form id="delete-customer-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteCustomer(customerId, customerName, customerCode) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Customer Account?',
                html: `Are you sure you want to archive <strong>${customerName} (${customerCode})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the record from active customer registries.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Archive',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-customer-form');
                    form.action = `{{ url('admin/masters/customer') }}/${customerId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive ${customerName} (${customerCode})?`)) {
                const form = document.getElementById('delete-customer-form');
                form.action = `{{ url('admin/masters/customer') }}/${customerId}`;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
