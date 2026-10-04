@extends('admin.layouts.app')

@section('title', 'New WB Purchase Entry - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<div class="erp-module-wrapper">
    <!-- Top Bar & Breadcrumb -->
    <div class="erp-header-toolbar mb-4">
        <div class="erp-breadcrumb-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.wb-purchase-entry') }}">WB Purchase Entry</a></li>
                    <li class="breadcrumb-item active" aria-current="page">New WB Entry</li>
                </ol>
            </nav>
            <h1 class="erp-page-title">New WB Purchase Entry (Without Bill)</h1>
            <p class="erp-page-desc">Record direct mandi cash purchase, weighbridge gross/tare weighments, and moisture tare deductions</p>
        </div>
        <div class="erp-header-actions">
            <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="erp-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to WB Slips
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

    <form method="POST" action="{{ route('admin.transactions.wb-purchase-entry.store') }}" id="wb-purchase-form">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Form Sections -->
            <div class="col-lg-8">
                <!-- Section 1: Slip Coordinates & Supplier -->
                <div class="card erp-form-section-card mb-4">
                    <div class="erp-section-header">
                        <div class="erp-section-icon" style="background: rgba(91, 132, 30, 0.1); color: #5B841E;">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="erp-section-title">Weighbridge Inward Coordinates</h3>
                            <p class="erp-section-desc">Slip identification, arrival date, farmer/vendor details, and vehicle</p>
                        </div>
                    </div>

                    <div class="erp-section-body">
                        <div class="row g-3">
                            <!-- WB Slip No -->
                            <div class="col-md-4">
                                <label class="erp-field-label">WB Slip No <span class="text-danger">*</span></label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-hashtag erp-field-icon"></i>
                                    <input type="text" name="slip_no" id="field-slip-no" class="form-control erp-field-input font-monospace font-weight-bold" value="{{ old('slip_no', $nextSlipNo) }}" readonly required>
                                </div>
                            </div>

                            <!-- Entry Date -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Entry Date <span class="text-danger">*</span></label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-calendar-day erp-field-icon"></i>
                                    <input type="date" name="entry_date" id="field-entry-date" class="form-control erp-field-input" value="{{ old('entry_date', date('Y-m-d')) }}" required onchange="updateLivePreview()">
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Payment Mode</label>
                                <select name="payment_mode" id="field-payment-mode" class="form-select erp-field-input" onchange="updateLivePreview()">
                                    <option value="Cash" {{ old('payment_mode') == 'Cash' ? 'selected' : '' }}>Mandi Cash</option>
                                    <option value="Bank Transfer" {{ old('payment_mode') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer / IMPS</option>
                                    <option value="Mandi Slip" {{ old('payment_mode') == 'Mandi Slip' ? 'selected' : '' }}>Mandi Slip / Voucher</option>
                                    <option value="Cheque" {{ old('payment_mode') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>

                            <!-- Vendor / Farmer Select -->
                            <div class="col-md-6">
                                <label class="erp-field-label">Vendor / Farmer Supplier <span class="text-danger">*</span></label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-user-tag erp-field-icon"></i>
                                    <select name="vendor_id" id="field-vendor-id" class="form-select erp-field-input" required onchange="onVendorChange(this)">
                                        <option value="">Select Supplier / Farmer...</option>
                                        @foreach($vendors as $vnd)
                                            <option value="{{ $vnd->id }}" data-name="{{ $vnd->name }}" data-city="{{ $vnd->city }}" {{ old('vendor_id') == $vnd->id ? 'selected' : '' }}>
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
                                    <select name="broker_id" id="field-broker-id" class="form-select erp-field-input" onchange="updateLivePreview()">
                                        <option value="">Direct Mandi Purchase (No Broker)</option>
                                        @foreach($brokers as $brk)
                                            <option value="{{ $brk->id }}" data-name="{{ $brk->name }}" {{ old('broker_id') == $brk->id ? 'selected' : '' }}>
                                                {{ $brk->name }} ({{ $brk->city ?? 'Sojat' }} - {{ $brk->commission_rate }}%)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Vehicle No -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Vehicle No / Trolley</label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-truck erp-field-icon"></i>
                                    <input type="text" name="vehicle_no" id="field-vehicle-no" class="form-control erp-field-input font-monospace" placeholder="Enter Vehicle No" value="{{ old('vehicle_no') }}" oninput="updateLivePreview()">
                                </div>
                            </div>

                            <!-- Driver Name -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Driver Name</label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-user erp-field-icon"></i>
                                    <input type="text" name="driver_name" id="field-driver-name" class="form-control erp-field-input" placeholder="Enter Driver Name" value="{{ old('driver_name') }}" oninput="updateLivePreview()">
                                </div>
                            </div>

                            <!-- Driver Phone -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Driver Phone</label>
                                <div class="erp-input-group-icon">
                                    <i class="fa-solid fa-phone erp-field-icon"></i>
                                    <input type="text" name="driver_phone" class="form-control erp-field-input font-monospace" placeholder="Enter Driver Phone" value="{{ old('driver_phone') }}">
                                </div>
                            </div>

                            <!-- Order Urgency -->
                            <div class="col-md-4">
                                <label class="erp-field-label">Consignment Urgency</label>
                                <select name="order_type" class="form-select erp-field-input">
                                    <option value="Medium">Medium (Regular)</option>
                                    <option value="Urgent">Urgent (Immediate Unload)</option>
                                    <option value="Fast">Fast</option>
                                    <option value="Ready Delivery">Ready Delivery</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Weighbridge Scale Tare & Net Weighments -->
                <div class="card erp-form-section-card mb-4">
                    <div class="erp-section-header">
                        <div class="erp-section-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563EB;">
                            <i class="fa-solid fa-scale-balanced"></i>
                        </div>
                        <div>
                            <h3 class="erp-section-title">Weighbridge Scale Readings (Kanta Weighment)</h3>
                            <p class="erp-section-desc">Gross truck weight, empty tare weight, and moisture/bag deduction in KG</p>
                        </div>
                    </div>

                    <div class="erp-section-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="erp-field-label">Gross Weight (KG)</label>
                                <input type="number" step="0.01" min="0" name="gross_weight" id="field-gross-weight" class="form-control erp-field-input font-monospace" placeholder="Enter Gross Weight" value="{{ old('gross_weight', '0.00') }}" oninput="calcWBWeights()">
                            </div>
                            <div class="col-md-3">
                                <label class="erp-field-label">Tare / Empty Weight (KG)</label>
                                <input type="number" step="0.01" min="0" name="tare_weight" id="field-tare-weight" class="form-control erp-field-input font-monospace" placeholder="Enter Tare Weight" value="{{ old('tare_weight', '0.00') }}" oninput="calcWBWeights()">
                            </div>
                            <div class="col-md-3">
                                <label class="erp-field-label">Deduction / Moisture (KG)</label>
                                <input type="number" step="0.01" min="0" name="deduction_weight" id="field-deduction-weight" class="form-control erp-field-input font-monospace" placeholder="Enter Deduction" value="{{ old('deduction_weight', '0.00') }}" oninput="calcWBWeights()">
                            </div>
                            <div class="col-md-3">
                                <label class="erp-field-label">Scale Net Weight (KG)</label>
                                <input type="number" step="0.01" min="0" name="net_weight" id="field-net-weight" class="form-control erp-field-input font-monospace font-weight-bold" style="background: #F1F5F9; color: #0F172A;" value="{{ old('net_weight', '0.00') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Line Items (Item, Quantity, WB Rate, Amount) -->
                <div class="card erp-form-section-card mb-4">
                    <div class="erp-section-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="erp-section-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                                <i class="fa-solid fa-boxes-stacked"></i>
                            </div>
                            <div>
                                <h3 class="erp-section-title">Raw Material Line Items</h3>
                                <p class="erp-section-desc">Item allocated, inward weight, and mandi cash rate</p>
                            </div>
                        </div>
                        <button type="button" class="erp-btn-outline erp-btn-sm" onclick="addWBItemRow()">
                            <i class="fa-solid fa-plus me-1"></i> Add Line Item
                        </button>
                    </div>

                    <div class="erp-section-body p-0">
                        <div class="table-responsive">
                            <table class="table erp-line-items-table mb-0" id="wb-items-table">
                                <thead style="background: #F8FAFC;">
                                    <tr>
                                        <th style="width: 40px; text-align: center;">#</th>
                                        <th style="min-width: 220px;">Product / Herb Item <span class="text-danger">*</span></th>
                                        <th style="width: 110px;">Batch No</th>
                                        <th style="width: 85px;">Unit</th>
                                        <th style="width: 120px;">Net Weight <span class="text-danger">*</span></th>
                                        <th style="width: 120px;">WB Rate (₹) <span class="text-danger">*</span></th>
                                        <th style="width: 130px; text-align: right;">Amount (₹)</th>
                                        <th style="width: 120px;">Remarks</th>
                                        <th style="width: 45px; text-align: center;"></th>
                                    </tr>
                                </thead>
                                <tbody id="wb-items-body">
                                    <tr class="item-row">
                                        <td class="text-center row-sno font-weight-600 font-monospace text-muted">1</td>
                                        <td>
                                            <select name="items[0][item_id]" class="form-select erp-item-select" required onchange="onWBItemSelect(this)">
                                                <option value="">Select Item...</option>
                                                @foreach($items as $itm)
                                                    <option value="{{ $itm->id }}"
                                                            data-code="{{ $itm->code }}"
                                                            data-unit="{{ $itm->unit }}"
                                                            data-rate="{{ $itm->purchase_rate }}"
                                                            data-batch="{{ $itm->batch_no }}">
                                                        {{ $itm->name }} ({{ $itm->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][batch_no]" class="form-control erp-field-input-sm row-batch font-monospace" placeholder="Enter Batch">
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][unit]" class="form-control erp-field-input-sm row-unit" value="KG" required>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0.001" name="items[0][quantity]" class="form-control erp-field-input-sm row-qty font-monospace font-weight-600" value="1000" required oninput="calcWBRow(this)">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="items[0][rate]" class="form-control erp-field-input-sm row-rate font-monospace font-weight-600" value="60.00" required oninput="calcWBRow(this)">
                                        </td>
                                        <td style="text-align: right;">
                                            <strong class="font-monospace text-dark row-amount" style="color: #D97706 !important;">₹60,000.00</strong>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][notes]" class="form-control erp-field-input-sm font-size-xs" placeholder="Bag notes">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm text-danger p-0 delete-row-btn" onclick="removeWBRow(this)" title="Remove item">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Remarks & Inward Yard Notes -->
                <div class="card erp-form-section-card">
                    <div class="erp-section-header">
                        <div class="erp-section-icon" style="background: rgba(100, 116, 139, 0.1); color: #475569;">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h3 class="erp-section-title">Consignment Notes & Mandi Remarks</h3>
                            <p class="erp-section-desc">Warehouse yard stack location, bag tare count, or moisture inspection</p>
                        </div>
                    </div>

                    <div class="erp-section-body">
                        <textarea name="notes" class="form-control erp-field-input" rows="3" placeholder="Enter consignment notes, weighbridge Dharam Kanta slip remarks...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Real-Time Preview & Actions -->
            <div class="col-lg-4">
                <div class="erp-sticky-sidebar">
                    <!-- Live Real-Time Preview Card -->
                    <div class="card erp-preview-card mb-4">
                        <div class="erp-preview-header">
                            <div class="d-flex align-items-center">
                                <div class="erp-preview-avatar me-2" id="preview-avatar">
                                    F
                                </div>
                                <div>
                                    <h4 class="erp-preview-title" id="preview-vendor-name">Direct Farmer / Vendor</h4>
                                    <span class="erp-preview-code font-monospace" id="preview-slip-no">{{ $nextSlipNo }}</span>
                                </div>
                            </div>
                            <span class="badge bg-warning-subtle text-warning-emphasis px-2 py-1" style="font-size: 0.72rem; font-weight: 700;">
                                Mandi Cash
                            </span>
                        </div>

                        <div class="erp-preview-body">
                            <!-- Slip Meta Info -->
                            <div class="erp-preview-meta-grid mb-3">
                                <div>
                                    <span class="erp-meta-label">Vehicle:</span>
                                    <strong class="erp-meta-value font-monospace" id="preview-vehicle">Trolley / Direct</strong>
                                </div>
                                <div>
                                    <span class="erp-meta-label">Driver:</span>
                                    <strong class="erp-meta-value" id="preview-driver">-</strong>
                                </div>
                                <div>
                                    <span class="erp-meta-label">Entry Date:</span>
                                    <strong class="erp-meta-value" id="preview-date">{{ date('d M Y') }}</strong>
                                </div>
                                <div>
                                    <span class="erp-meta-label">Payment Mode:</span>
                                    <strong class="erp-meta-value" id="preview-pay-mode">Mandi Cash</strong>
                                </div>
                            </div>

                            <hr style="border-color: #E2E8F0; margin: 0.75rem 0;">

                            <!-- Weight Breakdown -->
                            <div class="erp-calc-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Line Items:</span>
                                    <strong class="font-monospace text-dark" id="preview-items-count">1 Item(s)</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Scale Net Weight:</span>
                                    <strong class="font-monospace text-dark" id="preview-net-weight">1,000.00 KG</strong>
                                </div>

                                <div class="erp-grand-total-box p-3 rounded mt-3" style="background: #FFFBEB; border: 1px solid #FDE68A;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-dark font-weight-700" style="font-size: 0.95rem;">Total WB Amount:</span>
                                        <h3 class="font-monospace font-weight-bold mb-0" style="color: #D97706;" id="preview-total-amount">₹60,000.00</h3>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                        Cash / Unbilled mandi purchase liability
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Actions Card -->
                    <div class="card erp-sidebar-actions-card">
                        <div class="card-body">
                            <button type="submit" class="erp-btn-primary w-100 mb-2 py-2">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save WB Purchase Entry
                            </button>
                            <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="erp-btn-outline w-100 text-center py-2">
                                <i class="fa-solid fa-xmark me-1"></i> Cancel & Return
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    let wbRowIndex = 1;

    function addWBItemRow() {
        const tbody = document.getElementById('wb-items-body');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="text-center row-sno font-weight-600 font-monospace text-muted">${wbRowIndex + 1}</td>
            <td>
                <select name="items[${wbRowIndex}][item_id]" class="form-select erp-item-select" required onchange="onWBItemSelect(this)">
                    <option value="">Select Item...</option>
                    @foreach($items as $itm)
                        <option value="{{ $itm->id }}"
                                data-code="{{ $itm->code }}"
                                data-unit="{{ $itm->unit }}"
                                data-rate="{{ $itm->purchase_rate }}"
                                data-batch="{{ $itm->batch_no }}">
                            {{ $itm->name }} ({{ $itm->code }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="items[${wbRowIndex}][batch_no]" class="form-control erp-field-input-sm row-batch font-monospace" placeholder="Enter Batch">
            </td>
            <td>
                <input type="text" name="items[${wbRowIndex}][unit]" class="form-control erp-field-input-sm row-unit" value="KG" required>
            </td>
            <td>
                <input type="number" step="any" min="0.001" name="items[${wbRowIndex}][quantity]" class="form-control erp-field-input-sm row-qty font-monospace font-weight-600" value="100" required oninput="calcWBRow(this)">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${wbRowIndex}][rate]" class="form-control erp-field-input-sm row-rate font-monospace font-weight-600" value="60.00" required oninput="calcWBRow(this)">
            </td>
            <td style="text-align: right;">
                <strong class="font-monospace text-dark row-amount" style="color: #D97706 !important;">₹6,000.00</strong>
            </td>
            <td>
                <input type="text" name="items[${wbRowIndex}][notes]" class="form-control erp-field-input-sm font-size-xs" placeholder="Bag notes">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger p-0 delete-row-btn" onclick="removeWBRow(this)" title="Remove item">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        wbRowIndex++;
        updateWBRowNumbers();
        updateLivePreview();
    }

    function removeWBRow(btn) {
        const rows = document.querySelectorAll('#wb-items-body .item-row');
        if (rows.length <= 1) {
            alert('A WB purchase entry must contain at least one line item.');
            return;
        }
        btn.closest('tr').remove();
        updateWBRowNumbers();
        updateLivePreview();
    }

    function updateWBRowNumbers() {
        document.querySelectorAll('#wb-items-body .item-row').forEach((tr, index) => {
            tr.querySelector('.row-sno').innerText = index + 1;
        });
    }

    function onWBItemSelect(selectEl) {
        const tr = selectEl.closest('tr');
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) return;

        const unit = selectedOpt.getAttribute('data-unit') || 'KG';
        const rate = parseFloat(selectedOpt.getAttribute('data-rate')) || 60.0;
        const batch = selectedOpt.getAttribute('data-batch') || ('BAT-WB-' + new Date().getFullYear());

        tr.querySelector('.row-unit').value = unit;
        tr.querySelector('.row-rate').value = rate.toFixed(2);
        tr.querySelector('.row-batch').value = batch;

        calcWBRow(selectEl);
    }

    function calcWBRow(el) {
        const tr = el.closest('tr');
        const qty = parseFloat(tr.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(tr.querySelector('.row-rate').value) || 0;
        const amt = qty * rate;

        tr.querySelector('.row-amount').innerText = '₹' + amt.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        updateLivePreview();
    }

    function calcWBWeights() {
        const gross = parseFloat(document.getElementById('field-gross-weight').value) || 0;
        const tare = parseFloat(document.getElementById('field-tare-weight').value) || 0;
        const deduction = parseFloat(document.getElementById('field-deduction-weight').value) || 0;
        const net = Math.max(0, gross - tare - deduction);

        document.getElementById('field-net-weight').value = net.toFixed(2);
        updateLivePreview();
    }

    function onVendorChange(selectEl) {
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        const vName = selectedOpt ? selectedOpt.getAttribute('data-name') : 'Direct Farmer / Vendor';
        document.getElementById('preview-vendor-name').innerText = vName || 'Direct Farmer / Vendor';
        document.getElementById('preview-avatar').innerText = (vName ? vName.substring(0, 2) : 'F').toUpperCase();
        updateLivePreview();
    }

    function updateLivePreview() {
        let totalWeight = 0;
        let totalAmount = 0;
        let totalItems = 0;

        document.querySelectorAll('#wb-items-body .item-row').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.row-qty').value) || 0;
            const rate = parseFloat(tr.querySelector('.row-rate').value) || 0;
            totalWeight += qty;
            totalAmount += (qty * rate);
            totalItems++;
        });

        const scaleNet = parseFloat(document.getElementById('field-net-weight').value) || 0;
        const finalWeight = scaleNet > 0 ? scaleNet : totalWeight;

        document.getElementById('preview-net-weight').innerText = finalWeight.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' KG';
        document.getElementById('preview-total-amount').innerText = '₹' + Math.round(totalAmount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('preview-items-count').innerText = totalItems + ' Item(s)';

        const vehicle = document.getElementById('field-vehicle-no').value;
        document.getElementById('preview-vehicle').innerText = vehicle ? vehicle : 'Trolley / Direct';

        const driver = document.getElementById('field-driver-name').value;
        document.getElementById('preview-driver').innerText = driver ? driver : '-';

        const payMode = document.getElementById('field-payment-mode').value;
        document.getElementById('preview-pay-mode').innerText = payMode;

        const dateVal = document.getElementById('field-entry-date').value;
        if (dateVal) {
            const d = new Date(dateVal);
            document.getElementById('preview-date').innerText = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        calcWBRow(document.querySelector('.row-qty'));
    });
</script>
@endsection
