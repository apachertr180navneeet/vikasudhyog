@extends('admin.layouts.app')

@section('title', 'Purchase Voucher ' . $purchase->purchase_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-purchase')

@section('content')
<div class="erp-module-wrapper">
    <!-- Top Bar & Breadcrumbs -->
    <div class="erp-header-toolbar mb-4">
        <div class="erp-breadcrumb-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.purchase-entry') }}">Purchase Entry</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $purchase->purchase_no }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h1 class="erp-page-title mb-0">Purchase Voucher #{{ $purchase->purchase_no }}</h1>
                <span class="badge {{ $purchase->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1 font-weight-700 font-size-sm">
                    {{ ucfirst($purchase->status) }}
                </span>
            </div>
        </div>
        <div class="erp-header-actions">
            <button type="button" class="erp-btn-outline me-2" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print Voucher
            </button>
            <a href="{{ route('admin.transactions.purchase-entry.edit', $purchase) }}" class="erp-btn-primary me-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Voucher
            </a>
            <a href="{{ route('admin.transactions.purchase-entry') }}" class="erp-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- 4-Stat KPI Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Grand Total Payable</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #059669;">₹{{ number_format($purchase->grand_total, 2) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-shield-halved"></i> Total Inward Liability</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Official Billing Amount</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #2563EB;">₹{{ number_format($purchase->bill_total, 2) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-receipt"></i> Incl. ₹{{ number_format($purchase->tax_amount, 2) }} GST</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Under-Billing (U_B)</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #D97706;">₹{{ number_format($purchase->under_billing_total, 2) }}</h3>
                <span class="erp-kpi-meta" style="color: #D97706;"><i class="fa-solid fa-coins"></i> Cash / Mandi Spread</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(91, 132, 30, 0.12); color: #5B841E;">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Total Inward Quantity</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #0F172A;">
                    {{ number_format($purchase->items->sum('quantity'), 3) }}
                </h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-boxes-packing"></i> Across {{ count($purchase->items) }} line item(s)</span>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div class="row g-4">
        <!-- Left Column: Itemized Line Items & Notes -->
        <div class="col-lg-8">
            <!-- Line Items Card -->
            <div class="card erp-main-card mb-4" style="border-radius: 16px; overflow: hidden;">
                <div class="erp-section-header p-3 border-bottom d-flex justify-content-between align-items-center" style="background: #F8FAFC;">
                    <div class="d-flex align-items-center">
                        <div class="erp-section-icon me-2" style="background: rgba(91, 132, 30, 0.1); color: #5B841E; width: 34px; height: 34px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-list-check"></i>
                        </div>
                        <h4 class="font-weight-700 mb-0 font-size-base">Inward Product Line Items</h4>
                    </div>
                    <span class="badge bg-light text-dark border font-monospace">{{ count($purchase->items) }} Items</span>
                </div>

                <div class="table-responsive">
                    <table class="table erp-data-table mb-0">
                        <thead style="background: #F8FAFC;">
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th style="min-width: 180px;">Item / Product</th>
                                <th style="width: 100px;">Batch</th>
                                <th style="width: 90px; text-align: right;">Qty</th>
                                <th style="width: 95px; text-align: right;">Act Rate</th>
                                <th style="width: 95px; text-align: right;">Bill Rate</th>
                                <th style="width: 90px; text-align: right;">UB Rate</th>
                                <th style="width: 70px; text-align: right;">GST%</th>
                                <th style="width: 110px; text-align: right;">Bill Amt</th>
                                <th style="width: 110px; text-align: right;">UB Amt</th>
                                <th style="width: 120px; text-align: right;">Total Amt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $idx => $item)
                                <tr>
                                    <td class="text-center font-monospace text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $item->item->name ?? 'Raw Material' }}</strong>
                                        <div class="erp-table-subtext font-monospace">Code: {{ $item->item->code ?? 'N/A' }} | HSN: {{ $item->hsn_code ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $item->batch_no ?? 'Default' }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <span class="font-monospace font-weight-600 text-dark">{{ number_format($item->quantity, 3) }}</span>
                                        <span class="text-muted font-size-xs">{{ $item->unit }}</span>
                                    </td>
                                    <td style="text-align: right;" class="font-monospace text-muted">
                                        ₹{{ number_format($item->actual_rate, 2) }}
                                    </td>
                                    <td style="text-align: right;" class="font-monospace text-dark font-weight-600">
                                        ₹{{ number_format($item->bill_rate, 2) }}
                                    </td>
                                    <td style="text-align: right;" class="font-monospace font-weight-600" style="color: #D97706;">
                                        ₹{{ number_format($item->ub_rate, 2) }}
                                    </td>
                                    <td style="text-align: right;" class="font-monospace text-muted">
                                        {{ number_format($item->gst_percent, 1) }}%
                                    </td>
                                    <td style="text-align: right;" class="font-monospace text-dark font-weight-600">
                                        ₹{{ number_format($item->bill_amount, 2) }}
                                    </td>
                                    <td style="text-align: right;" class="font-monospace font-weight-600" style="color: #D97706;">
                                        ₹{{ number_format($item->under_amount, 2) }}
                                    </td>
                                    <td style="text-align: right;">
                                        <strong class="font-monospace text-dark">₹{{ number_format($item->total_amount, 2) }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                            <tr>
                                <th colspan="3" class="text-end font-weight-700">Totals:</th>
                                <th style="text-align: right;" class="font-monospace font-weight-bold text-dark">
                                    {{ number_format($purchase->items->sum('quantity'), 3) }}
                                </th>
                                <th colspan="4"></th>
                                <th style="text-align: right;" class="font-monospace font-weight-bold text-primary">
                                    ₹{{ number_format($purchase->bill_total, 2) }}
                                </th>
                                <th style="text-align: right;" class="font-monospace font-weight-bold" style="color: #D97706;">
                                    ₹{{ number_format($purchase->under_billing_total, 2) }}
                                </th>
                                <th style="text-align: right;" class="font-monospace font-weight-bold text-success font-size-base">
                                    ₹{{ number_format($purchase->grand_total, 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes & Remarks Card -->
            <div class="card erp-form-section-card">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-notes-medical me-2 text-muted"></i> Consignment Remarks & Unloading Terms</h4>
                </div>
                <div class="erp-section-body p-3">
                    <p class="text-muted mb-0 font-size-sm">
                        {{ $purchase->notes ? $purchase->notes : 'No specific consignment or quality inspection notes recorded for this purchase entry.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Financial & Supplier Summary -->
        <div class="col-lg-4">
            <!-- Vendor & Broker Profile Card -->
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-building me-2 text-muted"></i> Vendor & Broker Information</h4>
                </div>
                <div class="erp-section-body p-3 font-size-sm">
                    <div class="d-flex align-items-center mb-3">
                        <div class="erp-avatar-initials me-2" style="background: linear-gradient(135deg, #5B841E, #3D5A12); width: 44px; height: 44px; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; flex-shrink: 0;">
                            {{ strtoupper(substr($purchase->vendor->name ?? 'V', 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold text-dark font-size-base">{{ $purchase->vendor->name ?? 'Direct Vendor' }}</h5>
                            <span class="text-muted font-monospace">{{ $purchase->vendor->code ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">GSTIN:</span>
                        <span class="font-monospace text-dark font-weight-600">{{ $purchase->vendor->gstin ?? 'Unregistered' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">City / Location:</span>
                        <span class="text-dark">{{ $purchase->vendor->city ?? 'Sojat' }}, {{ $purchase->vendor->state ?? 'Rajasthan' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Contact Phone:</span>
                        <span class="font-monospace text-dark">{{ $purchase->vendor->phone ?? '-' }}</span>
                    </div>

                    @if($purchase->broker)
                        <hr style="border-color: #E2E8F0; margin: 0.75rem 0;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fa-solid fa-handshake me-1" style="color: #64748B;"></i> Broker:</span>
                            <strong class="text-dark">{{ $purchase->broker->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Commission:</span>
                            <span class="font-monospace text-dark">{{ $purchase->broker->commission_rate }}%</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transport & Voucher Logistics -->
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-truck-moving me-2 text-muted"></i> Logistics & Inward Coordinates</h4>
                </div>
                <div class="erp-section-body p-3 font-size-sm">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Supplier Bill No:</span>
                        <strong class="font-monospace text-dark">{{ $purchase->invoice_no ?? 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Invoice Date:</span>
                        <span class="text-dark font-weight-500">{{ $purchase->invoice_date->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Transport Vehicle:</span>
                        <span class="font-monospace text-dark font-weight-600">{{ $purchase->vehicle_no ?? 'Direct Handover' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Order Urgency:</span>
                        <span class="badge bg-light text-dark border">{{ $purchase->order_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Payment Terms:</span>
                        <span class="text-dark font-weight-600">{{ $purchase->payment_terms }}</span>
                    </div>
                </div>
            </div>

            <!-- Financial Settlement Card -->
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-calculator me-2 text-muted"></i> Financial Settlement</h4>
                </div>
                <div class="erp-section-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Bill Subtotal:</span>
                        <span class="font-monospace text-dark font-weight-600">₹{{ number_format($purchase->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">GST Tax Amount:</span>
                        <span class="font-monospace text-muted">₹{{ number_format($purchase->tax_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span style="color: #2563EB; font-weight: 600;">Official Billing Total:</span>
                        <strong class="font-monospace" style="color: #2563EB;">₹{{ number_format($purchase->bill_total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="color: #D97706; font-weight: 600;">Under-Billing Total:</span>
                        <strong class="font-monospace" style="color: #D97706;">₹{{ number_format($purchase->under_billing_total, 2) }}</strong>
                    </div>

                    <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-dark font-weight-700">Grand Total:</span>
                            <h3 class="font-monospace font-weight-bold mb-0" style="color: #059669;">₹{{ number_format($purchase->grand_total, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card erp-sidebar-actions-card">
                <div class="card-body">
                    <a href="{{ route('admin.transactions.purchase-entry.edit', $purchase) }}" class="erp-btn-primary w-100 text-center mb-2 py-2">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Purchase Voucher
                    </a>
                    <button type="button" class="erp-btn-outline w-100 py-2" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Print Inward Slip
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
