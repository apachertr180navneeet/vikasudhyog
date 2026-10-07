@extends('admin.layouts.app')

@section('title', 'New Receipt Voucher - VIKAS UDHYOG ERP')
@section('page_code', 'txn-receipt')

@section('content')
<section class="view-section active" id="view-receipt-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.receipt-voucher') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">New Receipt Voucher</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-hand-holding-dollar text-primary"></i> New Receipt Voucher
            </h1>
            <p class="erp-page-subtitle">
                Record payment collections against customer receivables, bank remittances, or miscellaneous direct cash receipts.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.receipt-voucher') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Receipt List
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
    <form action="{{ route('admin.transactions.receipt-voucher.store') }}" method="POST" id="receipt-form">
        @csrf

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Voucher Header & Payer Identity -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Voucher Header &amp; Payer Identity</h3>
                                <p class="erp-form-section-desc">Voucher reference coordinates, entry date, collection type &amp; party</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Voucher No -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>Voucher No <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="generateVoucherCode()" class="erp-btn-suggest" title="Generate next sequential voucher number">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Code
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="voucher_no" id="field-voucher-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter Voucher No" value="{{ old('voucher_no', $nextVoucherNo) }}" required oninput="this.value = this.value.toUpperCase(); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique sequential receipt reference code</span>
                        </div>

                        <!-- Voucher Date -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Voucher Date <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                <input type="date" name="voucher_date" id="field-voucher-date" class="form-control erp-field-input-iconified" value="{{ old('voucher_date', date('Y-m-d')) }}" required onchange="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Date when funds were received</span>
                        </div>

                        <!-- Receipt Type (Customer vs Direct Income) -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Receipt Category <span class="erp-req-star">*</span>
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1rem; border: 2px solid #E2E8F0; border-radius: 10px; cursor: pointer; transition: all 0.2s;" id="label-type-customer" class="receipt-type-radio-card active">
                                    <input type="radio" name="receipt_type" value="Customer" id="type-customer" {{ old('receipt_type', 'Customer') === 'Customer' ? 'checked' : '' }} onchange="toggleReceiptType('Customer')" style="accent-color: #5B841E;">
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem;"><i class="fa-solid fa-user-check me-1" style="color: #2563EB;"></i> Customer Collection</div>
                                        <div style="font-size: 0.76rem; color: #64748B;">Receivable payment against customer balance</div>
                                    </div>
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1rem; border: 2px solid #E2E8F0; border-radius: 10px; cursor: pointer; transition: all 0.2s;" id="label-type-income" class="receipt-type-radio-card">
                                    <input type="radio" name="receipt_type" value="Income" id="type-income" {{ old('receipt_type') === 'Income' ? 'checked' : '' }} onchange="toggleReceiptType('Income')" style="accent-color: #5B841E;">
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem;"><i class="fa-solid fa-coins me-1" style="color: #D97706;"></i> Direct Income</div>
                                        <div style="font-size: 0.76rem; color: #64748B;">Scrap sales, interest, rent &amp; miscellaneous revenues</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Customer Select (shown when Customer) -->
                        <div class="form-group erp-form-col-full" id="group-customer-select">
                            <label class="erp-field-label">
                                Customer Payer <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user-tag erp-field-icon"></i>
                                <select name="customer_id" id="field-customer" class="form-control erp-field-input-iconified" onchange="handleCustomerChange()">
                                    <option value="">Select Customer...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" data-balance="{{ $c->current_balance }}" data-name="{{ $c->name }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} ({{ $c->code }}) &mdash; Outstanding: ₹{{ number_format($c->current_balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint" id="customer-balance-hint">Customer outstanding balance will be reduced by received amount</span>
                        </div>

                        <!-- Income Source Select (shown when Income) -->
                        <div class="form-group erp-form-col-full" id="group-income-source" style="display: none;">
                            <label class="erp-field-label">
                                Income Revenue Head <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-chart-pie erp-field-icon"></i>
                                <select name="income_source" id="field-income-source" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">Select Income Source...</option>
                                    @foreach($incomeSources as $source)
                                        <option value="{{ $source }}" {{ old('income_source') === $source ? 'selected' : '' }}>{{ $source }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Classification head for direct cash inflow</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Financial Amount & Settlement Coordinates -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(5, 150, 105, 0.12); color: #059669;">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Financial Particulars &amp; Banking Settlement</h3>
                                <p class="erp-form-section-desc">Amount collected, receiving bank/cash ledger, payment mode &amp; transaction reference</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Amount -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Received Amount (₹) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <span class="erp-field-icon" style="font-weight: 700; color: #059669;">₹</span>
                                <input type="number" step="0.01" min="0.01" name="amount" id="field-amount" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter Amount" value="{{ old('amount') }}" required style="font-size: 1.15rem; font-weight: 700; color: #059669;" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Net funds collected in Indian National Rupee</span>
                        </div>

                        <!-- Deposited Into Account -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Deposited Account (Bank / Cash Till)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-columns erp-field-icon"></i>
                                <select name="account_id" id="field-account" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">Select Receiving Account...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" data-name="{{ $acc->name }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} ({{ $acc->account_group }}) &mdash; Bal: ₹{{ number_format($acc->current_balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">The ledger balance will be credited with received amount</span>
                        </div>

                        <!-- Payment Mode -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Payment Mode <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-wallet erp-field-icon"></i>
                                <select name="payment_mode" id="field-payment-mode" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    @foreach($paymentModes as $mode)
                                        <option value="{{ $mode }}" {{ old('payment_mode', 'Bank Transfer') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Transfer channel or instrument</span>
                        </div>

                        <!-- Reference No / Cheque No / UTR -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Cheque No / UTR / Txn Reference
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                <input type="text" name="reference_no" id="field-reference-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter Reference No" value="{{ old('reference_no') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Bank UTR, NEFT reference or physical cheque number</span>
                        </div>

                        <!-- Reference Date / Cheque Date -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Cheque / Transaction Date
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-calendar-check erp-field-icon"></i>
                                <input type="date" name="reference_date" id="field-reference-date" class="form-control erp-field-input-iconified" value="{{ old('reference_date') }}">
                            </div>
                            <span class="erp-field-hint">Instrument issue date or bank clearance date</span>
                        </div>

                        <!-- Against Invoice / Outward Bill -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Against Sales Invoice / Bill Ref
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-lines erp-field-icon"></i>
                                <input type="text" name="against_invoice" id="field-against-invoice" class="form-control erp-field-input-iconified" placeholder="Enter Invoice No" value="{{ old('against_invoice') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. SAL-2026-0001 or WBS-2026-0001 (optional)</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Remarks & Description -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-gray">
                                <i class="fa-solid fa-note-sticky"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Remarks &amp; Internal Audit Notes</h3>
                                <p class="erp-form-section-desc">Particulars recorded on payment voucher receipts &amp; audit trails</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Remarks / Notes
                            </label>
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter Remarks">{{ old('notes') }}</textarea>
                            <span class="erp-field-hint">e.g. Received partial payment against Diwali dispatch consignment.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-sidebar-col">

                <!-- Real-Time Live Preview Card -->
                <div class="card erp-preview-card" style="border-radius: 16px; border: 1px solid #E2E8F0; overflow: hidden; box-shadow: 0 4px 18px rgba(0,0,0,0.04); margin-bottom: 1.5rem;">
                    <div class="erp-preview-header" style="background: #F8FAFC; padding: 1rem 1.25rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center;">
                        <div class="erp-preview-badge" style="font-weight: 700; color: #475569; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fa-solid fa-eye text-primary me-1"></i> Live Voucher Preview
                        </div>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.72rem; padding: 3px 8px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.25);">
                            <i class="fa-solid fa-circle" style="font-size: 0.45rem; margin-right: 3px;"></i> Active
                        </span>
                    </div>

                    <div style="padding: 1.5rem 1.25rem; text-align: center;">
                        <div class="avatar" id="preview-avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; margin: 0 auto; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.35);">
                            RC
                        </div>
                        <h4 id="preview-party" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; word-break: break-word;">
                            Select Payer Party
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.35rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-voucher-no" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $nextVoucherNo }}
                            </span>
                            <span class="badge" id="preview-type-badge" style="background: rgba(37, 99, 235, 0.1); color: #2563EB; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid rgba(37, 99, 235, 0.2);">
                                Customer
                            </span>
                        </div>

                        <div style="margin-top: 1.25rem; padding: 1rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #F1F5F9;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; color: #64748B; font-weight: 600; letter-spacing: 0.05em;">Amount to Credit</div>
                            <div id="preview-amount" class="font-monospace" style="font-size: 1.75rem; font-weight: 800; color: #059669; margin-top: 0.25rem;">
                                ₹0.00
                            </div>
                        </div>

                        <div style="margin-top: 1.25rem; text-align: left; font-size: 0.83rem;">
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9;">
                                <span style="color: #64748B;">Voucher Date:</span>
                                <strong id="preview-date" style="color: #1E293B;">{{ date('d M, Y') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9;">
                                <span style="color: #64748B;">Payment Mode:</span>
                                <strong id="preview-mode" style="color: #1E293B;">Bank Transfer</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9;">
                                <span style="color: #64748B;">Deposited Ledger:</span>
                                <strong id="preview-account" style="color: #1E293B; max-width: 140px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">Not Assigned</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0;" id="preview-ref-row">
                                <span style="color: #64748B;">Ref / UTR:</span>
                                <strong id="preview-ref" class="font-monospace" style="color: #1E293B;">&mdash;</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Action Controls Card -->
                <div class="card erp-sidebar-actions-card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.25rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04);">
                    <h4 style="font-size: 0.92rem; font-weight: 700; color: #0F172A; margin-bottom: 0.85rem;">
                        <i class="fa-solid fa-floppy-disk text-primary me-1"></i> Save Voucher
                    </h4>
                    <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 1.25rem;">
                        Upon saving, customer balances and receiving account ledgers will be automatically reconciled.
                    </p>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.95rem; margin-bottom: 0.65rem;">
                        <i class="fa-solid fa-check"></i> Save &amp; Credit Ledger
                    </button>

                    <a href="{{ route('admin.transactions.receipt-voucher') }}" class="btn btn-outline" style="width: 100%; justify-content: center; padding: 0.65rem 1rem;">
                        Cancel
                    </a>
                </div>

            </div>
        </div>
    </form>
</section>

<script>
function toggleReceiptType(type) {
    const custGroup = document.getElementById('group-customer-select');
    const incomeGroup = document.getElementById('group-income-source');
    const custField = document.getElementById('field-customer');
    const incomeField = document.getElementById('field-income-source');
    const labelCust = document.getElementById('label-type-customer');
    const labelIncome = document.getElementById('label-type-income');

    if (type === 'Customer') {
        custGroup.style.display = 'block';
        incomeGroup.style.display = 'none';
        custField.required = true;
        incomeField.required = false;
        labelCust.style.borderColor = '#5B841E';
        labelCust.style.background = 'rgba(91, 132, 30, 0.05)';
        labelIncome.style.borderColor = '#E2E8F0';
        labelIncome.style.background = '#FFFFFF';
        document.getElementById('preview-type-badge').innerText = 'Customer';
        document.getElementById('preview-type-badge').style.color = '#2563EB';
        document.getElementById('preview-type-badge').style.background = 'rgba(37, 99, 235, 0.1)';
        document.getElementById('preview-type-badge').style.borderColor = 'rgba(37, 99, 235, 0.2)';
    } else {
        custGroup.style.display = 'none';
        incomeGroup.style.display = 'block';
        custField.required = false;
        incomeField.required = true;
        labelIncome.style.borderColor = '#5B841E';
        labelIncome.style.background = 'rgba(91, 132, 30, 0.05)';
        labelCust.style.borderColor = '#E2E8F0';
        labelCust.style.background = '#FFFFFF';
        document.getElementById('preview-type-badge').innerText = 'Direct Income';
        document.getElementById('preview-type-badge').style.color = '#D97706';
        document.getElementById('preview-type-badge').style.background = 'rgba(217, 119, 6, 0.1)';
        document.getElementById('preview-type-badge').style.borderColor = 'rgba(217, 119, 6, 0.2)';
    }

    updateLivePreview();
}

function handleCustomerChange() {
    const select = document.getElementById('field-customer');
    const hint = document.getElementById('customer-balance-hint');
    if (select.selectedIndex > 0) {
        const option = select.options[select.selectedIndex];
        const balance = option.getAttribute('data-balance');
        hint.innerHTML = `<strong style="color: #2563EB;">Current Outstanding: ₹${parseFloat(balance || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong> &bull; Will reduce upon credit.`;
    } else {
        hint.innerHTML = `Customer outstanding balance will be reduced by received amount`;
    }
    updateLivePreview();
}

function updateLivePreview() {
    const voucherNo = document.getElementById('field-voucher-no').value || 'RCP-2026-0001';
    document.getElementById('preview-voucher-no').innerText = voucherNo;

    const isCustomer = document.getElementById('type-customer').checked;
    let partyName = 'Select Payer Party';
    let initials = 'RC';

    if (isCustomer) {
        const custSelect = document.getElementById('field-customer');
        if (custSelect && custSelect.selectedIndex > 0) {
            partyName = custSelect.options[custSelect.selectedIndex].getAttribute('data-name') || custSelect.options[custSelect.selectedIndex].text;
            const words = partyName.trim().split(/\s+/);
            initials = words.length >= 2 ? (words[0][0] + words[1][0]).toUpperCase() : partyName.substring(0, 2).toUpperCase();
        }
    } else {
        const incomeSelect = document.getElementById('field-income-source');
        if (incomeSelect && incomeSelect.value) {
            partyName = incomeSelect.value;
            const words = partyName.trim().split(/\s+/);
            initials = words.length >= 2 ? (words[0][0] + words[1][0]).toUpperCase() : partyName.substring(0, 2).toUpperCase();
        }
    }

    document.getElementById('preview-party').innerText = partyName;
    document.getElementById('preview-avatar').innerText = initials;

    // Amount
    const amountVal = parseFloat(document.getElementById('field-amount').value) || 0;
    document.getElementById('preview-amount').innerText = '₹' + amountVal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // Date
    const dateVal = document.getElementById('field-voucher-date').value;
    if (dateVal) {
        const d = new Date(dateVal);
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        document.getElementById('preview-date').innerText = d.toLocaleDateString('en-GB', options);
    }

    // Mode
    const modeVal = document.getElementById('field-payment-mode').value;
    document.getElementById('preview-mode').innerText = modeVal;

    // Account
    const accSelect = document.getElementById('field-account');
    if (accSelect && accSelect.selectedIndex > 0) {
        document.getElementById('preview-account').innerText = accSelect.options[accSelect.selectedIndex].getAttribute('data-name') || accSelect.options[accSelect.selectedIndex].text;
    } else {
        document.getElementById('preview-account').innerText = 'Not Assigned';
    }

    // Ref
    const refVal = document.getElementById('field-reference-no').value;
    document.getElementById('preview-ref').innerText = refVal || '—';
}

function generateVoucherCode() {
    fetch('{{ route("admin.transactions.receipt-voucher.generate-code") }}')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.code) {
                document.getElementById('field-voucher-no').value = data.code;
                updateLivePreview();
            }
        })
        .catch(err => console.error(err));
}

document.addEventListener('DOMContentLoaded', function() {
    const isIncome = {{ old('receipt_type') === 'Income' ? 'true' : 'false' }};
    toggleReceiptType(isIncome ? 'Income' : 'Customer');
    handleCustomerChange();
    updateLivePreview();
});
</script>
@endsection
