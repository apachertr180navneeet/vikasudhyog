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
    <form action="{{ route('admin.transactions.sales-entry.update', $sale) }}" method="POST" id="sales-form" enctype="multipart/form-data" onsubmit="return validateStockBeforeSubmit(event)">
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
                    <!-- Invoice / Voucher No (Editable) -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Invoice No <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-hashtag erp-field-icon"></i>
                            <input type="text" name="sale_no" id="field-sale-no" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-bold" value="{{ old('sale_no', $sale->sale_no) }}" required>
                        </div>
                        <span class="erp-field-hint">Outward sales reference (Editable)</span>
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

                    <!-- Order Priority / Type -->
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

                    <!-- Payment Terms -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Payment Terms
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-clock erp-field-icon"></i>
                            <select name="payment_terms" id="field-payment-terms" class="form-control erp-field-input-iconified" onchange="onPaymentTermsChange(this)">
                                <option value="Advance" {{ old('payment_terms', $sale->payment_terms) === 'Advance' ? 'selected' : '' }}>Advance (Advance Payment)</option>
                                <option value="Cash" {{ old('payment_terms', $sale->payment_terms) === 'Cash' ? 'selected' : '' }}>Cash on Delivery (Immediate)</option>
                                <option value="15 Days" {{ old('payment_terms', $sale->payment_terms) === '15 Days' ? 'selected' : '' }}>Credit 15 Days</option>
                                <option value="30 Days" {{ old('payment_terms', $sale->payment_terms) === '30 Days' ? 'selected' : '' }}>Credit 30 Days</option>
                                <option value="45 Days" {{ old('payment_terms', $sale->payment_terms) === '45 Days' ? 'selected' : '' }}>Credit 45 Days</option>
                                <option value="Bank Transfer" {{ old('payment_terms', $sale->payment_terms) === 'Bank Transfer' ? 'selected' : '' }}>Bank RTGS / NEFT</option>
                            </select>
                        </div>
                        <span class="erp-field-hint">Agreed credit collection terms</span>
                    </div>

                    <!-- Advance Amount (Shown when Payment Terms == 'Advance') -->
                    <div class="form-group" id="advance-amount-group" style="{{ old('payment_terms', $sale->payment_terms) === 'Advance' ? '' : 'display: none;' }}">
                        <label class="erp-field-label">
                            Advance Received (₹) <span class="erp-req-star">*</span>
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-money-bill-wave erp-field-icon" style="color: #5B841E;"></i>
                            <input type="number" step="0.01" min="0" name="advance_amount" id="field-advance-amount" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" placeholder="Enter advance received" value="{{ old('advance_amount', $sale->advance_amount ?? '0.00') }}" oninput="onAdvanceAmountInput()">
                        </div>
                        <span class="erp-field-hint">Advance payment collected upon booking/order</span>
                    </div>

                    <!-- Invoice Attachment Upload / Preview -->
                    <div class="form-group {{ old('payment_terms', $sale->payment_terms) === 'Advance' ? 'erp-col-span-3' : 'erp-col-span-4' }}">
                        <label class="erp-field-label">
                            Sales Invoice / PO Attachment <span class="text-muted">(PDF, JPG, JPEG, PNG - Max 10MB)</span>
                        </label>
                        @if($sale->invoice_attachment)
                            <div class="mb-2 p-2 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between;">
                                <div style="font-size: 0.82rem; display: flex; align-items: center; gap: 0.5rem; color: #334155;">
                                    <i class="fa-solid fa-file-lines text-primary"></i>
                                    <span class="font-weight-600">Current File:</span>
                                    <span class="text-truncate font-monospace" style="max-width: 260px;">{{ basename($sale->invoice_attachment) }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $sale->invoice_attachment) }}" target="_blank" class="btn btn-sm btn-outline" style="padding: 2px 10px; font-size: 0.75rem;">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View / Download
                                </a>
                            </div>
                        @endif
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-paperclip erp-field-icon"></i>
                            <input type="file" name="invoice_attachment" id="field-invoice-attachment" class="form-control erp-field-input-iconified" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <span class="erp-field-hint">{{ $sale->invoice_attachment ? 'Select new file to replace existing attachment' : 'Upload sales order scan / buyer PO document' }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Transport & Logistics Details -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-primary">
                            <i class="fa-solid fa-truck-moving"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">2. Transport &amp; Logistics Details</h3>
                            <p class="erp-form-section-desc">Transporter master, vehicle registration, bilty / LR number &amp; E-Way Bill</p>
                        </div>
                    </div>
                </div>

                <div class="erp-form-section-body-4col">
                    <!-- Transport Name Input -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Transport Name / Carrier
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck-fast erp-field-icon"></i>
                            <input type="text" name="transport_name" id="field-transport-name" class="form-control erp-field-input-iconified" placeholder="Enter Transport Name" value="{{ old('transport_name', $sale->transport_name ?? $sale->transporter->name ?? '') }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Transporter or logistics carrier company name</span>
                    </div>

                    <!-- Vehicle / Truck Number -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Vehicle / Truck Number
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-truck erp-field-icon"></i>
                            <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter vehicle registration number" value="{{ old('vehicle_no', $sale->vehicle_no) }}" oninput="this.value = this.value.toUpperCase(); updateLiveSummary();">
                        </div>
                        <span class="erp-field-hint">Dispatch carrier registration number</span>
                    </div>

                    <!-- LR / Bilty Number -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            LR No. / Bilty Number
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                            <input type="text" name="lr_no" id="field-lr-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter LR / Bilty number" value="{{ old('lr_no', $sale->lr_no) }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Lorry receipt / consignment bilty reference</span>
                    </div>

                    <!-- Outward Freight Charges (₹) -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Freight / Cartage Charges (₹)
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                            <input type="number" step="0.01" min="0" name="freight_amount" id="field-freight-amount" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" placeholder="Enter freight amount" value="{{ old('freight_amount', $sale->freight_amount ?? '0.00') }}" oninput="updateLiveSummary()">
                        </div>
                        <span class="erp-field-hint">Freight charges billed to Customer Grand Total</span>
                    </div>

                    <!-- E-Way Bill Number -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            E-Way Bill Number
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-road erp-field-icon"></i>
                            <input type="text" name="e_way_bill_no" id="field-e-way-bill-no" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="Enter 12-digit E-Way bill number" value="{{ old('e_way_bill_no', $sale->e_way_bill_no) }}">
                        </div>
                        <span class="erp-field-hint">Govt GST E-Way Bill portal number</span>
                    </div>

                    <!-- Transport / Dispatch Date -->
                    <div class="form-group">
                        <label class="erp-field-label">
                            Transport / Dispatch Date
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                            <input type="date" name="transport_date" id="field-transport-date" class="form-control erp-field-input-iconified" value="{{ old('transport_date', $sale->transport_date ? \Carbon\Carbon::parse($sale->transport_date)->format('Y-m-d') : date('Y-m-d')) }}">
                        </div>
                        <span class="erp-field-hint">Consignment dispatch / bilty date</span>
                    </div>

                    <!-- Transport Remarks / Notes (Spans 2 columns) -->
                    <div class="form-group erp-col-span-2">
                        <label class="erp-field-label">
                            Transport Remarks / Logistics Notes
                        </label>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-comment-dots erp-field-icon"></i>
                            <input type="text" name="transport_notes" id="field-transport-notes" class="form-control erp-field-input-iconified" placeholder="Enter driver contact, route, notes" value="{{ old('transport_notes', $sale->transport_notes) }}">
                        </div>
                        <span class="erp-field-hint">Driver contact, destination details or delivery instructions</span>
                    </div>
                </div>
            </div>

            <!-- 3. Outward Product Line Items (With Live Stock Check & GST Mode) -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-success">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">3. Outward Product Line Items &amp; Stock Availability</h3>
                            <p class="erp-form-section-desc">Select outward items with live available stock checking, dual-rate official &amp; under-billing calculation</p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <label class="erp-tax-mode-toggle" style="display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; font-size: 0.82rem; font-weight: 600; color: #334155; margin-bottom: 0; background: #F8FAFC; border: 1px solid #CBD5E1; padding: 5px 12px; border-radius: 8px;">
                            <input type="checkbox" name="is_gst_inclusive" id="field-is-gst-inclusive" value="1" {{ old('is_gst_inclusive', $sale->is_gst_inclusive) ? 'checked' : '' }} onchange="onTaxModeToggle(this)" style="width: 16px; height: 16px; accent-color: #5B841E; cursor: pointer;">
                            <span>GST-Inclusive Pricing</span>
                            <span class="badge" id="tax-mode-badge" style="background: {{ old('is_gst_inclusive', $sale->is_gst_inclusive) ? '#ECFDF5' : '#E2E8F0' }}; color: {{ old('is_gst_inclusive', $sale->is_gst_inclusive) ? '#059669' : '#475569' }}; font-size: 0.7rem; padding: 2px 6px; border-radius: 4px;">
                                {{ old('is_gst_inclusive', $sale->is_gst_inclusive) ? 'GST-Inclusive' : 'Exclusive' }}
                            </span>
                        </label>
                        <button type="button" class="btn btn-outline erp-btn-add-row" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="addSalesRow()">
                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                        </button>
                    </div>
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
                                    <th style="width: 125px; text-align: right;" id="th-bill-rate">BILL RATE (₹) <span class="text-danger">*</span></th>
                                    <th style="width: 115px; text-align: right;">U-B RATE (₹)</th>
                                    <th style="width: 125px; text-align: right;" id="th-bill-amt">BILL TOTAL (₹)</th>
                                    <th style="width: 125px; text-align: right;">U-B TOTAL (₹)</th>
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
                                                        {{ $itm->name }} ({{ $itm->code }}) - Stock: {{ number_format($itm->current_stock, 2) }} {{ $itm->unit }}
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
                                                        $isSelected = $u->matchesValue($rU);
                                                    @endphp
                                                    <option value="{{ $uVal }}"
                                                            data-code="{{ $u->code }}"
                                                            data-name="{{ $u->name }}"
                                                            data-synonyms="{{ $u->synonyms }}"
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
                                            <input type="number" step="0.01" min="0" name="items[{{ $idx }}][bill_rate]" class="form-control row-bill-rate font-monospace" style="text-align: right;" value="{{ number_format((float)($row['bill_rate'] ?? 0), 2, '.', '') }}" required oninput="calcRow(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[{{ $idx }}][ub_rate]" class="form-control row-ub-rate font-monospace" style="text-align: right;" value="{{ number_format((float)($row['ub_rate'] ?? 0), 2, '.', '') }}" oninput="calcRow(this)">
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Unified Action & Totals Toolbar -->
                    <div class="erp-table-action-bar">
                        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-outline erp-btn-add-row" onclick="addSalesRow()">
                                <i class="fa-solid fa-plus me-1"></i> Add Another Item Row
                            </button>
                            <span class="erp-table-action-hint">
                                <i class="fa-solid fa-calculator me-1" style="color: #5B841E;"></i>
                                <span id="tax-math-hint">
                                    {{ old('is_gst_inclusive', $sale->is_gst_inclusive) ? 'GST-Inclusive: Tax extracted from Bill Rate without double counting' : 'Exclusive: Bill Rate × Net Wt + GST' }} &bull; <strong>U-B Total</strong> = Net Wt &times; U-B Rate
                                </span>
                            </span>
                        </div>

                        <div class="erp-table-totals-grid">
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Total Outward Wt</span>
                                <span class="erp-table-total-val font-monospace" id="footer-total-qty">0.000</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">Taxable Subtotal</span>
                                <span class="erp-table-total-val font-monospace" id="footer-bill-subtotal">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label">GST Tax</span>
                                <span class="erp-table-total-val font-monospace text-muted" id="footer-tax-total">₹0.00</span>
                            </div>
                            <div class="erp-table-total-item">
                                <span class="erp-table-total-label" style="color: #2563EB;">Freight / Cartage</span>
                                <span class="erp-table-total-val font-monospace" style="color: #2563EB;" id="footer-freight-total">₹0.00</span>
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

            <!-- 4. Settlement & Consignment Notes -->
            <div class="card erp-form-section-card">
                <div class="erp-form-section-header">
                    <div class="erp-form-section-header-left">
                        <div class="erp-form-section-icon-box erp-form-icon-purple">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="erp-form-section-title">4. Settlement &amp; Consignment Notes</h3>
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
                                <button type="button" class="btn btn-link p-0 text-decoration-none" style="font-size: 0.74rem; color: #5B841E; font-weight: 700; cursor: pointer; border: none; background: none;" onclick="setPaidAmountToGrandTotal()" title="Fill with Total Grand Value">
                                    <i class="fa-solid fa-bolt me-1"></i> Match Total
                                </button>
                            </div>
                        </div>
                        <div class="erp-field-icon-wrap">
                            <i class="fa-solid fa-indian-rupee-sign erp-field-icon"></i>
                            <input type="number" step="0.01" min="0" name="paid_amount" id="field-paid-amount" class="form-control erp-field-input-iconified erp-field-input-mono font-weight-700" placeholder="0.00" value="{{ old('paid_amount', $sale->paid_amount) }}" oninput="onPaidAmountInput()">
                        </div>
                        <span class="erp-field-hint" id="paid-amount-hint">Payment received from buyer</span>
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

            <!-- 5. Grand Total Financial Summary & Action Toolbar -->
            <div class="erp-voucher-summary-card">
                <div class="erp-voucher-summary-metrics">
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Total Outward Wt</span>
                        <span class="erp-voucher-metric-val font-monospace" id="prev-net-wt">0.000 KG</span>
                    </div>
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Taxable Subtotal</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #2563EB;" id="prev-bill-sub">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label">Total GST Tax</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #2563EB;" id="prev-tax-amt">₹0.00</span>
                    </div>
                    <div class="erp-voucher-metric">
                        <span class="erp-voucher-metric-label" style="color: #2563EB;">Freight Charges</span>
                        <span class="erp-voucher-metric-val font-monospace" style="color: #2563EB;" id="prev-freight-amt">₹0.00</span>
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
                        <i class="fa-solid fa-xmark me-1"></i> Cancel Changes
                    </a>
                    <button type="submit" id="btn-submit-sales" class="btn btn-primary" style="padding: 0.65rem 1.85rem; font-weight: 700; font-size: 0.95rem; border-radius: 8px; box-shadow: 0 4px 14px rgba(91, 132, 30, 0.25);">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Sales Invoice
                    </button>
                </div>
            </div>

            <!-- Voucher Audit Trail -->
            <div class="card" style="padding: 1rem 1.5rem; border-radius: 12px; border: 1px solid #E2E8F0; background: #FFFFFF; font-size: 0.82rem; color: #64748B;">
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: center;">
                        <span><i class="fa-solid fa-clock-rotate-left text-primary me-1"></i> <strong>Voucher Audit Trail</strong> (#{{ $sale->id }} - {{ $sale->sale_no }})</span>
                        <span>Registered: <strong>{{ $sale->created_at->format('d M Y, h:i A') }}</strong></span>
                        <span>Last Modified: <strong>{{ $sale->updated_at->format('d M Y, h:i A') }}</strong></span>
                    </div>
                    <div>
                        <span class="badge" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; font-weight: 600; padding: 5px 12px; border-radius: 9999px;">
                            <i class="fa-solid fa-circle-check me-1"></i> {{ ucfirst($sale->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Danger Zone: Delete / Void Sales Entry -->
            <div class="card" style="margin-top: 1rem; border: 1px solid #FECACA; background: #FFF5F5; border-radius: 14px; padding: 1.25rem 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h4 style="color: #991B1B; font-size: 0.95rem; font-weight: 700; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone: Void Sales Invoice
                        </h4>
                        <p style="color: #B91C1C; font-size: 0.8rem; margin: 0;">
                            Permanently void this sales invoice. This action will restore product physical stock and reverse customer ledger debits.
                        </p>
                    </div>
                    <button type="button" class="btn" onclick="confirmDeleteVoucher()" style="background: #DC2626; color: #FFFFFF; font-weight: 600; font-size: 0.85rem; padding: 0.55rem 1.25rem; border-radius: 8px; border: none;">
                        <i class="fa-solid fa-trash-can me-1"></i> Void Sales Invoice
                    </button>
                </div>
            </div>

            <!-- Optional Hidden Elements for Script Safety -->
            <div style="display: none;">
                <span id="prev-bill-total"></span>
            </div>

        </div>
    </form>

    <form id="delete-voucher-form" action="{{ route('admin.transactions.sales-entry.destroy', $sale) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</section>

<!-- Dynamic Unit Master Options Template -->
<template id="unit-options-template">
    @foreach($units as $u)
        <option value="{{ $u->code ?: $u->name }}" data-code="{{ $u->code }}" data-name="{{ $u->name }}" data-synonyms="{{ $u->synonyms }}">
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
                code: "{{ $itm->code }}",
                name: "{{ addslashes($itm->name) }}",
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
        if (!cleanTarget) return;

        for (let i = 0; i < unitSelect.options.length; i++) {
            const opt = unitSelect.options[i];
            const optVal = (opt.value || '').trim().toUpperCase();
            const optText = (opt.text || '').trim().toUpperCase();
            const optCode = (opt.getAttribute('data-code') || '').trim().toUpperCase();
            const optName = (opt.getAttribute('data-name') || '').trim().toUpperCase();

            if (optVal === cleanTarget || optCode === cleanTarget || optName === cleanTarget || optText === cleanTarget) {
                unitSelect.selectedIndex = i;
                return;
            }
        }

        for (let i = 0; i < unitSelect.options.length; i++) {
            const opt = unitSelect.options[i];
            const synAttr = (opt.getAttribute('data-synonyms') || '').trim().toUpperCase();
            if (synAttr) {
                const synonyms = synAttr.split(',').map(s => s.trim());
                if (synonyms.includes(cleanTarget)) {
                    unitSelect.selectedIndex = i;
                    return;
                }
            }
        }

        const newOpt = new Option(cleanTarget, cleanTarget, true, true);
        newOpt.setAttribute('data-code', cleanTarget);
        newOpt.setAttribute('data-name', cleanTarget);
        unitSelect.add(newOpt);
        unitSelect.value = cleanTarget;
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

        if (item) {
            if (hsnInput) hsnInput.value = item.hsn;
            if (gstInput) gstInput.value = item.gst.toFixed(2);
            if (unitSelect) setSelectUnit(unitSelect, item.unit);
            if (billRateInput && (parseFloat(billRateInput.value) === 0 || !billRateInput.value)) {
                billRateInput.value = item.sale_rate.toFixed(2);
            }
            if (batchInput) batchInput.value = item.batch;

            if (stockBadge) {
                const origQty = parseFloat(row.querySelector('.row-original-qty')?.value || 0) || 0;
                const effectiveStock = item.stock + origQty;
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
        } else {
            if (stockBadge) {
                stockBadge.textContent = '--';
                stockBadge.style.background = '#F1F5F9';
                stockBadge.style.color = '#64748B';
                stockBadge.style.borderColor = '#E2E8F0';
            }
        }

        calcRow(selectEl);
    }

    function onTaxModeToggle(cb) {
        const isInclusive = cb.checked;
        const badge = document.getElementById('tax-mode-badge');
        const thRate = document.getElementById('th-bill-rate');
        const hint = document.getElementById('tax-math-hint');

        if (isInclusive) {
            if (badge) {
                badge.textContent = 'GST-Inclusive';
                badge.style.background = '#ECFDF5';
                badge.style.color = '#059669';
            }
            if (thRate) {
                thRate.innerHTML = 'BILL RATE (₹) <span style="font-size:0.7rem; color:#059669;">(Inc. GST)</span> <span class="text-danger">*</span>';
            }
            if (hint) {
                hint.innerHTML = '<strong>GST-Inclusive</strong>: Tax extracted from Bill Rate without double counting &bull; <strong>U-B Total</strong> = Net Wt &times; U-B Rate';
            }
        } else {
            if (badge) {
                badge.textContent = 'Exclusive';
                badge.style.background = '#E2E8F0';
                badge.style.color = '#475569';
            }
            if (thRate) {
                thRate.innerHTML = 'BILL RATE (₹) <span class="text-danger">*</span>';
            }
            if (hint) {
                hint.innerHTML = '<strong>Exclusive</strong>: Bill Rate &times; Net Wt + GST &bull; <strong>U-B Total</strong> = Net Wt &times; U-B Rate';
            }
        }

        document.querySelectorAll('#sales-items-body .item-row').forEach(row => {
            calcRow(row.querySelector('.row-qty'));
        });
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
        const isInclusive = document.getElementById('field-is-gst-inclusive')?.checked;

        const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
        const billRate = parseFloat(billRateInput ? billRateInput.value : 0) || 0;
        const ubRate = parseFloat(ubRateInput ? ubRateInput.value : 0) || 0;
        const gstPercent = parseFloat(gstInput ? gstInput.value : 0) || 0;
        const origQty = parseFloat(row.querySelector('.row-original-qty')?.value || 0) || 0;

        // Stock check with original quantity added back
        const effectiveStock = item ? (item.stock + origQty) : 0;
        if (item && qty > effectiveStock) {
            qtyInput.style.borderColor = '#DC2626';
            qtyInput.style.backgroundColor = '#FEF2F2';
            if (stockErr) {
                stockErr.style.display = 'block';
                stockErr.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Exceeds available stock (${effectiveStock.toFixed(2)} ${item.unit})`;
            }
        } else {
            if (qtyInput) {
                qtyInput.style.borderColor = '';
                qtyInput.style.backgroundColor = '';
            }
            if (stockErr) stockErr.style.display = 'none';
        }

        let lineBillTotal = 0;
        if (isInclusive) {
            lineBillTotal = qty * billRate;
        } else {
            const lineTaxable = qty * billRate;
            const lineTax = lineTaxable * (gstPercent / 100);
            lineBillTotal = lineTaxable + lineTax;
        }

        const ubAmt = qty * ubRate;

        if (billAmtSpan) billAmtSpan.textContent = '₹' + lineBillTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (ubAmtSpan) ubAmtSpan.textContent = '₹' + ubAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        updateLiveSummary();
    }

    function onPaymentTermsChange(sel) {
        const advGroup = document.getElementById('advance-amount-group');
        const advInput = document.getElementById('field-advance-amount');
        if (sel.value === 'Advance') {
            if (advGroup) advGroup.style.display = 'block';
            if (advInput && parseFloat(advInput.value) > 0) {
                onAdvanceAmountInput();
            }
        } else {
            if (advGroup) advGroup.style.display = 'none';
        }
    }

    function onAdvanceAmountInput() {
        const advInput = document.getElementById('field-advance-amount');
        const paidInput = document.getElementById('field-paid-amount');
        const paymentStatus = document.getElementById('field-payment-status');
        const advVal = parseFloat(advInput?.value) || 0;

        if (paidInput) {
            paidInput.value = advVal.toFixed(2);
        }

        const grandVal = typeof window.currentGrandTotal === 'number' ? window.currentGrandTotal : 0;
        if (paymentStatus) {
            if (advVal <= 0) {
                paymentStatus.value = 'unpaid';
            } else if (grandVal > 0 && advVal >= grandVal - 0.01) {
                paymentStatus.value = 'paid';
            } else {
                paymentStatus.value = 'partial';
            }
        }
        updateLiveSummary();
    }

    function updateLiveSummary() {
        let totalQty = 0;
        let totalTaxableSubtotal = 0;
        let totalTax = 0;
        let totalUB = 0;
        const isInclusive = document.getElementById('field-is-gst-inclusive')?.checked;

        document.querySelectorAll('#sales-items-body .item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.row-qty')?.value || 0) || 0;
            const billRate = parseFloat(row.querySelector('.row-bill-rate')?.value || 0) || 0;
            const ubRate = parseFloat(row.querySelector('.row-ub-rate')?.value || 0) || 0;
            const gst = parseFloat(row.querySelector('.row-gst')?.value || 0) || 0;

            let lineTaxable = 0;
            let lineTax = 0;

            if (isInclusive) {
                const lineTotal = qty * billRate;
                lineTaxable = lineTotal / (1 + (gst / 100));
                lineTax = lineTotal - lineTaxable;
            } else {
                lineTaxable = qty * billRate;
                lineTax = lineTaxable * (gst / 100);
            }

            const lineUB = qty * ubRate;

            totalQty += qty;
            totalTaxableSubtotal += lineTaxable;
            totalTax += lineTax;
            totalUB += lineUB;
        });

        const freightAmount = parseFloat(document.getElementById('field-freight-amount')?.value || 0) || 0;
        const officialBillTotal = totalTaxableSubtotal + totalTax;
        const grandTotal = officialBillTotal + totalUB + freightAmount;
        window.currentGrandTotal = grandTotal;

        // Update footer totals
        document.getElementById('footer-total-qty').textContent = totalQty.toFixed(3);
        document.getElementById('footer-bill-subtotal').textContent = '₹' + totalTaxableSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-tax-total').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        const footerFreight = document.getElementById('footer-freight-total');
        if (footerFreight) {
            footerFreight.textContent = '₹' + freightAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        document.getElementById('footer-ub-total').textContent = '₹' + totalUB.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('footer-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Calculate Paid & Pending Receivable
        const paidInput = document.getElementById('field-paid-amount');
        const paidVal = parseFloat(paidInput?.value) || 0;
        const pendingAmount = Math.max(0, grandTotal - paidVal);

        const pendingField = document.getElementById('field-pending-amount');
        const pendingIcon = document.getElementById('pending-amount-icon');
        const pendingHint = document.getElementById('pending-amount-hint');
        if (pendingField) {
            pendingField.value = '₹' + pendingAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            if (pendingAmount <= 0.001) {
                pendingField.style.color = '#059669';
                pendingField.style.background = '#ECFDF5';
                pendingField.style.borderColor = '#A7F3D0';
                if (pendingIcon) {
                    pendingIcon.className = 'fa-solid fa-circle-check erp-field-icon';
                    pendingIcon.style.color = '#059669';
                }
                if (pendingHint) {
                    pendingHint.innerHTML = '<span style="color: #059669; font-weight: 600;"><i class="fa-solid fa-check me-1"></i>No pending balance (Fully Cleared)</span>';
                }
            } else {
                pendingField.style.color = '#DC2626';
                pendingField.style.background = '#FEF2F2';
                pendingField.style.borderColor = '#FECACA';
                if (pendingIcon) {
                    pendingIcon.className = 'fa-solid fa-hourglass-half erp-field-icon';
                    pendingIcon.style.color = '#DC2626';
                }
                if (pendingHint) {
                    pendingHint.innerHTML = '<span style="color: #DC2626; font-weight: 600;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Outstanding balance receivable from buyer</span>';
                }
            }
        }

        const footerPending = document.getElementById('footer-pending-total');
        if (footerPending) {
            footerPending.textContent = '₹' + pendingAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            footerPending.style.color = pendingAmount <= 0.001 ? '#059669' : '#DC2626';
        }

        const summaryPaid = document.getElementById('summary-paid-amount');
        if (summaryPaid) {
            summaryPaid.textContent = '₹' + paidVal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        const summaryPending = document.getElementById('summary-pending-amount');
        if (summaryPending) {
            summaryPending.textContent = '₹' + pendingAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            summaryPending.style.color = pendingAmount <= 0.001 ? '#059669' : '#DC2626';
        }

        // Live preview metrics
        document.getElementById('prev-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-net-wt').textContent = totalQty.toFixed(3) + ' KG';
        document.getElementById('prev-bill-sub').textContent = '₹' + totalTaxableSubtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('prev-tax-amt').textContent = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        
        const prevFreightEl = document.getElementById('prev-freight-amt');
        if (prevFreightEl) {
            prevFreightEl.textContent = '₹' + freightAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        document.getElementById('prev-ub-total').textContent = '₹' + totalUB.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function setPaidAmountToGrandTotal() {
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
            toastr.success(`Received Amount set to Total Grand Value: ₹${val.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
        }
        updateLiveSummary();
    }

    function onPaymentStatusChange() {
        const status = document.getElementById('field-payment-status').value;
        const paidInput = document.getElementById('field-paid-amount');
        const val = typeof window.currentGrandTotal === 'number' ? window.currentGrandTotal : 0;

        if (status === 'paid' && paidInput) {
            paidInput.value = val > 0 ? val.toFixed(2) : '0.00';
        } else if (status === 'unpaid' && paidInput) {
            paidInput.value = '0.00';
        }
        updateLiveSummary();
    }

    function onPaidAmountInput() {
        const paidInput = document.getElementById('field-paid-amount');
        const paymentStatus = document.getElementById('field-payment-status');
        if (!paymentStatus || !paidInput) return;

        const paidVal = parseFloat(paidInput.value) || 0;
        const grandVal = typeof window.currentGrandTotal === 'number' ? window.currentGrandTotal : 0;

        if (paidVal <= 0) {
            paymentStatus.value = 'unpaid';
        } else if (grandVal > 0 && paidVal >= grandVal - 0.01) {
            paymentStatus.value = 'paid';
        } else {
            paymentStatus.value = 'partial';
        }
        updateLiveSummary();
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
                            {{ $itm->name }} ({{ $itm->code }}) - Stock: {{ number_format($itm->current_stock, 2) }} {{ $itm->unit }}
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
            if (!selectEl || !selectEl.value) return;

            const itemId = selectEl.value;
            const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
            const item = availableItems.find(i => i.id == itemId);
            const origQty = parseFloat(row.querySelector('.row-original-qty')?.value || 0) || 0;

            if (item) {
                itemTotals[itemId] = (itemTotals[itemId] || 0) + qty;
                const effectiveStock = item.stock + origQty;
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
            const msg = `Insufficient inward stock for '${stockViolation.item.name}' (${stockViolation.item.code})!\n\nAvailable stock: ${stockViolation.available.toFixed(2)} ${stockViolation.item.unit}\nRequested outward: ${stockViolation.req.toFixed(2)} ${stockViolation.item.unit}\n\nYou cannot dispatch more than available inward stock!`;
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

    function confirmDeleteVoucher() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Void Sales Invoice?',
                html: 'Are you sure you want to permanently void sales invoice <strong>#{{ $sale->sale_no }}</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">Outward inventory stock will be restored and customer balance reversed.</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Void Invoice',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-voucher-form').submit();
                }
            });
        } else {
            if (confirm('Are you sure you want to void sales invoice #{{ $sale->sale_no }}? Stock will be restored.')) {
                document.getElementById('delete-voucher-form').submit();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#sales-items-body .item-row').forEach(row => {
            const sel = row.querySelector('.erp-item-select');
            if (sel && sel.value) {
                onItemSelect(sel);
            }
        });
        const gstInc = document.getElementById('field-is-gst-inclusive');
        if (gstInc && gstInc.checked) {
            onTaxModeToggle(gstInc);
        }
        updateLiveSummary();
    });
</script>
@endpush
@endsection
