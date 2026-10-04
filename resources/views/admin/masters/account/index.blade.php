@extends('admin.layouts.app')

@section('title', 'Account Master - Chart of Accounts - VIKAS UDHYOG ERP')
@section('page_code', 'master-account')

@section('content')
<section class="view-section active" id="view-master-account">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Account Master</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-book-journal-whills text-primary"></i> Account Master
            </h1>
            <p class="erp-page-subtitle">
                Chart of accounts, general ledgers, commercial banking accounts, cash reserves, income &amp; expense heads.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Chart of Accounts">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.account.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> Add New Account
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Chart Ledgers</div>
                <div class="erp-kpi-val">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Accounts</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Bank Liquidity</div>
                <div class="erp-kpi-val font-monospace">₹{{ number_format($stats['total_bank_balance'] ?? 0, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #10B981;">
            <div class="erp-kpi-icon-box" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Liquid Cash Reserves</div>
                <div class="erp-kpi-val font-monospace" style="color: #059669;">₹{{ number_format($stats['total_cash_balance'] ?? 0, 2) }}</div>
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

    <!-- Main Table Container -->
    <div class="card erp-main-card">
        <!-- Filter and Search Header -->
        <div class="erp-table-filter-header">
            <form action="{{ route('admin.masters.account') }}" method="GET" class="erp-filter-form">
                <div class="erp-search-wrap">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search account name, code, bank, account no..." class="form-control erp-search-input">
                </div>

                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="group" class="form-control erp-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['group'] ?? 'all') === 'all' ? 'selected' : '' }}>All Account Groups</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp }}" {{ ($filters['group'] ?? '') === $grp ? 'selected' : '' }}>{{ $grp }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-outline erp-btn-filter" title="Apply Filters">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['group'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.account') }}" class="btn btn-outline erp-btn-filter-clear" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                @endif
            </form>

            <div class="erp-table-summary-count">
                Showing <strong>{{ $accounts->count() }}</strong> of <strong>{{ $accounts->total() }}</strong> accounts
            </div>
        </div>

        <!-- Edge-to-Edge Table -->
        <div class="table-responsive" style="margin: 0; border: none; overflow-x: auto;">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Account Name &amp; Code</th>
                        <th>Account Group</th>
                        <th>Bank / Institutional Details</th>
                        <th style="text-align: center;">Dr / Cr</th>
                        <th style="text-align: right;">Opening Balance</th>
                        <th style="text-align: right;">Current Balance</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <!-- Account Name & Code -->
                            <td style="padding-left: 1.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $account->initials }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.masters.account.show', $account->id) }}" style="font-weight: 600; color: #1E293B; text-decoration: none; display: block;" class="erp-table-title-link">
                                            {{ $account->name }}
                                        </a>
                                        <div style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem;">
                                            <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.72rem; padding: 2px 6px; border-radius: 4px; border: 1px solid #E2E8F0;">
                                                {{ $account->code }}
                                            </span>
                                            @if($account->company)
                                                <span style="font-size: 0.72rem; color: #64748B;">
                                                    <i class="fa-solid fa-building" style="font-size: 0.65rem;"></i> {{ $account->company->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Account Group -->
                            <td>
                                @php
                                    $badgeStyle = match($account->account_group) {
                                        'Bank Accounts'       => 'background: rgba(59, 130, 246, 0.1); color: #2563EB; border: 1px solid rgba(59, 130, 246, 0.25);',
                                        'Cash in Hand'        => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25);',
                                        'Direct Incomes', 'Indirect Incomes' => 'background: rgba(91, 132, 30, 0.1); color: #5B841E; border: 1px solid rgba(91, 132, 30, 0.25);',
                                        'Direct Expenses', 'Indirect Expenses' => 'background: rgba(139, 92, 246, 0.1); color: #7C3AED; border: 1px solid rgba(139, 92, 246, 0.25);',
                                        'Duties & Taxes'      => 'background: rgba(239, 68, 68, 0.1); color: #DC2626; border: 1px solid rgba(239, 68, 68, 0.25);',
                                        default               => 'background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1;',
                                    };
                                @endphp
                                <span class="badge" style="{{ $badgeStyle }} font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 9999px;">
                                    {{ $account->account_group }}
                                </span>
                            </td>

                            <!-- Bank / Institutional Details -->
                            <td>
                                @if($account->bank_name || $account->account_number)
                                    <div style="font-weight: 600; color: #334155; font-size: 0.82rem;">
                                        <i class="fa-solid fa-building-columns text-primary me-1"></i> {{ $account->bank_name ?: 'Bank Account' }}
                                    </div>
                                    @if($account->account_number)
                                        <div class="font-monospace" style="font-size: 0.74rem; color: #64748B; margin-top: 0.1rem;">
                                            A/c: {{ $account->account_number }}
                                        </div>
                                    @endif
                                @elseif($account->upi_id)
                                    <div class="font-monospace" style="font-size: 0.78rem; color: #059669; font-weight: 600;">
                                        <i class="fa-solid fa-qrcode me-1"></i> {{ $account->upi_id }}
                                    </div>
                                @else
                                    <span style="font-size: 0.78rem; color: #94A3B8;">General Ledger Head</span>
                                @endif
                            </td>

                            <!-- Dr / Cr -->
                            <td style="text-align: center;">
                                @if($account->balance_type === 'debit')
                                    <span class="badge font-monospace" style="background: rgba(59, 130, 246, 0.12); color: #1D4ED8; font-size: 0.72rem; font-weight: 700; padding: 3px 7px; border-radius: 4px;">
                                        Dr
                                    </span>
                                @else
                                    <span class="badge font-monospace" style="background: rgba(245, 158, 11, 0.12); color: #B45309; font-size: 0.72rem; font-weight: 700; padding: 3px 7px; border-radius: 4px;">
                                        Cr
                                    </span>
                                @endif
                            </td>

                            <!-- Opening Balance -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 600; color: #475569;">
                                    ₹{{ number_format($account->opening_balance, 2) }}
                                </div>
                            </td>

                            <!-- Current Balance -->
                            <td style="text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; font-size: 0.95rem; color: {{ $account->current_balance >= 0 ? '#0F172A' : '#DC2626' }};">
                                    ₹{{ number_format($account->current_balance, 2) }}
                                </div>
                                <span style="font-size: 0.70rem; color: #64748B;">
                                    {{ strtoupper($account->balance_type) }}
                                </span>
                            </td>

                            <!-- Status Toggle Button -->
                            <td style="text-align: center;">
                                <form action="{{ route('admin.masters.account.toggle-status', $account->id) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($account->status === 'active')
                                        <button type="submit" class="erp-status-btn erp-status-btn-active" title="Click to Deactivate Account">
                                            <span class="erp-status-dot-green"></span> Active
                                        </button>
                                    @else
                                        <button type="submit" class="erp-status-btn erp-status-btn-inactive" title="Click to Activate Account">
                                            <span class="erp-status-dot-red"></span> Inactive
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Actions -->
                            <td style="text-align: right; padding-right: 1.5rem;">
                                <div class="erp-actions-cell" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.masters.account.show', $account->id) }}" class="erp-table-action-icon" title="View Account Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.masters.account.edit', $account->id) }}" class="erp-table-action-icon" title="Edit Account">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="erp-table-action-icon erp-table-action-icon-danger" onclick="confirmDeleteAccount({{ $account->id }}, '{{ addslashes($account->name) }}', '{{ $account->code }}')" title="Archive Account">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3.5rem 1.5rem;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div style="width: 64px; height: 64px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #94A3B8; font-size: 1.6rem; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-book-journal-whills"></i>
                                    </div>
                                    <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.35rem;">No Accounts Found</h4>
                                    <p style="color: #64748B; font-size: 0.88rem; max-width: 380px; margin-bottom: 1.25rem;">
                                        No general ledgers or bank accounts match your search or filter criteria.
                                    </p>
                                    @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all' || ($filters['group'] ?? 'all') !== 'all')
                                        <a href="{{ route('admin.masters.account') }}" class="btn btn-outline" style="border-radius: 8px;">
                                            <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                                        </a>
                                    @else
                                        <a href="{{ route('admin.masters.account.create') }}" class="btn btn-primary" style="border-radius: 8px;">
                                            <i class="fa-solid fa-plus"></i> Add First Account
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($accounts->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FAFBFD;">
                <div style="font-size: 0.82rem; color: #64748B;">
                    Showing <strong>{{ $accounts->firstItem() }}</strong> to <strong>{{ $accounts->lastItem() }}</strong> of <strong>{{ $accounts->total() }}</strong> entries
                </div>
                <div>
                    {{ $accounts->links() }}
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Delete Account Hidden Form -->
<form id="delete-account-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDeleteAccount(accountId, accountName, accountCode) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Archive Account Ledger?',
                html: `Are you sure you want to archive <strong>${accountName} (${accountCode})</strong>?<br><span style="font-size: 0.85rem; color: #64748B;">This action soft-deletes the ledger from active transaction voucher selections.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#64748B',
                confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Archive',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-account-form');
                    form.action = `{{ url('admin/masters/account') }}/${accountId}`;
                    form.submit();
                }
            });
        } else {
            if (confirm(`Are you sure you want to archive ${accountName} (${accountCode})?`)) {
                const form = document.getElementById('delete-account-form');
                form.action = `{{ url('admin/masters/account') }}/${accountId}`;
                form.submit();
            }
        }
    }
</script>
@endpush
@endsection
