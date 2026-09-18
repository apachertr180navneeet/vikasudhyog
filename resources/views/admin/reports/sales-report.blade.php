@extends('admin.layouts.app')

@section('title', 'Sales Report - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-sales')

@section('content')
<section class="view-section active" id="view-rpt-sales">
                    <div class="page-header">
                        <div><h1 class="page-title">Sales Report</h1><p class="page-subtitle">Customer sales & tax report</p></div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th>Taxable</th><th>GST</th><th>Grand Total</th><th>Status</th></tr></thead>
                                <tbody id="rpt-sales-body"></tbody>
                            </table>
                        </div>
                        <div style="margin-top: 1rem; font-weight: 700; font-size: 1.1rem; color: var(--dark);" id="rpt-sales-total-val"></div>
                    </div>
                </section>
@endsection


