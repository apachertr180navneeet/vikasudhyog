@extends('admin.layouts.app')

@section('title', 'Edit WB Sales Entry ' . $wbSale->slip_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-sales')

@section('content')
<section class="view-section active" id="view-wb-sales-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-sales-entry') }}">Transactions</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.transactions.wb-sales-entry') }}">WB Sales Entry</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit #{{ $wbSale->slip_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit WB Sales Slip #{{ $wbSale->slip_no }}
            </h1>
            <p class="erp-page-subtitle">
                Modify outward Weighbridge slip coordinates, tare deductions, rates &amp; outward items with live stock verification.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.wb-sales-entry.show', $wbSale) }}" class="btn btn-outline">
                <i class="fa-solid fa-eye"></i> View Slip Dossier
            </a>
            <a href="{{ route('admin.transactions.wb-sales-entry') }}" class="btn btn-outline">
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
    <form action="{{ route('admin.transactions.wb-sales-entry.update', $wbSale) }}" method="POST" id="wb-sales-form" onsubmit="return validateStockBeforeSubmit(event)">
        @csrf
        @method('PUT')

        <div class="erp-form-layout-full">

            <!-- 1. WB Slip & Customer Identity -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-primary">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">1. Outward Slip &amp; Customer Identity</h3>
                            <p class="erp-form-section-desc">Consignment slip coordinates, dispatch date, customer profile &amp; transport details</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body-4col">
                    <!-- Slip No (Readonly) -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            WB Slip No <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hashtag erp-field-icon"></i>
                            <input type="text" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" value="{{ $wbSale->slip_no }}" readonly>
                        </div>
                        <span class="erp-field-hint">Fixed outward slip reference</span>
                    </div>

                    <!-- Dispatch Date -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Dispatch Date <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                            <input type="date" name="entry_date" id="field-entry-date" class="form-control erp-field-input-iconified" value="{{ old('entry_date', $wbSale->entry_date->format('Y-m-d')) }}" required onchange="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Consignment dispatch date</span>
                    </div>

                    <!-- Customer Ref / Challan No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Customer PO / Challan No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                            <input type="text" name="invoice_no" id="field-invoice-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter PO or delivery challan" value="{{ old('invoice_no', $wbSale->invoice_no) }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Buyer delivery slip or PO reference</span>
                    </div>

                    <!-- Vehicle / Transport No -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Vehicle / Transport No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                            <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vehicle registration number" value="{{ old('vehicle_no', $wbSale->vehicle_no) }}" oninput="this.value = this.value.toUpperCase(); updateLiveSummary();">
                        </div>
                        <span class="erp-field-hint">Truck or transport registration number</span>
                    </div>

                    <!-- Customer Select (Spans 2 Columns) -->
                    <div class="form-group erp-col-span-2">
                        <label class="erp-field-label">
                            Customer / Buyer Firm <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-user-tie erp-field-icon"></i>
                            <select name="customer_id" id="field-customer-id" class="form-control erp-field-input-iconified" required onchange="onCustomerChange(this)">
                                <option value="">Select Customer / Buyer Firm...</option>
                                @foreach($customers as $cst)
                                    <option value="{{ $cst->id }}" data-name="{{ $cst->name }}" data-city="{{ $cst->city }}" {{ old('customer_id', $wbSale->customer_id) == $cst->id ? 'selected' : '' }}>
                                        {{ $cst->name }} ({{ $cst->code }}{{ $cst->city ? ' - ' . $cst->city : '' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <span class="erp-field-hint">Buyer account whose ledger balance will be debited</span>
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
                                    <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" data-comm="{{ $brk->commission_rate }}" {{ old('broker_id', $wbSale->broker_id) == $brk->id ? 'selected' : '' }}>
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
                                <option value="Medium" {{ old('order_type', $wbSale->order_type) === 'Medium' ? 'selected' : '' }}>Medium (Standard Processing)</option>
                                <option value="Urgent" {{ old('order_type', $wbSale->order_type) === 'Urgent' ? 'selected' : '' }}>Urgent Consignment</option>
                                <option value="Fast" {{ old('order_type', $wbSale->order_type) === 'Fast' ? 'selected' : '' }}>Fast Track</option>
                                <option value="Ready Delivery" {{ old('order_type', $wbSale->order_type) === 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Operational dispatch priority</span>
                    </div>

                    <!-- Driver Name -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Driver Name
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-id-card erp-field-icon"></i>
                            <input type="text" name="driver_name" class="form-control erp-field-input-iconified" placeholder="Enter driver name" value="{{ old('driver_name', $wbSale->driver_name) }}">
                        </div>
                        <span class="erp-field-hint">Transport driver name</span>
                    </div>

                    <!-- Driver Phone -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Driver Mobile No
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-phone erp-field-icon"></i>
                            <input type="text" name="driver_phone" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter 10-digit mobile number" value="{{ old('driver_phone', $wbSale->driver_phone) }}">
                        </div>
                        <span class="erp-field-hint">Driver contact phone</span>
                    </div>

                    <!-- Gross Weight -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Gross Weight (KG)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-weight-hanging erp-field-icon"></i>
                            <input type="number" step="0.001" min="0" name="gross_weight" id="field-gross-weight" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="0.000" value="{{ old('gross_weight', $wbSale->gross_weight) }}" oninput="calcNetWeight()">
                        </div>
                        <span class="erp-field-hint">Total vehicle loaded weight</span>
                    </div>

                    <!-- Tare Weight -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Tare Weight (KG)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck erp-field-icon"></i>
                            <input type="number" step="0.001" min="0" name="tare_weight" id="field-tare-weight" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="0.000" value="{{ old('tare_weight', $wbSale->tare_weight) }}" oninput="calcNetWeight()">
                        </div>
                        <span class="erp-field-hint">Empty vehicle unladen weight</span>
                    </div>

                    <!-- Deduction Weight -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Deduction Weight (KG)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-minus erp-field-icon"></i>
                            <input type="number" step="0.001" min="0" name="deduction_weight" id="field-deduction-weight" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="0.000" value="{{ old('deduction_weight', $wbSale->deduction_weight) }}" oninput="calcNetWeight()">
                        </div>
                        <span class="erp-field-hint">Moisture / bag tare allowance</span>
                    </div>

                    <!-- Net Outward Weight -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Net Outward Weight (KG)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-scale-balanced erp-field-icon" style="color: #2563EB;"></i>
                            <input type="text" name="net_weight" id="field-net-weight" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" readonly style="background: #EFF6FF; color: #1D4ED8; font-weight: 700; border-color: #BFDBFE;" value="{{ number_format($wbSale->net_weight, 3, '.', '') }}">
                        </div>
                        <span class="erp-field-hint">Gross &minus; Tare &minus; Deduction</span>
                    </div>
                </div>
            </div>

            <!-- 2. Outward Product Line Items (Direct Mandi Rate) -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-success">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">2. Outward Product Line Items &amp; Stock Availability</h3>
                            <p class="erp-form-section-desc">Outward items at direct agreed mandi rates with real-time inward stock checking (without GST or under-billing)</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline erp-btn-add-row" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="addWBSalesRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add Line Item
                    </button>
                </div>

                <div class="erp-form-section-body p-0 erp-form-section-body-table" style="display: block !important; padding: 0 !important; width: 100%;">
                    <div class="erp-items-table-wrapper" style="border: none; border-radius: 0; overflow-x: auto; width: 100%;">
                        <table class="erp-items-table" id="items-table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 45px; text-align: center;">S.NO</th>
                                    <th style="min-width: 260px;">ITEM <span class="text-danger">*</span></th>
                                    <th style="width: 110px; text-align: center;">INWARD STOCK</th>
                                    <th style="width: 100px;">HSN</th>
                                    <th style="width: 105px;">UNIT TYPE <span class="text-danger">*</span></th>
                                    <th style="width: 120px; text-align: right;">DISPATCH QTY <span class="text-danger">*</span></th>
                                    <th style="width: 125px; text-align: right;">RATE (₹) <span class="text-danger">*</span></th>
                                    <th style="width: 140px; text-align: right;">LINE TOTAL (₹)</th>
                                    <th style="min-width: 150px;">REMARKS</th>
                                    <th style="width: 45px; text-align: center;"></th>
                                </tr>
                            </thead>
                            <tbody id="wb-items-body">
                                @php
                                    $existingItems = old('items');
                                    if (!$existingItems) {
                                        $existingItems = $wbSale->items->map(function($i) {
                                            return [
                                                'item_id' => $i->item_id,
                                                'batch_no' => $i->batch_no,
                                                'hsn_code' => $i->hsn_code,
                                                'unit' => $i->unit,
                                                'quantity' => $i->quantity,
                                                'rate' => $i->rate,
                                                'notes' => $i->notes,
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
                                                            data-sale-rate="{{ $itm->sale_rate }}"
                                                            data-stock="{{ (float)$itm->current_stock }}"
                                                            data-batch="{{ $itm->batch_no }}"
                                                            {{ ($row['item_id'] ?? '') == $itm->id ? 'selected' : '' }}>
                                                        {{ $itm->name }} ({{ $itm->code }}) - Stock: {{ number_format($itm->current_stock, 2) }} {{ $itm->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="items[{{ $idx }}][batch_no]" class="row-batch" value="{{ $row['batch_no'] ?? '' }}">
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
                                            <select name="items[{{ $idx }}][unit]" class="form-select row-unit" required>
                                                @php
                                                    $rU = strtoupper(trim($row['unit'] ?? 'KG'));
                                                @endphp
                                                @foreach($units as $u)
                                                    @php
                                                        $uVal = $u->code ?: $u->name;
                                                         $isSelected = $u->matchesValue($rU);
@endphp
                                                    <option value="{{ $uVal }}"
                                                            data-code="{{ $u->code }}"
                                                            data-name="{{ $u->name }}"
                                                            {{ $isSelected ? 'selected' : '' }}>
                                                        {{ strtoupper($u->code ?: $u->name) }}
                                                    </option>
                                                @endforeach
                                                @php
                                                    $foundInMaster = $units->contains(fn($u) => $u->matchesValue($rU));
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

                    <!-- Unified Action & Totals Toolbar -->
                    <div class="erp-table-action-bar">
                        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-outline erp-btn-add-row" onclick="addWBSalesRow()">
                                <i class="fa-solid fa-plus me-1"></i> Add Another Item Row
                            </button>
                            <span class="erp-table-action-hint">
                                <i class="fa-solid fa-shield-halved me-1" style="color: #5B841E;"></i>
                                <span><strong>Stock Check:</strong> Outward quantity is actively checked against available inward stock.</span>
                            </span>
                        </div>

                        <div class="erp-table-totals-grid">
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Total Outward Items Wt</span>
                                <span class="erp-table-total-val font-monospace" id="footer-total-qty">0.000</span>
                            </div>
                            <div class="erp-table-total-item erp-table-total-grand" onclick="setPaidAmountToGrandTotal()" style="cursor: pointer;" title="Click to copy into Received Amount (₹)">
                                <span class="erp-table-total-label" style="color: #059669; display: flex; align-items: center; justify-content: space-between; gap: 0.35rem;">
                                    <span>Total Outward Value</span>
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

            <!-- 3. Settlement & Operational Notes -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-purple">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">3. Cash / Mandi Settlement &amp; Consignment Notes</h3>
                            <p class="erp-form-section-desc">Immediate settlement, payment mode &amp; outward observations</p>
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
                                <button type="button" class="btn btn-link p-0 text-decoration-none" style="font-size: 0.74rem; color: #5B841E; font-weight: 700; cursor: pointer; border: none; background: none;" onclick="setPaidAmountToGrandTotal(true)" title="Auto-fill with Total Value">
                                    <i class="fa-solid fa-bolt me-1"></i> Match Total
                                </button>
                            </div>
                        </div>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="field-paid-amount" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter amount received" value="{{ old('paid_amount', $wbSale->paid_amount) }}" oninput="onPaidAmountInput()">
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

                    <!-- Payment Mode -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Payment Mode
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-money-check-dollar erp-field-icon"></i>
                            <select name="payment_mode" class="form-control erp-field-input-iconified">
                                <option value="Cash" {{ old('payment_mode', $wbSale->payment_mode) === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Bank Transfer" {{ old('payment_mode', $wbSale->payment_mode) === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer / NEFT</option>
                                <option value="Mandi Slip" {{ old('payment_mode', $wbSale->payment_mode) === 'Mandi Slip' ? 'selected' : '' }}>Mandi Slip / Parchi</option>
                                <option value="Cheque" {{ old('payment_mode', $wbSale->payment_mode) === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Mandi cash settlement channel</span>
                    </div>

                    <!-- Payment Status -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Settlement Status
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-wallet erp-field-icon"></i>
                            <select name="payment_status" id="field-payment-status" class="form-control erp-field-input-iconified" onchange="onPaymentStatusChange()">
                                <option value="unpaid" {{ old('payment_status', $wbSale->payment_status) === 'unpaid' ? 'selected' : '' }}>Unpaid / On Credit</option>
                                <option value="paid" {{ old('payment_status', $wbSale->payment_status) === 'paid' ? 'selected' : '' }}>Fully Settled</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Accounts collection status</span>
                    </div>

                    <!-- Consignment Notes (Spans 4 Columns) -->
                    <div class="form-group erp-col-span-4">
                        <label class="erp-field-label">
                            Dispatch Remarks / Notes
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-comment-dots erp-field-icon"></i>
                            <input type="text" name="notes" class="form-control erp-field-input-iconified" placeholder="Enter outward dispatch observations" value="{{ old('notes', $wbSale->notes) }}">
                        </div>
                        <span class="erp-field-hint">Factory gate-pass or weighbridge observations</span>
                    </div>
                </div>
            </div>

            <!-- 4. Grand Total Financial Summary & Action Toolbar -->
            <div class="erp-voucher-summary-card">
                <div class="erp-voucher-summary-metrics">
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Net Weighbridge Wt</span>
                        <span class="erp-voucher-metric-val font-monospace" id="prev-wb-net-wt">0.000 KG</span>
                    </div>
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Item Line Total Wt</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #2563EB;" id="prev-item-wt">0.000 KG</span>
                    </div>
                    <div class="erp-voucher-metric" style="border-left: 2px solid #E2E8F0; padding-left: 1.5rem;">
                        <span class="erp-voucher-metric-label" style="color: #059669;">Total Outward Value</span>
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
                    <a href="{{ route('admin.transactions.wb-sales-entry') }}" class="btn btn-outline" style="padding: 0.65rem 1.4rem; font-weight: 600; border-radius: 8px;">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel
                    </a>
                    <button type="submit" id="btn-submit-wb-sales" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700; border-radius: 8px; box-shadow: 0 4px 12px rgba(91, 132, 30, 0.35);">
                        <i class="fa-solid fa-check me-1"></i> Update WB Sales Entry &amp; Adjust Stock
                    </button>
                </div>
            </div>

        </div>
    </form>
</section>

<!-- Dynamic Unit Master Options Template -->
<template id="unit-options-template">
    @foreach($units as $u)
        <option value="{{ $u->code ?: $u->name }}" data-code="{{ $u->code }}" data-name="{{ $u->name }}">
            {{ strtoupper($u->code ?: $u->name) }}
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
                sale_rate: {{ (float)($itm->sale_rate ?? 0) }},
                stock: {{ (float)($itm->current_stock ?? 0) }},
                batch: "{{ $itm->batch_no ?? '' }}"
            },
        @endforeach
    ];

    function calcNetWeight() {
        const gross = parseFloat(document.getElementById('field-gross-weight')?.value || 0) || 0;
        const tare = parseFloat(document.getElementById('field-tare-weight')?.value || 0) || 0;
        const deduction = parseFloat(document.getElementById('field-deduction-weight')?.value || 0) || 0;
        const net = Math.max(0, gross - tare - deduction);

        const netField = document.getElementById('field-net-weight');
        if (netField) netField.value = net.toFixed(3);

        const prevWbNet = document.getElementById('prev-wb-net-wt');
        if (prevWbNet) prevWbNet.textContent = net.toFixed(3) + ' KG';
    }

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
        const unitSelect = row.querySelector('.row-unit');
        const rateInput = row.querySelector('.row-rate');
        const batchInput = row.querySelector('.row-batch');
        const stockBadge = row.querySelector('.row-stock-badge');
        const origQtyInput = row.querySelector('.row-original-qty');
        const originalQty = parseFloat(origQtyInput ? origQtyInput.value : 0) || 0;

        if (item) {
            if (hsnInput) hsnInput.value = item.hsn;
            if (unitSelect) setSelectUnit(unitSelect, item.unit);
            if (rateInput && (parseFloat(rateInput.value) === 0 || !rateInput.value)) {
                rateInput.value = item.sale_rate.toFixed(2);
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
        const rateInput = row.querySelector('.row-rate');
        const lineTotalSpan = row.querySelector('.row-total-amt');
        const stockErr = row.querySelector('.row-stock-error');
        const origQtyInput = row.querySelector('.row-original-qty');
        const originalQty = parseFloat(origQtyInput ? origQtyInput.value : 0) || 0;

        const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
        const rate = parseFloat(rateInput ? rateInput.value : 0) || 0;

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

        const lineTotal = qty * rate;
        if (lineTotalSpan) lineTotalSpan.textContent = '₹' + lineTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        updateLiveSummary();
    }

    function updateLiveSummary() {
        let totalQty = 0;
        let totalVal = 0;

        document.querySelectorAll('#wb-items-body .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty')?.value || 0) || 0;
            const rate = parseFloat(row.querySelector('.row-rate')?.value || 0) || 0;
            totalQty += qty;
            totalVal += (qty * rate);
        });

        document.getElementById('footer-total-qty').textContent = totalQty.toFixed(3);
        document.getElementById('footer-grand-total').textContent = '₹' + totalVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('prev-item-wt').textContent = totalQty.toFixed(3) + ' KG';
        document.getElementById('prev-grand-total').textContent = '₹' + totalVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

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

    function addWBSalesRow() {
        const tbody = document.getElementById('wb-items-body');
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
                                data-sale-rate="{{ $itm->sale_rate }}"
                                data-stock="{{ (float)$itm->current_stock }}"
                                data-batch="{{ $itm->batch_no }}">
                            {{ $itm->name }} ({{ $itm->code }}) - Stock: {{ number_format($itm->current_stock, 2) }} {{ $itm->unit }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="items[${rowIndex}][batch_no]" class="row-batch" value="">
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
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][rate]" class="form-control row-rate font-monospace" style="text-align: right;" value="0.00" required oninput="calcRow(this)">
            </td>
            <td style="text-align: right;">
                <span class="font-monospace text-dark font-weight-700 row-total-amt">₹0.00</span>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][notes]" class="form-control font-monospace" placeholder="Enter remarks" style="font-size: 0.83rem;">
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
        const tbody = document.getElementById('wb-items-body');
        if (tbody.children.length <= 1) {
            alert('At least one item row is required in WB sales entry.');
            return;
        }
        btn.closest('.item-row').remove();
        renumberRows();
        updateLiveSummary();
    }

    function renumberRows() {
        document.querySelectorAll('#wb-items-body .item-row').forEach((row, i) => {
            const badge = row.querySelector('.row-sno .badge');
            if (badge) badge.textContent = i + 1;
        });
    }

    function validateStockBeforeSubmit(event) {
        const itemTotals = {};
        let stockViolation = null;

        document.querySelectorAll('#wb-items-body .item-row').forEach(row => {
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
        document.querySelectorAll('#wb-items-body .item-row').forEach(row => {
            const sel = row.querySelector('.erp-item-select');
            if (sel && sel.value) {
                onItemSelect(sel);
            }
        });
        calcNetWeight();
        updateLiveSummary();
    });
</script>
@endpush
@endsection
