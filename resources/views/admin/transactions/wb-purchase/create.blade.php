@extends('admin.layouts.app')

@section('title', 'New WB Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<section class="view-section active" id="view-wb-purchase-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-purchase-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-purchase-entry') }}">WB Purchase Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">New WB Entry</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-scale-balanced text-primary"></i> New WB Purchase Entry (Without Bill)
            </h1>
            <p class="erp-page-subtitle">
                Record direct mandi cash procurement, weighbridge gross/tare weighments, and moisture tare deductions.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to WB Slips
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(isset($errors) && $errors->any())
        <div class="alert erp-alert-danger">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-exclamation erp-alert-icon-danger"></i>
                <div>
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0 mt-1 ps-3" style="font-size: 0.85rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Form Container -->
    <form action="{{ route('admin.transactions.wb-purchase-entry.store') }}" method="POST" id="wb-purchase-form">
        @csrf

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Weighbridge Slip Identification & Supplier -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Weighbridge Slip Identification &amp; Supplier</h3>
                                <p class="erp-form-section-desc">Slip reference, procurement date, supplier coordinates &amp; vehicle details</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Slip No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                WB Slip No <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                <input type="text" name="slip_no" id="field-slip-no" class="form-control erp-field-input-iconified erp-field-input-mono" value="{{ old('slip_no', $nextSlipNo) }}" readonly required>
                            </div>
                            <span class="erp-field-hint">Auto-generated weighbridge slip identifier</span>
                        </div>

                        <!-- Entry Date -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Inward Entry Date <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                <input type="date" name="entry_date" id="field-entry-date" class="form-control erp-field-input-iconified" value="{{ old('entry_date', date('Y-m-d')) }}" required onchange="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Date truck arrived at factory weighbridge</span>
                        </div>

                        <!-- Vendor / Farmer Supplier Select -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Vendor / Mandi Farmer Supplier <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user-tag erp-field-icon"></i>
                                <select name="vendor_id" id="field-vendor-id" class="form-control erp-field-input-iconified" required onchange="onVendorChange(this)">
                                    <option value="">Select Supplier / Farmer...</option>
                                    @foreach($vendors as $vnd)
                                        <option value="{{ $vnd->id }}" data-name="{{ $vnd->name }}" data-city="{{ $vnd->city }}" {{ old('vendor_id') == $vnd->id ? 'selected' : '' }}>
                                            {{ $vnd->name }} ({{ $vnd->code }}{{ $vnd->city ? ' - ' . $vnd->city : '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Payable ledger recipient for this mandi slip</span>
                        </div>

                        <!-- Broker / Agent Select -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mandi Broker / Commission Agent <span class="text-muted">(Optional)</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-handshake erp-field-icon"></i>
                                <select name="broker_id" id="field-broker-id" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">Direct Mandi (No Broker)...</option>
                                    @foreach($brokers as $brk)
                                        <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" {{ old('broker_id') == $brk->id ? 'selected' : '' }}>
                                            {{ $brk->name }} ({{ $brk->city ?? 'Sojat' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Local sourcing broker</span>
                        </div>

                        <!-- Order Urgency / Type -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Order Classification / Priority <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-tag erp-field-icon"></i>
                                <select name="order_type" id="field-order-type" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    <option value="Medium" {{ old('order_type', 'Medium') === 'Medium' ? 'selected' : '' }}>Medium (Standard Processing)</option>
                                    <option value="Urgent" {{ old('order_type') === 'Urgent' ? 'selected' : '' }}>Urgent Mandi Lot</option>
                                    <option value="Fast" {{ old('order_type') === 'Fast' ? 'selected' : '' }}>Fast Track</option>
                                    <option value="Ready Delivery" {{ old('order_type') === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Inward lot processing urgency</span>
                        </div>

                        <!-- Vehicle No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Vehicle / Truck Registration No
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                                <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vehicle registration number" value="{{ old('vehicle_no') }}" oninput="this.value = this.value.toUpperCase(); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Truck number weighed on scale</span>
                        </div>

                        <!-- Driver Name -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Driver Name
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user erp-field-icon"></i>
                                <input type="text" name="driver_name" id="field-driver-name" class="form-control erp-field-input-iconified" placeholder="Enter driver name" value="{{ old('driver_name') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Name of driver presenting the consignment</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Weighbridge Scale Measurements -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-weight-scale"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Weighbridge Scale Measurements</h3>
                                <p class="erp-form-section-desc">Gross loaded scale weight, empty vehicle tare, and moisture deduction tare</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Gross Weight -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Gross Weight (KG) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-truck-ramp-box erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="gross_weight" id="field-gross-weight" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" style="text-align: right;" placeholder="0.00" value="{{ old('gross_weight', '0.00') }}" required oninput="calcWeighbridge()">
                            </div>
                            <span class="erp-field-hint">First weighment: Loaded truck weight</span>
                        </div>

                        <!-- Tare Weight -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Tare Weight (KG) <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-truck erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="tare_weight" id="field-tare-weight" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" style="text-align: right;" placeholder="0.00" value="{{ old('tare_weight', '0.00') }}" required oninput="calcWeighbridge()">
                            </div>
                            <span class="erp-field-hint">Second weighment: Empty vehicle tare</span>
                        </div>

                        <!-- Deduction / Moisture Tare -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Deduction / Bag Tare (KG)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-droplet-slash erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="deduction_weight" id="field-deduction-weight" class="form-control erp-field-input-iconified erp-field-input-mono" style="text-align: right;" placeholder="0.00" value="{{ old('deduction_weight', '0.00') }}" oninput="calcWeighbridge()">
                            </div>
                            <span class="erp-field-hint">Gunny bags, moisture loss, or dust deduction</span>
                        </div>

                        <!-- Net Weight (Calculated) -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Net Billable Weight (KG)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-scale-unbalanced erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="net_weight" id="field-net-weight" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" style="text-align: right; background: #F8FAFC; color: #059669;" value="{{ old('net_weight', '0.00') }}" readonly>
                            </div>
                            <span class="erp-field-hint">Formula: Gross - Tare - Deduction</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Inward Raw Materials Line Items -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-purple">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Inward Raw Materials &amp; Mandi Items</h3>
                                <p class="erp-form-section-desc">Henna leaves, herbs, and direct procurement lots at agreed mandi cash rates</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="addWBRow()">
                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                        </button>
                    </div>

                    <div class="erp-form-section-body p-0" style="padding: 0 !important;">
                        <div class="erp-items-table-wrapper" style="border: none; border-radius: 0;">
                            <table class="erp-items-table" id="wb-items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 38px; text-align: center;">S No</th>
                                        <th style="min-width: 220px;">Item Description <span class="text-danger">*</span></th>
                                        <th style="width: 100px;">Unit Type <span class="text-danger">*</span></th>
                                        <th style="width: 115px; text-align: right;">Net Qty <span class="text-danger">*</span></th>
                                        <th style="width: 115px; text-align: right;">Mandi Rate (₹) <span class="text-danger">*</span></th>
                                        <th style="width: 130px; text-align: right;">Line Total (₹)</th>
                                        <th style="min-width: 140px;">Remarks</th>
                                        <th style="width: 40px; text-align: center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="wb-items-body">
                                    <tr class="item-row">
                                        <td class="text-center row-sno font-weight-600 font-monospace text-muted">1</td>
                                        <td>
                                            <select name="items[0][item_id]" class="form-select erp-item-select" required onchange="onWBItemSelect(this)">
                                                <option value="">Select Raw Material / Herb...</option>
                                                @foreach($items as $itm)
                                                    <option value="{{ $itm->id }}"
                                                            data-code="{{ $itm->code }}"
                                                            data-unit="{{ $itm->unit }}"
                                                            data-purchase-rate="{{ $itm->purchase_rate }}">
                                                        {{ $itm->name }} ({{ $itm->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[0][batch_no]" class="row-batch" value="">
                                            <input type="hidden" name="items[0][hsn_code]" class="row-hsn" value="">
                                        </td>
                                        <td>
                                            <select name="items[0][unit]" class="form-select row-unit" required>
                                                <option value="KG">KG</option>
                                                <option value="BAG">BAG</option>
                                                <option value="QUINTAL">QUINTAL</option>
                                                <option value="TON">TON</option>
                                                <option value="BOX">BOX</option>
                                                @foreach($units as $u)
                                                    @if(!in_array(strtoupper($u->name), ['KG', 'BAG', 'QUINTAL', 'TON', 'BOX']))
                                                        <option value="{{ $u->name }}">{{ $u->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0.001" name="items[0][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="100" required oninput="calcWBRow(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[0][rate]" class="form-control row-rate font-monospace" style="text-align: right;" value="85.00" required oninput="calcWBRow(this)">
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace font-weight-700 text-dark row-amount">₹8,500.00</span>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][notes]" class="form-control" placeholder="Mandi lot note">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="delete-row-btn" onclick="removeWBRow(this)" title="Remove item">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Real-Time Totals Bar Beneath Table -->
                        <div class="erp-table-totals-bar">
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Total Inward Qty</span>
                                <span class="erp-table-total-val font-monospace" id="footer-wb-qty">100.000 KG</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label" style="color: #059669;">Grand Mandi Amount</span>
                                <span class="erp-table-total-val font-monospace" style="color: #059669; font-size: 1.2rem;" id="footer-wb-total">₹8,500.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Payment Settlement & Mandi Notes -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">4. Settlement Mode &amp; Mandi Notes</h3>
                                <p class="erp-form-section-desc">Mandi cash voucher details and observations</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Payment Mode -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Mandi Settlement Mode
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-money-bill-wave erp-field-icon"></i>
                                <select name="payment_mode" class="form-control erp-field-input-iconified">
                                    <option value="Cash" {{ old('payment_mode', 'Cash') === 'Cash' ? 'selected' : '' }}>Mandi Spot Cash</option>
                                    <option value="Bank Transfer" {{ old('payment_mode') === 'Bank Transfer' ? 'selected' : '' }}>Bank RTGS / IMPS</option>
                                    <option value="Mandi Slip" {{ old('payment_mode') === 'Mandi Slip' ? 'selected' : '' }}>Mandi Slip / Voucher</option>
                                    <option value="Cheque" {{ old('payment_mode') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Mode of payment release</span>
                        </div>

                        <!-- Settlement Status -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Settlement Status
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-wallet erp-field-icon"></i>
                                <select name="payment_status" class="form-control erp-field-input-iconified">
                                    <option value="unpaid" {{ old('payment_status', 'unpaid') === 'unpaid' ? 'selected' : '' }}>Unpaid / On Ledger</option>
                                    <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid Cash Instantly</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Payment status</span>
                        </div>

                        <!-- Consignment Notes -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Inward Inspection Remarks
                            </label>
                            <textarea name="notes" rows="3" class="form-control" style="border-radius: 8px; font-size: 0.88rem;" placeholder="Enter mandi slip notes, moisture percentage, lot cleanliness, or farmer remarks...">{{ old('notes') }}</textarea>
                            <span class="erp-field-hint">Internal warehouse &amp; weighbridge observations</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-sidebar-col">

                <!-- 1. Real-Time Live Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        <div class="erp-preview-avatar" id="prev-wb-avatar">
                            WB
                        </div>
                        <div>
                            <div class="erp-preview-title" id="prev-wb-title">New WB Entry Slip</div>
                            <div class="erp-preview-subtitle font-monospace" id="prev-wb-code">{{ old('slip_no', $nextSlipNo) }}</div>
                        </div>
                    </div>

                    <div class="erp-preview-badge-row">
                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px;" id="prev-wb-order-type">
                            Standard
                        </span>
                        <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px;" id="prev-wb-item-count">
                            1 Item(s)
                        </span>
                    </div>

                    <div style="margin: 1.25rem 0 1rem; padding: 1rem; background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; text-align: center;">
                        <div style="font-size: 0.73rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em; color: #64748B; margin-bottom: 0.25rem;">
                            Net Billable Scale Weight
                        </div>
                        <div class="font-monospace" style="font-size: 1.6rem; font-weight: 800; color: #059669;" id="prev-wb-net-wt">
                            0.00 KG
                        </div>
                        <div style="font-size: 0.74rem; color: #64748B; margin-top: 0.2rem;">
                            Gross - Tare - Deduction
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Supplier / Farmer</span>
                            <span class="erp-preview-meta-val" id="prev-wb-vendor">Select Supplier...</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Vehicle Registration</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-wb-veh">—</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Gross Scale Wt</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-wb-gross">0.00 KG</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Tare Wt</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-wb-tare">0.00 KG</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Moisture Tare</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-wb-ded">0.00 KG</span>
                        </div>
                        <div class="erp-preview-meta-item" style="border-top: 1px dashed #CBD5E1; padding-top: 0.6rem;">
                            <span class="erp-preview-meta-key" style="color: #D97706; font-weight: 700;">Total Mandi Value</span>
                            <span class="erp-preview-meta-val font-monospace" style="color: #D97706; font-weight: 800; font-size: 1.1rem;" id="prev-wb-amount">₹8,500.00</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Sidebar Action Buttons -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-action-submit" id="btn-save-wb">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save WB Purchase Slip
                    </button>
                    <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="btn btn-outline erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel &amp; Return
                    </a>
                </div>

                <!-- 3. Policy & Inward Rules -->
                <div class="card" style="padding: 1.15rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.8rem; color: #64748B;">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-circle-info" style="color: #5B841E;"></i> Weighbridge Inward Policy
                    </div>
                    <ul style="margin: 0; padding-left: 1.15rem; line-height: 1.55;">
                        <li>This entry records direct farm / mandi procurements without official tax bills.</li>
                        <li>Inventory stock is increased by the product quantities entered.</li>
                        <li>Status automatically defaults to <strong>Received</strong> upon submission.</li>
                    </ul>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Item Dropdown Options Template (Hidden) -->
<template id="wb-item-options-template">
    <option value="">Select Raw Material / Herb...</option>
    @foreach($items as $itm)
        <option value="{{ $itm->id }}"
                data-code="{{ $itm->code }}"
                data-unit="{{ $itm->unit }}"
                data-purchase-rate="{{ $itm->purchase_rate }}">
            {{ $itm->name }} ({{ $itm->code }})
        </option>
    @endforeach
</template>

<template id="wb-unit-options-template">
    <option value="KG">KG</option>
    <option value="BAG">BAG</option>
    <option value="QUINTAL">QUINTAL</option>
    <option value="TON">TON</option>
    <option value="BOX">BOX</option>
    @foreach($units as $u)
        @if(!in_array(strtoupper($u->name), ['KG', 'BAG', 'QUINTAL', 'TON', 'BOX']))
            <option value="{{ $u->name }}">{{ $u->name }}</option>
        @endif
    @endforeach
</template>

@push('scripts')
<script>
    let wbRowIndex = 1;

    function addWBRow() {
        const tbody = document.getElementById('wb-items-body');
        const itemOptions = document.getElementById('wb-item-options-template').innerHTML;
        const unitOptions = document.getElementById('wb-unit-options-template').innerHTML;

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-sno font-weight-600 font-monospace text-muted">${tbody.children.length + 1}</td>
            <td>
                <select name="items[${wbRowIndex}][item_id]" class="form-select erp-item-select" required onchange="onWBItemSelect(this)">
                    ${itemOptions}
                </select>
                <input type="hidden" name="items[${wbRowIndex}][batch_no]" class="row-batch" value="">
                <input type="hidden" name="items[${wbRowIndex}][hsn_code]" class="row-hsn" value="">
            </td>
            <td>
                <select name="items[${wbRowIndex}][unit]" class="form-select row-unit" required>
                    ${unitOptions}
                </select>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="items[${wbRowIndex}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="1" required oninput="calcWBRow(this)">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${wbRowIndex}][rate]" class="form-control row-rate font-monospace" style="text-align: right;" value="0.00" required oninput="calcWBRow(this)">
            </td>
            <td style="text-align: right;">
                <span class="font-monospace font-weight-700 text-dark row-amount">₹0.00</span>
            </td>
            <td>
                <input type="text" name="items[${wbRowIndex}][notes]" class="form-control" placeholder="Mandi lot note">
            </td>
            <td class="text-center">
                <button type="button" class="delete-row-btn" onclick="removeWBRow(this)" title="Remove item">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        wbRowIndex++;
        updateWBRowNumbers();
        calcWBTotals();
    }

    function removeWBRow(btn) {
        const tbody = document.getElementById('wb-items-body');
        if (tbody.querySelectorAll('.item-row').length <= 1) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('At least one line item is required.');
            } else {
                alert('At least one line item is required.');
            }
            return;
        }
        btn.closest('tr').remove();
        updateWBRowNumbers();
        calcWBTotals();
    }

    function updateWBRowNumbers() {
        document.querySelectorAll('#wb-items-body .row-sno').forEach((el, idx) => {
            el.textContent = idx + 1;
        });
    }

    function onWBItemSelect(selectEl) {
        const row = selectEl.closest('tr');
        const selected = selectEl.selectedOptions[0];
        if (!selected || !selected.value) return;

        const unit = selected.getAttribute('data-unit') || 'KG';
        const purchaseRate = parseFloat(selected.getAttribute('data-purchase-rate')) || 0.0;

        const unitSelect = row.querySelector('.row-unit');
        if (unitSelect) {
            let found = false;
            for (let i = 0; i < unitSelect.options.length; i++) {
                if (unitSelect.options[i].value.toUpperCase() === unit.toUpperCase()) {
                    unitSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found && unit) {
                const newOpt = new Option(unit, unit, true, true);
                unitSelect.add(newOpt);
            }
        }

        const rateInput = row.querySelector('.row-rate');
        if (parseFloat(rateInput.value) === 0 && purchaseRate > 0) {
            rateInput.value = purchaseRate.toFixed(2);
        }

        calcWBRow(selectEl);
    }

    function calcWBRow(el) {
        const row = el.closest('tr');
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;

        row.querySelector('.row-amount').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        calcWBTotals();
    }

    function calcWeighbridge() {
        const gross = parseFloat(document.getElementById('field-gross-weight').value) || 0;
        const tare = parseFloat(document.getElementById('field-tare-weight').value) || 0;
        const ded = parseFloat(document.getElementById('field-deduction-weight').value) || 0;

        const net = Math.max(0, gross - tare - ded);
        document.getElementById('field-net-weight').value = net.toFixed(2);

        document.getElementById('prev-wb-gross').textContent = gross.toFixed(2) + ' KG';
        document.getElementById('prev-wb-tare').textContent = tare.toFixed(2) + ' KG';
        document.getElementById('prev-wb-ded').textContent = ded.toFixed(2) + ' KG';
        document.getElementById('prev-wb-net-wt').textContent = net.toFixed(2) + ' KG';

        // Auto-sync first line item quantity if default 100 or 0
        const firstRowQty = document.querySelector('#wb-items-body .item-row .row-qty');
        if (firstRowQty && net > 0 && (parseFloat(firstRowQty.value) === 100 || parseFloat(firstRowQty.value) === 0)) {
            firstRowQty.value = net.toFixed(2);
            calcWBRow(firstRowQty);
        }

        updateLivePreview();
    }

    function calcWBTotals() {
        let totalQty = 0;
        let totalAmt = 0;
        let count = 0;

        document.querySelectorAll('#wb-items-body .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
            const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
            totalQty += qty;
            totalAmt += (qty * rate);
            count++;
        });

        document.getElementById('footer-wb-qty').textContent = totalQty.toFixed(3) + ' KG';
        document.getElementById('footer-wb-total').textContent = '₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('prev-wb-amount').textContent = '₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-wb-item-count').textContent = `${count} Item(s)`;
    }

    function onVendorChange(selectEl) {
        const selected = selectEl.selectedOptions[0];
        const prevTitle = document.getElementById('prev-wb-title');
        const prevVendor = document.getElementById('prev-wb-vendor');
        const prevAvatar = document.getElementById('prev-wb-avatar');

        if (selected && selected.value) {
            const name = selected.getAttribute('data-name');
            prevTitle.textContent = name;
            prevVendor.textContent = name;
            prevAvatar.textContent = name.substring(0, 2).toUpperCase();
        } else {
            prevTitle.textContent = 'New WB Entry Slip';
            prevVendor.textContent = 'Select Supplier...';
            prevAvatar.textContent = 'WB';
        }
        updateLivePreview();
    }

    function updateLivePreview() {
        const veh = document.getElementById('field-vehicle-no').value.trim();
        document.getElementById('prev-wb-veh').textContent = veh ? veh : '—';

        const orderType = document.getElementById('field-order-type');
        if (orderType) {
            document.getElementById('prev-wb-order-type').textContent = orderType.value;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        calcWeighbridge();
        calcWBTotals();
    });
</script>
@endpush
@endsection
