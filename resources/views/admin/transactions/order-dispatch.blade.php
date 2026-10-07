@extends('admin.layouts.app')

@section('title', 'Order Dispatch & Logistics - VIKAS UDHYOG ERP')
@section('page_code', 'txn-order-dispatch')

@push('styles')
<style>
    /* Order Dispatch Specific Styles adhering to ERP Design System */
    .erp-main-card {
        padding: 0 !important;
        overflow: hidden;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        background: #FFFFFF;
    }

    .custom-table thead th {
        background-color: #F8FAFC !important;
        color: #475569 !important;
        font-size: 0.73rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 0.95rem 1.15rem !important;
        border-bottom: 1px solid #E2E8F0 !important;
        white-space: nowrap;
        vertical-align: middle;
    }

    .custom-table tbody td {
        padding: 0.95rem 1.15rem !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #F1F5F9 !important;
        font-size: 0.88rem;
        color: #1E293B;
    }

    .custom-table tbody tr:hover {
        background-color: #F9FBFA !important;
    }

    .avatar-circle-olive {
        background: linear-gradient(135deg, #5B841E, #3D5A12);
        color: #FFFFFF;
        font-weight: 700;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);
    }

    .dsp-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .dsp-status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
    }

    .dsp-status-dispatched {
        background: #DCFCE7;
        color: #15803D;
        border: 1px solid #86EFAC;
    }
    .dsp-status-dispatched .dsp-status-dot {
        background: #16A34A;
        box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.25);
    }

    .dsp-status-intransit {
        background: #FEF3C7;
        color: #B45309;
        border: 1px solid #FDE68A;
    }
    .dsp-status-intransit .dsp-status-dot {
        background: #D97706;
        box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.25);
    }

    .dsp-status-delivered {
        background: #E0E7FF;
        color: #3730A3;
        border: 1px solid #C7D2FE;
    }
    .dsp-status-delivered .dsp-status-dot {
        background: #4F46E5;
    }

    .dsp-status-pending {
        background: #F1F5F9;
        color: #475569;
        border: 1px solid #CBD5E1;
    }
    .dsp-status-pending .dsp-status-dot {
        background: #64748B;
    }

    /* Print-Only Styles */
    @media print {
        .erp-page-top-bar,
        .erp-kpi-grid,
        .erp-table-filter-header,
        .erp-actions-cell,
        .custom-table th:last-child,
        .custom-table td:last-child,
        .sidebar,
        .topbar,
        .header-actions {
            display: none !important;
        }

        .main-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        .erp-main-card {
            box-shadow: none !important;
            border: none !important;
        }

        .print-manifest-header {
            display: block !important;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #000;
            padding-bottom: 0.75rem;
        }
    }

    .print-manifest-header {
        display: none;
    }
</style>
@endpush

@section('content')
<section class="view-section active" id="view-txn-order-dispatch">
    <!-- Print-Only Manifest Header -->
    <div class="print-manifest-header">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 800; color: #1E293B;">VIKAS UDHYOG</h2>
                <p style="margin: 2px 0 0; font-size: 0.85rem; color: #475569;">Daily Consignment Dispatch &amp; Logistics Manifest (With Bill &amp; Without Bill)</p>
            </div>
            <div style="text-align: right; font-size: 0.8rem; color: #475569;">
                <div><strong>Generated Date:</strong> {{ date('d M Y, h:i A') }}</div>
                <div><strong>Operational Hub:</strong> Sojat City, Pali, Rajasthan</div>
            </div>
        </div>
    </div>

    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.sales-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Order Dispatch</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-fast text-primary"></i> Order Dispatch &amp; Logistics Management
            </h1>
            <p class="erp-page-subtitle">
                Track consignment shipments, With Bill invoices, Without Bill weighbridge outward slips, vehicle allocations, and delivery gate-passes.
            </p>
        </div>

        <div class="erp-header-actions" style="display: flex; gap: 0.5rem; align-items: center;">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Dispatch Manifest">
                <i class="fa-solid fa-print"></i> Print Manifest
            </button>
            <button type="button" class="btn btn-outline" style="border-color: #F59E0B; color: #B45309; background: #FFFBEB; font-weight: 600;" onclick="openNewDispatchModal('without_bill')" title="Create Without Bill Weighbridge Dispatch">
                <i class="fa-solid fa-scale-unbalanced me-1"></i> + Without Bill Slip
            </button>
            <button type="button" class="btn btn-primary erp-btn-header-primary" onclick="openNewDispatchModal('with_bill')" title="Create With Bill Order Dispatch">
                <i class="fa-solid fa-plus me-1"></i> New Order Dispatch
            </button>
        </div>
    </div>

    <!-- 4-Card KPI Statistics Grid (Tracking Total, With Bill, Without Bill, In-Transit) -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Dispatched Orders</div>
                <div class="erp-kpi-val" id="kpi-total-dispatches">0</div>
                <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">All Consignments</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <div class="erp-kpi-label">With Bill Dispatches</div>
                <div class="erp-kpi-val" style="color: #2563EB;" id="kpi-with-bill">0</div>
                <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">Tax Sales Invoices</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #D97706;">
            <div class="erp-kpi-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-scale-unbalanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Without Bill Dispatches</div>
                <div class="erp-kpi-val" style="color: #D97706;" id="kpi-without-bill">0</div>
                <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">WB Outward Slips</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-route"></i>
            </div>
            <div>
                <div class="erp-kpi-label">In-Transit Shipments</div>
                <div class="erp-kpi-val erp-kpi-val-success" id="kpi-in-transit">0</div>
                <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">Active Road Fleet</div>
            </div>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="card erp-main-card">
        <!-- Search & Filter Header -->
        <div class="erp-table-filter-header">
            <div class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; width: 100%;">
                <div class="erp-search-wrap" style="flex: 1; min-width: 250px; max-width: 360px;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" id="dsp-search-input" placeholder="Search order/slip no, customer, vehicle, driver..." class="form-control erp-search-input" oninput="filterDispatches()">
                </div>

                <!-- Bill Type Filter (With Bill vs Without Bill) -->
                <select id="dsp-filter-bill-type" class="form-control erp-filter-select" onchange="filterDispatches()" style="min-width: 175px;">
                    <option value="">All Bill Types</option>
                    <option value="with_bill">With Bill (Sales Invoice)</option>
                    <option value="without_bill">Without Bill (WB Slip Outward)</option>
                </select>

                <select id="dsp-filter-status" class="form-control erp-filter-select" onchange="filterDispatches()" style="min-width: 140px;">
                    <option value="">All Statuses</option>
                    <option value="Dispatched">Dispatched</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Delivered">Delivered</option>
                    <option value="Pending">Pending</option>
                </select>

                <select id="dsp-filter-transporter" class="form-control erp-filter-select" onchange="filterDispatches()" style="min-width: 160px;">
                    <option value="">All Transporters</option>
                </select>

                <button type="button" class="btn btn-outline erp-btn-filter-clear" onclick="resetDispatchFilters()" title="Reset Filters">
                    <i class="fa-solid fa-arrow-rotate-left"></i> Reset
                </button>

                <div class="erp-table-summary-count ms-auto">
                    Showing <strong id="dsp-count-shown">0</strong> of <strong id="dsp-count-total">0</strong> dispatches
                </div>
            </div>
        </div>

        <!-- Edge-to-Edge Data Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem; width: 150px;">Order / Slip No</th>
                        <th style="width: 140px;">Bill Type</th>
                        <th style="min-width: 250px;">Customer / Buyer Firm</th>
                        <th style="width: 130px;">Dispatch Date</th>
                        <th style="width: 150px;">Vehicle No</th>
                        <th style="min-width: 170px;">Driver &amp; Mobile</th>
                        <th style="min-width: 180px;">Transporter / Cargo</th>
                        <th style="width: 125px; text-align: center;">Status</th>
                        <th style="width: 125px; text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody id="dispatch-table-body">
                    <!-- Dynamic Rows Rendered by Enhanced Engine Below -->
                </tbody>
            </table>
        </div>

        <!-- Empty State Container -->
        <div id="dsp-empty-state" style="display: none; padding: 3.5rem 1.5rem; text-align: center;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #F8FAFC; border: 1px solid #E2E8F0; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #94A3B8; font-size: 1.75rem;">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">No Consignment Dispatches Found</h4>
            <p style="color: #64748B; font-size: 0.88rem; max-width: 440px; margin: 0 auto 1.25rem;">
                No shipment records match your current filter query or have been generated yet.
            </p>
            <div style="display: flex; gap: 0.5rem; justify-content: center;">
                <button type="button" class="btn btn-outline" style="border-color: #F59E0B; color: #B45309;" onclick="openNewDispatchModal('without_bill')">
                    <i class="fa-solid fa-scale-unbalanced me-1"></i> New Without Bill Slip
                </button>
                <button type="button" class="btn btn-primary" onclick="openNewDispatchModal('with_bill')">
                    <i class="fa-solid fa-plus me-1"></i> New With Bill Dispatch
                </button>
            </div>
        </div>
    </div>
</section>

<!-- MODAL 1: Create New Order Dispatch (Supports Both With Bill and Without Bill) -->
<div class="modal-overlay" id="modal-new-dispatch" style="display: none;">
    <div class="modal-card" style="max-width: 640px; width: 95%;">
        <div class="modal-header" style="border-bottom: 1px solid #E2E8F0; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1E293B;" id="modal-dispatch-heading">New Consignment Dispatch &amp; Logistics</h3>
                    <p style="margin: 0; font-size: 0.78rem; color: #64748B;">Assign vehicle, driver, transporter and record outward consignment dispatch</p>
                </div>
            </div>
            <button type="button" class="btn-close" onclick="closeNewDispatchModal()" style="border: none; background: none; font-size: 1.25rem; color: #64748B; cursor: pointer;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 1.5rem;">
            <form id="form-new-dispatch" onsubmit="saveNewDispatch(event)">
                <!-- Segmented Bill Type Toggle -->
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="erp-field-label" style="margin-bottom: 0.5rem;">Consignment Type <span class="text-danger">*</span></label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <label id="lbl-bill-type-with" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 0.95rem; border: 2px solid #2563EB; border-radius: 8px; background: #EFF6FF; cursor: pointer; font-weight: 600; font-size: 0.86rem; color: #1D4ED8; transition: all 0.2s;">
                            <input type="radio" name="modal_bill_type" id="modal-bill-type-with" value="with_bill" checked onchange="setModalBillType('with_bill')" style="accent-color: #2563EB;">
                            <span><i class="fa-solid fa-file-invoice me-1"></i> With Bill (Sales Invoice)</span>
                        </label>
                        <label id="lbl-bill-type-without" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 0.95rem; border: 2px solid #E2E8F0; border-radius: 8px; background: #F8FAFC; cursor: pointer; font-weight: 600; font-size: 0.86rem; color: #475569; transition: all 0.2s;">
                            <input type="radio" name="modal_bill_type" id="modal-bill-type-without" value="without_bill" onchange="setModalBillType('without_bill')" style="accent-color: #D97706;">
                            <span><i class="fa-solid fa-scale-unbalanced me-1"></i> Without Bill (WB Slip)</span>
                        </label>
                    </div>
                </div>

                <!-- Quick Select From Pending Database Records -->
                <div class="form-group" style="margin-bottom: 1.25rem;">
                    <label class="erp-field-label">Quick Pick Recorded Entry (Optional)</label>
                    <div class="erp-field-icon-wrap">
                        <i class="fa-solid fa-list-check erp-field-icon"></i>
                        <select id="modal-dsp-quick-select" class="form-control erp-field-input-iconified" onchange="handleQuickSelectRecord(this.value)">
                            <option value="">-- Or enter custom consignment details below --</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <!-- Order / Slip No -->
                    <div class="form-group">
                        <label class="erp-field-label" id="lbl-modal-order-no">Order / Invoice No <span class="text-danger">*</span></label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hashtag erp-field-icon"></i>
                            <input type="text" id="modal-dsp-order-no" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" placeholder="Enter Order or Invoice No" required>
                        </div>
                    </div>

                    <!-- Dispatch Date -->
                    <div class="form-group">
                        <label class="erp-field-label">Dispatch Date <span class="text-danger">*</span></label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                            <input type="date" id="modal-dsp-date" class="form-control erp-field-input-iconified" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Customer / Consignee Firm (Col-Span-2) -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="erp-field-label">Customer / Buyer Firm <span class="text-danger">*</span></label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-user-tie erp-field-icon"></i>
                            <select id="modal-dsp-customer" class="form-control erp-field-input-iconified" required>
                                <option value="">Select Customer / Consignee...</option>
                                @if(isset($customers) && $customers->count() > 0)
                                    @foreach($customers as $cst)
                                        <option value="{{ $cst->name }}">{{ $cst->name }} ({{ $cst->code }}{{ $cst->city ? ' - ' . $cst->city : '' }})</option>
                                    @endforeach
                                @else
                                    <option value="Raj Traders">Raj Traders (Sojat City)</option>
                                    <option value="Sharma Cosmetics">Sharma Cosmetics (Ahmedabad)</option>
                                    <option value="Marwar Spices Trader">Marwar Spices Trader (Jodhpur)</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Vehicle No -->
                    <div class="form-group">
                        <label class="erp-field-label">Vehicle Registration No <span class="text-danger">*</span></label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                            <input type="text" id="modal-dsp-vehicle" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" placeholder="Enter vehicle registration number" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                    </div>

                    <!-- Driver Name -->
                    <div class="form-group">
                        <label class="erp-field-label">Driver Name</label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-id-card erp-field-icon"></i>
                            <input type="text" id="modal-dsp-driver" class="form-control erp-field-input-iconified" placeholder="Enter driver name">
                        </div>
                    </div>

                    <!-- Driver Mobile -->
                    <div class="form-group">
                        <label class="erp-field-label">Driver Mobile No</label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-phone erp-field-icon"></i>
                            <input type="text" id="modal-dsp-driver-phone" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter 10-digit mobile number">
                        </div>
                    </div>

                    <!-- Transporter / Carrier -->
                    <div class="form-group">
                        <label class="erp-field-label">Transporter / Logistics</label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-boxes-packing erp-field-icon"></i>
                            <input type="text" id="modal-dsp-transporter" class="form-control erp-field-input-iconified" placeholder="Enter transporter company">
                        </div>
                    </div>

                    <!-- Consignment Notes (Col-Span-2) -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="erp-field-label">Consignment / Gate-Pass Remarks</label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-comment-dots erp-field-icon"></i>
                            <input type="text" id="modal-dsp-notes" class="form-control erp-field-input-iconified" placeholder="Enter dispatch remarks, weight or delivery destination">
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #F1F5F9;">
                    <button type="button" class="btn btn-outline" onclick="closeNewDispatchModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.4rem;">
                        <i class="fa-solid fa-check me-1"></i> Save &amp; Dispatch Consignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: View Dispatch Dossier Details -->
<div class="modal-overlay" id="modal-view-dispatch-details" style="display: none;">
    <div class="modal-card" style="max-width: 620px; width: 95%;">
        <div class="modal-header" style="border-bottom: 1px solid #E2E8F0; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #1E293B;">Consignment Dispatch Dossier</h3>
                    <p style="margin: 0; font-size: 0.78rem; color: #64748B;">Official outward gate-pass &amp; transport slip summary</p>
                </div>
            </div>
            <button type="button" class="btn-close" onclick="closeViewDispatchModal()" style="border: none; background: none; font-size: 1.25rem; color: #64748B; cursor: pointer;">&times;</button>
        </div>

        <div class="modal-body" style="padding: 1.5rem;" id="dossier-body-content">
            <!-- Dynamic Dossier Populated in JS -->
        </div>

        <div class="modal-footer" style="padding: 1rem 1.5rem; border-top: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center;">
            <button type="button" class="btn btn-outline" onclick="closeViewDispatchModal()">Close</button>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-outline" id="btn-dossier-toggle-delivered" onclick="toggleDeliveredCurrent()">
                    <i class="fa-solid fa-check-double me-1"></i> Mark Delivered
                </button>
                <button type="button" class="btn btn-primary" onclick="printCurrentDossier()">
                    <i class="fa-solid fa-print me-1"></i> Print Delivery Challan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // In-Memory & LocalStorage Dispatches Sync Store
    let allDispatchesList = [];
    let currentSelectedDispatchId = null;

    // Server-Provided Seed Data from Database (With Bill Sales + Without Bill WB Sales)
    const serverSalesDispatches = @json($sales ?? []);
    const serverWBSalesDispatches = @json($wbSales ?? []);

    function initOrderDispatchModule() {
        // 1. Fetch from localStorage first
        let localData = [];
        try {
            if (typeof db !== 'undefined' && typeof db.getAll === 'function') {
                localData = db.getAll('DISPATCHES') || [];
            } else {
                const raw = localStorage.getItem('vu_dispatches');
                if (raw) localData = JSON.parse(raw);
            }
        } catch(e) {
            console.warn('Error reading local dispatches:', e);
        }

        // 2. Normalize existing entries: assign billType if missing
        if (Array.isArray(localData) && localData.length > 0) {
            localData.forEach(d => {
                if (!d.billType) {
                    const no = (d.orderNo || '').toUpperCase();
                    d.billType = (no.includes('WB') || no.includes('SLIP') || no.includes('WEIGH')) ? 'without_bill' : 'with_bill';
                }
            });
        }

        const hasWithoutBill = Array.isArray(localData) && localData.some(d => d.billType === 'without_bill');

        // 3. Populate initial data if empty
        if (!localData || localData.length === 0) {
            localData = [];

            // Add server Sales (With Bill)
            if (serverSalesDispatches && serverSalesDispatches.length > 0) {
                serverSalesDispatches.forEach(s => {
                    localData.push({
                        id: 'DSP-SO-' + s.id,
                        billType: 'with_bill',
                        orderNo: s.sale_no || ('SO-' + s.id),
                        customerName: s.customer ? s.customer.name : 'Direct Buyer',
                        date: s.sale_date ? s.sale_date.split('T')[0] : new Date().toISOString().split('T')[0],
                        vehicleNo: s.vehicle_no || 'RJ-19-GB-8842',
                        driverName: s.driver_name || 'Mohan Lal',
                        driverPhone: s.driver_phone || '9829012345',
                        transporter: s.transporter || 'Vikas Logistics Cargo',
                        status: s.status === 'completed' ? 'Delivered' : (s.status === 'dispatched' ? 'Dispatched' : 'In Transit'),
                        notes: s.notes || 'Official tax invoice outward consignment'
                    });
                });
            }

            // Add server WB Sales (Without Bill)
            if (serverWBSalesDispatches && serverWBSalesDispatches.length > 0) {
                serverWBSalesDispatches.forEach(w => {
                    localData.push({
                        id: 'DSP-WB-' + w.id,
                        billType: 'without_bill',
                        orderNo: w.slip_no || ('WB-OUT-' + w.id),
                        customerName: w.customer ? w.customer.name : 'Direct Consignee',
                        date: w.entry_date ? w.entry_date.split('T')[0] : new Date().toISOString().split('T')[0],
                        vehicleNo: w.vehicle_no || 'RJ-19-TR-4411',
                        driverName: w.driver_name || 'Kailash Meena',
                        driverPhone: w.driver_phone || '9414122334',
                        transporter: w.transporter || 'Direct Mandi Freight',
                        status: w.status === 'completed' ? 'Delivered' : 'Dispatched',
                        notes: w.notes || `Weighbridge outward slip (Net Wt: ${w.net_weight || 0} KG)`
                    });
                });
            }

            // If still empty (e.g. database fresh install), supply rich initial records showcasing both With Bill & Without Bill
            if (localData.length === 0) {
                localData = [
                    {
                        id: 'DSP-501',
                        billType: 'with_bill',
                        orderNo: 'SO-1024',
                        customerName: 'Raj Traders',
                        date: '2026-09-08',
                        vehicleNo: 'RJ-19-GA-4521',
                        driverName: 'Mohan Lal',
                        driverPhone: '9829012345',
                        transporter: 'Vikas Logistics',
                        status: 'Dispatched',
                        notes: 'Henna powder bulk consignment - Prompt delivery'
                    },
                    {
                        id: 'DSP-502',
                        billType: 'without_bill',
                        orderNo: 'WB-OUT-901',
                        customerName: 'Sharma Cosmetics',
                        date: '2026-09-08',
                        vehicleNo: 'RJ-22-AA-9988',
                        driverName: 'Ramesh Singh',
                        driverPhone: '9414198765',
                        transporter: 'Marwar Freight Carriers',
                        status: 'In Transit',
                        notes: 'Weighbridge outward consignment - Net Wt: 250 KG'
                    },
                    {
                        id: 'DSP-503',
                        billType: 'with_bill',
                        orderNo: 'SO-1025',
                        customerName: 'Marwar Spices Trader',
                        date: '2026-09-05',
                        vehicleNo: 'RJ-19-GB-7711',
                        driverName: 'Kailash Meena',
                        driverPhone: '9829988221',
                        transporter: 'Shree Karni Transport',
                        status: 'Delivered',
                        notes: 'Whole senna leaves lot delivery verified'
                    },
                    {
                        id: 'DSP-504',
                        billType: 'without_bill',
                        orderNo: 'WB-OUT-902',
                        customerName: 'Raj Traders',
                        date: '2026-09-07',
                        vehicleNo: 'RJ-19-TR-3321',
                        driverName: 'Sohan Ram',
                        driverPhone: '9829033445',
                        transporter: 'Direct Mandi Truck',
                        status: 'Dispatched',
                        notes: 'Without bill mandi outward - Amla powder 150 KG'
                    }
                ];
            }

            persistDispatches(localData);
        } else if (!hasWithoutBill) {
            // LocalStorage existed but lacked "Without Bill" dispatches:
            if (serverWBSalesDispatches && serverWBSalesDispatches.length > 0) {
                serverWBSalesDispatches.forEach(w => {
                    localData.push({
                        id: 'DSP-WB-' + w.id,
                        billType: 'without_bill',
                        orderNo: w.slip_no || ('WB-OUT-' + w.id),
                        customerName: w.customer ? w.customer.name : 'Direct Consignee',
                        date: w.entry_date ? w.entry_date.split('T')[0] : new Date().toISOString().split('T')[0],
                        vehicleNo: w.vehicle_no || 'RJ-19-TR-4411',
                        driverName: w.driver_name || 'Kailash Meena',
                        driverPhone: w.driver_phone || '9414122334',
                        transporter: w.transporter || 'Direct Mandi Freight',
                        status: w.status === 'completed' ? 'Delivered' : 'Dispatched',
                        notes: w.notes || `Weighbridge outward slip (Net Wt: ${w.net_weight || 0} KG)`
                    });
                });
            } else {
                localData.push(
                    {
                        id: 'DSP-WB-DEMO-1',
                        billType: 'without_bill',
                        orderNo: 'WB-OUT-901',
                        customerName: 'Sharma Cosmetics',
                        date: '2026-09-08',
                        vehicleNo: 'RJ-22-AA-9988',
                        driverName: 'Ramesh Singh',
                        driverPhone: '9414198765',
                        transporter: 'Marwar Freight Carriers',
                        status: 'In Transit',
                        notes: 'Weighbridge outward consignment - Net Wt: 250 KG'
                    },
                    {
                        id: 'DSP-WB-DEMO-2',
                        billType: 'without_bill',
                        orderNo: 'WB-OUT-902',
                        customerName: 'Raj Traders',
                        date: '2026-09-07',
                        vehicleNo: 'RJ-19-TR-3321',
                        driverName: 'Sohan Ram',
                        driverPhone: '9829033445',
                        transporter: 'Direct Mandi Truck',
                        status: 'Dispatched',
                        notes: 'Without bill mandi outward - Amla powder 150 KG'
                    }
                );
            }
            persistDispatches(localData);
        }

        allDispatchesList = localData;

        // Populate Transporter dropdown & modal records
        populateTransporterFilter();
        populateModalQuickSelect('with_bill');

        // Render table & KPI metrics
        renderEnhancedOrderDispatch();
    }

    function populateTransporterFilter() {
        const select = document.getElementById('dsp-filter-transporter');
        if (!select) return;

        const transporters = [...new Set(allDispatchesList.map(d => d.transporter).filter(Boolean))];
        let opts = '<option value="">All Transporters</option>';
        transporters.forEach(t => {
            opts += `<option value="${escapeHtml(t)}">${escapeHtml(t)}</option>`;
        });
        select.innerHTML = opts;
    }

    function populateModalQuickSelect(activeBillType = 'with_bill') {
        const select = document.getElementById('modal-dsp-quick-select');
        if (!select) return;

        let opts = '<option value="">-- Or enter custom consignment details below --</option>';

        if (activeBillType === 'without_bill') {
            if (serverWBSalesDispatches && serverWBSalesDispatches.length > 0) {
                opts += '<optgroup label="Recorded WB Sales (Without Bill)">';
                serverWBSalesDispatches.forEach(w => {
                    const cName = w.customer ? w.customer.name : 'Mandi Consignee';
                    opts += `<option value="WB_${w.id}" data-type="without_bill" data-no="${escapeHtml(w.slip_no)}" data-cust="${escapeHtml(cName)}" data-veh="${escapeHtml(w.vehicle_no || '')}" data-driver="${escapeHtml(w.driver_name || '')}" data-phone="${escapeHtml(w.driver_phone || '')}">
                        ${escapeHtml(w.slip_no)} - ${escapeHtml(cName)} (${w.vehicle_no || 'No Vehicle'})
                    </option>`;
                });
                opts += '</optgroup>';
            }
        } else {
            if (serverSalesDispatches && serverSalesDispatches.length > 0) {
                opts += '<optgroup label="Recorded Sales Invoices (With Bill)">';
                serverSalesDispatches.forEach(s => {
                    const cName = s.customer ? s.customer.name : 'Buyer Firm';
                    opts += `<option value="SO_${s.id}" data-type="with_bill" data-no="${escapeHtml(s.sale_no)}" data-cust="${escapeHtml(cName)}" data-veh="${escapeHtml(s.vehicle_no || '')}" data-driver="${escapeHtml(s.driver_name || '')}" data-phone="${escapeHtml(s.driver_phone || '')}">
                        ${escapeHtml(s.sale_no)} - ${escapeHtml(cName)} (${s.vehicle_no || 'No Vehicle'})
                    </option>`;
                });
                opts += '</optgroup>';
            }
        }

        select.innerHTML = opts;
    }

    function handleQuickSelectRecord(val) {
        if (!val) return;
        const select = document.getElementById('modal-dsp-quick-select');
        const opt = select.options[select.selectedIndex];
        if (!opt) return;

        const orderNo = opt.getAttribute('data-no');
        const customer = opt.getAttribute('data-cust');
        const vehicle = opt.getAttribute('data-veh');
        const driver = opt.getAttribute('data-driver');
        const phone = opt.getAttribute('data-phone');

        if (orderNo) document.getElementById('modal-dsp-order-no').value = orderNo;
        if (customer) {
            const custSelect = document.getElementById('modal-dsp-customer');
            let found = false;
            for (let i = 0; i < custSelect.options.length; i++) {
                if (custSelect.options[i].value === customer) {
                    custSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found && customer) {
                const newOpt = new Option(customer, customer, true, true);
                custSelect.add(newOpt);
            }
        }
        if (vehicle) document.getElementById('modal-dsp-vehicle').value = vehicle;
        if (driver) document.getElementById('modal-dsp-driver').value = driver;
        if (phone) document.getElementById('modal-dsp-driver-phone').value = phone;
    }

    function renderEnhancedOrderDispatch(filteredList = null) {
        const list = filteredList !== null ? filteredList : allDispatchesList;
        const tbody = document.getElementById('dispatch-table-body');
        const emptyState = document.getElementById('dsp-empty-state');
        if (!tbody) return;

        // Update Summary & KPI counters
        updateKpiCounters();

        document.getElementById('dsp-count-shown').textContent = list.length;
        document.getElementById('dsp-count-total').textContent = allDispatchesList.length;

        if (list.length === 0) {
            tbody.innerHTML = '';
            if (emptyState) emptyState.style.display = 'block';
            return;
        }

        if (emptyState) emptyState.style.display = 'none';

        let html = '';
        list.forEach((d, idx) => {
            const rawStatus = (d.status || 'Dispatched').trim();
            const statusLower = rawStatus.toLowerCase();

            let statusClass = 'dsp-status-dispatched';
            let statusLabel = 'Dispatched';

            if (statusLower.includes('transit')) {
                statusClass = 'dsp-status-intransit';
                statusLabel = 'In Transit';
            } else if (statusLower.includes('deliver')) {
                statusClass = 'dsp-status-delivered';
                statusLabel = 'Delivered';
            } else if (statusLower.includes('pending')) {
                statusClass = 'dsp-status-pending';
                statusLabel = 'Pending';
            }

            const isWithoutBill = (d.billType === 'without_bill');
            const customerName = d.customerName || 'Direct Consignee';
            const initials = getCustomerInitials(customerName);
            const orderNo = d.orderNo || (isWithoutBill ? ('WB-' + (idx + 1)) : ('SO-' + (idx + 1)));
            const vehicleNo = d.vehicleNo || 'N/A';
            const driverName = d.driverName || 'Self / Direct';
            const driverPhone = d.driverPhone || '';
            const transporter = d.transporter || (isWithoutBill ? 'Direct Mandi Truck' : 'Direct Delivery');
            const dateStr = formatDate(d.date);
            const dispatchId = d.id || orderNo;

            // Bill Type Badge
            const billTypeBadge = isWithoutBill
                ? `<span class="badge" style="background: #FEF3C7; color: #92400E; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid #FDE68A; display: inline-flex; align-items: center; gap: 5px;">
                     <i class="fa-solid fa-scale-unbalanced" style="color: #D97706;"></i> Without Bill
                   </span>`
                : `<span class="badge" style="background: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid #BFDBFE; display: inline-flex; align-items: center; gap: 5px;">
                     <i class="fa-solid fa-file-invoice" style="color: #2563EB;"></i> With Bill
                   </span>`;

            // Order Badge
            const orderBadge = isWithoutBill
                ? `<span class="badge font-monospace" style="background: #FFFBEB; color: #92400E; font-weight: 700; font-size: 0.82rem; padding: 5px 9px; border-radius: 6px; border: 1px solid #FDE68A;">
                     <i class="fa-solid fa-weight-scale me-1 text-muted"></i>${escapeHtml(orderNo)}
                   </span>`
                : `<span class="badge font-monospace" style="background: #F1F5F9; color: #1E293B; font-weight: 700; font-size: 0.82rem; padding: 5px 9px; border-radius: 6px; border: 1px solid #CBD5E1;">
                     <i class="fa-solid fa-hashtag me-1 text-muted"></i>${escapeHtml(orderNo)}
                   </span>`;

            html += `
                <tr>
                    <td style="padding-left: 1.5rem;">
                        ${orderBadge}
                    </td>
                    <td>
                        ${billTypeBadge}
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div class="avatar-circle-olive">
                                ${initials}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #1E293B;">${escapeHtml(customerName)}</div>
                                <div style="font-size: 0.75rem; color: #64748B;">${isWithoutBill ? 'Mandi Consignee' : 'Registered Consignee'}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #334155; font-size: 0.86rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-regular fa-calendar-check text-muted"></i>
                            <span>${dateStr}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge font-monospace" style="background: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 0.82rem; padding: 5px 10px; border-radius: 6px; border: 1px solid #BFDBFE;">
                            <i class="fa-solid fa-truck-moving me-1" style="color: #3B82F6;"></i>${escapeHtml(vehicleNo)}
                        </span>
                    </td>
                    <td>
                        <div>
                            <div style="font-weight: 600; color: #1E293B; font-size: 0.86rem;">
                                <i class="fa-solid fa-id-card text-muted me-1"></i>${escapeHtml(driverName)}
                            </div>
                            ${driverPhone ? `
                                <div style="font-size: 0.75rem; color: #64748B;" class="font-monospace">
                                    <i class="fa-solid fa-phone text-muted me-1"></i>${escapeHtml(driverPhone)}
                                </div>
                            ` : ''}
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 500; color: #334155; font-size: 0.86rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-boxes-packing text-muted"></i>
                            <span>${escapeHtml(transporter)}</span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span class="dsp-status-pill ${statusClass}">
                            <span class="dsp-status-dot"></span>
                            <span>${statusLabel}</span>
                        </span>
                    </td>
                    <td style="text-align: right; padding-right: 1.5rem;">
                        <div class="erp-actions-cell" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.35rem;">
                            <button type="button" class="btn btn-icon btn-sm" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569;" onclick="viewDispatchDossier('${escapeHtml(dispatchId)}')" title="View Dispatch Slip Details">
                                <i class="fa-solid fa-eye" style="font-size: 0.85rem;"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #5B841E;" onclick="printWaybillDirect('${escapeHtml(dispatchId)}')" title="Print Delivery Challan">
                                <i class="fa-solid fa-print" style="font-size: 0.85rem;"></i>
                            </button>
                            <button type="button" class="btn btn-icon btn-sm text-danger" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #FEE2E2; background: #FEF2F2; color: #DC2626;" onclick="deleteDispatchEntry('${escapeHtml(dispatchId)}')" title="Delete Dispatch Entry">
                                <i class="fa-solid fa-trash-can" style="font-size: 0.85rem;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function updateKpiCounters() {
        const total = allDispatchesList.length;
        const withBillCount = allDispatchesList.filter(d => (d.billType || 'with_bill') === 'with_bill').length;
        const withoutBillCount = allDispatchesList.filter(d => d.billType === 'without_bill').length;
        const inTransit = allDispatchesList.filter(d => (d.status || '').toLowerCase().includes('transit')).length;

        const elTotal = document.getElementById('kpi-total-dispatches');
        const elWith = document.getElementById('kpi-with-bill');
        const elWithout = document.getElementById('kpi-without-bill');
        const elTransit = document.getElementById('kpi-in-transit');

        if (elTotal) elTotal.textContent = total;
        if (elWith) elWith.textContent = withBillCount;
        if (elWithout) elWithout.textContent = withoutBillCount;
        if (elTransit) elTransit.textContent = inTransit;
    }

    function filterDispatches() {
        const search = (document.getElementById('dsp-search-input')?.value || '').toLowerCase().trim();
        const billType = (document.getElementById('dsp-filter-bill-type')?.value || '').toLowerCase().trim();
        const status = (document.getElementById('dsp-filter-status')?.value || '').toLowerCase().trim();
        const transporter = (document.getElementById('dsp-filter-transporter')?.value || '').toLowerCase().trim();

        const filtered = allDispatchesList.filter(d => {
            const matchSearch = !search ||
                (d.orderNo && d.orderNo.toLowerCase().includes(search)) ||
                (d.customerName && d.customerName.toLowerCase().includes(search)) ||
                (d.vehicleNo && d.vehicleNo.toLowerCase().includes(search)) ||
                (d.driverName && d.driverName.toLowerCase().includes(search)) ||
                (d.transporter && d.transporter.toLowerCase().includes(search));

            const curBillType = (d.billType || 'with_bill').toLowerCase();
            const matchBillType = !billType || curBillType === billType;

            const matchStatus = !status || (d.status && d.status.toLowerCase().includes(status));
            const matchTransporter = !transporter || (d.transporter && d.transporter.toLowerCase() === transporter);

            return matchSearch && matchBillType && matchStatus && matchTransporter;
        });

        renderEnhancedOrderDispatch(filtered);
    }

    function resetDispatchFilters() {
        if (document.getElementById('dsp-search-input')) document.getElementById('dsp-search-input').value = '';
        if (document.getElementById('dsp-filter-bill-type')) document.getElementById('dsp-filter-bill-type').value = '';
        if (document.getElementById('dsp-filter-status')) document.getElementById('dsp-filter-status').value = '';
        if (document.getElementById('dsp-filter-transporter')) document.getElementById('dsp-filter-transporter').value = '';
        renderEnhancedOrderDispatch(allDispatchesList);
    }

    function getCustomerInitials(name) {
        if (!name) return 'VU';
        const parts = name.trim().split(/\s+/);
        if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }

    function formatDate(raw) {
        if (!raw) return 'Today';
        try {
            const dt = new Date(raw);
            if (isNaN(dt.getTime())) return raw;
            return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        } catch(e) {
            return raw;
        }
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, function(m) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }

    // Modal Control: Create Dispatch
    function openNewDispatchModal(defaultType = 'with_bill') {
        setModalBillType(defaultType);
        document.getElementById('modal-new-dispatch').style.display = 'flex';
    }

    function setModalBillType(type) {
        const isWithout = (type === 'without_bill');
        const radioWith = document.getElementById('modal-bill-type-with');
        const radioWithout = document.getElementById('modal-bill-type-without');
        const lblWith = document.getElementById('lbl-bill-type-with');
        const lblWithout = document.getElementById('lbl-bill-type-without');
        const orderNoLabel = document.getElementById('lbl-modal-order-no');
        const orderNoInput = document.getElementById('modal-dsp-order-no');
        const heading = document.getElementById('modal-dispatch-heading');

        if (radioWith && radioWithout) {
            radioWith.checked = !isWithout;
            radioWithout.checked = isWithout;
        }

        if (lblWith && lblWithout) {
            if (isWithout) {
                lblWithout.style.border = '2px solid #D97706';
                lblWithout.style.background = '#FEF3C7';
                lblWithout.style.color = '#92400E';
                lblWith.style.border = '2px solid #E2E8F0';
                lblWith.style.background = '#F8FAFC';
                lblWith.style.color = '#475569';
                if (heading) heading.textContent = 'New Without Bill Consignment Dispatch';
            } else {
                lblWith.style.border = '2px solid #2563EB';
                lblWith.style.background = '#EFF6FF';
                lblWith.style.color = '#1D4ED8';
                lblWithout.style.border = '2px solid #E2E8F0';
                lblWithout.style.background = '#F8FAFC';
                lblWithout.style.color = '#475569';
                if (heading) heading.textContent = 'New With Bill Consignment Dispatch';
            }
        }

        if (orderNoLabel) {
            orderNoLabel.innerHTML = isWithout
                ? 'WB Outward Slip No <span class="text-danger">*</span>'
                : 'Order / Invoice No <span class="text-danger">*</span>';
        }

        if (orderNoInput) {
            const nextSeq = allDispatchesList.length + 1;
            orderNoInput.placeholder = isWithout ? 'Enter WB Outward Slip No' : 'Enter Order or Invoice No';
            orderNoInput.value = isWithout ? ('WB-OUT-' + (900 + nextSeq)) : ('SO-' + (1024 + nextSeq));
        }

        populateModalQuickSelect(type);
    }

    function closeNewDispatchModal() {
        document.getElementById('modal-new-dispatch').style.display = 'none';
        document.getElementById('form-new-dispatch').reset();
    }

    function saveNewDispatch(e) {
        e.preventDefault();
        const billType = document.querySelector('input[name="modal_bill_type"]:checked')?.value || 'with_bill';
        const orderNo = document.getElementById('modal-dsp-order-no').value.trim();
        const customerName = document.getElementById('modal-dsp-customer').value.trim();
        const date = document.getElementById('modal-dsp-date').value || new Date().toISOString().split('T')[0];
        const vehicleNo = document.getElementById('modal-dsp-vehicle').value.trim().toUpperCase();
        const driverName = document.getElementById('modal-dsp-driver').value.trim() || 'Direct Driver';
        const driverPhone = document.getElementById('modal-dsp-driver-phone').value.trim();
        const transporter = document.getElementById('modal-dsp-transporter').value.trim() || (billType === 'without_bill' ? 'Direct Mandi Truck' : 'Vikas Logistics');
        const notes = document.getElementById('modal-dsp-notes').value.trim();

        const newRecord = {
            id: 'DSP-' + (billType === 'without_bill' ? 'WB-' : 'SO-') + Date.now(),
            billType,
            orderNo,
            customerName,
            date,
            vehicleNo,
            driverName,
            driverPhone,
            transporter,
            status: 'Dispatched',
            notes: notes || (billType === 'without_bill' ? 'Without bill outward consignment (WB Slip)' : 'Official outward consignment dispatch')
        };

        allDispatchesList.unshift(newRecord);
        persistDispatches();
        populateTransporterFilter();
        renderEnhancedOrderDispatch();

        closeNewDispatchModal();

        const msgType = (billType === 'without_bill') ? 'Without Bill Outward Slip' : 'Sales Order Dispatch';
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: `${msgType} Dispatched!`,
                text: `${orderNo} dispatched successfully with vehicle ${vehicleNo}.`,
                confirmButtonColor: '#5B841E',
                timer: 2500
            });
        } else if (typeof toastr !== 'undefined') {
            toastr.success(`${orderNo} dispatched successfully!`);
        }
    }

    // Modal Control: View Dossier
    function viewDispatchDossier(id) {
        currentSelectedDispatchId = id;
        const d = allDispatchesList.find(item => item.id == id || item.orderNo == id);
        if (!d) return;

        const content = document.getElementById('dossier-body-content');
        const rawStatus = (d.status || 'Dispatched').trim();
        const statusLower = rawStatus.toLowerCase();
        let statusBadge = '<span class="badge" style="background: #DCFCE7; color: #15803D; font-weight: 700; padding: 4px 10px; border-radius: 20px;">Dispatched</span>';
        if (statusLower.includes('transit')) {
            statusBadge = '<span class="badge" style="background: #FEF3C7; color: #B45309; font-weight: 700; padding: 4px 10px; border-radius: 20px;">In Transit</span>';
        } else if (statusLower.includes('deliver')) {
            statusBadge = '<span class="badge" style="background: #E0E7FF; color: #3730A3; font-weight: 700; padding: 4px 10px; border-radius: 20px;">Delivered</span>';
        }

        const isWithout = (d.billType === 'without_bill');
        const billBadge = isWithout
            ? '<span class="badge" style="background: #FEF3C7; color: #92400E; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px; border: 1px solid #FDE68A;"><i class="fa-solid fa-scale-unbalanced me-1"></i>Without Bill (WB Outward)</span>'
            : '<span class="badge" style="background: #EFF6FF; color: #1D4ED8; font-weight: 700; font-size: 0.8rem; padding: 4px 10px; border-radius: 6px; border: 1px solid #BFDBFE;"><i class="fa-solid fa-file-invoice me-1"></i>With Bill (Sales Invoice)</span>';

        content.innerHTML = `
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="badge font-monospace" style="background: #E2E8F0; color: #1E293B; font-weight: 700; font-size: 0.85rem; padding: 4px 8px; border-radius: 6px;">
                                ${escapeHtml(d.orderNo)}
                            </span>
                            ${billBadge}
                        </div>
                        <h4 style="margin: 0.5rem 0 0; font-weight: 700; color: #1E293B; font-size: 1.1rem;">${escapeHtml(d.customerName)}</h4>
                    </div>
                    <div>${statusBadge}</div>
                </div>
                <div style="font-size: 0.82rem; color: #64748B;">
                    <i class="fa-solid fa-location-dot text-danger me-1"></i> ${isWithout ? 'Weighbridge Outward Gate-Pass' : 'Official Sales Consignment Dispatch Gate-Pass'}
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.86rem; margin-bottom: 1.25rem;">
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px; padding: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748B; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-truck text-primary me-1"></i> Vehicle &amp; Logistics
                    </div>
                    <div style="margin-bottom: 0.4rem;"><strong>Vehicle No:</strong> <span class="font-monospace text-primary font-weight-700">${escapeHtml(d.vehicleNo || 'N/A')}</span></div>
                    <div><strong>Transporter:</strong> ${escapeHtml(d.transporter || 'Direct Delivery')}</div>
                </div>

                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px; padding: 1rem;">
                    <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748B; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-user-gear text-primary me-1"></i> Driver &amp; Contact
                    </div>
                    <div style="margin-bottom: 0.4rem;"><strong>Driver:</strong> ${escapeHtml(d.driverName || 'Self / Direct')}</div>
                    <div><strong>Mobile:</strong> <span class="font-monospace">${escapeHtml(d.driverPhone || 'Not Provided')}</span></div>
                </div>
            </div>

            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px; padding: 1rem; font-size: 0.86rem;">
                <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #64748B; margin-bottom: 0.4rem; letter-spacing: 0.05em;">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Dispatch Observations / Remarks
                </div>
                <div style="color: #334155;">
                    ${escapeHtml(d.notes || 'Outward goods verified and dispatched under standard quality protocol.')}
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.78rem; color: #64748B;">
                    <strong>Dispatch Date:</strong> ${formatDate(d.date)}
                </div>
            </div>
        `;

        document.getElementById('modal-view-dispatch-details').style.display = 'flex';
    }

    function closeViewDispatchModal() {
        document.getElementById('modal-view-dispatch-details').style.display = 'none';
        currentSelectedDispatchId = null;
    }

    function toggleDeliveredCurrent() {
        if (!currentSelectedDispatchId) return;
        const d = allDispatchesList.find(item => item.id == currentSelectedDispatchId || item.orderNo == currentSelectedDispatchId);
        if (d) {
            d.status = 'Delivered';
            persistDispatches();
            renderEnhancedOrderDispatch();
            closeViewDispatchModal();
            if (typeof toastr !== 'undefined') toastr.success(`Order ${d.orderNo} marked as Delivered!`);
        }
    }

    function printCurrentDossier() {
        if (!currentSelectedDispatchId) return;
        printWaybillDirect(currentSelectedDispatchId);
    }

    function printWaybillDirect(id) {
        const d = allDispatchesList.find(item => item.id == id || item.orderNo == id);
        if (!d) return;

        const isWithout = (d.billType === 'without_bill');
        const printTitle = isWithout ? 'WITHOUT BILL (WEIGHBRIDGE OUTWARD GATE-PASS)' : 'WITH BILL (TAX INVOICE DELIVERY CHALLAN)';

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Delivery Challan - ${escapeHtml(d.orderNo)}</title>
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 2rem; color: #1E293B; line-height: 1.5; }
                        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #5B841E; padding-bottom: 1rem; margin-bottom: 1.5rem; }
                        .title { font-size: 1.6rem; font-weight: 800; color: #5B841E; margin: 0; }
                        .subtitle { font-size: 0.9rem; font-weight: 700; color: ${isWithout ? '#D97706' : '#2563EB'}; margin-top: 3px; }
                        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
                        .card { border: 1px solid #CBD5E1; border-radius: 8px; padding: 1rem; }
                        .badge { display: inline-block; padding: 4px 8px; background: #F1F5F9; border-radius: 4px; font-family: monospace; font-weight: bold; }
                        .footer { margin-top: 3rem; display: flex; justify-content: space-between; padding-top: 1rem; border-top: 1px dashed #CBD5E1; font-size: 0.85rem; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <div>
                            <div class="title">VIKAS UDHYOG</div>
                            <div class="subtitle">${printTitle}</div>
                            <div style="font-size: 0.82rem; color: #64748B;">Sojat City, Pali, Rajasthan - 306104</div>
                        </div>
                        <div style="text-align: right;">
                            <div>Challan / Slip No: <span class="badge">${escapeHtml(d.orderNo)}</span></div>
                            <div>Date: <strong>${escapeHtml(d.date)}</strong></div>
                            <div>Status: <strong>${escapeHtml(d.status || 'Dispatched')}</strong></div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="card">
                            <h4 style="margin: 0 0 0.5rem; color: #5B841E;">CONSIGNEE DETAILS</h4>
                            <div><strong>Firm Name:</strong> ${escapeHtml(d.customerName)}</div>
                            <div><strong>Type:</strong> ${isWithout ? 'Mandi Consignee (Without Bill)' : 'Registered Buyer (With Bill)'}</div>
                            <div><strong>Destination:</strong> Sojat / Inter-State Transit</div>
                        </div>
                        <div class="card">
                            <h4 style="margin: 0 0 0.5rem; color: #5B841E;">TRANSPORT LOGISTICS</h4>
                            <div><strong>Vehicle No:</strong> <span class="badge">${escapeHtml(d.vehicleNo || 'N/A')}</span></div>
                            <div><strong>Driver:</strong> ${escapeHtml(d.driverName || 'Direct')} (${escapeHtml(d.driverPhone || 'N/A')})</div>
                            <div><strong>Transporter:</strong> ${escapeHtml(d.transporter || 'Direct Cargo')}</div>
                        </div>
                    </div>

                    <div class="card" style="margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 0.5rem; color: #5B841E;">DISPATCH OBSERVATIONS</h4>
                        <div>${escapeHtml(d.notes || 'Consignment verified and released through security dispatch gate.')}</div>
                    </div>

                    <div class="footer">
                        <div>Prepared By: ____________________</div>
                        <div>Driver Signature: ____________________</div>
                        <div>Gatekeeper / Authorized Signatory: ____________________</div>
                    </div>
                    <script>
                        window.onload = function() { window.print(); }
                    <\/script>
                </body>
            </html>
        `);
        printWindow.document.close();
    }

    function deleteDispatchEntry(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Dispatch Entry?',
                text: 'Are you sure you want to remove this dispatch record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    performDeleteDispatch(id);
                }
            });
        } else {
            if (confirm('Are you sure you want to delete this dispatch entry?')) {
                performDeleteDispatch(id);
            }
        }
    }

    function performDeleteDispatch(id) {
        allDispatchesList = allDispatchesList.filter(item => item.id != id && item.orderNo != id);
        persistDispatches();
        populateTransporterFilter();
        renderEnhancedOrderDispatch();

        if (typeof toastr !== 'undefined') toastr.success('Dispatch record deleted successfully.');
    }

    function persistDispatches(data = null) {
        const listToSave = data || allDispatchesList;
        try {
            if (typeof db !== 'undefined' && typeof db.set === 'function') {
                db.set('vu_dispatches', listToSave);
            } else {
                localStorage.setItem('vu_dispatches', JSON.stringify(listToSave));
            }
        } catch(e) {}
    }

    // Assign globally so external modules or legacy calls route cleanly
    window.renderEnhancedOrderDispatch = renderEnhancedOrderDispatch;
    window.openNewDispatchModal = openNewDispatchModal;
    window.closeNewDispatchModal = closeNewDispatchModal;
    window.setModalBillType = setModalBillType;
    window.viewDispatchDossier = viewDispatchDossier;
    window.closeViewDispatchModal = closeViewDispatchModal;
    window.toggleDeliveredCurrent = toggleDeliveredCurrent;
    window.printCurrentDossier = printCurrentDossier;
    window.printWaybillDirect = printWaybillDirect;
    window.deleteDispatchEntry = deleteDispatchEntry;
    window.filterDispatches = filterDispatches;
    window.resetDispatchFilters = resetDispatchFilters;

    // Override global Sales.renderOrderDispatch so legacy calls seamlessly use enhanced renderer
    if (typeof Sales !== 'undefined') {
        Sales.renderOrderDispatch = function() {
            initOrderDispatchModule();
        };
    }

    // Initialize on DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        initOrderDispatchModule();
    });
</script>
@endpush
@endsection
