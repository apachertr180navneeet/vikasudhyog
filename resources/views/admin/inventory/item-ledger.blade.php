@extends('admin.layouts.app')

@section('title', ($selectedItem ? $selectedItem->name . ' - ' : '') . 'Item Stock Ledger - VIKAS UDHYOG ERP')
@section('page_code', 'inv-ledger')

@section('content')
<section class="view-section active" id="view-inv-ledger">

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
                <span class="erp-breadcrumb-active">Item Stock Ledger</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-clock-rotate-left text-primary"></i> Item Stock Ledger
            </h1>
            <p class="erp-page-subtitle">
                Item-wise chronological transaction register, audit movement timeline &amp; running stock balance.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Stock Ledger Statement">
                <i class="fa-solid fa-print"></i> Print Ledger
            </button>
            @if($selectedItem)
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Ledger to CSV Spreadsheet">
                    <i class="fa-solid fa-file-csv"></i> Export CSV
                </a>
                <a href="{{ route('admin.masters.item.show', $selectedItem->id) }}" class="btn btn-secondary" title="View 360° Item Master Dossier">
                    <i class="fa-solid fa-box-archive"></i> Item Dossier
                </a>
            @endif
            <a href="{{ route('admin.inventory.stock-overview') }}" class="btn btn-primary erp-btn-header-primary" title="View Catalog Stock Overview">
                <i class="fa-solid fa-boxes-stacked"></i> Stock Overview
            </a>
        </div>
    </div>

    @if(!$selectedItem)
        <!-- Empty State When No Items Exist -->
        <div class="card erp-main-card" style="padding: 4rem 1.5rem !important; text-align: center; border-radius: 16px;">
            <div style="width: 76px; height: 76px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; color: #94A3B8; margin: 0 auto 1.25rem;">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <h3 style="font-weight: 700; color: #1E293B; margin-bottom: 0.5rem; font-size: 1.25rem;">No Catalog Items Found</h3>
            <p style="color: #64748B; max-width: 480px; margin: 0 auto 1.5rem; font-size: 0.92rem; line-height: 1.5;">
                There are currently no items configured in your inventory database. Add an item in the Item Master first to track stock ledgers.
            </p>
            <a href="{{ route('admin.masters.item.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Item
            </a>
        </div>
    @else

        @php
            $isOutOfStock = (float)$closingBalance <= 0;
            $isLowStock = !$isOutOfStock && (float)$closingBalance <= (float)$selectedItem->min_stock_alert;
            $closingValuation = (float)$closingBalance * (float)$selectedItem->purchase_rate;
        @endphp

        <!-- Executive Product Hero Dossier & Item Switcher Banner -->
        <div class="card erp-product-banner-card" style="border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 18px -2px rgba(0, 0, 0, 0.04); margin-bottom: 1.5rem; padding: 1.25rem 1.6rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
                
                <!-- Left: Avatar + Title & Meta -->
                <div style="display: flex; align-items: center; gap: 1.15rem; min-width: 300px; flex: 1;">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 800; font-size: 1.15rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(91,132,30,0.25);">
                        {{ $selectedItem->initials }}
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #0F172A; margin: 0; letter-spacing: -0.01em;">
                                {{ $selectedItem->name }}
                            </h2>
                            <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0; font-size: 0.8rem; font-weight: 700;" title="Item SKU Code">
                                <i class="fa-solid fa-barcode me-1 text-muted"></i>{{ $selectedItem->code }}
                            </span>
                            <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-weight: 600; font-size: 0.76rem; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.22);" title="Commodity Category">
                                <i class="fa-solid fa-layer-group me-1"></i>{{ $selectedItem->category ?: 'General' }}
                            </span>
                            @if($selectedItem->company)
                                <span class="badge" style="background: #F8FAFC; color: #64748B; font-weight: 500; font-size: 0.75rem; padding: 3px 8px; border-radius: 9999px; border: 1px solid #E2E8F0;" title="Assigned Production Plant">
                                    <i class="fa-solid fa-building me-1 text-muted"></i>{{ $selectedItem->company->name }}
                                </span>
                            @endif
                        </div>

                        <!-- Micro Specs Pills Row -->
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.55rem; flex-wrap: wrap;">
                            <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.77rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                <span style="color: #64748B;">Unit:</span> <strong class="font-monospace" style="color: #0F172A;">{{ $selectedItem->unit }}</strong>
                            </span>
                            @if($selectedItem->hsn_code)
                                <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.77rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                    <span style="color: #64748B;">HSN:</span> <strong class="font-monospace" style="color: #0F172A;">{{ $selectedItem->hsn_code }}</strong>
                                </span>
                            @endif
                            @if($selectedItem->batch_no)
                                <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.77rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                    <span style="color: #64748B;">Batch:</span> <strong class="font-monospace" style="color: #0F172A;">{{ $selectedItem->batch_no }}</strong>
                                </span>
                            @endif
                            <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.77rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                <span style="color: #64748B;">Purchase Rate:</span> <strong class="font-monospace" style="color: #0F172A;">₹{{ number_format($selectedItem->purchase_rate, 2) }}</strong>
                            </span>
                            <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.77rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                <span style="color: #64748B;">Sale Rate:</span> <strong class="font-monospace" style="color: #0F172A;">₹{{ number_format($selectedItem->sale_rate, 2) }}</strong>
                            </span>
                            <span class="badge" style="background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0; font-size: 0.77rem; font-weight: 500; padding: 3px 8px; border-radius: 6px;">
                                <span style="color: #64748B;">Reorder Alert:</span> <strong class="font-monospace" style="color: #D97706;">{{ number_format($selectedItem->min_stock_alert, 2) }} {{ $selectedItem->unit }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right: Inline Product Switcher Dropdown + Status + Full Dossier Button -->
                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    
                    <!-- Sleek Product Switcher Select -->
                    <form action="{{ route('admin.inventory.item-ledger') }}" method="GET" style="margin: 0; min-width: 250px; max-width: 320px;">
                        @if(!empty($filters['from_date'])) <input type="hidden" name="from_date" value="{{ $filters['from_date'] }}"> @endif
                        @if(!empty($filters['to_date'])) <input type="hidden" name="to_date" value="{{ $filters['to_date'] }}"> @endif
                        @if(!empty($filters['type']) && $filters['type'] !== 'all') <input type="hidden" name="type" value="{{ $filters['type'] }}"> @endif
                        @if(!empty($filters['sort_order']) && $filters['sort_order'] !== 'asc') <input type="hidden" name="sort_order" value="{{ $filters['sort_order'] }}"> @endif
                        @if(!empty($viewMode)) <input type="hidden" name="view_mode" value="{{ $viewMode }}"> @endif

                        <div style="position: relative;">
                            <i class="fa-solid fa-arrows-rotate" style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.8rem; pointer-events: none; z-index: 2;"></i>
                            <select name="item_id" class="form-control" onchange="this.form.submit()" style="height: 38px; padding-left: 2rem; font-weight: 600; font-size: 0.85rem; color: #1E293B; border-color: #CBD5E1; border-radius: 8px; background-color: #FFFFFF; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.04);" title="Switch to another item to view its ledger">
                                @foreach($allItems as $itm)
                                    <option value="{{ $itm->id }}" {{ $selectedItem->id == $itm->id ? 'selected' : '' }}>
                                        [{{ $itm->code }}] {{ $itm->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    <!-- Stock Status Pill -->
                    @if($isOutOfStock)
                        <span class="badge" style="background: rgba(220, 38, 38, 0.1); color: #DC2626; font-size: 0.8rem; font-weight: 700; padding: 6px 12px; border-radius: 9999px; border: 1px solid rgba(220, 38, 38, 0.25);">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #DC2626; display: inline-block; margin-right: 5px;"></span> Out of Stock
                        </span>
                    @elseif($isLowStock)
                        <span class="badge" style="background: rgba(217, 119, 6, 0.1); color: #D97706; font-size: 0.8rem; font-weight: 700; padding: 6px 12px; border-radius: 9999px; border: 1px solid rgba(217, 119, 6, 0.25);">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #D97706; display: inline-block; margin-right: 5px;"></span> Low Stock
                        </span>
                    @else
                        <span class="badge" style="background: rgba(16, 185, 129, 0.1); color: #059669; font-size: 0.8rem; font-weight: 700; padding: 6px 12px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; display: inline-block; margin-right: 5px; box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);"></span> In Stock
                        </span>
                    @endif

                    <!-- Full Dossier Button -->
                    <a href="{{ route('admin.masters.item.show', $selectedItem->id) }}" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.82rem; font-weight: 600;" title="View Complete 360° Item Dossier">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Dossier
                    </a>
                </div>

            </div>
        </div>

        <!-- 4-Card KPI Summary Statistics Grid -->
        <div class="erp-kpi-grid">
            <!-- 1. Opening Stock Position -->
            <div class="card erp-kpi-card erp-kpi-primary">
                <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <div class="erp-kpi-label">
                        {{ !empty($filters['from_date']) ? 'Period Opening Stock' : 'Base Opening Stock' }}
                    </div>
                    <div class="erp-kpi-val font-monospace">
                        {{ number_format($openingBalance, 2) }}
                        <span style="font-size: 0.75rem; font-weight: 600; color: #64748B;">{{ $selectedItem->unit }}</span>
                    </div>
                    <div style="font-size: 0.74rem; color: #64748B; margin-top: 3px; font-weight: 500;">
                        Valuation: <span class="font-monospace" style="color: #334155;">₹{{ number_format($openingBalance * (float)$selectedItem->purchase_rate, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Total Inward Stock Movement (+ In) -->
            <div class="card erp-kpi-card erp-kpi-success">
                <div class="erp-kpi-icon-box erp-kpi-icon-success">
                    <i class="fa-solid fa-circle-arrow-down"></i>
                </div>
                <div>
                    <div class="erp-kpi-label">Total Inward (+ In)</div>
                    <div class="erp-kpi-val erp-kpi-val-success font-monospace">
                        +{{ number_format($totalInQty, 2) }}
                        <span style="font-size: 0.75rem; font-weight: 600; color: #059669;">{{ $selectedItem->unit }}</span>
                    </div>
                    <div style="font-size: 0.74rem; color: #059669; margin-top: 3px; font-weight: 600;">
                        {{ $inCount }} Receipts &bull; <span class="font-monospace">₹{{ number_format($totalInVal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- 3. Total Outward Stock Movement (- Out) -->
            <div class="card erp-kpi-card" style="border-left: 4px solid #DC2626;">
                <div class="erp-kpi-icon-box" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                    <i class="fa-solid fa-circle-arrow-up"></i>
                </div>
                <div>
                    <div class="erp-kpi-label">Total Outward (- Out)</div>
                    <div class="erp-kpi-val font-monospace" style="color: #DC2626;">
                        -{{ number_format($totalOutQty, 2) }}
                        <span style="font-size: 0.75rem; font-weight: 600; color: #DC2626;">{{ $selectedItem->unit }}</span>
                    </div>
                    <div style="font-size: 0.74rem; color: #DC2626; margin-top: 3px; font-weight: 600;">
                        {{ $outCount }} Dispatches &bull; <span class="font-monospace">₹{{ number_format($totalOutVal, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- 4. Closing Balance & Current Valuation -->
            <div class="card erp-kpi-card erp-kpi-purple" style="border-left: 4px solid {{ $isOutOfStock ? '#DC2626' : ($isLowStock ? '#D97706' : '#5B841E') }};">
                <div class="erp-kpi-icon-box" style="background: {{ $isOutOfStock ? 'rgba(220, 38, 38, 0.12)' : ($isLowStock ? 'rgba(217, 119, 6, 0.12)' : 'rgba(91, 132, 30, 0.12)') }}; color: {{ $isOutOfStock ? '#DC2626' : ($isLowStock ? '#D97706' : '#5B841E') }};">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <div>
                    <div class="erp-kpi-label">Current Stock Balance</div>
                    <div class="erp-kpi-val font-monospace" style="color: {{ $isOutOfStock ? '#DC2626' : ($isLowStock ? '#D97706' : '#0F172A') }};">
                        {{ number_format($closingBalance, 2) }}
                        <span style="font-size: 0.75rem; font-weight: 600; color: #64748B;">{{ $selectedItem->unit }}</span>
                    </div>
                    <div style="font-size: 0.74rem; color: #059669; font-weight: 700; margin-top: 3px;">
                        Valuation: <span class="font-monospace">₹{{ number_format($closingValuation, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- VIEW CONTAINER: EDGE-TO-EDGE TABLE REGISTER OR VISUAL TIMELINE    -->
        <!-- ================================================================= -->
        <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">
            
            <!-- Filter & Search Toolbar Header -->
            <div class="erp-table-filter-header" style="padding: 1rem 1.25rem; background: #FCFDFB; border-bottom: 1px solid #E2E8F0;">
                <form action="{{ route('admin.inventory.item-ledger') }}" method="GET" class="erp-filter-form" id="erp-ledger-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
                    
                    <!-- Hidden item_id and view_mode -->
                    <input type="hidden" name="item_id" value="{{ $selectedItem->id }}">
                    <input type="hidden" name="view_mode" id="view-mode-input" value="{{ $viewMode ?? 'table' }}">

                    <!-- Left: Inline Filter Controls -->
                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; flex: 1;">
                        
                        <!-- Search Box -->
                        <div class="erp-search-wrap" style="min-width: 210px; max-width: 290px;">
                            <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search voucher, party, invoice..." class="form-control erp-search-input">
                        </div>

                        <!-- Date Range: From Date -->
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">From:</span>
                            <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="form-control erp-filter-select" style="min-width: 135px; padding: 0 0.65rem; font-size: 0.83rem;">
                        </div>

                        <!-- Date Range: To Date -->
                        <div style="display: flex; align-items: center; gap: 5px;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">To:</span>
                            <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="form-control erp-filter-select" style="min-width: 135px; padding: 0 0.65rem; font-size: 0.83rem;">
                        </div>

                        <!-- Movement Type Filter -->
                        <select name="type" class="form-control erp-filter-select" onchange="this.form.submit()" style="min-width: 155px;">
                            <option value="all" {{ ($filters['type'] ?? 'all') === 'all' ? 'selected' : '' }}>All Movements</option>
                            <option value="inward" {{ ($filters['type'] ?? '') === 'inward' ? 'selected' : '' }}>Stock Inward (+ In)</option>
                            <option value="outward" {{ ($filters['type'] ?? '') === 'outward' ? 'selected' : '' }}>Stock Outward (- Out)</option>
                            <option value="purchase" {{ ($filters['type'] ?? '') === 'purchase' ? 'selected' : '' }}>Purchase (Bill)</option>
                            <option value="wb_purchase" {{ ($filters['type'] ?? '') === 'wb_purchase' ? 'selected' : '' }}>WB Purchase</option>
                            <option value="sale" {{ ($filters['type'] ?? '') === 'sale' ? 'selected' : '' }}>Sales (Bill)</option>
                            <option value="wb_sale" {{ ($filters['type'] ?? '') === 'wb_sale' ? 'selected' : '' }}>WB Sales</option>
                        </select>

                        <!-- Chronological Sort Order -->
                        <select name="sort_order" class="form-control erp-filter-select" onchange="this.form.submit()" style="min-width: 155px;">
                            <option value="asc" {{ ($sortOrder ?? 'asc') === 'asc' ? 'selected' : '' }}>Chronological (Oldest First)</option>
                            <option value="desc" {{ ($sortOrder ?? '') === 'desc' ? 'selected' : '' }}>Reverse (Newest First)</option>
                        </select>

                        <!-- Filter Submit & Clear -->
                        <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filter Criteria">
                            <i class="fa-solid fa-filter"></i> Filter
                        </button>
                        @if(!empty($filters['search']) || !empty($filters['from_date']) || !empty($filters['to_date']) || ($filters['type'] ?? 'all') !== 'all' || ($sortOrder ?? 'asc') !== 'asc')
                            <a href="{{ route('admin.inventory.item-ledger', ['item_id' => $selectedItem->id]) }}" class="btn btn-outline erp-btn-filter-clear" title="Reset All Filters">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>

                    <!-- Right: View Mode Segmented Switcher & Record Counter -->
                    <div style="display: flex; align-items: center; gap: 0.85rem; flex-shrink: 0;">
                        <!-- Segmented Switcher Pill -->
                        <div class="erp-view-segmented-wrap" style="display: inline-flex; background: #EEF2F6; padding: 3px; border-radius: 9px; border: 1px solid #E2E8F0;">
                            <button type="button" class="erp-segmented-btn {{ ($viewMode ?? 'table') === 'table' ? 'active' : '' }}" onclick="switchLedgerView('table')" style="border: none; border-radius: 7px; padding: 5px 12px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px; {{ ($viewMode ?? 'table') === 'table' ? 'background: #5B841E; color: #FFFFFF; box-shadow: 0 2px 6px rgba(91,132,30,0.25);' : 'background: transparent; color: #64748B;' }}">
                                <i class="fa-solid fa-table-list"></i> Table Register
                            </button>
                            <button type="button" class="erp-segmented-btn {{ ($viewMode ?? 'table') === 'timeline' ? 'active' : '' }}" onclick="switchLedgerView('timeline')" style="border: none; border-radius: 7px; padding: 5px 12px; font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: inline-flex; align-items: center; gap: 6px; {{ ($viewMode ?? 'table') === 'timeline' ? 'background: #5B841E; color: #FFFFFF; box-shadow: 0 2px 6px rgba(91,132,30,0.25);' : 'background: transparent; color: #64748B;' }}">
                                <i class="fa-solid fa-timeline"></i> Visual Timeline
                            </button>
                        </div>

                        <!-- Summary Records Badge -->
                        <div class="erp-table-summary-count" style="margin: 0; font-size: 0.8rem;">
                            <strong>{{ count($movements) }}</strong> movements
                        </div>
                    </div>
                </form>
            </div>

            <!-- ============================================== -->
            <!-- 1. VIEW MODE: DATA TABLE REGISTER              -->
            <!-- ============================================== -->
            <div id="ledger-table-view" style="{{ ($viewMode ?? 'table') === 'table' ? 'display: block;' : 'display: none;' }}">
                
                <!-- Quick Table Status Subheader Strip -->
                <div style="padding: 0.85rem 1.4rem; background: #FFFFFF; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="font-size: 0.85rem; font-weight: 600; color: #334155; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-list-check text-primary"></i>
                        <span>Stock Register Log: <strong style="color: #0F172A;">{{ $selectedItem->name }}</strong> ({{ $selectedItem->code }})</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.8rem;">
                        <span style="color: #059669; font-weight: 700;">
                            <i class="fa-solid fa-circle-arrow-down me-1"></i> Inward: <span class="font-monospace">{{ number_format($totalInQty, 2) }}</span> {{ $selectedItem->unit }}
                        </span>
                        <span style="color: #CBD5E1;">&bull;</span>
                        <span style="color: #DC2626; font-weight: 700;">
                            <i class="fa-solid fa-circle-arrow-up me-1"></i> Outward: <span class="font-monospace">{{ number_format($totalOutQty, 2) }}</span> {{ $selectedItem->unit }}
                        </span>
                        <span style="color: #CBD5E1;">&bull;</span>
                        <span style="color: #0F172A; font-weight: 800;">
                            <i class="fa-solid fa-wallet me-1 text-primary"></i> Closing: <span class="font-monospace">{{ number_format($closingBalance, 2) }}</span> {{ $selectedItem->unit }}
                        </span>
                    </div>
                </div>

                <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
                    <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 125px;">Date &amp; Time</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 165px;">Transaction Type</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; width: 150px;">Voucher / Ref No</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Party / Counterparty</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 130px;">Stock In (+)</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 130px;">Stock Out (-)</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 150px;">Running Balance</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 110px;">Rate (₹)</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right; width: 125px;">Amount (₹)</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center; width: 75px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Opening Balance Row (Shown on Top in Chronological Ascending View) -->
                            @if(($sortOrder ?? 'asc') === 'asc' && $openingRow)
                                <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div class="font-monospace" style="font-weight: 700; color: #334155; font-size: 0.85rem;">
                                            {{ \Carbon\Carbon::parse($openingRow['date'])->format('d M Y') }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #94A3B8; margin-top: 1px;">Baseline</div>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-weight: 700; font-size: 0.75rem; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.25);">
                                            <i class="fa-solid fa-box-open me-1"></i> {{ $openingRow['type_label'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <span class="badge font-monospace" style="background: #FFFFFF; color: #64748B; font-weight: 700; border: 1px solid #E2E8F0; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                            {{ $openingRow['voucher_no'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div style="font-size: 0.85rem; color: #334155; font-weight: 600;">
                                            {{ $openingRow['party_name'] }}
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748B;">Item Master Opening Ledger</div>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-weight: 700; color: #059669; font-size: 0.92rem;">
                                            +{{ number_format($openingRow['in_qty'], 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span style="color: #94A3B8;">-</span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-weight: 800; color: #0F172A; font-size: 0.95rem;">
                                            {{ number_format($openingRow['running_balance'], 2) }}
                                        </span>
                                        <span style="font-size: 0.74rem; color: #64748B; font-weight: 500;">{{ $selectedItem->unit }}</span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-size: 0.85rem; color: #475569;">
                                            ₹{{ number_format($openingRow['rate'], 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-size: 0.86rem; font-weight: 700; color: #0F172A;">
                                            ₹{{ number_format($openingRow['amount'], 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                        <span style="color: #CBD5E1; font-size: 1.1rem;">&bull;</span>
                                    </td>
                                </tr>
                            @endif

                            <!-- Movement Rows -->
                            @forelse($movements as $m)
                                @php
                                    $isInward = $m['in_qty'] > 0;
                                    $typeBadgeStyles = match($m['type']) {
                                        'purchase' => 'background: rgba(91, 132, 30, 0.1); color: #5B841E; border: 1px solid rgba(91, 132, 30, 0.25);',
                                        'wb_purchase' => 'background: rgba(2, 132, 199, 0.1); color: #0284C7; border: 1px solid rgba(2, 132, 199, 0.25);',
                                        'sale' => 'background: rgba(220, 38, 38, 0.1); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.25);',
                                        'wb_sale' => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.25);',
                                        default => 'background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;',
                                    };
                                    $typeIcon = match($m['type']) {
                                        'purchase' => 'fa-truck-ramp-box',
                                        'wb_purchase' => 'fa-scale-balanced',
                                        'sale' => 'fa-file-invoice-dollar',
                                        'wb_sale' => 'fa-truck-fast',
                                        default => 'fa-arrow-right-arrow-left',
                                    };
                                @endphp
                                <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                    <!-- Date & Time -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div class="font-monospace" style="font-weight: 700; color: #1E293B; font-size: 0.86rem;">
                                            {{ \Carbon\Carbon::parse($m['date'])->format('d M Y') }}
                                        </div>
                                        <div style="font-size: 0.72rem; color: #94A3B8; margin-top: 2px;">
                                            {{ \Carbon\Carbon::parse($m['datetime'])->format('h:i A') }}
                                        </div>
                                    </td>

                                    <!-- Transaction Type -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <span class="badge" style="{{ $typeBadgeStyles }} font-weight: 700; font-size: 0.75rem; padding: 4px 10px; border-radius: 9999px;">
                                            <i class="fa-solid {{ $typeIcon }} me-1"></i> {{ $m['type_label'] }}
                                        </span>
                                    </td>

                                    <!-- Voucher / Ref No -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                                            <a href="{{ $m['url'] }}" style="font-weight: 700; color: #2563EB; text-decoration: none; font-size: 0.88rem;" class="font-monospace" title="Click to view full voucher record">
                                                {{ $m['voucher_no'] }}
                                            </a>
                                        </div>
                                        @if($m['invoice_no'])
                                            <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                                                Inv: <span class="font-monospace">{{ $m['invoice_no'] }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Party / Counterparty -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div style="font-weight: 600; color: #0F172A; font-size: 0.88rem;">
                                            {{ $m['party_name'] }}
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 1px;">
                                            <span class="badge" style="background: #F8FAFC; color: #64748B; font-weight: 600; font-size: 0.68rem; padding: 1px 6px; border: 1px solid #E2E8F0; border-radius: 4px;">
                                                {{ $m['party_type'] }}
                                            </span>
                                            @if($m['batch_no'])
                                                <span style="margin-left: 5px;" class="font-monospace"><i class="fa-solid fa-tag" style="font-size: 0.65rem;"></i> {{ $m['batch_no'] }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Stock In (+ In) -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        @if($m['in_qty'] > 0)
                                            <span class="font-monospace" style="font-weight: 800; color: #059669; font-size: 0.94rem;">
                                                +{{ number_format($m['in_qty'], 2) }}
                                            </span>
                                        @else
                                            <span style="color: #CBD5E1;">-</span>
                                        @endif
                                    </td>

                                    <!-- Stock Out (- Out) -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        @if($m['out_qty'] > 0)
                                            <span class="font-monospace" style="font-weight: 800; color: #DC2626; font-size: 0.94rem;">
                                                -{{ number_format($m['out_qty'], 2) }}
                                            </span>
                                        @else
                                            <span style="color: #CBD5E1;">-</span>
                                        @endif
                                    </td>

                                    <!-- Running Balance -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-weight: 800; color: #0F172A; font-size: 0.96rem;">
                                            {{ number_format($m['running_balance'], 2) }}
                                        </span>
                                        <span style="font-size: 0.74rem; color: #64748B; font-weight: 500;">{{ $selectedItem->unit }}</span>
                                    </td>

                                    <!-- Rate (₹) -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-size: 0.85rem; color: #475569;">
                                            ₹{{ number_format($m['rate'], 2) }}
                                        </span>
                                    </td>

                                    <!-- Amount (₹) -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-weight: 700; color: #0F172A; font-size: 0.88rem;">
                                            ₹{{ number_format($m['amount'], 2) }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                        <div class="erp-actions-cell" style="justify-content: center;">
                                            <a href="{{ $m['url'] }}" class="erp-table-action-icon" title="View Transaction Voucher">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 3.5rem 1rem; color: #64748B;">
                                        <div style="width: 58px; height: 58px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                            <i class="fa-solid fa-inbox"></i>
                                        </div>
                                        <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.1rem;">No Transaction Movements Found</h4>
                                        <p style="font-size: 0.85rem; color: #64748B; margin: 0; max-width: 440px; margin: 0 auto;">
                                            No stock transactions match your active date, movement type, or search filters for <strong>{{ $selectedItem->name }}</strong>.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse

                            <!-- Opening Balance Row (Shown at Bottom in Descending Newest First View) -->
                            @if(($sortOrder ?? 'asc') === 'desc' && $openingRow)
                                <tr style="background: #F8FAFC; border-top: 1px solid #E2E8F0;">
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div class="font-monospace" style="font-weight: 700; color: #334155; font-size: 0.85rem;">
                                            {{ \Carbon\Carbon::parse($openingRow['date'])->format('d M Y') }}
                                        </div>
                                        <div style="font-size: 0.7rem; color: #94A3B8; margin-top: 1px;">Baseline</div>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-weight: 700; font-size: 0.75rem; padding: 4px 10px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.25);">
                                            <i class="fa-solid fa-box-open me-1"></i> {{ $openingRow['type_label'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <span class="badge font-monospace" style="background: #FFFFFF; color: #64748B; font-weight: 700; border: 1px solid #E2E8F0; font-size: 0.78rem; padding: 3px 8px; border-radius: 6px;">
                                            {{ $openingRow['voucher_no'] }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                        <div style="font-size: 0.85rem; color: #334155; font-weight: 600;">
                                            {{ $openingRow['party_name'] }}
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748B;">Item Master Opening Ledger</div>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-weight: 700; color: #059669; font-size: 0.92rem;">
                                            +{{ number_format($openingRow['in_qty'], 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span style="color: #94A3B8;">-</span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-weight: 800; color: #0F172A; font-size: 0.95rem;">
                                            {{ number_format($openingRow['running_balance'], 2) }}
                                        </span>
                                        <span style="font-size: 0.74rem; color: #64748B; font-weight: 500;">{{ $selectedItem->unit }}</span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-size: 0.85rem; color: #475569;">
                                            ₹{{ number_format($openingRow['rate'], 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                        <span class="font-monospace" style="font-size: 0.86rem; font-weight: 700; color: #0F172A;">
                                            ₹{{ number_format($openingRow['amount'], 2) }}
                                        </span>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                        <span style="color: #CBD5E1; font-size: 1.1rem;">&bull;</span>
                                    </td>
                                </tr>
                            @endif
                        </tbody>

                        <!-- Table Summary Footer -->
                        <tfoot>
                            <tr style="background: #F8FAFC; border-top: 2px solid #CBD5E1; font-weight: 700;">
                                <td colspan="4" style="padding: 1rem 1.15rem; color: #1E293B; font-size: 0.88rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Total Period Movement &bull; Closing Balance
                                </td>
                                <!-- Total Inward -->
                                <td style="padding: 1rem 1.15rem; text-align: right; color: #059669; font-size: 0.96rem;" class="font-monospace">
                                    +{{ number_format($totalInQty, 2) }}
                                </td>
                                <!-- Total Outward -->
                                <td style="padding: 1rem 1.15rem; text-align: right; color: #DC2626; font-size: 0.96rem;" class="font-monospace">
                                    -{{ number_format($totalOutQty, 2) }}
                                </td>
                                <!-- Final Balance -->
                                <td style="padding: 1rem 1.15rem; text-align: right; color: #0F172A; font-size: 1.05rem;" class="font-monospace">
                                    {{ number_format($closingBalance, 2) }} <span style="font-size: 0.78rem; font-weight: 600; color: #64748B;">{{ $selectedItem->unit }}</span>
                                </td>
                                <td style="padding: 1rem 1.15rem; text-align: right; color: #64748B; font-size: 0.78rem;">
                                    Avg Rate
                                </td>
                                <!-- Total Net Valuation -->
                                <td style="padding: 1rem 1.15rem; text-align: right; color: #0F172A; font-size: 0.96rem;" class="font-monospace">
                                    ₹{{ number_format($closingValuation, 2) }}
                                </td>
                                <td style="padding: 1rem 1.15rem;"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- 2. VIEW MODE: VISUAL IN/OUT TIMELINE STREAM    -->
            <!-- ============================================== -->
            <div id="ledger-timeline-view" style="{{ ($viewMode ?? 'table') === 'timeline' ? 'display: block;' : 'display: none;' }}; padding: 1.75rem 2rem;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 1.1rem; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin: 0;">
                            <i class="fa-solid fa-timeline text-primary me-2"></i> Chronological Movement Flow (Inward &amp; Outward Stream)
                        </h3>
                        <p style="font-size: 0.82rem; color: #64748B; margin: 3px 0 0;">
                            Visual sequential audit trail tracking each physical inventory inflow and outflow event.
                        </p>
                    </div>
                    <div style="display: flex; gap: 0.85rem; font-size: 0.78rem; align-items: center;">
                        <span style="display: flex; align-items: center; gap: 5px; color: #059669; font-weight: 600;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #059669; display: inline-block;"></span> Stock Inward
                        </span>
                        <span style="display: flex; align-items: center; gap: 5px; color: #DC2626; font-weight: 600;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #DC2626; display: inline-block;"></span> Stock Outward
                        </span>
                        <span style="display: flex; align-items: center; gap: 5px; color: #5B841E; font-weight: 600;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #5B841E; display: inline-block;"></span> Opening Balance
                        </span>
                    </div>
                </div>

                <!-- Timeline Stream -->
                <div class="erp-timeline-stream" style="position: relative; padding-left: 2.5rem;">
                    
                    <!-- Vertical Track Line -->
                    <div style="position: absolute; left: 18px; top: 15px; bottom: 25px; width: 2px; background: #E2E8F0; border-radius: 2px;"></div>

                    <!-- 1. Opening Node (If Ascending) -->
                    @if(($sortOrder ?? 'asc') === 'asc' && $openingRow)
                        <div class="timeline-step" style="position: relative; margin-bottom: 2rem;">
                            <!-- Node Icon Circle -->
                            <div style="position: absolute; left: -2.5rem; top: 0; width: 38px; height: 38px; border-radius: 50%; background: #F4F8EE; border: 3px solid #5B841E; color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; box-shadow: 0 2px 8px rgba(91,132,30,0.25); z-index: 2;">
                                <i class="fa-solid fa-box-open"></i>
                            </div>

                            <!-- Card Body -->
                            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #5B841E; border-radius: 12px; padding: 1.1rem 1.35rem; margin-left: 0.85rem; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <div>
                                        <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-weight: 700; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">
                                            {{ $openingRow['type_label'] }}
                                        </span>
                                        <span class="font-monospace" style="font-size: 0.85rem; font-weight: 700; color: #1E293B; margin-left: 0.5rem;">
                                            {{ $openingRow['voucher_no'] }}
                                        </span>
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.8rem; color: #64748B; font-weight: 600;">
                                        {{ \Carbon\Carbon::parse($openingRow['date'])->format('d M Y') }}
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.65rem; flex-wrap: wrap; gap: 0.75rem;">
                                    <div style="font-size: 0.84rem; color: #475569;">
                                        Party: <strong>{{ $openingRow['party_name'] }}</strong>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <span class="font-monospace" style="font-size: 0.92rem; font-weight: 700; color: #059669;">
                                            Opening Qty: +{{ number_format($openingRow['in_qty'], 2) }} {{ $selectedItem->unit }}
                                        </span>
                                        <span class="font-monospace" style="font-size: 0.95rem; font-weight: 800; color: #0F172A; background: #FFFFFF; padding: 4px 11px; border-radius: 7px; border: 1px solid #E2E8F0;">
                                            Balance: {{ number_format($openingRow['running_balance'], 2) }} {{ $selectedItem->unit }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Timeline Movement Nodes -->
                    @forelse($movements as $m)
                        @php
                            $isIn = $m['in_qty'] > 0;
                            $nodeBorderColor = $isIn ? '#059669' : '#DC2626';
                            $nodeBg = $isIn ? '#ECFDF5' : '#FEF2F2';
                            $nodeIcon = $isIn ? 'fa-arrow-down-left' : 'fa-arrow-up-right';
                        @endphp
                        <div class="timeline-step" style="position: relative; margin-bottom: 2rem;">
                            <!-- Node Circle -->
                            <div style="position: absolute; left: -2.5rem; top: 0; width: 38px; height: 38px; border-radius: 50%; background: {{ $nodeBg }}; border: 3px solid {{ $nodeBorderColor }}; color: {{ $nodeBorderColor }}; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; box-shadow: 0 2px 8px rgba(0,0,0,0.06); z-index: 2;">
                                <i class="fa-solid {{ $nodeIcon }}"></i>
                            </div>

                            <!-- Card Body -->
                            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-left: 4px solid {{ $nodeBorderColor }}; border-radius: 12px; padding: 1.15rem 1.4rem; margin-left: 0.85rem; box-shadow: 0 3px 10px rgba(0,0,0,0.03);">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                                        <span class="badge" style="background: {{ $isIn ? 'rgba(5, 150, 105, 0.1)' : 'rgba(220, 38, 38, 0.1)' }}; color: {{ $nodeBorderColor }}; font-weight: 700; font-size: 0.75rem; padding: 3px 9px; border-radius: 6px;">
                                            {{ $m['type_label'] }}
                                        </span>
                                        <a href="{{ $m['url'] }}" class="font-monospace" style="font-weight: 700; color: #2563EB; font-size: 0.9rem; text-decoration: none;">
                                            {{ $m['voucher_no'] }}
                                        </a>
                                        @if($m['invoice_no'])
                                            <span style="font-size: 0.75rem; color: #64748B;">
                                                (Inv: <strong class="font-monospace">{{ $m['invoice_no'] }}</strong>)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.8rem; color: #64748B; font-weight: 600;">
                                        <i class="fa-regular fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($m['date'])->format('d M Y') }} &bull; {{ \Carbon\Carbon::parse($m['datetime'])->format('h:i A') }}
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem; flex-wrap: wrap; gap: 0.75rem; border-top: 1px dashed #F1F5F9; padding-top: 0.75rem;">
                                    <div>
                                        <div style="font-size: 0.86rem; color: #1E293B; font-weight: 600;">
                                            <i class="fa-solid {{ $isIn ? 'fa-building-wheat' : 'fa-store' }} me-1" style="color: #64748B;"></i> {{ $m['party_name'] }}
                                            <span class="badge" style="background: #F8FAFC; color: #64748B; font-size: 0.68rem; margin-left: 5px; padding: 2px 6px;">{{ $m['party_type'] }}</span>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #64748B; margin-top: 2px;">
                                            Rate: <span class="font-monospace">₹{{ number_format($m['rate'], 2) }}</span> &bull; 
                                            Amount: <span class="font-monospace" style="color: #0F172A; font-weight: 700;">₹{{ number_format($m['amount'], 2) }}</span>
                                            @if($m['batch_no'])
                                                &bull; Batch: <span class="font-monospace">{{ $m['batch_no'] }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                                        <!-- Movement Delta -->
                                        <div style="text-align: right;">
                                            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B; font-weight: 700;">
                                                {{ $isIn ? 'Stock Inward' : 'Stock Outward' }}
                                            </div>
                                            <div class="font-monospace" style="font-size: 1.05rem; font-weight: 800; color: {{ $nodeBorderColor }};">
                                                {{ $isIn ? '+' : '-' }}{{ number_format($isIn ? $m['in_qty'] : $m['out_qty'], 2) }} {{ $selectedItem->unit }}
                                            </div>
                                        </div>

                                        <!-- Running Balance Chip -->
                                        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 6px 12px; text-align: right;">
                                            <div style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.04em; color: #64748B; font-weight: 700;">
                                                Running Balance
                                            </div>
                                            <div class="font-monospace" style="font-size: 0.98rem; font-weight: 800; color: #0F172A;">
                                                {{ number_format($m['running_balance'], 2) }} {{ $selectedItem->unit }}
                                            </div>
                                        </div>

                                        <!-- Quick Action -->
                                        <a href="{{ $m['url'] }}" class="erp-table-action-icon" title="View Source Voucher" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 3rem 1rem; text-align: center; color: #64748B;">
                            <i class="fa-solid fa-inbox" style="font-size: 2rem; color: #CBD5E1; margin-bottom: 0.5rem; display: block;"></i>
                            <p style="margin: 0; font-size: 0.92rem;">No timeline movements recorded for this item under the selected filters.</p>
                        </div>
                    @endforelse

                    <!-- 3. Final Closing Position Marker -->
                    <div class="timeline-step" style="position: relative; margin-top: 2rem;">
                        <div style="position: absolute; left: -2.5rem; top: 0; width: 38px; height: 38px; border-radius: 50%; background: #0F172A; border: 3px solid #334155; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.95rem; box-shadow: 0 2px 8px rgba(0,0,0,0.2); z-index: 2;">
                            <i class="fa-solid fa-flag-checkered"></i>
                        </div>
                        <div style="background: #0F172A; color: #FFFFFF; border-radius: 12px; padding: 1.15rem 1.5rem; margin-left: 0.85rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 16px rgba(15,23,42,0.15);">
                            <div>
                                <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8; font-weight: 700;">
                                    Final Closing Stock Position
                                </div>
                                <div style="font-size: 1.15rem; font-weight: 700; color: #FFFFFF; margin-top: 2px;">
                                    {{ $selectedItem->name }} <span class="badge font-monospace" style="background: rgba(255,255,255,0.15); color: #E2E8F0; font-size: 0.78rem; font-weight: 600; margin-left: 6px;">{{ $selectedItem->code }}</span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div class="font-monospace" style="font-size: 1.35rem; font-weight: 800; color: #34D399;">
                                    {{ number_format($closingBalance, 2) }} {{ $selectedItem->unit }}
                                </div>
                                <div style="font-size: 0.78rem; color: #94A3B8; margin-top: 2px;">
                                    Inventory Valuation: <strong class="font-monospace" style="color: #F8FAFC;">₹{{ number_format($closingValuation, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @endif

</section>

<!-- View Mode Switch Script -->
<script>
function switchLedgerView(mode) {
    var tableView = document.getElementById('ledger-table-view');
    var timelineView = document.getElementById('ledger-timeline-view');
    var modeInput = document.getElementById('view-mode-input');
    var buttons = document.querySelectorAll('.erp-segmented-btn');

    if (mode === 'timeline') {
        if (tableView) tableView.style.display = 'none';
        if (timelineView) timelineView.style.display = 'block';
        if (modeInput) modeInput.value = 'timeline';
    } else {
        if (tableView) tableView.style.display = 'block';
        if (timelineView) timelineView.style.display = 'none';
        if (modeInput) modeInput.value = 'table';
    }

    // Toggle button active styling
    buttons.forEach(function(btn) {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = '#64748B';
        btn.style.boxShadow = 'none';
    });

    event.currentTarget.classList.add('active');
    event.currentTarget.style.background = '#5B841E';
    event.currentTarget.style.color = '#FFFFFF';
    event.currentTarget.style.boxShadow = '0 2px 6px rgba(91,132,30,0.25)';
}
</script>

<!-- Print Stylesheet -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #view-inv-ledger, #view-inv-ledger * {
        visibility: visible;
    }
    #view-inv-ledger {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .sidebar, .navbar, .erp-page-top-bar .erp-header-actions, .erp-table-filter-header, .erp-hero-dossier-card form, .erp-view-segmented-wrap, .erp-table-action-icon {
        display: none !important;
    }
    .erp-main-card {
        box-shadow: none !important;
        border: 1px solid #CBD5E1 !important;
    }
}
</style>
@endsection
