@extends('admin.layouts.app')

@section('title', $broker->name . ' (' . $broker->code . ') - Broker Profile - VIKAS UDHYOG ERP')
@section('page_code', 'master-broker')

@section('content')
<section class="view-section active" id="view-broker-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.broker') }}">Broker Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $broker->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-handshake text-primary"></i> Broker Profile Details
            </h1>
            <p class="erp-page-subtitle">
                Commission agreements, TDS Section 194H compliance, mandi representation &amp; bank settlement details.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Broker Profile">
                <i class="fa-solid fa-print"></i> Print Profile
            </button>
            <a href="{{ route('admin.masters.broker.edit', $broker->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </a>
            <a href="{{ route('admin.masters.broker') }}" class="btn btn-outline">
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

    <!-- Executive Profile Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ $broker->initials }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $broker->name }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Code Pill -->
                        <span class="erp-profile-username-pill" title="Broker System Code">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $broker->code }}
                        </span>

                        <!-- Commission Rate Pill -->
                        <span class="badge" style="background: rgba(16, 185, 129, 0.25); color: #A7F3D0; font-size: 0.82rem; border: 1px solid rgba(167, 243, 208, 0.4); padding: 4px 10px; border-radius: 6px;">
                            <i class="fa-solid fa-percent me-1"></i>{{ number_format($broker->commission_rate, 2) }}% Brokerage
                        </span>

                        <!-- Plant Linkage Pill -->
                        @if($broker->company)
                            <span class="erp-profile-role-pill" title="Assigned Production Plant">
                                <i class="fa-solid fa-building me-1"></i> {{ $broker->company->name }} ({{ $broker->company->code }})
                            </span>
                        @else
                            <span class="erp-profile-role-pill" title="Global Intermediation Across All Units">
                                <i class="fa-solid fa-globe me-1"></i> All Plants (Global Broker)
                            </span>
                        @endif

                        <!-- City Pill -->
                        @if($broker->city)
                            <span class="erp-profile-role-pill" title="Mandi / Agricultural Hub">
                                <i class="fa-solid fa-location-dot me-1"></i> {{ $broker->city }}, {{ $broker->state ?: 'Rajasthan' }}
                            </span>
                        @endif

                        <!-- PAN Pill -->
                        @if($broker->pan)
                            <span class="erp-profile-username-pill font-monospace" title="Income Tax PAN for TDS">
                                <i class="fa-solid fa-id-card me-1"></i>{{ $broker->pan }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Action & Status Column -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <!-- Status Toggle Button Form -->
                <form action="{{ route('admin.masters.broker.toggle-status', $broker->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    @if($broker->status === 'active')
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate Broker">
                            <span class="erp-profile-status-dot-active"></span> Status: Active
                        </button>
                    @else
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate Broker">
                            <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                        </button>
                    @endif
                </form>

                <!-- Quick Action Buttons -->
                <div class="d-flex align-items-center gap-2">
                    @if($broker->phone)
                        @php
                            $rawPhone = preg_replace('/[^0-9]/', '', $broker->phone);
                            $waPhone = strlen($rawPhone) === 10 ? '91' . $rawPhone : $rawPhone;
                        @endphp
                        <a href="tel:{{ $broker->phone }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Call Broker Representative">
                            <i class="fa-solid fa-phone me-1"></i> Call
                        </a>
                        <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="btn" style="background: #25D366; color: #FFFFFF; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 700; box-shadow: 0 4px 10px rgba(37,211,102,0.3);" title="Message on WhatsApp">
                            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                        </a>
                    @endif

                    @if($broker->email)
                        <a href="mailto:{{ $broker->email }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Send Commercial Email">
                            <i class="fa-regular fa-envelope me-1"></i> Email
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI 4-Card Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <!-- Outstanding Commission -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Outstanding Commission (Payable)</div>
                <div class="erp-kpi-val font-monospace" style="color: #7E22CE;">
                    ₹{{ number_format($broker->current_balance, 2) }}
                </div>
            </div>
        </div>

        <!-- Opening Balance -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Opening Balance</div>
                <div class="erp-kpi-val font-monospace">
                    ₹{{ number_format($broker->opening_balance, 2) }}
                </div>
            </div>
        </div>

        <!-- Agreed Commission Rate -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Agreed Brokerage Rate</div>
                <div class="erp-kpi-val erp-kpi-val-success">
                    {{ number_format($broker->commission_rate, 2) }}%
                </div>
            </div>
        </div>

        <!-- Tenure / Vintage -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Agent Since</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem; color: #1E293B;">
                    {{ $broker->created_at ? $broker->created_at->format('M Y') : 'Active' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Profile Details Breakdown -->
    <div class="erp-profile-details-grid">
        <!-- Main Column (Left) -->
        <div class="erp-form-main-col">
            <!-- 1. Statutory & Tax Compliance Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-file-invoice text-primary"></i> Statutory &amp; TDS Section 194H Compliance
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">Income Tax PAN Number</div>
                        <div class="erp-profile-detail-val">
                            @if($broker->pan)
                                <span class="font-monospace text-dark fw-bold">{{ $broker->pan }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->pan }}', 'PAN copied to clipboard!')" title="Copy PAN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Not Provided</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">GSTIN Registration</div>
                        <div class="erp-profile-detail-val">
                            @if($broker->gstin)
                                <span class="font-monospace text-dark fw-bold">{{ $broker->gstin }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->gstin }}', 'GSTIN copied to clipboard!')" title="Copy GSTIN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Unregistered Agent</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Broker Master Code</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ $broker->code }}</span>
                            <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->code }}', 'Broker Code copied!')" title="Copy Code">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Brokerage Basis</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold text-dark">{{ $broker->brokerage_type ?: 'Percentage (%)' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Manufacturing Plant Allocation</div>
                        <div class="erp-profile-detail-val">
                            @if($broker->company)
                                <a href="{{ route('admin.masters.company.show', $broker->company->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                    <i class="fa-solid fa-building me-1"></i>{{ $broker->company->name }} ({{ $broker->company->code }})
                                </a>
                            @else
                                <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.78rem;">
                                    <i class="fa-solid fa-globe me-1"></i>All Plants / Global Intermediation
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Mandi Yard & Office Address Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-location-dot text-primary"></i> Mandi Yard &amp; Office Location
                </div>
                <div class="erp-profile-detail-body">
                    <div style="grid-column: 1 / -1;">
                        <div class="erp-profile-detail-label">Shop / Mandi Complex Address</div>
                        <div class="erp-profile-detail-val fw-medium">
                            <i class="fa-solid fa-map-pin text-primary"></i>
                            <span>{{ $broker->address ?: 'No street address specified in profile.' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">City / Mandi Hub</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $broker->city ?: 'Sojat' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">State</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $broker->state ?: 'Rajasthan' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Postal Pincode</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold">{{ $broker->pincode ?: '—' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Country</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">India</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Procurement Remarks Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-note-sticky text-primary"></i> Mandi Intermediation Remarks &amp; Notes
                </div>
                <div class="p-3">
                    @if($broker->notes)
                        <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.9rem; line-height: 1.6; color: #334155;">
                            {{ $broker->notes }}
                        </div>
                    @else
                        <div class="text-muted p-2" style="font-size: 0.88rem; font-style: italic;">
                            No special brokerage instructions recorded for this broker. Remarks can be modified in the edit screen.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="erp-form-side-col">
            <!-- 1. Key Contact Representative Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-user-tie text-primary"></i> Key Contact Representative
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Contact Person</span>
                        <strong class="text-dark">{{ $broker->contact_person ?: '—' }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Mobile Phone</span>
                        @if($broker->phone)
                            <div class="d-flex align-items-center gap-1">
                                <a href="tel:{{ $broker->phone }}" class="font-monospace fw-bold text-dark text-decoration-none">
                                    {{ $broker->phone }}
                                </a>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->phone }}', 'Phone number copied!')" title="Copy Phone">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Email Address</span>
                        @if($broker->email)
                            <div class="d-flex align-items-center gap-1">
                                <a href="mailto:{{ $broker->email }}" class="text-dark text-decoration-none" style="font-size: 0.85rem;">
                                    {{ $broker->email }}
                                </a>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->email }}', 'Email copied!')" title="Copy Email">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Bank Settlement Details Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-building-columns text-primary"></i> Bank Settlement Account
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Bank Name</span>
                        <strong class="text-dark">{{ $broker->bank_name ?: '—' }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Account No.</span>
                        @if($broker->bank_account_no)
                            <div class="d-flex align-items-center gap-1">
                                <strong class="font-monospace text-dark">{{ $broker->bank_account_no }}</strong>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->bank_account_no }}', 'Bank account copied!')" title="Copy Account No">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">IFSC Code</span>
                        @if($broker->bank_ifsc)
                            <div class="d-flex align-items-center gap-1">
                                <strong class="font-monospace text-dark">{{ $broker->bank_ifsc }}</strong>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $broker->bank_ifsc }}', 'IFSC Code copied!')" title="Copy IFSC">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Branch</span>
                        <strong class="text-dark">{{ $broker->bank_branch ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Audit Stamp Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> System Ledger Stamps
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Record ID</span>
                        <strong class="font-monospace text-dark">#{{ $broker->id }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Created Date</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $broker->created_at ? $broker->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Last Modified</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $broker->updated_at ? $broker->updated_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 4. Quick Actions Sidebar Card -->
            <div class="card erp-sidebar-actions-card mb-4">
                <a href="{{ route('admin.masters.broker.edit', $broker->id) }}" class="erp-btn-action-submit">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Broker Profile
                </a>
                <a href="{{ route('admin.masters.broker') }}" class="erp-btn-action-cancel">
                    <i class="fa-solid fa-arrow-left"></i> Back to Broker Directory
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text, message) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success(message || 'Copied to clipboard!');
            } else {
                alert(message || 'Copied to clipboard!');
            }
        }).catch(function() {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            if (typeof toastr !== 'undefined') {
                toastr.success(message || 'Copied to clipboard!');
            }
        });
    }
</script>
@endpush
