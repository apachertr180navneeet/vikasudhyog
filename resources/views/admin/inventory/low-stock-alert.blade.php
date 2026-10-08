@extends('admin.layouts.app')

@section('title', 'Low Stock Alert Center - VIKAS UDHYOG ERP')
@section('page_code', 'inv-low-stock')

@section('content')
<section class="view-section active" id="view-inv-low-stock">

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
                <span class="erp-breadcrumb-active">Low Stock Alert Center</span>
            </div>
            <h1 class="erp-page-title" style="color: #0F172A;">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i> Low Stock Alert Center
            </h1>
            <p class="erp-page-subtitle">
                Real-time procurement replenishment dashboard &bull; Catalog items requiring immediate purchase reorder &amp; buffer restoration.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Procurement Reorder Sheet">
                <i class="fa-solid fa-print"></i> Print Reorder Sheet
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Reorder Requisition to CSV">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.transactions.purchase-entry.create') }}" class="btn btn-primary erp-btn-header-primary" title="Record New Purchase Bill">
                <i class="fa-solid fa-plus"></i> New Purchase Entry
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

    <!-- 4-Card KPI Summary Statistics Grid -->
    <div class="erp-kpi-grid">
        <!-- 1. Total Alert Items -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #DC2626;">
            <div class="erp-kpi-icon-box" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Low Stock Alerts</div>
                <div class="erp-kpi-val font-monospace" style="color: #DC2626;">
                    {{ number_format($stats['total_alerts'] ?? 0) }}
                </div>
                <div style="font-size: 0.74rem; color: #DC2626; margin-top: 2px; font-weight: 600;">
                    {{ $stats['out_of_stock_count'] ?? 0 }} Nil &bull; {{ $stats['low_stock_count'] ?? 0 }} Below Min Threshold
                </div>
            </div>
        </div>

        <!-- 2. Critical Out of Stock (Nil) -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #991B1B;">
            <div class="erp-kpi-icon-box" style="background: rgba(153, 27, 27, 0.12); color: #991B1B;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Critical Out of Stock (Nil)</div>
                <div class="erp-kpi-val font-monospace" style="color: #991B1B;">
                    {{ number_format($stats['out_of_stock_count'] ?? 0) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Zero Available Physical Inventory
                </div>
            </div>
        </div>

        <!-- 3. Total Suggested Reorder Qty -->
        <div class="card erp-kpi-card erp-kpi-primary" style="border-left: 4px solid #5B841E;">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Reorder Requisition</div>
                <div class="erp-kpi-val font-monospace" style="color: #5B841E;">
                    {{ number_format($stats['total_suggested_qty'] ?? 0, 1) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Deficit: <strong class="font-monospace" style="color: #D97706;">{{ number_format($stats['total_shortfall_qty'] ?? 0, 1) }}</strong> Shortfall Units
                </div>
            </div>
        </div>

        <!-- 4. Estimated Capital Requirement -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #0F172A;">
            <div class="erp-kpi-icon-box" style="background: rgba(15, 23, 42, 0.08); color: #0F172A;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Estimated Procurement Cost</div>
                <div class="erp-kpi-val font-monospace" style="color: #0F172A;">
                    ₹{{ number_format($stats['estimated_capital'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #059669; margin-top: 2px; font-weight: 600;">
                    Budget Required to Restore Buffers
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container Card -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">
        
        <!-- Inline Search & Filter Toolbar Header -->
        <div class="erp-table-filter-header" style="padding: 1rem 1.25rem; background: #FCFDFB; border-bottom: 1px solid #E2E8F0;">
            <form action="{{ route('admin.inventory.low-stock-alert') }}" method="GET" class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
                
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; flex: 1;">
                    <!-- Keyword Search -->
                    <div class="erp-search-wrap" style="min-width: 240px; flex: 1; max-width: 360px;">
                        <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search alert item, code, HSN..." class="form-control erp-search-input">
                    </div>

                    <!-- Severity Filter Dropdown -->
                    <select name="severity" class="form-control erp-filter-select" onchange="this.form.submit()" style="min-width: 175px;">
                        <option value="all" {{ ($filters['severity'] ?? 'all') === 'all' ? 'selected' : '' }}>All Alert Items</option>
                        <option value="out_of_stock" {{ ($filters['severity'] ?? '') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (Nil / &le; 0)</option>
                        <option value="critical" {{ ($filters['severity'] ?? '') === 'critical' ? 'selected' : '' }}>Critical (&lt; 50% Min Buffer)</option>
                        <option value="low_stock" {{ ($filters['severity'] ?? '') === 'low_stock' ? 'selected' : '' }}>Low Stock (&le; Min Alert)</option>
                    </select>

                    <!-- Category Filter Dropdown -->
                    <select name="category" class="form-control erp-filter-select" onchange="this.form.submit()" style="min-width: 155px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ ($filters['category'] ?? '') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Sort Order Dropdown -->
                    <select name="sort_by" class="form-control erp-filter-select" onchange="this.form.submit()" style="min-width: 180px;">
                        <option value="deficit_desc" {{ ($filters['sort_by'] ?? 'deficit_desc') === 'deficit_desc' ? 'selected' : '' }}>Highest Shortfall First</option>
                        <option value="stock_asc" {{ ($filters['sort_by'] ?? '') === 'stock_asc' ? 'selected' : '' }}>Lowest Stock First</option>
                        <option value="cost_desc" {{ ($filters['sort_by'] ?? '') === 'cost_desc' ? 'selected' : '' }}>Highest Reorder Cost</option>
                        <option value="name_asc" {{ ($filters['sort_by'] ?? '') === 'name_asc' ? 'selected' : '' }}>Product Name (A-Z)</option>
                    </select>

                    <!-- Filter & Clear Buttons -->
                    <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filter Criteria">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    @if(!empty($filters['search']) || !empty($filters['category']) || ($filters['severity'] ?? 'all') !== 'all' || ($filters['sort_by'] ?? 'deficit_desc') !== 'deficit_desc')
                        <a href="{{ route('admin.inventory.low-stock-alert') }}" class="btn btn-outline erp-btn-filter-clear" title="Reset All Filters">
                            <i class="fa-solid fa-xmark"></i> Clear
                        </a>
                    @endif
                </div>

                <div class="erp-table-summary-count" style="margin: 0; font-size: 0.8rem;">
                    Showing <strong>{{ $items->count() }}</strong> of <strong>{{ $items->total() }}</strong> alert items
                </div>
            </form>
        </div>

        <!-- Edge-to-Edge Data Table (Refined 6-Column High-Density Layout: No Horizontal Scrollbar) -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0; table-layout: auto;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <th style="padding: 1rem 1.25rem; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 28%;">Herbal Product</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 20%;">Stock &amp; Buffer Capacity</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 18%;">Deficit &amp; Suggested Reorder</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 14%;">Reorder Capital</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 14%;">Urgency &amp; Supplier</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.74rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center; width: 6%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $isNil = (float)$item->current_stock <= 0;
                            $pct = $item->stock_pct;
                            $progressBg = $isNil ? '#DC2626' : ($pct <= 50 ? '#EA580C' : '#D97706');
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                            
                            <!-- 1. Product Details (Avatar, Title, SKU, Category, Lot) -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, {{ $isNil ? '#DC2626, #991B1B' : '#D97706, #B45309' }}); color: #FFFFFF; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">
                                        {{ $item->initials }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.94rem; line-height: 1.3;">
                                            <a href="{{ route('admin.masters.item.show', $item->id) }}" style="text-decoration: none; color: inherit;" title="View Complete Item Dossier">
                                                {{ $item->name }}
                                            </a>
                                        </div>
                                        <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 6px; margin-top: 4px;">
                                            <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border: 1px solid #E2E8F0;">
                                                {{ $item->code }}
                                            </span>
                                            <span class="badge" style="background: #F8FAFC; color: #64748B; border: 1px solid #E2E8F0; font-size: 0.72rem; padding: 2px 6px;">
                                                {{ $item->category ?: 'General' }}
                                            </span>
                                            @if($item->batch_no)
                                                <span style="font-size: 0.72rem; color: #94A3B8;" class="font-monospace">
                                                    Lot: {{ $item->batch_no }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- 2. Stock & Buffer Capacity (Current vs Min & Progress Bar) -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div style="display: flex; align-items: baseline; gap: 6px; margin-bottom: 4px;">
                                    <span class="font-monospace" style="font-weight: 800; font-size: 1.05rem; color: {{ $isNil ? '#DC2626' : '#D97706' }};">
                                        {{ number_format($item->current_stock, 1) }}
                                    </span>
                                    <span style="font-size: 0.8rem; color: #64748B;">
                                        / Min {{ number_format($item->min_stock_alert, 1) }} {{ $item->unit }}
                                    </span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="flex: 1; height: 6px; background: #F1F5F9; border-radius: 9999px; overflow: hidden; border: 1px solid #E2E8F0;">
                                        <div style="width: {{ $pct }}%; height: 100%; background: {{ $progressBg }}; border-radius: 9999px; transition: width 0.3s ease;"></div>
                                    </div>
                                    <span class="font-monospace" style="font-size: 0.74rem; font-weight: 700; color: {{ $isNil ? '#DC2626' : '#475569' }}; min-width: 38px; text-align: right;">
                                        {{ $pct }}%
                                    </span>
                                </div>
                            </td>

                            <!-- 3. Deficit Shortfall & Suggested Reorder -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <div style="font-size: 0.78rem; color: #64748B;">
                                        Shortfall: <strong class="font-monospace" style="color: #DC2626;">-{{ number_format($item->shortfall_qty, 1) }} {{ $item->unit }}</strong>
                                    </div>
                                    <div>
                                        <span class="font-monospace" style="font-weight: 700; font-size: 0.82rem; color: #059669; background: #ECFDF5; padding: 2px 7px; border-radius: 5px; display: inline-block; border: 1px solid #A7F3D0;">
                                            +{{ number_format($item->suggested_qty, 1) }} {{ $item->unit }} Target
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- 4. Estimated Reorder Capital -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: #0F172A;">
                                    ₹{{ number_format($item->reorder_cost, 2) }}
                                </div>
                                <div class="font-monospace" style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                                    @ ₹{{ number_format($item->purchase_rate, 2) }}/{{ $item->unit }}
                                </div>
                            </td>

                            <!-- 5. Urgency Pill & Last Supplier -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                @if($isNil)
                                    <span class="badge" style="background: rgba(220, 38, 38, 0.1); color: #DC2626; font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; border: 1px solid rgba(220, 38, 38, 0.25);">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Out of Stock
                                    </span>
                                @elseif($pct <= 50)
                                    <span class="badge" style="background: rgba(234, 88, 12, 0.1); color: #EA580C; font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; border: 1px solid rgba(234, 88, 12, 0.25);">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Critical &lt; 50%
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(217, 119, 6, 0.1); color: #D97706; font-size: 0.74rem; font-weight: 700; padding: 3px 8px; border-radius: 9999px; border: 1px solid rgba(217, 119, 6, 0.25);">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i> Low Stock
                                    </span>
                                @endif

                                @if($item->last_vendor)
                                    <div style="font-size: 0.75rem; color: #475569; margin-top: 4px; font-weight: 500;" title="Last Supplier: {{ $item->last_vendor->name }}">
                                        <i class="fa-solid fa-truck-field me-1 text-muted"></i> {{ $item->last_vendor->name }}
                                    </div>
                                @endif
                            </td>

                            <!-- 6. Actions (Reorder + Ledger) -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <!-- Create Purchase Button -->
                                    <a href="{{ route('admin.transactions.purchase-entry.create', ['item_id' => $item->id, 'suggested_qty' => $item->suggested_qty]) }}" class="btn btn-sm btn-primary" style="padding: 6px 12px; font-size: 0.78rem; font-weight: 700; border-radius: 6px; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.2);" title="Create Purchase Bill for this item">
                                        <i class="fa-solid fa-cart-plus"></i> Reorder
                                    </a>

                                    <!-- View Ledger Icon -->
                                    <a href="{{ route('admin.inventory.item-ledger', ['item_id' => $item->id]) }}" class="erp-table-action-icon" title="View Product Stock Ledger">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4.5rem 1rem; color: #64748B;">
                                <div style="width: 64px; height: 64px; border-radius: 50%; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 0.85rem;">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h3 style="font-weight: 700; color: #0F172A; margin-bottom: 0.35rem; font-size: 1.25rem;">Inventory Levels Healthy!</h3>
                                <p style="font-size: 0.88rem; color: #64748B; margin: 0; max-width: 440px; margin-left: auto; margin-right: auto;">
                                    No products are currently below their minimum safety threshold. All catalog stock buffers are sufficient.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($items->hasPages())
            <div style="padding: 1rem 1.25rem; background: #FFFFFF; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing entries {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }}
                </div>
                <div>
                    {{ $items->links() }}
                </div>
            </div>
        @endif

    </div>

</section>
@endsection
