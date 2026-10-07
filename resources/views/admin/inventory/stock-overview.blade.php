@extends('admin.layouts.app')

@section('title', 'Stock Overview & Valuation - VIKAS UDHYOG ERP')
@section('page_code', 'inv-overview')

@section('content')
<section class="view-section active" id="view-inv-overview">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Inventory</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Stock Overview</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-boxes-stacked text-primary"></i> Stock Overview &amp; Valuation
            </h1>
            <p class="erp-page-subtitle">
                Real-time catalog stock levels, inventory valuation, minimum reorder thresholds &amp; outward availability.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Current Stock Sheet">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Stock Overview to CSV / Excel">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.inventory.stock-adjustment') }}" class="btn btn-secondary" title="Record Physical Stock Adjustment">
                <i class="fa-solid fa-sliders"></i> Adjust Stock
            </a>
            <a href="{{ route('admin.masters.item') }}" class="btn btn-primary erp-btn-header-primary" title="Open Item Master Catalog">
                <i class="fa-solid fa-box-archive"></i> Item Master
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <!-- Total Items -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Catalog Items</div>
                <div class="erp-kpi-val">{{ number_format($totalItems) }}</div>
            </div>
        </div>

        <!-- Inventory Valuation -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Inventory Valuation</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($totalStockValuation, 2) }}</div>
            </div>
        </div>

        <!-- Total Physical Stock -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Physical Stock Qty</div>
                <div class="erp-kpi-val font-monospace" style="color: #2563EB;">
                    {{ number_format($totalStockQty, 2) }}
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card erp-kpi-card" style="border-left: 4px solid {{ $lowStockCount > 0 ? '#DC2626' : '#059669' }};">
            <div class="erp-kpi-icon-box" style="background: {{ $lowStockCount > 0 ? 'rgba(220, 38, 38, 0.12)' : 'rgba(5, 150, 105, 0.12)' }}; color: {{ $lowStockCount > 0 ? '#DC2626' : '#059669' }};">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Low Stock Alerts</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ $lowStockCount > 0 ? '#DC2626' : '#059669' }};">
                    {{ number_format($lowStockCount) }}
                    @if($outOfStockCount > 0)
                        <span style="font-size: 0.76rem; font-weight: normal; color: #DC2626; margin-left: 4px;">({{ $outOfStockCount }} Nil)</span>
                    @endif
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

    <!-- Inline Filter Toolbar -->
    <div class="card erp-table-filter-header" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem;">
        <form method="GET" action="{{ route('admin.inventory.stock-overview') }}" class="erp-filter-form" style="display: flex; gap: 0.85rem; flex-wrap: wrap; align-items: center; justify-content: space-between; width: 100%;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                <!-- Search -->
                <div class="erp-search-wrap" style="position: relative; min-width: 240px; flex: 1;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="text" name="search" class="form-control erp-search-input" placeholder="Search Product, Code, HSN, Batch..." value="{{ request('search') }}" style="padding-left: 2.25rem;">
                </div>

                <!-- Category Filter -->
                <select name="category" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 140px;">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <!-- Stock Health Status -->
                <select name="stock_status" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 140px;">
                    <option value="">All Stock Levels</option>
                    <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock (&gt; Min)</option>
                    <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                    <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock (Nil)</option>
                </select>

                <!-- Unit Filter -->
                <select name="unit" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 110px;">
                    <option value="">All Units</option>
                    @foreach($units as $u)
                        <option value="{{ $u }}" {{ request('unit') === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>

                <!-- Sort By -->
                <select name="sort_by" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 140px;">
                    <option value="name_asc" {{ request('sort_by') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    <option value="name_desc" {{ request('sort_by') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                    <option value="stock_desc" {{ request('sort_by') === 'stock_desc' ? 'selected' : '' }}>Highest Stock</option>
                    <option value="stock_asc" {{ request('sort_by') === 'stock_asc' ? 'selected' : '' }}>Lowest Stock</option>
                    <option value="value_desc" {{ request('sort_by') === 'value_desc' ? 'selected' : '' }}>Highest Valuation</option>
                </select>

                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 0.95rem;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(request()->anyFilled(['search', 'category', 'stock_status', 'unit', 'sort_by']))
                    <a href="{{ route('admin.inventory.stock-overview') }}" class="btn btn-outline" style="padding: 0.5rem 0.85rem;" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>

            <div class="erp-table-summary-count">
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 600; padding: 0.45rem 0.75rem; border-radius: 6px;">
                    Showing {{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }} of {{ $items->total() }} Products
                </span>
            </div>
        </form>
    </div>

    <!-- Main Edge-to-Edge Data Table Card -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">
        <div class="table-responsive">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Product &amp; Code</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Category</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right;">Current Stock</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right;">Min Alert Level</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right;">Purchase Cost (₹)</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right;">Stock Valuation (₹)</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center;">Stock Status</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $valuation = (float)$item->current_stock * (float)$item->purchase_rate;
                            $stockRatio = $item->min_stock_alert > 0 ? min(100, max(0, ($item->current_stock / $item->min_stock_alert) * 100)) : 100;
                            $isOut = (float)$item->current_stock <= 0;
                            $isLow = !$isOut && (float)$item->current_stock <= (float)$item->min_stock_alert;
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease; {{ $isOut ? 'background: rgba(254, 242, 242, 0.4);' : ($isLow ? 'background: rgba(255, 251, 235, 0.4);' : '') }}">
                            <!-- Product & Code -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $item->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.item.show', $item->id) }}" style="font-weight: 700; color: #0F172A; text-decoration: none; font-size: 0.92rem;">
                                            {{ $item->name }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: #64748B; margin-top: 2px; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                            <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                {{ $item->code }}
                                            </span>
                                            @if($item->batch_no)
                                                <span><i class="fa-solid fa-tag" style="font-size: 0.65rem; color: #94A3B8;"></i> {{ $item->batch_no }}</span>
                                            @endif
                                            @if($item->hsn_code)
                                                <span>HSN: {{ $item->hsn_code }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <span class="badge" style="background: rgba(91, 132, 30, 0.08); color: #5B841E; font-weight: 600; font-size: 0.76rem; padding: 4px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                    {{ $item->category ?: 'General' }}
                                </span>
                            </td>

                            <!-- Current Stock -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: {{ $isOut ? '#DC2626' : ($isLow ? '#D97706' : '#0F172A') }};">
                                    {{ number_format($item->current_stock, 2) }}
                                    <span style="font-size: 0.76rem; font-weight: normal; color: #64748B;">{{ $item->unit }}</span>
                                </div>
                                <!-- Health Bar -->
                                <div style="width: 100%; max-width: 90px; height: 4px; background: #E2E8F0; border-radius: 9999px; overflow: hidden; margin-top: 4px; margin-left: auto;">
                                    <div style="height: 100%; width: {{ min(100, $stockRatio) }}%; background: {{ $isOut ? '#DC2626' : ($isLow ? '#D97706' : '#059669') }};"></div>
                                </div>
                            </td>

                            <!-- Min Alert Level -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <span class="font-monospace" style="font-size: 0.88rem; color: #64748B;">
                                    {{ number_format($item->min_stock_alert, 2) }}
                                    <span style="font-size: 0.73rem;">{{ $item->unit }}</span>
                                </span>
                            </td>

                            <!-- Purchase Cost -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <span class="font-monospace" style="font-size: 0.88rem; color: #334155;">
                                    ₹{{ number_format($item->purchase_rate, 2) }}
                                </span>
                            </td>

                            <!-- Stock Valuation -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: #059669;">
                                    ₹{{ number_format($valuation, 2) }}
                                </div>
                            </td>

                            <!-- Stock Status -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                @if($isOut)
                                    <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-size: 0.74rem; font-weight: 700; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(220, 38, 38, 0.25);">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Out of Stock
                                    </span>
                                @elseif($isLow)
                                    <span class="badge" style="background: rgba(217, 119, 6, 0.12); color: #D97706; font-size: 0.74rem; font-weight: 700; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(217, 119, 6, 0.25);">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.74rem; font-weight: 700; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                                        <i class="fa-solid fa-circle-check me-1"></i> In Stock
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                    <a href="{{ route('admin.masters.item.show', $item->id) }}" class="btn btn-sm btn-icon" title="View Product Profile" style="color: #475569; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: #F8FAFC; border: 1px solid #E2E8F0;">
                                        <i class="fa-solid fa-eye" style="font-size: 0.82rem;"></i>
                                    </a>
                                    <a href="{{ route('admin.inventory.item-ledger', ['item_id' => $item->id]) }}" class="btn btn-sm btn-icon" title="View Item Stock Ledger Timeline" style="color: #2563EB; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.2);">
                                        <i class="fa-solid fa-clock-rotate-left" style="font-size: 0.82rem;"></i>
                                    </a>
                                    <a href="{{ route('admin.inventory.stock-adjustment', ['item_id' => $item->id]) }}" class="btn btn-sm btn-icon" title="Adjust Physical Stock" style="color: #5B841E; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: rgba(91, 132, 30, 0.08); border: 1px solid rgba(91, 132, 30, 0.2);">
                                        <i class="fa-solid fa-sliders" style="font-size: 0.82rem;"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3.5rem 1rem; color: #64748B;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div style="width: 68px; height: 68px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: #94A3B8; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-boxes-stacked"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">No Inventory Items Found</h4>
                                    <p style="font-size: 0.875rem; color: #64748B; max-width: 420px; margin-bottom: 1.25rem;">
                                        No items matching your search or filter parameters. Clear filters or add items in Item Master.
                                    </p>
                                    <a href="{{ route('admin.masters.item.create') }}" class="btn btn-primary">
                                        <i class="fa-solid fa-plus"></i> Add New Product to Inventory
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; background: #FFFFFF; display: flex; justify-content: flex-end;">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
