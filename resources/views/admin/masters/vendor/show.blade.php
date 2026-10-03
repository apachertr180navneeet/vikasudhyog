@extends('admin.layouts.app')

@section('title', $vendor->name . ' (' . $vendor->code . ') - Supplier Profile')
@section('page_code', 'master-vendor')

@section('content')
<section class="view-section active" id="view-vendor-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.vendor') }}">Vendor Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $vendor->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-field text-primary"></i> Supplier Profile Details
            </h1>
            <p class="erp-page-subtitle">
                Complete commercial dossier, GST compliance, bank settlement ledger &amp; procurement contact.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Vendor Profile">
                <i class="fa-solid fa-print"></i> Print Profile
            </button>
            <a href="{{ route('admin.masters.vendor.edit', $vendor->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </a>
            <a href="{{ route('admin.masters.vendor') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Profile Header Hero Card -->
    <div class="card p-4 mb-4" style="border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="erp-user-avatar-circle" style="width: 64px; height: 64px; font-size: 1.5rem; background: rgba(91, 132, 30, 0.12); color: var(--primary);">
                    {{ $vendor->initials }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h2 class="fw-bold text-dark mb-0" style="font-size: 1.5rem;">{{ $vendor->name }}</h2>
                        <span class="badge" style="background: #F1F5F9; color: #475569; font-family: var(--font-mono); font-size: 0.82rem; font-weight: 700;">
                            {{ $vendor->code }}
                        </span>
                        @if($vendor->status === 'active')
                            <span class="erp-status-btn erp-status-btn-active" style="cursor: default;">
                                <span class="erp-status-dot-green"></span> Active
                            </span>
                        @else
                            <span class="erp-status-btn erp-status-btn-inactive" style="cursor: default;">
                                <span class="erp-status-dot-red"></span> Inactive
                            </span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-1 text-muted flex-wrap" style="font-size: 0.85rem;">
                        @if($vendor->city)
                            <span><i class="fa-solid fa-location-dot me-1 text-primary"></i>{{ $vendor->city }}, {{ $vendor->state ?: 'Rajasthan' }}</span>
                        @endif
                        @if($vendor->company)
                            <span><i class="fa-solid fa-building me-1 text-primary"></i>Assigned Plant: {{ $vendor->company->name }} ({{ $vendor->company->code }})</span>
                        @else
                            <span><i class="fa-solid fa-globe me-1 text-primary"></i>Multi-Plant Global Supplier</span>
                        @endif
                        @if($vendor->gstin)
                            <span><i class="fa-solid fa-stamp me-1 text-primary"></i>GSTIN: <span class="font-monospace text-dark fw-semibold">{{ $vendor->gstin }}</span></span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if($vendor->phone)
                    <a href="tel:{{ $vendor->phone }}" class="btn btn-outline btn-sm">
                        <i class="fa-solid fa-phone me-1"></i> Call Phone
                    </a>
                @endif
                @if($vendor->email)
                    <a href="mailto:{{ $vendor->email }}" class="btn btn-outline btn-sm">
                        <i class="fa-regular fa-envelope me-1"></i> Send Email
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI Summary -->
    <div class="erp-kpi-grid mb-4">
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Current Balance (Payable)</div>
                <div class="erp-kpi-val" style="color: #B45309;">₹{{ number_format($vendor->current_balance, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Opening Balance</div>
                <div class="erp-kpi-val">₹{{ number_format($vendor->opening_balance, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Payment Terms</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $vendor->payment_terms ?: '30 Days' }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Supplier Since</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem;">{{ $vendor->created_at ? $vendor->created_at->format('M Y') : 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-7">
            <!-- Statutory & Commercial Details -->
            <div class="card p-4 mb-4" style="border-radius: 14px;">
                <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-file-invoice text-primary me-2"></i>Statutory &amp; Tax Compliance
                </h4>
                <div class="d-flex flex-column gap-3" style="font-size: 0.9rem;">
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">GSTIN Registration:</span>
                        <strong class="font-monospace text-dark">{{ $vendor->gstin ?: 'Unregistered Supplier' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Income Tax PAN:</span>
                        <strong class="font-monospace text-dark">{{ $vendor->pan ?: 'Not Provided' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Payment Terms Credit Limit:</span>
                        <strong class="text-dark">{{ $vendor->payment_terms ?: '30 Days' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Assigned Manufacturing Plant:</span>
                        <strong class="text-dark">{{ $vendor->company ? $vendor->company->name : 'All Registered Plants (Global)' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Address & Location -->
            <div class="card p-4 mb-4" style="border-radius: 14px;">
                <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-location-dot text-primary me-2"></i>Premises &amp; Factory Address
                </h4>
                <div class="d-flex flex-column gap-3" style="font-size: 0.9rem;">
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Street Address:</span>
                        <span class="fw-medium text-dark text-end" style="max-width: 350px;">{{ $vendor->address ?: 'Not Specified' }}</span>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">City / Mandi Hub:</span>
                        <strong class="text-dark">{{ $vendor->city ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">State:</span>
                        <strong class="text-dark">{{ $vendor->state ?: 'Rajasthan' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Pincode:</span>
                        <strong class="font-monospace text-dark">{{ $vendor->pincode ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            @if($vendor->notes)
                <!-- Notes -->
                <div class="card p-4" style="border-radius: 14px;">
                    <h4 class="fw-bold mb-2 text-dark" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-note-sticky text-primary me-2"></i>Procurement Notes &amp; Remarks
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.6;">
                        {{ $vendor->notes }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-5">
            <!-- Primary Contact Person -->
            <div class="card p-4 mb-4" style="border-radius: 14px;">
                <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-user-tie text-primary me-2"></i>Primary Contact Representative
                </h4>
                <div class="d-flex flex-column gap-3" style="font-size: 0.9rem;">
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Contact Name:</span>
                        <strong class="text-dark">{{ $vendor->contact_person ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Mobile Number:</span>
                        @if($vendor->phone)
                            <a href="tel:{{ $vendor->phone }}" class="fw-bold font-monospace text-dark text-decoration-none">
                                {{ $vendor->phone }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Email Address:</span>
                        @if($vendor->email)
                            <a href="mailto:{{ $vendor->email }}" class="text-dark text-decoration-none">
                                {{ $vendor->email }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bank Settlement Details -->
            <div class="card p-4 mb-4" style="border-radius: 14px;">
                <h4 class="fw-bold mb-3 text-dark" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-building-columns text-primary me-2"></i>Bank Settlement Information
                </h4>
                <div class="d-flex flex-column gap-3" style="font-size: 0.9rem;">
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Bank Name:</span>
                        <strong class="text-dark">{{ $vendor->bank_name ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Account Number:</span>
                        <strong class="font-monospace text-dark">{{ $vendor->bank_account_no ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">IFSC Code:</span>
                        <strong class="font-monospace text-dark">{{ $vendor->bank_ifsc ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between pb-2 border-bottom">
                        <span class="text-muted">Branch Name:</span>
                        <strong class="text-dark">{{ $vendor->bank_branch ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="card p-4" style="border-radius: 14px; background: #F8FAFC; border: 1px solid #E2E8F0;">
                <h5 class="fw-bold mb-2 text-dark" style="font-size: 0.92rem;">
                    <i class="fa-solid fa-shield-halved text-muted me-2"></i>System Ledger Record
                </h5>
                <div class="d-flex flex-column gap-2 text-muted" style="font-size: 0.82rem;">
                    <div class="d-flex justify-content-between">
                        <span>Record ID:</span>
                        <strong class="text-dark">#{{ $vendor->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Created Date:</span>
                        <strong class="text-dark">{{ $vendor->created_at ? $vendor->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Last Updated:</span>
                        <strong class="text-dark">{{ $vendor->updated_at ? $vendor->updated_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
