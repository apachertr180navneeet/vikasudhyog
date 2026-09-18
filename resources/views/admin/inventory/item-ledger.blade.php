@extends('admin.layouts.app')

@section('title', 'Item Ledger - VIKAS UDHYOG ERP')
@section('page_code', 'inv-ledger')

@section('content')
<section class="view-section active" id="view-inv-ledger">
                    <div class="page-header">
                        <div><h1 class="page-title">Item Stock Ledger</h1><p class="page-subtitle">Item-wise movement log (In / Out timeline)</p></div>
                    </div>
                    <div class="card" style="margin-bottom: 1rem;">
                        <div class="form-grid" style="grid-template-columns: 2fr 1fr;">
                            <div class="form-group">
                                <label class="form-label">Select Herbal Product</label>
                                <select id="ledger-item-select" class="form-control" onchange="Inventory.loadLedgerData()"></select>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Date</th><th>Transaction Type</th><th>Reference No</th><th>Stock In</th><th>Stock Out</th><th>Closing Balance</th></tr></thead>
                                <tbody id="ledger-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


