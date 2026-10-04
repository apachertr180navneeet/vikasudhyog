@extends('admin.layouts.app')

@section('title', 'WB Purchase Slip ' . $wbPurchase->slip_no . ' - VIKAS UDHYOG ERP')
@section('page_code', 'txn-wb-purchase')

@section('content')
<div class="erp-module-wrapper">
    <!-- Top Bar & Breadcrumbs -->
    <div class="erp-header-toolbar mb-4">
        <div class="erp-breadcrumb-wrap">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transactions.wb-purchase-entry') }}">WB Purchase Entry</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $wbPurchase->slip_no }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-3">
                <h1 class="erp-page-title mb-0">Weighbridge Slip #{{ $wbPurchase->slip_no }}</h1>
                <span class="badge {{ $wbPurchase->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-1 font-weight-700 font-size-sm">
                    {{ ucfirst($wbPurchase->status) }}
                </span>
            </div>
        </div>
        <div class="erp-header-actions">
            <button type="button" class="erp-btn-outline me-2" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Print WB Slip
            </button>
            <a href="{{ route('admin.transactions.wb-purchase-entry.edit', $wbPurchase) }}" class="erp-btn-primary me-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Slip
            </a>
            <a href="{{ route('admin.transactions.wb-purchase-entry') }}" class="erp-btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- 4-Stat KPI Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Total Mandi Cash Amount</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #D97706;">₹{{ number_format($wbPurchase->total_amount, 2) }}</h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-wallet"></i> Mode: {{ $wbPurchase->payment_mode }}</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Final Net Inward Weight</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #059669;">{{ number_format($wbPurchase->net_weight, 2) }} <span style="font-size: 0.9rem; font-weight: 500;">KG</span></h3>
                <span class="erp-kpi-meta text-success"><i class="fa-solid fa-check"></i> Tare & Moisture Deducted</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-truck-moving"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Gross Weighment</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #2563EB;">{{ number_format($wbPurchase->gross_weight, 2) }} <span style="font-size: 0.9rem; font-weight: 500;">KG</span></h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-truck"></i> Loaded Vehicle Weight</span>
            </div>
        </div>

        <div class="erp-kpi-card">
            <div class="erp-kpi-icon" style="background: rgba(100, 116, 139, 0.12); color: #475569;">
                <i class="fa-solid fa-scale-unbalanced"></i>
            </div>
            <div class="erp-kpi-content">
                <span class="erp-kpi-label">Tare & Deductions</span>
                <h3 class="erp-kpi-value font-monospace" style="color: #0F172A;">
                    {{ number_format($wbPurchase->tare_weight + $wbPurchase->deduction_weight, 2) }} <span style="font-size: 0.9rem; font-weight: 500;">KG</span>
                </h3>
                <span class="erp-kpi-meta"><i class="fa-solid fa-minus"></i> Tare: {{ number_format($wbPurchase->tare_weight, 2) }} | Bag: {{ number_format($wbPurchase->deduction_weight, 2) }}</span>
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
                        <h4 class="font-weight-700 mb-0 font-size-base">Weighbridge Raw Material Items</h4>
                    </div>
                    <span class="badge bg-light text-dark border font-monospace">{{ count($wbPurchase->items) }} Items</span>
                </div>

                <div class="table-responsive">
                    <table class="table erp-data-table mb-0">
                        <thead style="background: #F8FAFC;">
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th style="min-width: 200px;">Product / Herb Item</th>
                                <th style="width: 110px;">Batch No</th>
                                <th style="width: 120px; text-align: right;">Net Weight</th>
                                <th style="width: 120px; text-align: right;">WB Rate (₹)</th>
                                <th style="width: 140px; text-align: right;">Mandi Amount (₹)</th>
                                <th style="width: 140px;">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wbPurchase->items as $idx => $item)
                                <tr>
                                    <td class="text-center font-monospace text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong class="text-dark">{{ $item->item->name ?? 'Raw Herb' }}</strong>
                                        <div class="erp-table-subtext font-monospace">Code: {{ $item->item->code ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $item->batch_no ?? 'WB-Arrival' }}</span>
                                    </td>
                                    <td style="text-align: right;">
                                        <strong class="font-monospace text-dark font-size-base">{{ number_format($item->quantity, 3) }}</strong>
                                        <span class="text-muted font-size-xs">{{ $item->unit }}</span>
                                    </td>
                                    <td style="text-align: right;" class="font-monospace text-dark font-weight-600">
                                        ₹{{ number_format($item->rate, 2) }}
                                    </td>
                                    <td style="text-align: right;">
                                        <strong class="font-monospace font-size-base" style="color: #D97706;">
                                            ₹{{ number_format($item->amount, 2) }}
                                        </strong>
                                    </td>
                                    <td class="font-size-sm text-muted">
                                        {{ $item->notes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0;">
                            <tr>
                                <th colspan="3" class="text-end font-weight-700">Total Net Inward:</th>
                                <th style="text-align: right;" class="font-monospace font-weight-bold text-dark font-size-base">
                                    {{ number_format($wbPurchase->items->sum('quantity'), 3) }} KG
                                </th>
                                <th class="text-end font-weight-700">Total Payable:</th>
                                <th style="text-align: right;" class="font-monospace font-weight-bold font-size-base" style="color: #D97706;">
                                    ₹{{ number_format($wbPurchase->total_amount, 2) }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes & Remarks Card -->
            <div class="card erp-form-section-card">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-notes-medical me-2 text-muted"></i> Consignment Remarks & Kanta Inspection</h4>
                </div>
                <div class="erp-section-body p-3">
                    <p class="text-muted mb-0 font-size-sm">
                        {{ $wbPurchase->notes ? $wbPurchase->notes : 'No specific mandi inspection or yard moisture notes recorded for this WB slip.' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Column: Weighbridge Scale Certificate & Supplier Info -->
        <div class="col-lg-4">
            <!-- Scale Certificate Card -->
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-scale-balanced me-2 text-muted"></i> Weighbridge Scale Certificate</h4>
                </div>
                <div class="erp-section-body p-3 font-size-sm">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Gross Weight (Loaded):</span>
                        <span class="font-monospace font-weight-600 text-dark">{{ number_format($wbPurchase->gross_weight, 2) }} KG</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tare Weight (Empty):</span>
                        <span class="font-monospace text-muted">{{ number_format($wbPurchase->tare_weight, 2) }} KG</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Moisture / Bag Deduction:</span>
                        <span class="font-monospace text-muted">- {{ number_format($wbPurchase->deduction_weight, 2) }} KG</span>
                    </div>
                    <hr style="border-color: #E2E8F0; margin: 0.75rem 0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-dark font-weight-700 font-size-base">Net Stock Weight:</span>
                        <h4 class="font-monospace font-weight-bold mb-0 text-success">{{ number_format($wbPurchase->net_weight, 2) }} KG</h4>
                    </div>
                </div>
            </div>

            <!-- Vendor / Farmer Profile Card -->
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-user-tag me-2 text-muted"></i> Farmer / Supplier Coordinates</h4>
                </div>
                <div class="erp-section-body p-3 font-size-sm">
                    <div class="d-flex align-items-center mb-3">
                        <div class="erp-avatar-initials me-2" style="background: linear-gradient(135deg, #5B841E, #3D5A12); width: 44px; height: 44px; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; flex-shrink: 0;">
                            {{ strtoupper(substr($wbPurchase->vendor->name ?? 'F', 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold text-dark font-size-base">{{ $wbPurchase->vendor->name ?? 'Direct Farmer' }}</h5>
                            <span class="text-muted font-monospace">{{ $wbPurchase->vendor->code ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">City / Mandi:</span>
                        <span class="text-dark">{{ $wbPurchase->vendor->city ?? 'Sojat' }}, {{ $wbPurchase->vendor->state ?? 'Rajasthan' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Contact Phone:</span>
                        <span class="font-monospace text-dark">{{ $wbPurchase->vendor->phone ?? '-' }}</span>
                    </div>

                    @if($wbPurchase->broker)
                        <hr style="border-color: #E2E8F0; margin: 0.75rem 0;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fa-solid fa-handshake me-1" style="color: #64748B;"></i> Broker:</span>
                            <strong class="text-dark">{{ $wbPurchase->broker->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Commission:</span>
                            <span class="font-monospace text-dark">{{ $wbPurchase->broker->commission_rate }}%</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transport & Logistics -->
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header p-3 border-bottom" style="background: #F8FAFC;">
                    <h4 class="font-weight-600 mb-0 font-size-sm"><i class="fa-solid fa-truck me-2 text-muted"></i> Transport & Arrival Vehicle</h4>
                </div>
                <div class="erp-section-body p-3 font-size-sm">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Vehicle No:</span>
                        <strong class="font-monospace text-dark">{{ $wbPurchase->vehicle_no ?? 'Trolley / Direct' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Driver Name:</span>
                        <span class="text-dark">{{ $wbPurchase->driver_name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Driver Phone:</span>
                        <span class="font-monospace text-dark">{{ $wbPurchase->driver_phone ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Inward Date:</span>
                        <span class="text-dark font-weight-500">{{ $wbPurchase->entry_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card erp-sidebar-actions-card">
                <div class="card-body">
                    <a href="{{ route('admin.transactions.wb-purchase-entry.edit', $wbPurchase) }}" class="erp-btn-primary w-100 text-center mb-2 py-2">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit WB Purchase Slip
                    </a>
                    <button type="button" class="erp-btn-outline w-100 py-2" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Print WB Slip
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
