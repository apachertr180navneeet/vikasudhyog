@extends('admin.layouts.app')

@section('title', 'Add New Item - VIKAS UDHYOG ERP')
@section('page_code', 'master-item')

@section('content')
<section class="view-section active" id="view-item-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.item') }}">Item Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Add New Item</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-box-open text-primary"></i> Add New Product / Raw Material
            </h1>
            <p class="erp-page-subtitle">
                Register finished goods, henna &amp; herbal powder blends, packaging supplies, tax rates &amp; reorder levels.
            </p>
        </div>

        <div class="erp-header-actions">
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
    <form action="{{ route('admin.masters.item.store') }}" method="POST" id="item-create-form">
        @csrf

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
                                <p class="erp-form-section-desc">Commodity naming, category classification &amp; plant allocation</p>
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
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter product name" value="{{ old('name') }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. Sojat Premium Mehndi Powder (20kg Bag), Senna Leaves T-Cut, Herbal Hair Pack</span>
                        </div>

                        <!-- Item Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Item Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateItemCode()" class="erp-btn-suggest" title="Generate Next Item Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter item code" value="{{ old('code', $suggestedCode ?? 'ITM-01') }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique SKU identifier (e.g. ITM-01)</span>
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
                                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
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
                                    <option value="">All Production Plants (Global Stock)</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} ({{ $company->code }}) — {{ $company->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Bind item to a specific factory unit or leave global across all plants</span>
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
                                        <option value="{{ $u }}" {{ old('unit', 'KG') === $u ? 'selected' : '' }}>{{ $u }}</option>
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
                                <input type="text" name="hsn_code" id="field-hsn" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter HSN code" value="{{ old('hsn_code', '330499') }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">e.g. 330499 (Henna / Cosmetics), 121190 (Herbal Raw)</span>
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
                                        <option value="{{ $rate }}" {{ old('gst_rate', '5.00') == $rate ? 'selected' : '' }}>
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
                                <input type="number" step="0.01" min="0" name="purchase_rate" id="field-purchase-rate" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter purchase rate" value="{{ old('purchase_rate', '0.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Estimated procurement cost per selected unit</span>
                        </div>

                        <!-- Sale Rate -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Standard Selling Rate (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-arrow-up-long erp-field-icon" style="color: #059669;"></i>
                                <input type="number" step="0.01" min="0" name="sale_rate" id="field-sale-rate" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter sale rate" value="{{ old('sale_rate', '0.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Standard wholesale sale price per selected unit</span>
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
                                <p class="erp-form-section-desc">Opening balance, minimum threshold reorder alerts &amp; lot tracking</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Opening Stock -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Opening Stock Quantity
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-boxes-packing erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="opening_stock" id="field-opening-stock" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter opening stock" value="{{ old('opening_stock', '0.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Initial inventory balance on record creation</span>
                        </div>

                        <!-- Min Stock Alert -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Min Stock Alert Level (Reorder Threshold)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-triangle-exclamation erp-field-icon" style="color: #DC2626;"></i>
                                <input type="number" step="0.01" min="0" name="min_stock_alert" id="field-min-stock-alert" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter minimum stock alert level" value="{{ old('min_stock_alert', '20.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Dashboard alerts trigger when stock drops to or below this level</span>
                        </div>

                        <!-- Batch No -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Initial Batch / Lot Identifier
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-stamp erp-field-icon"></i>
                                <input type="text" name="batch_no" id="field-batch-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter batch number" value="{{ old('batch_no') }}" maxlength="50" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. SOJ-2026-B1 or LOT-04</span>
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
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter storage instructions or quality notes">{{ old('notes') }}</textarea>
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
                        <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                            <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i> Active
                        </span>
                    </div>

                    <div class="erp-preview-avatar-wrap">
                        <div class="avatar" id="preview-avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.35);">
                            IT
                        </div>
                        <h4 class="erp-preview-title" id="preview-name" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; text-align: center; word-break: break-word;">
                            Product Name
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.3rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $suggestedCode ?? 'ITM-01' }}
                            </span>
                            <span class="badge" id="preview-category" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                Mehndi / Henna
                            </span>
                        </div>
                    </div>

                    <!-- Financials / Stock Ribbon -->
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem; margin: 1.15rem 0;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; text-align: center;">
                            <div style="border-right: 1px solid #E2E8F0; padding-right: 0.5rem;">
                                <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Stock Qty</div>
                                <div class="font-monospace" id="preview-stock" style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-top: 0.15rem;">
                                    0.00 KG
                                </div>
                            </div>
                            <div style="padding-left: 0.5rem;">
                                <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Valuation</div>
                                <div class="font-monospace" id="preview-valuation" style="font-size: 1.05rem; font-weight: 700; color: #5B841E; margin-top: 0.15rem;">
                                    ₹0.00
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Purchase Rate:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-purchase-rate" style="color: #334155; font-weight: 600;">₹0.00</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Selling Rate:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-sale-rate" style="color: #059669; font-weight: 700;">₹0.00</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">HSN &amp; GST:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-hsn-gst">330499 (5.00%)</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Reorder Alert:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-reorder-alert" style="color: #DC2626;">20.00 KG</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Batch / Lot:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-batch">Unassigned</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Plant Unit:</span>
                            <span class="erp-preview-meta-val" id="preview-company">All Units</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Card -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-sidebar-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Save Product Item
                    </button>
                    <button type="reset" class="btn btn-outline erp-btn-sidebar-reset" onclick="setTimeout(updateLivePreview, 50)">
                        <i class="fa-solid fa-rotate-left"></i> Reset Form
                    </button>
                    <a href="{{ route('admin.masters.item') }}" class="btn btn-outline erp-btn-sidebar-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>

                <!-- Guidance Tips Card -->
                <div class="card" style="padding: 1.15rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.65rem;">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        <h5 style="margin: 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">Item Master Guidelines</h5>
                    </div>
                    <ul style="font-size: 0.78rem; color: #64748B; margin: 0; padding-left: 1.15rem; line-height: 1.55;">
                        <li>Status is automatically set to <strong>Active</strong> upon registration.</li>
                        <li>Henna powder standard GST is <strong>5%</strong> under HSN code <strong>330499</strong>.</li>
                        <li>Initial opening stock establishes the starting ledger balance.</li>
                        <li>Dashboard warning alerts fire when stock falls to or below the reorder level.</li>
                    </ul>
                </div>

            </div>
        </div>
    </form>
</section>

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
        const stockVal = parseFloat(document.getElementById('field-opening-stock').value) || 0;
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

        fetch('{{ route("admin.masters.item.generate-code") }}?prefix=ITM')
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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
    });
</script>
@endpush
@endsection
