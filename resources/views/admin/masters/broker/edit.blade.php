@extends('admin.layouts.app')

@section('title', 'Edit Broker: ' . $broker->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-broker')

@section('content')
<section class="view-section active" id="view-broker-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.broker') }}">Broker Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit: {{ $broker->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Broker Profile
            </h1>
            <p class="erp-page-subtitle">
                Update commission rates, TDS PAN details, mandi contact numbers, and bank payout instructions.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.broker.show', $broker->id) }}" class="btn btn-outline" title="View Broker Profile">
                <i class="fa-solid fa-eye"></i> View Profile
            </a>
            <a href="{{ route('admin.masters.broker') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
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
    <form action="{{ route('admin.masters.broker.update', $broker->id) }}" method="POST" id="broker-edit-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Agency & Broker Identity -->
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
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter broker or agency name" value="{{ old('name', $broker->name) }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Registered commission agency firm or individual mandi broker name</span>
                        </div>

                        <!-- Broker Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Broker Code <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter broker code" value="{{ old('code', $broker->code) }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique ledger identifier</span>
                        </div>

                        <!-- Contact Person -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Primary Contact Person
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-user erp-field-icon"></i>
                                <input type="text" name="contact_person" id="field-contact" class="form-control erp-field-input-iconified" placeholder="Enter contact person" value="{{ old('contact_person', $broker->contact_person) }}" oninput="updateLivePreview()">
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
                                <input type="number" step="0.01" min="0" max="100" name="commission_rate" id="field-commission" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter commission rate" value="{{ old('commission_rate', $broker->commission_rate) }}" required oninput="updateLivePreview()">
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
                                    <option value="Percentage (%)" {{ old('brokerage_type', $broker->brokerage_type) === 'Percentage (%)' ? 'selected' : '' }}>Percentage (%) on Bill Value</option>
                                    <option value="Per KG Rate (₹/KG)" {{ old('brokerage_type', $broker->brokerage_type) === 'Per KG Rate (₹/KG)' ? 'selected' : '' }}>Per KG Rate (₹/KG Weight)</option>
                                    <option value="Flat Per Consignment" {{ old('brokerage_type', $broker->brokerage_type) === 'Flat Per Consignment' ? 'selected' : '' }}>Flat Amount Per Consignment</option>
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
                                <input type="text" name="pan" id="field-pan" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PAN number" value="{{ old('pan', $broker->pan) }}" maxlength="10" oninput="this.value = this.value.toUpperCase(); updateLivePreview();">
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
                                <input type="text" name="gstin" id="field-gstin" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter GSTIN number" value="{{ old('gstin', $broker->gstin) }}" maxlength="15" oninput="this.value = this.value.toUpperCase(); updateLivePreview();">
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
                                <input type="tel" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="Enter mobile number" value="{{ old('phone', $broker->phone) }}" oninput="updateLivePreview()">
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
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="Enter email address" value="{{ old('email', $broker->email) }}">
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
                                <input type="text" name="address" id="field-address" class="form-control erp-field-input-iconified" placeholder="Enter shop or mandi yard address" value="{{ old('address', $broker->address) }}">
                            </div>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mandi City / Town
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-city erp-field-icon"></i>
                                <input type="text" name="city" id="field-city" class="form-control erp-field-input-iconified" placeholder="Enter city" value="{{ old('city', $broker->city) }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                State / Province
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map erp-field-icon"></i>
                                <input type="text" name="state" id="field-state" class="form-control erp-field-input-iconified" placeholder="Enter state" value="{{ old('state', $broker->state) }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Postal Pincode
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-envelopes-bulk erp-field-icon"></i>
                                <input type="text" name="pincode" id="field-pincode" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter pincode" value="{{ old('pincode', $broker->pincode) }}" maxlength="10">
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
                                <p class="erp-form-section-desc">Opening and current balance adjustments with company plant mapping</p>
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
                                <input type="number" step="0.01" name="opening_balance" id="field-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter opening balance" value="{{ old('opening_balance', $broker->opening_balance) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Initial outstanding commission balance payable</span>
                        </div>

                        <!-- Current Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Current Balance (₹ Payable)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-bill-wave erp-field-icon"></i>
                                <input type="number" step="0.01" name="current_balance" id="field-current-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter current balance" value="{{ old('current_balance', $broker->current_balance) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Active net ledger balance payable to broker</span>
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
                                        <option value="{{ $comp->id }}" {{ old('company_id', $broker->company_id) == $comp->id ? 'selected' : '' }}>
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
                            <textarea name="notes" id="field-notes" class="form-control" rows="2" placeholder="Enter remarks or brokerage notes">{{ old('notes', $broker->notes) }}</textarea>
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
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name', $broker->bank_name) }}">
                            </div>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-check erp-field-icon"></i>
                                <input type="text" name="bank_account_no" id="field-bank-acc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter account number" value="{{ old('bank_account_no', $broker->bank_account_no) }}">
                            </div>
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-shield-halved erp-field-icon"></i>
                                <input type="text" name="bank_ifsc" id="field-bank-ifsc" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter IFSC code" value="{{ old('bank_ifsc', $broker->bank_ifsc) }}" maxlength="15" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Branch Name / Location
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-flag erp-field-icon"></i>
                                <input type="text" name="bank_branch" id="field-bank-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('bank_branch', $broker->bank_branch) }}">
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
                            {{ $broker->initials }}
                        </div>

                        <h4 class="erp-preview-title" id="preview-name">{{ $broker->name }}</h4>

                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge erp-preview-code-badge font-monospace" id="preview-code">{{ $broker->code }}</span>
                            <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #047857; font-weight: 700;" id="preview-commission">
                                {{ number_format($broker->commission_rate, 2) }}% Commission
                            </span>
                            @if($broker->status === 'active')
                                <span class="erp-status-btn erp-status-btn-active" style="padding: 2px 8px; font-size: 0.72rem; cursor: default;">
                                    <span class="erp-status-dot-green"></span> Active
                                </span>
                            @else
                                <span class="erp-status-btn erp-status-btn-inactive" style="padding: 2px 8px; font-size: 0.72rem; cursor: default;">
                                    <span class="erp-status-dot-red"></span> Inactive
                                </span>
                            @endif
                        </div>

                        <!-- Current Balance Box -->
                        <div class="p-2 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                            <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase;">Current Commission Payable</div>
                            <div id="preview-balance" style="font-size: 1.25rem; font-weight: 800; color: #7E22CE; font-family: var(--font-mono);">
                                ₹{{ number_format($broker->current_balance, 2) }}
                            </div>
                        </div>

                        <div class="erp-preview-meta-list text-start">
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-user-tie"></i>
                                <span id="preview-contact">{{ $broker->contact_person ?: 'Contact Person' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-phone"></i>
                                <span id="preview-phone">{{ $broker->phone ?: 'Phone Number' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-id-card"></i>
                                <span id="preview-pan">{{ $broker->pan ?: 'PAN Number' }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-location-dot"></i>
                                <span id="preview-location">{{ $broker->city }}, {{ $broker->state }}</span>
                            </div>
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-building"></i>
                                <span id="preview-company">{{ $broker->company ? $broker->company->name : 'All Plants / Global' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="card erp-sidebar-actions-card mb-4">
                    <button type="submit" class="erp-btn-action-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.masters.broker') }}" class="erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>

                <!-- Danger Zone Card -->
                <div class="erp-alert-error-list-card p-3 mb-4">
                    <div class="erp-alert-error-header mb-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </div>
                    <p class="erp-field-hint mt-0 mb-2">Soft delete this broker ledger. Existing order bookings and commission payouts remain safely intact.</p>
                    <button type="button" class="erp-btn-action-danger" onclick="confirmDeleteBroker({{ $broker->id }}, '{{ addslashes($broker->name) }}', '{{ $broker->code }}')">
                        <i class="fa-solid fa-trash-can"></i> Archive Broker Account
                    </button>
                </div>

                <!-- Audit Stamp Card -->
                <div class="card erp-profile-detail-card mb-4">
                    <div class="erp-profile-detail-header">
                        <i class="fa-solid fa-clock-rotate-left text-primary"></i> System Ledger Stamps
                    </div>
                    <div class="erp-profile-detail-body-single">
                        <div class="erp-profile-detail-row">
                            <span class="erp-profile-detail-label">Record ID</span>
                            <strong class="font-monospace text-dark">#{{ $broker->id }}</strong>
                        </div>
                        <div class="erp-profile-detail-row">
                            <span class="erp-profile-detail-label">Created</span>
                            <strong class="text-dark" style="font-size: 0.82rem;">{{ $broker->created_at ? $broker->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                        </div>
                        <div class="erp-profile-detail-row">
                            <span class="erp-profile-detail-label">Updated</span>
                            <strong class="text-dark" style="font-size: 0.82rem;">{{ $broker->updated_at ? $broker->updated_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <!-- Hidden Form for Safe Deletion -->
    <form id="delete-broker-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</section>
@endsection

@push('scripts')
<script>
function updateLivePreview() {
    const name = document.getElementById('field-name').value.trim() || 'Broker Agency Name';
    const code = document.getElementById('field-code').value.trim() || 'BRK-01';
    const contact = document.getElementById('field-contact').value.trim() || 'Contact Person';
    const phone = document.getElementById('field-phone').value.trim() || 'Phone Number';
    const pan = document.getElementById('field-pan').value.trim() || 'PAN Number';
    const city = document.getElementById('field-city').value.trim() || 'Sojat City';
    const state = document.getElementById('field-state').value.trim() || 'Rajasthan';
    const commission = document.getElementById('field-commission').value.trim() || '1.50';
    const balance = parseFloat(document.getElementById('field-current-balance').value) || parseFloat(document.getElementById('field-balance').value) || 0;

    const companySelect = document.getElementById('field-company');
    const companyText = companySelect.selectedIndex > 0 ? companySelect.options[companySelect.selectedIndex].text : 'All Plants / Global';

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

function confirmDeleteBroker(id, name, code) {
    Swal.fire({
        title: 'Archive Broker Account?',
        html: `Are you sure you want to archive broker <strong>"${name}"</strong> (<code style="color:var(--primary); font-family:monospace;">${code}</code>)?<br><small class="text-muted mt-2 d-block">Existing commission ledgers and linked sales orders will remain intact.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, Archive Account',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-broker-form');
            form.action = `/admin/masters/broker/${id}`;
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', updateLivePreview);
</script>
@endpush
