@extends('admin.layouts.app')

@section('title', 'WB Sales Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-sales')

@section('content')
<section class="view-section active" id="view-txn-wb-sales">
                    <div class="page-header">
                        <div><h1 class="page-title">WB Sales Entry (Without Bill)</h1><p class="page-subtitle">Manage Without-Bill (WB) / Mandi Cash Wholesale Sales Entries</p></div>
                        <button class="btn btn-primary" onclick="App.openModal('modal-wb-sales')">+ New WB Sales Entry</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>WB Slip No</th><th>Date</th><th>Customer</th><th>Net Weight</th><th>WB Amount</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody id="wb-sales-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


