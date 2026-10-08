@extends('admin.layouts.app')

@section('title', 'Record Stock Adjustment - VIKAS UDHYOG ERP')
@section('page_code', 'inv-adjustment')

@section('content')
<section class="view-section active" id="view-adj-create">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Inventory</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.inventory.stock-adjustment') }}">Stock Adjustment</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Record Stock Adjustment</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-sliders text-primary"></i> Record Stock Adjustment
            </h1>
            <p class="erp-page-subtitle">
                Reconcile physical warehouse stock counts with digital inventory balance, logging shortages, spillage or surplus gains.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.inventory.stock-adjustment') }}" class="btn btn-outline" title="Return to Audit Log">
                <i class="fa-solid fa-arrow-left"></i> Back to Audit List
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
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

    <!-- Form Container (2-Column Standard Layout) -->
    <form action="{{ route('admin.inventory.stock-adjustment.store') }}" method="POST" id="form-stock-adjustment" onsubmit="return handleFormSubmit(event)">
        @csrf

        <div class="erp-form-layout-2col">
            
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Audit Reference Coordinates & Product -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-barcode"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Audit Coordinates &amp; Herbal Product</h3>
                                <p class="erp-form-section-desc">Reference tracking code, reconciliation date and target inventory SKU</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <!-- Adjustment No -->
                            <div class="form-group">
                                <label class="erp-field-label">
                                    Adjustment Docket No <span class="erp-req-star">*</span>
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                    <input type="text" name="adjustment_no" id="field-adj-no" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter Adjustment No" value="{{ old('adjustment_no', $nextAdjustmentNo) }}" required readonly style="background-color: #F8FAFC;">
                                </div>
                                <span class="erp-field-hint">Unique sequential audit docket identifier</span>
                            </div>

                            <!-- Audit Date -->
                            <div class="form-group">
                                <label class="erp-field-label">
                                    Audit Date <span class="erp-req-star">*</span>
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                    <input type="date" name="adjustment_date" id="field-adj-date" class="form-control erp-field-input-iconified" value="{{ old('adjustment_date', date('Y-m-d')) }}" required onchange="updateLivePreview()">
                                </div>
                                <span class="erp-field-hint">Date when physical inventory count was audited</span>
                            </div>
                        </div>

                        <!-- Target Product Selector -->
                        <div class="form-group" style="margin-top: 1rem;">
                            <label class="erp-field-label">
                                Select Herbal Product to Reconcile <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-leaf erp-field-icon"></i>
                                <select name="item_id" id="field-item-select" class="form-control erp-field-input-iconified" required onchange="onItemChange(this)">
                                    <option value="">-- Choose Herbal Product to Reconcile --</option>
                                    @foreach($allItems as $itm)
                                        <option value="{{ $itm->id }}" 
                                            data-name="{{ $itm->name }}"
                                            data-code="{{ $itm->code }}"
                                            data-stock="{{ (float)$itm->current_stock }}"
                                            data-unit="{{ $itm->unit }}"
                                            data-rate="{{ (float)$itm->purchase_rate }}"
                                            data-category="{{ $itm->category ?: 'General' }}"
                                            data-initials="{{ $itm->initials }}"
                                            {{ (old('item_id', $selectedItemId) == $itm->id) ? 'selected' : '' }}>
                                            [{{ $itm->code }}] {{ $itm->name }} &bull; System Stock: {{ number_format($itm->current_stock, 1) }} {{ $itm->unit }} (₹{{ number_format($itm->purchase_rate, 2) }}/{{ $itm->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Select the catalog item verified during warehouse physical count</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Variance Quantities & Valuation Impact -->
                <div class="card erp-form-section-card" style="margin-top: 1.25rem;">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Physical Variance &amp; Balance Delta</h3>
                                <p class="erp-form-section-desc">Specify whether variance represents surplus physical stock or unrecorded reduction</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        
                        <!-- Adjustment Direction / Type Segmented Selection -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Adjustment Direction / Type <span class="erp-req-star">*</span>
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.35rem;">
                                <!-- Option 1: Surplus -->
                                <label id="card-type-add" style="border: 2px solid #10B981; background: #ECFDF5; border-radius: 12px; padding: 1rem 1.15rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.85rem; transition: all 0.2s ease;">
                                    <input type="radio" name="type" value="add" checked onchange="onTypeChange('add')" style="margin-top: 4px; accent-color: #10B981; width: 18px; height: 18px;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="font-weight: 800; color: #065F46; font-size: 0.95rem;">
                                                + Physical Surplus
                                            </span>
                                            <span class="badge" style="background: #D1FAE5; color: #065F46; font-size: 0.7rem; font-weight: 700; padding: 2px 6px;">Inward Gain</span>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #047857; margin-top: 3px; line-height: 1.4;">
                                            Floor count exceeds system record. Reconciles inventory balance upwards.
                                        </div>
                                    </div>
                                </label>

                                <!-- Option 2: Shortage -->
                                <label id="card-type-reduce" style="border: 1px solid #E2E8F0; background: #FFFFFF; border-radius: 12px; padding: 1rem 1.15rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.85rem; transition: all 0.2s ease;">
                                    <input type="radio" name="type" value="reduce" onchange="onTypeChange('reduce')" style="margin-top: 4px; accent-color: #DC2626; width: 18px; height: 18px;">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <span style="font-weight: 800; color: #991B1B; font-size: 0.95rem;">
                                                - Stock Reduction
                                            </span>
                                            <span class="badge" style="background: #FEE2E2; color: #991B1B; font-size: 0.7rem; font-weight: 700; padding: 2px 6px;">Outward Loss</span>
                                        </div>
                                        <div style="font-size: 0.78rem; color: #B91C1C; margin-top: 3px; line-height: 1.4;">
                                            Damage, spillage, expired or shortage. Deducts inventory balance downwards.
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Variance Quantity & Reason Dropdown -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                            <!-- Quantity -->
                            <div class="form-group">
                                <label class="erp-field-label">
                                    Physical Variance Quantity <span class="erp-req-star">*</span>
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-arrow-up-right-dots erp-field-icon"></i>
                                    <input type="number" step="0.01" min="0.01" name="quantity" id="field-adj-qty" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter Variance Quantity" value="{{ old('quantity') }}" required oninput="updateLivePreview()" style="font-size: 1rem; font-weight: 700;">
                                </div>
                                <span class="erp-field-hint" id="hint-unit">Variance units verified during floor audit</span>
                            </div>

                            <!-- Reason -->
                            <div class="form-group">
                                <label class="erp-field-label">
                                    Audit Reason Category <span class="erp-req-star">*</span>
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-tag erp-field-icon"></i>
                                    <select name="reason" id="field-reason" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                        @foreach($reasons as $rsn)
                                            <option value="{{ $rsn }}" {{ old('reason') === $rsn ? 'selected' : '' }}>
                                                {{ $rsn }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="erp-field-hint">Classification for internal accounting audit</span>
                            </div>
                        </div>

                        <!-- Real-time Balance Projection Ribbon (Styled as ERP Voucher Summary Card) -->
                        <div class="erp-voucher-summary-card" style="margin-top: 1.25rem; background: #FAFBF9; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.15rem 1.25rem;">
                            <div style="width: 100%; display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; text-align: center;">
                                <div>
                                    <div style="font-size: 0.72rem; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">System Stock</div>
                                    <div class="font-monospace" id="proj-current-stock" style="font-size: 1.1rem; font-weight: 700; color: #334155; margin-top: 3px;">
                                        0.00 Unit
                                    </div>
                                </div>
                                <div style="border-left: 1px solid #E2E8F0; padding-left: 0.5rem;">
                                    <div style="font-size: 0.72rem; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Variance Delta</div>
                                    <div class="font-monospace" id="proj-variance-delta" style="font-size: 1.1rem; font-weight: 800; color: #059669; margin-top: 3px;">
                                        +0.00 Unit
                                    </div>
                                </div>
                                <div style="border-left: 1px solid #E2E8F0; padding-left: 0.5rem;">
                                    <div style="font-size: 0.72rem; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Reconciled Stock</div>
                                    <div class="font-monospace" id="proj-new-stock" style="font-size: 1.1rem; font-weight: 800; color: #059669; margin-top: 3px;">
                                        0.00 Unit
                                    </div>
                                </div>
                                <div style="border-left: 1px solid #E2E8F0; padding-left: 0.5rem;">
                                    <div style="font-size: 0.72rem; color: #64748B; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">Valuation Delta</div>
                                    <div class="font-monospace" id="proj-valuation-impact" style="font-size: 1.1rem; font-weight: 800; color: #059669; margin-top: 3px;">
                                        +₹0.00
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Auditor Remarks / Notes -->
                        <div class="form-group" style="margin-top: 1.25rem;">
                            <label class="erp-field-label">
                                Auditor Notes / Inspection Remarks
                            </label>
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter batch lot, physical warehouse bin, investigation notes..." oninput="updateLivePreview()">{{ old('notes') }}</textarea>
                            <span class="erp-field-hint">Optional remarks permanently logged against this audit record</span>
                        </div>

                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-sidebar-col">

                <!-- 1. Real-Time Live Preview Card (Unified ERP Standard) -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        <div class="erp-preview-badge">
                            <i class="fa-solid fa-eye text-primary"></i> Live Audit Preview
                        </div>
                        <span class="badge" id="preview-badge-type" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.74rem; font-weight: 700; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                            + Surplus
                        </span>
                    </div>

                    <div class="erp-preview-avatar-wrap">
                        <div class="avatar" id="preview-avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; margin: 0 auto; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.35);">
                            AD
                        </div>
                        <h4 class="erp-preview-title" id="preview-name" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; text-align: center; word-break: break-word;">
                            Choose Herbal Product
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.3rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                ---
                            </span>
                            <span class="badge" id="preview-category" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                General
                            </span>
                        </div>
                    </div>

                    <!-- Financials / Stock Ribbon -->
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.85rem; margin: 1.15rem 0;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; text-align: center;">
                            <div style="border-right: 1px solid #E2E8F0; padding-right: 0.5rem;">
                                <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">System Stock</div>
                                <div class="font-monospace" id="preview-stock" style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-top: 0.15rem;">
                                    0.00 Unit
                                </div>
                            </div>
                            <div style="padding-left: 0.5rem;">
                                <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 600; letter-spacing: 0.04em;">Reconciled</div>
                                <div class="font-monospace" id="preview-reconciled" style="font-size: 1.05rem; font-weight: 800; color: #059669; margin-top: 0.15rem;">
                                    0.00 Unit
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Highlighted Delta Banner -->
                    <div id="preview-delta-box" style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 0.85rem; text-align: center; margin-bottom: 1.15rem;">
                        <div style="font-size: 0.72rem; color: #047857; font-weight: 700; text-transform: uppercase;">Physical Variance Delta</div>
                        <div class="font-monospace" id="preview-delta-val" style="font-size: 1.35rem; font-weight: 800; color: #059669; margin: 2px 0;">
                            +0.00 Unit
                        </div>
                        <div style="font-size: 0.75rem; color: #065F46;" id="preview-delta-sub">
                            Valuation Impact: <strong class="font-monospace" id="preview-val-impact">+₹0.00</strong>
                        </div>
                    </div>

                    <!-- Meta List -->
                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Audit Docket:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-docket">{{ $nextAdjustmentNo }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Audit Date:</span>
                            <span class="erp-preview-meta-val" id="preview-date">{{ date('d M Y') }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Unit Rate:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-rate">₹0.00 / Unit</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Audit Reason:</span>
                            <span class="erp-preview-meta-val" id="preview-reason">{{ $reasons[0] ?? 'Physical Count Variance' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Auditor:</span>
                            <span class="erp-preview-meta-val">{{ Auth::user()->name ?? 'Admin Auditor' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Form Submission Action Card -->
                <div class="card erp-sidebar-actions-card" style="margin-top: 1.25rem;">
                    <button type="submit" class="btn btn-primary erp-btn-sidebar-submit" id="btn-submit-adj" style="width: 100%;">
                        <i class="fa-solid fa-check"></i> Record &amp; Reconcile Stock
                    </button>
                    <a href="{{ route('admin.inventory.stock-adjustment') }}" class="btn btn-outline erp-btn-sidebar-cancel" style="width: 100%;">
                        <i class="fa-solid fa-xmark"></i> Cancel &amp; Return
                    </a>
                </div>

                <!-- 3. Guidance Card -->
                <div class="card" style="padding: 1.15rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; margin-top: 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.65rem;">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        <h5 style="margin: 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">Stock Audit Principles</h5>
                    </div>
                    <ul style="font-size: 0.78rem; color: #64748B; margin: 0; padding-left: 1.15rem; line-height: 1.55;">
                        <li>Stock adjustments update the live physical inventory balance immediately upon save.</li>
                        <li><strong>Surplus (+)</strong> records uncounted floor goods, increasing catalog balances.</li>
                        <li><strong>Reduction (-)</strong> writes off spillage, damage, or sampling loss.</li>
                        <li>Adjustments can be rolled back anytime from the Audit Log register.</li>
                    </ul>
                </div>

            </div>

        </div>

    </form>

</section>

<script>
var activeItemStock = 0;
var activeItemUnit = 'Unit';
var activeItemRate = 0;

function onItemChange(selectEl) {
    var opt = selectEl.options[selectEl.selectedIndex];
    if (opt && opt.value) {
        activeItemStock = parseFloat(opt.getAttribute('data-stock')) || 0;
        activeItemUnit = opt.getAttribute('data-unit') || 'Unit';
        activeItemRate = parseFloat(opt.getAttribute('data-rate')) || 0;

        document.getElementById('preview-name').innerText = opt.getAttribute('data-name');
        document.getElementById('preview-code').innerText = opt.getAttribute('data-code');
        document.getElementById('preview-category').innerText = opt.getAttribute('data-category');
        document.getElementById('preview-avatar').innerText = opt.getAttribute('data-initials') || 'PR';
    } else {
        activeItemStock = 0;
        activeItemUnit = 'Unit';
        activeItemRate = 0;
        document.getElementById('preview-name').innerText = 'Choose Herbal Product';
        document.getElementById('preview-code').innerText = '---';
        document.getElementById('preview-category').innerText = 'General';
        document.getElementById('preview-avatar').innerText = 'AD';
    }

    var hintUnit = document.getElementById('hint-unit');
    if (hintUnit) hintUnit.innerText = 'Variance in ' + activeItemUnit + ' verified during floor audit';

    updateLivePreview();
}

function onTypeChange(type) {
    var cardAdd = document.getElementById('card-type-add');
    var cardReduce = document.getElementById('card-type-reduce');

    if (type === 'add') {
        if (cardAdd) {
            cardAdd.style.border = '2px solid #10B981';
            cardAdd.style.background = '#ECFDF5';
        }
        if (cardReduce) {
            cardReduce.style.border = '1px solid #E2E8F0';
            cardReduce.style.background = '#FFFFFF';
        }
    } else {
        if (cardReduce) {
            cardReduce.style.border = '2px solid #DC2626';
            cardReduce.style.background = '#FEF2F2';
        }
        if (cardAdd) {
            cardAdd.style.border = '1px solid #E2E8F0';
            cardAdd.style.background = '#FFFFFF';
        }
    }

    updateLivePreview();
}

function updateLivePreview() {
    var qty = parseFloat(document.getElementById('field-adj-qty').value) || 0;
    var typeRadio = document.querySelector('input[name="type"]:checked');
    var type = typeRadio ? typeRadio.value : 'add';
    var isAdd = type === 'add';

    var newStock = isAdd ? (activeItemStock + qty) : (activeItemStock - qty);
    var valImpact = qty * activeItemRate;

    // 1. Update Voucher Summary Ribbon
    document.getElementById('proj-current-stock').innerText = activeItemStock.toFixed(2) + ' ' + activeItemUnit;
    var varDeltaEl = document.getElementById('proj-variance-delta');
    varDeltaEl.innerText = (isAdd ? '+' : '-') + qty.toFixed(2) + ' ' + activeItemUnit;
    varDeltaEl.style.color = isAdd ? '#059669' : '#DC2626';

    var projNewEl = document.getElementById('proj-new-stock');
    projNewEl.innerText = newStock.toFixed(2) + ' ' + activeItemUnit;
    projNewEl.style.color = isAdd ? '#059669' : (newStock < 0 ? '#DC2626' : '#D97706');

    var projValEl = document.getElementById('proj-valuation-impact');
    projValEl.innerText = (isAdd ? '+' : '-') + '₹' + valImpact.toFixed(2);
    projValEl.style.color = isAdd ? '#059669' : '#DC2626';

    // 2. Update Sidebar Preview Card
    document.getElementById('preview-stock').innerText = activeItemStock.toFixed(2) + ' ' + activeItemUnit;
    var prevReconciledEl = document.getElementById('preview-reconciled');
    prevReconciledEl.innerText = newStock.toFixed(2) + ' ' + activeItemUnit;
    prevReconciledEl.style.color = isAdd ? '#059669' : (newStock < 0 ? '#DC2626' : '#D97706');

    var deltaValEl = document.getElementById('preview-delta-val');
    deltaValEl.innerText = (isAdd ? '+' : '-') + qty.toFixed(2) + ' ' + activeItemUnit;
    deltaValEl.style.color = isAdd ? '#059669' : '#DC2626';

    var valImpactEl = document.getElementById('preview-val-impact');
    valImpactEl.innerText = (isAdd ? '+' : '-') + '₹' + valImpact.toFixed(2);

    var deltaBox = document.getElementById('preview-delta-box');
    var badgeType = document.getElementById('preview-badge-type');

    if (isAdd) {
        deltaBox.style.background = '#ECFDF5';
        deltaBox.style.borderColor = '#A7F3D0';
        badgeType.style.background = 'rgba(16, 185, 129, 0.12)';
        badgeType.style.borderColor = 'rgba(16, 185, 129, 0.25)';
        badgeType.style.color = '#059669';
        badgeType.innerText = '+ Surplus';
    } else {
        deltaBox.style.background = '#FEF2F2';
        deltaBox.style.borderColor = '#FECACA';
        badgeType.style.background = 'rgba(239, 68, 68, 0.12)';
        badgeType.style.borderColor = 'rgba(239, 68, 68, 0.25)';
        badgeType.style.color = '#DC2626';
        badgeType.innerText = '- Shortage';
    }

    var dateVal = document.getElementById('field-adj-date').value;
    if (dateVal) {
        var d = new Date(dateVal);
        document.getElementById('preview-date').innerText = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    var rsnEl = document.getElementById('field-reason');
    if (rsnEl) document.getElementById('preview-reason').innerText = rsnEl.value;

    document.getElementById('preview-rate').innerText = '₹' + activeItemRate.toFixed(2) + ' / ' + activeItemUnit;
}

function handleFormSubmit(e) {
    var select = document.getElementById('field-item-select');
    var qty = parseFloat(document.getElementById('field-adj-qty').value);

    if (!select || !select.value) {
        alert('Please select an item to adjust.');
        e.preventDefault();
        return false;
    }

    if (!qty || qty <= 0) {
        alert('Please enter a valid quantity greater than 0.');
        e.preventDefault();
        return false;
    }

    var btn = document.getElementById('btn-submit-adj');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Recording Stock...';
    }

    return true;
}

// Auto-run on page load
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('field-item-select');
    if (select && select.value) {
        onItemChange(select);
    }
});
</script>
@endsection
