@extends('admin.layouts.app')

@section('title', 'Edit Purchase Entry ' . $purchase->purchase_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<section class="view-section active" id="view-purchase-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.purchase-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.purchase-entry') }}">Purchase Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit #{{ $purchase->purchase_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Purchase Voucher #{{ $purchase->purchase_no }}
            </h1>
            <p class="erp-page-subtitle">
                Modify vendor billing coordinates, rates, and stock inward items with live audit reconciliation.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.purchase-entry.show', $purchase) }}" class="btn btn-outline">
                <i class="fa-solid fa-eye"></i> View Voucher Dossier
            </a>
            <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchases
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
    <form action="{{ route('admin.transactions.purchase-entry.update', $purchase) }}" method="POST" id="purchase-form">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-2col">
            <!-- Left Main Column -->
            <div class="erp-form-main-col">

                <!-- 1. Purchase Voucher & Supplier Identity -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Purchase Voucher &amp; Supplier Identity</h3>
                                <p class="erp-form-section-desc">Supplier invoice coordinates, transport vehicle &amp; vendor profile</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Voucher No (Readonly) -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Voucher No <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                <input type="text" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" value="{{ $purchase->purchase_no }}" readonly>
                            </div>
                            <span class="erp-field-hint">Fixed inward purchase voucher reference</span>
                        </div>

                        <!-- Invoice Date -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Invoice Date <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                <input type="date" name="invoice_date" id="field-invoice-date" class="form-control erp-field-input-iconified" value="{{ old('invoice_date', $purchase->invoice_date->format('Y-m-d')) }}" required onchange="updateLiveSummary()">
                            </div>
                            <span class="erp-field-hint">Billing or arrival consignment date</span>
                        </div>

                        <!-- Supplier Invoice No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Supplier Bill / Invoice No
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                                <input type="text" name="invoice_no" id="field-invoice-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter invoice number" value="{{ old('invoice_no', $purchase->invoice_no) }}" oninput="updateLiveSummary()">
                            </div>
                            <span class="erp-field-hint">Vendor's original printed bill number</span>
                        </div>

                        <!-- Vendor / Supplier Select -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Vendor / Supplier Firm <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-truck-field erp-field-icon"></i>
                                <select name="vendor_id" id="field-vendor-id" class="form-control erp-field-input-iconified" required onchange="onVendorChange(this)">
                                    @foreach($vendors as $vnd)
                                        <option value="{{ $vnd->id }}" data-name="{{ $vnd->name }}" data-city="{{ $vnd->city }}" data-gstin="{{ $vnd->gstin }}" {{ old('vendor_id', $purchase->vendor_id) == $vnd->id ? 'selected' : '' }}>
                                            {{ $vnd->name }} ({{ $vnd->code }}{{ $vnd->city ? ' - ' . $vnd->city : '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Creditor account whose ledger balance will be adjusted</span>
                        </div>

                        <!-- Broker / Agent Select -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Broker / Mandi Commission Agent <span class="text-muted">(Optional)</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-handshake erp-field-icon"></i>
                                <select name="broker_id" id="field-broker-id" class="form-control erp-field-input-iconified" onchange="updateLiveSummary()">
                                    <option value="">Direct Purchase (No Broker)...</option>
                                    @foreach($brokers as $brk)
                                        <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" data-comm="{{ $brk->commission_rate }}" {{ old('broker_id', $purchase->broker_id) == $brk->id ? 'selected' : '' }}>
                                            {{ $brk->name }} ({{ $brk->city ?? 'Sojat' }} - {{ $brk->commission_rate }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Commission tracking agent</span>
                        </div>

                        <!-- Order Urgency / Type -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Order Classification / Urgency <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-tag erp-field-icon"></i>
                                <select name="order_type" id="field-order-type" class="form-control erp-field-input-iconified" required onchange="updateLiveSummary()">
                                    <option value="Medium" {{ old('order_type', $purchase->order_type) === 'Medium' ? 'selected' : '' }}>Medium (Standard Processing)</option>
                                    <option value="Urgent" {{ old('order_type', $purchase->order_type) === 'Urgent' ? 'selected' : '' }}>Urgent Consignment</option>
                                    <option value="Fast" {{ old('order_type', $purchase->order_type) === 'Fast' ? 'selected' : '' }}>Fast Track</option>
                                    <option value="Ready Delivery" {{ old('order_type', $purchase->order_type) === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Operational delivery tag</span>
                        </div>

                        <!-- Vehicle No -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Vehicle / Transport No
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                                <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vehicle registration number" value="{{ old('vehicle_no', $purchase->vehicle_no) }}" oninput="this.value = this.value.toUpperCase(); updateLiveSummary();">
                            </div>
                            <span class="erp-field-hint">Truck or transport registration number</span>
                        </div>

                        <!-- Payment Terms -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Payment Terms
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-clock erp-field-icon"></i>
                                <select name="payment_terms" class="form-control erp-field-input-iconified">
                                    <option value="Cash" {{ old('payment_terms', $purchase->payment_terms) === 'Cash' ? 'selected' : '' }}>Cash on Delivery (Immediate)</option>
                                    <option value="15 Days" {{ old('payment_terms', $purchase->payment_terms) === '15 Days' ? 'selected' : '' }}>Credit 15 Days</option>
                                    <option value="30 Days" {{ old('payment_terms', $purchase->payment_terms) === '30 Days' ? 'selected' : '' }}>Credit 30 Days</option>
                                    <option value="45 Days" {{ old('payment_terms', $purchase->payment_terms) === '45 Days' ? 'selected' : '' }}>Credit 45 Days</option>
                                    <option value="Bank Transfer" {{ old('payment_terms', $purchase->payment_terms) === 'Bank Transfer' ? 'selected' : '' }}>Bank RTGS / NEFT</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Agreed credit repayment cycle</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Inward Product Line Items (Matching Ledger Layout) -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-success">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Inward Product Line Items</h3>
                                <p class="erp-form-section-desc">Raw materials, herbs, packaging with dual-rate official &amp; under-billing calculation</p>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="addPurchaseRow()">
                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                        </button>
                    </div>

                    <div class="erp-form-section-body p-0" style="padding: 0 !important;">
                        <div class="erp-items-table-wrapper" style="border: none; border-radius: 0;">
                            <table class="erp-items-table" id="items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45px; text-align: center;">S.NO</th>
                                        <th style="min-width: 220px;">ITEM <span class="text-danger">*</span></th>
                                        <th style="width: 90px;">HSN</th>
                                        <th style="width: 80px; text-align: center;">GST</th>
                                        <th style="width: 110px;">UNIT TYPE <span class="text-danger">*</span></th>
                                        <th style="width: 110px; text-align: right;">NET WT <span class="text-danger">*</span></th>
                                        <th style="width: 110px; text-align: right;">BILL RATE (₹) <span class="text-danger">*</span></th>
                                        <th style="width: 110px; text-align: right;">U-B RATE (₹)</th>
                                        <th style="width: 120px; text-align: right;">BILL ARNT (₹)</th>
                                        <th style="width: 120px; text-align: right;">U-B ARNT (₹)</th>
                                        <th style="width: 45px; text-align: center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-items-body">
                                    @php
                                        $editItems = old('items');
                                    @endphp
                                    @if($editItems && is_array($editItems))
                                        @foreach($editItems as $idx => $row)
                                            <tr class="item-row">
                                                <td class="text-center row-sno font-weight-600 font-monospace text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    <select name="items[{{ $idx }}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
                                                        <option value="">Select Item...</option>
                                                        @foreach($items as $itm)
                                                            <option value="{{ $itm->id }}"
                                                                    data-code="{{ $itm->code }}"
                                                                    data-hsn="{{ $itm->hsn_code }}"
                                                                    data-unit="{{ $itm->unit }}"
                                                                    data-gst="{{ $itm->gst_rate }}"
                                                                    data-purchase-rate="{{ $itm->purchase_rate }}"
                                                                    data-batch="{{ $itm->batch_no }}"
                                                                    {{ ($row['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                                                                {{ $itm->name }} ({{ $itm->code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $idx }}][batch_no]" class="row-batch" value="{{ $row['batch_no'] ?? '' }}">
                                                    <input type="hidden" name="items[{{ $idx }}][actual_rate]" class="row-actual" value="{{ $row['actual_rate'] ?? '0.00' }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="HSN" value="{{ $row['hsn_code'] ?? '' }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][gst_percent]" class="form-control row-gst font-monospace" value="{{ number_format((float)($row['gst_percent'] ?? 5), 2, '.', '') }}" oninput="calcRow(this)">
                                                </td>
                                                <td>
                                                    <select name="items[{{ $idx }}][unit]" class="form-select row-unit" required>
                                                        @php $rowUnit = strtoupper($row['unit'] ?? 'KG'); @endphp
                                                        <option value="KG" {{ $rowUnit === 'KG' ? 'selected' : '' }}>KG</option>
                                                        <option value="BOX" {{ $rowUnit === 'BOX' ? 'selected' : '' }}>BOX</option>
                                                        <option value="BAG" {{ $rowUnit === 'BAG' ? 'selected' : '' }}>BAG</option>
                                                        <option value="QUINTAL" {{ $rowUnit === 'QUINTAL' ? 'selected' : '' }}>QUINTAL</option>
                                                        <option value="TON" {{ $rowUnit === 'TON' ? 'selected' : '' }}>TON</option>
                                                        <option value="PCS" {{ $rowUnit === 'PCS' ? 'selected' : '' }}>PCS</option>
                                                        @foreach($units as $u)
                                                            @if(!in_array(strtoupper($u->name), ['KG', 'BOX', 'BAG', 'QUINTAL', 'TON', 'PCS']))
                                                                <option value="{{ $u->name }}" {{ $rowUnit === strtoupper($u->name) ? 'selected' : '' }}>{{ $u->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0.001" name="items[{{ $idx }}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="{{ $row['quantity'] ?? '1' }}" required oninput="calcRow(this)">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][bill_rate]" class="form-control row-bill-rate font-monospace" style="text-align: right;" value="{{ number_format((float)($row['bill_rate'] ?? 0), 2, '.', '') }}" required oninput="calcRow(this)">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][ub_rate]" class="form-control row-ub-rate font-monospace" style="text-align: right;" value="{{ number_format((float)($row['ub_rate'] ?? 0), 2, '.', '') }}" oninput="calcRow(this)">
                                                </td>
                                                <td style="text-align: right;">
                                                    @php
                                                        $rQty = (float)($row['quantity'] ?? 0);
                                                        $rBill = (float)($row['bill_rate'] ?? 0);
                                                        $rUb = (float)($row['ub_rate'] ?? 0);
                                                    @endphp
                                                    <span class="font-monospace text-dark font-weight-600 row-bill-amt">₹{{ number_format($rQty * $rBill, 2) }}</span>
                                                </td>
                                                <td style="text-align: right;">
                                                    <span class="font-monospace font-weight-600 row-ub-amt" style="color: #D97706;">₹{{ number_format($rQty * $rUb, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="delete-row-btn" onclick="removeRow(this)" title="Remove line item">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach($purchase->items as $idx => $lineItem)
                                            <tr class="item-row">
                                                <td class="text-center row-sno font-weight-600 font-monospace text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    <select name="items[{{ $idx }}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
                                                        <option value="">Select Item...</option>
                                                        @foreach($items as $itm)
                                                            <option value="{{ $itm->id }}"
                                                                    data-code="{{ $itm->code }}"
                                                                    data-hsn="{{ $itm->hsn_code }}"
                                                                    data-unit="{{ $itm->unit }}"
                                                                    data-gst="{{ $itm->gst_rate }}"
                                                                    data-purchase-rate="{{ $itm->purchase_rate }}"
                                                                    data-batch="{{ $itm->batch_no }}"
                                                                    {{ $lineItem->item_id == $itm->id ? 'selected' : '' }}>
                                                                {{ $itm->name }} ({{ $itm->code }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" name="items[{{ $idx }}][batch_no]" class="row-batch" value="{{ $lineItem->batch_no }}">
                                                    <input type="hidden" name="items[{{ $idx }}][actual_rate]" class="row-actual" value="{{ $lineItem->actual_rate }}">
                                                </td>
                                                <td>
                                                    <input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="HSN" value="{{ $lineItem->hsn_code }}">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][gst_percent]" class="form-control row-gst font-monospace" value="{{ number_format($lineItem->gst_percent, 2, '.', '') }}" oninput="calcRow(this)">
                                                </td>
                                                <td>
                                                    <select name="items[{{ $idx }}][unit]" class="form-select row-unit" required>
                                                        <option value="KG" {{ strtoupper($lineItem->unit) === 'KG' ? 'selected' : '' }}>KG</option>
                                                        <option value="BOX" {{ strtoupper($lineItem->unit) === 'BOX' ? 'selected' : '' }}>BOX</option>
                                                        <option value="BAG" {{ strtoupper($lineItem->unit) === 'BAG' ? 'selected' : '' }}>BAG</option>
                                                        <option value="QUINTAL" {{ strtoupper($lineItem->unit) === 'QUINTAL' ? 'selected' : '' }}>QUINTAL</option>
                                                        <option value="TON" {{ strtoupper($lineItem->unit) === 'TON' ? 'selected' : '' }}>TON</option>
                                                        <option value="PCS" {{ strtoupper($lineItem->unit) === 'PCS' ? 'selected' : '' }}>PCS</option>
                                                        @foreach($units as $u)
                                                            @if(!in_array(strtoupper($u->name), ['KG', 'BOX', 'BAG', 'QUINTAL', 'TON', 'PCS']))
                                                                <option value="{{ $u->name }}" {{ strtoupper($lineItem->unit) === strtoupper($u->name) ? 'selected' : '' }}>{{ $u->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="any" min="0.001" name="items[{{ $idx }}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="{{ $lineItem->quantity }}" required oninput="calcRow(this)">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][bill_rate]" class="form-control row-bill-rate font-monospace" style="text-align: right;" value="{{ number_format($lineItem->bill_rate, 2, '.', '') }}" required oninput="calcRow(this)">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0" name="items[{{ $idx }}][ub_rate]" class="form-control row-ub-rate font-monospace" style="text-align: right;" value="{{ number_format($lineItem->ub_rate, 2, '.', '') }}" oninput="calcRow(this)">
                                                </td>
                                                <td style="text-align: right;">
                                                    <span class="font-monospace text-dark font-weight-600 row-bill-amt">₹{{ number_format($lineItem->quantity * $lineItem->bill_rate, 2) }}</span>
                                                </td>
                                                <td style="text-align: right;">
                                                    <span class="font-monospace font-weight-600 row-ub-amt" style="color: #D97706;">₹{{ number_format($lineItem->under_amount, 2) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="delete-row-btn" onclick="removeRow(this)" title="Remove line item">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Bar to Add Rows -->
                        <div style="padding: 0.75rem 1.25rem; background: #FFFFFF; border-top: 1px dashed #CBD5E1; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                            <button type="button" class="btn btn-outline" style="border-radius: 8px; font-size: 0.84rem; font-weight: 600; padding: 0.45rem 1.15rem; border-color: #5B841E; color: #5B841E;" onclick="addPurchaseRow()">
                                <i class="fa-solid fa-plus me-1"></i> Add Another Item Row
                            </button>
                            <span style="font-size: 0.78rem; color: #64748B;">
                                <i class="fa-solid fa-circle-check text-success me-1"></i> Live compute: Bill Arnt = Net Wt &times; Bill Rate &bull; U-B Arnt = Net Wt &times; U-B Rate
                            </span>
                        </div>

                        <!-- Real-Time Totals Bar Beneath Table -->
                        <div class="erp-table-totals-bar">
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Total Net Wt</span>
                                <span class="erp-table-total-val font-monospace" id="footer-total-qty">0.000</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Bill Subtotal</span>
                                <span class="erp-table-total-val font-monospace" id="footer-bill-subtotal">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">GST Tax</span>
                                <span class="erp-table-total-val font-monospace text-muted" id="footer-tax-total">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label" style="color: #D97706;">Total U-B Amount</span>
                                <span class="erp-table-total-val font-monospace" style="color: #D97706;" id="footer-ub-total">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label" style="color: #059669;">Total Grand Value</span>
                                <span class="erp-table-total-val font-monospace" style="color: #059669; font-size: 1.15rem;" id="footer-grand-total">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Settlement & Operational Notes -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-purple">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Settlement &amp; Consignment Notes</h3>
                                <p class="erp-form-section-desc">Immediate settlement, payment recording &amp; factory inward observations</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Paid Amount -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Paid Amount (₹)
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="field-paid-amount" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter amount paid" value="{{ old('paid_amount', $purchase->paid_amount) }}" oninput="updateLiveSummary()">
                            </div>
                            <span class="erp-field-hint">Initial advance or immediate cash settlement</span>
                        </div>

                        <!-- Payment Status -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Settlement Status
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-wallet erp-field-icon"></i>
                                <select name="payment_status" id="field-payment-status" class="form-control erp-field-input-iconified" onchange="updateLiveSummary()">
                                    <option value="unpaid" {{ old('payment_status', $purchase->payment_status) === 'unpaid' ? 'selected' : '' }}>Unpaid / On Credit</option>
                                    <option value="partial" {{ old('payment_status', $purchase->payment_status) === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                                    <option value="paid" {{ old('payment_status', $purchase->payment_status) === 'paid' ? 'selected' : '' }}>Fully Settled</option>
                                </select>
                            </div>
                            <span class="erp-field-hint">Accounts payable status</span>
                        </div>

                        <!-- Consignment Notes -->
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Consignment Remarks / Inward Notes
                            </label>
                            <textarea name="notes" rows="3" class="form-control" style="border-radius: 8px; font-size: 0.88rem;" placeholder="Enter any gate pass observations, moisture check, lot condition, or mandi remarks...">{{ old('notes', $purchase->notes) }}</textarea>
                            <span class="erp-field-hint">Internal warehouse &amp; procurement observations</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-sidebar-col">

                <!-- 1. Real-Time Live Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        <div class="erp-preview-avatar" id="prev-avatar">
                            {{ strtoupper(substr($purchase->vendor->name ?? 'PE', 0, 2)) }}
                        </div>
                        <div>
                            <div class="erp-preview-title" id="prev-title">{{ $purchase->vendor->name ?? 'Purchase Voucher' }}</div>
                            <div class="erp-preview-subtitle font-monospace" id="prev-code">{{ $purchase->purchase_no }}</div>
                        </div>
                    </div>

                    <div class="erp-preview-badge-row">
                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px;" id="prev-order-type">
                            {{ $purchase->order_type }}
                        </span>
                        <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px;" id="prev-item-count">
                            {{ count($purchase->items) }} Item(s)
                        </span>
                    </div>

                    <div style="margin: 1.25rem 0 1rem; padding: 1rem; background: #F8FAFC; border-radius: 10px; border: 1px solid #E2E8F0; text-align: center;">
                        <div style="font-size: 0.73rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em; color: #64748B; margin-bottom: 0.25rem;">
                            Grand Total Inward Value
                        </div>
                        <div class="font-monospace" style="font-size: 1.6rem; font-weight: 800; color: #059669;" id="prev-grand-total">
                            ₹{{ number_format($purchase->grand_total, 2) }}
                        </div>
                        <div style="font-size: 0.74rem; color: #64748B; margin-top: 0.2rem;" id="prev-balance-text">
                            Vendor balance will be updated to this amount
                        </div>
                    </div>

                    <div class="erp-preview-meta-list">
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Supplier Firm</span>
                            <span class="erp-preview-meta-val" id="prev-vendor">{{ $purchase->vendor->name ?? '—' }}</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Invoice Ref</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-inv-ref">{{ $purchase->invoice_no ?: '—' }}</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Total Net Weight</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-net-wt">{{ number_format($purchase->items->sum('quantity'), 3) }}</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Bill Subtotal</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-bill-sub">₹{{ number_format($purchase->subtotal, 2) }}</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">GST Tax Amount</span>
                            <span class="erp-preview-meta-val font-monospace" id="prev-tax-amt">₹{{ number_format($purchase->tax_amount, 2) }}</span>
                        </div>
                        <div class="erp-preview-meta-item">
                            <span class="erp-preview-meta-key">Official Bill Total</span>
                            <span class="erp-preview-meta-val font-monospace" style="font-weight: 700; color: #2563EB;" id="prev-bill-total">₹{{ number_format($purchase->bill_total, 2) }}</span>
                        </div>
                        <div class="erp-preview-meta-item" style="border-top: 1px dashed #CBD5E1; padding-top: 0.6rem;">
                            <span class="erp-preview-meta-key" style="color: #D97706;">Under-Billing (U-B)</span>
                            <span class="erp-preview-meta-val font-monospace" style="color: #D97706; font-weight: 700;" id="prev-ub-total">₹{{ number_format($purchase->under_billing_total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Audit Trail Timestamps Card -->
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.45rem;">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #3B82F6;"></i> Voucher Audit Trail
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Voucher ID:</span>
                        <strong class="font-monospace text-dark">#{{ $purchase->id }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Registered On:</span>
                        <strong class="text-dark">{{ $purchase->created_at->format('d M Y, h:i A') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Last Modified:</span>
                        <strong class="text-dark">{{ $purchase->updated_at->format('d M Y, h:i A') }}</strong>
                    </div>
                </div>

                <!-- 3. Sidebar Action Buttons -->
                <div class="card erp-sidebar-actions-card">
                    <button type="submit" class="btn btn-primary erp-btn-action-submit" id="btn-save-purchase">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Purchase Voucher
                    </button>
                    <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline erp-btn-action-cancel">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel Changes
                    </a>
                </div>

                <!-- 4. Danger Zone Card -->
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #FECACA; background: #FFF5F5;">
                    <div style="font-weight: 700; color: #B91C1C; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                    </div>
                    <p style="font-size: 0.78rem; color: #7F1D1D; margin-bottom: 0.85rem;">
                        Deleting this voucher will revert inventory stock additions and reverse supplier ledger credit.
                    </p>
                    <button type="button" class="btn btn-outline" style="border-color: #F87171; color: #DC2626; width: 100%; font-size: 0.82rem; font-weight: 600;" onclick="confirmDeleteVoucher()">
                        <i class="fa-regular fa-trash-can me-1"></i> Delete Purchase Voucher
                    </button>
                </div>

            </div>
        </div>
    </form>
</section>

<!-- Delete Purchase Hidden Form -->
<form id="delete-voucher-form" action="{{ route('admin.transactions.purchase-entry.destroy', $purchase) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Item Dropdown Options Template (Hidden) -->
<template id="item-options-template">
    <option value="">Select Item...</option>
    @foreach($items as $itm)
        <option value="{{ $itm->id }}"
                data-code="{{ $itm->code }}"
                data-hsn="{{ $itm->hsn_code }}"
                data-unit="{{ $itm->unit }}"
                data-gst="{{ $itm->gst_rate }}"
                data-purchase-rate="{{ $itm->purchase_rate }}"
                data-batch="{{ $itm->batch_no }}">
            {{ $itm->name }} ({{ $itm->code }})
        </option>
    @endforeach
</template>

<template id="unit-options-template">
    <option value="KG">KG</option>
    <option value="BOX">BOX</option>
    <option value="BAG">BAG</option>
    <option value="QUINTAL">QUINTAL</option>
    <option value="TON">TON</option>
    <option value="PCS">PCS</option>
    @foreach($units as $u)
        @if(!in_array(strtoupper($u->name), ['KG', 'BOX', 'BAG', 'QUINTAL', 'TON', 'PCS']))
            <option value="{{ $u->name }}">{{ $u->name }}</option>
        @endif
    @endforeach
</template>

@push('scripts')
<script>
    let rowIndex = {{ count($editItems ?? $purchase->items) }};

    function addPurchaseRow() {
        const tbody = document.getElementById('purchase-items-body');
        const itemOptions = document.getElementById('item-options-template').innerHTML;
        const unitOptions = document.getElementById('unit-options-template').innerHTML;

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-sno font-weight-600 font-monospace text-muted">${tbody.children.length + 1}</td>
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
                    ${itemOptions}
                </select>
                <input type="hidden" name="items[${rowIndex}][batch_no]" class="row-batch" value="">
                <input type="hidden" name="items[${rowIndex}][actual_rate]" class="row-actual" value="0.00">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="HSN" value="">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][gst_percent]" class="form-control row-gst font-monospace" value="5.00" oninput="calcRow(this)">
            </td>
            <td>
                <select name="items[${rowIndex}][unit]" class="form-select row-unit" required>
                    ${unitOptions}
                </select>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="items[${rowIndex}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="1" required oninput="calcRow(this)">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][bill_rate]" class="form-control row-bill-rate font-monospace" style="text-align: right;" value="0.00" required oninput="calcRow(this)">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][ub_rate]" class="form-control row-ub-rate font-monospace" style="text-align: right;" value="0.00" oninput="calcRow(this)">
            </td>
            <td style="text-align: right;">
                <span class="font-monospace text-dark font-weight-600 row-bill-amt">₹0.00</span>
            </td>
            <td style="text-align: right;">
                <span class="font-monospace font-weight-600 row-ub-amt" style="color: #D97706;">₹0.00</span>
            </td>
            <td class="text-center">
                <button type="button" class="delete-row-btn" onclick="removeRow(this)" title="Remove line item">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        updateRowNumbers();
        updateLiveSummary();
    }

    function removeRow(btn) {
        const tbody = document.getElementById('purchase-items-body');
        if (tbody.querySelectorAll('.item-row').length <= 1) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('At least one purchase item is required.');
            } else {
                alert('At least one purchase item is required.');
            }
            return;
        }
        btn.closest('tr').remove();
        updateRowNumbers();
        updateLiveSummary();
    }

    function updateRowNumbers() {
        document.querySelectorAll('#purchase-items-body .row-sno').forEach((el, idx) => {
            el.textContent = idx + 1;
        });
    }

    function onItemSelect(selectEl) {
        const row = selectEl.closest('tr');
        const selected = selectEl.selectedOptions[0];
        if (!selected || !selected.value) return;

        const hsn = selected.getAttribute('data-hsn') || '';
        const unit = selected.getAttribute('data-unit') || 'KG';
        const gst = parseFloat(selected.getAttribute('data-gst')) || 5.0;
        const purchaseRate = parseFloat(selected.getAttribute('data-purchase-rate')) || 0.0;
        const batch = selected.getAttribute('data-batch') || '';

        row.querySelector('.row-hsn').value = hsn;
        row.querySelector('.row-gst').value = gst.toFixed(2);
        row.querySelector('.row-batch').value = batch;

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

        const billRateInput = row.querySelector('.row-bill-rate');
        if (parseFloat(billRateInput.value) === 0 && purchaseRate > 0) {
            billRateInput.value = purchaseRate.toFixed(2);
        }

        calcRow(selectEl);
    }

    function calcRow(el) {
        const row = el.closest('tr');
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const billRate = parseFloat(row.querySelector('.row-bill-rate').value) || 0;
        const ubRate = parseFloat(row.querySelector('.row-ub-rate').value) || 0;
        const gstPercent = parseFloat(row.querySelector('.row-gst').value) || 0;

        const actualRate = billRate + ubRate;
        row.querySelector('.row-actual').value = actualRate.toFixed(2);

        const billAmt = qty * billRate;
        const ubAmt = qty * ubRate;

        row.querySelector('.row-bill-amt').textContent = '₹' + billAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        row.querySelector('.row-ub-amt').textContent = '₹' + ubAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        updateLiveSummary();
    }

    function onVendorChange(selectEl) {
        const selected = selectEl.selectedOptions[0];
        const prevTitle = document.getElementById('prev-title');
        const prevVendor = document.getElementById('prev-vendor');
        const prevAvatar = document.getElementById('prev-avatar');

        if (selected && selected.value) {
            const name = selected.getAttribute('data-name');
            prevTitle.textContent = name;
            prevVendor.textContent = name;
            prevAvatar.textContent = name.substring(0, 2).toUpperCase();
        } else {
            prevTitle.textContent = 'Purchase Voucher';
            prevVendor.textContent = 'Select Vendor...';
            prevAvatar.textContent = 'PE';
        }
        updateLiveSummary();
    }

    function updateLiveSummary() {
        let totalQty = 0;
        let totalBillSubtotal = 0;
        let totalTax = 0;
        let totalUB = 0;
        let itemCount = 0;

        document.querySelectorAll('#purchase-items-body .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
            const billRate = parseFloat(row.querySelector('.row-bill-rate').value) || 0;
            const ubRate = parseFloat(row.querySelector('.row-ub-rate').value) || 0;
            const gstPercent = parseFloat(row.querySelector('.row-gst').value) || 0;

            const lineBill = qty * billRate;
            const lineTax = lineBill * (gstPercent / 100);
            const lineUB = qty * ubRate;

            totalQty += qty;
            totalBillSubtotal += lineBill;
            totalTax += lineTax;
            totalUB += lineUB;
            itemCount++;
        });

        const totalBill = totalBillSubtotal + totalTax;
        const grandTotal = totalBill + totalUB;

        // Footer Totals
        document.getElementById('footer-total-qty').textContent = totalQty.toFixed(3);
        document.getElementById('footer-bill-subtotal').textContent = '₹' + totalBillSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-tax-total').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-ub-total').textContent = '₹' + totalUB.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Sidebar Live Preview
        document.getElementById('prev-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-net-wt').textContent = totalQty.toFixed(3);
        document.getElementById('prev-bill-sub').textContent = '₹' + totalBillSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-tax-amt').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-bill-total').textContent = '₹' + totalBill.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-ub-total').textContent = '₹' + totalUB.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-item-count').textContent = `${itemCount} Item(s)`;

        const invNo = document.getElementById('field-invoice-no').value.trim();
        document.getElementById('prev-inv-ref').textContent = invNo ? invNo : '—';

        const orderTypeEl = document.getElementById('field-order-type');
        if (orderTypeEl) {
            document.getElementById('prev-order-type').textContent = orderTypeEl.value;
        }
    }

    function confirmDeleteVoucher() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Purchase Voucher?',
                html: 'Are you sure you want to permanently delete purchase voucher <strong>#{{ $purchase->purchase_no }}</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">Inward inventory stock will be reverted and supplier balance adjusted.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Delete Voucher',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-voucher-form').submit();
                }
            });
        } else {
            if (confirm('Are you sure you want to delete purchase voucher #{{ $purchase->purchase_no }}? Inward stock will be reverted.')) {
                document.getElementById('delete-voucher-form').submit();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateLiveSummary();
    });
</script>
@endpush
@endsection
