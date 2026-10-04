@extends('admin.layouts.app')

@section('title', 'Edit Unit: ' . $unit->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-unit')

@section('content')
<section class="view-section active" id="view-unit-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.unit') }}">Unit Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Unit: {{ $unit->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Measurement Unit
            </h1>
            <p class="erp-page-subtitle">
                Update unit specifications, GST UQC classification &amp; conversion relation factors.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.unit.show', $unit->id) }}" class="btn btn-outline" title="View Unit Dossier">
                <i class="fa-regular fa-eye"></i> View Dossier
            </a>
            <a href="{{ route('admin.masters.unit') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Unit List
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
    <form action="{{ route('admin.masters.unit.update', $unit->id) }}" method="POST" id="unit-edit-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Unit Definition & Identification -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-cube"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Unit Definition &amp; Standard Codes</h3>
                                <p class="erp-form-section-desc">Primary name, symbol abbreviation &amp; official GST reporting UQC code</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Unit Name -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Unit Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-tag erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter unit name" value="{{ old('name', $unit->name) }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. Centimeter, Meter, Kilogram, Gram, Bag (20kg), Quintal, Box</span>
                        </div>

                        <!-- Unit Symbol / Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Unit Symbol / Code <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-signature erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter unit symbol" value="{{ old('code', $unit->code) }}" required maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">e.g. CM, M, KG, GM, BAG, QTL, BOX</span>
                        </div>

                        <!-- GST UQC Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                GST UQC Code (Unique Quantity Code)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                                <select name="uqc_code" id="field-uqc" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">Select Official GST UQC</option>
                                    @foreach($uqcCodes as $codeKey => $codeLabel)
                                        <option value="{{ $codeKey }}" {{ old('uqc_code', $unit->uqc_code) === $codeKey ? 'selected' : '' }}>
                                            {{ $codeLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Standard quantity code for GST returns</span>
                        </div>

                        <!-- Decimal Places -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Decimal Precision (Decimal Places) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                <select name="decimal_places" id="field-decimals" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    <option value="0" {{ old('decimal_places', $unit->decimal_places) == '0' ? 'selected' : '' }}>0 Decimals (Whole integers e.g. 10 BOX, 5 BAG)</option>
                                    <option value="1" {{ old('decimal_places', $unit->decimal_places) == '1' ? 'selected' : '' }}>1 Decimal (e.g. 10.5)</option>
                                    <option value="2" {{ old('decimal_places', $unit->decimal_places) == '2' ? 'selected' : '' }}>2 Decimals (Standard metric e.g. 25.50 KG, 1.25 M)</option>
                                    <option value="3" {{ old('decimal_places', $unit->decimal_places) == '3' ? 'selected' : '' }}>3 Decimals (High precision e.g. 0.350 KG)</option>
                                    <option value="4" {{ old('decimal_places', $unit->decimal_places) == '4' ? 'selected' : '' }}>4 Decimals (Micro formulations e.g. 0.0025 KG)</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Permitted decimals for transaction ledger entries</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Unit Conversion & Relation Logic (100 cm = 1 m) -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-arrows-split-up-and-left"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Unit Conversion &amp; Relation Logic</h3>
                                <p class="erp-form-section-desc">Create mathematical conversion formula linking to a base unit (e.g. 100 cm = 1 m)</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        @php
                            $isCurrentlyDerived = !empty($unit->base_unit_id) || old('base_unit_id');
                        @endphp

                        <!-- Unit Classification Type -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Unit Classification &amp; Hierarchy
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.25rem;">
                                <label style="display: flex; align-items: flex-start; gap: 0.65rem; padding: 0.85rem; border: 1.5px solid {{ $isCurrentlyDerived ? '#CBD5E1' : '#5B841E' }}; border-radius: 10px; cursor: pointer; background: {{ $isCurrentlyDerived ? '#FFFFFF' : '#F9FBFA' }};" id="label-unit-type-base">
                                    <input type="radio" name="unit_classification" value="base" id="radio-type-base" {{ $isCurrentlyDerived ? '' : 'checked' }} onchange="toggleRelationSection()" style="margin-top: 0.2rem;">
                                    <div>
                                        <strong style="color: #1E293B; font-size: 0.88rem; display: block;">Primary Base Unit</strong>
                                        <span style="font-size: 0.75rem; color: #64748B;">Standard independent unit (e.g. Meter, Kilogram, Box)</span>
                                    </div>
                                </label>

                                <label style="display: flex; align-items: flex-start; gap: 0.65rem; padding: 0.85rem; border: 1.5px solid {{ $isCurrentlyDerived ? '#5B841E' : '#CBD5E1' }}; border-radius: 10px; cursor: pointer; background: {{ $isCurrentlyDerived ? '#F9FBFA' : '#FFFFFF' }};" id="label-unit-type-derived">
                                    <input type="radio" name="unit_classification" value="derived" id="radio-type-derived" {{ $isCurrentlyDerived ? 'checked' : '' }} onchange="toggleRelationSection()" style="margin-top: 0.2rem;">
                                    <div>
                                        <strong style="color: #1E293B; font-size: 0.88rem; display: block;">Derived / Sub-Unit</strong>
                                        <span style="font-size: 0.75rem; color: #64748B;">Has conversion relation (e.g. 100 cm = 1 m, 1000 gm = 1 kg)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Relation Definition Container -->
                        <div id="relation-fields-wrap" style="grid-column: 1 / -1; display: {{ $isCurrentlyDerived ? 'grid' : 'none' }}; grid-template-columns: 1fr 1fr; gap: 1.15rem; background: #F8FAFC; padding: 1.15rem; border-radius: 12px; border: 1px solid #E2E8F0;">
                            
                            <!-- Base Unit Selection -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="erp-field-label">
                                    Parent / Base Unit <span class="erp-req-star">*</span>
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-cube erp-field-icon"></i>
                                    <select name="base_unit_id" id="field-base-unit" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                        <option value="">Select Base Unit</option>
                                        @foreach($baseUnits as $bu)
                                            <option value="{{ $bu->id }}" data-code="{{ $bu->code }}" data-name="{{ $bu->name }}" {{ (int)old('base_unit_id', $unit->base_unit_id) === (int)$bu->id ? 'selected' : '' }}>
                                                {{ $bu->name }} ({{ $bu->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <span class="erp-field-hint">e.g. Select Meter (M) or Kilogram (KG)</span>
                            </div>

                            <!-- Conversion Operator Mode -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="erp-field-label">
                                    Relation Direction Mode
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-calculator erp-field-icon"></i>
                                    <select name="operator" id="field-operator" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                        <option value="/" {{ old('operator', $unit->operator) === '/' ? 'selected' : '' }}>
                                            [Factor] [This Unit] = 1 [Base Unit] (e.g. 100 CM = 1 M)
                                        </option>
                                        <option value="*" {{ old('operator', $unit->operator) === '*' ? 'selected' : '' }}>
                                            1 [This Unit] = [Factor] [Base Unit] (e.g. 1 BAG = 20 KG)
                                        </option>
                                    </select>
                                </div>
                                <span class="erp-field-hint">Formula layout direction</span>
                            </div>

                            <!-- Conversion Factor -->
                            <div class="form-group erp-form-col-full" style="margin-bottom: 0;">
                                <label class="erp-field-label">
                                    Conversion Factor <span class="erp-req-star">*</span>
                                </label>
                                <div class="erp-field-icon-wrap">
                                    <i class="fa-solid fa-divide erp-field-icon"></i>
                                    <input type="number" step="0.0001" min="0.0001" name="conversion_factor" id="field-factor" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter conversion factor" value="{{ old('conversion_factor', (float)$unit->conversion_factor) }}" oninput="updateLivePreview()">
                                </div>
                                <span class="erp-field-hint">e.g. Enter 100 for 100 cm = 1 m, or 1000 for 1000 gm = 1 kg</span>
                            </div>

                            <!-- Live Calculated Formula Ribbon -->
                            <div style="grid-column: 1 / -1; background: #ECFDF5; border: 1.5px solid #A7F3D0; border-radius: 10px; padding: 0.85rem 1rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 1.1rem;"></i>
                                        <span style="font-size: 0.82rem; font-weight: 600; color: #065F46;">Active Conversion Formula:</span>
                                    </div>
                                    <div class="font-monospace" id="live-relation-formula-banner" style="font-size: 1.05rem; font-weight: 800; color: #047857;">
                                        {{ $unit->relation_formula }}
                                    </div>
                                </div>
                                <div class="font-monospace" id="live-relation-equiv-banner" style="font-size: 0.78rem; color: #047857; margin-top: 0.25rem; text-align: right;">
                                    {{ $unit->equivalent_formula ? '(' . $unit->equivalent_formula . ')' : '' }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- 3. Notes & Usage Description -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-gray">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Specifications &amp; Operational Notes</h3>
                                <p class="erp-form-section-desc">Usage context, packaging notes &amp; dispatch application</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Unit Description / Usage Notes
                            </label>
                            <textarea name="description" id="field-description" rows="3" class="form-control" placeholder="Enter description">{{ old('description', $unit->description) }}</textarea>
                            <span class="erp-field-hint">e.g. Derived linear unit used for packaging film dimensions. 100 cm = 1 m.</span>
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
                            <i class="fa-solid fa-eye"></i> Live Unit Preview
                        </div>
                        @if($unit->status === 'active')
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
                            {{ $unit->initials }}
                        </div>
                        <h4 class="erp-preview-title" id="preview-name" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; text-align: center; word-break: break-word;">
                            {{ $unit->name }}
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.3rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #1E293B; font-size: 0.80rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                {{ $unit->code }}
                            </span>
                            <span class="badge font-monospace" id="preview-uqc" style="background: #EFF6FF; color: #1D4ED8; font-size: 0.74rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; border: 1px solid #DBEAFE;">
                                UQC: {{ $unit->uqc_code ?: '—' }}
                            </span>
                        </div>
                    </div>

                    <!-- Highlighted Formula Box -->
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.95rem; margin: 1.15rem 0; text-align: center;">
                        <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.35rem;">
                            Defined Unit Relation
                        </div>
                        <div class="font-monospace" id="preview-formula" style="font-size: 1.12rem; font-weight: 800; color: #047857;">
                            {{ $unit->relation_formula }}
                        </div>
                        <div class="font-monospace" id="preview-formula-sub" style="font-size: 0.74rem; color: #64748B; margin-top: 0.2rem;">
                            {{ $unit->equivalent_formula ?: 'Standard Base Metric' }}
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Classification:</span>
                            <span class="erp-preview-meta-val" id="preview-classification">{{ $unit->is_base_unit ? 'Base Unit' : 'Derived Sub-Unit' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Base Reference:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-base-ref">{{ $unit->baseUnit ? $unit->baseUnit->name . ' (' . $unit->baseUnit->code . ')' : 'None (Standard)' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Conversion Factor:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-factor">{{ (float)$unit->conversion_factor }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Decimal Places:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-decimals">{{ $unit->decimal_places }} Decimals</span>
                        </div>
                    </div>

                    <!-- Interactive Quick Conversion Tester -->
                    <div style="margin-top: 1.15rem; padding-top: 1rem; border-top: 1px solid #E2E8F0;">
                        <div style="font-size: 0.74rem; font-weight: 700; color: #1E293B; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-calculator text-primary"></i> Live Conversion Tester
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="number" id="tester-input" value="100" class="form-control font-monospace" style="font-size: 0.82rem; padding: 0.4rem 0.6rem;" oninput="runLiveConversionTest()">
                            <span class="font-monospace" id="tester-unit-label" style="font-size: 0.8rem; font-weight: 700; color: #334155;">{{ $unit->code }}</span>
                            <span style="font-weight: 700; color: #94A3B8;">=</span>
                            <span class="font-monospace" id="tester-output" style="font-size: 0.88rem; font-weight: 800; color: #047857; min-width: 60px;">—</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Card -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-sidebar-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Update Measurement Unit
                    </button>
                    <a href="{{ route('admin.masters.unit') }}" class="btn btn-outline erp-btn-sidebar-cancel">
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
                            <strong>Unit ID:</strong> <span class="font-monospace text-dark">#{{ $unit->id }}</span>
                        </div>
                        <div>
                            <strong>Created:</strong> {{ $unit->created_at ? $unit->created_at->format('d M Y, h:i A') : 'System Initial' }}
                        </div>
                        <div>
                            <strong>Last Modified:</strong> {{ $unit->updated_at ? $unit->updated_at->format('d M Y, h:i A') : 'Never' }}
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
                        Archiving soft-deletes this unit. It will be hidden from new product and invoice creation.
                    </p>
                    <button type="button" class="btn btn-outline" style="border-color: #EF4444; color: #DC2626; width: 100%; font-size: 0.82rem; font-weight: 600;" onclick="confirmArchiveUnit()">
                        <i class="fa-regular fa-trash-can me-1"></i> Archive Unit
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Delete Hidden Form -->
<form id="archive-unit-form" action="{{ route('admin.masters.unit.destroy', $unit->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function toggleRelationSection() {
        const isDerived = document.getElementById('radio-type-derived').checked;
        const relationWrap = document.getElementById('relation-fields-wrap');
        const baseUnitSelect = document.getElementById('field-base-unit');
        
        if (isDerived) {
            relationWrap.style.display = 'grid';
            document.getElementById('label-unit-type-derived').style.borderColor = '#5B841E';
            document.getElementById('label-unit-type-derived').style.background = '#F9FBFA';
            document.getElementById('label-unit-type-base').style.borderColor = '#CBD5E1';
            document.getElementById('label-unit-type-base').style.background = '#FFFFFF';
            baseUnitSelect.setAttribute('required', 'required');
        } else {
            relationWrap.style.display = 'none';
            document.getElementById('label-unit-type-base').style.borderColor = '#5B841E';
            document.getElementById('label-unit-type-base').style.background = '#F9FBFA';
            document.getElementById('label-unit-type-derived').style.borderColor = '#CBD5E1';
            document.getElementById('label-unit-type-derived').style.background = '#FFFFFF';
            baseUnitSelect.removeAttribute('required');
            baseUnitSelect.value = '';
        }
        updateLivePreview();
    }

    function updateLivePreview() {
        const nameVal = document.getElementById('field-name').value.trim();
        const codeVal = document.getElementById('field-code').value.trim().toUpperCase() || '{{ $unit->code }}';
        const uqcElem = document.getElementById('field-uqc');
        const uqcVal = uqcElem.value;
        const decimalsVal = document.getElementById('field-decimals').value;
        const isDerived = document.getElementById('radio-type-derived').checked;
        const baseUnitElem = document.getElementById('field-base-unit');
        const operatorVal = document.getElementById('field-operator').value;
        const factorVal = parseFloat(document.getElementById('field-factor').value) || 1;

        // Avatar Initials
        let initials = '{{ $unit->initials }}';
        if (codeVal && codeVal.length >= 2) {
            initials = codeVal.substring(0, 2);
        }

        document.getElementById('preview-avatar').textContent = initials;
        document.getElementById('preview-name').textContent = nameVal || 'Unit Name';
        document.getElementById('preview-code').textContent = codeVal;
        document.getElementById('preview-uqc').textContent = uqcVal ? 'UQC: ' + uqcVal : 'UQC: —';
        document.getElementById('preview-decimals').textContent = decimalsVal + ' Decimals';

        if (isDerived && baseUnitElem.value) {
            const baseOption = baseUnitElem.options[baseUnitElem.selectedIndex];
            const baseCode = baseOption.getAttribute('data-code') || 'BASE';
            const baseName = baseOption.getAttribute('data-name') || 'Base Unit';

            let formulaText = '';
            let equivText = '';
            let factorStr = factorVal.toString();

            if (operatorVal === '/') {
                formulaText = `${factorStr} ${codeVal} = 1 ${baseCode}`;
                const equiv = factorVal > 0 ? (1 / factorVal).toFixed(4) : '0';
                equivText = `(1 ${codeVal} = ${equiv} ${baseCode})`;
            } else {
                formulaText = `1 ${codeVal} = ${factorStr} ${baseCode}`;
                const equiv = factorVal > 0 ? (1 / factorVal).toFixed(4) : '0';
                equivText = `(1 ${baseCode} = ${equiv} ${codeVal})`;
            }

            document.getElementById('live-relation-formula-banner').textContent = formulaText;
            document.getElementById('live-relation-equiv-banner').textContent = equivText;

            document.getElementById('preview-formula').textContent = formulaText;
            document.getElementById('preview-formula-sub').textContent = equivText;
            document.getElementById('preview-classification').textContent = 'Derived Sub-Unit';
            document.getElementById('preview-base-ref').textContent = `${baseName} (${baseCode})`;
            document.getElementById('preview-factor').textContent = factorVal.toFixed(4);
        } else {
            document.getElementById('preview-formula').textContent = 'Primary Base Unit';
            document.getElementById('preview-formula-sub').textContent = 'Standard Base Metric';
            document.getElementById('preview-classification').textContent = 'Base Unit';
            document.getElementById('preview-base-ref').textContent = 'None (Standard)';
            document.getElementById('preview-factor').textContent = '1.0000';
            document.getElementById('live-relation-formula-banner').textContent = 'Primary Base Unit';
            document.getElementById('live-relation-equiv-banner').textContent = '(Standard Base Metric)';
        }

        runLiveConversionTest();
    }

    function runLiveConversionTest() {
        const codeVal = document.getElementById('field-code').value.trim().toUpperCase() || '{{ $unit->code }}';
        const isDerived = document.getElementById('radio-type-derived').checked;
        const baseUnitElem = document.getElementById('field-base-unit');
        const operatorVal = document.getElementById('field-operator').value;
        const factorVal = parseFloat(document.getElementById('field-factor').value) || 1;
        const inputVal = parseFloat(document.getElementById('tester-input').value) || 0;

        document.getElementById('tester-unit-label').textContent = codeVal;

        if (isDerived && baseUnitElem.value) {
            const baseOption = baseUnitElem.options[baseUnitElem.selectedIndex];
            const baseCode = baseOption.getAttribute('data-code') || 'BASE';
            let result = 0;

            if (operatorVal === '/') {
                result = factorVal > 0 ? inputVal / factorVal : 0;
            } else {
                result = inputVal * factorVal;
            }
            document.getElementById('tester-output').textContent = result.toFixed(2) + ' ' + baseCode;
        } else {
            document.getElementById('tester-output').textContent = inputVal.toFixed(2) + ' ' + codeVal;
        }
    }

    function confirmArchiveUnit() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Measurement Unit?',
                html: `Are you sure you want to archive <strong>{{ addslashes($unit->name) }} ({{ $unit->code }})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the record from active unit master directories.</span>`,
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
                    document.getElementById('archive-unit-form').submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive {{ addslashes($unit->name) }} ({{ $unit->code }})?`)) {
                document.getElementById('archive-unit-form').submit();
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
