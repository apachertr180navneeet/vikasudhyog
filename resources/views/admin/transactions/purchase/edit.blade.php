@extends('admin.layouts.app')

@section('title', 'Edit Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<div class="erp-module-wrapper">
    <!-- Top Bar & Breadcrumb -->
    <div class="erp-header-toolbar mb-4">
        <div class="erp-breadcrumb-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.purchase-entry') }}">Purchase Entry</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit #{{ $purchase->purchase_no }}</li>
                </ol>
            </nav>
            <h1 class="erp-page-title">Edit Purchase Entry #{{ $purchase->purchase_no }}</h1>
            <p class="erp-page-desc">Modify vendor invoice details, rates, and stock inward items</p>
        </div>
        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.purchase-entry.show', $purchase) }}" class="erp-btn-outline me-2">
                <i class="fa-solid fa-eye"></i> View 360
            </a>
            <a href="{{ route('admin.transactions.purchase-entry') }}" class="erp-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Purchases
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any())
        <div class="alert alert-danger erp-alert alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.transactions.purchase-entry.update', $purchase) }}" id="purchase-edit-form">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Form Sections -->
            <div class="col-lg-8">
                <!-- Section 1: Voucher & Supplier Details -->
                <div class="card erp-form-section-card mb-4">
                    <div class="erp-section-header">
                        <div class="erp-section-icon" style="background: rgba(91, 132, 30, 0.1); color: #5B841E;">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="erp-section-title">Purchase Voucher Details</h3>
                            <p class="erp-section-desc">Supplier invoice coordinates, transport vehicle, and vendor profile</p>
                        </div>
                    </div>

                    <div class="erp-section-body">
                        <div class="row g-3">
                            <!-- Purchase Voucher No (Readonly) -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Voucher No</label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                    <input type="text" class="form-control erp-field-input font-monospace font-weight-bold" value="{{ $purchase->purchase_no }}" readonly>
                                </div>
                            </div>

                            <!-- Invoice Date -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Invoice Date <span class="text-danger">*</span></label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                    <input type="date" name="invoice_date" id="field-invoice-date" class="form-control erp-field-input" value="{{ old('invoice_date', $purchase->invoice_date->format('Y-m-d')) }}" required onchange="updateLiveSummary()">
                                </div>
                            </div>

                            <!-- Supplier Bill / Inv No -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Supplier Invoice No</label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-file-invoice erp-field-icon"></i>
                                    <input type="text" name="invoice_no" id="field-invoice-no" class="form-control erp-field-input font-monospace" placeholder="Enter Invoice No" value="{{ old('invoice_no', $purchase->invoice_no) }}" oninput="updateLiveSummary()">
                                </div>
                            </div>

                            <!-- Vendor / Supplier Select -->
                            <div class="col-md-6">
                                <label class="erp-field-label">Vendor / Supplier <span class="text-danger">*</span></label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-truck-field erp-field-icon"></i>
                                    <select name="vendor_id" id="field-vendor-id" class="form-select erp-field-input" required onchange="onVendorChange(this)">
                                        @foreach($vendors as $vnd)
                                            <option value="{{ $vnd->id }}" data-name="{{ $vnd->name }}" {{ old('vendor_id', $purchase->vendor_id) == $vnd->id ? 'selected' : '' }}>
                                                {{ $vnd->name }} ({{ $vnd->city ?? 'Rajasthan' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Broker / Agent Select -->
                            <div class="col-md-6">
                                <label class="erp-field-label">Broker / Agent <span class="text-muted">(Optional)</span></label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-handshake erp-field-icon"></i>
                                    <select name="broker_id" id="field-broker-id" class="form-select erp-field-input" onchange="updateLiveSummary()">
                                        <option value="">None / Direct</option>
                                        @foreach($brokers as $brk)
                                            <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" {{ old('broker_id', $purchase->broker_id) == $brk->id ? 'selected' : '' }}>
                                                {{ $brk->name }} ({{ $brk->city ?? 'Sojat' }} - {{ $brk->commission_rate }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Order Type -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Order Urgency / Type <span class="text-danger">*</span></label>
                                <select name="order_type" id="field-order-type" class="form-select erp-field-input" required onchange="updateLiveSummary()">
                                    <option value="Medium" {{ old('order_type', $purchase->order_type) == 'Medium' ? 'selected' : '' }}>Medium (Standard)</option>
                                    <option value="Urgent" {{ old('order_type', $purchase->order_type) == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                    <option value="Fast" {{ old('order_type', $purchase->order_type) == 'Fast' ? 'selected' : '' }}>Fast</option>
                                    <option value="Ready Delivery" {{ old('order_type', $purchase->order_type) == 'Ready Delivery' ? 'selected' : '' }}>Ready Delivery</option>
                                </select>
                            </div>

                            <!-- Vehicle No -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Vehicle / Transport No</label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-truck-moving erp-field-icon"></i>
                                    <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input font-monospace" placeholder="Enter Vehicle No" value="{{ old('vehicle_no', $purchase->vehicle_no) }}">
                                </div>
                            </div>

                            <!-- Payment Terms -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Payment Terms</label>
                                <select name="payment_terms" class="form-select erp-field-input">
                                    <option value="Cash" {{ old('payment_terms', $purchase->payment_terms) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="15 Days" {{ old('payment_terms', $purchase->payment_terms) == '15 Days' ? 'selected' : '' }}>Credit 15 Days</option>
                                    <option value="30 Days" {{ old('payment_terms', $purchase->payment_terms) == '30 Days' ? 'selected' : '' }}>Credit 30 Days</option>
                                    <option value="45 Days" {{ old('payment_terms', $purchase->payment_terms) == '45 Days' ? 'selected' : '' }}>Credit 45 Days</option>
                                    <option value="Bank Transfer" {{ old('payment_terms', $purchase->payment_terms) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer / RTGS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Line Items Dynamic Table -->
                <div class="card erp-form-section-card mb-4">
                    <div class="erp-section-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="erp-section-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="erp-section-title">Purchase Line Items</h3>
                                <p class="erp-section-desc">Raw materials, herbal powders, packaging items with dual rates</p>
                            </div>
                        </div>
                        <button type="button" class="erp-btn-outline erp-btn-sm" onclick="addPurchaseRow()">
                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                        </button>
                    </div>

                    <div class="erp-section-body p-0">
                        <div class="table-responsive">
                            <table class="table erp-line-items-table mb-0" id="items-table">
                                <thead style="background: #F8FAFC;">
                                    <tr>
                                        <th style="width: 40px; text-align: center;">#</th>
                                        <th style="min-width: 220px;">Product Item <span class="text-danger">*</span></th>
                                        <th style="width: 110px;">Batch No</th>
                                        <th style="width: 90px;">HSN</th>
                                        <th style="width: 85px;">Unit</th>
                                        <th style="width: 105px;">Quantity <span class="text-danger">*</span></th>
                                        <th style="width: 110px;">Actual Rate (₹)</th>
                                        <th style="width: 110px;">Bill Rate (₹) <span class="text-danger">*</span></th>
                                        <th style="width: 105px;">UB Rate (₹)</th>
                                        <th style="width: 80px;">GST %</th>
                                        <th style="width: 115px; text-align: right;">Bill Amt</th>
                                        <th style="width: 115px; text-align: right;">UB Amt</th>
                                        <th style="width: 45px; text-align: center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="purchase-items-body">
                                    @foreach($purchase->items as $idx => $item)
                                        <tr class="item-row">
                                            <td class="text-center row-sno font-weight-600 font-monospace text-muted">{{ $idx + 1 }}</td>
                                            <td>
                                                <select name="items[{{ $idx }}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
                                                    @foreach($items as $itm)
                                                        <option value="{{ $itm->id }}"
                                                                data-code="{{ $itm->code }}"
                                                                data-hsn="{{ $itm->hsn_code }}"
                                                                data-unit="{{ $itm->unit }}"
                                                                data-gst="{{ $itm->gst_rate }}"
                                                                data-purchase-rate="{{ $itm->purchase_rate }}"
                                                                data-batch="{{ $itm->batch_no }}"
                                                                {{ $item->item_id == $itm->id ? 'selected' : '' }}>
                                                            {{ $itm->name }} ({{ $itm->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][batch_no]" class="form-control erp-field-input-sm row-batch font-monospace" placeholder="Enter Batch" value="{{ $item->batch_no }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control erp-field-input-sm row-hsn font-monospace" placeholder="Enter HSN" value="{{ $item->hsn_code }}">
                                            </td>
                                            <td>
                                                <input type="text" name="items[{{ $idx }}][unit]" class="form-control erp-field-input-sm row-unit" value="{{ $item->unit }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="any" min="0.001" name="items[{{ $idx }}][quantity]" class="form-control erp-field-input-sm row-qty font-monospace" value="{{ (float)$item->quantity }}" required oninput="calcRow(this)">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][actual_rate]" class="form-control erp-field-input-sm row-actual font-monospace" value="{{ (float)$item->actual_rate }}" required oninput="calcRow(this, 'actual')">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][bill_rate]" class="form-control erp-field-input-sm row-bill-rate font-monospace" value="{{ (float)$item->bill_rate }}" required oninput="calcRow(this, 'bill')">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][ub_rate]" class="form-control erp-field-input-sm row-ub-rate font-monospace" value="{{ (float)$item->ub_rate }}" oninput="calcRow(this, 'ub')">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][gst_percent]" class="form-control erp-field-input-sm row-gst font-monospace" value="{{ (float)$item->gst_percent }}" oninput="calcRow(this)">
                                            </td>
                                            <td style="text-align: right;">
                                                <span class="font-monospace text-dark font-weight-600 row-bill-amt">₹{{ number_format($item->bill_amount, 2) }}</span>
                                            </td>
                                            <td style="text-align: right;">
                                                <span class="font-monospace font-weight-600 row-ub-amt" style="color: #D97706;">₹{{ number_format($item->under_amount, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm text-danger p-0 delete-row-btn" onclick="removeRow(this)" title="Remove item">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Notes & Logistics -->
                <div class="card erp-form-section-card">
                    <div class="erp-section-header">
                        <div class="erp-section-icon" style="background: rgba(100, 116, 139, 0.1); color: #475569;">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="erp-section-title">Consignment Notes & Payment Remarks</h3>
                            <p class="erp-section-desc">Warehouse location, quality grade notes, or Mandi transport terms</p>
                        </div>
                    </div>

                    <div class="erp-section-body">
                        <textarea name="notes" class="form-control erp-field-input" rows="3" placeholder="Enter consignment notes, mandi moisture deduction or unloading remarks...">{{ old('notes', $purchase->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Preview & Actions -->
            <div class="col-lg-4">
                <div class="erp-sticky-sidebar">
                    <!-- Live Real-Time Preview Card -->
                    <div class="card erp-preview-card mb-4">
                        <div class="erp-preview-header">
                            <div class="d-flex align-items-center">
                                <div class="erp-preview-avatar me-2" id="preview-avatar">
                                    {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 2)) }}
                                </div>
                                <div>
                                    <h4 class="erp-preview-title" id="preview-vendor-name">{{ $purchase->vendor->name ?? 'Direct Vendor' }}</h4>
                                    <span class="erp-preview-code font-monospace" id="preview-voucher-no">{{ $purchase->purchase_no }}</span>
                                </div>
                            </div>
                            <span class="badge {{ $purchase->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-1" style="font-size: 0.72rem; font-weight: 700;">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>

                        <div class="erp-preview-body">
                            <!-- Invoice Meta Tags -->
                            <div class="erp-preview-meta-grid mb-3">
                                <div>
                                    <span class="erp-meta-label">Bill / Inv No:</span>
                                    <strong class="erp-meta-value font-monospace" id="preview-inv-no">{{ $purchase->invoice_no ?? 'INV-PENDING' }}</strong>
                                </div>
                                <div>
                                    <span class="erp-meta-label">Invoice Date:</span>
                                    <strong class="erp-meta-value" id="preview-date">{{ $purchase->invoice_date->format('d M Y') }}</strong>
                                </div>
                                <div>
                                    <span class="erp-meta-label">Order Type:</span>
                                    <strong class="erp-meta-value" id="preview-order-type">{{ $purchase->order_type }}</strong>
                                </div>
                                <div>
                                    <span class="erp-meta-label">Items Count:</span>
                                    <strong class="erp-meta-value font-monospace" id="preview-items-count">{{ count($purchase->items) }} Item(s)</strong>
                                </div>
                            </div>

                            <hr style="border-color: #E2E8F0; margin: 0.75rem 0;">

                            <!-- Financial Breakdown -->
                            <div class="erp-calc-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Bill Subtotal:</span>
                                    <strong class="font-monospace text-dark" id="preview-subtotal">₹{{ number_format($purchase->subtotal, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">GST Tax:</span>
                                    <strong class="font-monospace text-muted" id="preview-tax">₹{{ number_format($purchase->tax_amount, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <span style="color: #2563EB; font-weight: 600;">Official Billing Total:</span>
                                    <strong class="font-monospace" style="color: #2563EB;" id="preview-bill-total">₹{{ number_format($purchase->bill_total, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span style="color: #D97706; font-weight: 600;">Under-Billing (U_B):</span>
                                    <strong class="font-monospace" style="color: #D97706;" id="preview-ub-total">₹{{ number_format($purchase->under_billing_total, 2) }}</strong>
                                </div>

                                <div class="erp-grand-total-box p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark font-weight-700" style="font-size: 0.95rem;">Grand Total:</span>
                                        <h3 class="font-monospace font-weight-bold mb-0" style="color: #059669;" id="preview-grand-total">₹{{ number_format($purchase->grand_total, 2) }}</h3>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                        Total payable to vendor ledger
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Trail Card -->
                    <div class="card erp-form-section-card mb-4">
                        <div class="erp-section-header p-3">
                            <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i> Voucher Audit Trail</h4>
                        </div>
                        <div class="erp-section-body p-3 font-size-sm">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Created:</span>
                                <span class="font-monospace text-dark">{{ $purchase->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Last Modified:</span>
                                <span class="font-monospace text-dark">{{ $purchase->updated_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Stock Status:</span>
                                <span class="text-success font-weight-600"><i class="fa-solid fa-check-double me-1"></i> Stock Reconciled</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Actions Card -->
                    <div class="card erp-sidebar-actions-card mb-4">
                        <div class="card-body">
                            <button type="submit" class="erp-btn-primary w-100 mb-2 py-2">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Purchase Entry
                            </button>
                            <a href="{{ route('admin.transactions.purchase-entry') }}" class="erp-btn-outline w-100 text-center py-2">
                                <i class="fa-solid fa-xmark me-1"></i> Cancel & Return
                            </a>
                        </div>
                    </div>

                    <!-- Danger Zone Card -->
                    <div class="card border-danger-subtle bg-danger-subtle p-3 rounded">
                        <h6 class="text-danger font-weight-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Danger Zone</h6>
                        <p class="text-muted font-size-sm mb-3">Deleting this voucher will reverse all line item stock inward quantities and deduct the balance from vendor ledger.</p>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="if(confirm('Are you sure you want to delete purchase voucher #{{ $purchase->purchase_no }}?')) document.getElementById('delete-purchase-form').submit();">
                            <i class="fa-solid fa-trash me-1"></i> Delete Voucher
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.transactions.purchase-entry.destroy', $purchase) }}" id="delete-purchase-form" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<!-- Item Row Template Script -->
<script>
    let rowIndex = {{ count($purchase->items) }};

    function addPurchaseRow() {
        const tbody = document.getElementById('purchase-items-body');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-sno font-weight-600 font-monospace text-muted">${rowIndex + 1}</td>
            <td>
                <select name="items[${rowIndex}][item_id]" class="form-select erp-item-select" required onchange="onItemSelect(this)">
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
                </select>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][batch_no]" class="form-control erp-field-input-sm row-batch font-monospace" placeholder="Enter Batch">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][hsn_code]" class="form-control erp-field-input-sm row-hsn font-monospace" placeholder="Enter HSN">
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][unit]" class="form-control erp-field-input-sm row-unit" value="KG" required>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="items[${rowIndex}][quantity]" class="form-control erp-field-input-sm row-qty font-monospace" value="10" required oninput="calcRow(this)">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][actual_rate]" class="form-control erp-field-input-sm row-actual font-monospace" value="100.00" required oninput="calcRow(this, 'actual')">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][bill_rate]" class="form-control erp-field-input-sm row-bill-rate font-monospace" value="50.00" required oninput="calcRow(this, 'bill')">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][ub_rate]" class="form-control erp-field-input-sm row-ub-rate font-monospace" value="50.00" oninput="calcRow(this, 'ub')">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][gst_percent]" class="form-control erp-field-input-sm row-gst font-monospace" value="5.00" oninput="calcRow(this)">
            </td>
            <td style="text-align: right;">
                <span class="font-monospace text-dark font-weight-600 row-bill-amt">₹525.00</span>
            </td>
            <td style="text-align: right;">
                <span class="font-monospace font-weight-600 row-ub-amt" style="color: #D97706;">₹500.00</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger p-0 delete-row-btn" onclick="removeRow(this)" title="Remove item">
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
        const rows = document.querySelectorAll('#purchase-items-body .item-row');
        if (rows.length <= 1) {
            alert('A purchase entry must contain at least one line item.');
            return;
        }
        btn.closest('tr').remove();
        updateRowNumbers();
        updateLiveSummary();
    }

    function updateRowNumbers() {
        document.querySelectorAll('#purchase-items-body .item-row').forEach((tr, index) => {
            tr.querySelector('.row-sno').innerText = index + 1;
        });
    }

    function onItemSelect(selectEl) {
        const tr = selectEl.closest('tr');
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) return;

        const hsn = selectedOpt.getAttribute('data-hsn') || '1404';
        const unit = selectedOpt.getAttribute('data-unit') || 'KG';
        const gst = parseFloat(selectedOpt.getAttribute('data-gst')) || 5.0;
        const actualRate = parseFloat(selectedOpt.getAttribute('data-purchase-rate')) || 100.0;
        const batch = selectedOpt.getAttribute('data-batch') || ('BAT-' + new Date().getFullYear() + '-01');

        tr.querySelector('.row-hsn').value = hsn;
        tr.querySelector('.row-unit').value = unit;
        tr.querySelector('.row-gst').value = gst;
        tr.querySelector('.row-batch').value = batch;

        const billRate = Math.round(actualRate * 0.5 * 100) / 100;
        const ubRate = Math.round((actualRate - billRate) * 100) / 100;

        tr.querySelector('.row-actual').value = actualRate.toFixed(2);
        tr.querySelector('.row-bill-rate').value = billRate.toFixed(2);
        tr.querySelector('.row-ub-rate').value = ubRate.toFixed(2);

        calcRow(selectEl);
    }

    function calcRow(el, source) {
        const tr = el.closest('tr');
        const qty = parseFloat(tr.querySelector('.row-qty').value) || 0;
        let actual = parseFloat(tr.querySelector('.row-actual').value) || 0;
        let bill = parseFloat(tr.querySelector('.row-bill-rate').value) || 0;
        let ub = parseFloat(tr.querySelector('.row-ub-rate').value) || 0;
        const gst = parseFloat(tr.querySelector('.row-gst').value) || 0;

        if (source === 'actual' || source === 'bill') {
            ub = Math.max(0, actual - bill);
            tr.querySelector('.row-ub-rate').value = ub.toFixed(2);
        } else if (source === 'ub') {
            actual = bill + ub;
            tr.querySelector('.row-actual').value = actual.toFixed(2);
        }

        const billSub = qty * bill;
        const billTax = billSub * (gst / 100);
        const billAmt = billSub + billTax;
        const ubAmt = qty * ub;

        tr.querySelector('.row-bill-amt').innerText = '₹' + billAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        tr.querySelector('.row-ub-amt').innerText = '₹' + ubAmt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        updateLiveSummary();
    }

    function onVendorChange(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const vName = selectedOpt ? selectedOpt.getAttribute('data-name') : 'Direct Vendor';
        document.getElementById('preview-vendor-name').innerText = vName || 'Direct Vendor';
        document.getElementById('preview-avatar').innerText = (vName ? vName.substring(0, 2) : 'V').toUpperCase();
        updateLiveSummary();
    }

    function updateLiveSummary() {
        let subtotal = 0;
        let totalTax = 0;
        let totalUB = 0;
        let totalItems = 0;

        document.querySelectorAll('#purchase-items-body .item-row').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.row-qty').value) || 0;
            const bill = parseFloat(tr.querySelector('.row-bill-rate').value) || 0;
            const ub = parseFloat(tr.querySelector('.row-ub-rate').value) || 0;
            const gst = parseFloat(tr.querySelector('.row-gst').value) || 0;

            const lineSub = qty * bill;
            const lineTax = lineSub * (gst / 100);
            const lineUB = qty * ub;

            subtotal += lineSub;
            totalTax += lineTax;
            totalUB += lineUB;
            totalItems++;
        });

        const billTotal = subtotal + totalTax;
        const grandTotal = billTotal + totalUB;

        document.getElementById('preview-subtotal').innerText = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('preview-tax').innerText = '₹' + totalTax.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('preview-bill-total').innerText = '₹' + billTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('preview-ub-total').innerText = '₹' + totalUB.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('preview-grand-total').innerText = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const invNo = document.getElementById('field-invoice-no').value;
        document.getElementById('preview-inv-no').innerText = invNo ? invNo : 'INV-PENDING';

        const invDate = document.getElementById('field-invoice-date').value;
        if (invDate) {
            const d = new Date(invDate);
            document.getElementById('preview-date').innerText = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }

        document.getElementById('preview-order-type').innerText = document.getElementById('field-order-type').value;
        document.getElementById('preview-items-count').innerText = totalItems + ' Item(s)';
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateLiveSummary();
    });
</script>
@endsection
