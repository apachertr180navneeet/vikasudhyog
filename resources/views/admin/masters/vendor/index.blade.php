@extends('admin.layouts.app')

@section('title', 'Vendor Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-vendor')

@section('content')
<section class="view-section active" id="view-master-vendor">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Vendor Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-field text-primary"></i> Vendor Master
            </h1>
            <p class="erp-page-subtitle">
                Supplier accounts, raw material suppliers, packaging vendors &amp; procurement ledgers.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Vendor Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.vendor.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Vendor
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-truck-field"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Suppliers</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Suppliers</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Outstanding Payable</div>
                <div class="erp-kpi-val">₹{{ number_format($stats['total_payable'] ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Sourcing Cities</div>
                <div class="erp-kpi-val">{{ $stats['cities_count'] ?? 0 }} Hubs</div>
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

    <!-- Main Table Card -->
    <div class="card erp-main-card">
        <!-- Filter and Search Header -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.masters.vendor') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search vendor firm, code, GSTIN, phone, city..." class="form-control erp-search-input">
                </div>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                @if(!empty($cities) && $cities->count() > 0)
                    <select name="city" class="form-control erp-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($filters['city'] ?? 'all') === 'all' ? 'selected' : '' }}>All Cities</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ ($filters['city'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-outline erp-btn-filter">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['city'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.vendor') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $vendors->count() }}</strong> of <strong>{{ $vendors->total() }}</strong> vendors
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">#</th>
                        <th>Vendor Firm / Code</th>
                        <th>Contact Person</th>
                        <th>Phone &amp; Email</th>
                        <th>GSTIN &amp; PAN</th>
                        <th>City / State</th>
                        <th>Terms &amp; Payable</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $index => $vendor)
                        <tr class="{{ $vendor->status === 'inactive' ? 'erp-row-dimmed' : '' }}">
                            <td class="text-muted" style="font-size: 0.82rem;">
                                {{ $vendors->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="erp-user-avatar-circle" style="background: rgba(91, 132, 30, 0.12); color: var(--primary);">
                                        {{ $vendor->initials }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('admin.masters.vendor.show', $vendor->id) }}" class="fw-bold text-dark text-decoration-none hover-primary" style="font-size: 0.92rem;">
                                                {{ $vendor->name }}
                                            </a>
                                            <span class="badge" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; font-family: var(--font-mono); font-weight: 600;">
                                                {{ $vendor->code }}
                                            </span>
                                        </div>
                                        @if($vendor->company)
                                            <div style="font-size: 0.76rem; color: var(--text-muted);">
                                                <i class="fa-solid fa-building me-1" style="font-size: 0.7rem;"></i>{{ $vendor->company->name }}
                                            </div>
                                        @else
                                            <div style="font-size: 0.76rem; color: var(--text-muted);">
                                                <i class="fa-solid fa-globe me-1" style="font-size: 0.7rem;"></i>Global Supplier
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($vendor->contact_person)
                                    <div class="fw-semibold text-dark" style="font-size: 0.88rem;">
                                        {{ $vendor->contact_person }}
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($vendor->phone)
                                    <div style="font-size: 0.85rem; font-family: var(--font-mono);">
                                        <a href="tel:{{ $vendor->phone }}" class="text-dark text-decoration-none">
                                            <i class="fa-solid fa-phone text-muted me-1" style="font-size: 0.75rem;"></i>{{ $vendor->phone }}
                                        </a>
                                    </div>
                                @endif
                                @if($vendor->email)
                                    <div style="font-size: 0.78rem; color: var(--text-muted);">
                                        <a href="mailto:{{ $vendor->email }}" class="text-muted text-decoration-none">
                                            <i class="fa-regular fa-envelope me-1" style="font-size: 0.72rem;"></i>{{ $vendor->email }}
                                        </a>
                                    </div>
                                @endif
                                @if(!$vendor->phone && !$vendor->email)
                                    <span class="text-muted" style="font-size: 0.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($vendor->gstin)
                                    <div style="font-family: var(--font-mono); font-size: 0.8rem; font-weight: 600; color: #1E293B;">
                                        {{ $vendor->gstin }}
                                    </div>
                                @endif
                                @if($vendor->pan)
                                    <div style="font-family: var(--font-mono); font-size: 0.75rem; color: var(--text-muted);">
                                        PAN: {{ $vendor->pan }}
                                    </div>
                                @endif
                                @if(!$vendor->gstin && !$vendor->pan)
                                    <span class="badge" style="background: #F8FAFC; color: #94A3B8; font-size: 0.72rem; border: 1px dashed #CBD5E1;">Unregistered</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-dark" style="font-size: 0.88rem;">
                                    {{ $vendor->city ?: '—' }}
                                </div>
                                <div style="font-size: 0.76rem; color: var(--text-muted);">
                                    {{ $vendor->state ?: 'Rajasthan' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold" style="font-size: 0.92rem; color: {{ $vendor->current_balance > 0 ? '#B45309' : '#166534' }};">
                                    ₹{{ number_format($vendor->current_balance, 2) }}
                                </div>
                                <div style="font-size: 0.75rem;">
                                    <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: var(--primary); font-weight: 500;">
                                        {{ $vendor->payment_terms ?: '30 Days' }}
                                    </span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.masters.vendor.toggle-status', $vendor->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($vendor->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Supplier">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Supplier">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-icon btn-sm" onclick="openVendorDrawer({{ $vendor->id }})" title="Quick View Supplier">
                                        <i class="fa-regular fa-eye" style="color: #64748B;"></i>
                                    </button>
                                    <a href="{{ route('admin.masters.vendor.edit', $vendor->id) }}" class="btn btn-icon btn-sm" title="Edit Vendor Details">
                                        <i class="fa-solid fa-pen-to-square" style="color: var(--primary);"></i>
                                    </a>
                                    <button type="button" class="btn btn-icon btn-sm text-danger" onclick="confirmDeleteVendor({{ $vendor->id }}, '{{ addslashes($vendor->name) }}', '{{ $vendor->code }}')" title="Archive / Delete Vendor">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-truck-field"></i>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-1">No Vendors Found</h4>
                                    <p class="text-muted mb-3" style="font-size: 0.88rem; max-width: 400px;">
                                        @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['city'] ?? 'all') !== 'all')
                                            No vendor accounts match your active filters. Try adjusting your search query or reset filters.
                                        @else
                                            No vendor suppliers have been registered yet. Add your first raw material supplier or herbal vendor account.
                                        @endif
                                    </p>
                                    @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['city'] ?? 'all') !== 'all')
                                        <a href="{{ route('admin.masters.vendor') }}" class="btn btn-outline btn-sm">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Reset Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.masters.vendor.create') }}" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-plus me-1"></i> Add First Vendor
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
        @if($vendors->hasPages())
            <div class="erp-pagination-wrap">
                <div class="erp-pagination-meta">
                    Showing {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} vendors
                </div>
                <div>
                    {{ $vendors->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Hidden Form for Safe Deletion -->
    <form id="delete-vendor-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</section>

<!-- Quick View Slide-over Modal / Offcanvas -->
<div class="modal-overlay" id="modal-quick-vendor" style="display: none;">
    <div class="modal-card" style="max-width: 650px; border-radius: 16px;">
        <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 1.25rem 1.5rem;">
            <div class="d-flex align-items-center gap-2">
                <div id="drawer-avatar" class="erp-user-avatar-circle" style="background: rgba(91, 132, 30, 0.12); color: var(--primary);">
                    VN
                </div>
                <div>
                    <h3 id="drawer-name" class="modal-title mb-0" style="font-size: 1.15rem; font-weight: 700;">Vendor Name</h3>
                    <span id="drawer-code" class="badge" style="background: #F1F5F9; color: #475569; font-family: var(--font-mono); font-size: 0.72rem;">VND-00</span>
                </div>
            </div>
            <button type="button" class="modal-close" onclick="closeVendorDrawer()">&times;</button>
        </div>

        <div class="modal-body" style="padding: 1.5rem; max-height: 75vh; overflow-y: auto;">
            <!-- Financial Strip -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div class="card p-3" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Current Balance (Payable)</div>
                    <div id="drawer-balance" style="font-size: 1.4rem; font-weight: 800; color: #B45309;">₹0.00</div>
                </div>
                <div class="card p-3" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Payment Terms</div>
                    <div id="drawer-terms" style="font-size: 1.2rem; font-weight: 700; color: var(--primary);">30 Days</div>
                </div>
            </div>

            <!-- Details Table -->
            <div class="card p-3 mb-3" style="border: 1px solid var(--border-color); border-radius: 10px;">
                <h5 class="fw-bold mb-3" style="font-size: 0.9rem; color: var(--text-primary);"><i class="fa-solid fa-address-book me-2 text-primary"></i>Contact &amp; Location</h5>
                <div class="d-flex flex-column gap-2" style="font-size: 0.88rem;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Contact Person:</span>
                        <strong id="drawer-contact" class="text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Mobile Number:</span>
                        <strong id="drawer-phone" class="text-dark font-monospace">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Email:</span>
                        <strong id="drawer-email" class="text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">City / State:</span>
                        <strong id="drawer-location" class="text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Full Address:</span>
                        <strong id="drawer-address" class="text-dark text-end" style="max-width: 320px;">—</strong>
                    </div>
                </div>
            </div>

            <!-- Tax & Bank Details -->
            <div class="card p-3" style="border: 1px solid var(--border-color); border-radius: 10px;">
                <h5 class="fw-bold mb-3" style="font-size: 0.9rem; color: var(--text-primary);"><i class="fa-solid fa-building-columns me-2 text-primary"></i>Tax &amp; Bank Settlement</h5>
                <div class="d-flex flex-column gap-2" style="font-size: 0.88rem;">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">GSTIN:</span>
                        <strong id="drawer-gstin" class="font-monospace text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">PAN:</span>
                        <strong id="drawer-pan" class="font-monospace text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Bank Name:</span>
                        <strong id="drawer-bank" class="text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Account No:</span>
                        <strong id="drawer-account" class="font-monospace text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">IFSC Code:</span>
                        <strong id="drawer-ifsc" class="font-monospace text-dark">—</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Branch:</span>
                        <strong id="drawer-branch" class="text-dark">—</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 1rem 1.5rem; display: flex; justify-content: space-between;">
            <button type="button" class="btn btn-outline" onclick="closeVendorDrawer()">Close</button>
            <a id="drawer-edit-btn" href="#" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Full Profile
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openVendorDrawer(vendorId) {
    const modal = document.getElementById('modal-quick-vendor');
    if (!modal) return;

    // Fetch vendor details via AJAX
    fetch(`/admin/masters/vendor/${vendorId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success || !data.vendor) return;
        const v = data.vendor;

        // Populate drawer
        document.getElementById('drawer-name').innerText = v.name;
        document.getElementById('drawer-code').innerText = v.code;
        document.getElementById('drawer-avatar').innerText = v.name.substring(0, 2).toUpperCase();
        document.getElementById('drawer-balance').innerText = '₹' + Number(v.current_balance || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('drawer-terms').innerText = v.payment_terms || '30 Days';

        document.getElementById('drawer-contact').innerText = v.contact_person || '—';
        document.getElementById('drawer-phone').innerText = v.phone || '—';
        document.getElementById('drawer-email').innerText = v.email || '—';
        document.getElementById('drawer-location').innerText = (v.city || '') + (v.state ? ', ' + v.state : '');
        document.getElementById('drawer-address').innerText = v.address || '—';

        document.getElementById('drawer-gstin').innerText = v.gstin || 'Unregistered';
        document.getElementById('drawer-pan').innerText = v.pan || '—';
        document.getElementById('drawer-bank').innerText = v.bank_name || '—';
        document.getElementById('drawer-account').innerText = v.bank_account_no || '—';
        document.getElementById('drawer-ifsc').innerText = v.bank_ifsc || '—';
        document.getElementById('drawer-branch').innerText = v.bank_branch || '—';

        document.getElementById('drawer-edit-btn').href = `/admin/masters/vendor/${v.id}/edit`;

        modal.style.display = 'flex';
    })
    .catch(err => {
        console.error(err);
        if (typeof toastr !== 'undefined') toastr.error('Failed to load vendor details.');
    });
}

function closeVendorDrawer() {
    const modal = document.getElementById('modal-quick-vendor');
    if (modal) modal.style.display = 'none';
}

function confirmDeleteVendor(vendorId, vendorName, vendorCode) {
    Swal.fire({
        title: 'Archive Vendor Account?',
        html: `Are you sure you want to remove vendor <strong>"${vendorName}"</strong> (${vendorCode})?<br><small class="text-muted">This supplier will be archived and hidden from transaction entry dropdowns.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Archive Vendor',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-border-radius'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-vendor-form');
            form.action = `/admin/masters/vendor/${vendorId}`;
            form.submit();
        }
    });
}

// Close drawer on background click
document.addEventListener('click', (e) => {
    const modal = document.getElementById('modal-quick-vendor');
    if (modal && e.target === modal) {
        closeVendorDrawer();
    }
});
</script>
@endpush
