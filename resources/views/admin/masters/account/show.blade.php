@extends('admin.layouts.app')

@section('title', $account->name . ' (' . $account->code . ') - Account Dossier - VIKAS UDHYOG ERP')
@section('page_code', 'master-account')

@section('content')
<section class="view-section active" id="view-account-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.account') }}">Account Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $account->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-book-journal-whills text-primary"></i> Account Ledger Dossier
            </h1>
            <p class="erp-page-subtitle">
                Complete chart of accounts classification, banking coordinates, current balance &amp; audit history.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Account Dossier">
                <i class="fa-solid fa-print"></i> Print Dossier
            </button>
            <a href="{{ route('admin.masters.account.edit', $account->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Account
            </a>
            <a href="{{ route('admin.masters.account') }}" class="btn btn-outline">
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
                    {{ $account->initials }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $account->name }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Code Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Ledger Code">
                            <i class="fa-solid fa-barcode me-1"></i>{{ $account->code }}
                        </span>

                        <!-- Account Group Pill -->
                        <span class="erp-profile-role-pill" title="Chart of Accounts Group">
                            <i class="fa-solid fa-layer-group me-1"></i>{{ $account->account_group }}
                        </span>

                        <!-- Plant Binding Pill -->
                        @if($account->company)
                            <span class="erp-profile-role-pill" title="Assigned Production Plant">
                                <i class="fa-solid fa-building me-1"></i>{{ $account->company->name }} ({{ $account->company->code }})
                            </span>
                        @else
                            <span class="erp-profile-role-pill" title="General Ledger across all units">
                                <i class="fa-solid fa-globe me-1"></i>All Plants (General Ledger)
                            </span>
                        @endif

                        <!-- Bank Name Pill -->
                        @if($account->bank_name)
                            <span class="erp-profile-username-pill" title="Commercial Bank">
                                <i class="fa-solid fa-building-columns me-1"></i>{{ $account->bank_name }}
                            </span>
                        @endif

                        <!-- Account No Pill -->
                        @if($account->account_number)
                            <span class="erp-profile-role-pill font-monospace" title="Bank Account Number">
                                <i class="fa-solid fa-credit-card me-1"></i>A/c: {{ $account->account_number }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Action & Status Column -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <!-- Status Toggle Button Form -->
                <form action="{{ route('admin.masters.account.toggle-status', $account->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    @if($account->status === 'active')
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate Account">
                            <span class="erp-profile-status-dot-active"></span> Status: Active
                        </button>
                    @else
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate Account">
                            <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                        </button>
                    @endif
                </form>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.masters.account.edit', $account->id) }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Modify Ledger Record">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Ledger
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI 4-Card Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <!-- Current Balance -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Current Ledger Balance</div>
                <div class="erp-kpi-val font-monospace">
                    ₹{{ number_format($account->current_balance, 2) }}
                    <span style="font-size: 0.8rem; color: #1D4ED8; font-weight: 700;">{{ strtoupper($account->balance_type) }}</span>
                </div>
            </div>
        </div>

        <!-- Opening Balance -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Opening Balance</div>
                <div class="erp-kpi-val font-monospace">
                    ₹{{ number_format($account->opening_balance, 2) }}
                </div>
            </div>
        </div>

        <!-- Group Placement -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Chart Group</div>
                <div class="erp-kpi-val erp-kpi-val-success" style="font-size: 1.15rem;">
                    {{ $account->account_group }}
                </div>
            </div>
        </div>

        <!-- Balance Nature -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Balance Nature</div>
                <div class="erp-kpi-val" style="color: #2563EB; font-size: 1.15rem;">
                    {{ ucfirst($account->balance_type) }} ({{ $account->balance_type === 'debit' ? 'Dr' : 'Cr' }})
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Profile Details Breakdown Grid -->
    <div class="erp-profile-details-grid">
        <!-- Main Column (Left) -->
        <div class="erp-form-main-col">

            <!-- 1. General Ledger Taxonomy & Classifications -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-book-bookmark text-primary"></i> General Ledger Classification &amp; Identifiers
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">Ledger Account Title</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold text-dark">{{ $account->name }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Unique Ledger Code</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ $account->code }}</span>
                            <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $account->code }}', 'Account code copied!')" title="Copy Code">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Chart of Accounts Group</div>
                        <div class="erp-profile-detail-val">
                            <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                <i class="fa-solid fa-folder-tree me-1"></i>{{ $account->account_group }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Production Plant Unit</div>
                        <div class="erp-profile-detail-val">
                            @if($account->company)
                                <a href="{{ route('admin.masters.company.show', $account->company->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                    <i class="fa-solid fa-building me-1"></i>{{ $account->company->name }} ({{ $account->company->code }})
                                </a>
                            @else
                                <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.78rem;">
                                    <i class="fa-solid fa-globe me-1"></i>All Units / General Firm
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Standard Accounting Side</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">
                                {{ ucfirst($account->balance_type) }} ({{ $account->balance_type === 'debit' ? 'Dr' : 'Cr' }})
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Account Status</div>
                        <div class="erp-profile-detail-val">
                            @if($account->status === 'active')
                                <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 700; font-size: 0.8rem;">
                                    Active Ledger
                                </span>
                            @else
                                <span class="badge" style="background: rgba(239, 68, 68, 0.12); color: #DC2626; font-weight: 700; font-size: 0.8rem;">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Banking & Institutional Credentials (if applicable) -->
            @if($account->bank_name || $account->account_number || $account->ifsc_code || $account->upi_id)
                <div class="card erp-profile-detail-card mb-4">
                    <div class="erp-profile-detail-header">
                        <i class="fa-solid fa-building-columns text-primary"></i> Commercial Banking Credentials &amp; Transfer Coordinates
                    </div>
                    <div class="erp-profile-detail-body">
                        <div>
                            <div class="erp-profile-detail-label">Bank Institution</div>
                            <div class="erp-profile-detail-val">
                                <span class="fw-bold text-dark">{{ $account->bank_name ?: '—' }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="erp-profile-detail-label">Account Number</div>
                            <div class="erp-profile-detail-val">
                                @if($account->account_number)
                                    <span class="font-monospace fw-bold text-dark">{{ $account->account_number }}</span>
                                    <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $account->account_number }}', 'Account number copied!')" title="Copy Account No">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="erp-profile-detail-label">IFSC Code</div>
                            <div class="erp-profile-detail-val">
                                @if($account->ifsc_code)
                                    <span class="font-monospace fw-bold text-dark">{{ $account->ifsc_code }}</span>
                                    <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $account->ifsc_code }}', 'IFSC code copied!')" title="Copy IFSC">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="erp-profile-detail-label">Branch Name</div>
                            <div class="erp-profile-detail-val">
                                <span>{{ $account->branch_name ?: '—' }}</span>
                            </div>
                        </div>

                        <div style="grid-column: 1 / -1;">
                            <div class="erp-profile-detail-label">UPI VPA Handle / QR</div>
                            <div class="erp-profile-detail-val">
                                @if($account->upi_id)
                                    <span class="font-monospace fw-bold text-success">{{ $account->upi_id }}</span>
                                    <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $account->upi_id }}', 'UPI ID copied!')" title="Copy UPI">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. Operational Purpose & Audit Remarks -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clipboard-list text-primary"></i> Operational Remarks &amp; Audit Notes
                </div>
                <div class="p-3">
                    @if($account->notes)
                        <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.9rem; line-height: 1.6; color: #334155;">
                            {{ $account->notes }}
                        </div>
                    @else
                        <div class="text-muted p-2" style="font-size: 0.88rem; font-style: italic;">
                            No special ledger instructions, reconciliation notes or transaction conditions recorded for this account.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="erp-form-side-col">
            <!-- 1. Real-Time Balance Card -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-coins text-primary"></i> Current Balance Status
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Opening Balance</span>
                        <strong class="font-monospace text-dark">₹{{ number_format($account->opening_balance, 2) }}</strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Current Ledger Balance</span>
                        <strong class="font-monospace" style="font-size: 1.15rem; color: #059669;">
                            ₹{{ number_format($account->current_balance, 2) }}
                        </strong>
                    </div>

                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Balance Nature</span>
                        <span class="badge font-monospace" style="background: rgba(59, 130, 246, 0.12); color: #1D4ED8; font-size: 0.78rem;">
                            {{ strtoupper($account->balance_type) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. System Record Audit Trail -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> Ledger Security &amp; Audit Trail
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">System Record ID</span>
                        <strong class="font-monospace text-dark">#{{ $account->id }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Registration Date</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $account->created_at ? $account->created_at->format('d M Y, h:i A') : 'System Initial' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Last Modified</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $account->updated_at ? $account->updated_at->format('d M Y, h:i A') : 'Never' }}</strong>
                    </div>
                </div>
            </div>

            <!-- 3. Quick Accounting Voucher Actions -->
            <div class="card" style="padding: 1.15rem; background: #FAFBFD; border: 1px solid #E2E8F0; border-radius: 12px;">
                <h5 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">
                    <i class="fa-solid fa-bolt text-primary me-1"></i> Quick Voucher Entries
                </h5>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.transactions.payment-voucher') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-arrow-up-from-bracket me-2 text-primary"></i> Record Payment Voucher
                    </a>
                    <a href="{{ route('admin.transactions.receipt-voucher') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-arrow-down-to-bracket me-2 text-primary"></i> Record Receipt Voucher
                    </a>
                    <a href="{{ route('admin.masters.account.edit', $account->id) }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Ledger Profile
                    </a>
                    <a href="{{ route('admin.masters.account') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-list me-2 text-primary"></i> Return to Account Directory
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function copyToClipboard(text, successMessage) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast(successMessage);
            });
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast(successMessage);
            } catch (err) {
                console.error('Unable to copy', err);
            }
            document.body.removeChild(textArea);
        }
    }

    function showToast(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }
</script>
@endpush
@endsection
