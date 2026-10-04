@extends('admin.layouts.app')

@section('title', 'Unit Master - Measurement & Conversion Relations - VIKAS UDHYOG ERP')
@section('page_code', 'master-unit')

@section('content')
<section class="view-section active" id="view-master-unit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Unit Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-scale-balanced text-primary"></i> Unit Master
            </h1>
            <p class="erp-page-subtitle">
                Measurement units of measure (UOM), GST UQC reporting codes &amp; conversion relations (e.g. 100 cm = 1 m).
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Unit Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.unit.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Unit
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Defined Units</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Units</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Primary Base Units</div>
                <div class="erp-kpi-val">{{ $stats['base_units'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-arrows-split-up-and-left"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Conversion Relations</div>
                <div class="erp-kpi-val" style="color: #2563EB;">{{ $stats['derived_units'] ?? 0 }}</div>
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
            <form action="{{ route('admin.masters.unit') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search unit name, symbol, UQC..." class="form-control erp-search-input">
                </div>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="type" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' }}>All Unit Types</option>
                    <option value="base" {{ ($filters['type'] ?? '') === 'base' ? 'selected' : '' }}>Primary Base Units</option>
                    <option value="derived" {{ ($filters['type'] ?? '') === 'derived' ? 'selected' : '' }}>Derived Conversion Units</option>
                </select>

                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filters">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['type'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.unit') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $units->count() }}</strong> of <strong>{{ $units->total() }}</strong> units
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Unit Name &amp; Symbol</th>
                        <th>Symbol / Code</th>
                        <th>GST UQC Code</th>
                        <th>Classification</th>
                        <th>Conversion Relation Formula</th>
                        <th style="text-align: center;">Decimal Precision</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr>
                            <!-- Unit Name & Avatar -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $unit->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.unit.show', $unit->id) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link">
                                            {{ $unit->name }}
                                        </a>
                                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 0.1rem;">
                                            {{ $unit->description ? Str::limit($unit->description, 45) : 'Standard measurement unit' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Unit Symbol -->
                            <td>
                                <span class="badge font-monospace" style="background: #F1F5F9; color: #1E293B; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                    {{ $unit->code }}
                                </span>
                            </td>

                            <!-- GST UQC Code -->
                            <td>
                                @if($unit->uqc_code)
                                    <span class="badge font-monospace" style="background: #EFF6FF; color: #1D4ED8; font-size: 0.74rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; border: 1px solid #DBEAFE;">
                                        {{ $unit->uqc_code }}
                                    </span>
                                @else
                                    <span style="color: #94A3B8; font-size: 0.8rem;">—</span>
                                @endif
                            </td>

                            <!-- Classification -->
                            <td>
                                @if($unit->is_base_unit || empty($unit->base_unit_id))
                                    <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                        <i class="fa-solid fa-cube me-1"></i> Base Unit
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.1); color: #2563EB; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(59, 130, 246, 0.2);">
                                        <i class="fa-solid fa-arrows-split-up-and-left me-1"></i> Sub-Unit
                                    </span>
                                @endif
                            </td>

                            <!-- Conversion Relation Formula -->
                            <td>
                                @if($unit->is_base_unit || empty($unit->base_unit_id) || !$unit->baseUnit)
                                    <span style="font-size: 0.80rem; color: #64748B; font-style: italic;">
                                        Primary standard (No conversion)
                                    </span>
                                @else
                                    <div>
                                        <span class="badge font-monospace" style="background: #ECFDF5; color: #047857; font-size: 0.78rem; font-weight: 700; padding: 4px 9px; border-radius: 6px; border: 1px solid #A7F3D0;">
                                            <i class="fa-solid fa-link me-1"></i> {{ $unit->relation_formula }}
                                        </span>
                                        @if($unit->equivalent_formula)
                                            <div class="font-monospace" style="font-size: 0.70rem; color: #64748B; margin-top: 0.15rem;">
                                                ({{ $unit->equivalent_formula }})
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- Decimal Precision -->
                            <td style="text-align: center;">
                                <span class="badge font-monospace" style="background: #F8FAFC; color: #475569; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                    {{ $unit->decimal_places }} Decimals
                                </span>
                            </td>

                            <!-- Status Toggle Button -->
                            <td style="text-align: center;">
                                <form action="{{ route('admin.masters.unit.toggle-status', $unit->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($unit->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Unit">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Unit">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.masters.unit.show', $unit->id) }}" class="erp-table-action-icon" title="View Unit Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.unit.edit', $unit->id) }}" class="erp-table-action-icon" title="Edit Unit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}', '{{ $unit->code }}')" title="Archive Unit">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3.5rem 1.5rem;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1.6rem; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-scale-unbalanced"></i>
                                    </div>
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No Units Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 380px; margin-bottom: 1.25rem;">
                                        No measurement units match your search or filter criteria.
                                    </p>
                                    @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['type'] ?? 'all') !== 'all')
                                        <a href="{{ route('admin.masters.unit') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.masters.unit.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Add First Unit
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
        @if($units->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $units->firstItem() }}</strong> to <strong>{{ $units->lastItem() }}</strong> of <strong>{{ $units->total() }}</strong> entries
                </div>
                <div>
                    {{ $units->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Unit Hidden Form -->
<form id="delete-unit-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteUnit(unitId, unitName, unitCode) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Measurement Unit?',
                html: `Are you sure you want to archive <strong>${unitName} (${unitCode})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the unit from active item selections.</span>`,
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
                    const form = document.getElementById('delete-unit-form');
                    form.action = `{{ url('admin/masters/unit') }}/${unitId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive ${unitName} (${unitCode})?`)) {
                const form = document.getElementById('delete-unit-form');
                form.action = `{{ url('admin/masters/unit') }}/${unitId}`;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
