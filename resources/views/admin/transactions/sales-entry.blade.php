@extends('admin.layouts.app')

@section('title', 'Sales Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-sales-order')

@section('content')
<section class="view-section active" id="view-txn-sales-order">
                    <div class="page-header">
                        <div><h1 class="page-title">Sales Entry</h1><p class="page-subtitle">Manage customer wholesale sales entries</p></div>
                        <button class="btn btn-primary" onclick="App.openModal('modal-sales-order')">+ Create Sales Entry</button>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Order No</th><th>Order Date</th><th>Customer</th><th>Delivery Date</th><th>Bill Amt</th><th>Under Bill Amt</th><th>Grand Total</th><th>Status</th><th>Action</th></tr></thead>
                                <tbody id="so-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


