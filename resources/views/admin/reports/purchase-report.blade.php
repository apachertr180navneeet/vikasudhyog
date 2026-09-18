@extends('admin.layouts.app')

@section('title', 'Purchase Report - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-purchase')

@section('content')
<section class="view-section active" id="view-rpt-purchase">
                    <div class="page-header">
                        <div><h1 class="page-title">Purchase Report</h1><p class="page-subtitle">Detailed vendor purchase summary</p></div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Date</th><th>Invoice No</th><th>Vendor Firm</th><th>Subtotal</th><th>GST</th><th>Total Amount</th></tr></thead>
                                <tbody id="rpt-purchase-body"></tbody>
                            </table>
                        </div>
                        <div style="margin-top: 1rem; font-weight: 700; font-size: 1.1rem; color: var(--dark);" id="rpt-pur-total-val"></div>
                    </div>
                </section>
@endsection


