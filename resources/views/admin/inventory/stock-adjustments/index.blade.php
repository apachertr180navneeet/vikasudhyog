@extends('admin.layouts.app')

@section('title', 'Stock Adjustment & Physical Audit - VIKAS UDHYOG ERP')
@section('page_code', 'inv-adjustment')

@section('content')
<section class="view-section active" id="view-inv-adjustment">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Inventory</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.inventory.stock-overview') }}">Stock Overview</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Stock Adjustment &amp; Physical Audit</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-sliders text-primary"></i> Stock Adjustment &amp; Physical Audit
            </h1>
            <p class="erp-page-subtitle">
                Reconcile physical warehouse stock counts with digital inventory records, log shortages, spillage &amp; surplus gains.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Current Audit Log Sheet">
                <i class="fa-solid fa-print"></i> Print Log
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Audit Log to CSV Spreadsheet">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.inventory.stock-adjustment.create') }}" class="btn btn-primary erp-btn-header-primary" title="Record a Physical Stock Adjustment">
                <i class="fa-solid fa-plus"></i> Record Adjustment
            </a>
            <a href="{{ route('admin.inventory.stock-overview') }}" class="btn btn-secondary" title="View Real-Time Catalog Stock Overview">
                <i class="fa-solid fa-boxes-stacked"></i> Stock Overview
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success" style="margin-bottom: 1.25rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert erp-alert-danger" style="margin-bottom: 1.25rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-exclamation erp-alert-icon-danger"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert erp-alert-danger" style="margin-bottom: 1.25rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-exclamation erp-alert-icon-danger"></i>
                <div>
                    <strong>Please correct the following errors:</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- 4-Card KPI Summary Statistics Grid -->
    <div class="erp-kpi-grid">
        <!-- 1. Total Audit Adjustments -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Audit Adjustments</div>
                <div class="erp-kpi-val">{{ number_format($stats['total'] ?? 0) }}</div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    {{ $stats['add_count'] ?? 0 }} Surplus &bull; {{ $stats['reduce_count'] ?? 0 }} Shortages
                </div>
            </div>
        </div>

        <!-- 2. Total Surplus Added (+ In) -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Stock Surplus Added (+)</div>
                <div class="erp-kpi-val" style="color: #059669;">
                    +{{ number_format($stats['add_qty'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #059669; margin-top: 2px;">
                    {{ $stats['add_count'] ?? 0 }} Physical Count Gains
                </div>
            </div>
        </div>

        <!-- 3. Total Stock Reductions (- Out) -->
        <div class="card erp-kpi-card erp-kpi-danger">
            <div class="erp-kpi-icon-box erp-kpi-icon-danger">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Stock Reductions (-)</div>
                <div class="erp-kpi-val" style="color: #DC2626;">
                    -{{ number_format($stats['reduce_qty'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #DC2626; margin-top: 2px;">
                    {{ $stats['reduce_count'] ?? 0 }} Damage / Spillage Entries
                </div>
            </div>
        </div>

        <!-- 4. Net Valuation Impact -->
        <div class="card erp-kpi-card erp-kpi-info">
            <div class="erp-kpi-icon-box erp-kpi-icon-info">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Net Valuation Impact</div>
                <div class="erp-kpi-val" style="color: {{ ($stats['net_valuation'] ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                    {{ ($stats['net_valuation'] ?? 0) >= 0 ? '+' : '' }}₹{{ number_format($stats['net_valuation'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Balance Sheet Inventory Delta
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card Container with Edge-to-Edge Table -->
    <div class="card erp-main-card">

        <!-- Inline Search & Filter Toolbar -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.inventory.stock-adjustment') }}" method="GET" class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
                
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.6rem; flex-grow: 1;">
                    <!-- Keyword Search -->
                    <div class="erp-search-wrap" style="min-width: 260px;">
                        <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                        <input type="text" name="search" class="form-control erp-search-input" placeholder="Search adjustment no, item, reason..." value="{{ $filters['search'] ?? '' }}">
                    </div>

                    <!-- Item Filter -->
                    <select name="item_id" class="form-control erp-filter-select" style="min-width: 170px;">
                        <option value="">All Herbal Products</option>
                        @foreach($allItems as $itm)
                            <option value="{{ $itm->id }}" {{ ($filters['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                                {{ $itm->name }} ({{ $itm->code }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Type Filter -->
                    <select name="type" class="form-control erp-filter-select" style="min-width: 150px;">
                        <option value="">All Adjustment Types</option>
                        <option value="add" {{ ($filters['type'] ?? '') === 'add' ? 'selected' : '' }}>+ Surplus / Inward</option>
                        <option value="reduce" {{ ($filters['type'] ?? '') === 'reduce' ? 'selected' : '' }}>- Damage / Shortage</option>
                    </select>

                    <!-- Date Range -->
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <span style="font-size: 0.76rem; font-weight: 600; color: #64748B;">FROM:</span>
                        <input type="date" name="from_date" class="form-control erp-filter-select" value="{{ $filters['from_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;">
                        <span style="font-size: 0.76rem; font-weight: 600; color: #64748B;">TO:</span>
                        <input type="date" name="to_date" class="form-control erp-filter-select" value="{{ $filters['to_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;">
                    </div>

                    <button type="submit" class="btn btn-outline" style="padding: 0.45rem 0.9rem;" title="Filter Records">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>

                    @if(!empty($filters['search']) || !empty($filters['item_id']) || !empty($filters['type']) || !empty($filters['from_date']) || !empty($filters['to_date']))
                        <a href="{{ route('admin.inventory.stock-adjustment') }}" class="btn btn-outline erp-btn-filter-clear" title="Reset All Filters">
                            <i class="fa-solid fa-xmark"></i> Clear
                        </a>
                    @endif
                </div>

                <div class="erp-table-summary-count" style="margin: 0; font-size: 0.8rem;">
                    Showing <strong>{{ $adjustments->count() }}</strong> of <strong>{{ $adjustments->total() }}</strong> audit logs
                </div>
            </form>
        </div>

        <!-- Edge-to-Edge Data Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 140px;">Date &amp; Time</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 150px;">Adjustment No</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 220px;">Herbal Product</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 130px; text-align: center;">Audit Type</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 130px;">Variance Qty</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 150px;">Before &rarr; After</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 130px;">Valuation (₹)</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Reason / Remark</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 130px;">Audited By</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center; width: 130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                        @php
                            $isAdd = $adj->type === 'add';
                            $item = $adj->item;
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                            
                            <!-- Date & Time -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="font-weight: 700; color: #1E293B; font-size: 0.85rem;">
                                    {{ $adj->adjustment_date ? $adj->adjustment_date->format('d M Y') : '-' }}
                                </div>
                                <div style="font-size: 0.72rem; color: #94A3B8;">
                                    {{ $adj->created_at ? $adj->created_at->format('h:i A') : '' }}
                                </div>
                            </td>

                            <!-- Adjustment No -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <a href="{{ route('admin.inventory.stock-adjustment.show', $adj->id) }}" style="text-decoration: none;" title="View Adjustment Dossier">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border: 1px solid #E2E8F0; letter-spacing: 0.02em;">
                                        {{ $adj->adjustment_no }}
                                    </span>
                                </a>
                            </td>

                            <!-- Herbal Product -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 0.65rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                                        {{ $item ? $item->initials : 'PR' }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.88rem;">
                                            @if($item)
                                                <a href="{{ route('admin.masters.item.show', $item->id) }}" style="text-decoration: none; color: inherit;">
                                                    {{ $item->name }}
                                                </a>
                                            @else
                                                <span class="text-muted">Item Deleted</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.74rem; color: #64748B;" class="font-monospace">
                                            {{ $item ? $item->code : '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Audit Type -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                @if($isAdd)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25); font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">
                                        <i class="fa-solid fa-plus me-1"></i> Surplus
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.25); font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px;">
                                        <i class="fa-solid fa-minus me-1"></i> Shortage
                                    </span>
                                @endif
                            </td>

                            <!-- Variance Qty -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-weight: 800; font-size: 0.94rem; color: {{ $isAdd ? '#059669' : '#DC2626' }};">
                                    {{ $isAdd ? '+' : '-' }}{{ number_format($adj->quantity, 2) }}
                                </div>
                                <div style="font-size: 0.72rem; color: #64748B;">
                                    {{ $adj->unit }}
                                </div>
                            </td>

                            <!-- Before -> After -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-size: 0.8rem; color: #64748B;">
                                    {{ number_format($adj->previous_stock, 1) }} &rarr;
                                    <strong style="color: #0F172A; font-size: 0.86rem;">{{ number_format($adj->new_stock, 1) }}</strong>
                                </div>
                                <div style="font-size: 0.7rem; color: #94A3B8;">
                                    {{ $adj->unit }}
                                </div>
                            </td>

                            <!-- Valuation Impact -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-weight: 800; font-size: 0.9rem; color: {{ $isAdd ? '#059669' : '#DC2626' }};">
                                    {{ $isAdd ? '+' : '-' }}₹{{ number_format($adj->total_value, 2) }}
                                </div>
                                <div style="font-size: 0.7rem; color: #94A3B8;" class="font-monospace">
                                    @ ₹{{ number_format($adj->rate, 2) }}
                                </div>
                            </td>

                            <!-- Reason / Remark -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="font-weight: 600; color: #334155; font-size: 0.82rem;">
                                    {{ $adj->reason }}
                                </div>
                                @if($adj->notes)
                                    <div style="font-size: 0.74rem; color: #64748B; margin-top: 1px; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $adj->notes }}">
                                        {{ $adj->notes }}
                                    </div>
                                @endif
                            </td>

                            <!-- Audited By -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="font-size: 0.8rem; font-weight: 600; color: #475569;">
                                    <i class="fa-regular fa-user me-1 text-muted"></i>
                                    {{ $adj->audited_by ?: ($adj->user ? $adj->user->name : 'System') }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                <div class="erp-actions-cell" style="justify-content: center; gap: 4px;">
                                    <!-- View Profile -->
                                    <a href="{{ route('admin.inventory.stock-adjustment.show', $adj->id) }}" class="erp-table-action-icon" title="View Audit Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>

                                    <!-- Edit Adjustment -->
                                    <a href="{{ route('admin.inventory.stock-adjustment.edit', $adj->id) }}" class="erp-table-action-icon" style="color: #2563EB;" title="Edit Physical Adjustment">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <!-- Rollback / Delete -->
                                    <form action="{{ route('admin.inventory.stock-adjustment.destroy', $adj->id) }}" method="POST" style="display: inline;" onsubmit="return confirmRollback(event, '{{ $adj->adjustment_no }}', '{{ addslashes($item ? $item->name : '') }}', '{{ $isAdd ? 'Surplus' : 'Reduction' }}', '{{ number_format($adj->quantity, 2) }} {{ $adj->unit }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="erp-table-action-icon erp-table-action-icon-danger" title="Rollback / Cancel Adjustment">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </div>
                                <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Stock Adjustments Recorded</h4>
                                <p style="font-size: 0.88rem; color: #64748B; margin: 0 0 1.25rem; max-width: 440px; margin-left: auto; margin-right: auto;">
                                    All catalog items currently match their digital counts, or no physical variance audits match your filters.
                                </p>
                                <a href="{{ route('admin.inventory.stock-adjustment.create') }}" class="btn btn-primary erp-btn-header-primary">
                                    <i class="fa-solid fa-plus"></i> Record First Adjustment
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($adjustments->hasPages())
            <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing entries {{ $adjustments->firstItem() }} to {{ $adjustments->lastItem() }} of {{ $adjustments->total() }}
                </div>
                <div>
                    {{ $adjustments->links() }}
                </div>
            </div>
        @endif

    </div>

</section>

<script>
function confirmRollback(e, adjNo, itemName, direction, qty) {
    var msg = "Are you sure you want to cancel and revert adjustment " + adjNo + " for " + itemName + "?\n\nThis will reverse " + qty + " back to the item inventory balance.";
    return confirm(msg);
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #view-inv-adjustment, #view-inv-adjustment * {
        visibility: visible;
    }
    #view-inv-adjustment {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .sidebar, .navbar, .erp-page-top-bar .erp-header-actions, .erp-table-filter-header, .erp-actions-cell {
        display: none !important;
    }
    .erp-main-card {
        box-shadow: none !important;
        border: 1px solid #CBD5E1 !important;
    }
}
</style>
@endsection
