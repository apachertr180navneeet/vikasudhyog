@extends('admin.layouts.app')

@section('title', 'Edit Sales Entry ' . $sale->sale_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-sales-order')

@section('content')
<section class="view-section active" id="view-sales-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.sales-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.sales-entry') }}">Sales Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit #{{ $sale->sale_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Sales Invoice #{{ $sale->sale_no }}
            </h1>
            <p class="erp-page-subtitle">
                Modify customer billing coordinates, rates, and outward dispatch items with live stock verification.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.sales-entry.show', $sale) }}" class="btn btn-outline">
                <i class="fa-solid fa-eye"></i> View Profile Dossier
            </a>
            <a href="{{ route('admin.transactions.sales-entry') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Sales List
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
    <form action="{{ route('admin.transactions.sales-entry.update', $sale) }}" method="POST" id="sales-form" onsubmit="return validateStockBeforeSubmit(event)">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-full">

            <!-- 1. Sales Voucher & Customer Identity -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-primary">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">1. Sales Invoice &amp; Customer Identity</h3>
                            <p class="erp-form-section-desc">Invoice coordinates, dispatch date, customer profile &amp; transport details</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body-4col">
                    <!-- Invoice / Voucher No (Readonly) -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Invoice No <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hashtag erp-field-icon"></i>
                            <input type="text" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" value="{{ $sale->sale_no }}" readonly>
                        </div>
                        <span class="erp-field-hint">Fixed outward sales reference</span>
                    </div>

                    <!-- Sale Date -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Sale / Dispatch Date <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                            <input type="date" name="sale_date" id="field-sale-date" class="form-control erp-field-input-iconified" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required onchange="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Billing or goods dispatch date</span>
                    </div>

                    <!-- Customer Order / Ref No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Customer PO / Ref No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                            <input type="text" name="invoice_no" id="field-invoice-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PO or reference number" value="{{ old('invoice_no', $sale->invoice_no) }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Buyer purchase order or challan reference</span>
                    </div>

                    <!-- Vehicle / Transport No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Vehicle / Transport No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                            <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vehicle registration number" value="{{ old('vehicle_no', $sale->vehicle_no) }}" oninput="this.value = this.value.toUpperCase(); updateLiveSummary();">
                        </div>
                        <span class="erp-field-hint">Dispatch transport or truck registration</span>
                    </div>

                    <!-- Customer Firm Select (Spans 2 Columns) -->
                    <div class="form-group erp-col-span-2">
                        <label class="erp-field-label">
                            Customer / Buyer Firm <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-user-tie erp-field-icon"></i>
                            <select name="customer_id" id="field-customer-id" class="form-control erp-field-input-iconified" required onchange="onCustomerChange(this)">
                                <option value="">Select Customer / Buyer Firm...</option>
                                @foreach($customers as $cst)
                                    <option value="{{ $cst->id }}" data-name="{{ $cst->name }}" data-city="{{ $cst->city }}" data-gstin="{{ $cst->gstin }}" {{ old('customer_id', $sale->customer_id) == $cst->id ? 'selected' : '' }}>
                                        {{ $cst->name }} ({{ $cst->code }}{{ $cst->city ? ' - ' . $cst->city : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="erp-field-hint">Debtor account whose ledger balance will be debited</span>
                    </div>

                    <!-- Broker / Agent Select -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Broker / Sales Commission Agent <span class="text-muted">(Optional)</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-handshake erp-field-icon"></i>
                            <select name="broker_id" id="field-broker-id" class="form-control erp-field-input-iconified" onchange="updateLiveSummary()">
                                <option value="">Direct Sale (No Broker)...</option>
                                @foreach($brokers as $brk)
                                    <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" data-comm="{{ $brk->commission_rate }}" {{ old('broker_id', $sale->broker_id) == $brk->id ? 'selected' : '' }}>
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
                            Order Priority / Type <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-tag erp-field-icon"></i>
                            <select name="order_type" id="field-order-type" class="form-control erp-field-input-iconified" required onchange="updateLiveSummary()">
                                <option value="Medium" {{ old('order_type', $sale->order_type) === 'Medium' ? 'selected' : '' }}>Medium (Standard Processing)</option>
                                <option value="Urgent" {{ old('order_type', $sale->order_type) === 'Urgent' ? 'selected' : '' }}>Urgent Consignment</option>
                                <option value="Fast" {{ old('order_type', $sale->order_type) === 'Fast' ? 'selected' : '' }}>Fast Track</option>
                                <option value="Ready Delivery" {{ old('order_type', $sale->order_type) === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Operational dispatch priority</span>
                    </div>

                    <!-- Payment Terms (Spans 2 Columns) -->
                    <div class="form-group erp-col-span-2">
                        <label class="erp-field-label">
                            Payment Terms
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-clock erp-field-icon"></i>
                            <select name="payment_terms" class="form-control erp-field-input-iconified">
                                <option value="Cash" {{ old('payment_terms', $sale->payment_terms) === 'Cash' ? 'selected' : '' }}>Cash on Delivery (Immediate)</option>
                                <option value="15 Days" {{ old('payment_terms', $sale->payment_terms) === '15 Days' ? 'selected' : '' }}>Credit 15 Days</option>
                                <option value="30 Days" {{ old('payment_terms', $sale->payment_terms) === '30 Days' ? 'selected' : '' }}>Credit 30 Days</option>
                                <option value="45 Days" {{ old('payment_terms', $sale->payment_terms) === '45 Days' ? 'selected' : '' }}>Credit 45 Days</option>
                                <option value="Bank Transfer" {{ old('payment_terms', $sale->payment_terms) === 'Bank Transfer' ? 'selected' : '' }}>Bank RTGS / NEFT</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Agreed credit collection terms</span>
                    </div>
                </div>
            </div>

            <!-- 2. Outward Product Line Items (With Live Stock Check) -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-success">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">2. Outward Product Line Items &amp; Stock Availability</h3>
                            <p class="erp-form-section-desc">Select outward items with live available stock checking, dual-rate official &amp; under-billing calculation</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline erp-btn-add-row" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="addSalesRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Line Item
                    </button>
                </div>

                <div class="erp-form-section-body p-0 erp-form-section-body-table" style="display: block !important; padding: 0 !important; width: 100%;">
                    <div class="erp-items-table-wrapper" style="border: none; border-radius: 0; overflow-x: auto; width: 100%;">
                        <table class="erp-items-table" id="items-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">S.NO</th>
                                    <th style="min-width: 250px;">ITEM <span class="text-danger">*</span></th>
                                    <th style="width: 110px; text-align: center;">INWARD STOCK</th>
                                    <th style="width: 90px;">HSN</th>
                                    <th style="width: 75px; text-align: center;">GST %</th>
                                    <th style="width: 105px;">UNIT TYPE <span class="text-danger">*</span></th>
                                    <th style="width: 115px; text-align: right;">DISPATCH QTY <span class="text-danger">*</span></th>
                                    <th style="width: 125px; text-align: right;">BILL RATE (₹) <span class="text-danger">*</span></th>
                                    <th style="width: 115px; text-align: right;">U-B RATE (₹)</th>
                                    <th style="width: 125px; text-align: right;">BILL AMT (₹)</th>
                                    <th style="width: 125px; text-align: right;">U-B AMT (₹)</th>
                                    <th style="width: 45px; text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody id="sales-items-body">
                                @php
                                    $existingItems = old('items');
                                    if (!$existingItems) {
                                        $existingItems = $sale->items->map(function($i) {
                                            return [
                                                'item_id' => $i->item_id,
                                                'batch_no' => $i->batch_no,
                                                'actual_rate' => $i->actual_rate,
                                                'hsn_code' => $i->hsn_code,
                                                'gst_percent' => $i->gst_percent,
                                                'unit' => $i->unit,
                                                'quantity' => $i->quantity,
                                                'bill_rate' => $i->bill_rate,
                                                'ub_rate' => $i->ub_rate,
                                                'original_qty' => $i->quantity,
                                            ];
                                        })->toArray();
                                    }
                                @endphp
                                @foreach($existingItems as $idx => $row)
                                    <tr class="item-row">
                                        <td class="text-center row-sno">
                                            <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.78rem; padding: 4px 8px; border-radius: 6px;">{{ $idx + 1 }}</span>
                                        </td>
                                        <td>
                                            <select name="items[{{ $idx }}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
                                                <option value="">Select Outward Item...</option>
                                                @foreach($items as $itm)
                                                    <option value="{{ $itm->id }}"
                                                            data-code="{{ $itm->code }}"
                                                            data-hsn="{{ $itm->hsn_code }}"
                                                            data-unit="{{ $itm->unit }}"
                                                            data-gst="{{ $itm->gst_rate }}"
                                                            data-sale-rate="{{ $itm->sale_rate }}"
                                                            data-stock="{{ (float)$itm->current_stock }}"
                                                            data-batch="{{ $itm->batch_no }}"
                                                            {{ ($row['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                                                        {{ $itm->name }} ({{ $itm->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $idx }}][batch_no]" class="row-batch" value="{{ $row['batch_no'] ?? '' }}">
                                            <input type="hidden" name="items[{{ $idx }}][actual_rate]" class="row-actual" value="{{ $row['actual_rate'] ?? '0.00' }}">
                                            <input type="hidden" class="row-original-qty" value="{{ $row['original_qty'] ?? 0 }}">
                                        </td>
                                        <!-- Live Stock Availability Badge -->
                                        <td class="text-center">
                                            <div class="row-stock-badge-wrap">
                                                <span class="badge row-stock-badge font-monospace" style="background: #F1F5F9; color: #64748B; font-size: 0.76rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                                                    --
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="Enter HSN" value="{{ $row['hsn_code'] ?? '' }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[{{ $idx }}][gst_percent]" class="form-control row-gst font-monospace text-center" value="{{ number_format((float)($row['gst_percent'] ?? 5), 2, '.', '') }}" oninput="calcRow(this)">
                                        </td>
                                        <td>
                                            <select name="items[{{ $idx }}][unit]" class="form-select row-unit" required>
                                                @php
                                                    $rU = strtoupper(trim($row['unit'] ?? 'KG'));
                                                @endphp
                                                @foreach($units as $u)
                                                    @php
                                                        $uVal = $u->code ?: $u->name;
                                                        $uCode = strtoupper(trim($u->code ?? ''));
                                                        $uName = strtoupper(trim($u->name ?? ''));
                                                        $isSelected = ($rU === $uCode || $rU === $uName ||
                                                                      (($rU === 'PACKET' || $rU === 'PKT') && in_array($uCode, ['PKT', 'PACKET'])) ||
                                                                      (($rU === 'KG' || $rU === 'KILOGRAM') && in_array($uCode, ['KG', 'KILOGRAM'])) ||
                                                                      (($rU === 'QTL' || $rU === 'QUINTAL') && in_array($uCode, ['QTL', 'QUINTAL'])));
                                                    @endphp
                                                    <option value="{{ $uVal }}"
                                                            data-code="{{ $u->code }}"
                                                            data-name="{{ $u->name }}"
                                                            title="{{ $u->name }} ({{ $u->code }})"
                                                            {{ $isSelected ? 'selected' : '' }}>
                                                        {{ $u->code ?: $u->name }}
                                                    </option>
                                                @endforeach
                                                @php
                                                    $foundInMaster = $units->contains(function($u) use ($rU) {
                                                        $uCode = strtoupper(trim($u->code ?? ''));
                                                        $uName = strtoupper(trim($u->name ?? ''));
                                                        return $rU === $uCode || $rU === $uName ||
                                                               (($rU === 'PACKET' || $rU === 'PKT') && in_array($uCode, ['PKT', 'PACKET'])) ||
                                                               (($rU === 'KG' || $rU === 'KILOGRAM') && in_array($uCode, ['KG', 'KILOGRAM'])) ||
                                                               (($rU === 'QTL' || $rU === 'QUINTAL') && in_array($uCode, ['QTL', 'QUINTAL']));
                                                    });
                                                @endphp
                                                @if(!$foundInMaster && !empty($row['unit']))
                                                    <option value="{{ $row['unit'] }}" data-code="{{ $row['unit'] }}" data-name="{{ $row['unit'] }}" selected>
                                                        {{ $row['unit'] }}
                                                    </option>
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0.001" name="items[{{ $idx }}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="{{ $row['quantity'] ?? '0' }}" required oninput="calcRow(this)">
                                            <div class="row-stock-error text-danger font-monospace" style="display: none; font-size: 0.68rem; margin-top: 2px;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> Exceeds stock!
                                            </div>
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
                            <button type="button" class="btn btn-outline erp-btn-add-row" onclick="addSalesRow()">
                                <i class="fa-solid fa-plus me-1"></i> Add Another Item Row
                            </button>
                            <span class="erp-table-action-hint">
                                <i class="fa-solid fa-shield-halved me-1" style="color: #5B841E;"></i>
                                <span><strong>Stock Check:</strong> Outward quantity is actively validated against available inward purchase stock.</span>
                            </span>
                        </div>

                        <div class="erp-table-totals-grid">
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Total Outward Wt</span>
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
                            <div class="erp-table-total-item erp-table-total-grand" onclick="setPaidAmountToGrandTotal()" style="cursor: pointer;" title="Click to copy into Received Amount (₹)">
                                <span class="erp-table-total-label" style="color: #059669; display: flex; align-items: center; justify-content: space-between; gap: 0.35rem;">
                                    <span>Total Grand Value</span>
                                    <i class="fa-solid fa-arrow-down" style="font-size: 0.65rem;" title="Copy to Received Amount"></i>
                                </span>
                                <span class="erp-table-total-val font-monospace" style="color: #059669; font-size: 1.1rem;" id="footer-grand-total">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item" style="border-left: 1px dashed #CBD5E1; padding-left: 0.85rem;">
                                <span class="erp-table-total-label" style="color: #DC2626;" id="footer-pending-label">Pending Receivable</span>
                                <span class="erp-table-total-val font-monospace" style="color: #DC2626; font-size: 1.05rem;" id="footer-pending-total">₹0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Settlement & Consignment Notes -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-purple">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">3. Settlement &amp; Consignment Notes</h3>
                            <p class="erp-form-section-desc">Advance collection, payment tracking &amp; outward dispatch notes</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body-4col">
                    <!-- Received Amount -->
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label class="erp-field-label mb-0" style="margin-bottom: 0 !important;">
                                Received Amount (₹)
                            </label>
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <button type="button" class="btn btn-link p-0 text-decoration-none" style="font-size: 0.74rem; color: #5B841E; font-weight: 700; cursor: pointer; border: none; background: none;" onclick="setPaidAmountToGrandTotal(true)" title="Auto-fill with Total Grand Value">
                                    <i class="fa-solid fa-bolt me-1"></i> Match Total
                                </button>
                            </div>
                        </div>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="field-paid-amount" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter amount received" value="{{ old('paid_amount', $sale->paid_amount) }}" oninput="onPaidAmountInput()">
                        </div>
                        <span class="erp-field-hint" id="paid-amount-hint">Customer payment received</span>
                    </div>

                    <!-- Pending Customer Receivable (₹) -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Pending Customer Receivable (₹)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hourglass-half erp-field-icon" id="pending-amount-icon" style="color: #DC2626;"></i>
                            <input type="text" id="field-pending-amount" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" readonly style="background: #FEF2F2; color: #DC2626; font-weight: 700; border-color: #FECACA;" value="₹0.00">
                        </div>
                        <span class="erp-field-hint" id="pending-amount-hint">Outstanding balance receivable from buyer</span>
                    </div>

                    <!-- Payment Status -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Settlement Status
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-wallet erp-field-icon"></i>
                            <select name="payment_status" id="field-payment-status" class="form-control erp-field-input-iconified" onchange="onPaymentStatusChange()">
                                <option value="unpaid" {{ old('payment_status', $sale->payment_status) === 'unpaid' ? 'selected' : '' }}>Unpaid / On Credit</option>
                                <option value="partial" {{ old('payment_status', $sale->payment_status) === 'partial' ? 'selected' : '' }}>Partially Received</option>
                                <option value="paid" {{ old('payment_status', $sale->payment_status) === 'paid' ? 'selected' : '' }}>Fully Settled</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Accounts receivable collection status</span>
                    </div>

                    <!-- Consignment Notes -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Dispatch Remarks / Notes
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-comment-dots erp-field-icon"></i>
                            <input type="text" name="notes" class="form-control erp-field-input-iconified" placeholder="Enter dispatch observations" value="{{ old('notes', $sale->notes) }}">
                        </div>
                        <span class="erp-field-hint">Internal factory delivery &amp; gate-pass observations</span>
                    </div>
                </div>
            </div>

            <!-- 4. Grand Total Financial Summary & Action Toolbar -->
            <div class="erp-voucher-summary-card">
                <div class="erp-voucher-summary-metrics">
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Total Outward Wt</span>
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
                        <span class="erp-voucher-metric-label" style="color: #059669;">Grand Total Outward Value</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #059669; font-size: 1.65rem;" id="prev-grand-total">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric" style="border-left: 1px solid #E2E8F0; padding-left: 1.25rem;">
                        <span class="erp-voucher-metric-label" style="color: #059669;">Received Amount</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #059669; font-size: 1.25rem;" id="summary-paid-amount">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric" style="border-left: 1px solid #E2E8F0; padding-left: 1.25rem;">
                        <span class="erp-voucher-metric-label" id="summary-pending-label" style="color: #DC2626;">Pending Receivable</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #DC2626; font-size: 1.35rem;" id="summary-pending-amount">₹0.00</span>
                    </div>
                </div>

                <div class="erp-voucher-actions">
                    <a href="{{ route('admin.transactions.sales-entry') }}" class="btn btn-outline" style="padding: 0.65rem 1.4rem; font-weight: 600; border-radius: 8px;">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </a>
                    <button type="submit" id="btn-submit-sales" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 12px rgba(91, 132, 30, 0.35);">
                        <i class="fa-solid fa-check me-1"></i> Update Sales Entry &amp; Adjust Stock
                    </button>
                </div>
            </div>

        </div>
    </form>
</section>

<!-- Dynamic Unit Master Options Template -->
<template id="unit-options-template">
    @foreach($units as $u)
        <option value="{{ $u->code ?: $u->name }}" data-code="{{ $u->code }}" data-name="{{ $u->name }}" title="{{ $u->name }} ({{ $u->code }})">
            {{ $u->code ?: $u->name }}
        </option>
    @endforeach
</template>

@push('scripts')
<script>
    let rowIndex = {{ count($existingItems) }};

    const availableItems = [
        @foreach($items as $itm)
            {
                id: {{ $itm->id }},
                name: "{{ addslashes($itm->name) }}",
                code: "{{ $itm->code }}",
                hsn: "{{ $itm->hsn_code ?? '' }}",
                unit: "{{ $itm->unit ?? 'KG' }}",
                gst: {{ (float)($itm->gst_rate ?? 5) }},
                sale_rate: {{ (float)($itm->sale_rate ?? 0) }},
                stock: {{ (float)($itm->current_stock ?? 0) }},
                batch: "{{ $itm->batch_no ?? '' }}"
            },
        @endforeach
    ];

    function setSelectUnit(unitSelect, targetUnit) {
        if (!unitSelect || !targetUnit) return;
        const cleanTarget = targetUnit.toString().trim().toUpperCase();
        let matched = false;

        // 1. Direct match on value, text, data-code, or data-name
        for (let i = 0; i < unitSelect.options.length; i++) {
            const opt = unitSelect.options[i];
            const optVal = opt.value.trim().toUpperCase();
            const optText = opt.text.trim().toUpperCase();
            const optCode = (opt.getAttribute('data-code') || '').trim().toUpperCase();
            const optName = (opt.getAttribute('data-name') || '').trim().toUpperCase();

            if (optVal === cleanTarget || optCode === cleanTarget || optName === cleanTarget || optText === cleanTarget) {
                unitSelect.selectedIndex = i;
                matched = true;
                break;
            }
        }

        // 2. Intelligent synonym & ERP abbreviation match
        if (!matched) {
            for (let i = 0; i < unitSelect.options.length; i++) {
                const opt = unitSelect.options[i];
                const optVal = opt.value.trim().toUpperCase();
                const optCode = (opt.getAttribute('data-code') || '').trim().toUpperCase();
                const optName = (opt.getAttribute('data-name') || '').trim().toUpperCase();

                const isPacket = (cleanTarget === 'PACKET' || cleanTarget === 'PKT' || cleanTarget === 'PAC') &&
                                 (optVal === 'PKT' || optCode === 'PKT' || optName.includes('PACKET') || optVal === 'PACKET');
                const isKg = (cleanTarget === 'KG' || cleanTarget === 'KGS' || cleanTarget === 'KILOGRAM') &&
                             (optVal === 'KG' || optCode === 'KG' || optName.includes('KILOGRAM') || optVal === 'KILOGRAM');
                const isBag = (cleanTarget === 'BAG' || cleanTarget === 'BAGS') &&
                              (optVal === 'BAG' || optCode === 'BAG' || optName.includes('BAG'));
                const isBox = (cleanTarget === 'BOX' || cleanTarget === 'BOXES') &&
                              (optVal === 'BOX' || optCode === 'BOX' || optName.includes('BOX'));
                const isQuintal = (cleanTarget === 'QUINTAL' || cleanTarget === 'QTL') &&
                                  (optVal === 'QTL' || optCode === 'QTL' || optName.includes('QUINTAL') || optVal === 'QUINTAL');
                const isGram = (cleanTarget === 'GM' || cleanTarget === 'GRAM' || cleanTarget === 'GMS') &&
                               (optVal === 'GM' || optCode === 'GM' || optName.includes('GRAM') || optVal === 'GRAM');
                const isMeter = (cleanTarget === 'M' || cleanTarget === 'MTR' || cleanTarget === 'METER') &&
                                (optVal === 'M' || optCode === 'M' || optName.includes('METER') || optVal === 'METER');
                const isPcs = (cleanTarget === 'PCS' || cleanTarget === 'PIECE' || cleanTarget === 'PIECES') &&
                              (optVal === 'PCS' || optCode === 'PCS' || optName.includes('PIECE'));

                if (isPacket || isKg || isBag || isBox || isQuintal || isGram || isMeter || isPcs) {
                    unitSelect.selectedIndex = i;
                    matched = true;
                    break;
                }
            }
        }

        // 3. Fallback: dynamically add and select so it never fails
        if (!matched) {
            const newOpt = new Option(cleanTarget, cleanTarget, true, true);
            newOpt.setAttribute('data-code', cleanTarget);
            newOpt.setAttribute('data-name', cleanTarget);
            unitSelect.add(newOpt);
            unitSelect.value = cleanTarget;
        }
    }

    function onItemSelect(selectEl) {
        const row = selectEl.closest('.item-row');
        const selectedId = selectEl.value;
        const item = availableItems.find(i => i.id == selectedId);

        const hsnInput = row.querySelector('.row-hsn');
        const gstInput = row.querySelector('.row-gst');
        const unitSelect = row.querySelector('.row-unit');
        const billRateInput = row.querySelector('.row-bill-rate');
        const batchInput = row.querySelector('.row-batch');
        const stockBadge = row.querySelector('.row-stock-badge');
        const origQtyInput = row.querySelector('.row-original-qty');
        const originalQty = parseFloat(origQtyInput ? origQtyInput.value : 0) || 0;

        if (item) {
            if (hsnInput) hsnInput.value = item.hsn;
            if (gstInput) gstInput.value = item.gst.toFixed(2);
            if (unitSelect) setSelectUnit(unitSelect, item.unit);
            if (billRateInput && (parseFloat(billRateInput.value) === 0 || !billRateInput.value)) {
                billRateInput.value = item.sale_rate.toFixed(2);
            }
            if (batchInput) batchInput.value = item.batch;

            const effectiveStock = item.stock + originalQty;
            if (stockBadge) {
                stockBadge.textContent = effectiveStock.toFixed(2) + ' ' + item.unit;
                if (effectiveStock > 0) {
                    stockBadge.style.background = '#ECFDF5';
                    stockBadge.style.color = '#065F46';
                    stockBadge.style.borderColor = '#A7F3D0';
                } else {
                    stockBadge.style.background = '#FEF2F2';
                    stockBadge.style.color = '#B91C1C';
                    stockBadge.style.borderColor = '#FECACA';
                }
            }
        }

        calcRow(selectEl);
    }

    function calcRow(el) {
        const row = el.closest('.item-row');
        const selectEl = row.querySelector('.erp-item-select');
        const selectedId = selectEl ? selectEl.value : null;
        const item = availableItems.find(i => i.id == selectedId);

        const qtyInput = row.querySelector('.row-qty');
        const billRateInput = row.querySelector('.row-bill-rate');
        const ubRateInput = row.querySelector('.row-ub-rate');
        const gstInput = row.querySelector('.row-gst');
        const billAmtSpan = row.querySelector('.row-bill-amt');
        const ubAmtSpan = row.querySelector('.row-ub-amt');
        const stockErr = row.querySelector('.row-stock-error');
        const origQtyInput = row.querySelector('.row-original-qty');
        const originalQty = parseFloat(origQtyInput ? origQtyInput.value : 0) || 0;

        const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
        const billRate = parseFloat(billRateInput ? billRateInput.value : 0) || 0;
        const ubRate = parseFloat(ubRateInput ? ubRateInput.value : 0) || 0;
        const gstPercent = parseFloat(gstInput ? gstInput.value : 0) || 0;

        if (item) {
            const effectiveStock = item.stock + originalQty;
            if (qty > effectiveStock) {
                qtyInput.style.borderColor = '#DC2626';
                qtyInput.style.backgroundColor = '#FEF2F2';
                if (stockErr) {
                    stockErr.style.display = 'block';
                    stockErr.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Exceeds stock (${effectiveStock.toFixed(2)} ${item.unit})`;
                }
            } else {
                qtyInput.style.borderColor = '';
                qtyInput.style.backgroundColor = '';
                if (stockErr) stockErr.style.display = 'none';
            }
        }

        const billAmt = qty * billRate;
        const ubAmt = qty * ubRate;

        if (billAmtSpan) billAmtSpan.textContent = '₹' + billAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (ubAmtSpan) ubAmtSpan.textContent = '₹' + ubAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        updateLiveSummary();
    }

    function updateLiveSummary() {
        let totalQty = 0;
        let subtotal = 0;
        let taxTotal = 0;
        let ubTotal = 0;

        document.querySelectorAll('#sales-items-body .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty')?.value || 0) || 0;
            const billRate = parseFloat(row.querySelector('.row-bill-rate')?.value || 0) || 0;
            const ubRate = parseFloat(row.querySelector('.row-ub-rate')?.value || 0) || 0;
            const gst = parseFloat(row.querySelector('.row-gst')?.value || 0) || 0;

            const lineSub = qty * billRate;
            const lineTax = lineSub * (gst / 100);
            const lineUB = qty * ubRate;

            totalQty += qty;
            subtotal += lineSub;
            taxTotal += lineTax;
            ubTotal += lineUB;
        });

        const billTotal = subtotal + taxTotal;
        const grandTotal = billTotal + ubTotal;

        document.getElementById('footer-total-qty').textContent = totalQty.toFixed(3);
        document.getElementById('footer-bill-subtotal').textContent = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-tax-total').textContent = '₹' + taxTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-ub-total').textContent = '₹' + ubTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('prev-net-wt').textContent = totalQty.toFixed(3) + ' KG';
        document.getElementById('prev-bill-total').textContent = '₹' + billTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-ub-total').textContent = '₹' + ubTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        recalcPendingCollection();
    }

    function onPaidAmountInput() {
        recalcPendingCollection();
    }

    function setPaidAmountToGrandTotal(force = false) {
        const grandText = document.getElementById('footer-grand-total').textContent.replace(/[₹,]/g, '');
        const grandTotal = parseFloat(grandText) || 0;
        const paidInput = document.getElementById('field-paid-amount');
        if (paidInput) {
            paidInput.value = grandTotal.toFixed(2);
        }
        recalcPendingCollection();
    }

    function recalcPendingCollection() {
        const grandText = document.getElementById('footer-grand-total').textContent.replace(/[₹,]/g, '');
        const grandTotal = parseFloat(grandText) || 0;
        const paidInput = document.getElementById('field-paid-amount');
        const paidVal = parseFloat(paidInput ? paidInput.value : 0) || 0;
        const pending = Math.max(0, grandTotal - paidVal);

        const pendingField = document.getElementById('field-pending-amount');
        const summaryPaid = document.getElementById('summary-paid-amount');
        const summaryPending = document.getElementById('summary-pending-amount');
        const footerPending = document.getElementById('footer-pending-total');

        if (pendingField) pendingField.value = '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (summaryPaid) summaryPaid.textContent = '₹' + paidVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (summaryPending) summaryPending.textContent = '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (footerPending) footerPending.textContent = '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function onPaymentStatusChange() {
        const status = document.getElementById('field-payment-status').value;
        if (status === 'paid') {
            setPaidAmountToGrandTotal(true);
        } else if (status === 'unpaid') {
            const paidInput = document.getElementById('field-paid-amount');
            if (paidInput) paidInput.value = '0.00';
            recalcPendingCollection();
        }
    }

    function addSalesRow() {
        const tbody = document.getElementById('sales-items-body');
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="text-center row-sno">
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; font-size: 0.78rem; padding: 4px 8px; border-radius: 6px;">${tbody.children.length + 1}</span>
            </td>
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
                    <option value="">Select Outward Item...</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}"
                                data-code="{{ $itm->code }}"
                                data-hsn="{{ $itm->hsn_code }}"
                                data-unit="{{ $itm->unit }}"
                                data-gst="{{ $itm->gst_rate }}"
                                data-sale-rate="{{ $itm->sale_rate }}"
                                data-stock="{{ (float)$itm->current_stock }}"
                                data-batch="{{ $itm->batch_no }}">
                            {{ $itm->name }} ({{ $itm->code }})
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${rowIndex}][batch_no]" class="row-batch" value="">
                <input type="hidden" name="items[${rowIndex}][actual_rate]" class="row-actual" value="0.00">
                <input type="hidden" class="row-original-qty" value="0">
            </td>
            <td class="text-center">
                <div class="row-stock-badge-wrap">
                    <span class="badge row-stock-badge font-monospace" style="background: #F1F5F9; color: #64748B; font-size: 0.76rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #E2E8F0;">
                        --
                    </span>
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][hsn_code]" class="form-control row-hsn font-monospace" placeholder="Enter HSN">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][gst_percent]" class="form-control row-gst font-monospace text-center" value="5.00" oninput="calcRow(this)">
            </td>
            <td>
                <select name="items[${rowIndex}][unit]" class="form-select row-unit" required>
                    ${document.getElementById('unit-options-template').innerHTML}
                </select>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="items[${rowIndex}][quantity]" class="form-control row-qty font-monospace" style="text-align: right;" value="0" required oninput="calcRow(this)">
                <div class="row-stock-error text-danger font-monospace" style="display: none; font-size: 0.68rem; margin-top: 2px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Exceeds stock!
                </div>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][bill_rate]" class="form-control row-bill-rate font-monospace" style="text-align: right;" value="0.00" required oninput="calcRow(this)">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][ub_rate]" class="form-control row-ub-rate font-monospace" style="text-align: right;" value="0.00" oninput="calcRow(this)">
            </td>
            <td style="text-align: right;">
                <span class="font-monospace text-dark font-weight-700 row-bill-amt">₹0.00</span>
            </td>
            <td style="text-align: right;">
                <span class="font-monospace font-weight-700 row-ub-amt" style="color: #D97706;">₹0.00</span>
            </td>
            <td class="text-center">
                <button type="button" class="delete-row-btn" onclick="removeRow(this)" title="Remove line item">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;
        tbody.appendChild(newRow);
        rowIndex++;
        renumberRows();
    }

    function removeRow(btn) {
        const tbody = document.getElementById('sales-items-body');
        if (tbody.children.length <= 1) {
            alert('At least one item row is required in sales entry.');
            return;
        }
        btn.closest('.item-row').remove();
        renumberRows();
        updateLiveSummary();
    }

    function renumberRows() {
        document.querySelectorAll('#sales-items-body .item-row').forEach((row, i) => {
            const badge = row.querySelector('.row-sno .badge');
            if (badge) badge.textContent = i + 1;
        });
    }

    function validateStockBeforeSubmit(event) {
        const itemTotals = {};
        let stockViolation = null;

        document.querySelectorAll('#sales-items-body .item-row').forEach(row => {
            const selectEl = row.querySelector('.erp-item-select');
            const qtyInput = row.querySelector('.row-qty');
            const origQtyInput = row.querySelector('.row-original-qty');
            if (!selectEl || !selectEl.value) return;

            const itemId = selectEl.value;
            const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
            const originalQty = parseFloat(origQtyInput ? origQtyInput.value : 0) || 0;
            const item = availableItems.find(i => i.id == itemId);

            if (item) {
                itemTotals[itemId] = (itemTotals[itemId] || 0) + qty;
                const effectiveStock = item.stock + originalQty;
                if (itemTotals[itemId] > effectiveStock) {
                    stockViolation = {
                        item: item,
                        req: itemTotals[itemId],
                        available: effectiveStock
                    };
                }
            }
        });

        if (stockViolation) {
            event.preventDefault();
            const msg = `Insufficient inward stock for '${stockViolation.item.name}' (${stockViolation.item.code})!\n\nAvailable stock: ${stockViolation.available.toFixed(2)} ${stockViolation.item.unit}\nRequested outward: ${stockViolation.req.toFixed(2)} ${stockViolation.item.unit}`;
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock Limit Exceeded!',
                    text: msg,
                    confirmButtonColor: '#EF4444'
                });
            } else {
                alert(msg);
            }
            return false;
        }

        return true;
    }

    function onCustomerChange(selectEl) {
        updateLiveSummary();
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#sales-items-body .item-row').forEach(row => {
            const sel = row.querySelector('.erp-item-select');
            if (sel && sel.value) {
                onItemSelect(sel);
            }
        });
        updateLiveSummary();
    });
</script>
@endpush
@endsection
