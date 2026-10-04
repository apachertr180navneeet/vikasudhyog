@extends('admin.layouts.app')

@section('title', 'New Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<section class="view-section active" id="view-purchase-create">
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
                <span class="erp-breadcrumb-active">New Purchase Entry</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-circle-plus text-primary"></i> New Purchase Entry (Stock Inward)
            </h1>
            <p class="erp-page-subtitle">
                Record raw materials, herbs &amp; packaging consignments with dual-rate official billing and under-billing calculations.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchase List
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
    <form action="{{ route('admin.transactions.purchase-entry.store') }}" method="POST" id="purchase-form">
        @csrf

        <div class="erp-form-layout-full">

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

                <div class="erp-form-section-body-4col">
                    <!-- Voucher No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Voucher No <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hashtag erp-field-icon"></i>
                            <input type="text" name="purchase_no" id="field-purchase-no" class="form-control erp-field-input-iconified erp-field-input-mono" value="{{ old('purchase_no', $nextPurchaseNo) }}" readonly required>
                        </div>
                        <span class="erp-field-hint">Auto-generated inward purchase reference</span>
                    </div>

                    <!-- Invoice Date -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Invoice Date <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                            <input type="date" name="invoice_date" id="field-invoice-date" class="form-control erp-field-input-iconified" value="{{ old('invoice_date', date('Y-m-d')) }}" required onchange="updateLiveSummary()">
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
                            <input type="text" name="invoice_no" id="field-invoice-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter invoice number" value="{{ old('invoice_no') }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Vendor's original printed bill number</span>
                    </div>

                    <!-- Vehicle No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Vehicle / Transport No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                            <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vehicle registration number" value="{{ old('vehicle_no') }}" oninput="this.value = this.value.toUpperCase(); updateLiveSummary();">
                        </div>
                        <span class="erp-field-hint">Truck or transport registration number</span>
                    </div>

                    <!-- Vendor / Supplier Select (Spans 2 Columns) -->
                    <div class="form-group erp-col-span-2">
                        <label class="erp-field-label">
                            Vendor / Supplier Firm <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck-field erp-field-icon"></i>
                            <select name="vendor_id" id="field-vendor-id" class="form-control erp-field-input-iconified" required onchange="onVendorChange(this)">
                                <option value="">Select Vendor / Supplier Firm...</option>
                                @foreach($vendors as $vnd)
                                    <option value="{{ $vnd->id }}" data-name="{{ $vnd->name }}" data-city="{{ $vnd->city }}" data-gstin="{{ $vnd->gstin }}" {{ old('vendor_id') == $vnd->id ? 'selected' : '' }}>
                                        {{ $vnd->name }} ({{ $vnd->code }}{{ $vnd->city ? ' - ' . $vnd->city : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="erp-field-hint">Creditor account whose ledger balance will be credited</span>
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
                                    <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" data-comm="{{ $brk->commission_rate }}" {{ old('broker_id') == $brk->id ? 'selected' : '' }}>
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
                                <option value="Medium" {{ old('order_type', 'Medium') === 'Medium' ? 'selected' : '' }}>Medium (Standard Processing)</option>
                                <option value="Urgent" {{ old('order_type') === 'Urgent' ? 'selected' : '' }}>Urgent Consignment</option>
                                <option value="Fast" {{ old('order_type') === 'Fast' ? 'selected' : '' }}>Fast Track</option>
                                <option value="Ready Delivery" {{ old('order_type') === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Operational delivery tag</span>
                    </div>

                    <!-- Payment Terms (Spans 2 Columns) -->
                    <div class="form-group erp-col-span-2">
                        <label class="erp-field-label">
                            Payment Terms
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-clock erp-field-icon"></i>
                            <select name="payment_terms" class="form-control erp-field-input-iconified">
                                <option value="Cash" {{ old('payment_terms') === 'Cash' ? 'selected' : '' }}>Cash on Delivery (Immediate)</option>
                                <option value="15 Days" {{ old('payment_terms') === '15 Days' ? 'selected' : '' }}>Credit 15 Days</option>
                                <option value="30 Days" {{ old('payment_terms', '30 Days') === '30 Days' ? 'selected' : '' }}>Credit 30 Days</option>
                                <option value="45 Days" {{ old('payment_terms') === '45 Days' ? 'selected' : '' }}>Credit 45 Days</option>
                                <option value="Bank Transfer" {{ old('payment_terms') === 'Bank Transfer' ? 'selected' : '' }}>Bank RTGS / NEFT</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Agreed credit repayment cycle</span>
                    </div>
                </div>
            </div>

            <!-- 2. Inward Product Line Items (Matching Ledger Layout - 100% Full Width) -->
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
                    <button type="button" class="btn btn-outline erp-btn-add-row" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="addPurchaseRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Line Item
                    </button>
                </div>

                <div class="erp-form-section-body p-0 erp-form-section-body-table" style="display: block !important; padding: 0 !important; width: 100%;">
                    <div class="erp-items-table-wrapper" style="border: none; border-radius: 0; overflow-x: auto; width: 100%;">
                        <table class="erp-items-table" id="items-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">S.NO</th>
                                    <th style="min-width: 260px;">ITEM <span class="text-danger">*</span></th>
                                    <th style="width: 100px;">HSN</th>
                                    <th style="width: 80px; text-align: center;">GST</th>
                                    <th style="width: 110px;">UNIT TYPE <span class="text-danger">*</span></th>
                                    <th style="width: 100px; text-align: right;">NET WT <span class="text-danger">*</span></th>
                                    <th style="width: 125px; text-align: right;">BILL RATE (₹) <span class="text-danger">*</span></th>
                                    <th style="width: 120px; text-align: right;">U-B RATE (₹)</th>
                                    <th style="width: 125px; text-align: right;">BILL ARNT (₹)</th>
                                    <th style="width: 125px; text-align: right;">U-B ARNT (₹)</th>
                                    <th style="width: 48px; text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody id="purchase-items-body">
                                @php
                                    $initialItems = old('items', [
                                        [
                                            'item_id' => '',
                                            'batch_no' => '',
                                            'actual_rate' => '0.00',
                                            'hsn_code' => '',
                                            'gst_percent' => '5.00',
                                            'unit' => 'KG',
                                            'quantity' => '0',
                                            'bill_rate' => '0.00',
                                            'ub_rate' => '0.00',
                                        ]
                                    ]);
                                @endphp
                                @foreach($initialItems as $idx => $row)
                                    <tr class="item-row">
                                        <td class="text-center row-sno">
                                            <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.78rem; padding: 4px 8px; border-radius: 6px;">{{ $idx + 1 }}</span>
                                        </td>
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
                                            <input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="Enter HSN" value="{{ $row['hsn_code'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[{{ $idx }}][gst_percent]" class="form-control row-gst font-monospace text-center" value="{{ number_format((float)($row['gst_percent'] ?? 5), 2, '.', '') }}" oninput="calcRow(this)">
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
                                            <input type="number" step="any" min="0" name="items[{{ $idx }}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="{{ $row['quantity'] ?? '0' }}" required oninput="calcRow(this)">
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
                                            <span class="font-monospace text-dark font-weight-700 row-bill-amt">₹{{ number_format($rQty * $rBill, 2) }}</span>
                                        </td>
                                        <td style="text-align: right;">
                                            <span class="font-monospace font-weight-700 row-ub-amt" style="color: #D97706;">₹{{ number_format($rQty * $rUb, 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="delete-row-btn" onclick="removeRow(this)" title="Remove line item">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Compact Action & Totals Toolbar -->
                    <div class="erp-table-action-bar">
                        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-outline erp-btn-add-row" onclick="addPurchaseRow()">
                                <i class="fa-solid fa-plus me-1"></i> Add Another Item Row
                            </button>
                            <span class="erp-table-action-hint">
                                <i class="fa-solid fa-calculator me-1" style="color: #5B841E;"></i>
                                <span><strong>Bill Arnt</strong> = Net Wt &times; Bill Rate &bull; <strong>U-B Arnt</strong> = Net Wt &times; U-B Rate</span>
                            </span>
                        </div>

                        <div class="erp-table-totals-grid">
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
                                <span class="erp-table-total-label" style="color: #D97706;">U-B Amount</span>
                                <span class="erp-table-total-val font-monospace" style="color: #D97706;" id="footer-ub-total">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item erp-table-total-grand" onclick="setPaidAmountToGrandTotal()" style="cursor: pointer;" title="Click to set this value in Paid Amount (₹)">
                                <span class="erp-table-total-label" style="color: #059669; display: flex; align-items: center; justify-content: space-between; gap: 0.35rem;">
                                    <span>Total Grand Value</span>
                                    <i class="fa-solid fa-arrow-down" style="font-size: 0.65rem;" title="Copy to Paid Amount"></i>
                                </span>
                                <span class="erp-table-total-val font-monospace" style="color: #059669; font-size: 1.1rem;" id="footer-grand-total">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item" style="border-left: 1px dashed #CBD5E1; padding-left: 0.85rem;">
                                <span class="erp-table-total-label" style="color: #DC2626;" id="footer-pending-label">Pending Collection</span>
                                <span class="erp-table-total-val font-monospace" style="color: #DC2626; font-size: 1.05rem;" id="footer-pending-total">₹0.00</span>
                            </div>
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

                <div class="erp-form-section-body-4col">
                    <!-- Paid Amount -->
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label class="erp-field-label mb-0" style="margin-bottom: 0 !important;">
                                Paid Amount (₹)
                            </label>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span id="auto-sync-status-badge" class="badge" style="background: #E8F5E9; color: #2E7D32; font-size: 0.7rem; font-weight: 600; padding: 2px 7px; border-radius: 4px; border: 1px solid #A5D6A7;" title="Automatically tracks and updates with Total Grand Value">
                                    <i class="fa-solid fa-arrows-rotate fa-spin-pulse me-1"></i> Auto-Syncing
                                </span>
                                <button type="button" class="btn btn-link p-0 text-decoration-none" style="font-size: 0.74rem; color: #5B841E; font-weight: 700; cursor: pointer; border: none; background: none;" onclick="setPaidAmountToGrandTotal(true)" title="Auto-fill with Total Grand Value">
                                    <i class="fa-solid fa-bolt me-1"></i> Match Total
                                </button>
                            </div>
                        </div>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="field-paid-amount" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter amount paid" value="{{ old('paid_amount', '0.00') }}" oninput="onPaidAmountInput()">
                        </div>
                        <span class="erp-field-hint" id="paid-amount-hint">Automatically updated from Total Grand Value (<span id="hint-grand-total">₹0.00</span>)</span>
                    </div>

                    <!-- Pending Payment Collection (₹) -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Pending Payment Collection (₹)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hourglass-half erp-field-icon" id="pending-amount-icon" style="color: #DC2626;"></i>
                            <input type="text" id="field-pending-amount" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" readonly style="background: #FEF2F2; color: #DC2626; font-weight: 700; border-color: #FECACA;" value="₹0.00">
                        </div>
                        <span class="erp-field-hint" id="pending-amount-hint">Outstanding balance payable to vendor</span>
                    </div>

                    <!-- Payment Status -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Settlement Status
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-wallet erp-field-icon"></i>
                            <select name="payment_status" id="field-payment-status" class="form-control erp-field-input-iconified" onchange="onPaymentStatusChange()">
                                <option value="unpaid" {{ old('payment_status', 'unpaid') === 'unpaid' ? 'selected' : '' }}>Unpaid / On Credit</option>
                                <option value="partial" {{ old('payment_status', 'partial') === 'partial' ? 'selected' : '' }}>Partially Paid</option>
                                <option value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'selected' : '' }}>Fully Settled</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Accounts payable status</span>
                    </div>

                    <!-- Consignment Notes -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Consignment Remarks / Inward Notes
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-comment-dots erp-field-icon"></i>
                            <input type="text" name="notes" class="form-control erp-field-input-iconified" placeholder="Enter consignment notes" value="{{ old('notes') }}">
                        </div>
                        <span class="erp-field-hint">Internal warehouse &amp; procurement observations</span>
                    </div>
                </div>
            </div>

            <!-- 4. Grand Total Financial Summary & Action Toolbar -->
            <div class="erp-voucher-summary-card">
                <div class="erp-voucher-summary-metrics">
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Total Net Weight</span>
                        <span class="erp-voucher-metric-val font-monospace" id="prev-net-wt">0.000 KG</span>
                    </div>
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Official Bill Total</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #2563EB;" id="prev-bill-total">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Under-Billing (U-B)</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #D97706;" id="prev-ub-total">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric" style="border-left: 2px solid #E2E8F0; padding-left: 1.5rem;">
                        <span class="erp-voucher-metric-label" style="color: #059669;">Grand Total Inward Value</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #059669; font-size: 1.65rem;" id="prev-grand-total">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric" style="border-left: 1px solid #E2E8F0; padding-left: 1.25rem;">
                        <span class="erp-voucher-metric-label" style="color: #059669;">Paid Amount</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #059669; font-size: 1.25rem;" id="summary-paid-amount">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric" style="border-left: 1px solid #E2E8F0; padding-left: 1.25rem;">
                        <span class="erp-voucher-metric-label" id="summary-pending-label" style="color: #DC2626;">Pending Collection</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #DC2626; font-size: 1.35rem;" id="summary-pending-amount">₹0.00</span>
                    </div>
                </div>

                <div class="erp-voucher-actions">
                    <a href="{{ route('admin.transactions.purchase-entry') }}" class="btn btn-outline" style="padding: 0.65rem 1.4rem; font-weight: 600; border-radius: 8px;">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel &amp; Return
                    </a>
                    <button type="submit" class="btn btn-primary" id="btn-save-purchase" style="padding: 0.65rem 1.85rem; font-weight: 700; font-size: 0.95rem; border-radius: 8px; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.25);">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Purchase Voucher
                    </button>
                </div>
            </div>

            <!-- Inward Accounting Rules Card -->
            <div class="card" style="padding: 1.15rem 1.5rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                <div style="font-weight: 700; color: #334155; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-circle-info" style="color: #5B841E;"></i> Inward Accounting Rules
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 2rem; line-height: 1.5;">
                    <span>&bull; Saving this entry increments product physical stock inward immediately.</span>
                    <span>&bull; Supplier accounts payable ledger will be updated by the full Grand Total amount.</span>
                    <span>&bull; Status automatically defaults to <strong>Received</strong> upon submission.</span>
                </div>
            </div>

            <!-- Optional Hidden Elements for Script Safety -->
            <div style="display: none;">
                <span id="prev-title"></span>
                <span id="prev-vendor"></span>
                <span id="prev-avatar"></span>
                <span id="prev-code"></span>
                <span id="prev-order-type"></span>
                <span id="prev-item-count"></span>
                <span id="prev-balance-text"></span>
                <span id="prev-inv-ref"></span>
                <span id="prev-bill-sub"></span>
                <span id="prev-tax-amt"></span>
            </div>

        </div>
    </form>
</section>

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
    let rowIndex = {{ count($initialItems ?? [1]) }};

    function addPurchaseRow() {
        const tbody = document.getElementById('purchase-items-body');
        const itemOptions = document.getElementById('item-options-template').innerHTML;
        const unitOptions = document.getElementById('unit-options-template').innerHTML;

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-sno">
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.78rem; padding: 4px 8px; border-radius: 6px;">${tbody.children.length + 1}</span>
            </td>
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
                <input type="number" step="any" min="0" name="items[${rowIndex}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="0" required oninput="calcRow(this)">
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
            el.innerHTML = `<span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.78rem; padding: 4px 8px; border-radius: 6px;">${idx + 1}</span>`;
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
        const ubRateInput = row.querySelector('.row-ub-rate');
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
            prevTitle.textContent = 'New Purchase Voucher';
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
        window.currentGrandTotal = grandTotal;

        // Auto-set Paid Amount to Total Grand Value
        if (typeof isAutoSyncPaid === 'undefined' || isAutoSyncPaid) {
            const paidInput = document.getElementById('field-paid-amount');
            const paymentStatus = document.getElementById('field-payment-status');
            if (paidInput) {
                paidInput.value = grandTotal > 0 ? grandTotal.toFixed(2) : '0.00';
            }
            if (paymentStatus) {
                paymentStatus.value = grandTotal > 0 ? 'paid' : 'unpaid';
            }
        }

        // Footer Totals
        document.getElementById('footer-total-qty').textContent = totalQty.toFixed(3);
        document.getElementById('footer-bill-subtotal').textContent = '₹' + totalBillSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-tax-total').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-ub-total').textContent = '₹' + totalUB.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Calculate Paid & Pending Collection
        const paidVal = parseFloat(document.getElementById('field-paid-amount')?.value) || 0;
        const pendingAmount = Math.max(0, grandTotal - paidVal);

        // Update Section 3 Pending Input
        const pendingInput = document.getElementById('field-pending-amount');
        const pendingIcon = document.getElementById('pending-amount-icon');
        const pendingHint = document.getElementById('pending-amount-hint');
        if (pendingInput) {
            pendingInput.value = '₹' + pendingAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (pendingAmount <= 0.001) {
                pendingInput.style.color = '#059669';
                pendingInput.style.background = '#ECFDF5';
                pendingInput.style.borderColor = '#A7F3D0';
                if (pendingIcon) {
                    pendingIcon.className = 'fa-solid fa-circle-check erp-field-icon';
                    pendingIcon.style.color = '#059669';
                }
                if (pendingHint) {
                    pendingHint.innerHTML = '<span style="color: #059669; font-weight: 600;"><i class="fa-solid fa-check me-1"></i>No pending balance (Fully Cleared)</span>';
                }
            } else {
                pendingInput.style.color = '#DC2626';
                pendingInput.style.background = '#FEF2F2';
                pendingInput.style.borderColor = '#FECACA';
                if (pendingIcon) {
                    pendingIcon.className = 'fa-solid fa-hourglass-half erp-field-icon';
                    pendingIcon.style.color = '#DC2626';
                }
                if (pendingHint) {
                    pendingHint.innerHTML = '<span style="color: #DC2626; font-weight: 600;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Pending payable collection to vendor</span>';
                }
            }
        }

        // Update Footer Pending Total
        const footerPendingEl = document.getElementById('footer-pending-total');
        if (footerPendingEl) {
            footerPendingEl.textContent = '₹' + pendingAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            footerPendingEl.style.color = pendingAmount <= 0.001 ? '#059669' : '#DC2626';
        }

        // Update Summary Bar Paid & Pending
        const summaryPaidEl = document.getElementById('summary-paid-amount');
        if (summaryPaidEl) {
            summaryPaidEl.textContent = '₹' + paidVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        const summaryPendingEl = document.getElementById('summary-pending-amount');
        if (summaryPendingEl) {
            summaryPendingEl.textContent = '₹' + pendingAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            summaryPendingEl.style.color = pendingAmount <= 0.001 ? '#059669' : '#DC2626';
        }

        const hintGrandEl = document.getElementById('hint-grand-total');
        if (hintGrandEl) {
            hintGrandEl.textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

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

    let isAutoSyncPaid = {{ old('paid_amount') !== null && old('paid_amount') !== '0.00' ? 'false' : 'true' }};

    function setAutoSync(enabled) {
        isAutoSyncPaid = enabled;
        const badge = document.getElementById('auto-sync-status-badge');
        const hint = document.getElementById('paid-amount-hint');
        if (badge) {
            if (enabled) {
                badge.style.display = 'inline-block';
                badge.innerHTML = '<i class="fa-solid fa-arrows-rotate fa-spin-pulse me-1"></i> Auto-Syncing';
                badge.style.background = '#E8F5E9';
                badge.style.color = '#2E7D32';
                badge.style.borderColor = '#A5D6A7';
            } else {
                badge.style.display = 'inline-block';
                badge.innerHTML = '<i class="fa-solid fa-pen me-1"></i> Manual Entry';
                badge.style.background = '#FEF3C7';
                badge.style.color = '#B45309';
                badge.style.borderColor = '#FCD34D';
            }
        }
    }

    function setPaidAmountToGrandTotal(enableAuto = true) {
        if (enableAuto) {
            setAutoSync(true);
        }
        const val = typeof window.currentGrandTotal === 'number' ? window.currentGrandTotal : 0;
        const paidInput = document.getElementById('field-paid-amount');
        const paymentStatus = document.getElementById('field-payment-status');
        if (!paidInput) return;

        paidInput.value = val > 0 ? val.toFixed(2) : '0.00';
        if (paymentStatus) {
            paymentStatus.value = val > 0 ? 'paid' : 'unpaid';
        }

        paidInput.style.transition = 'all 0.3s ease';
        paidInput.style.backgroundColor = '#ECFDF5';
        paidInput.style.borderColor = '#10B981';
        paidInput.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.2)';
        setTimeout(() => {
            paidInput.style.backgroundColor = '';
            paidInput.style.borderColor = '';
            paidInput.style.boxShadow = '';
        }, 1200);

        if (window.toastr) {
            toastr.success(`Paid Amount automatically synchronized to Total Grand Value: ₹${val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
        }
        updateLiveSummary();
    }

    function onPaymentStatusChange() {
        const paymentStatus = document.getElementById('field-payment-status');
        const paidInput = document.getElementById('field-paid-amount');
        if (!paymentStatus || !paidInput) return;

        const val = typeof window.currentGrandTotal === 'number' ? window.currentGrandTotal : 0;
        if (paymentStatus.value === 'paid') {
            setAutoSync(true);
            paidInput.value = val > 0 ? val.toFixed(2) : '0.00';
        } else if (paymentStatus.value === 'unpaid') {
            setAutoSync(false);
            paidInput.value = '0.00';
        } else if (paymentStatus.value === 'partial') {
            setAutoSync(false);
        }
        updateLiveSummary();
    }

    function onPaidAmountInput() {
        const paymentStatus = document.getElementById('field-payment-status');
        const paidInput = document.getElementById('field-paid-amount');
        if (!paymentStatus || !paidInput) return;

        const paidVal = parseFloat(paidInput.value) || 0;
        const grandVal = typeof window.currentGrandTotal === 'number' ? window.currentGrandTotal : 0;

        if (Math.abs(paidVal - grandVal) > 0.01) {
            setAutoSync(false);
        }

        if (paidVal <= 0) {
            paymentStatus.value = 'unpaid';
        } else if (grandVal > 0 && paidVal >= grandVal - 0.01) {
            paymentStatus.value = 'paid';
        } else {
            paymentStatus.value = 'partial';
        }
        updateLiveSummary();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const vendorSelect = document.getElementById('field-vendor-id');
        if (vendorSelect && vendorSelect.value) {
            onVendorChange(vendorSelect);
        }
        setAutoSync(isAutoSyncPaid);
        updateLiveSummary();
    });
</script>
@endpush
@endsection
