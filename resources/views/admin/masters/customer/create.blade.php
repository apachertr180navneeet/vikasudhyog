@extends('admin.layouts.app')

@section('title', 'Add New Customer - VIKAS UDHYOG ERP')
@section('page_code', 'master-customer')

@section('content')
<section class="view-section active" id="view-customer-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.customer') }}">Customer Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Add New Customer</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-user-plus text-primary"></i> Add New Customer Profile
            </h1>
            <p class="erp-page-subtitle">
                Register client account, wholesale distributor, credit limit, payment terms &amp; dispatch destination.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.customer') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Customer List
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
    <form action="{{ route('admin.masters.customer.store') }}" method="POST" id="customer-create-form">
        @csrf

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Customer & Firm Profile -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-building-user"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Customer &amp; Enterprise Identity</h3>
                                <p class="erp-form-section-desc">Client business name, code identifier &amp; trade classification</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Customer Name -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Customer / Client Firm Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-industry erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter customer name" value="{{ old('name') }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Registered business, distribution agency, or proprietary name</span>
                        </div>

                        <!-- Customer Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Customer Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateCustomerCode()" class="erp-btn-suggest" title="Generate Next Customer Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter customer code" value="{{ old('code', $suggestedCode ?? 'CST-01') }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique ledger identifier (e.g. CST-01)</span>
                        </div>

                        <!-- Customer Type -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Client Classification / Type <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-tag erp-field-icon"></i>
                                <select name="customer_type" id="field-type" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    @foreach($types as $typeOption)
                                        <option value="{{ $typeOption }}" {{ old('customer_type', 'Distributor') === $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Business model category</span>
                        </div>

                        <!-- Assigned Company / Plant -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Assigned Processing Plant / Company Branch
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">-- All Units / General --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} {{ $company->city ? '(' . $company->city . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Select company unit managing sales dispatches for this client</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Contact & Tax Compliance -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-address-book"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Contact &amp; Statutory Compliance</h3>
                                <p class="erp-form-section-desc">Key point of contact, communications &amp; GSTIN / PAN details</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Contact Person -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Primary Contact Person
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user-tie erp-field-icon"></i>
                                <input type="text" name="contact_person" id="field-contact" class="form-control erp-field-input-iconified" placeholder="Enter contact person" value="{{ old('contact_person') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Proprietor, director, or purchase manager</span>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mobile / Phone Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-phone erp-field-icon"></i>
                                <input type="tel" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="Enter mobile number" value="{{ old('phone') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Primary number for dispatch notices &amp; order updates</span>
                        </div>

                        <!-- Email -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Email Address
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-envelope erp-field-icon"></i>
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="Enter email address" value="{{ old('email') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">For digital invoices &amp; dispatch manifests</span>
                        </div>

                        <!-- GSTIN -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                GSTIN Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-stamp erp-field-icon"></i>
                                <input type="text" name="gstin" id="field-gstin" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter GSTIN" value="{{ old('gstin') }}" maxlength="15" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); handleGstinInput(this.value); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">15-character alphanumeric GST registration</span>
                        </div>

                        <!-- PAN -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                PAN Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-id-card erp-field-icon"></i>
                                <input type="text" name="pan" id="field-pan" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PAN" value="{{ old('pan') }}" maxlength="10" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">10-character Permanent Account Number</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Billing & Dispatch Address -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-amber">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Billing &amp; Dispatch Location</h3>
                                <p class="erp-form-section-desc">Warehouse, shop, or retail premises address for consignments</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Address -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Street Address / Warehouse Location
                            </label>
                            <textarea name="address" id="field-address" class="form-control" rows="2" placeholder="Enter billing address">{{ old('address') }}</textarea>
                            <span class="erp-field-hint">Full road, shop number, mandi complex or commercial zone</span>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                City / Mandi Hub
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-city erp-field-icon"></i>
                                <input type="text" name="city" id="field-city" class="form-control erp-field-input-iconified" placeholder="Enter city" value="{{ old('city') }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                State
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map erp-field-icon"></i>
                                <input type="text" name="state" id="field-state" class="form-control erp-field-input-iconified" placeholder="Enter state" value="{{ old('state', 'Rajasthan') }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Postal Pincode
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-envelopes-bulk erp-field-icon"></i>
                                <input type="text" name="pincode" id="field-pincode" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter pincode" value="{{ old('pincode') }}" maxlength="10">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Credit Terms & Financial Setup -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(139, 92, 246, 0.12); color: #8B5CF6;">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">4. Credit Limits &amp; Financial Setup</h3>
                                <p class="erp-form-section-desc">Permitted trade credit limit, payment grace duration &amp; opening balance</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Credit Limit -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Approved Credit Limit (₹) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-credit-card erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="credit_limit" id="field-credit-limit" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter credit limit" value="{{ old('credit_limit', '300000.00') }}" required oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Max outstanding balance before auto-hold on new orders</span>
                        </div>

                        <!-- Payment Terms -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Credit Duration / Payment Terms
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-days erp-field-icon"></i>
                                <input type="text" name="payment_terms" id="field-terms" class="form-control erp-field-input-iconified" placeholder="Enter payment terms" value="{{ old('payment_terms', '30 Days') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. 15 Days, 30 Days, 45 Days, Immediate</span>
                        </div>

                        <!-- Opening Balance -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Opening Receivable Balance (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="opening_balance" id="field-opening-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter opening balance" value="{{ old('opening_balance', '0.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Initial outstanding ledger balance carried forward</span>
                        </div>
                    </div>
                </div>

                <!-- 5. Bank Settlement & Operational Notes -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">5. Bank Settlement &amp; Operational Notes</h3>
                                <p class="erp-form-section-desc">Client remittance bank account details and special trade instructions</p>
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
                                <i class="fa-solid fa-building-columns erp-field-icon"></i>
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name') }}">
                            </div>
                        </div>

                        <!-- Bank Account No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-check erp-field-icon"></i>
                                <input type="text" name="bank_account_no" id="field-bank-acc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter account number" value="{{ old('bank_account_no') }}">
                            </div>
                        </div>

                        <!-- Bank IFSC -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-code-branch erp-field-icon"></i>
                                <input type="text" name="bank_ifsc" id="field-bank-ifsc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter IFSC code" value="{{ old('bank_ifsc') }}" maxlength="11" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');">
                            </div>
                        </div>

                        <!-- Bank Branch -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Branch Name
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map-pin erp-field-icon"></i>
                                <input type="text" name="bank_branch" id="field-bank-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('bank_branch') }}">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Trade Notes / Remarks
                            </label>
                            <textarea name="notes" id="field-notes" class="form-control" rows="3" placeholder="Enter notes">{{ old('notes') }}</textarea>
                            <span class="erp-field-hint">Preferred transport carriers, delivery slots or billing conditions</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-side-col">

                <!-- 1. Live Dynamic Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        <span class="erp-preview-badge">
                            <i class="fa-solid fa-eye"></i> Live Preview
                        </span>
                        <span class="erp-status-btn erp-status-btn-active" style="padding: 2px 8px; font-size: 0.72rem; cursor: default;">
                            <span class="erp-status-dot-green"></span> Active
                        </span>
                    </div>

                    <div class="erp-preview-body">
                        <!-- Avatar & Title -->
                        <div class="erp-preview-hero">
                            <div class="erp-preview-avatar" id="preview-avatar">
                                CS
                            </div>
                            <div class="erp-preview-hero-info">
                                <div class="erp-preview-name" id="preview-name">Customer Name</div>
                                <div class="erp-preview-meta-row">
                                    <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.75rem; border: 1px solid #E2E8F0;">
                                        {{ old('code', $suggestedCode ?? 'CST-01') }}
                                    </span>
                                    <span class="badge" id="preview-type" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem;">
                                        Distributor
                                    </span>
                                </div>
                            </div>
                        </div>

                        <hr class="erp-preview-divider">

                        <!-- Financial Snapshot -->
                        <div class="erp-preview-stats-grid">
                            <div class="erp-preview-stat-item">
                                <span class="erp-preview-stat-label">Credit Limit</span>
                                <span class="erp-preview-stat-val font-monospace" id="preview-credit-limit">₹300,000.00</span>
                            </div>
                            <div class="erp-preview-stat-item">
                                <span class="erp-preview-stat-label">Opening Due</span>
                                <span class="erp-preview-stat-val font-monospace" id="preview-balance" style="color: #059669;">₹0.00</span>
                            </div>
                        </div>

                        <hr class="erp-preview-divider">

                        <!-- Meta List -->
                        <div class="erp-preview-meta-list">
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-user erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">Contact Person</div>
                                    <div class="erp-preview-meta-val" id="preview-contact">Not specified</div>
                                </div>
                            </div>

                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-phone erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">Mobile Number</div>
                                    <div class="erp-preview-meta-val font-monospace" id="preview-phone">Not specified</div>
                                </div>
                            </div>

                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-location-dot erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">City / State</div>
                                    <div class="erp-preview-meta-val" id="preview-location">Rajasthan</div>
                                </div>
                            </div>

                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-stamp erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">GSTIN</div>
                                    <div class="erp-preview-meta-val font-monospace" id="preview-gstin">Unregistered</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Sidebar Action Buttons -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-action-submit" id="btn-save-customer">
                        <i class="fa-solid fa-floppy-disk"></i> Save Customer Account
                    </button>
                    <a href="{{ route('admin.masters.customer') }}" class="btn btn-outline erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark"></i> Discard &amp; Return
                    </a>
                </div>

                <!-- 3. Policy Info Box -->
                <div class="card" style="padding: 1.25rem; border-radius: 12px; background: #FAFBFD; border: 1px solid #E2E8F0;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: #1E293B; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-shield-halved" style="color: #5B841E;"></i> Vikas Udhyog Credit Policy
                    </h4>
                    <ul style="font-size: 0.78rem; color: #64748B; margin: 0; padding-left: 1.15rem; line-height: 1.55;">
                        <li>Status is automatically set to <strong>Active</strong> upon creation.</li>
                        <li>Dispatch invoices check against the defined <strong>Credit Limit</strong>.</li>
                        <li>Entering a valid GSTIN auto-fills the corresponding PAN number.</li>
                        <li>Opening balances immediately reflect on the general customer ledger.</li>
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
        const typeVal = document.getElementById('field-type').value;
        const contactVal = document.getElementById('field-contact').value.trim();
        const phoneVal = document.getElementById('field-phone').value.trim();
        const cityVal = document.getElementById('field-city').value.trim();
        const stateVal = document.getElementById('field-state').value.trim();
        const gstinVal = document.getElementById('field-gstin').value.trim();
        const limitVal = parseFloat(document.getElementById('field-credit-limit').value) || 0;
        const balVal = parseFloat(document.getElementById('field-opening-balance').value) || 0;

        // Avatar Initials
        let initials = 'CS';
        if (nameVal.length > 0) {
            const words = nameVal.split(/\s+/).filter(Boolean);
            if (words.length >= 2) {
                initials = (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
            } else {
                initials = nameVal.substring(0, 2).toUpperCase();
            }
        }
        document.getElementById('preview-avatar').textContent = initials;
        document.getElementById('preview-name').textContent = nameVal || 'Customer Name';
        document.getElementById('preview-code').textContent = codeVal || 'CST-01';
        document.getElementById('preview-type').textContent = typeVal || 'Distributor';

        // Financials
        document.getElementById('preview-credit-limit').textContent = '₹' + limitVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const balanceElem = document.getElementById('preview-balance');
        balanceElem.textContent = '₹' + balVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        balanceElem.style.color = balVal > 0 ? '#B91C1C' : '#059669';

        // Meta List
        document.getElementById('preview-contact').textContent = contactVal || 'Not specified';
        document.getElementById('preview-phone').textContent = phoneVal || 'Not specified';
        
        let loc = [];
        if (cityVal) loc.push(cityVal);
        if (stateVal) loc.push(stateVal);
        document.getElementById('preview-location').textContent = loc.length > 0 ? loc.join(', ') : 'Rajasthan';

        document.getElementById('preview-gstin').textContent = gstinVal || 'Unregistered';
    }

    // Auto-generate PAN from 15-character GSTIN
    function handleGstinInput(gstin) {
        if (gstin && gstin.length >= 12) {
            const panCandidate = gstin.substring(2, 12);
            const panField = document.getElementById('field-pan');
            if (panCandidate.match(/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i)) {
                panField.value = panCandidate.toUpperCase();
            }
        }
    }

    // AJAX Auto-generate Customer Code
    function generateCustomerCode() {
        const btn = event.currentTarget;
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        fetch('{{ route("admin.masters.customer.generate-code") }}?prefix=CST')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.code) {
                    document.getElementById('field-code').value = data.code;
                    updateLivePreview();
                }
            })
            .catch(err => console.error('Error generating code:', err))
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
