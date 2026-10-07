@extends('admin.layouts.app')

@section('title', 'Edit Payment Voucher ' . $paymentVoucher->voucher_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-payment')

@section('content')
<section class="view-section active" id="view-payment-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.payment-voucher') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.payment-voucher.show', $paymentVoucher) }}">{{ $paymentVoucher->voucher_no }}</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Voucher</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Payment Voucher
            </h1>
            <p class="erp-page-subtitle">
                Modify disbursement particulars, remittance instrument details, reference numbers or source ledger account.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.payment-voucher.show', $paymentVoucher) }}" class="btn btn-outline">
                <i class="fa-solid fa-eye"></i> View Voucher Profile
            </a>
            <a href="{{ route('admin.transactions.payment-voucher') }}" class="btn btn-outline">
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
    <form action="{{ route('admin.transactions.payment-voucher.update', $paymentVoucher) }}" method="POST" id="payment-edit-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Voucher Header & Beneficiary Identity -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                                <i class="fa-solid fa-file-invoice"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Voucher Header &amp; Beneficiary Identity</h3>
                                <p class="erp-form-section-desc">Voucher reference coordinates, entry date, payment category &amp; payee details</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Voucher No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Voucher No <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-barcode erp-field-icon"></i>
                                <input type="text" name="voucher_no" id="field-voucher-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter Voucher No" value="{{ old('voucher_no', $paymentVoucher->voucher_no) }}" required oninput="this.value = this.value.toUpperCase(); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Unique sequential payment reference code</span>
                        </div>

                        <!-- Voucher Date -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Voucher Date <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                <input type="date" name="voucher_date" id="field-voucher-date" class="form-control erp-field-input-iconified" value="{{ old('voucher_date', $paymentVoucher->voucher_date->format('Y-m-d')) }}" required onchange="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Date when disbursement was executed</span>
                        </div>

                        <!-- Payment Category (Vendor vs Expense) -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Payment Category <span class="erp-req-star">*</span>
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1rem; border: 2px solid #E2E8F0; border-radius: 10px; cursor: pointer; transition: all 0.2s;" id="label-type-vendor" class="payment-type-radio-card {{ old('payment_type', $paymentVoucher->payment_type) === 'Vendor' ? 'active' : '' }}">
                                    <input type="radio" name="payment_type" value="Vendor" id="type-vendor" {{ old('payment_type', $paymentVoucher->payment_type) === 'Vendor' ? 'checked' : '' }} onchange="togglePaymentType('Vendor')" style="accent-color: #5B841E;">
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem;"><i class="fa-solid fa-truck-ramp-box me-1" style="color: #2563EB;"></i> Vendor Settlement</div>
                                        <div style="font-size: 0.76rem; color: #64748B;">Disbursement against raw material purchase debts</div>
                                    </div>
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.65rem; padding: 0.85rem 1rem; border: 2px solid #E2E8F0; border-radius: 10px; cursor: pointer; transition: all 0.2s;" id="label-type-expense" class="payment-type-radio-card {{ old('payment_type', $paymentVoucher->payment_type) === 'Expense' ? 'active' : '' }}">
                                    <input type="radio" name="payment_type" value="Expense" id="type-expense" {{ old('payment_type', $paymentVoucher->payment_type) === 'Expense' ? 'checked' : '' }} onchange="togglePaymentType('Expense')" style="accent-color: #5B841E;">
                                    <div>
                                        <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem;"><i class="fa-solid fa-receipt me-1" style="color: #D97706;"></i> Direct Operational Expense</div>
                                        <div style="font-size: 0.76rem; color: #64748B;">Freight, wages, power, maintenance &amp; overheads</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Vendor Select (shown when Vendor) -->
                        <div class="form-group erp-form-col-full" id="group-vendor-select">
                            <label class="erp-field-label">
                                Vendor / Supplier Payee <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building-user erp-field-icon"></i>
                                <select name="vendor_id" id="field-vendor" class="form-control erp-field-input-iconified" onchange="handleVendorChange()">
                                    <option value="">Select Vendor...</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}" data-balance="{{ $v->current_balance }}" data-name="{{ $v->name }}" {{ old('vendor_id', $paymentVoucher->vendor_id) == $v->id ? 'selected' : '' }}>
                                            {{ $v->name }} ({{ $v->code }}) &mdash; Payable Debt: ₹{{ number_format($v->current_balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint" id="vendor-balance-hint">Vendor payable balance will be automatically adjusted</span>
                        </div>

                        <!-- Expense Head Select (shown when Expense) -->
                        <div class="form-group erp-form-col-full" id="group-expense-head" style="display: none;">
                            <label class="erp-field-label">
                                Operational Expense Head <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-chart-pie erp-field-icon"></i>
                                <select name="expense_head" id="field-expense-head" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">Select Expense Head...</option>
                                    @foreach($expenseHeads as $head)
                                        <option value="{{ $head }}" {{ old('expense_head', $paymentVoucher->expense_head) === $head ? 'selected' : '' }}>{{ $head }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Classification head for direct cash outflow</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Financial Amount & Banking Settlement -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Financial Particulars &amp; Banking Settlement</h3>
                                <p class="erp-form-section-desc">Amount paid, source bank/cash ledger, payment instrument &amp; transaction reference</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Amount -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Paid Amount (₹) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <span class="erp-field-icon" style="font-weight: 700; color: #DC2626;">₹</span>
                                <input type="number" step="0.01" min="0.01" name="amount" id="field-amount" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter Amount" value="{{ old('amount', $paymentVoucher->amount) }}" required style="font-size: 1.15rem; font-weight: 700; color: #DC2626;" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Net funds disbursed in Indian National Rupee</span>
                        </div>

                        <!-- Withdrawn From Account -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Paid From Account (Bank / Cash Till)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-vault erp-field-icon"></i>
                                <select name="account_id" id="field-account" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">Select Paying Account...</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}" data-name="{{ $acc->name }}" {{ old('account_id', $paymentVoucher->account_id) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->name }} ({{ $acc->account_group }}) &mdash; Bal: ₹{{ number_format($acc->current_balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">The source ledger will be debited with paid amount</span>
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
                                        <option value="{{ $mode }}" {{ old('payment_mode', $paymentVoucher->payment_mode) === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Payment remittance mode or instrument</span>
                        </div>

                        <!-- Reference No / Cheque No / UTR -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Cheque No / UTR / Txn Reference
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                <input type="text" name="reference_no" id="field-reference-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter Reference No" value="{{ old('reference_no', $paymentVoucher->reference_no) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Bank UTR, RTGS/NEFT ref or physical cheque number</span>
                        </div>

                        <!-- Reference Date / Cheque Date -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Cheque / Transaction Date
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-calendar-check erp-field-icon"></i>
                                <input type="date" name="reference_date" id="field-reference-date" class="form-control erp-field-input-iconified" value="{{ old('reference_date', optional($paymentVoucher->reference_date)->format('Y-m-d')) }}">
                            </div>
                            <span class="erp-field-hint">Instrument issue date or bank clearance date</span>
                        </div>

                        <!-- Against Invoice / Inward Bill -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Against Purchase Bill / Inward Ref
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-lines erp-field-icon"></i>
                                <input type="text" name="against_invoice" id="field-against-invoice" class="form-control erp-field-input-iconified" placeholder="Enter Invoice No" value="{{ old('against_invoice', $paymentVoucher->against_invoice) }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">e.g. PUR-2026-0001 or WBP-2026-0001 (optional)</span>
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
                                <p class="erp-form-section-desc">Particulars recorded on payment vouchers &amp; ledger narrations</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Remarks / Notes
                            </label>
                            <textarea name="notes" id="field-notes" rows="3" class="form-control" placeholder="Enter Remarks">{{ old('notes', $paymentVoucher->notes) }}</textarea>
                            <span class="erp-field-hint">e.g. Paid full settlement for raw henna leaves procurement batch.</span>
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
                        <span class="badge" style="background: {{ $paymentVoucher->status === 'active' ? 'rgba(16, 185, 129, 0.12)' : 'rgba(220, 38, 38, 0.12)' }}; color: {{ $paymentVoucher->status === 'active' ? '#059669' : '#DC2626' }}; font-size: 0.72rem; padding: 3px 8px; border-radius: 9999px; border: 1px solid {{ $paymentVoucher->status === 'active' ? 'rgba(16, 185, 129, 0.25)' : 'rgba(220, 38, 38, 0.25)' }};">
                            <i class="fa-solid fa-circle" style="font-size: 0.45rem; margin-right: 3px;"></i> {{ ucfirst($paymentVoucher->status) }}
                        </span>
                    </div>

                    <div style="padding: 1.5rem 1.25rem; text-align: center;">
                        <div class="avatar" id="preview-avatar" style="background: linear-gradient(135deg, #DC2626, #991B1B); color: #FFFFFF; font-weight: 700; width: 62px; height: 62px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; margin: 0 auto; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.35);">
                            {{ $paymentVoucher->initials }}
                        </div>
                        <h4 id="preview-party" style="margin-top: 0.85rem; font-weight: 700; color: #0F172A; word-break: break-word;">
                            {{ $paymentVoucher->party_name }}
                        </h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.4rem; margin-top: 0.35rem; flex-wrap: wrap;">
                            <span class="badge font-monospace" id="preview-voucher-no" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                {{ $paymentVoucher->voucher_no }}
                            </span>
                            <span class="badge" id="preview-type-badge" style="background: {{ $paymentVoucher->payment_type === 'Vendor' ? 'rgba(37, 99, 235, 0.1)' : 'rgba(217, 119, 6, 0.1)' }}; color: {{ $paymentVoucher->payment_type === 'Vendor' ? '#2563EB' : '#D97706' }}; font-size: 0.74rem; font-weight: 600; padding: 3px 9px; border-radius: 9999px; border: 1px solid {{ $paymentVoucher->payment_type === 'Vendor' ? 'rgba(37, 99, 235, 0.2)' : 'rgba(217, 119, 6, 0.2)' }};">
                                {{ $paymentVoucher->payment_type === 'Vendor' ? 'Vendor' : 'Expense' }}
                            </span>
                        </div>

                        <div style="margin-top: 1.25rem; padding: 1rem; background: #FEF2F2; border-radius: 12px; border: 1px solid #FEE2E2;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; color: #991B1B; font-weight: 600; letter-spacing: 0.05em;">Amount to Disburse</div>
                            <div id="preview-amount" class="font-monospace" style="font-size: 1.75rem; font-weight: 800; color: #DC2626; margin-top: 0.25rem;">
                                ₹{{ number_format($paymentVoucher->amount, 2) }}
                            </div>
                        </div>

                        <div style="margin-top: 1.25rem; text-align: left; font-size: 0.83rem;">
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9;">
                                <span style="color: #64748B;">Voucher Date:</span>
                                <strong id="preview-date" style="color: #1E293B;">{{ $paymentVoucher->voucher_date->format('d M, Y') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9;">
                                <span style="color: #64748B;">Payment Mode:</span>
                                <strong id="preview-mode" style="color: #1E293B;">{{ $paymentVoucher->payment_mode }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid #F1F5F9;">
                                <span style="color: #64748B;">Paid From Ledger:</span>
                                <strong id="preview-account" style="color: #1E293B; max-width: 140px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $paymentVoucher->account ? $paymentVoucher->account->name : 'Not Assigned' }}
                                </strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 0.4rem 0;" id="preview-ref-row">
                                <span style="color: #64748B;">Ref / UTR:</span>
                                <strong id="preview-ref" class="font-monospace" style="color: #1E293B;">{{ $paymentVoucher->reference_no ?: '—' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Action Controls Card -->
                <div class="card erp-sidebar-actions-card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.25rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04); margin-bottom: 1.5rem;">
                    <h4 style="font-size: 0.92rem; font-weight: 700; color: #0F172A; margin-bottom: 0.85rem;">
                        <i class="fa-solid fa-floppy-disk text-primary me-1"></i> Update Voucher
                    </h4>
                    <p style="font-size: 0.8rem; color: #64748B; margin-bottom: 1.25rem;">
                        Updating transaction will adjust prior ledger balances and re-calculate vendor and source ledgers.
                    </p>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem 1rem; font-weight: 600; font-size: 0.95rem; margin-bottom: 0.65rem;">
                        <i class="fa-solid fa-check"></i> Update &amp; Reconcile
                    </button>

                    <a href="{{ route('admin.transactions.payment-voucher.show', $paymentVoucher) }}" class="btn btn-outline" style="width: 100%; justify-content: center; padding: 0.65rem 1rem;">
                        Cancel
                    </a>
                </div>

                <!-- Audit Trail Timestamps Card -->
                <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.25rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04); margin-bottom: 1.5rem;">
                    <h4 style="font-size: 0.88rem; font-weight: 700; color: #475569; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-timeline text-primary me-1"></i> Audit Trail
                    </h4>
                    <div style="font-size: 0.8rem; color: #64748B; display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Created:</span>
                            <strong style="color: #1E293B;">{{ $paymentVoucher->created_at->format('d M, Y H:i') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Last Updated:</span>
                            <strong style="color: #1E293B;">{{ $paymentVoucher->updated_at->format('d M, Y H:i') }}</strong>
                        </div>
                        @if($paymentVoucher->creator)
                            <div style="display: flex; justify-content: space-between;">
                                <span>Recorded By:</span>
                                <strong style="color: #1E293B;">{{ $paymentVoucher->creator->name }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Danger Zone Archive Card -->
                <div class="card" style="border-radius: 16px; border: 1px solid rgba(220, 38, 38, 0.25); background: rgba(254, 242, 242, 0.5); padding: 1.25rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04);">
                    <h4 style="font-size: 0.88rem; font-weight: 700; color: #DC2626; margin-bottom: 0.5rem;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Danger Zone
                    </h4>
                    <p style="font-size: 0.78rem; color: #7F1D1D; margin-bottom: 1rem;">
                        Actions here alter or reverse vendor debts and paying ledger balances.
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <form action="{{ route('admin.transactions.payment-voucher.toggle-status', $paymentVoucher) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline" style="width: 100%; justify-content: center; font-size: 0.82rem; border-color: {{ $paymentVoucher->status === 'active' ? '#DC2626' : '#059669' }}; color: {{ $paymentVoucher->status === 'active' ? '#DC2626' : '#059669' }};" onclick="return confirm('{{ $paymentVoucher->status === 'active' ? 'Cancel this payment voucher? Reverses ledger balances.' : 'Reactivate this payment voucher?' }}')">
                                <i class="fa-solid fa-power-off me-1"></i> {{ $paymentVoucher->status === 'active' ? 'Cancel Voucher' : 'Reactivate Voucher' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.transactions.payment-voucher.destroy', $paymentVoucher) }}" method="POST" onsubmit="return confirm('Permanently archive and delete this voucher record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="width: 100%; justify-content: center; font-size: 0.82rem; background: #DC2626; color: #FFFFFF; border: none; padding: 0.5rem;">
                                <i class="fa-solid fa-trash-can me-1"></i> Archive Voucher
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </form>
</section>

<script>
function togglePaymentType(type) {
    const vendorGroup = document.getElementById('group-vendor-select');
    const expenseGroup = document.getElementById('group-expense-head');
    const vendorField = document.getElementById('field-vendor');
    const expenseField = document.getElementById('field-expense-head');
    const labelVendor = document.getElementById('label-type-vendor');
    const labelExpense = document.getElementById('label-type-expense');

    if (type === 'Vendor') {
        vendorGroup.style.display = 'block';
        expenseGroup.style.display = 'none';
        vendorField.required = true;
        expenseField.required = false;
        labelVendor.style.borderColor = '#5B841E';
        labelVendor.style.background = 'rgba(91, 132, 30, 0.05)';
        labelExpense.style.borderColor = '#E2E8F0';
        labelExpense.style.background = '#FFFFFF';
        document.getElementById('preview-type-badge').innerText = 'Vendor';
        document.getElementById('preview-type-badge').style.color = '#2563EB';
        document.getElementById('preview-type-badge').style.background = 'rgba(37, 99, 235, 0.1)';
        document.getElementById('preview-type-badge').style.borderColor = 'rgba(37, 99, 235, 0.2)';
    } else {
        vendorGroup.style.display = 'none';
        expenseGroup.style.display = 'block';
        vendorField.required = false;
        expenseField.required = true;
        labelExpense.style.borderColor = '#5B841E';
        labelExpense.style.background = 'rgba(91, 132, 30, 0.05)';
        labelVendor.style.borderColor = '#E2E8F0';
        labelVendor.style.background = '#FFFFFF';
        document.getElementById('preview-type-badge').innerText = 'Expense';
        document.getElementById('preview-type-badge').style.color = '#D97706';
        document.getElementById('preview-type-badge').style.background = 'rgba(217, 119, 6, 0.1)';
        document.getElementById('preview-type-badge').style.borderColor = 'rgba(217, 119, 6, 0.2)';
    }

    updateLivePreview();
}

function handleVendorChange() {
    const select = document.getElementById('field-vendor');
    const hint = document.getElementById('vendor-balance-hint');
    if (select.selectedIndex > 0) {
        const option = select.options[select.selectedIndex];
        const balance = option.getAttribute('data-balance');
        hint.innerHTML = `<strong style="color: #DC2626;">Payable Debt: ₹${parseFloat(balance || 0).toLocaleString('en-IN', {minimumFractionDigits: 2})}</strong> &bull; Will adjust accordingly.`;
    } else {
        hint.innerHTML = `Vendor payable balance will be automatically adjusted`;
    }
    updateLivePreview();
}

function updateLivePreview() {
    const voucherNo = document.getElementById('field-voucher-no').value || '{{ $paymentVoucher->voucher_no }}';
    document.getElementById('preview-voucher-no').innerText = voucherNo;

    const isVendor = document.getElementById('type-vendor').checked;
    let partyName = 'Select Payee Beneficiary';
    let initials = 'PV';

    if (isVendor) {
        const vendorSelect = document.getElementById('field-vendor');
        if (vendorSelect && vendorSelect.selectedIndex > 0) {
            partyName = vendorSelect.options[vendorSelect.selectedIndex].getAttribute('data-name') || vendorSelect.options[vendorSelect.selectedIndex].text;
            const words = partyName.trim().split(/\s+/);
            initials = words.length >= 2 ? (words[0][0] + words[1][0]).toUpperCase() : partyName.substring(0, 2).toUpperCase();
        }
    } else {
        const expenseSelect = document.getElementById('field-expense-head');
        if (expenseSelect && expenseSelect.value) {
            partyName = expenseSelect.value;
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

document.addEventListener('DOMContentLoaded', function() {
    const isExpense = {{ old('payment_type', $paymentVoucher->payment_type) === 'Expense' ? 'true' : 'false' }};
    togglePaymentType(isExpense ? 'Expense' : 'Vendor');
    handleVendorChange();
    updateLivePreview();
});
</script>
@endsection
