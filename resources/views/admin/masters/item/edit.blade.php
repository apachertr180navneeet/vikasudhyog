@extends('admin.layouts.app')

@section('title', 'Edit Item: ' . $item->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-item')

@section('content')
<section class="view-section active" id="view-item-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.item') }}">Item Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Item: {{ $item->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Product / Raw Material
            </h1>
            <p class="erp-page-subtitle">
                Update item specifications, tax rates, commercial pricing, current stock count &amp; reorder thresholds.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.item.show', $item->id) }}" class="btn btn-outline" title="View Item Dossier">
                <i class="fa-regular fa-eye"></i> View Dossier
            </a>
            <a href="{{ route('admin.masters.item') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Item List
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(isset($errors) && $errors->any())
        <div class="alert erp-alert-danger">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-exclamation erp-alert-icon-danger"></i>
                <div>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-1 ps-3" style="font-size: 0.85rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Form Container -->
    <form action="{{ route('admin.masters.item.update', $item->id) }}" method="POST" id="item-edit-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Product & Material Identity -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Product &amp; Material Identity</h3>
                                <p class="erp-form-section-desc">Commodity naming, SKU identifier &amp; plant allocation</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Item Name -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Product / Material Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-tag erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter product name" value="{{ old('name', $item->name) }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. Sojat Premium Mehndi Powder (20kg Bag), Senna Leaves T-Cut</span>
                        </div>

                        <!-- Item Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Item Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateItemCode()" class="erp-btn-suggest" title="Generate Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter item code" value="{{ old('code', $item->code) }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique SKU identifier</span>
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Item Category <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-layer-group erp-field-icon"></i>
                                <select name="category" id="field-category" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $item->category) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Classification for reporting &amp; inventory grouping</span>
                        </div>

                        <!-- Production Plant / Company -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Manufacturing Plant / Unit Assignment
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-circle-check erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="" {{ empty($item->company_id) ? 'selected' : '' }}>All Production Plants (Global Stock)</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $item->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} ({{ $company->code }}) — {{ $company->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Assigned factory facility for inventory batches</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Measurement Unit & Taxation -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Measurement Unit &amp; Taxation</h3>
                                <p class="erp-form-section-desc">Unit of measurement, HSN code &amp; GST tax slabs</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Unit of Measure -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Primary Unit of Measure (UOM) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-cubes erp-field-icon"></i>
                                <select name="unit" id="field-unit" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    @foreach($units as $u)
                                        <option value="{{ $u }}" {{ old('unit', $item->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Standard quantity billing metric</span>
                        </div>

                        <!-- HSN Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                HSN / Tariff Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                                <input type="text" name="hsn_code" id="field-hsn" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter HSN code" value="{{ old('hsn_code', $item->hsn_code) }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Harmonized system tariff code</span>
                        </div>

                        <!-- GST Tax Rate -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                GST Tax Rate (%)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-percent erp-field-icon"></i>
                                <select name="gst_rate" id="field-gst-rate" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    @foreach($gstRates as $rate)
                                        <option value="{{ $rate }}" {{ (float)old('gst_rate', $item->gst_rate) == (float)$rate ? 'selected' : '' }}>
                                            {{ number_format($rate, 2) }}% {{ $rate == 5 ? '(Standard Henna / Agri Slab)' : ($rate == 0 ? '(Exempted)' : '') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Applicable GST percentage for billing &amp; GST returns</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Commercial Pricing & Valuation -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-purple">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Commercial Pricing &amp; Valuation</h3>
                                <p class="erp-form-section-desc">Default purchase cost rate &amp; standard sales rate</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Purchase Rate -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Default Purchase Rate (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-arrow-down-long erp-field-icon" style="color: #64748B;"></i>
                                <input type="number" step="0.01" min="0" name="purchase_rate" id="field-purchase-rate" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter purchase rate" value="{{ old('purchase_rate', $item->purchase_rate) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Procurement cost per unit for inventory valuation</span>
                        </div>

                        <!-- Sale Rate -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Standard Selling Rate (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-arrow-up-long erp-field-icon" style="color: #059669;"></i>
                                <input type="number" step="0.01" min="0" name="sale_rate" id="field-sale-rate" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter sale rate" value="{{ old('sale_rate', $item->sale_rate) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Standard wholesale sale price per unit</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Stock & Batch Management -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">4. Stock &amp; Batch Management</h3>
                                <p class="erp-form-section-desc">Current inventory level, minimum threshold alerts &amp; lot tracking</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Current Stock -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Current Stock On-Hand
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-boxes-stacked erp-field-icon" style="color: #0F172A;"></i>
                                <input type="number" step="0.01" min="0" name="current_stock" id="field-current-stock" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter current stock" value="{{ old('current_stock', $item->current_stock) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Physical stock currently available in warehouse</span>
                        </div>

                        <!-- Opening Stock -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Original Opening Stock
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-boxes-packing erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="opening_stock" id="field-opening-stock" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter opening stock" value="{{ old('opening_stock', $item->opening_stock) }}">
                            </div>
                            <span class="erp-field-hint">Base inventory balance established at registration</span>
                        </div>

                        <!-- Min Stock Alert -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Min Stock Alert Level (Reorder Threshold)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-triangle-exclamation erp-field-icon" style="color: #DC2626;"></i>
                                <input type="number" step="0.01" min="0" name="min_stock_alert" id="field-min-stock-alert" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter minimum stock alert level" value="{{ old('min_stock_alert', $item->min_stock_alert) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Alert triggers when stock drops to or below this amount</span>
                        </div>

                        <!-- Batch No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Active Batch / Lot Identifier
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-stamp erp-field-icon"></i>
                                <input type="text" name="batch_no" id="field-batch-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter batch number" value="{{ old('batch_no', $item->batch_no) }}" maxlength="50" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. SOJ-2026-B1</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Storage Specifications & Notes -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-gray">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">5. Storage Specifications &amp; Quality Notes</h3>
                                <p class="erp-form-section-desc">Moisture guidelines, warehouse rack location &amp; handling instructions</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Storage Notes / Specifications
                            </label>
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter storage instructions or quality notes">{{ old('notes', $item->notes) }}</textarea>
                            <span class="erp-field-hint">e.g. Store in dry, moisture-free warehouse pallet. Moisture content &lt; 8.5%.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-sidebar-col">

                <!-- Real-Time Live Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        <div class="erp-preview-badge">
                            <i class="fa-solid fa-eye"></i> Live Item Preview
                        </div>
                        @if($item->status === 'active')
                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i> Active
                            </span>
                        @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(239, 68, 68, 0.25);">
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i> Inactive
                            </span>
                        @endif
                    </div>

                    <div class="erp-preview-avatar-wrap">
                        <div class="avatar" id="preview-avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.35);">
                            {{ $item->initials }}
                        </div>
                        <h4 class="erp-preview-title" id="preview-name" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; text-align: center; word-break: break-word;">
                            {{ $item->name }}
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.3rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $item->code }}
                            </span>
                            <span class="badge" id="preview-category" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                {{ $item->category }}
                            </span>
                        </div>
                    </div>

                    <!-- Financials / Stock Ribbon -->
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem; margin: 1.15rem 0;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; text-align: center;">
                            <div style="border-right: 1px solid #E2E8F0; padding-right: 0.5rem;">
                                <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Stock Qty</div>
                                <div class="font-monospace" id="preview-stock" style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-top: 0.15rem;">
                                    {{ number_format($item->current_stock, 2) }} {{ $item->unit }}
                                </div>
                            </div>
                            <div style="padding-left: 0.5rem;">
                                <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Valuation</div>
                                <div class="font-monospace" id="preview-valuation" style="font-size: 1.05rem; font-weight: 700; color: #5B841E; margin-top: 0.15rem;">
                                    ₹{{ number_format($item->stock_valuation, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Purchase Rate:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-purchase-rate" style="color: #334155; font-weight: 600;">₹{{ number_format($item->purchase_rate, 2) }} / {{ $item->unit }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Selling Rate:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-sale-rate" style="color: #059669; font-weight: 700;">₹{{ number_format($item->sale_rate, 2) }} / {{ $item->unit }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">HSN &amp; GST:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-hsn-gst">{{ $item->hsn_code ?: '—' }} ({{ number_format($item->gst_rate, 2) }}%)</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Reorder Alert:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-reorder-alert" style="color: #DC2626;">{{ number_format($item->min_stock_alert, 2) }} {{ $item->unit }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Batch / Lot:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-batch">{{ $item->batch_no ?: 'Unassigned' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Plant Unit:</span>
                            <span class="erp-preview-meta-val" id="preview-company">{{ $item->company ? $item->company->name : 'All Units' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Card -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-sidebar-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Update Product Item
                    </button>
                    <a href="{{ route('admin.masters.item') }}" class="btn btn-outline erp-btn-sidebar-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel Changes
                    </a>
                </div>

                <!-- Audit Trail Card -->
                <div class="card" style="padding: 1.15rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                        <h5 style="margin: 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">Record Audit Trail</h5>
                    </div>
                    <div style="font-size: 0.78rem; color: #64748B; display: flex; flex-direction: column; gap: 0.4rem;">
                        <div>
                            <strong>Item ID:</strong> <span class="font-monospace text-dark">#{{ $item->id }}</span>
                        </div>
                        <div>
                            <strong>Created:</strong> {{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : 'System Initial' }}
                        </div>
                        <div>
                            <strong>Last Modified:</strong> {{ $item->updated_at ? $item->updated_at->format('d M Y, h:i A') : 'Never' }}
                        </div>
                    </div>
                </div>

                <!-- Danger Zone Archive Card -->
                <div class="card" style="padding: 1.15rem; border: 1px solid #FECACA; background: #FFF5F5; border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: #DC2626;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <h5 style="margin: 0; font-size: 0.88rem; font-weight: 700; color: #991B1B;">Danger Zone</h5>
                    </div>
                    <p style="font-size: 0.78rem; color: #7F1D1D; margin-bottom: 0.85rem; line-height: 1.45;">
                        Archiving soft-deletes this item. It will be removed from order creation and catalog selections.
                    </p>
                    <button type="button" class="btn btn-outline" style="border-color: #EF4444; color: #DC2626; width: 100%; font-size: 0.82rem; font-weight: 600;" onclick="confirmArchiveItem()">
                        <i class="fa-regular fa-trash-can me-1"></i> Archive Product Item
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Delete Hidden Form -->
<form id="archive-item-form" action="{{ route('admin.masters.item.destroy', $item->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Live Dynamic Preview Updater
    function updateLivePreview() {
        const nameVal = document.getElementById('field-name').value.trim();
        const codeVal = document.getElementById('field-code').value.trim();
        const catVal = document.getElementById('field-category').value;
        const unitVal = document.getElementById('field-unit').value || 'KG';
        const hsnVal = document.getElementById('field-hsn').value.trim() || '—';
        const gstVal = parseFloat(document.getElementById('field-gst-rate').value) || 0;
        const purVal = parseFloat(document.getElementById('field-purchase-rate').value) || 0;
        const saleVal = parseFloat(document.getElementById('field-sale-rate').value) || 0;
        const stockVal = parseFloat(document.getElementById('field-current-stock').value) || 0;
        const alertVal = parseFloat(document.getElementById('field-min-stock-alert').value) || 0;
        const batchVal = document.getElementById('field-batch-no').value.trim();
        const companyElem = document.getElementById('field-company');
        const companyText = companyElem.selectedIndex > 0 ? companyElem.options[companyElem.selectedIndex].text.split('(')[0].trim() : 'All Units';

        // Avatar Initials
        let initials = 'IT';
        if (nameVal.length > 0) {
            const words = nameVal.split(/\s+/).filter(Boolean);
            if (words.length >= 2) {
                initials = (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
            } else {
                initials = nameVal.substring(0, 2).toUpperCase();
            }
        }
        document.getElementById('preview-avatar').textContent = initials;
        document.getElementById('preview-name').textContent = nameVal || 'Product Name';
        document.getElementById('preview-code').textContent = codeVal || 'ITM-01';
        document.getElementById('preview-category').textContent = catVal || 'Mehndi / Henna';

        // Quantities & Valuation
        document.getElementById('preview-stock').textContent = stockVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + unitVal;
        const totalValuation = stockVal * purVal;
        document.getElementById('preview-valuation').textContent = '₹' + totalValuation.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Financials & Details
        document.getElementById('preview-purchase-rate').textContent = '₹' + purVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / ' + unitVal;
        document.getElementById('preview-sale-rate').textContent = '₹' + saleVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' / ' + unitVal;
        document.getElementById('preview-hsn-gst').textContent = hsnVal + ' (' + gstVal.toFixed(2) + '%)';
        document.getElementById('preview-reorder-alert').textContent = alertVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ' + unitVal;
        document.getElementById('preview-batch').textContent = batchVal || 'Unassigned';
        document.getElementById('preview-company').textContent = companyText;
    }

    // AJAX Auto-generate Item Code
    function generateItemCode() {
        const btn = event.currentTarget;
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('{{ route("admin.masters.item.generate-code") }}?prefix=ITM&exclude_id={{ $item->id }}')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.code) {
                    document.getElementById('field-code').value = data.code;
                    updateLivePreview();
                }
            })
            .catch(err => console.error('Error generating item code:', err))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            });
    }

    function confirmArchiveItem() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Item from Catalog?',
                html: `Are you sure you want to archive <strong>{{ addslashes($item->name) }} ({{ $item->code }})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the record from active item master catalogues.</span>`,
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
                    document.getElementById('archive-item-form').submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive {{ addslashes($item->name) }} ({{ $item->code }})?`)) {
                document.getElementById('archive-item-form').submit();
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
    });
</script>
@endpush
@endsection
