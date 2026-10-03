@extends('admin.layouts.app')

@section('title', 'Edit Vendor: ' . $vendor->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-vendor')

@section('content')
<section class="view-section active" id="view-vendor-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.vendor') }}">Vendor Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Vendor Profile</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-field text-primary"></i> Edit Vendor: {{ $vendor->name }}
            </h1>
            <p class="erp-page-subtitle">
                Update supplier firm details, tax compliance, contact persons &amp; bank settlement terms.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.vendor.show', $vendor->id) }}" class="btn btn-outline" title="View Supplier Profile">
                <i class="fa-regular fa-eye"></i> View Profile
            </a>
            <a href="{{ route('admin.masters.vendor') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Vendor List
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
    <form action="{{ route('admin.masters.vendor.update', $vendor->id) }}" method="POST" id="vendor-edit-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Supplier & Firm Profile -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-truck-field"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Supplier &amp; Firm Identity</h3>
                                <p class="erp-form-section-desc">Official supplier entity, vendor code &amp; primary contact</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Firm Name -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Vendor Firm Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-industry erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter vendor firm name" value="{{ old('name', $vendor->name) }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Registered supplier firm or trading enterprise name</span>
                        </div>

                        <!-- Vendor Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Vendor Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateVendorCode()" class="erp-btn-suggest" title="Generate Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vendor code" value="{{ old('code', $vendor->code) }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique ledger identifier (e.g. VND-01)</span>
                        </div>

                        <!-- Contact Person -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Contact Person
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user-tie erp-field-icon"></i>
                                <input type="text" name="contact_person" id="field-contact" class="form-control erp-field-input-iconified" placeholder="Enter contact person name" value="{{ old('contact_person', $vendor->contact_person) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Primary owner, manager or sales representative</span>
                        </div>

                        <!-- Mobile / Phone -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mobile / Phone Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-phone erp-field-icon"></i>
                                <input type="text" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="Enter mobile number" value="{{ old('phone', $vendor->phone) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Primary 10-digit calling &amp; WhatsApp number</span>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Email Address
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-envelope erp-field-icon"></i>
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="Enter email address" value="{{ old('email', $vendor->email) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Official email for purchase orders &amp; vouchers</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Statutory & Tax Registration -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-blue">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Statutory &amp; Tax Registration</h3>
                                <p class="erp-form-section-desc">Goods &amp; Services Tax Identification Number (GSTIN) and Income Tax PAN</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- GSTIN -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                GSTIN Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-stamp erp-field-icon"></i>
                                <input type="text" name="gstin" id="field-gstin" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter GSTIN number" value="{{ old('gstin', $vendor->gstin) }}" maxlength="15" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); handleGstinInput(this.value); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">15-digit alphanumeric GST registration code</span>
                        </div>

                        <!-- PAN -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                PAN Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-id-card erp-field-icon"></i>
                                <input type="text" name="pan" id="field-pan" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PAN number" value="{{ old('pan', $vendor->pan) }}" maxlength="10" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">10-digit Permanent Account Number</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Address & Geographical Location -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-amber">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Address &amp; Location</h3>
                                <p class="erp-form-section-desc">Registered factory premises, mandi yard or shipping address</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Address -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Full Street Address
                            </label>
                            <textarea name="address" id="field-address" class="form-control" rows="2" placeholder="Enter street address">{{ old('address', $vendor->address) }}</textarea>
                            <span class="erp-field-hint">Plot, street, industrial area or mandi address</span>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                City / District
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-city erp-field-icon"></i>
                                <input type="text" name="city" id="field-city" class="form-control erp-field-input-iconified" placeholder="Enter city" value="{{ old('city', $vendor->city) }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                State
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map erp-field-icon"></i>
                                <input type="text" name="state" id="field-state" class="form-control erp-field-input-iconified" placeholder="Enter state" value="{{ old('state', $vendor->state) }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Pincode
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-envelopes-bulk erp-field-icon"></i>
                                <input type="text" name="pincode" id="field-pincode" class="form-control erp-field-input-iconified" placeholder="Enter pincode" value="{{ old('pincode', $vendor->pincode) }}" maxlength="10">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Commercial Terms & Plant Assignment -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">4. Commercial Terms &amp; Plant Mapping</h3>
                                <p class="erp-form-section-desc">Credit period terms, initial ledger opening balance and plant linking</p>
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
                                <input type="number" step="0.01" name="opening_balance" id="field-opening-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter opening balance" value="{{ old('opening_balance', $vendor->opening_balance) }}" oninput="syncOpeningBalance()">
                            </div>
                            <span class="erp-field-hint">Initial registered ledger opening balance</span>
                        </div>

                        <!-- Current Balance (Payable) -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Current Balance (₹ Payable)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-bill-wave erp-field-icon"></i>
                                <input type="number" step="0.01" name="current_balance" id="field-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter current balance" value="{{ old('current_balance', $vendor->current_balance) }}" oninput="this.dataset.touched='true'; updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Active outstanding ledger balance payable to vendor</span>
                        </div>

                        <!-- Payment Terms -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Payment Terms
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-check erp-field-icon"></i>
                                <select name="payment_terms" id="field-terms" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="Immediate" {{ old('payment_terms', $vendor->payment_terms) === 'Immediate' ? 'selected' : '' }}>Immediate (Cash / Advance)</option>
                                    <option value="7 Days" {{ old('payment_terms', $vendor->payment_terms) === '7 Days' ? 'selected' : '' }}>7 Days</option>
                                    <option value="15 Days" {{ old('payment_terms', $vendor->payment_terms) === '15 Days' ? 'selected' : '' }}>15 Days</option>
                                    <option value="30 Days" {{ old('payment_terms', $vendor->payment_terms) === '30 Days' ? 'selected' : '' }}>30 Days</option>
                                    <option value="45 Days" {{ old('payment_terms', $vendor->payment_terms) === '45 Days' ? 'selected' : '' }}>45 Days</option>
                                    <option value="60 Days" {{ old('payment_terms', $vendor->payment_terms) === '60 Days' ? 'selected' : '' }}>60 Days</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Default agreed payment credit timeline</span>
                        </div>

                        <!-- Plant Mapping -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Company / Plant Linkage
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">-- All Plants / Global Procurement --</option>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->id }}" {{ old('company_id', $vendor->company_id) == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->name }} ({{ $comp->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Restrict vendor to a specific plant or leave global</span>
                        </div>


                        <!-- Notes -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Procurement Remarks / Notes
                            </label>
                            <textarea name="notes" id="field-notes" class="form-control" rows="2" placeholder="Enter remarks or procurement notes">{{ old('notes', $vendor->notes) }}</textarea>
                            <span class="erp-field-hint">Internal notes regarding materials supplied, quality, or mandi brokers</span>
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
                                <p class="erp-form-section-desc">Bank account information for NEFT / RTGS payout vouchers</p>
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
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name', $vendor->bank_name) }}">
                            </div>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-check erp-field-icon"></i>
                                <input type="text" name="bank_account_no" id="field-bank-acc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter account number" value="{{ old('bank_account_no', $vendor->bank_account_no) }}">
                            </div>
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-shield-halved erp-field-icon"></i>
                                <input type="text" name="bank_ifsc" id="field-bank-ifsc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter IFSC code" value="{{ old('bank_ifsc', $vendor->bank_ifsc) }}" maxlength="11" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');">
                            </div>
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Branch Name
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map-pin erp-field-icon"></i>
                                <input type="text" name="bank_branch" id="field-bank-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('bank_branch', $vendor->bank_branch) }}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-side-col">

                <!-- Live Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        Live Supplier Card Preview
                    </div>
                    <div class="erp-preview-body">
                        <div id="preview-avatar" class="erp-preview-avatar-circle" style="background: rgba(91, 132, 30, 0.12); color: var(--primary);">
                            {{ $vendor->initials }}
                        </div>
                        <h4 id="preview-name" class="erp-preview-name-text">
                            {{ $vendor->name }}
                        </h4>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <span id="preview-code" class="badge" style="background: #F1F5F9; color: #475569; font-family: var(--font-mono); font-size: 0.75rem; font-weight: 700;">
                                {{ $vendor->code }}
                            </span>
                            <span id="preview-terms" class="badge" style="background: rgba(91, 132, 30, 0.1); color: var(--primary); font-size: 0.72rem;">
                                {{ $vendor->payment_terms ?: '30 Days' }}
                            </span>
                        </div>

                        <!-- Balance Pill -->
                        <div class="p-2 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                            <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Current Balance (Payable)</div>
                            <div id="preview-balance" style="font-size: 1.25rem; font-weight: 800; color: #B45309;">
                                ₹{{ number_format($vendor->current_balance, 2) }}
                            </div>
                        </div>

                        <div class="erp-preview-meta-list text-start">
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-user-tie"></i>
                                <span id="preview-contact">{{ $vendor->contact_person ?: 'Contact Person' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-phone"></i>
                                <span id="preview-phone">{{ $vendor->phone ?: 'Phone Number' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-stamp"></i>
                                <span id="preview-gstin">{{ $vendor->gstin ?: 'Unregistered' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span id="preview-location">{{ $vendor->city ?: 'City' }}, {{ $vendor->state ?: 'State' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-building"></i>
                                <span id="preview-company">{{ $vendor->company ? $vendor->company->name : 'All Plants / Global' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Metadata Card -->
                <div class="card erp-profile-detail-card p-3">
                    <div class="erp-profile-audit-title mb-2">
                        <i class="fa-solid fa-clock-rotate-left"></i> Audit Trail
                    </div>
                    <div class="d-flex flex-column gap-2 text-muted" style="font-size: 0.82rem;">
                        <div class="d-flex justify-content-between">
                            <span>Vendor ID:</span>
                            <strong class="text-dark">#{{ $vendor->id }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Registered:</span>
                            <strong class="text-dark">{{ $vendor->created_at ? $vendor->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Last Updated:</span>
                            <strong class="text-dark">{{ $vendor->updated_at ? $vendor->updated_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="erp-btn-action-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.masters.vendor') }}" class="erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>

                <!-- Danger Zone (Archive Vendor) -->
                <div class="erp-alert-error-list-card p-3">
                    <div class="erp-alert-error-header mb-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </div>
                    <p class="erp-field-hint mt-0 mb-2">Soft delete this supplier ledger. Existing transactions remain safely intact.</p>
                    <button type="button" class="erp-btn-action-danger" onclick="confirmDeleteVendor({{ $vendor->id }}, '{{ addslashes($vendor->name) }}', '{{ $vendor->code }}')">
                        <i class="fa-solid fa-trash-can"></i> Archive Vendor Account
                    </button>
                </div>

            </div>
        </div>
    </form>

    <!-- Hidden Form for Safe Deletion -->
    <form id="delete-vendor-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
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
    if (gstin.length >= 12) {
        const extractedPan = gstin.substring(2, 12);
        document.getElementById('field-pan').value = extractedPan;
    }
}

function generateVendorCode() {
    fetch('{{ route("admin.masters.vendor.generate-code") }}?exclude_id={{ $vendor->id }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.code) {
            document.getElementById('field-code').value = data.code;
            updateLivePreview();
            if (typeof toastr !== 'undefined') toastr.success('Generated Vendor Code: ' + data.code);
        }
    })
    .catch(err => {
        console.error(err);
    });
}

function syncOpeningBalance() {
    const openingInput = document.getElementById('field-opening-balance');
    const currentInput = document.getElementById('field-balance');
    if (openingInput && currentInput && currentInput.dataset.touched !== 'true') {
        currentInput.value = openingInput.value;
    }
    updateLivePreview();
}

function updateLivePreview() {
    const name = document.getElementById('field-name').value.trim() || 'Vendor Firm Name';
    const code = document.getElementById('field-code').value.trim() || 'VND-00';
    const contact = document.getElementById('field-contact').value.trim() || 'Contact Person';
    const phone = document.getElementById('field-phone').value.trim() || 'Phone Number';
    const gstin = document.getElementById('field-gstin').value.trim() || 'GSTIN Number';
    const city = document.getElementById('field-city').value.trim() || 'City';
    const state = document.getElementById('field-state').value.trim() || 'State';
    const terms = document.getElementById('field-terms').value || '30 Days';

    const balanceInput = document.getElementById('field-balance');
    const balance = balanceInput ? (Number(balanceInput.value) || 0) : 0;

    const companySelect = document.getElementById('field-company');
    const companyText = companySelect.options[companySelect.selectedIndex].text;

    // Initials
    const words = name.split(/\s+/);
    let initials = 'VN';
    if (words.length >= 2 && words[0] && words[1]) {
        initials = (words[0][0] + words[1][0]).toUpperCase();
    } else if (words.length >= 1 && words[0]) {
        initials = words[0].substring(0, 2).toUpperCase();
    }

    document.getElementById('preview-avatar').innerText = initials;
    document.getElementById('preview-name').innerText = name;
    document.getElementById('preview-code').innerText = code;
    document.getElementById('preview-terms').innerText = terms;

    const previewBal = document.getElementById('preview-balance');
    if (previewBal) {
        previewBal.innerText = '₹' + balance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.getElementById('preview-contact').innerText = contact;
    document.getElementById('preview-phone').innerText = phone;
    document.getElementById('preview-gstin').innerText = gstin;
    document.getElementById('preview-location').innerText = city + ', ' + state;

    if (companySelect.value) {
        document.getElementById('preview-company').innerText = companyText;
    } else {
        document.getElementById('preview-company').innerText = 'All Plants / Global';
    }
}

function confirmDeleteVendor(vendorId, vendorName, vendorCode) {
    Swal.fire({
        title: 'Archive Vendor Account?',
        html: `Are you sure you want to remove vendor <strong>"${vendorName}"</strong> (${vendorCode})?<br><small class="text-muted">This supplier will be archived and hidden from transaction entry dropdowns.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Archive Vendor',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-border-radius'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-vendor-form');
            form.action = `/admin/masters/vendor/${vendorId}`;
            form.submit();
        }
    });
}
</script>
@endpush
