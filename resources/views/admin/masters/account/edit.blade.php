@extends('admin.layouts.app')

@section('title', 'Edit Account: ' . $account->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-account')

@section('content')
<section class="view-section active" id="view-account-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.account') }}">Account Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Account: {{ $account->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Account Ledger
            </h1>
            <p class="erp-page-subtitle">
                Update chart of accounts classification, banking coordinates, current balance &amp; audit remarks.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.account.show', $account->id) }}" class="btn btn-outline" title="View Account Dossier">
                <i class="fa-regular fa-eye"></i> View Dossier
            </a>
            <a href="{{ route('admin.masters.account') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Account List
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
    <form action="{{ route('admin.masters.account.update', $account->id) }}" method="POST" id="account-edit-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Account Classification & Identity -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-book-bookmark"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Account Classification &amp; Identity</h3>
                                <p class="erp-form-section-desc">Ledger title, unique code identifier &amp; chart of accounts group</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Account Name -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Account Ledger Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-signature erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter account name" value="{{ old('name', $account->name) }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. State Bank of India - Current A/c, Cash in Hand - Factory Till</span>
                        </div>

                        <!-- Account Group -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Chart of Accounts Group <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-layer-group erp-field-icon"></i>
                                <select name="account_group" id="field-group" class="form-control erp-field-input-iconified" required onchange="handleGroupChange()">
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp }}" {{ old('account_group', $account->account_group) === $grp ? 'selected' : '' }}>{{ $grp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Classification determines balance sheet &amp; P&amp;L placement</span>
                        </div>

                        <!-- Account Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Account Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateAccountCode()" class="erp-btn-suggest" title="Generate Code">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter account code" value="{{ old('code', $account->code) }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique ledger identifier</span>
                        </div>

                        <!-- Manufacturing Plant / Unit Assignment -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Production Plant / Company Facility
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-circle-check erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="" {{ empty($account->company_id) ? 'selected' : '' }}>All Units / General Firm Ledger</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $account->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} ({{ $company->code }}) — {{ $company->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Assigned factory facility or global</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Financial Balance & Nature -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Financial Balance &amp; Nature</h3>
                                <p class="erp-form-section-desc">Current on-hand balance &amp; standard accounting Dr / Cr nature</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Current Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Current Ledger Balance (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-wallet erp-field-icon" style="color: #0F172A;"></i>
                                <input type="number" step="0.01" name="current_balance" id="field-current-balance" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter current balance" value="{{ old('current_balance', $account->current_balance) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Real-time ledger balance as of latest voucher entry</span>
                        </div>

                        <!-- Opening Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Original Opening Balance (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-clock-rotate-left erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="opening_balance" id="field-balance" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter opening balance" value="{{ old('opening_balance', $account->opening_balance) }}">
                            </div>
                            <span class="erp-field-hint">Initial balance carried forward at creation</span>
                        </div>

                        <!-- Balance Nature (Debit vs Credit) -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Balance Nature (Dr / Cr) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-scale-balanced erp-field-icon"></i>
                                <select name="balance_type" id="field-balance-type" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    <option value="debit" {{ old('balance_type', $account->balance_type) === 'debit' ? 'selected' : '' }}>
                                        Debit (Dr) — Assets, Bank, Cash, Expenses
                                    </option>
                                    <option value="credit" {{ old('balance_type', $account->balance_type) === 'credit' ? 'selected' : '' }}>
                                        Credit (Cr) — Liabilities, Incomes, Capital, Duties
                                    </option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Standard accounting ledger balance side</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Banking & Institutional Credentials -->
                <div class="card erp-form-section-card" id="banking-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-purple">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Banking &amp; Institutional Credentials</h3>
                                <p class="erp-form-section-desc">Required for Bank Accounts to process payments, RTGS &amp; reconciliations</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Bank Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Commercial Bank Name
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-landmark erp-field-icon"></i>
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name', $account->bank_name) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. State Bank of India, HDFC Bank</span>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-credit-card erp-field-icon"></i>
                                <input type="text" name="account_number" id="field-account-number" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter account number" value="{{ old('account_number', $account->account_number) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Account number for online banking &amp; cheques</span>
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-stamp erp-field-icon"></i>
                                <input type="text" name="ifsc_code" id="field-ifsc" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter IFSC code" value="{{ old('ifsc_code', $account->ifsc_code) }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">11-character Indian Financial System Code</span>
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Branch Location
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-location-dot erp-field-icon"></i>
                                <input type="text" name="branch_name" id="field-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('branch_name', $account->branch_name) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. Sojat Mandi Branch</span>
                        </div>

                        <!-- UPI ID -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                UPI VPA / QR Handle
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-qrcode erp-field-icon"></i>
                                <input type="text" name="upi_id" id="field-upi" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter UPI ID" value="{{ old('upi_id', $account->upi_id) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. vikasudhyog@sbi</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Description & Ledger Notes -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-gray">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">4. Operational Remarks &amp; Audit Notes</h3>
                                <p class="erp-form-section-desc">Ledger instructions, reconciliation guidelines &amp; purpose</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Account Description / Audit Remarks
                            </label>
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter ledger description or audit remarks">{{ old('notes', $account->notes) }}</textarea>
                            <span class="erp-field-hint">e.g. Factory premise operational account used for farmer mandi cash purchases and freight.</span>
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
                            <i class="fa-solid fa-eye"></i> Live Account Preview
                        </div>
                        @if($account->status === 'active')
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
                            {{ $account->initials }}
                        </div>
                        <h4 class="erp-preview-title" id="preview-name" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; text-align: center; word-break: break-word;">
                            {{ $account->name }}
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.3rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $account->code }}
                            </span>
                            <span class="badge" id="preview-group" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                {{ $account->account_group }}
                            </span>
                        </div>
                    </div>

                    <!-- Balance Ribbon -->
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.95rem; margin: 1.15rem 0; text-align: center;">
                        <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                            Current Ledger Balance
                        </div>
                        <div class="font-monospace" id="preview-balance" style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin-top: 0.2rem;">
                            ₹{{ number_format($account->current_balance, 2) }} <span id="preview-balance-side" style="font-size: 0.82rem; color: {{ $account->balance_type === 'debit' ? '#1D4ED8' : '#B45309' }}; font-weight: 700;">{{ $account->balance_type === 'debit' ? 'Dr' : 'Cr' }}</span>
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Bank Institution:</span>
                            <span class="erp-preview-meta-val" id="preview-bank">{{ $account->bank_name ?: 'None' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Account No:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-account-no">{{ $account->account_number ?: '—' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">IFSC Code:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-ifsc">{{ $account->ifsc_code ?: '—' }}</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Plant Unit:</span>
                            <span class="erp-preview-meta-val" id="preview-company">{{ $account->company ? $account->company->name : 'All Units' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Action Card -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-sidebar-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Update Account Ledger
                    </button>
                    <a href="{{ route('admin.masters.account') }}" class="btn btn-outline erp-btn-sidebar-cancel">
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
                            <strong>Account ID:</strong> <span class="font-monospace text-dark">#{{ $account->id }}</span>
                        </div>
                        <div>
                            <strong>Created:</strong> {{ $account->created_at ? $account->created_at->format('d M Y, h:i A') : 'System Initial' }}
                        </div>
                        <div>
                            <strong>Last Modified:</strong> {{ $account->updated_at ? $account->updated_at->format('d M Y, h:i A') : 'Never' }}
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
                        Archiving soft-deletes this account. It will be hidden from new voucher entries and ledger selectors.
                    </p>
                    <button type="button" class="btn btn-outline" style="border-color: #EF4444; color: #DC2626; width: 100%; font-size: 0.82rem; font-weight: 600;" onclick="confirmArchiveAccount()">
                        <i class="fa-regular fa-trash-can me-1"></i> Archive Account
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Delete Hidden Form -->
<form id="archive-account-form" action="{{ route('admin.masters.account.destroy', $account->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function handleGroupChange() {
        const groupVal = document.getElementById('field-group').value;
        const balanceTypeElem = document.getElementById('field-balance-type');

        if (groupVal === 'Direct Incomes' || groupVal === 'Indirect Incomes' || groupVal === 'Duties & Taxes' || groupVal === 'Current Liabilities' || groupVal === 'Capital & Reserves') {
            balanceTypeElem.value = 'credit';
        } else {
            balanceTypeElem.value = 'debit';
        }

        updateLivePreview();
    }

    function generateAccountCode() {
        const groupVal = document.getElementById('field-group').value;
        const btn = document.querySelector('.erp-btn-suggest');
        if (btn) btn.disabled = true;

        fetch(`{{ route("admin.masters.account.generate-code") }}?group=${encodeURIComponent(groupVal)}&exclude_id={{ $account->id }}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.code) {
                    document.getElementById('field-code').value = data.code;
                    updateLivePreview();
                }
            })
            .catch(err => console.error('Error generating account code:', err))
            .finally(() => {
                if (btn) btn.disabled = false;
            });
    }

    function updateLivePreview() {
        const nameVal = document.getElementById('field-name').value.trim();
        const codeVal = document.getElementById('field-code').value.trim();
        const groupVal = document.getElementById('field-group').value;
        const balanceVal = parseFloat(document.getElementById('field-current-balance').value) || 0;
        const balanceTypeVal = document.getElementById('field-balance-type').value === 'debit' ? 'Dr' : 'Cr';
        const bankVal = document.getElementById('field-bank-name').value.trim();
        const accountNoVal = document.getElementById('field-account-number').value.trim();
        const ifscVal = document.getElementById('field-ifsc').value.trim();
        const companyElem = document.getElementById('field-company');
        const companyText = companyElem.selectedIndex > 0 ? companyElem.options[companyElem.selectedIndex].text.split('(')[0].trim() : 'All Units';

        // Avatar Initials
        let initials = '{{ $account->initials }}';
        if (nameVal.length > 0) {
            const words = nameVal.split(/\s+/).filter(Boolean);
            if (words.length >= 2) {
                initials = (words[0].substring(0, 1) + words[1].substring(0, 1)).toUpperCase();
            } else {
                initials = nameVal.substring(0, 2).toUpperCase();
            }
        }

        document.getElementById('preview-avatar').textContent = initials;
        document.getElementById('preview-name').textContent = nameVal || 'Account Ledger Name';
        document.getElementById('preview-code').textContent = codeVal || 'ACC-01';
        document.getElementById('preview-group').textContent = groupVal;
        document.getElementById('preview-balance').innerHTML = '₹' + balanceVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ` <span id="preview-balance-side" style="font-size: 0.82rem; color: ${balanceTypeVal === 'Dr' ? '#1D4ED8' : '#B45309'}; font-weight: 700;">${balanceTypeVal}</span>`;
        document.getElementById('preview-bank').textContent = bankVal || 'None';
        document.getElementById('preview-account-no').textContent = accountNoVal || '—';
        document.getElementById('preview-ifsc').textContent = ifscVal || '—';
        document.getElementById('preview-company').textContent = companyText;
    }

    function confirmArchiveAccount() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Account Ledger?',
                html: `Are you sure you want to archive <strong>{{ addslashes($account->name) }} ({{ $account->code }})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the ledger from active chart of accounts.</span>`,
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
                    document.getElementById('archive-account-form').submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive {{ addslashes($account->name) }} ({{ $account->code }})?`)) {
                document.getElementById('archive-account-form').submit();
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
