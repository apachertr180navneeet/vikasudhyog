@extends('admin.layouts.app')

@section('title', 'Receipt Voucher ' . $receiptVoucher->voucher_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-receipt')

@section('content')
<section class="view-section active" id="view-receipt-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar d-print-none">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.receipt-voucher') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $receiptVoucher->voucher_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i> Receipt Voucher Dossier
            </h1>
            <p class="erp-page-subtitle">
                Official transaction collection receipt, settlement verification &amp; ledger credit record.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Receipt Slip">
                <i class="fa-solid fa-print"></i> Print Voucher
            </button>
            <a href="{{ route('admin.transactions.receipt-voucher.edit', $receiptVoucher) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Voucher
            </a>
            <a href="{{ route('admin.transactions.receipt-voucher') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success d-print-none">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Profile Hero Banner Card -->
    <div class="card erp-profile-hero-card d-print-none" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.5rem 1.75rem; margin-bottom: 1.5rem; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 68px; height: 68px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.35);">
                    {{ $receiptVoucher->initials }}
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <h2 style="font-size: 1.35rem; font-weight: 800; color: #0F172A; margin: 0;">
                            {{ $receiptVoucher->party_name }}
                        </h2>
                        <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.8rem; padding: 4px 9px; border-radius: 6px; border: 1px solid #E2E8F0;">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $receiptVoucher->voucher_no }}
                        </span>
                        @if($receiptVoucher->receipt_type === 'Customer')
                            <span class="badge" style="background: rgba(37, 99, 235, 0.1); color: #2563EB; font-size: 0.76rem; font-weight: 600; padding: 4px 9px; border-radius: 9999px; border: 1px solid rgba(37, 99, 235, 0.25);">
                                Customer Collection
                            </span>
                        @else
                            <span class="badge" style="background: rgba(217, 119, 6, 0.1); color: #D97706; font-size: 0.76rem; font-weight: 600; padding: 4px 9px; border-radius: 9999px; border: 1px solid rgba(217, 119, 6, 0.25);">
                                Direct Income
                            </span>
                        @endif
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.4rem; font-size: 0.82rem; color: #64748B; flex-wrap: wrap;">
                        <span><i class="fa-regular fa-calendar me-1"></i>{{ $receiptVoucher->voucher_date->format('d M, Y') }}</span>
                        @if($receiptVoucher->customer && $receiptVoucher->customer->city)
                            <span><i class="fa-solid fa-location-dot me-1"></i>{{ $receiptVoucher->customer->city }}</span>
                        @endif
                        @if($receiptVoucher->account)
                            <span><i class="fa-solid fa-building-columns me-1"></i>{{ $receiptVoucher->account->name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                @if($receiptVoucher->status === 'active')
                    <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.82rem; font-weight: 700; padding: 6px 14px; border-radius: 9999px; border: 1px solid rgba(16, 185, 129, 0.3);">
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Active &bull; Credited
                    </span>
                @else
                    <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-size: 0.82rem; font-weight: 700; padding: 6px 14px; border-radius: 9999px; border: 1px solid rgba(220, 38, 38, 0.3);">
                        <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 4px;"></i> Cancelled &bull; Reversed
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- 4-Stat KPI Ribbon -->
    <div class="erp-kpi-grid d-print-none" style="margin-bottom: 1.5rem;">
        <!-- Amount -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Received Amount</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($receiptVoucher->amount, 2) }}</div>
            </div>
        </div>

        <!-- Mode -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Payment Channel</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem; color: #1E293B;">{{ $receiptVoucher->payment_mode }}</div>
            </div>
        </div>

        <!-- Account -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #5B841E;">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Deposited Ledger</div>
                <div class="erp-kpi-val" style="font-size: 1.05rem; color: #1E293B; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                    {{ $receiptVoucher->account ? $receiptVoucher->account->name : 'Unspecified' }}
                </div>
            </div>
        </div>

        <!-- Reference -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #64748B;">
            <div class="erp-kpi-icon-box" style="background: rgba(100, 116, 139, 0.12); color: #64748B;">
                <i class="fa-solid fa-hashtag"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Cheque / UTR Ref</div>
                <div class="erp-kpi-val font-monospace" style="font-size: 1.05rem; color: #475569;">
                    {{ $receiptVoucher->reference_no ?: 'None / Cash' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div class="erp-form-layout-2col d-print-none" style="margin-bottom: 2rem;">
        <!-- Left Column: Payer & Voucher Information -->
        <div>
            <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04);">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-user-tag text-primary"></i> Payer &amp; Party Details
                </h3>

                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <tbody>
                        <tr>
                            <td style="width: 38%; padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Party Category</td>
                            <td style="padding: 0.65rem 0.5rem; color: #0F172A; font-weight: 600;">
                                {{ $receiptVoucher->receipt_type === 'Customer' ? 'Customer Receivable Collection' : 'Direct Operational Income' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Party Name</td>
                            <td style="padding: 0.65rem 0.5rem; color: #0F172A; font-weight: 700;">
                                @if($receiptVoucher->customer)
                                    <a href="{{ route('admin.masters.customer.show', $receiptVoucher->customer_id) }}" style="color: #5B841E; text-decoration: none;">
                                        {{ $receiptVoucher->customer->name }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.72rem;"></i>
                                    </a>
                                @else
                                    {{ $receiptVoucher->income_source }}
                                @endif
                            </td>
                        </tr>
                        @if($receiptVoucher->customer)
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Customer Code</td>
                                <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; color: #334155; font-weight: 600;">{{ $receiptVoucher->customer->code }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Phone / Contact</td>
                                <td style="padding: 0.65rem 0.5rem; color: #334155;">{{ $receiptVoucher->customer->phone ?: 'Not provided' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">GSTIN</td>
                                <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; color: #334155;">{{ $receiptVoucher->customer->gstin ?: 'Unregistered' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Current Balance</td>
                                <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; font-weight: 700; color: {{ $receiptVoucher->customer->current_balance > 0 ? '#DC2626' : '#059669' }};">
                                    ₹{{ number_format($receiptVoucher->customer->current_balance, 2) }}
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Against Sales Bill / Inv</td>
                            <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; color: #5B841E; font-weight: 600;">
                                {{ $receiptVoucher->against_invoice ?: 'General On-Account Receipt' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Remarks Card -->
            <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.5rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04);">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-note-sticky text-primary"></i> Particulars &amp; Remarks
                </h3>
                <p style="font-size: 0.88rem; color: #334155; line-height: 1.6; margin: 0; background: #F8FAFC; padding: 1rem; border-radius: 8px; border: 1px solid #E2E8F0;">
                    {{ $receiptVoucher->notes ?: 'No special notes or narration recorded for this transaction.' }}
                </p>
            </div>
        </div>

        <!-- Right Column: Banking & Financial Settlement -->
        <div>
            <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04);">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-vault text-primary"></i> Banking &amp; Financial Settlement
                </h3>

                <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <tbody>
                        <tr>
                            <td style="width: 38%; padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Net Amount</td>
                            <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; font-size: 1.2rem; font-weight: 800; color: #059669;">
                                ₹{{ number_format($receiptVoucher->amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Payment Mode</td>
                            <td style="padding: 0.65rem 0.5rem;">
                                <span class="badge" style="background: #F1F5F9; color: #334155; font-size: 0.8rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                    {{ $receiptVoucher->payment_mode }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Deposited Ledger A/c</td>
                            <td style="padding: 0.65rem 0.5rem; color: #0F172A; font-weight: 700;">
                                @if($receiptVoucher->account)
                                    <a href="{{ route('admin.masters.account.show', $receiptVoucher->account_id) }}" style="color: #5B841E; text-decoration: none;">
                                        {{ $receiptVoucher->account->name }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.72rem;"></i>
                                    </a>
                                    <div style="font-size: 0.75rem; color: #64748B; font-weight: normal;">
                                        {{ $receiptVoucher->account->account_group }}
                                    </div>
                                @else
                                    <span style="color: #94A3B8;">Not specified</span>
                                @endif
                            </td>
                        </tr>
                        @if($receiptVoucher->account && $receiptVoucher->account->is_bank)
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Bank / Branch</td>
                                <td style="padding: 0.65rem 0.5rem; color: #334155;">{{ $receiptVoucher->account->bank_name }} ({{ $receiptVoucher->account->branch_name ?: 'Main' }})</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Bank A/c No</td>
                                <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; color: #334155;">{{ $receiptVoucher->account->account_number ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">IFSC Code</td>
                                <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; color: #334155;">{{ $receiptVoucher->account->ifsc_code ?: 'N/A' }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Cheque / UTR Ref</td>
                            <td style="padding: 0.65rem 0.5rem; font-family: Consolas, monospace; color: #334155;">
                                {{ $receiptVoucher->reference_no ?: 'None' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 0.65rem 0.5rem; color: #64748B; font-size: 0.84rem; font-weight: 600;">Instrument Date</td>
                            <td style="padding: 0.65rem 0.5rem; color: #334155;">
                                {{ $receiptVoucher->reference_date ? $receiptVoucher->reference_date->format('d M, Y') : 'N/A' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Audit Stamp Card -->
            <div class="card" style="border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.25rem 1.5rem; box-shadow: 0 4px 18px rgba(0,0,0,0.04);">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #64748B;">
                    <span>Created on <strong>{{ $receiptVoucher->created_at->format('d M, Y \a\t H:i') }}</strong></span>
                    @if($receiptVoucher->creator)
                        <span>By <strong>{{ $receiptVoucher->creator->name }}</strong></span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Official Printable Voucher Section (Displayed in Print mode) -->
    <div class="erp-print-voucher" style="display: none; background: #FFFFFF; padding: 2rem; border: 1px solid #000; font-family: Arial, sans-serif;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h2 style="margin: 0; font-size: 1.6rem; color: #1E3A8A; font-weight: bold;">VIKAS UDHYOG</h2>
                <div style="font-size: 0.85rem; color: #333; margin-top: 4px;">
                    Sojat City, Pali, Rajasthan &bull; ISO 9001:2015 Certified Herbal Processing
                </div>
                <div style="font-size: 0.85rem; color: #333;">
                    GSTIN: 08AABFV1234F1Z8 &bull; Phone: +91 94141 12345
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.3rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #111;">
                    RECEIPT VOUCHER
                </div>
                <div style="font-size: 1rem; font-weight: bold; font-family: monospace; margin-top: 4px;">
                    {{ $receiptVoucher->voucher_no }}
                </div>
                <div style="font-size: 0.85rem; color: #555;">
                    Date: {{ $receiptVoucher->voucher_date->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; font-size: 0.9rem;">
            <div style="padding: 1rem; border: 1px solid #ccc; border-radius: 6px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: #777; font-weight: bold; margin-bottom: 4px;">Received From (Party)</div>
                <div style="font-size: 1.1rem; font-weight: bold;">{{ $receiptVoucher->party_name }}</div>
                @if($receiptVoucher->customer)
                    <div style="margin-top: 4px; color: #444;">Code: {{ $receiptVoucher->customer->code }}</div>
                    @if($receiptVoucher->customer->city)<div>City: {{ $receiptVoucher->customer->city }}, {{ $receiptVoucher->customer->state }}</div>@endif
                @endif
                @if($receiptVoucher->against_invoice)
                    <div style="margin-top: 6px; font-weight: bold; color: #1E3A8A;">Against Bill: {{ $receiptVoucher->against_invoice }}</div>
                @endif
            </div>

            <div style="padding: 1rem; border: 1px solid #ccc; border-radius: 6px;">
                <div style="font-size: 0.75rem; text-transform: uppercase; color: #777; font-weight: bold; margin-bottom: 4px;">Settlement Details</div>
                <div style="margin-bottom: 4px;"><strong>Payment Mode:</strong> {{ $receiptVoucher->payment_mode }}</div>
                <div style="margin-bottom: 4px;"><strong>Deposited To:</strong> {{ $receiptVoucher->account ? $receiptVoucher->account->name : 'Cash In Hand' }}</div>
                @if($receiptVoucher->reference_no)
                    <div style="margin-bottom: 4px;"><strong>Cheque / UTR No:</strong> {{ $receiptVoucher->reference_no }}</div>
                @endif
                @if($receiptVoucher->reference_date)
                    <div><strong>Date:</strong> {{ $receiptVoucher->reference_date->format('d/m/Y') }}</div>
                @endif
            </div>
        </div>

        <div style="padding: 1.25rem; background: #f9f9f9; border: 2px solid #333; margin-bottom: 2rem; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-size: 0.8rem; text-transform: uppercase; color: #555; font-weight: bold;">Amount Received</div>
                <div style="font-size: 0.9rem; font-style: italic; color: #333; margin-top: 3px;">
                    Narration: {{ $receiptVoucher->notes ?: 'Received on account with thanks.' }}
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.8rem; font-weight: bold; font-family: monospace; color: #000;">
                    ₹{{ number_format($receiptVoucher->amount, 2) }}
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 4rem; padding-top: 1rem; font-size: 0.85rem;">
            <div style="text-align: center; width: 200px; border-top: 1px solid #000; padding-top: 6px;">
                Customer / Payer Signature
            </div>
            <div style="text-align: center; width: 220px; border-top: 1px solid #000; padding-top: 6px;">
                For VIKAS UDHYOG<br>
                <span style="font-size: 0.75rem; color: #666;">Authorised Signatory</span>
            </div>
        </div>
    </div>
</section>

<style>
@media print {
    .app-header, .top-navbar, .sidebar, .sidebar-drawer, .sidebar-overlay, .d-print-none, .erp-page-top-bar, .erp-kpi-grid, .erp-profile-hero-card {
        display: none !important;
    }
    .main-wrapper, .content-body, .view-section {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .erp-print-voucher {
        display: block !important;
    }
}
</style>

@if(request('print'))
<script>
window.addEventListener('DOMContentLoaded', function() {
    window.print();
});
</script>
@endif
@endsection
