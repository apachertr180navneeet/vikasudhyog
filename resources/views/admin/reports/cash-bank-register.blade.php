@extends('admin.layouts.app')

@section('title', 'Cash & Bank Register - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-cash-reg')

@section('content')
<section class="view-section active" id="view-rpt-cash-reg">
    <div class="page-header">
        <div>
            <h1 class="page-title">Cash & Bank Register</h1>
            <p class="page-subtitle">Combined daybook register for all receipts (Dr) and payments (Cr)</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <button class="btn btn-sm btn-outline cash-filter-btn active" onclick="Reports.filterCashRegister('all', this)">All Transactions</button>
            <button class="btn btn-sm btn-outline cash-filter-btn" onclick="Reports.filterCashRegister('receipt', this)"><i class="fa-solid fa-arrow-down-left" style="color:var(--status-success);"></i> Receipts (Dr) Only</button>
            <button class="btn btn-sm btn-outline cash-filter-btn" onclick="Reports.filterCashRegister('payment', this)"><i class="fa-solid fa-arrow-up-right" style="color:var(--status-danger);"></i> Payments (Cr) Only</button>
        </div>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Voucher No</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Party Name</th>
                        <th>Mode</th>
                        <th style="color: var(--status-success);">Debit (Dr) ₹</th>
                        <th style="color: var(--status-danger);">Credit (Cr) ₹</th>
                        <th>Ref Bill/Inv</th>
                        <th>Voucher</th>
                    </tr>
                </thead>
                <tbody id="rpt-cash-body"></tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;" id="rpt-cash-total-val"></div>
    </div>
</section>
@endsection
