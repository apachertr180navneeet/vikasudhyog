@extends('admin.layouts.app')

@section('title', $item->name . ' (' . $item->code . ') - Item Dossier - VIKAS UDHYOG ERP')
@section('page_code', 'master-item')

@section('content')
<section class="view-section active" id="view-item-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.item') }}">Item Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $item->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-boxes-packing text-primary"></i> Product &amp; Material Dossier
            </h1>
            <p class="erp-page-subtitle">
                Complete specifications, commercial rates, HSN tax classifications &amp; inventory valuation ledger.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Item Dossier">
                <i class="fa-solid fa-print"></i> Print Dossier
            </button>
            <a href="{{ route('admin.masters.item.edit', $item->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Item
            </a>
            <a href="{{ route('admin.masters.item') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
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

    <!-- Executive Profile Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ $item->initials }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $item->name }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Code Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Item SKU Code">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $item->code }}
                        </span>

                        <!-- Category Pill -->
                        <span class="erp-profile-role-pill" title="Commodity Category">
                            <i class="fa-solid fa-layer-group me-1"></i>{{ $item->category }}
                        </span>

                        <!-- Plant Binding Pill -->
                        @if($item->company)
                            <span class="erp-profile-role-pill" title="Assigned Production Plant">
                                <i class="fa-solid fa-building me-1"></i>{{ $item->company->name }} ({{ $item->company->code }})
                            </span>
                        @else
                            <span class="erp-profile-role-pill" title="Serviced Across All Plants">
                                <i class="fa-solid fa-globe me-1"></i>All Plants (Global Stock)
                            </span>
                        @endif

                        <!-- HSN Pill -->
                        @if($item->hsn_code)
                            <span class="erp-profile-username-pill font-monospace" title="Harmonized Tariff Code">
                                <i class="fa-solid fa-stamp me-1"></i>HSN: {{ $item->hsn_code }}
                            </span>
                        @endif

                        <!-- Batch Pill -->
                        @if($item->batch_no)
                            <span class="erp-profile-role-pill font-monospace" title="Lot / Batch Tracking">
                                <i class="fa-solid fa-tag me-1"></i>{{ $item->batch_no }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Action & Status Column -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <!-- Status Toggle Button Form -->
                <form action="{{ route('admin.masters.item.toggle-status', $item->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    @if($item->status === 'active')
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate Item">
                            <span class="erp-profile-status-dot-active"></span> Status: Active
                        </button>
                    @else
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate Item">
                            <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                        </button>
                    @endif
                </form>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.masters.item.edit', $item->id) }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Modify Item Record">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Specifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI 4-Card Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <!-- Available On-Hand Stock -->
        <div class="card erp-kpi-card" style="border-left: 4px solid {{ $item->is_low_stock ? '#EF4444' : '#10B981' }};">
            <div class="erp-kpi-icon-box" style="background: {{ $item->is_low_stock ? 'rgba(239, 68, 68, 0.12)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ $item->is_low_stock ? '#DC2626' : '#059669' }};">
                <i class="fa-solid {{ $item->is_low_stock ? 'fa-triangle-exclamation' : 'fa-warehouse' }}"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Available On-Hand Stock</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ $item->is_low_stock ? '#DC2626' : '#0F172A' }};">
                    {{ number_format($item->current_stock, 2) }} <span style="font-size: 0.82rem; color: #64748B;">{{ $item->unit }}</span>
                </div>
            </div>
        </div>

        <!-- Default Purchase Cost -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-arrow-down-long"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Default Purchase Rate</div>
                <div class="erp-kpi-val font-monospace">
                    ₹{{ number_format($item->purchase_rate, 2) }} <span style="font-size: 0.75rem; color: #64748B;">/ {{ $item->unit }}</span>
                </div>
            </div>
        </div>

        <!-- Standard Selling Price -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-arrow-up-long"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Standard Selling Rate</div>
                <div class="erp-kpi-val font-monospace erp-kpi-val-success">
                    ₹{{ number_format($item->sale_rate, 2) }} <span style="font-size: 0.75rem; color: #64748B;">/ {{ $item->unit }}</span>
                </div>
            </div>
        </div>

        <!-- Inventory Valuation -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Inventory Valuation</div>
                <div class="erp-kpi-val font-monospace">
                    ₹{{ number_format($item->stock_valuation, 2) }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Profile Details Breakdown Grid -->
    <div class="erp-profile-details-grid">
        <!-- Main Column (Left) -->
        <div class="erp-form-main-col">

            <!-- 1. Product Specifications & Material Taxonomy -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-boxes-stacked text-primary"></i> Product Specifications &amp; Material Classification
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">Item SKU Code</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ $item->code }}</span>
                            <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $item->code }}', 'Item Code copied!')" title="Copy Code">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Item Category</div>
                        <div class="erp-profile-detail-val">
                            <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                <i class="fa-solid fa-layer-group me-1"></i>{{ $item->category }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Primary Unit of Measure</div>
                        <div class="erp-profile-detail-val">
                            <span class="badge" style="background: #F1F5F9; color: #334155; font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                {{ $item->unit }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">HSN / Tariff Code</div>
                        <div class="erp-profile-detail-val">
                            @if($item->hsn_code)
                                <span class="font-monospace text-dark fw-bold">{{ $item->hsn_code }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $item->hsn_code }}', 'HSN code copied!')" title="Copy HSN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Not Specified</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Applicable GST Rate</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ number_format($item->gst_rate, 2) }}%</span>
                            <span style="font-size: 0.78rem; color: #64748B; margin-left: 0.35rem;">
                                {{ $item->gst_rate == 5 ? '(Henna/Agri Slab)' : '' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Production Plant Assignment</div>
                        <div class="erp-profile-detail-val">
                            @if($item->company)
                                <a href="{{ route('admin.masters.company.show', $item->company->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                    <i class="fa-solid fa-building me-1"></i>{{ $item->company->name }} ({{ $item->company->code }})
                                </a>
                            @else
                                <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.78rem;">
                                    <i class="fa-solid fa-globe me-1"></i>All Plants (Global Stock)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Inventory Management & Stock Controls -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-warehouse text-primary"></i> Inventory Controls &amp; Batch Tracking
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">Physical Stock On-Hand</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold" style="font-size: 1.05rem; color: {{ $item->is_low_stock ? '#DC2626' : '#0F172A' }};">
                                {{ number_format($item->current_stock, 2) }} {{ $item->unit }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Reorder Alert Threshold</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">
                                {{ number_format($item->min_stock_alert, 2) }} {{ $item->unit }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Initial Opening Stock</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">
                                {{ number_format($item->opening_stock, 2) }} {{ $item->unit }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Stock Health Status</div>
                        <div class="erp-profile-detail-val">
                            @if($item->is_low_stock)
                                <span class="badge" style="background: #FEE2E2; color: #DC2626; font-size: 0.78rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #FECACA;">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock Alert (Reorder Required)
                                </span>
                            @else
                                <span class="badge" style="background: #ECFDF5; color: #059669; font-size: 0.78rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #A7F3D0;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Stock Healthy (Above Threshold)
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Current Batch / Lot Identifier</div>
                        <div class="erp-profile-detail-val">
                            @if($item->batch_no)
                                <span class="font-monospace fw-bold text-dark">{{ $item->batch_no }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $item->batch_no }}', 'Batch number copied!')" title="Copy Batch">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">No Batch Assigned</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Total Inventory Valuation</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-primary" style="font-size: 1.05rem;">
                                ₹{{ number_format($item->stock_valuation, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Storage Specifications & Notes -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clipboard-list text-primary"></i> Storage Specifications &amp; Quality Notes
                </div>
                <div class="p-3">
                    @if($item->notes)
                        <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.9rem; line-height: 1.6; color: #334155;">
                            {{ $item->notes }}
                        </div>
                    @else
                        <div class="text-muted p-2" style="font-size: 0.88rem; font-style: italic;">
                            No special warehouse storage guidelines, moisture control specifications or handling instructions recorded for this item.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="erp-form-side-col">
            <!-- 1. Pricing, Margins & Commercial Spread -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-indian-rupee-sign text-primary"></i> Pricing &amp; Commercial Margins
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Default Purchase Rate</span>
                        <strong class="font-monospace text-dark">₹{{ number_format($item->purchase_rate, 2) }} / {{ $item->unit }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Standard Selling Rate</span>
                        <strong class="font-monospace" style="color: #059669;">₹{{ number_format($item->sale_rate, 2) }} / {{ $item->unit }}</strong>
                    </div>

                    @php
                        $marginSpread = (float)$item->sale_rate - (float)$item->purchase_rate;
                        $marginPercent = (float)$item->purchase_rate > 0 ? ($marginSpread / (float)$item->purchase_rate) * 100 : 0;
                    @endphp

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Unit Profit Spread</span>
                        <strong class="font-monospace" style="color: {{ $marginSpread >= 0 ? '#059669' : '#DC2626' }};">
                            {{ $marginSpread >= 0 ? '+' : '' }}₹{{ number_format($marginSpread, 2) }}
                        </strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Gross Margin Markup</span>
                        <strong class="font-monospace" style="color: {{ $marginPercent >= 0 ? '#059669' : '#DC2626' }};">
                            {{ $marginPercent >= 0 ? '+' : '' }}{{ number_format($marginPercent, 1) }}%
                        </strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">On-Hand Stock Valuation</span>
                        <strong class="font-monospace text-primary">₹{{ number_format($item->stock_valuation, 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- 2. System Ledger & Audit Security Stamp -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> Ledger Security &amp; Audit Trail
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">System Record ID</span>
                        <strong class="font-monospace text-dark">#{{ $item->id }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Created Date</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Last Modified</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $item->updated_at ? $item->updated_at->format('d M Y, h:i A') : 'Never' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Master Status</span>
                        @if($item->status === 'active')
                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 700; font-size: 0.75rem;">
                                Active Catalog Item
                            </span>
                        @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; font-weight: 700; font-size: 0.75rem;">
                                Inactive Item
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="card" style="padding: 1.15rem; background: #FAFBFD; border: 1px solid #E2E8F0; border-radius: 12px;">
                <h5 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">
                    <i class="fa-solid fa-bolt text-primary me-1"></i> Quick Inventory Actions
                </h5>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.masters.item.edit', $item->id) }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Specifications &amp; Pricing
                    </a>
                    <a href="{{ route('admin.masters.item.create') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-plus me-2 text-primary"></i> Add Another Product / Material
                    </a>
                    <a href="{{ route('admin.masters.item') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-list me-2 text-primary"></i> Return to Item Directory
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function copyToClipboard(text, successMessage) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(successMessage);
            });
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast(successMessage);
            } catch (err) {
                console.error('Unable to copy', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showToast(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }
</script>
@endpush
@endsection
