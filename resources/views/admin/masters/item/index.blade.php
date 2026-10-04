@extends('admin.layouts.app')

@section('title', 'Item Master (Products & Raw Materials) - VIKAS UDHYOG ERP')
@section('page_code', 'master-item')

@section('content')
<section class="view-section active" id="view-master-item">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Item Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-boxes-stacked text-primary"></i> Item Master
            </h1>
            <p class="erp-page-subtitle">
                Finished products, henna &amp; herbal raw powders, packaging materials, tax rates, batch lots &amp; stock valuation.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Item Inventory Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.item.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Item
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Catalog Items</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Items</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Stock Valuation</div>
                <div class="erp-kpi-val font-monospace">₹{{ number_format($stats['total_valuation'] ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid {{ ($stats['low_stock_count'] ?? 0) > 0 ? '#EF4444' : '#10B981' }};">
            <div class="erp-kpi-icon-box" style="background: {{ ($stats['low_stock_count'] ?? 0) > 0 ? 'rgba(239, 68, 68, 0.12)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ ($stats['low_stock_count'] ?? 0) > 0 ? '#DC2626' : '#059669' }};">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Low Stock Alerts</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ ($stats['low_stock_count'] ?? 0) > 0 ? '#DC2626' : '#059669' }};">
                    {{ $stats['low_stock_count'] ?? 0 }} <span style="font-size: 0.8rem; font-weight: normal; color: #64748B;">Items</span>
                </div>
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
            <form action="{{ route('admin.masters.item') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search item name, code, HSN, batch..." class="form-control erp-search-input">
                </div>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="category" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['category'] ?? 'all') === 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <select name="unit" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['unit'] ?? 'all') === 'all' ? 'selected' : '' }}>All Units</option>
                    @foreach($units as $u)
                        @php
                            $uVal = $u->code ?: $u->name;
                        @endphp
                        <option value="{{ $uVal }}" {{ ($filters['unit'] ?? '') === $uVal ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->code }})
                        </option>
                    @endforeach
                </select>

                <select name="stock_status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['stock_status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Stock Levels</option>
                    <option value="in_stock" {{ ($filters['stock_status'] ?? '') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ ($filters['stock_status'] ?? '') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                </select>

                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filters">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['category'] ?? 'all') !== 'all' || ($filters['unit'] ?? 'all') !== 'all' || ($filters['stock_status'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.item') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $items->count() }}</strong> of <strong>{{ $items->total() }}</strong> items
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Product &amp; Material</th>
                        <th>Category</th>
                        <th>HSN &amp; GST</th>
                        <th style="text-align: right;">Current Stock</th>
                        <th style="text-align: right;">Purchase Rate</th>
                        <th style="text-align: right;">Selling Rate</th>
                        <th style="text-align: right;">Valuation</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <!-- Product & Material Identity -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.88rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $item->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.item.show', $item->id) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link">
                                            {{ $item->name }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem;">
                                            <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                {{ $item->code }}
                                            </span>
                                            @if($item->batch_no)
                                                <span class="badge font-monospace" style="background: #EFF6FF; color: #1D4ED8; font-size: 0.70rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #DBEAFE;">
                                                    <i class="fa-solid fa-tag" style="font-size: 0.65rem;"></i> {{ $item->batch_no }}
                                                </span>
                                            @endif
                                            @if($item->company)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    <i class="fa-solid fa-building" style="font-size: 0.65rem;"></i> {{ $item->company->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td>
                                <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                    {{ $item->category }}
                                </span>
                            </td>

                            <!-- HSN & GST -->
                            <td>
                                <div class="font-monospace" style="font-size: 0.78rem; color: #1E293B; font-weight: 600;">
                                    {{ $item->hsn_code ?: '—' }}
                                </div>
                                <div style="font-size: 0.72rem; color: #64748B; margin-top: 0.1rem;">
                                    GST: <strong class="font-monospace" style="color: #0F172A;">{{ number_format($item->gst_rate, 1) }}%</strong>
                                </div>
                            </td>

                            <!-- Current Stock -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: {{ $item->is_low_stock ? '#DC2626' : '#0F172A' }};">
                                    {{ number_format($item->current_stock, 2) }} <span style="font-size: 0.74rem; font-weight: 600; color: #64748B;">{{ $item->unit }}</span>
                                </div>
                                @if($item->is_low_stock)
                                    <div style="margin-top: 0.15rem;">
                                        <span class="badge" style="background: #FEE2E2; color: #DC2626; font-size: 0.68rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; border: 1px solid #FECACA;">
                                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.65rem;"></i> Min {{ number_format($item->min_stock_alert, 0) }} {{ $item->unit }}
                                        </span>
                                    </div>
                                @else
                                    <div style="font-size: 0.70rem; color: #10B981; margin-top: 0.1rem;">
                                        <i class="fa-solid fa-check" style="font-size: 0.65rem;"></i> In Stock
                                    </div>
                                @endif
                            </td>

                            <!-- Purchase Rate -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #334155;">
                                    ₹{{ number_format($item->purchase_rate, 2) }}
                                </div>
                                <div style="font-size: 0.70rem; color: #64748B;">
                                    per {{ $item->unit }}
                                </div>
                            </td>

                            <!-- Selling Rate -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #059669;">
                                    ₹{{ number_format($item->sale_rate, 2) }}
                                </div>
                                <div style="font-size: 0.70rem; color: #64748B;">
                                    per {{ $item->unit }}
                                </div>
                            </td>

                            <!-- Stock Valuation -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; color: #0F172A;">
                                    ₹{{ number_format($item->stock_valuation, 2) }}
                                </div>
                                <div style="font-size: 0.70rem; color: #64748B;">
                                    At Cost
                                </div>
                            </td>

                            <!-- Status Toggle Button -->
                            <td style="text-align: center;">
                                <form action="{{ route('admin.masters.item.toggle-status', $item->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($item->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Item">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Item">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.masters.item.show', $item->id) }}" class="erp-table-action-icon" title="View Item Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.item.edit', $item->id) }}" class="erp-table-action-icon" title="Edit Item">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ $item->code }}')" title="Archive Item">
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
                                        <i class="fa-solid fa-box-open"></i>
                                    </div>
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No Items Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 380px; margin-bottom: 1.25rem;">
                                        No item catalog records match your filter criteria or search query.
                                    </p>
                                    @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['category'] ?? 'all') !== 'all' || ($filters['unit'] ?? 'all') !== 'all' || ($filters['stock_status'] ?? 'all') !== 'all')
                                        <a href="{{ route('admin.masters.item') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.masters.item.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Add First Item
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
        @if($items->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $items->firstItem() }}</strong> to <strong>{{ $items->lastItem() }}</strong> of <strong>{{ $items->total() }}</strong> entries
                </div>
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Item Hidden Form -->
<form id="delete-item-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteItem(itemId, itemName, itemCode) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Item from Catalog?',
                html: `Are you sure you want to archive <strong>${itemName} (${itemCode})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the item from active sales and inventory selections.</span>`,
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
                    const form = document.getElementById('delete-item-form');
                    form.action = `{{ url('admin/masters/item') }}/${itemId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive ${itemName} (${itemCode})?`)) {
                const form = document.getElementById('delete-item-form');
                form.action = `{{ url('admin/masters/item') }}/${itemId}`;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
