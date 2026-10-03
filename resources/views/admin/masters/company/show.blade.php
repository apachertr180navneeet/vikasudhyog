@extends('admin.layouts.app')

@section('title', $company->name . ' - Company Profile - VIKAS UDHYOG ERP')
@section('page_code', 'master-company-show')

@section('content')
<section class="view-section active" id="view-master-company-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.company') }}">Company Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $company->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-building text-primary"></i> Company Profile Details
            </h1>
            <p class="erp-page-subtitle">
                {{ $company->tagline ?? 'Registered manufacturing unit, commercial tax compliance & corporate profile.' }}
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Company Dossier">
                <i class="fa-solid fa-print"></i> Print Dossier
            </button>
            <a href="{{ route('admin.masters.company.edit', $company->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Company
            </a>
            <a href="{{ route('admin.masters.company') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Directory
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

    <!-- Executive Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $company->name }}
                        @if($company->is_default)
                            <span class="badge" style="background: rgba(212, 160, 23, 0.25); color: #FDE047; font-size: 0.75rem; border: 1px solid rgba(253, 224, 71, 0.4); padding: 3px 8px; border-radius: 6px;">
                                <i class="fa-solid fa-crown me-1"></i>Primary Default Plant
                            </span>
                        @endif
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Code Pill -->
                        <span class="erp-profile-username-pill" title="Prefix Ledger Code">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $company->code ?: 'VU-NEW' }}
                        </span>

                        <!-- City Pill -->
                        <span class="erp-profile-role-pill">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ $company->city }}, {{ $company->state }}
                        </span>

                        <!-- GSTIN Pill -->
                        @if($company->gstin)
                            <span class="erp-profile-username-pill font-monospace" title="Registered GSTIN">
                                <i class="fa-solid fa-stamp me-1"></i>{{ $company->gstin }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Action & Status Column -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <!-- Status Toggle Button Form -->
                <form action="{{ route('admin.masters.company.toggle-status', $company->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    @if($company->status === 'active')
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate Company">
                            <span class="erp-profile-status-dot-active"></span> Status: Active
                        </button>
                    @else
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate Company">
                            <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                        </button>
                    @endif
                </form>

                <!-- Quick Action Buttons -->
                <div class="d-flex align-items-center gap-2">
                    @if($company->phone)
                        <a href="tel:{{ $company->phone }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Call Unit">
                            <i class="fa-solid fa-phone me-1"></i> Call
                        </a>
                    @endif
                    @if($company->email)
                        <a href="mailto:{{ $company->email }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Email Unit">
                            <i class="fa-regular fa-envelope me-1"></i> Email
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI 4-Card Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <!-- Financial Year -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Financial Year</div>
                <div class="erp-kpi-val">{{ $company->financial_year ?? '2026-2027' }}</div>
            </div>
        </div>

        <!-- Entity Category -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-industry"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Entity Status</div>
                <div class="erp-kpi-val erp-kpi-val-success">
                    {{ $company->is_default ? 'Primary Unit' : 'Branch Unit' }}
                </div>
            </div>
        </div>

        <!-- Tax Registration -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-stamp"></i>
            </div>
            <div>
                <div class="erp-kpi-label">GST Compliance</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem; color: #7E22CE;">
                    {{ $company->gstin ? 'Registered' : 'Exempted' }}
                </div>
            </div>
        </div>

        <!-- Plant Location -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Operating Hub</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem; color: #1E293B;">
                    {{ $company->city ?: 'Sojat' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Details Breakdown -->
    <div class="erp-profile-details-grid">
        <!-- Main Column (Left) -->
        <div class="erp-form-main-col">
            <!-- 1. Statutory Compliance Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-file-invoice text-primary"></i> Statutory &amp; Government Registrations
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">GSTIN Registration Number</div>
                        <div class="erp-profile-detail-val">
                            @if($company->gstin)
                                <span class="font-monospace text-dark fw-bold">{{ $company->gstin }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $company->gstin }}', 'GSTIN copied!')" title="Copy GSTIN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Not Registered</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Income Tax PAN</div>
                        <div class="erp-profile-detail-val">
                            @if($company->pan)
                                <span class="font-monospace text-dark fw-bold">{{ $company->pan }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $company->pan }}', 'PAN copied!')" title="Copy PAN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Not Provided</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Invoice Prefix Code</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ $company->code ?: 'VU-NEW' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Active Financial Year</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $company->financial_year ?? '2026-2027' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Plant Premises Address Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-map-location-dot text-primary"></i> Plant Premises &amp; Factory Location
                </div>
                <div class="erp-profile-detail-body">
                    <div style="grid-column: 1 / -1;">
                        <div class="erp-profile-detail-label">Street / Facility Address</div>
                        <div class="erp-profile-detail-val fw-medium">
                            <i class="fa-solid fa-map-pin text-primary"></i>
                            <span>{{ $company->address ?: 'Address not specified' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">City / Industrial Area</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $company->city }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">State</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $company->state }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Postal Pincode</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold">{{ $company->pincode ?: '—' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Official Website</div>
                        <div class="erp-profile-detail-val">
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" class="text-primary text-decoration-none">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>{{ $company->website }}
                                </a>
                            @else
                                <span class="text-muted fw-normal">Not Provided</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="erp-form-side-col">
            <!-- 1. Contact Representative Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-address-book text-primary"></i> Corporate Contact
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Phone</span>
                        @if($company->phone)
                            <div class="d-flex align-items-center gap-1">
                                <a href="tel:{{ $company->phone }}" class="font-monospace fw-bold text-dark text-decoration-none">
                                    {{ $company->phone }}
                                </a>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $company->phone }}', 'Phone copied!')" title="Copy Phone">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Email</span>
                        @if($company->email)
                            <div class="d-flex align-items-center gap-1">
                                <a href="mailto:{{ $company->email }}" class="text-dark text-decoration-none" style="font-size: 0.85rem;">
                                    {{ $company->email }}
                                </a>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $company->email }}', 'Email copied!')" title="Copy Email">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Bank Settlement Account Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-building-columns text-primary"></i> Bank Settlement Account
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Bank Name</span>
                        <strong class="text-dark">{{ $company->bank_name ?: 'Not configured' }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Account No.</span>
                        @if($company->bank_account_no)
                            <div class="d-flex align-items-center gap-1">
                                <strong class="font-monospace text-dark">{{ $company->bank_account_no }}</strong>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $company->bank_account_no }}', 'Account copied!')" title="Copy Account No">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">IFSC Code</span>
                        @if($company->bank_ifsc)
                            <div class="d-flex align-items-center gap-1">
                                <strong class="font-monospace text-dark">{{ $company->bank_ifsc }}</strong>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $company->bank_ifsc }}', 'IFSC copied!')" title="Copy IFSC">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Branch</span>
                        <strong class="text-dark">{{ $company->bank_branch ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. System Record Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> System Record Stamps
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Unit ID</span>
                        <strong class="font-monospace text-dark">#{{ $company->id }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Registered Date</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $company->created_at ? $company->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Last Modified</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $company->updated_at ? $company->updated_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 4. Quick Actions Sidebar Card -->
            <div class="card erp-sidebar-actions-card mb-4">
                <a href="{{ route('admin.masters.company.edit', $company->id) }}" class="erp-btn-action-submit">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Company Profile
                </a>
                <a href="{{ route('admin.masters.company') }}" class="erp-btn-action-cancel">
                    <i class="fa-solid fa-arrow-left"></i> Back to Company Directory
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
