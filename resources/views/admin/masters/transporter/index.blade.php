@extends('admin.layouts.app')

@section('title', 'Transporter Master - Logistics & Freight Directory - VIKAS UDHYOG ERP')
@section('page_code', 'master-transporter')

@section('content')
<section class="view-section active" id="view-master-transporter">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Transporter Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-fast text-primary"></i> Transporter Master
            </h1>
            <p class="erp-page-subtitle">
                Manage logistics partners, freight carriers, vehicle transport services, and GSTIN transporter codes.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print List">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.transporter.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Transporter
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-truck"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Transporters</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Transporters</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-warning">
            <div class="erp-kpi-icon-box erp-kpi-icon-warning">
                <i class="fa-solid fa-circle-pause"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Inactive Providers</div>
                <div class="erp-kpi-val">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-info">
            <div class="erp-kpi-icon-box erp-kpi-icon-info">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Operating Cities</div>
                <div class="erp-kpi-val">{{ $stats['cities_count'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="card erp-main-card">
        <!-- Filter Toolbar Header -->
        <div class="erp-table-filter-header">
            <form method="GET" action="{{ route('admin.masters.transporter') }}" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Enter Transporter Name / GSTIN / Phone..."
                           class="erp-filter-search">
                </div>

                <select name="status" class="erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>

                <select name="city" class="erp-filter-select" onchange="this.form.submit()">
                    <option value="">All Cities</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ request('city') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>

                @if(request()->hasAny(['search', 'status', 'city']))
                    <a href="{{ route('admin.masters.transporter') }}" class="btn btn-outline btn-sm" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $transporters->firstItem() ?? 0 }}-{{ $transporters->lastItem() ?? 0 }}</strong> of <strong>{{ $transporters->total() }}</strong> entries
            </div>
        </div>

        <!-- Table Listing -->
        <div class="table-responsive">
            <table class="table erp-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Transporter Name</th>
                        <th>Transporter ID / GSTIN</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Status</th>
                        <th style="width: 140px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transporters as $index => $t)
                        <tr>
                            <td>{{ $transporters->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="erp-table-avatar">
                                        {{ strtoupper(substr($t->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.transporter.show', $t) }}" class="erp-row-title text-decoration-none fw-bold">
                                            {{ $t->name }}
                                        </a>
                                        @if($t->notes)
                                            <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $t->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($t->transporter_id_gstin)
                                    <span class="erp-code-pill font-monospace">{{ $t->transporter_id_gstin }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($t->phone)
                                    <a href="tel:{{ $t->phone }}" class="text-decoration-none text-dark font-monospace">
                                        <i class="fa-solid fa-phone text-muted me-1"></i>{{ $t->phone }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $t->city ?? '—' }}</td>
                            <td>
                                <form action="{{ route('admin.masters.transporter.toggle-status', $t) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="btn btn-sm {{ $t->is_active ? 'erp-status-btn-active' : 'erp-status-btn-inactive' }}"
                                            title="Click to toggle status">
                                        <i class="fa-solid {{ $t->is_active ? 'fa-check' : 'fa-ban' }}"></i>
                                        {{ $t->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.masters.transporter.show', $t) }}" class="btn btn-sm btn-outline" title="View 360° Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.transporter.edit', $t) }}" class="btn btn-sm btn-outline text-primary" title="Edit Transporter">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.masters.transporter.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this transporter record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline text-danger" title="Archive">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="erp-empty-state">
                                    <i class="fa-solid fa-truck-ramp-box fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Transporters Found</h5>
                                    <p class="text-muted small">No logistics carriers matched your search criteria.</p>
                                    <a href="{{ route('admin.masters.transporter.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fa-solid fa-plus"></i> Add First Transporter
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transporters->hasPages())
            <div class="erp-pagination-wrap p-3">
                {{ $transporters->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
