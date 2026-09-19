@extends('admin.layouts.app')

@section('title', 'User Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-user')

@section('content')
<section class="view-section active" id="view-master-user">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">User Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-users-gear"></i> User Master
            </h1>
            <p class="erp-page-subtitle">
                Manage operator accounts, login credentials, ERP access privileges &amp; plant assignments.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print User Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.user.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-user-plus"></i> Add New User
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Users</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Accounts</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Super Admins</div>
                <div class="erp-kpi-val">{{ $stats['super_admins'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-danger">
            <div class="erp-kpi-icon-box erp-kpi-icon-danger">
                <i class="fa-solid fa-user-lock"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Inactive / Locked</div>
                <div class="erp-kpi-val erp-kpi-val-danger">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
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

    @if(session('error'))
        <div class="alert erp-alert-danger">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-exclamation erp-alert-icon-danger"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card erp-table-container-card">
        <!-- Filter and Search Header -->
        <div class="erp-filter-header">
            <form action="{{ route('admin.masters.user') }}" method="GET" class="erp-filter-form">
                <!-- Search Box -->
                <div class="erp-filter-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-filter-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, username, email, phone..." class="form-control erp-filter-search-field">
                </div>

                <!-- Role Filter -->
                <select name="role" class="form-control erp-filter-select-role" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['role'] ?? 'all') === 'all' ? 'selected' : '' }}>All Roles</option>
                    @foreach($roles as $roleKey => $roleDesc)
                        <option value="{{ $roleKey }}" {{ ($filters['role'] ?? '') === $roleKey ? 'selected' : '' }}>{{ $roleKey }}</option>
                    @endforeach
                </select>

                <!-- Company Filter -->
                <select name="company_id" class="form-control erp-filter-select-company" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['company_id'] ?? 'all') === 'all' ? 'selected' : '' }}>All Companies</option>
                    <option value="global" {{ ($filters['company_id'] ?? '') === 'global' ? 'selected' : '' }}>All Plants (Global)</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}" {{ ($filters['company_id'] ?? '') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="form-control erp-filter-select-status" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="btn btn-outline erp-filter-submit-btn">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['role'] ?? 'all') !== 'all' || ($filters['company_id'] ?? 'all') !== 'all' || ($filters['status'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.user') }}" class="btn btn-outline erp-filter-clear-btn" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="custom-table erp-main-table">
                <thead>
                    <tr>
                        <th>User Account</th>
                        <th>Contact Details</th>
                        <th>ERP Role</th>
                        <th>Company Assignment</th>
                        <th class="text-center">Status</th>
                        <th>Last Activity</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <!-- User Account & Username -->
                            <td>
                                <div class="erp-user-cell">
                                    <div class="erp-avatar-box">
                                        {{ $u->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.user.show', $u) }}" class="erp-user-name">
                                            {{ $u->name }}
                                        </a>
                                        <div class="erp-user-username">
                                            <i class="fa-solid fa-at"></i>{{ $u->username }}
                                            @if(auth()->id() === $u->id)
                                                <span class="sub-perm-badge">You</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Details -->
                            <td>
                                <div class="erp-user-contact-email">
                                    <i class="fa-regular fa-envelope"></i>
                                    <a href="mailto:{{ $u->email }}">{{ $u->email }}</a>
                                </div>
                                <div class="erp-user-contact-phone">
                                    <i class="fa-solid fa-phone"></i>
                                    <span>{{ $u->phone ?: 'Not provided' }}</span>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td>
                                @php
                                    $roleClasses = [
                                        'Super Administrator' => 'erp-badge-role-super',
                                        'Admin'               => 'erp-badge-role-admin',
                                        'Manager'             => 'erp-badge-role-manager',
                                        'Accountant'          => 'erp-badge-role-accountant',
                                        'Sales Manager'       => 'erp-badge-role-sales',
                                        'Purchase Manager'    => 'erp-badge-role-purchase',
                                        'Inventory Operator'  => 'erp-badge-role-warehouse',
                                    ];
                                    $roleClass = $roleClasses[$u->role] ?? 'erp-badge-role-default';
                                @endphp
                                <span class="erp-badge-role {{ $roleClass }}">
                                    @if($u->isSuperAdmin())
                                        <i class="fa-solid fa-shield-halved"></i>
                                    @else
                                        <i class="fa-solid fa-user-tag"></i>
                                    @endif
                                    {{ $u->role }}
                                </span>
                            </td>

                            <!-- Company Assignment -->
                            <td>
                                @if($u->company)
                                    <a href="{{ route('admin.masters.company.show', $u->company) }}" class="erp-company-link">
                                        <i class="fa-solid fa-building"></i>
                                        <span>{{ $u->company->name }}</span>
                                    </a>
                                    <div class="erp-field-hint">
                                        Code: <strong>{{ $u->company->code }}</strong>
                                    </div>
                                @else
                                    <span class="erp-company-global-pill">
                                        <i class="fa-solid fa-globe"></i> All Plants / Global
                                    </span>
                                @endif
                            </td>

                            <!-- Status Badge with Toggle -->
                            <td class="text-center">
                                <form action="{{ route('admin.masters.user.toggle-status', $u) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    @if($u->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Account">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @elseif($u->status === 'suspended')
                                        <button type="submit" class="erp-status-btn erp-status-btn-suspended" title="Click to Activate Account">
                                            <span class="erp-status-dot-amber"></span> Suspended
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Account">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Last Activity -->
                            <td>
                                @if($u->last_login_at)
                                    <div class="erp-profile-audit-title">
                                        {{ $u->last_login_at->diffForHumans() }}
                                    </div>
                                    <div class="erp-profile-audit-time">
                                        IP: {{ $u->last_login_ip ?: 'Unknown' }}
                                    </div>
                                @else
                                    <span class="erp-field-hint">
                                        Never logged in
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="text-end">
                                <div class="erp-actions-cell">
                                    <a href="{{ route('admin.masters.user.show', $u) }}" class="erp-table-action-icon" title="View User Profile">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.user.edit', $u) }}" class="erp-table-action-icon" title="Edit User Details">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if(auth()->id() !== $u->id)
                                        <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->username }}')" title="Delete User Account">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="erp-table-empty-cell">
                                <div class="erp-table-empty-icon">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <h3 class="erp-table-empty-title">No User Accounts Found</h3>
                                <p class="erp-page-subtitle">No user accounts match your search and filter criteria.</p>
                                <a href="{{ route('admin.masters.user.create') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Add New User
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($users->hasPages())
            <div class="erp-pagination-wrap d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="erp-field-hint">
                    Showing <strong>{{ $users->firstItem() }}</strong> to <strong>{{ $users->lastItem() }}</strong> of <strong>{{ $users->total() }}</strong> users
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete User Modal Form -->
<form id="delete-user-form" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDeleteUser(userId, userName, username) {
    Swal.fire({
        title: 'Delete User Account?',
        html: `Are you sure you want to remove user <strong>"${userName}"</strong> (@${username})?<br><small class="text-muted">This action soft-deletes the user and revokes ERP access immediately.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Delete User',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-border-radius'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-user-form');
            form.action = `/admin/masters/user/${userId}`;
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
