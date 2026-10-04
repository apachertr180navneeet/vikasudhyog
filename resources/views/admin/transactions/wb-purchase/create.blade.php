@extends('admin.layouts.app')

@section('title', 'New WB Purchase Entry (Without Bill) - VIKAS UDHYOG ERP')
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
                <span class="erp-breadcrumb-active">New WB Entry (Without Bill)</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-file-circle-plus text-primary"></i> New WB Purchase Entry (Without Bill)
            </h1>
            <p class="erp-page-subtitle">
                Record raw materials, herbs &amp; inward consignments at direct agreed procurement rates without official GST billing.
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

        <div class="erp-form-layout-full">

            <!-- 1. WB Slip & Supplier Identity -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-primary">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">1. Inward Slip &amp; Supplier Identity</h3>
                            <p class="erp-form-section-desc">Consignment slip coordinates, procurement date, supplier coordinates &amp; transport details</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body-4col">
                    <!-- Slip No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            WB Slip No <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hashtag erp-field-icon"></i>
                            <input type="text" name="slip_no" id="field-slip-no" class="form-control erp-field-input-iconified erp-field-input-mono" value="{{ old('slip_no', $nextSlipNo) }}" readonly required>
                        </div>
                        <span class="erp-field-hint">Auto-generated inward slip reference</span>
                    </div>

                    <!-- Inward Entry Date -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Entry Date <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                            <input type="date" name="entry_date" id="field-entry-date" class="form-control erp-field-input-iconified" value="{{ old('entry_date', date('Y-m-d')) }}" required onchange="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Consignment inward arrival date</span>
                    </div>

                    <!-- Supplier Slip / Challan No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Supplier Slip / Challan No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                            <input type="text" name="invoice_no" id="field-invoice-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter supplier slip or challan number" value="{{ old('invoice_no') }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Vendor's printed delivery slip or challan number</span>
                    </div>

                    <!-- Vehicle / Transport No -->
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
                                    <option value="{{ $vnd->id }}" data-name="{{ $vnd->name }}" data-city="{{ $vnd->city }}" {{ old('vendor_id') == $vnd->id ? 'selected' : '' }}>
                                        {{ $vnd->name }} ({{ $vnd->code }}{{ $vnd->city ? ' - ' . $vnd->city : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="erp-field-hint">Creditor account whose ledger balance will be updated</span>
                    </div>

                    <!-- Broker / Agent Select -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Broker / Mandi Commission Agent <span class="text-muted">(Optional)</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-handshake erp-field-icon"></i>
                            <select name="broker_id" id="field-broker-id" class="form-control erp-field-input-iconified" onchange="updateLiveSummary()">
                                <option value="">Direct Mandi (No Broker)...</option>
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
                                <option value="Urgent" {{ old('order_type') === 'Urgent' ? 'selected' : '' }}>Urgent Mandi Lot</option>
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

            <!-- 2. Inward Product Line Items (Same Place, Without Tax/UB Calculation) -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-success">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">2. Inward Product Line Items</h3>
                            <p class="erp-form-section-desc">Raw materials, herbs &amp; inward lots at direct agreed rates (without GST or under-billing calculation)</p>
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
                                    <th style="min-width: 280px;">ITEM <span class="text-danger">*</span></th>
                                    <th style="width: 120px;">HSN</th>
                                    <th style="width: 120px;">UNIT TYPE <span class="text-danger">*</span></th>
                                    <th style="width: 130px; text-align: right;">NET WT <span class="text-danger">*</span></th>
                                    <th style="width: 140px; text-align: right;">RATE (₹) <span class="text-danger">*</span></th>
                                    <th style="width: 150px; text-align: right;">LINE TOTAL (₹)</th>
                                    <th style="min-width: 160px;">REMARKS</th>
                                    <th style="width: 48px; text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody id="wb-items-body">
                                @php
                                    $initialItems = old('items', [
                                        [
                                            'item_id' => '',
                                            'batch_no' => '',
                                            'hsn_code' => '',
                                            'unit' => 'KG',
                                            'quantity' => '0',
                                            'rate' => '0.00',
                                            'notes' => '',
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
                                                            data-purchase-rate="{{ $itm->purchase_rate }}"
                                                            data-batch="{{ $itm->batch_no }}"
                                                            {{ ($row['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                                                        {{ $itm->name }} ({{ $itm->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $idx }}][batch_no]" class="row-batch" value="{{ $row['batch_no'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="Enter HSN" value="{{ $row['hsn_code'] ?? '' }}">
                                        </td>
                                        <td>
                                            <select name="items[{{ $idx }}][unit]" class="form-select row-unit" required>
                                                @php $rowUnit = strtoupper($row['unit'] ?? 'KG'); @endphp
                                                <option value="KG" {{ $rowUnit === 'KG' ? 'selected' : '' }}>KG</option>
                                                <option value="BAG" {{ $rowUnit === 'BAG' ? 'selected' : '' }}>BAG</option>
                                                <option value="QUINTAL" {{ $rowUnit === 'QUINTAL' ? 'selected' : '' }}>QUINTAL</option>
                                                <option value="TON" {{ $rowUnit === 'TON' ? 'selected' : '' }}>TON</option>
                                                <option value="BOX" {{ $rowUnit === 'BOX' ? 'selected' : '' }}>BOX</option>
                                                <option value="PCS" {{ $rowUnit === 'PCS' ? 'selected' : '' }}>PCS</option>
                                                @foreach($units as $u)
                                                    @if(!in_array(strtoupper($u->name), ['KG', 'BAG', 'QUINTAL', 'TON', 'BOX', 'PCS']))
                                                        <option value="{{ $u->name }}" {{ $rowUnit === strtoupper($u->name) ? 'selected' : '' }}>{{ $u->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0" name="items[{{ $idx }}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="{{ $row['quantity'] ?? '0' }}" required oninput="calcRow(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[{{ $idx }}][rate]" class="form-control row-rate font-monospace" style="text-align: right;" value="{{ number_format((float)($row['rate'] ?? 0), 2, '.', '') }}" required oninput="calcRow(this)">
                                        </td>
                                        <td style="text-align: right;">
                                            @php
                                                $rQty = (float)($row['quantity'] ?? 0);
                                                $rRate = (float)($row['rate'] ?? 0);
                                            @endphp
                                            <span class="font-monospace text-dark font-weight-700 row-total-amt">₹{{ number_format($rQty * $rRate, 2) }}</span>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $idx }}][notes]" class="form-control font-monospace" placeholder="Enter remarks" value="{{ $row['notes'] ?? '' }}" style="font-size: 0.83rem;">
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
                                <span><strong>Line Total</strong> = Net Wt &times; Rate (Direct Procurement without tax calculation)</span>
                            </span>
                        </div>

                        <div class="erp-table-totals-grid">
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Total Net Wt</span>
                                <span class="erp-table-total-val font-monospace" id="footer-total-qty">0.000</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Procurement Subtotal</span>
                                <span class="erp-table-total-val font-monospace" id="footer-subtotal">₹0.00</span>
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
                        <span class="erp-field-hint">Accounts payable settlement status</span>
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
                    <div class="erp-voucher-metric" style="border-left: 2px solid #E2E8F0; padding-left: 1.5rem;">
                        <span class="erp-voucher-metric-label" style="color: #059669;">Total Procurement Value</span>
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
                    <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="btn btn-outline" style="padding: 0.65rem 1.4rem; font-weight: 600; border-radius: 8px;">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel &amp; Return
                    </a>
                    <button type="submit" class="btn btn-primary" id="btn-save-purchase" style="padding: 0.65rem 1.85rem; font-weight: 700; font-size: 0.95rem; border-radius: 8px; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.25);">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save WB Purchase Slip
                    </button>
                </div>
            </div>

            <!-- Inward Accounting Rules Card -->
            <div class="card" style="padding: 1.15rem 1.5rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                <div style="font-weight: 700; color: #334155; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                    <i class="fa-solid fa-circle-info" style="color: #5B841E;"></i> Inward Accounting Rules (Without Bill)
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 2rem; line-height: 1.5;">
                    <span>&bull; Saving this entry increments product physical stock inward immediately.</span>
                    <span>&bull; Supplier accounts payable ledger will be updated by the total procurement amount.</span>
                    <span>&bull; Status automatically defaults to <strong>Received</strong> upon submission.</span>
                </div>
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
                data-purchase-rate="{{ $itm->purchase_rate }}"
                data-batch="{{ $itm->batch_no }}">
            {{ $itm->name }} ({{ $itm->code }})
        </option>
    @endforeach
</template>

<template id="unit-options-template">
    <option value="KG">KG</option>
    <option value="BAG">BAG</option>
    <option value="QUINTAL">QUINTAL</option>
    <option value="TON">TON</option>
    <option value="BOX">BOX</option>
    <option value="PCS">PCS</option>
    @foreach($units as $u)
        @if(!in_array(strtoupper($u->name), ['KG', 'BAG', 'QUINTAL', 'TON', 'BOX', 'PCS']))
            <option value="{{ $u->name }}">{{ $u->name }}</option>
        @endif
    @endforeach
</template>

@push('scripts')
<script>
    let rowIndex = {{ count($initialItems ?? [1]) }};
    let isUserEditingPaidAmount = false;

    function addPurchaseRow() {
        const tbody = document.getElementById('wb-items-body');
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
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="Enter HSN" value="">
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
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][rate]" class="form-control row-rate font-monospace" style="text-align: right;" value="0.00" required oninput="calcRow(this)">
            </td>
            <td style="text-align: right;">
                <span class="font-monospace text-dark font-weight-700 row-total-amt">₹0.00</span>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][notes]" class="form-control font-monospace" placeholder="Enter remarks" value="" style="font-size: 0.83rem;">
            </td>
            <td class="text-center">
                <button type="button" class="delete-row-btn" onclick="removeRow(this)" title="Remove line item">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
        renumberRows();
        calcAll();
    }

    function removeRow(btn) {
        const tbody = document.getElementById('wb-items-body');
        if (tbody.querySelectorAll('tr.item-row').length <= 1) {
            alert('At least one item line is required for this WB purchase voucher.');
            return;
        }
        btn.closest('tr.item-row').remove();
        renumberRows();
        calcAll();
    }

    function renumberRows() {
        const rows = document.querySelectorAll('#wb-items-body tr.item-row');
        rows.forEach((row, i) => {
            const badge = row.querySelector('.row-sno .badge');
            if (badge) badge.textContent = i + 1;
        });
    }

    function onItemSelect(sel) {
        const row = sel.closest('tr.item-row');
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;

        const hsn = opt.dataset.hsn || '';
        const unit = opt.dataset.unit || 'KG';
        const purchaseRate = parseFloat(opt.dataset.purchaseRate) || 0;
        const batch = opt.dataset.batch || '';

        const hsnInput = row.querySelector('.row-hsn');
        if (hsnInput) hsnInput.value = hsn;

        const unitSelect = row.querySelector('.row-unit');
        if (unitSelect) {
            for (let i = 0; i < unitSelect.options.length; i++) {
                if (unitSelect.options[i].value.toUpperCase() === unit.toUpperCase()) {
                    unitSelect.selectedIndex = i;
                    break;
                }
            }
        }

        const rateInput = row.querySelector('.row-rate');
        if (rateInput && (!parseFloat(rateInput.value) || parseFloat(rateInput.value) === 0)) {
            rateInput.value = purchaseRate.toFixed(2);
        }

        const batchHidden = row.querySelector('.row-batch');
        if (batchHidden) batchHidden.value = batch;

        calcRow(sel);
    }

    function calcRow(elem) {
        const row = elem.closest('tr.item-row');
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;

        const lineTotal = qty * rate;

        const totalAmtSpan = row.querySelector('.row-total-amt');
        if (totalAmtSpan) {
            totalAmtSpan.textContent = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        calcAll();
    }

    function calcAll() {
        const rows = document.querySelectorAll('#wb-items-body tr.item-row');
        let totalQty = 0;
        let totalAmt = 0;

        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty')?.value) || 0;
            const rate = parseFloat(row.querySelector('.row-rate')?.value) || 0;

            totalQty += qty;
            totalAmt += (qty * rate);
        });

        // Update footer totals
        const footerTotalQty = document.getElementById('footer-total-qty');
        if (footerTotalQty) footerTotalQty.textContent = totalQty.toFixed(3);

        const footerSubtotal = document.getElementById('footer-subtotal');
        if (footerSubtotal) footerSubtotal.textContent = '₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const footerGrand = document.getElementById('footer-grand-total');
        if (footerGrand) footerGrand.textContent = '₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Update bottom floating summary
        const prevNetWt = document.getElementById('prev-net-wt');
        if (prevNetWt) prevNetWt.textContent = totalQty.toFixed(3) + ' KG';

        const prevGrand = document.getElementById('prev-grand-total');
        if (prevGrand) prevGrand.textContent = '₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const hintGrand = document.getElementById('hint-grand-total');
        if (hintGrand) hintGrand.textContent = '₹' + totalAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Auto-sync Paid Amount if user hasn't explicitly diverged
        const paidAmountInput = document.getElementById('field-paid-amount');
        if (!isUserEditingPaidAmount && paidAmountInput) {
            paidAmountInput.value = totalAmt.toFixed(2);
        }

        calculatePendingPayment();
    }

    function onPaidAmountInput() {
        isUserEditingPaidAmount = true;
        const autoBadge = document.getElementById('auto-sync-status-badge');
        if (autoBadge) {
            autoBadge.style.display = 'none';
        }
        calculatePendingPayment();
    }

    function setPaidAmountToGrandTotal(manualClick = false) {
        const rows = document.querySelectorAll('#wb-items-body tr.item-row');
        let totalAmt = 0;
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty')?.value) || 0;
            const rate = parseFloat(row.querySelector('.row-rate')?.value) || 0;
            totalAmt += (qty * rate);
        });

        const paidInput = document.getElementById('field-paid-amount');
        if (paidInput) {
            paidInput.value = totalAmt.toFixed(2);
        }

        if (manualClick) {
            isUserEditingPaidAmount = false;
            const autoBadge = document.getElementById('auto-sync-status-badge');
            if (autoBadge) {
                autoBadge.style.display = 'inline-flex';
            }
        }

        calculatePendingPayment();
    }

    function calculatePendingPayment() {
        const rows = document.querySelectorAll('#wb-items-body tr.item-row');
        let totalAmt = 0;
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty')?.value) || 0;
            const rate = parseFloat(row.querySelector('.row-rate')?.value) || 0;
            totalAmt += (qty * rate);
        });

        const paidInput = document.getElementById('field-paid-amount');
        const paidVal = parseFloat(paidInput ? paidInput.value : 0) || 0;
        const pending = Math.max(0, totalAmt - paidVal);

        const pendingField = document.getElementById('field-pending-amount');
        const pendingIcon = document.getElementById('pending-amount-icon');
        const pendingHint = document.getElementById('pending-amount-hint');

        const footerPending = document.getElementById('footer-pending-total');
        const footerPendingLabel = document.getElementById('footer-pending-label');
        const summaryPaid = document.getElementById('summary-paid-amount');
        const summaryPending = document.getElementById('summary-pending-amount');
        const summaryPendingLabel = document.getElementById('summary-pending-label');

        if (summaryPaid) {
            summaryPaid.textContent = '₹' + paidVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        const formattedPending = '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (pendingField) pendingField.value = formattedPending;
        if (footerPending) footerPending.textContent = formattedPending;
        if (summaryPending) summaryPending.textContent = formattedPending;

        const paymentStatusSelect = document.getElementById('field-payment-status');

        if (pending <= 0.009) {
            if (pendingField) {
                pendingField.style.background = '#ECFDF5';
                pendingField.style.color = '#059669';
                pendingField.style.borderColor = '#A7F3D0';
            }
            if (pendingIcon) pendingIcon.style.color = '#059669';
            if (pendingHint) pendingHint.textContent = 'Full payment settled upon entry creation';
            if (footerPending) footerPending.style.color = '#059669';
            if (footerPendingLabel) {
                footerPendingLabel.textContent = 'Settled In Full';
                footerPendingLabel.style.color = '#059669';
            }
            if (summaryPending) summaryPending.style.color = '#059669';
            if (summaryPendingLabel) {
                summaryPendingLabel.textContent = 'Settled In Full';
                summaryPendingLabel.style.color = '#059669';
            }

            if (paymentStatusSelect && !paymentStatusSelect.dataset.manuallyTouched) {
                paymentStatusSelect.value = 'paid';
            }
        } else {
            if (pendingField) {
                pendingField.style.background = '#FEF2F2';
                pendingField.style.color = '#DC2626';
                pendingField.style.borderColor = '#FECACA';
            }
            if (pendingIcon) pendingIcon.style.color = '#DC2626';
            if (pendingHint) pendingHint.textContent = 'Outstanding balance payable to vendor';
            if (footerPending) footerPending.style.color = '#DC2626';
            if (footerPendingLabel) {
                footerPendingLabel.textContent = 'Pending Collection';
                footerPendingLabel.style.color = '#DC2626';
            }
            if (summaryPending) summaryPending.style.color = '#DC2626';
            if (summaryPendingLabel) {
                summaryPendingLabel.textContent = 'Pending Collection';
                summaryPendingLabel.style.color = '#DC2626';
            }

            if (paymentStatusSelect && !paymentStatusSelect.dataset.manuallyTouched) {
                if (paidVal > 0) {
                    paymentStatusSelect.value = 'partial';
                } else {
                    paymentStatusSelect.value = 'unpaid';
                }
            }
        }
    }

    function onPaymentStatusChange() {
        const paymentStatusSelect = document.getElementById('field-payment-status');
        if (paymentStatusSelect) {
            paymentStatusSelect.dataset.manuallyTouched = 'true';
        }
    }

    function onVendorChange(sel) {
        updateLiveSummary();
    }

    function updateLiveSummary() {
        // Keeps state refreshed
    }

    document.addEventListener('DOMContentLoaded', function() {
        calcAll();
    });

    // Form submit validation
    document.getElementById('wb-purchase-form')?.addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#wb-items-body tr.item-row');
        if (rows.length === 0) {
            e.preventDefault();
            alert('Please add at least one line item to the voucher.');
            return false;
        }

        let hasValidItem = false;
        rows.forEach(r => {
            const itemSelect = r.querySelector('.erp-item-select');
            const qty = parseFloat(r.querySelector('.row-qty')?.value) || 0;
            if (itemSelect && itemSelect.value && qty > 0) {
                hasValidItem = true;
            }
        });

        if (!hasValidItem) {
            e.preventDefault();
            alert('Please select an item and provide a valid Net Weight quantity greater than 0.');
            return false;
        }

        const saveBtn = document.getElementById('btn-save-purchase');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving Slip...';
        }
    });
</script>
@endpush
@endsection
