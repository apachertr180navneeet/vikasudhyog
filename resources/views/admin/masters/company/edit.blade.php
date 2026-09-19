@extends('admin.layouts.app')

@section('title', 'Edit ' . $company->name . ' - Company Master')
@section('page_code', 'master-company-edit')

@section('content')
<section class="view-section active" id="view-master-company-edit">
    <!-- Top Header Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <a href="{{ route('admin.masters.company') }}" style="color: var(--text-muted); text-decoration: none;">Company Master</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span style="color: var(--primary); font-weight: 600;">Edit Profile</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.65rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.65rem;">
                <span style="width: 40px; height: 40px; border-radius: 12px; background: rgba(107, 142, 35, 0.12); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
                Edit Company: {{ $company->name }}
            </h1>
            <p class="page-subtitle" style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Update corporate details, tax numbers, factory premises, and bank settlement configurations.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.masters.company.show', $company->id) }}" class="btn btn-outline" style="border-radius: 10px; height: 42px; padding: 0 1.15rem; font-weight: 600;">
                <i class="fa-solid fa-eye"></i> View Profile
            </a>
            <a href="{{ route('admin.masters.company') }}" class="btn btn-outline" style="border-radius: 10px; height: 42px; padding: 0 1.15rem; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 5px solid #EF4444; padding: 1.1rem 1.35rem; border-radius: 14px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.75rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);">
            <div style="font-weight: 700; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem;"></i> Validation Errors Found
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.masters.company.update', $company->id) }}" method="POST" id="company-edit-form">
        @csrf
        @method('PUT')

        <div class="form-grid-layout">
            <!-- Left Main Form Column -->
            <div class="form-main-col">
                <!-- 1. General Business Profile -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <div class="erp-card-header-left">
                            <div class="erp-card-icon">
                                <i class="fa-solid fa-building"></i>
                            </div>
                            <div>
                                <h3 class="erp-card-title">1. Business Identity</h3>
                                <p class="erp-card-subtitle">Official company legal name and operating classification</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-card-body">
                        <div class="form-row-2">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label class="form-label">
                                    <span>Company Legal Name <span class="req">*</span></span>
                                    <span class="label-hint">As printed on GST &amp; Tax invoices</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-building input-icon"></i>
                                    <input type="text" name="name" id="field-name" class="form-control" placeholder="e.g. Vikas Udhyog Herbal Formulations" value="{{ old('name', $company->name) }}" required autofocus>
                                </div>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                                    <span>Short Code / Prefix</span>
                                    <span class="label-hint" id="code-status-badge" style="font-size: 0.72rem; color: var(--primary); font-weight: 600;">Unique Code</span>
                                </label>
                                <div class="input-icon-wrap" style="position: relative;">
                                    <i class="fa-solid fa-hashtag input-icon"></i>
                                    <input type="text" name="code" id="field-code" class="form-control uppercase-input" placeholder="e.g. VU" value="{{ old('code', $company->code) }}" maxlength="30" style="padding-right: 2.5rem; text-transform: uppercase;" autocomplete="off">
                                    <button type="button" id="btn-regenerate-code" title="Generate unique short code from name" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 5px 8px; font-size: 0.85rem; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.color='var(--primary)'; this.style.background='rgba(107, 142, 35, 0.1)'" onmouseout="this.style.color='var(--text-muted)'; this.style.background='none'">
                                        <i class="fa-solid fa-arrows-rotate"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <span>Financial Year</span>
                                    <span class="label-hint">Current accounting period</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-calendar-days input-icon"></i>
                                    <input type="text" name="financial_year" class="form-control" placeholder="2026-2027" value="{{ old('financial_year', $company->financial_year ?? '2026-2027') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group no-margin">
                            <label class="form-label">
                                <span>Tagline / Operational Scope</span>
                                <span class="label-hint">Appears beneath company name on reports</span>
                            </label>
                            <div class="input-icon-wrap">
                                <i class="fa-solid fa-quote-left input-icon"></i>
                                <input type="text" name="tagline" id="field-tagline" class="form-control" placeholder="e.g. Leading Manufacturer of Pure Sojat Henna Powder &amp; Natural Herbal Extracts" value="{{ old('tagline', $company->tagline) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Tax & Compliance Registrations -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <div class="erp-card-header-left">
                            <div class="erp-card-icon" style="background: rgba(212, 160, 23, 0.12); color: #B45309;">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h3 class="erp-card-title">2. Tax Registrations &amp; Compliance</h3>
                                <p class="erp-card-subtitle">GSTIN, Income Tax PAN and regulatory numbers</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-card-body">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">
                                    <span>GSTIN Number</span>
                                    <span class="label-hint">15-Digit GST Code</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-receipt input-icon"></i>
                                    <input type="text" name="gstin" id="field-gstin" class="form-control uppercase-input" placeholder="08AABCV1234F1Z5" maxlength="20" value="{{ old('gstin', $company->gstin) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <span>Permanent Account Number (PAN)</span>
                                    <span class="label-hint">10-Digit Alpha-numeric</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-id-card input-icon"></i>
                                    <input type="text" name="pan" id="field-pan" class="form-control uppercase-input" placeholder="AABCV1234F" maxlength="15" value="{{ old('pan', $company->pan) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Address & Plant Location -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <div class="erp-card-header-left">
                            <div class="erp-card-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="erp-card-title">3. Factory Address &amp; Contact Details</h3>
                                <p class="erp-card-subtitle">Physical plant location, official email and contact telephone</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-card-body">
                        <div class="form-row-3">
                            <div class="form-group">
                                <label class="form-label">
                                    <span>Official Phone / Mobile</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-phone input-icon"></i>
                                    <input type="text" name="phone" class="form-control" placeholder="+91 94140 12345" value="{{ old('phone', $company->phone) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <span>Official Email Address</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="form-control" placeholder="contact@vikasudhyog.com" value="{{ old('email', $company->email) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <span>Website Domain</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-globe input-icon"></i>
                                    <input type="text" name="website" class="form-control" placeholder="https://vikasudhyog.com" value="{{ old('website', $company->website) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <span>Plant / Registered Premises Address</span>
                            </label>
                            <textarea name="address" rows="2" class="form-control" placeholder="Plot No. 12-15, Mandi Yard Road, Industrial Area">{{ old('address', $company->address) }}</textarea>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group no-margin">
                                <label class="form-label">
                                    <span>City / Town <span class="req">*</span></span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-city input-icon"></i>
                                    <input type="text" name="city" id="field-city" class="form-control" placeholder="Sojat City" value="{{ old('city', $company->city) }}" required>
                                </div>
                            </div>

                            <div class="form-group no-margin">
                                <label class="form-label">
                                    <span>State <span class="req">*</span></span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-map-pin input-icon"></i>
                                    <input type="text" name="state" class="form-control" placeholder="Rajasthan" value="{{ old('state', $company->state) }}" required>
                                </div>
                            </div>

                            <div class="form-group no-margin">
                                <label class="form-label">
                                    <span>Postal Pincode</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-envelope-open-text input-icon"></i>
                                    <input type="text" name="pincode" class="form-control" placeholder="306104" maxlength="10" value="{{ old('pincode', $company->pincode) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Banking & Direct Settlement -->
                <div class="erp-card">
                    <div class="erp-card-header">
                        <div class="erp-card-header-left">
                            <div class="erp-card-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <h3 class="erp-card-title">4. Banking &amp; Financial Settlement</h3>
                                <p class="erp-card-subtitle">Bank account for RTGS / NEFT payment receipts on invoices</p>
                            </div>
                        </div>
                    </div>
                    <div class="erp-card-body">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">
                                    <span>Primary Bank Name</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-landmark input-icon"></i>
                                    <input type="text" name="bank_name" class="form-control" placeholder="State Bank of India / HDFC Bank" value="{{ old('bank_name', $company->bank_name) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <span>Bank Account Number</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-money-check input-icon"></i>
                                    <input type="text" name="bank_account_no" class="form-control" placeholder="38491029384" value="{{ old('bank_account_no', $company->bank_account_no) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group no-margin">
                                <label class="form-label">
                                    <span>IFSC Code</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-shield input-icon"></i>
                                    <input type="text" name="bank_ifsc" class="form-control uppercase-input" placeholder="SBIN0031204" maxlength="25" value="{{ old('bank_ifsc', $company->bank_ifsc) }}">
                                </div>
                            </div>

                            <div class="form-group no-margin">
                                <label class="form-label">
                                    <span>Branch Location</span>
                                </label>
                                <div class="input-icon-wrap">
                                    <i class="fa-solid fa-location-dot input-icon"></i>
                                    <input type="text" name="bank_branch" class="form-control" placeholder="Main Branch, Sojat City" value="{{ old('bank_branch', $company->bank_branch) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Default Company Switch -->
                <div class="erp-card" style="margin-bottom: 0;">
                    <div class="erp-card-body" style="padding: 1.25rem 1.75rem;">
                        <label class="switch-card" for="is_default">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(212, 160, 23, 0.15); color: #B45309; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                                    <i class="fa-solid fa-crown"></i>
                                </div>
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.15rem;">
                                        Set as Primary Default Entity
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">
                                        This company will be pre-selected in invoices, bills, voucher entries, and stock reports across the ERP.
                                    </div>
                                </div>
                            </div>
                            <div class="switch-control">
                                <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $company->is_default) ? 'checked' : '' }}>
                                <span class="switch-slider"></span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Sticky Bottom Action Bar -->
                <div class="form-actions-bar">
                    <a href="{{ route('admin.masters.company') }}" class="btn btn-outline" style="border-radius: 10px; font-weight: 600; padding: 0.65rem 1.35rem;">
                        <i class="fa-solid fa-xmark"></i> Discard
                    </a>

                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <button type="submit" class="btn btn-primary" style="border-radius: 10px; font-weight: 700; padding: 0.65rem 1.75rem; box-shadow: 0 4px 14px rgba(107, 142, 35, 0.35);">
                            <i class="fa-solid fa-floppy-disk"></i> Update Company Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column (Live Card Preview & Help) -->
            <div class="form-sidebar-col">
                <!-- Live Preview Card -->
                <div class="preview-company-card" style="margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <span class="preview-badge-pill status" id="preview-status-pill">
                            <i class="fa-solid fa-circle" style="font-size: 0.5rem;"></i> {{ ucfirst($company->status) }}
                        </span>
                        <span class="preview-badge-pill default" id="preview-default-pill" style="{{ $company->is_default ? '' : 'display: none;' }}">
                            <i class="fa-solid fa-crown"></i> Primary
                        </span>
                    </div>

                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.75px; opacity: 0.7; margin-bottom: 0.25rem;">
                        Company Profile Preview
                    </div>
                    <h2 id="preview-name" style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF; margin: 0 0 0.4rem; line-height: 1.3; word-break: break-word;">
                        {{ $company->name }}
                    </h2>
                    <div id="preview-tagline" style="font-size: 0.8rem; opacity: 0.85; margin-bottom: 1.25rem; font-style: italic; line-height: 1.35;">
                        {{ $company->tagline ?: 'Herbal manufacturing & trading division' }}
                    </div>

                    <div style="border-top: 1px solid rgba(255, 255, 255, 0.15); padding-top: 0.85rem; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8rem;">
                        <div style="display: flex; justify-content: space-between; opacity: 0.9;">
                            <span>Code:</span>
                            <strong id="preview-code">{{ $company->code ?: 'VU-NEW' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; opacity: 0.9;">
                            <span>GSTIN:</span>
                            <strong id="preview-gstin">{{ $company->gstin ?: 'Not Set' }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; opacity: 0.9;">
                            <span>Location:</span>
                            <strong id="preview-city">{{ $company->city }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Guidelines Card -->
                <div class="erp-card">
                    <div class="erp-card-header" style="padding: 1rem 1.25rem;">
                        <div class="erp-card-header-left">
                            <div class="erp-card-icon" style="width: 32px; height: 32px; font-size: 0.9rem;">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h4 style="font-size: 0.92rem; font-weight: 700; margin: 0; color: var(--dark);">Setup Guidance</h4>
                        </div>
                    </div>
                    <div class="erp-card-body" style="padding: 1.25rem;">
                        <div class="guideline-item">
                            <i class="fa-solid fa-check-circle"></i>
                            <div><strong>GSTIN Compliance:</strong> Valid 15-digit GST number is mandatory for GST e-Invoicing &amp; E-Way Bill generation.</div>
                        </div>
                        <div class="guideline-item">
                            <i class="fa-solid fa-check-circle"></i>
                            <div><strong>Prefix Code:</strong> Used to generate sequence invoice numbers (e.g. <code>VU/2026/001</code>).</div>
                        </div>
                        <div class="guideline-item">
                            <i class="fa-solid fa-check-circle"></i>
                            <div><strong>Multiple Entities:</strong> You can register separate plants for Mehandi Powder, Herbal Extracts, and Trading.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
    // Live Dynamic Preview Helper
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.getElementById('field-name');
        const codeInput = document.getElementById('field-code');
        const taglineInput = document.getElementById('field-tagline');
        const gstinInput = document.getElementById('field-gstin');
        const cityInput = document.getElementById('field-city');
        const defaultCheck = document.getElementById('is_default');

        const prevName = document.getElementById('preview-name');
        const prevCode = document.getElementById('preview-code');
        const prevTagline = document.getElementById('preview-tagline');
        const prevGstin = document.getElementById('preview-gstin');
        const prevCity = document.getElementById('preview-city');
        const prevStatusPill = document.getElementById('preview-status-pill');
        const prevDefaultPill = document.getElementById('preview-default-pill');

        function updatePreview() {
            if (prevName) prevName.textContent = nameInput && nameInput.value.trim() ? nameInput.value.trim() : 'New Company Name';
            if (prevCode) prevCode.textContent = codeInput && codeInput.value.trim() ? codeInput.value.trim().toUpperCase() : 'VU-NEW';
            if (prevTagline) prevTagline.textContent = taglineInput && taglineInput.value.trim() ? taglineInput.value.trim() : 'Herbal manufacturing & trading division';
            if (prevGstin) prevGstin.textContent = gstinInput && gstinInput.value.trim() ? gstinInput.value.trim().toUpperCase() : 'Not Set';
            if (prevCity) prevCity.textContent = cityInput && cityInput.value.trim() ? cityInput.value.trim() : 'Sojat City';

            if (defaultCheck && prevDefaultPill) {
                prevDefaultPill.style.display = defaultCheck.checked ? 'inline-flex' : 'none';
            }
        }

        [nameInput, codeInput, taglineInput, gstinInput, cityInput].forEach(el => {
            if (el) el.addEventListener('input', updatePreview);
        });

        if (defaultCheck) defaultCheck.addEventListener('change', updatePreview);

        // Auto-generate Unique Short Code
        let isManualCode = true; // When editing, existing code is preserved unless changed or clicked regenerate
        let codeTimer = null;
        const codeBadge = document.getElementById('code-status-badge');
        const regenBtn = document.getElementById('btn-regenerate-code');
        const excludeId = '{{ $company->id }}';

        function fetchGeneratedCode(name) {
            if (!name) return;

            if (codeBadge) codeBadge.textContent = 'Generating...';

            fetch(`{{ route('admin.masters.company.generate-code') }}?name=${encodeURIComponent(name)}&exclude_id=${excludeId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.code) {
                        codeInput.value = data.code;
                        updatePreview();
                    }
                    if (codeBadge) codeBadge.textContent = 'Unique Code';
                })
                .catch(() => {
                    if (codeBadge) codeBadge.textContent = 'Unique Code';
                });
        }

        if (codeInput) {
            codeInput.addEventListener('input', () => {
                codeInput.value = codeInput.value.toUpperCase().replace(/[^A-Z0-9\-_]/g, '');
                isManualCode = true;
                if (codeBadge) {
                    codeBadge.textContent = 'Custom Code';
                }
            });
        }

        if (regenBtn) {
            regenBtn.addEventListener('click', () => {
                isManualCode = false;
                const icon = regenBtn.querySelector('i');
                if (icon) icon.classList.add('fa-spin');
                fetchGeneratedCode(nameInput ? nameInput.value.trim() : '');
                setTimeout(() => {
                    if (icon) icon.classList.remove('fa-spin');
                }, 500);
            });
        }

        // AJAX Form Submission with Toastr
        const editForm = document.getElementById('company-edit-form');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalHtml = submitBtn ? submitBtn.innerHTML : '';

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating Profile...';
                }

                const formData = new FormData(form);
                formData.append('_method', 'PUT');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (response.ok && data.success) {
                        toastr.success(data.message || 'Company profile updated successfully!');
                        setTimeout(() => {
                            window.location.href = "{{ route('admin.masters.company') }}";
                        }, 800);
                    } else if (response.status === 422 && data.errors) {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        }
                        for (const key in data.errors) {
                            if (data.errors.hasOwnProperty(key)) {
                                data.errors[key].forEach(err => toastr.error(err));
                            }
                        }
                    } else {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        }
                        toastr.error(data.message || 'Failed to update company profile.');
                    }
                })
                .catch(err => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalHtml;
                    }
                    toastr.error('An unexpected network error occurred.');
                });
            });
        }

        updatePreview();
    });
</script>
@endpush
@endsection
