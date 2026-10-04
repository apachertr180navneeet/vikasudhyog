@extends('admin.layouts.app')

@section('title', 'Add New Account - VIKAS UDHYOG ERP')
@section('page_code', 'master-account')

@section('content')
<section class="view-section active" id="view-account-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.account') }}">Account Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Add New Account</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-book-journal-whills text-primary"></i> Add Account Ledger
            </h1>
            <p class="erp-page-subtitle">
                Register bank accounts, cash tills, direct/indirect expense heads &amp; revenue ledgers in chart of accounts.
            </p>
        </div>

        <div class="erp-header-actions">
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
    <form action="{{ route('admin.masters.account.store') }}" method="POST" id="account-create-form">
        @csrf

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
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="Enter account name" value="{{ old('name') }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. State Bank of India - Current A/c, Cash in Hand - Factory Till, Henna Sales A/c, Factory Power Expense</span>
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
                                        <option value="{{ $grp }}" {{ old('account_group') === $grp ? 'selected' : '' }}>{{ $grp }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Classification determines balance sheet &amp; P&amp;L placement</span>
                        </div>

                        <!-- Account Code -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Account Code <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateAccountCode()" class="erp-btn-suggest" title="Generate Code for Selected Group">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="code" id="field-code" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter account code" value="{{ old('code', $suggestedCode ?? 'ACC-01') }}" required oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique ledger identifier (e.g. BNK-01, CSH-01, EXP-01)</span>
                        </div>

                        <!-- Manufacturing Plant / Unit Assignment -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Production Plant / Company Facility
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-circle-check erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">All Units / General Firm Ledger</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }} ({{ $company->code }}) — {{ $company->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Assign this ledger account to a specific factory unit or maintain globally</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Opening Balance & Ledger Type -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-indian-rupee-sign"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Financial Opening Balance &amp; Nature</h3>
                                <p class="erp-form-section-desc">Initial starting balance &amp; standard accounting Dr / Cr nature</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Opening Balance -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Initial Opening Balance (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-wallet erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="opening_balance" id="field-balance" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter opening balance" value="{{ old('opening_balance', '0.00') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Starting balance carried forward from previous financial books</span>
                        </div>

                        <!-- Balance Nature (Debit vs Credit) -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Balance Nature (Dr / Cr) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-scale-balanced erp-field-icon"></i>
                                <select name="balance_type" id="field-balance-type" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    <option value="debit" {{ old('balance_type', 'debit') === 'debit' ? 'selected' : '' }}>
                                        Debit (Dr) — Assets, Bank, Cash, Expenses
                                    </option>
                                    <option value="credit" {{ old('balance_type') === 'credit' ? 'selected' : '' }}>
                                        Credit (Cr) — Liabilities, Incomes, Capital, Duties
                                    </option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Standard accounting ledger balance side</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Banking & Institutional Credentials (Dynamic) -->
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
                                <input type="text" name="bank_name" id="field-bank-name" class="form-control erp-field-input-iconified" placeholder="Enter bank name" value="{{ old('bank_name') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. State Bank of India, HDFC Bank, ICICI Bank</span>
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank Account Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-credit-card erp-field-icon"></i>
                                <input type="text" name="account_number" id="field-account-number" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter account number" value="{{ old('account_number') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Bank account number for cheques &amp; online transfers</span>
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Bank IFSC Code
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-stamp erp-field-icon"></i>
                                <input type="text" name="ifsc_code" id="field-ifsc" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter IFSC code" value="{{ old('ifsc_code') }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, ''); updateLivePreview();">
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
                                <input type="text" name="branch_name" id="field-branch" class="form-control erp-field-input-iconified" placeholder="Enter branch name" value="{{ old('branch_name') }}" oninput="updateLivePreview()">
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
                                <input type="text" name="upi_id" id="field-upi" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter UPI ID" value="{{ old('upi_id') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. vikasudhyog@sbi (for QR collections &amp; payments)</span>
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
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter ledger description or audit remarks">{{ old('notes') }}</textarea>
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
                        <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem; padding: 2px 8px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                            <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 3px;"></i> Active
                        </span>
                    </div>

                    <div class="erp-preview-avatar-wrap">
                        <div class="avatar" id="preview-avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.35);">
                            AC
                        </div>
                        <h4 class="erp-preview-title" id="preview-name" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; text-align: center; word-break: break-word;">
                            Account Ledger Name
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.3rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-code" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $suggestedCode ?? 'ACC-01' }}
                            </span>
                            <span class="badge" id="preview-group" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(91, 132, 30, 0.2);">
                                Bank Accounts
                            </span>
                        </div>
                    </div>

                    <!-- Balance Ribbon -->
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 0.95rem; margin: 1.15rem 0; text-align: center;">
                        <div style="font-size: 0.70rem; color: #64748B; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                            Opening Ledger Balance
                        </div>
                        <div class="font-monospace" id="preview-balance" style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin-top: 0.2rem;">
                            ₹0.00 <span id="preview-balance-side" style="font-size: 0.82rem; color: #1D4ED8; font-weight: 700;">Dr</span>
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Bank Institution:</span>
                            <span class="erp-preview-meta-val" id="preview-bank">None</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">Account No:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-account-no">—</span>
                        </div>
                        <div class="erp-preview-meta-row">
                            <span class="erp-preview-meta-label">IFSC Code:</span>
                            <span class="erp-preview-meta-val font-monospace" id="preview-ifsc">—</span>
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
                        <i class="fa-solid fa-floppy-disk"></i> Save Account Ledger
                    </button>
                    <button type="reset" class="btn btn-outline erp-btn-sidebar-reset" onclick="setTimeout(updateLivePreview, 50)">
                        <i class="fa-solid fa-rotate-left"></i> Reset Form
                    </button>
                    <a href="{{ route('admin.masters.account') }}" class="btn btn-outline erp-btn-sidebar-cancel">
                        <i class="fa-solid fa-xmark"></i> Cancel
                    </a>
                </div>

                <!-- Guidance Tips Card -->
                <div class="card" style="padding: 1.15rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.65rem;">
                        <i class="fa-solid fa-circle-info text-primary"></i>
                        <h5 style="margin: 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">Account Master Rules</h5>
                    </div>
                    <ul style="font-size: 0.78rem; color: #64748B; margin: 0; padding-left: 1.15rem; line-height: 1.55;">
                        <li>Status is automatically set to <strong>Active</strong> upon registration.</li>
                        <li><strong>Bank &amp; Cash</strong> accounts appear automatically in Payment &amp; Receipt vouchers.</li>
                        <li><strong>Expense Ledgers</strong> automatically suggest Debit (Dr) balance nature.</li>
                        <li><strong>Income Ledgers</strong> automatically suggest Credit (Cr) balance nature.</li>
                    </ul>
                </div>

            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
    function handleGroupChange() {
        const groupVal = document.getElementById('field-group').value;
        const balanceTypeElem = document.getElementById('field-balance-type');

        // Automatic smart balance nature suggestion
        if (groupVal === 'Direct Incomes' || groupVal === 'Indirect Incomes' || groupVal === 'Duties & Taxes' || groupVal === 'Current Liabilities' || groupVal === 'Capital & Reserves') {
            balanceTypeElem.value = 'credit';
        } else {
            balanceTypeElem.value = 'debit';
        }

        generateAccountCode();
        updateLivePreview();
    }

    function generateAccountCode() {
        const groupVal = document.getElementById('field-group').value;
        const btn = document.querySelector('.erp-btn-suggest');
        if (btn) btn.disabled = true;

        fetch(`{{ route("admin.masters.account.generate-code") }}?group=${encodeURIComponent(groupVal)}`)
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
        const balanceVal = parseFloat(document.getElementById('field-balance').value) || 0;
        const balanceTypeVal = document.getElementById('field-balance-type').value === 'debit' ? 'Dr' : 'Cr';
        const bankVal = document.getElementById('field-bank-name').value.trim();
        const accountNoVal = document.getElementById('field-account-number').value.trim();
        const ifscVal = document.getElementById('field-ifsc').value.trim();
        const companyElem = document.getElementById('field-company');
        const companyText = companyElem.selectedIndex > 0 ? companyElem.options[companyElem.selectedIndex].text.split('(')[0].trim() : 'All Units';

        // Avatar Initials
        let initials = 'AC';
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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLivePreview();
    });
</script>
@endpush
@endsection
