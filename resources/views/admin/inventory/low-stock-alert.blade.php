@extends('admin.layouts.app')

@section('title', 'Low Stock Alert - VIKAS UDHYOG ERP')
@section('page_code', 'inv-low-stock')

@section('content')
<section class="view-section active" id="view-inv-low-stock">
                    <div class="page-header">
                        <div><h1 class="page-title" style="color: var(--status-danger);">Low Stock Alert Center</h1><p class="page-subtitle">Items requiring immediate reorder</p></div>
                    </div>
                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead><tr><th>Code</th><th>Product Name</th><th>Category</th><th>Current Stock</th><th>Min Level</th><th>Action</th></tr></thead>
                                <tbody id="low-stock-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


