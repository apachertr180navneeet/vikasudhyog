@extends('admin.layouts.app')

@section('title', $user->name . ' - User Profile - VIKAS UDHYOG ERP')
@section('page_code', 'master-user')

@section('content')
<section class="view-section active" id="view-master-user-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.user') }}">User Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $user->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <span class="erp-page-title-icon-box">
                    <i class="fa-solid fa-user-shield"></i>
                </span>
                User Profile
            </h1>
            <p class="erp-page-subtitle">
                Comprehensive overview of account status, ERP module access, and plant bindings.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.user.edit', $user) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </a>
            <a href="{{ route('admin.masters.user') }}" class="btn btn-outline erp-btn-header-back">
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

    <!-- Profile Hero Card -->
    <div class="card erp-profile-hero-card">
        <div class="erp-profile-hero-banner">
            <div class="erp-profile-hero-left">
                <div class="erp-profile-hero-avatar">
                    {{ $user->initials }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $user->name }}
                        @if(auth()->id() === $user->id)
                            <span class="erp-profile-active-session-pill">Active Session</span>
                        @endif
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <span class="erp-profile-username-pill">
                            <i class="fa-solid fa-at"></i>{{ $user->username }}
                        </span>
                        <span class="erp-profile-role-pill">
                            <i class="fa-solid fa-shield-halved"></i> {{ $user->role }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status Form Action -->
            <form action="{{ route('admin.masters.user.toggle-status', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($user->status === 'active')
                    <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate">
                        <span class="erp-profile-status-dot-active"></span> Status: Active
                    </button>
                @elseif($user->status === 'suspended')
                    <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-suspended" title="Click to Activate">
                        <span class="erp-profile-status-dot-suspended"></span> Status: Suspended
                    </button>
                @else
                    <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate">
                        <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                    </button>
                @endif
            </form>
        </div>
    </div>

    <!-- Detailed Attributes Grid -->
    <div class="erp-profile-details-grid">
        <!-- Left Column: Details -->
        <div class="erp-form-main-col">
            <!-- Contact & Corporate Assignment -->
            <div class="card erp-profile-detail-card">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-address-card text-primary"></i> Contact &amp; Organization Binding
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">Official Email</div>
                        <div class="erp-profile-detail-val">
                            <i class="fa-regular fa-envelope text-primary"></i>
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Mobile Number</div>
                        <div class="erp-profile-detail-val">
                            <i class="fa-solid fa-phone text-primary"></i>
                            <span>{{ $user->phone ?: 'Not provided' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Assigned Plant Entity</div>
                        <div class="erp-profile-detail-val">
                            @if($user->company)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-building text-primary"></i>
                                    <a href="{{ route('admin.masters.company.show', $user->company) }}" class="text-primary font-weight-bold">
                                        {{ $user->company->name }} ({{ $user->company->code }})
                                    </a>
                                </div>
                            @else
                                <span class="erp-company-global-pill">
                                    <i class="fa-solid fa-globe"></i> All Plants / Global Multi-Unit
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">ERP Access Level</div>
                        <div class="erp-profile-detail-val">
                            <span class="erp-profile-role-tag">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allowed Module Capabilities -->
            <div class="card erp-profile-detail-card">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-cubes text-primary"></i> Operating Module Scope
                </div>
                <div class="p-3">
                    <div class="erp-scope-grid">
                        <div class="erp-profile-mod-badge">
                            <i class="fa-solid fa-folder-tree text-primary fs-5"></i>
                            <div>
                                <div class="erp-profile-audit-title">Masters</div>
                                <div class="erp-field-hint mt-0">Company, Items, Accounts</div>
                            </div>
                        </div>

                        <div class="erp-profile-mod-badge">
                            <i class="fa-solid fa-right-left text-primary fs-5"></i>
                            <div>
                                <div class="erp-profile-audit-title">Transactions</div>
                                <div class="erp-field-hint mt-0">Sales, Purchases, Vouchers</div>
                            </div>
                        </div>

                        <div class="erp-profile-mod-badge">
                            <i class="fa-solid fa-boxes-stacked text-warning fs-5"></i>
                            <div>
                                <div class="erp-profile-audit-title">Inventory</div>
                                <div class="erp-field-hint mt-0">Stock ledger &amp; alerts</div>
                            </div>
                        </div>

                        <div class="erp-profile-mod-badge">
                            <i class="fa-solid fa-file-contract text-success fs-5"></i>
                            <div>
                                <div class="erp-profile-audit-title">Reports</div>
                                <div class="erp-field-hint mt-0">GST &amp; Financial audit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Audit & Actions -->
        <div class="erp-form-side-col">
            <!-- Audit Trail -->
            <div class="card erp-profile-detail-card p-3">
                <div class="erp-profile-audit-title mb-3">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> Security &amp; Activity
                </div>
                <div class="d-flex flex-column gap-2 text-muted">
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>System ID:</span>
                        <strong class="text-dark">#{{ $user->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>Created Date:</span>
                        <strong class="text-dark">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2">
                        <span>Last Active:</span>
                        <strong class="text-dark">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Last Login IP:</span>
                        <strong class="text-dark font-monospace">{{ $user->last_login_ip ?: 'None' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Action Quick Card -->
            <div class="card erp-profile-detail-card p-3 d-flex flex-column gap-2">
                <a href="{{ route('admin.masters.user.edit', $user) }}" class="btn btn-primary erp-btn-header-primary w-100 justify-content-center">
                    <i class="fa-solid fa-pen-to-square"></i> Edit User Profile
                </a>
                <a href="{{ route('admin.masters.user') }}" class="btn btn-outline erp-btn-header-back w-100 justify-content-center">
                    Back to Directory
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
