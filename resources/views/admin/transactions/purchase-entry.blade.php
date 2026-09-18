@extends('admin.layouts.app')

@section('title', 'Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<section class="view-section active" id="view-txn-purchase">
                    <div class="page-header">
                        <div><h1 class="page-title">Purchase Entry</h1><p class="page-subtitle">Record raw material & bulk product purchases</p></div>
                        <button class="btn btn-primary" onclick="App.openModal('modal-purchase');">+ New Purchase Entry</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Inv No</th><th>Date</th><th>Vendor Firm</th><th>Bill Subtotal</th><th>GST Tax</th><th>U_B Total</th><th>Grand Total</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody id="purchase-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


