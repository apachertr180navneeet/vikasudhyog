@extends('admin.layouts.app')

@section('title', 'Edit Customer: ' . $customer->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-customer')

@section('content')
<section class="view-section active" id="view-customer-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.customer') }}">Customer Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Customer Profile</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-user-pen text-primary"></i> Edit Customer: {{ $customer->name }}
            </h1>
            <p class="erp-page-subtitle">
                Update client account details, credit limit, payment terms, contact details &amp; dispatch destination.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.customer.show', $customer->id) }}" class="btn btn-outline" title="View Customer Profile">
                <i class="fa-regular fa-eye"></i> View Profile
            </a>
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
    <form action="{{ route('admin.masters.customer.update', $customer->id) }}" method="POST" id="customer-edit-form">
        @csrf
        @method('PUT')

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
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter customer name" value="{{ old('name', $customer->name) }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Registered business, distribution agency, or proprietary name</span>
                        </div>

                        <!-- Customer Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Customer Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateCustomerCode()" class="erp-btn-suggest" title="Generate Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter customer code" value="{{ old('code', $customer->code) }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
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
                                        <option value="{{ $typeOption }}" {{ old('customer_type', $customer->customer_type) === $typeOption ? 'selected' : '' }}>{{ $typeOption }}</option>
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
                                        <option value="{{ $company->id }}" {{ old('company_id', $customer->company_id) == $company->id ? 'selected' : '' }}>
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
                                <input type="text" name="contact_person" id="field-contact" class="form-control erp-field-input-iconified" placeholder="Enter contact person" value="{{ old('contact_person', $customer->contact_person) }}" oninput="updateLivePreview()">
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
                                <input type="tel" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="Enter mobile number" value="{{ old('phone', $customer->phone) }}" oninput="updateLivePreview()">
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
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="Enter email address" value="{{ old('email', $customer->email) }}" oninput="updateLivePreview()">
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
                                <input type="text" name="gstin" id="field-gstin" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter GSTIN" value="{{ old('gstin', $customer->gstin) }}" maxlength="15" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); handleGstinInput(this.value); updateLivePreview();">
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
                                <input type="text" name="pan" id="field-pan" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PAN" value="{{ old('pan', $customer->pan) }}" maxlength="10" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
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
                            <textarea name="address" id="field-address" class="form-control" rows="2" placeholder="Enter billing address">{{ old('address', $customer->address) }}</textarea>
                            <span class="erp-field-hint">Full road, shop number, mandi complex or commercial zone</span>
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                City / Mandi Hub
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-city erp-field-icon"></i>
                                <input type="text" name="city" id="field-city" class="form-control erp-field-input-iconified" placeholder="Enter city" value="{{ old('city', $customer->city) }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- State -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                State
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map erp-field-icon"></i>
                                <input type="text" name="state" id="field-state" class="form-control erp-field-input-iconified" placeholder="Enter state" value="{{ old('state', $customer->state ?: 'Rajasthan') }}" oninput="updateLivePreview()">
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Postal Pincode
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-envelopes-bulk erp-field-icon"></i>
                                <input type="text" name="pincode" id="field-pincode" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter pincode" value="{{ old('pincode', $customer->pincode) }}" maxlength="10">
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
                                <p class="erp-form-section-desc">Permitted trade credit limit, payment grace duration &amp; balance tracking</p>
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
                                <input type="number" step="0.01" min="0" name="credit_limit" id="field-credit-limit" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter credit limit" value="{{ old('credit_limit', $customer->credit_limit) }}" required oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Max allowable outstanding for this client</span>
                        </div>

                        <!-- Payment Terms -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Credit Duration / Payment Terms
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-days erp-field-icon"></i>
                                <input type="text" name="payment_terms" id="field-terms" class="form-control erp-field-input-iconified" placeholder="Enter payment terms" value="{{ old('payment_terms', $customer->payment_terms ?: '30 Days') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. 15 Days, 30 Days, 45 Days, Immediate</span>
                        </div>

                        <!-- Opening Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Opening Receivable Balance (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="opening_balance" id="field-opening-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter opening balance" value="{{ old('opening_balance', $customer->opening_balance) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Initial ledger balance</span>
                        </div>

                        <!-- Current Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Current Outstanding Balance (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-hand-holding-dollar erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="current_balance" id="field-current-balance" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter current balance" value="{{ old('current_balance', $customer->current_balance) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Live ledger outstanding due amount</span>
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
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name', $customer->bank_name) }}">
                            </div>
                        </div>

                        <!-- Bank Account No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-check erp-field-icon"></i>
                                <input type="text" name="bank_account_no" id="field-bank-acc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter account number" value="{{ old('bank_account_no', $customer->bank_account_no) }}">
                            </div>
                        </div>

                        <!-- Bank IFSC -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-code-branch erp-field-icon"></i>
                                <input type="text" name="bank_ifsc" id="field-bank-ifsc" class="form-control erp-field-input-iconified font-monospace" placeholder="Enter IFSC code" value="{{ old('bank_ifsc', $customer->bank_ifsc) }}" maxlength="11" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');">
                            </div>
                        </div>

                        <!-- Bank Branch -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Branch Name
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-map-pin erp-field-icon"></i>
                                <input type="text" name="bank_branch" id="field-bank-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('bank_branch', $customer->bank_branch) }}">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Trade Notes / Remarks
                            </label>
                            <textarea name="notes" id="field-notes" class="form-control" rows="3" placeholder="Enter notes">{{ old('notes', $customer->notes) }}</textarea>
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
                        @if($customer->status === 'active')
                            <span class="erp-status-btn erp-status-btn-active" style="padding: 2px 8px; font-size: 0.72rem; cursor: default;">
                                <span class="erp-status-dot-green"></span> Active
                            </span>
                        @else
                            <span class="erp-status-btn erp-status-btn-inactive" style="padding: 2px 8px; font-size: 0.72rem; cursor: default;">
                                <span class="erp-status-dot-red"></span> Inactive
                            </span>
                        @endif
                    </div>

                    <div class="erp-preview-body">
                        <!-- Avatar & Title -->
                        <div class="erp-preview-hero">
                            <div class="erp-preview-avatar" id="preview-avatar">
                                {{ $customer->initials }}
                            </div>
                            <div class="erp-preview-hero-info">
                                <div class="erp-preview-name" id="preview-name">{{ $customer->name }}</div>
                                <div class="erp-preview-meta-row">
                                    <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.75rem; border: 1px solid #E2E8F0;">
                                        {{ $customer->code }}
                                    </span>
                                    <span class="badge" id="preview-type" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem;">
                                        {{ $customer->customer_type }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <hr class="erp-preview-divider">

                        <!-- Financial Snapshot -->
                        <div class="erp-preview-stats-grid">
                            <div class="erp-preview-stat-item">
                                <span class="erp-preview-stat-label">Credit Limit</span>
                                <span class="erp-preview-stat-val font-monospace" id="preview-credit-limit">₹{{ number_format($customer->credit_limit, 2) }}</span>
                            </div>
                            <div class="erp-preview-stat-item">
                                <span class="erp-preview-stat-label">Outstanding</span>
                                <span class="erp-preview-stat-val font-monospace" id="preview-balance" style="color: {{ $customer->current_balance > 0 ? '#B91C1C' : '#059669' }};">
                                    ₹{{ number_format($customer->current_balance, 2) }}
                                </span>
                            </div>
                        </div>

                        <hr class="erp-preview-divider">

                        <!-- Meta List -->
                        <div class="erp-preview-meta-list">
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-user erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">Contact Person</div>
                                    <div class="erp-preview-meta-val" id="preview-contact">{{ $customer->contact_person ?: 'Not specified' }}</div>
                                </div>
                            </div>

                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-phone erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">Mobile Number</div>
                                    <div class="erp-preview-meta-val font-monospace" id="preview-phone">{{ $customer->phone ?: 'Not specified' }}</div>
                                </div>
                            </div>

                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-location-dot erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">City / State</div>
                                    <div class="erp-preview-meta-val" id="preview-location">
                                        {{ implode(', ', array_filter([$customer->city, $customer->state])) ?: 'Rajasthan' }}
                                    </div>
                                </div>
                            </div>

                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-stamp erp-preview-meta-icon"></i>
                                <div>
                                    <div class="erp-preview-meta-label">GSTIN</div>
                                    <div class="erp-preview-meta-val font-monospace" id="preview-gstin">{{ $customer->gstin ?: 'Unregistered' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Sidebar Action Buttons -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-action-submit" id="btn-update-customer">
                        <i class="fa-solid fa-check"></i> Update Customer Profile
                    </button>
                    <a href="{{ route('admin.masters.customer') }}" class="btn btn-outline erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel &amp; Return
                    </a>
                </div>

                <!-- 3. Audit Trail Timestamps Card -->
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: #1E293B; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #64748B;"></i> Record Audit Trail
                    </h4>
                    <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.8rem;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #F1F5F9; padding-bottom: 0.4rem;">
                            <span style="color: #64748B;">Customer ID:</span>
                            <span class="font-monospace" style="font-weight: 600; color: #334155;">#{{ $customer->id }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #F1F5F9; padding-bottom: 0.4rem;">
                            <span style="color: #64748B;">Registered On:</span>
                            <span style="color: #334155;">{{ $customer->created_at ? $customer->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748B;">Last Modified:</span>
                            <span style="color: #334155;">{{ $customer->updated_at ? $customer->updated_at->format('d M Y, h:i A') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Danger Zone Card -->
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #FEE2E2; background: #FFF5F5;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: #B91C1C; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </h4>
                    <p style="font-size: 0.78rem; color: #7F1D1D; margin-bottom: 0.85rem; line-height: 1.45;">
                        Archiving this customer soft-deletes the record from active billing selectors and dispatch entry forms.
                    </p>
                    <button type="button" class="btn btn-outline" onclick="confirmDeleteCustomer()" style="width: 100%; border-color: #EF4444; color: #DC2626; font-size: 0.82rem; font-weight: 600; justify-content: center; background: #FFFFFF;">
                        <i class="fa-regular fa-trash-can"></i> Archive Customer
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Delete Customer Hidden Form -->
<form id="delete-customer-form" action="{{ route('admin.masters.customer.destroy', $customer->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

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
        const balVal = parseFloat(document.getElementById('field-current-balance').value) || 0;

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

        fetch('{{ route("admin.masters.customer.generate-code") }}?prefix=CST&exclude_id={{ $customer->id }}')
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

    // SweetAlert2 Confirmation for Archive
    function confirmDeleteCustomer() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Customer Account?',
                html: `Are you sure you want to archive <strong>{{ addslashes($customer->name) }} ({{ $customer->code }})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the record from active customer registries.</span>`,
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
                    document.getElementById('delete-customer-form').submit();
                }
            });
        } else {
            if (confirm('Are you sure you want to archive {{ addslashes($customer->name) }} ({{ $customer->code }})?')) {
                document.getElementById('delete-customer-form').submit();
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
