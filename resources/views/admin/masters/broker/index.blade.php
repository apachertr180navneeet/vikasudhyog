@extends('admin.layouts.app')

@section('title', 'Broker Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-broker')

@section('content')
<section class="view-section active" id="view-master-broker">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Broker Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-handshake text-primary"></i> Broker Master
            </h1>
            <p class="erp-page-subtitle">
                Mandi commission agents, procurement middlemen &amp; sales order intermediator accounts.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Broker Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.broker.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Broker
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Brokers</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Agents</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Commission Payable</div>
                <div class="erp-kpi-val font-monospace" style="color: #7E22CE;">₹{{ number_format($stats['total_commission_payable'] ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Mandi Hubs</div>
                <div class="erp-kpi-val" style="color: #1E293B;">{{ $stats['cities_count'] ?? 0 }} Locations</div>
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

    <!-- Main Content Card with Data Table -->
    <div class="card erp-main-card">
        <!-- Integrated Filter Bar -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.masters.broker') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search broker name, code, contact, phone, PAN, city..." class="form-control erp-search-input">
                </div>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                @if(!empty($cities) && $cities->count() > 0)
                    <select name="city" class="form-control erp-filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($filters['city'] ?? 'all') === 'all' ? 'selected' : '' }}>All Mandis</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ ($filters['city'] ?? '') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                @endif

                <button type="submit" class="btn btn-outline erp-btn-filter">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['city'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.broker') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $brokers->count() }}</strong> of <strong>{{ $brokers->total() }}</strong> brokers
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">#</th>
                        <th>Broker / Agency Firm</th>
                        <th>Contact Person</th>
                        <th>Phone &amp; Email</th>
                        <th>Commission (%)</th>
                        <th>PAN / GSTIN</th>
                        <th>City / Mandi</th>
                        <th>Current Balance</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brokers as $index => $broker)
                        <tr>
                            <td>
                                <span class="text-muted" style="font-size: 0.8rem;">
                                    {{ $brokers->firstItem() + $index }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $broker->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.broker.show', $broker->id) }}" class="fw-bold text-dark text-decoration-none erp-table-title-link">
                                            {{ $broker->name }}
                                        </a>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                {{ $broker->code }}
                                            </span>
                                            @if($broker->company)
                                                <span class="badge" style="background: rgba(91, 132, 30, 0.08); color: var(--primary); font-size: 0.72rem; border: 1px solid rgba(91, 132, 30, 0.15);" title="Assigned Plant: {{ $broker->company->name }}">
                                                    <i class="fa-solid fa-building" style="font-size: 0.65rem;"></i> {{ $broker->company->code }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark" style="font-size: 0.88rem;">
                                    {{ $broker->contact_person ?: '—' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem;">
                                    @if($broker->phone)
                                        <div>
                                            <i class="fa-solid fa-phone" style="font-size: 0.7rem; color: var(--primary); width: 14px;"></i>
                                            <a href="tel:{{ $broker->phone }}" class="text-dark text-decoration-none font-monospace">{{ $broker->phone }}</a>
                                        </div>
                                    @endif
                                    @if($broker->email)
                                        <div style="margin-top: 2px;">
                                            <i class="fa-regular fa-envelope" style="font-size: 0.7rem; color: var(--primary); width: 14px;"></i>
                                            <a href="mailto:{{ $broker->email }}" class="text-muted text-decoration-none">{{ $broker->email }}</a>
                                        </div>
                                    @endif
                                    @if(!$broker->phone && !$broker->email)
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #047857; font-weight: 700; font-size: 0.82rem; border: 1px solid rgba(16, 185, 129, 0.2);">
                                    {{ number_format($broker->commission_rate, 2) }}%
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem;">
                                    @if($broker->pan)
                                        <div><span class="text-muted" style="font-size: 0.72rem;">PAN:</span> <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; font-size: 0.74rem; border: 1px solid #E2E8F0; padding: 2px 6px; border-radius: 4px;">{{ $broker->pan }}</span></div>
                                    @endif
                                    @if($broker->gstin)
                                        <div style="margin-top: 2px;"><span class="text-muted" style="font-size: 0.72rem;">GST:</span> <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; font-size: 0.74rem; border: 1px solid #E2E8F0; padding: 2px 6px; border-radius: 4px;">{{ $broker->gstin }}</span></div>
                                    @endif
                                    @if(!$broker->pan && !$broker->gstin)
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem;">
                                    <strong style="color: #1E293B;">{{ $broker->city ?: 'Sojat' }}</strong>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $broker->state ?: 'Rajasthan' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="font-monospace fw-bold" style="color: {{ $broker->current_balance > 0 ? '#B91C1C' : '#059669' }};">
                                    ₹{{ number_format($broker->current_balance, 2) }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <form action="{{ route('admin.masters.broker.toggle-status', $broker->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($broker->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Broker">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Broker">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div class="erp-actions-cell">
                                    <a href="{{ route('admin.masters.broker.show', $broker->id) }}" class="erp-table-action-icon" title="View Broker Profile">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.broker.edit', $broker->id) }}" class="erp-table-action-icon" title="Edit Broker Details">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteBroker({{ $broker->id }}, '{{ addslashes($broker->name) }}', '{{ $broker->code }}')" title="Archive / Delete Broker">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-handshake"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">No Broker Accounts Found</h5>
                                    <p class="text-muted" style="max-width: 400px; font-size: 0.88rem;">
                                        No mandi brokers or commission agents match your current search and filter settings.
                                    </p>
                                    <a href="{{ route('admin.masters.broker.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa-solid fa-plus me-1"></i> Add New Broker
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Summary Footer -->
        @if($brokers->hasPages())
            <div class="erp-pagination-wrap d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Showing <strong>{{ $brokers->firstItem() }}</strong> to <strong>{{ $brokers->lastItem() }}</strong> of <strong>{{ $brokers->total() }}</strong> entries
                </div>
                <div>
                    {{ $brokers->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Hidden Form for Safe Deletion -->
    <form id="delete-broker-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</section>
@endsection

@push('scripts')
<script>
    function confirmDeleteBroker(id, name, code) {
        Swal.fire({
            title: 'Archive Broker Account?',
            html: `Are you sure you want to archive broker <strong>"${name}"</strong> (<code style="color:var(--primary); font-family:monospace;">${code}</code>)?<br><small class="text-muted mt-2 d-block">Existing commission ledgers and linked sales orders will remain intact.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, Archive Account',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-broker-form');
                form.action = `/admin/masters/broker/${id}`;
                form.submit();
            }
        });
    }
</script>
@endpush
