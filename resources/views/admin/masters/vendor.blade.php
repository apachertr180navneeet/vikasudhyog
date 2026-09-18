@extends('admin.layouts.app')

@section('title', 'Vendor Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-vendor')

@section('content')
<section class="view-section active" id="view-master-vendor">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Vendor Master</h1>
                            <p class="page-subtitle">Supplier accounts & raw material vendors</p>
                        </div>
                        <button class="btn btn-primary" onclick="document.getElementById('vendor-form').reset(); document.getElementById('vnd-id').value=''; App.openModal('modal-vendor');">+ Add Vendor</button>
                    </div>

                    <div class="card">
                        <div class="table-responsive">
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Vendor Firm</th>
                                        <th>Contact Person</th>
                                        <th>Phone</th>
                                        <th>GSTIN</th>
                                        <th>City</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vendor-table-body"></tbody>
                            </table>
                        </div>
                    </div>
                </section>
@endsection


