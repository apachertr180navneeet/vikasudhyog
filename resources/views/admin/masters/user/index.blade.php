@extends('admin.layouts.app')

@section('title', 'User Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-user')

@section('content')
<section class="view-section active" id="view-master-user">
    <!-- Breadcrumb & Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span style="color: var(--primary); font-weight: 600;">User Master</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.6rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-users-gear" style="color: var(--primary);"></i> User Master
            </h1>
            <p class="page-subtitle" style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Manage operator accounts, login credentials, ERP access privileges &amp; plant assignments.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print User Directory">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.user.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 600; padding: 0.6rem 1.25rem; border-radius: 8px;">
                <i class="fa-solid fa-user-plus"></i> Add New User
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--primary); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(91, 132, 30, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Users</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); line-height: 1.2;">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--status-success); border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.12); color: var(--status-success); display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Active Accounts</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--status-success); line-height: 1.2;">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #8B5CF6; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(139, 92, 246, 0.12); color: #8B5CF6; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Super Admins</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); line-height: 1.2;">{{ $stats['super_admins'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #EF4444; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">
                <i class="fa-solid fa-user-lock"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Inactive / Locked</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: #EF4444; line-height: 1.2;">{{ $stats['inactive'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success" style="background: #F0FDF4; border: 1px solid #BBF7D0; border-left: 4px solid #16A34A; padding: 0.9rem 1.2rem; border-radius: 10px; color: #166534; font-size: 0.88rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.1rem; color: #16A34A;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #166534; cursor: pointer; font-size: 1.1rem;">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; padding: 0.9rem 1.2rem; border-radius: 10px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem; color: #EF4444;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #991B1B; cursor: pointer; font-size: 1.1rem;">&times;</button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden;">
        <!-- Filter and Search Header -->
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <form action="{{ route('admin.masters.user') }}" method="GET" style="display: flex; align-items: center; gap: 0.75rem; flex: 1; flex-wrap: wrap;">
                <!-- Search Box -->
                <div style="position: relative; flex: 1; min-width: 220px; max-width: 320px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search name, username, email, phone..." class="form-control" style="padding-left: 2.25rem; height: 40px; border-radius: 8px;">
                </div>

                <!-- Role Filter -->
                <select name="role" class="form-control" style="width: 170px; height: 40px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['role'] ?? 'all') === 'all' ? 'selected' : '' }}>All Roles</option>
                    @foreach($roles as $roleKey => $roleDesc)
                        <option value="{{ $roleKey }}" {{ ($filters['role'] ?? '') === $roleKey ? 'selected' : '' }}>{{ $roleKey }}</option>
                    @endforeach
                </select>

                <!-- Company Filter -->
                <select name="company_id" class="form-control" style="width: 180px; height: 40px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['company_id'] ?? 'all') === 'all' ? 'selected' : '' }}>All Companies</option>
                    <option value="global" {{ ($filters['company_id'] ?? '') === 'global' ? 'selected' : '' }}>All Plants (Global)</option>
                    @foreach($companies as $comp)
                        <option value="{{ $comp->id }}" {{ ($filters['company_id'] ?? '') == $comp->id ? 'selected' : '' }}>{{ $comp->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="form-control" style="width: 130px; height: 40px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="btn btn-outline" style="height: 40px; padding: 0 1rem; border-radius: 8px;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['role'] ?? 'all') !== 'all' || ($filters['company_id'] ?? 'all') !== 'all' || ($filters['status'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.user') }}" class="btn btn-outline" style="height: 40px; padding: 0 0.85rem; border-radius: 8px; color: var(--text-muted);" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="custom-table" style="margin: 0; width: 100%;">
                <thead>
                    <tr style="background: #F8FAF6;">
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">User Account</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Contact Details</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">ERP Role</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Company Assignment</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: center;">Status</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Last Activity</th>
                        <th style="padding: 1rem 1.25rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#FBFDF9'" onmouseout="this.style.backgroundColor='transparent'">
                            <!-- User Account & Username -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, var(--primary) 0%, #3D5A14 100%); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; box-shadow: 0 2px 6px rgba(91,132,30,0.25); flex-shrink: 0;">
                                        {{ $u->initials }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary); font-size: 0.95rem;">
                                            <a href="{{ route('admin.masters.user.show', $u) }}" style="color: inherit; text-decoration: none;" onmouseover="this.style.color='var(--primary)'" onmouseout="this.style.color='inherit'">
                                                {{ $u->name }}
                                            </a>
                                            @if(auth()->id() === $u->id)
                                                <span style="font-size: 0.68rem; background: rgba(91,132,30,0.15); color: var(--primary); padding: 0.15rem 0.45rem; border-radius: 4px; font-weight: 700; margin-left: 0.35rem;">You</span>
                                            @endif
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.2rem;">
                                            <span style="font-family: monospace; font-size: 0.75rem; background: #F1F5F9; color: #475569; padding: 0.15rem 0.4rem; border-radius: 4px; font-weight: 600;">
                                                <i class="fa-solid fa-at" style="font-size: 0.7rem; color: #94A3B8;"></i>{{ $u->username }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Details -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                <div style="font-size: 0.85rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.4rem;">
                                    <i class="fa-regular fa-envelope" style="color: var(--text-muted); font-size: 0.8rem; width: 14px;"></i>
                                    <span>{{ $u->email }}</span>
                                </div>
                                <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 0.25rem; display: flex; align-items: center; gap: 0.4rem;">
                                    <i class="fa-solid fa-phone" style="color: var(--text-muted); font-size: 0.75rem; width: 14px;"></i>
                                    <span>{{ $u->phone ?: 'Not provided' }}</span>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                @php
                                    $roleBadgeStyles = [
                                        'Super Administrator' => 'background: #FAF5FF; color: #7E22CE; border: 1px solid #E9D5FF;',
                                        'Admin'               => 'background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0;',
                                        'Manager'             => 'background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE;',
                                        'Accountant'          => 'background: #F0FDFA; color: #0F766E; border: 1px solid #99F6E4;',
                                        'Sales Manager'       => 'background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A;',
                                        'Purchase Manager'    => 'background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0;',
                                        'Inventory Operator'  => 'background: #ECFEFF; color: #0E7490; border: 1px solid #A5F3FC;',
                                    ];
                                    $badgeStyle = $roleBadgeStyles[$u->role] ?? 'background: #F8FAFC; color: #475569; border: 1px solid #E2E8F0;';
                                @endphp
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.3rem 0.65rem; border-radius: 6px; font-size: 0.78rem; font-weight: 700; {{ $badgeStyle }}">
                                    @if($u->isSuperAdmin())
                                        <i class="fa-solid fa-shield-halved"></i>
                                    @else
                                        <i class="fa-solid fa-user-tag"></i>
                                    @endif
                                    {{ $u->role }}
                                </span>
                            </td>

                            <!-- Company Assignment -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                @if($u->company)
                                    <div style="font-weight: 600; color: var(--text-primary); font-size: 0.88rem; display: flex; align-items: center; gap: 0.4rem;">
                                        <i class="fa-solid fa-building" style="color: var(--primary); font-size: 0.8rem;"></i>
                                        <span>{{ $u->company->name }}</span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                                        Code: <strong style="color: #475569;">{{ $u->company->code }}</strong>
                                    </div>
                                @else
                                    <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.78rem; font-weight: 600; color: #0369A1; background: #F0F9FF; padding: 0.25rem 0.55rem; border-radius: 6px; border: 1px solid #BAE6FD;">
                                        <i class="fa-solid fa-globe"></i> All Plants / Global
                                    </span>
                                @endif
                            </td>

                            <!-- Status Badge with Toggle -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle; text-align: center;">
                                <form action="{{ route('admin.masters.user.toggle-status', $u) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($u->status === 'active')
                                        <button type="submit" style="background: #DCFCE7; color: #166534; border: 1px solid #86EFAC; padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s;" title="Click to Deactivate Account" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #16A34A;"></span> Active
                                        </button>
                                    @elseif($u->status === 'suspended')
                                        <button type="submit" style="background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s;" title="Click to Activate Account" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #D97706;"></span> Suspended
                                        </button>
                                    @else
                                        <button type="submit" style="background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; padding: 0.3rem 0.7rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; transition: all 0.2s;" title="Click to Activate Account" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                            <span style="width: 7px; height: 7px; border-radius: 50%; background: #DC2626;"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Last Activity -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle;">
                                @if($u->last_login_at)
                                    <div style="font-size: 0.82rem; color: var(--text-primary); font-weight: 600;">
                                        {{ $u->last_login_at->diffForHumans() }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.15rem;">
                                        IP: {{ $u->last_login_ip ?: 'Unknown' }}
                                    </div>
                                @else
                                    <span style="font-size: 0.78rem; color: var(--text-muted); font-style: italic;">
                                        Never logged in
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td style="padding: 1rem 1.25rem; vertical-align: middle; text-align: right;">
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.4rem;">
                                    <a href="{{ route('admin.masters.user.show', $u) }}" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.82rem; border-radius: 6px;" title="View User Profile">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.user.edit', $u) }}" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.82rem; border-radius: 6px; color: var(--primary);" title="Edit User Details">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    @if(auth()->id() !== $u->id)
                                        <button type="button" class="btn btn-outline" style="padding: 0.35rem 0.65rem; font-size: 0.82rem; border-radius: 6px; color: #EF4444;" onclick="confirmDeleteUser({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->username }}')" title="Delete User Account">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem 1.5rem;">
                                <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; color: #94A3B8; display: inline-flex; align-items: center; justify-content: center; font-size: 1.75rem; margin-bottom: 1rem;">
                                    <i class="fa-solid fa-user-slash"></i>
                                </div>
                                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin: 0 0 0.5rem;">No User Accounts Found</h3>
                                <p style="font-size: 0.88rem; color: var(--text-muted); margin: 0 0 1.25rem;">No user accounts match your search and filter criteria.</p>
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
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; background: #FCFDFB;">
                <div style="font-size: 0.82rem; color: var(--text-muted);">
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
<form id="delete-user-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDeleteUser(userId, userName, username) {
    Swal.fire({
        title: 'Delete User Account?',
        html: `Are you sure you want to remove user <strong>"${userName}"</strong> (@${username})?<br><small style="color: #64748B;">This action soft-deletes the user and revokes ERP access immediately.</small>`,
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
