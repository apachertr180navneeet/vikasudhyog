@extends('admin.layouts.app')

@section('title', 'WB Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<section class="view-section active" id="view-txn-wb-purchase">
                    <div class="page-header">
                        <div><h1 class="page-title">WB Purchase Entry (Without Bill)</h1><p class="page-subtitle">Manage Without-Bill (WB) / Mandi Cash Purchase Stock Receipts</p></div>
                        <button class="btn btn-primary" onclick="App.openModal('modal-wb-purchase')">+ New WB Purchase Entry</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>WB Slip No</th><th>Date</th><th>Vendor / Supplier</th><th>Net Weight</th><th>WB Amount</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody id="wb-purchase-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


