@extends('admin.layouts.app')

@section('title', 'Add New Broker - VIKAS UDHYOG ERP')
@section('page_code', 'master-broker')

@section('content')
<section class="view-section active" id="view-broker-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.broker') }}">Broker Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Add New Broker</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-handshake text-primary"></i> Add New Broker Profile
            </h1>
            <p class="erp-page-subtitle">
                Register mandi commission agent, brokerage rates, PAN compliance, plant linkage &amp; bank settlement details.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.broker') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Broker List
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
    <form action="{{ route('admin.masters.broker.store') }}" method="POST" id="broker-create-form">
        @csrf

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Broker & Agency Profile -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Agency &amp; Broker Identity</h3>
                                <p class="erp-form-section-desc">Official broker agency name, code, contact &amp; commission terms</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Agency Name -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Broker / Agency Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user-tie erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter broker or agency name" value="{{ old('name') }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Registered commission agency firm or individual mandi broker name</span>
                        </div>

                        <!-- Broker Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Broker Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateBrokerCode()" class="erp-btn-suggest" title="Generate Next Broker Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter broker code" value="{{ old('code', $suggestedCode ?? 'BRK-01') }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique ledger identifier (e.g. BRK-01)</span>
                        </div>

                        <!-- Contact Person -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Primary Contact Person
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-user erp-field-icon"></i>
                                <input type="text" name="contact_person" id="field-contact" class="form-control erp-field-input-iconified" placeholder="Enter contact person" value="{{ old('contact_person') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Key representative managing consignments</span>
                        </div>

                        <!-- Commission Rate -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Commission Rate (%) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-percent erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" max="100" name="commission_rate" id="field-commission" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter commission rate" value="{{ old('commission_rate', '1.50') }}" required oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Standard agreed mandi brokerage percentage</span>
                        </div>

                        <!-- Brokerage Calculation Type -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Brokerage Calculation Basis
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calculator erp-field-icon"></i>
                                <select name="brokerage_type" id="field-type" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="Percentage (%)" {{ old('brokerage_type', 'Percentage (%)') === 'Percentage (%)' ? 'selected' : '' }}>Percentage (%) on Bill Value</option>
                                    <option value="Per KG Rate (₹/KG)" {{ old('brokerage_type') === 'Per KG Rate (₹/KG)' ? 'selected' : '' }}>Per KG Rate (₹/KG Weight)</option>
                                    <option value="Flat Per Consignment" {{ old('brokerage_type') === 'Flat Per Consignment' ? 'selected' : '' }}>Flat Amount Per Consignment</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Method used to auto-calculate brokerage during invoicing</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Statutory & TDS Compliance -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(126, 34, 206, 0.12); color: #7E22CE;">
                                <i class="fa-solid fa-stamp"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Statutory &amp; TDS Compliance</h3>
                                <p class="erp-form-section-desc">Income Tax PAN for TDS Section 194H brokerage compliance &amp; GSTIN</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- PAN Number -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Income Tax PAN Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-id-card erp-field-icon"></i>
                                <input type="text" name="pan" id="field-pan" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PAN number" value="{{ old('pan') }}" maxlength="10" oninput="this.value = this.value.toUpperCase(); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Mandatory for TDS deduction under Section 194H</span>
                        </div>

                        <!-- GSTIN -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                GSTIN Registration (If Applicable)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                                <input type="text" name="gstin" id="field-gstin" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter GSTIN number" value="{{ old('gstin') }}" maxlength="15" oninput="this.value = this.value.toUpperCase(); handleGstinInput(this.value); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">15-digit GST identifier for registered agents</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Contact & Mandi Location -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Contact &amp; Mandi Location</h3>
                                <p class="erp-form-section-desc">Communication channels &amp; agricultural mandi hub address</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Mobile Phone -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mobile / WhatsApp Phone
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-phone erp-field-icon"></i>
                                <input type="tel" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="Enter mobile number" value="{{ old('phone') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Primary number for dispatch alerts &amp; WhatsApp communication</span>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Email Address
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-envelope erp-field-icon"></i>
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="Enter email address" value="{{ old('email') }}">
                            </div>
                            <span class="erp-field-hint">For monthly commission statements &amp; vouchers</span>
                        </div>

                        <!-- Street Address -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Shop / Mandi Yard Address
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-location-dot erp-field-icon"></i>
                                <input type="text" name="address" id="field-address" class="form-control erp-field-input-iconified" placeholder="Enter shop or mandi yard address" value="{{ old('address') }}">
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mandi City / Town
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-city erp-field-icon"></i>
                                <input type="text" name="city" id="field-city" class="form-control erp-field-input-iconified" placeholder="Enter city" value="{{ old('city', 'Sojat City') }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                State / Province
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map erp-field-icon"></i>
                                <input type="text" name="state" id="field-state" class="form-control erp-field-input-iconified" placeholder="Enter state" value="{{ old('state', 'Rajasthan') }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Postal Pincode
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-envelopes-bulk erp-field-icon"></i>
                                <input type="text" name="pincode" id="field-pincode" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter pincode" value="{{ old('pincode', '306104') }}" maxlength="10">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Financial Balance & Plant Assignment -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">4. Ledger Balance &amp; Plant Assignment</h3>
                                <p class="erp-form-section-desc">Opening commission balance and manufacturing unit binding</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Opening Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Opening Balance (₹ Payable)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                                <input type="number" step="0.01" name="opening_balance" id="field-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter opening balance" value="{{ old('opening_balance', '0.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Initial outstanding commission balance payable to broker</span>
                        </div>

                        <!-- Plant Mapping -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Company / Plant Linkage
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">-- All Plants / Global Intermediation --</option>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->name }} ({{ $comp->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Restrict broker to a specific plant or leave multi-unit global</span>
                        </div>

                        <!-- Notes -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Mandi Brokerage Remarks / Notes
                            </label>
                            <textarea name="notes" id="field-notes" class="form-control" rows="2" placeholder="Enter remarks or brokerage notes">{{ old('notes') }}</textarea>
                            <span class="erp-field-hint">Internal operational notes regarding mandi routes, quality verification, or party credit</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Bank Settlement Details -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #10B981;">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">5. Bank Settlement Details</h3>
                                <p class="erp-form-section-desc">Bank account details for commission payout vouchers via NEFT / RTGS</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Bank Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank Name
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-landmark erp-field-icon"></i>
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name') }}">
                            </div>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-check erp-field-icon"></i>
                                <input type="text" name="bank_account_no" id="field-bank-acc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter account number" value="{{ old('bank_account_no') }}">
                            </div>
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-shield-halved erp-field-icon"></i>
                                <input type="text" name="bank_ifsc" id="field-bank-ifsc" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter IFSC code" value="{{ old('bank_ifsc') }}" maxlength="15" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Branch Name / Location
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-flag erp-field-icon"></i>
                                <input type="text" name="bank_branch" id="field-bank-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('bank_branch') }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-side-col">

                <!-- Dynamic Live Preview Card -->
                <div class="card erp-preview-card text-center mb-4">
                    <div class="card-body p-4">
                        <div class="erp-preview-avatar-circle mx-auto mb-3" id="preview-avatar">
                            BR
                        </div>

                        <h4 class="erp-preview-title" id="preview-name">Broker Agency Name</h4>

                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge erp-preview-code-badge font-monospace" id="preview-code">BRK-01</span>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #047857; font-weight: 700;" id="preview-commission">
                                1.50% Commission
                            </span>
                            <span class="erp-status-btn erp-status-btn-active" style="padding: 2px 8px; font-size: 0.72rem; cursor: default;">
                                <span class="erp-status-dot-green"></span> Active
                            </span>
                        </div>

                        <!-- Balance Box -->
                        <div class="p-2 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                            <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Opening Commission Payable</div>
                            <div id="preview-balance" style="font-size: 1.25rem; font-weight: 800; color: #7E22CE; font-family: var(--font-mono);">
                                ₹0.00
                            </div>
                        </div>

                        <div class="erp-preview-meta-list text-start">
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-user-tie"></i>
                                <span id="preview-contact">Contact Person</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-phone"></i>
                                <span id="preview-phone">Phone Number</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-id-card"></i>
                                <span id="preview-pan">PAN Number</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span id="preview-location">Sojat City, Rajasthan</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-building"></i>
                                <span id="preview-company">All Plants / Global</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="card erp-sidebar-actions-card mb-4">
                    <button type="submit" class="erp-btn-action-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Save Broker Profile
                    </button>
                    <a href="{{ route('admin.masters.broker') }}" class="erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>

                <!-- Policy Box -->
                <div class="erp-profile-mod-badge flex-column align-items-start p-3 mb-4">
                    <div class="erp-alert-error-header mb-1 text-primary">
                        <i class="fa-solid fa-circle-info"></i> Broker Master Policy
                    </div>
                    <span class="erp-field-hint mt-0">Once registered, this broker will be selectable in <strong>Sales Orders</strong>, <strong>Weighbridge Mandi Entries</strong>, and <strong>Commission Vouchers</strong>. Opening balances post directly to the brokerage ledger.</span>
                </div>

            </div>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
const GST_STATE_CODES = {
    '08': 'Rajasthan',
    '24': 'Gujarat',
    '27': 'Maharashtra',
    '07': 'Delhi',
    '23': 'Madhya Pradesh',
    '09': 'Uttar Pradesh',
    '03': 'Punjab',
    '06': 'Haryana',
    '19': 'West Bengal',
    '33': 'Tamil Nadu',
    '29': 'Karnataka',
    '36': 'Telangana',
    '37': 'Andhra Pradesh',
    '10': 'Bihar'
};

function handleGstinInput(gstin) {
    if (gstin.length >= 2) {
        const stateCode = gstin.substring(0, 2);
        if (GST_STATE_CODES[stateCode]) {
            document.getElementById('field-state').value = GST_STATE_CODES[stateCode];
        }
    }
}

function generateBrokerCode() {
    fetch("{{ route('admin.masters.broker.generate-code') }}?prefix=BRK")
        .then(response => response.json())
        .then(data => {
            if (data.success && data.code) {
                const codeInput = document.getElementById('field-code');
                codeInput.value = data.code;
                updateLivePreview();
                if (typeof toastr !== 'undefined') {
                    toastr.info('Generated unique code: ' + data.code);
                }
            }
        })
        .catch(() => {
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to auto-generate broker code.');
            }
        });
}

function updateLivePreview() {
    const name = document.getElementById('field-name').value.trim() || 'Broker Agency Name';
    const code = document.getElementById('field-code').value.trim() || 'BRK-01';
    const contact = document.getElementById('field-contact').value.trim() || 'Contact Person';
    const phone = document.getElementById('field-phone').value.trim() || 'Phone Number';
    const pan = document.getElementById('field-pan').value.trim() || 'PAN Number';
    const city = document.getElementById('field-city').value.trim() || 'Sojat City';
    const state = document.getElementById('field-state').value.trim() || 'Rajasthan';
    const commission = document.getElementById('field-commission').value.trim() || '1.50';
    const balance = parseFloat(document.getElementById('field-balance').value) || 0;

    const companySelect = document.getElementById('field-company');
    const companyText = companySelect.selectedIndex > 0 ? companySelect.options[companySelect.selectedIndex].text : 'All Plants / Global';

    // Initials calculation
    const words = name.split(/\s+/).filter(Boolean);
    let initials = 'BR';
    if (words.length >= 2) {
        initials = (words[0][0] + words[1][0]).toUpperCase();
    } else if (words.length === 1 && words[0].length >= 2) {
        initials = words[0].substring(0, 2).toUpperCase();
    }

    document.getElementById('preview-name').innerText = name;
    document.getElementById('preview-code').innerText = code;
    document.getElementById('preview-avatar').innerText = initials;
    document.getElementById('preview-contact').innerText = contact;
    document.getElementById('preview-phone').innerText = phone;
    document.getElementById('preview-pan').innerText = pan;
    document.getElementById('preview-location').innerText = `${city}, ${state}`;
    document.getElementById('preview-company').innerText = companyText;
    document.getElementById('preview-commission').innerText = `${parseFloat(commission).toFixed(2)}% Commission`;
    document.getElementById('preview-balance').innerText = '₹' + balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.addEventListener('DOMContentLoaded', updateLivePreview);
</script>
@endpush
