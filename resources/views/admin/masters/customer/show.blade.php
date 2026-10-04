@extends('admin.layouts.app')

@section('title', $customer->name . ' (' . $customer->code . ') - Customer Dossier - VIKAS UDHYOG ERP')
@section('page_code', 'master-customer')

@section('content')
<section class="view-section active" id="view-customer-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.customer') }}">Customer Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $customer->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-user-check text-primary"></i> Customer Profile Dossier
            </h1>
            <p class="erp-page-subtitle">
                Complete commercial profile, credit limits, dispatch destinations &amp; ledger receivables balance.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Customer Profile">
                <i class="fa-solid fa-print"></i> Print Profile
            </button>
            <a href="{{ route('admin.masters.customer.edit', $customer->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </a>
            <a href="{{ route('admin.masters.customer') }}" class="btn btn-outline">
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
                    {{ $customer->initials }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $customer->name }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Code Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Customer Code">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $customer->code }}
                        </span>

                        <!-- Customer Type Pill -->
                        <span class="erp-profile-role-pill" title="Client Classification">
                            <i class="fa-solid fa-tag me-1"></i>{{ $customer->customer_type }}
                        </span>

                        <!-- Plant Binding Pill -->
                        @if($customer->company)
                            <span class="erp-profile-role-pill" title="Assigned Production Plant">
                                <i class="fa-solid fa-building me-1"></i>{{ $customer->company->name }} ({{ $customer->company->code }})
                            </span>
                        @else
                            <span class="erp-profile-role-pill" title="Serviced Across All Units">
                                <i class="fa-solid fa-globe me-1"></i>All Units (Global Client)
                            </span>
                        @endif

                        <!-- City Pill -->
                        @if($customer->city)
                            <span class="erp-profile-role-pill" title="Market / Hub">
                                <i class="fa-solid fa-location-dot me-1"></i>{{ $customer->city }}, {{ $customer->state ?: 'Rajasthan' }}
                            </span>
                        @endif

                        <!-- GSTIN Pill -->
                        @if($customer->gstin)
                            <span class="erp-profile-username-pill font-monospace" title="Registered GSTIN">
                                <i class="fa-solid fa-stamp me-1"></i>{{ $customer->gstin }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Action & Status Column -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <!-- Status Toggle Button Form -->
                <form action="{{ route('admin.masters.customer.toggle-status', $customer->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    @if($customer->status === 'active')
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate Customer">
                            <span class="erp-profile-status-dot-active"></span> Status: Active
                        </button>
                    @else
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate Customer">
                            <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                        </button>
                    @endif
                </form>

                <!-- Quick Contact Buttons with Frosted Glass -->
                <div class="d-flex align-items-center gap-2">
                    @if($customer->phone)
                        @php
                            $rawPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                            $waPhone = strlen($rawPhone) === 10 ? '91' . $rawPhone : $rawPhone;
                        @endphp
                        <a href="tel:{{ $customer->phone }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Call Customer Contact">
                            <i class="fa-solid fa-phone me-1"></i> Call
                        </a>
                        <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="btn" style="background: #25D366; color: #FFFFFF; border: none; border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 700; box-shadow: 0 4px 10px rgba(37,211,102,0.3);" title="Message on WhatsApp">
                            <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                        </a>
                    @endif

                    @if($customer->email)
                        <a href="mailto:{{ $customer->email }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Send Business Email">
                            <i class="fa-regular fa-envelope me-1"></i> Email
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI 4-Card Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <!-- Outstanding Receivables -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Current Outstanding Due</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ $customer->current_balance > 0 ? '#DC2626' : '#059669' }};">
                    ₹{{ number_format($customer->current_balance, 2) }}
                </div>
            </div>
        </div>

        <!-- Approved Credit Limit -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Approved Credit Limit</div>
                <div class="erp-kpi-val font-monospace">
                    ₹{{ number_format($customer->credit_limit, 2) }}
                </div>
            </div>
        </div>

        <!-- Payment Terms -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Payment Credit Terms</div>
                <div class="erp-kpi-val erp-kpi-val-success">
                    {{ $customer->payment_terms ?: '30 Days' }}
                </div>
            </div>
        </div>

        <!-- Client Tenure -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Customer Since</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem; color: #1E293B;">
                    {{ $customer->created_at ? $customer->created_at->format('M Y') : 'Active' }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Profile Details Breakdown Grid -->
    <div class="erp-profile-details-grid">
        <!-- Main Column (Left) -->
        <div class="erp-form-main-col">

            <!-- 1. Statutory & Tax Compliance Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-file-invoice text-primary"></i> Statutory Compliance &amp; Enterprise Registrations
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">GSTIN Registration</div>
                        <div class="erp-profile-detail-val">
                            @if($customer->gstin)
                                <span class="font-monospace text-dark fw-bold">{{ $customer->gstin }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->gstin }}', 'GSTIN copied to clipboard!')" title="Copy GSTIN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Unregistered Client</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Income Tax PAN</div>
                        <div class="erp-profile-detail-val">
                            @if($customer->pan)
                                <span class="font-monospace text-dark fw-bold">{{ $customer->pan }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->pan }}', 'PAN copied to clipboard!')" title="Copy PAN">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Not Provided</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Customer Master Code</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ $customer->code }}</span>
                            <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->code }}', 'Customer Code copied!')" title="Copy Code">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Client Classification</div>
                        <div class="erp-profile-detail-val">
                            <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                <i class="fa-solid fa-tag me-1"></i>{{ $customer->customer_type }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Assigned Production Plant</div>
                        <div class="erp-profile-detail-val">
                            @if($customer->company)
                                <a href="{{ route('admin.masters.company.show', $customer->company->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                    <i class="fa-solid fa-building me-1"></i>{{ $customer->company->name }} ({{ $customer->company->code }})
                                </a>
                            @else
                                <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.78rem;">
                                    <i class="fa-solid fa-globe me-1"></i>All Plants / General
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Opening Balance</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace text-dark fw-bold">₹{{ number_format($customer->opening_balance, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Billing & Dispatch Location Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-location-dot text-primary"></i> Billing &amp; Dispatch Premises
                </div>
                <div class="erp-profile-detail-body">
                    <div style="grid-column: 1 / -1;">
                        <div class="erp-profile-detail-label">Street Address / Dispatch Facility</div>
                        <div class="erp-profile-detail-val fw-medium">
                            <i class="fa-solid fa-map-pin text-primary"></i>
                            <span>{{ $customer->address ?: 'No street address specified in profile.' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">City / Mandi Hub</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $customer->city ?: '—' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">State / Union Territory</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold">{{ $customer->state ?: 'Rajasthan' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Postal Pincode</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold">{{ $customer->pincode ?: '—' }}</span>
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

            <!-- 3. Trade Notes & Special Instructions Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-note-sticky text-primary"></i> Trade Remarks &amp; Dispatch Conditions
                </div>
                <div class="p-3">
                    @if($customer->notes)
                        <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.9rem; line-height: 1.6; color: #334155;">
                            {{ $customer->notes }}
                        </div>
                    @else
                        <div class="text-muted p-2" style="font-size: 0.88rem; font-style: italic;">
                            No special dispatch notes, preferred transport carriers or billing conditions recorded for this customer.
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
                    <i class="fa-solid fa-user-tie text-primary"></i> Primary Contact Representative
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Contact Name</span>
                        <strong class="text-dark">{{ $customer->contact_person ?: '—' }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Mobile Phone</span>
                        @if($customer->phone)
                            <div class="d-flex align-items-center gap-1">
                                <a href="tel:{{ $customer->phone }}" class="font-monospace fw-bold text-dark text-decoration-none">
                                    {{ $customer->phone }}
                                </a>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->phone }}', 'Phone number copied!')" title="Copy Phone">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Email Address</span>
                        @if($customer->email)
                            <div class="d-flex align-items-center gap-1">
                                <a href="mailto:{{ $customer->email }}" class="text-dark text-decoration-none" style="font-size: 0.85rem;">
                                    {{ $customer->email }}
                                </a>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->email }}', 'Email copied!')" title="Copy Email">
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
                    <i class="fa-solid fa-building-columns text-primary"></i> Remittance &amp; Banking Details
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Bank Name</span>
                        <strong class="text-dark">{{ $customer->bank_name ?: '—' }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Account No.</span>
                        @if($customer->bank_account_no)
                            <div class="d-flex align-items-center gap-1">
                                <strong class="font-monospace text-dark">{{ $customer->bank_account_no }}</strong>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->bank_account_no }}', 'Bank account copied!')" title="Copy Account No">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">IFSC Code</span>
                        @if($customer->bank_ifsc)
                            <div class="d-flex align-items-center gap-1">
                                <strong class="font-monospace text-dark">{{ $customer->bank_ifsc }}</strong>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $customer->bank_ifsc }}', 'IFSC Code copied!')" title="Copy IFSC">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Branch</span>
                        <strong class="text-dark">{{ $customer->bank_branch ?: '—' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Ledger Security & Audit Stamp Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> Ledger Security &amp; Audit Trail
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">System Record ID</span>
                        <strong class="font-monospace text-dark">#{{ $customer->id }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Created Date</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $customer->created_at ? $customer->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Last Modified</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $customer->updated_at ? $customer->updated_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 4. Quick Actions Sidebar Card -->
            <div class="card erp-sidebar-actions-card mb-4">
                <a href="{{ route('admin.masters.customer.edit', $customer->id) }}" class="erp-btn-action-submit">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Customer Account
                </a>
                <a href="{{ route('admin.masters.customer') }}" class="erp-btn-action-cancel">
                    <i class="fa-solid fa-arrow-left"></i> Back to Customer Directory
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
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.success(message || 'Copied to clipboard!');
                } else {
                    alert(message || 'Copied to clipboard!');
                }
            }).catch(function() {
                fallbackCopy(text, message);
            });
        } else {
            fallbackCopy(text, message);
        }
    }

    function fallbackCopy(text, message) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        try {
            document.execCommand('copy');
            if (typeof toastr !== 'undefined') {
                toastr.success(message || 'Copied to clipboard!');
            } else {
                alert(message || 'Copied to clipboard!');
            }
        } catch (err) {
            console.error('Fallback copy error:', err);
        }
        document.body.removeChild(textarea);
    }
</script>
@endpush
