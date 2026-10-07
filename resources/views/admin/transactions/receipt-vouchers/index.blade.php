@extends('admin.layouts.app')

@section('title', 'Receipt Voucher - VIKAS UDHYOG ERP')
@section('page_code', 'txn-receipt')

@section('content')
<section class="view-section active" id="view-txn-receipt">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Transactions</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Receipt Voucher</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-hand-holding-dollar text-primary"></i> Receipt Voucher
            </h1>
            <p class="erp-page-subtitle">
                Record customer sales collections, bank transfers, cash receipts &amp; other operational income inflows.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Receipt Register">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.transactions.receipt-voucher.create') }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-plus"></i> New Receipt Voucher
            </a>
        </div>
    </div>

    <!-- 4-Card KPI Summary Grid -->
    <div class="erp-kpi-grid">
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Receipts</div>
                <div class="erp-kpi-val">{{ number_format($totalCount) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Funds Received</div>
                <div class="erp-kpi-val erp-kpi-val-success font-monospace">₹{{ number_format($totalCollected, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #2563EB;">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 99, 235, 0.12); color: #2563EB;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Customer Collections</div>
                <div class="erp-kpi-val font-monospace" style="color: #2563EB;">₹{{ number_format($customerCollections, 2) }}</div>
            </div>
        </div>

        <div class="card erp-kpi-card" style="border-left: 4px solid #D97706;">
            <div class="erp-kpi-icon-box" style="background: rgba(217, 119, 6, 0.12); color: #D97706;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Direct Incomes</div>
                <div class="erp-kpi-val font-monospace" style="color: #D97706;">₹{{ number_format($directIncomes, 2) }}</div>
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

    <!-- Inline Filter Toolbar -->
    <div class="card erp-table-filter-header" style="margin-bottom: 1.25rem; padding: 1rem 1.25rem;">
        <form method="GET" action="{{ route('admin.transactions.receipt-voucher') }}" class="erp-filter-form" style="display: flex; gap: 0.85rem; flex-wrap: wrap; align-items: center; justify-content: space-between; width: 100%;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                <!-- Search -->
                <div class="erp-search-wrap" style="position: relative; min-width: 240px; flex: 1;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8;"></i>
                    <input type="text" name="search" class="form-control erp-search-input" placeholder="Search Voucher, Party, UTR, Ref..." value="{{ request('search') }}" style="padding-left: 2.25rem;">
                </div>

                <!-- Receipt Type -->
                <select name="receipt_type" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 140px;">
                    <option value="">All Types</option>
                    <option value="Customer" {{ request('receipt_type') === 'Customer' ? 'selected' : '' }}>Customer</option>
                    <option value="Income" {{ request('receipt_type') === 'Income' ? 'selected' : '' }}>Direct Income</option>
                </select>

                <!-- Payment Mode -->
                <select name="payment_mode" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 130px;">
                    <option value="">All Modes</option>
                    @foreach(['Cash', 'Bank Transfer', 'NEFT', 'RTGS', 'Cheque', 'UPI'] as $mode)
                        <option value="{{ $mode }}" {{ request('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select name="status" class="form-control erp-filter-select" onchange="this.form.submit()" style="width: auto; min-width: 120px;">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 0.95rem;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(request()->anyFilled(['search', 'receipt_type', 'payment_mode', 'customer_id', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('admin.transactions.receipt-voucher') }}" class="btn btn-outline" style="padding: 0.5rem 0.85rem;" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif
            </div>

            <div class="erp-table-summary-count">
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 600; padding: 0.45rem 0.75rem; border-radius: 6px;">
                    Showing {{ $vouchers->firstItem() ?? 0 }}-{{ $vouchers->lastItem() ?? 0 }} of {{ $vouchers->total() }} Vouchers
                </span>
            </div>
        </form>
    </div>

    <!-- Main Edge-to-Edge Data Table Card -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); margin-bottom: 2rem;">
        <div class="table-responsive">
            <table class="custom-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Voucher No &amp; Date</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Received From / Party</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Type</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Deposited Account</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0;">Payment Mode &amp; Ref</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: right;">Amount (₹)</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center;">Status</th>
                        <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #475569; border-bottom: 1px solid #E2E8F0; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                        <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                            <!-- Voucher No & Date -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="display: flex; flex-direction: column;">
                                    <a href="{{ route('admin.transactions.receipt-voucher.show', $voucher) }}" style="font-weight: 700; color: #5B841E; text-decoration: none; font-family: Consolas, 'SFMono-Regular', Menlo, Monaco, monospace; font-size: 0.92rem;">
                                        {{ $voucher->voucher_no }}
                                    </a>
                                    <span style="font-size: 0.8rem; color: #64748B; margin-top: 2px;">
                                        <i class="fa-regular fa-calendar" style="font-size: 0.72rem; margin-right: 3px;"></i>{{ $voucher->voucher_date->format('d M, Y') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Received From / Party -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="avatar" style="background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 700; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ $voucher->initials }}
                                    </div>
                                    <div>
                                        @if($voucher->receipt_type === 'Customer' && $voucher->customer)
                                            <a href="{{ route('admin.masters.customer.show', $voucher->customer_id) }}" style="font-weight: 600; color: #0F172A; text-decoration: none;">
                                                {{ $voucher->customer->name }}
                                            </a>
                                            <div style="font-size: 0.75rem; color: #64748B;">
                                                <span class="font-monospace">{{ $voucher->customer->code }}</span>
                                                @if($voucher->customer->city) &bull; {{ $voucher->customer->city }} @endif
                                            </div>
                                        @else
                                            <div style="font-weight: 600; color: #0F172A;">
                                                {{ $voucher->income_source ?: 'Direct Operational Income' }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: #64748B;">
                                                Non-debtor revenue receipt
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Receipt Type -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                @if($voucher->receipt_type === 'Customer')
                                    <span class="badge" style="background: rgba(37, 99, 235, 0.1); color: #2563EB; font-weight: 600; font-size: 0.76rem; padding: 4px 9px; border-radius: 9999px; border: 1px solid rgba(37, 99, 235, 0.25);">
                                        <i class="fa-solid fa-user-check" style="font-size: 0.65rem; margin-right: 3px;"></i> Customer
                                    </span>
                                @else
                                    <span class="badge" style="background: rgba(217, 119, 6, 0.1); color: #D97706; font-weight: 600; font-size: 0.76rem; padding: 4px 9px; border-radius: 9999px; border: 1px solid rgba(217, 119, 6, 0.25);">
                                        <i class="fa-solid fa-coins" style="font-size: 0.65rem; margin-right: 3px;"></i> Direct Income
                                    </span>
                                @endif
                            </td>

                            <!-- Deposited Account -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                @if($voucher->account)
                                    <div style="font-weight: 600; color: #1E293B; font-size: 0.88rem;">
                                        {{ $voucher->account->name }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748B;">
                                        <span class="font-monospace">{{ $voucher->account->code }}</span>
                                        @if($voucher->account->bank_name) &bull; {{ $voucher->account->bank_name }} @endif
                                    </div>
                                @else
                                    <span style="color: #94A3B8; font-size: 0.85rem; font-style: italic;">Unspecified Ledger</span>
                                @endif
                            </td>

                            <!-- Payment Mode & Reference -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <span class="badge" style="background: #F1F5F9; color: #334155; font-size: 0.76rem; font-weight: 600; padding: 3px 8px; border-radius: 6px; width: fit-content; border: 1px solid #E2E8F0;">
                                        @if($voucher->payment_mode === 'Cash')
                                            <i class="fa-solid fa-money-bill-1-wave" style="color: #059669; margin-right: 3px;"></i>
                                        @elseif($voucher->payment_mode === 'UPI')
                                            <i class="fa-solid fa-qrcode" style="color: #7C3AED; margin-right: 3px;"></i>
                                        @elseif($voucher->payment_mode === 'Cheque')
                                            <i class="fa-solid fa-money-check" style="color: #D97706; margin-right: 3px;"></i>
                                        @else
                                            <i class="fa-solid fa-building-columns" style="color: #2563EB; margin-right: 3px;"></i>
                                        @endif
                                        {{ $voucher->payment_mode }}
                                    </span>
                                    @if($voucher->reference_no)
                                        <span class="font-monospace" style="font-size: 0.75rem; color: #64748B;">
                                            Ref: {{ $voucher->reference_no }}
                                        </span>
                                    @endif
                                    @if($voucher->against_invoice)
                                        <span style="font-size: 0.73rem; color: #5B841E;">
                                            Inv: {{ $voucher->against_invoice }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Amount -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                <div class="font-monospace" style="font-weight: 700; font-size: 1rem; color: #059669;">
                                    ₹{{ number_format($voucher->amount, 2) }}
                                </div>
                            </td>

                            <!-- Status Toggle -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                <form action="{{ route('admin.transactions.receipt-voucher.toggle-status', $voucher) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    @if($voucher->status === 'active')
                                        <button type="submit" class="btn btn-sm erp-status-btn-active" title="Click to Cancel Voucher" onclick="return confirm('Cancel Receipt Voucher {{ $voucher->voucher_no }}? This will reverse credited balances.')" style="background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 9999px; font-weight: 600; font-size: 0.74rem; padding: 4px 12px; cursor: pointer;">
                                            <i class="fa-solid fa-circle" style="font-size: 0.45rem; margin-right: 4px; vertical-align: middle;"></i> Active
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm erp-status-btn-inactive" title="Click to Reactivate Voucher" onclick="return confirm('Reactivate Receipt Voucher {{ $voucher->voucher_no }}?')" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.3); border-radius: 9999px; font-weight: 600; font-size: 0.74rem; padding: 4px 12px; cursor: pointer;">
                                            <i class="fa-solid fa-circle" style="font-size: 0.45rem; margin-right: 4px; vertical-align: middle;"></i> Cancelled
                                        </button>
                                    @endif
                                </form>
                            </td>

                            <!-- Action Buttons -->
                            <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                    <a href="{{ route('admin.transactions.receipt-voucher.show', $voucher) }}" class="btn btn-sm btn-icon" title="View Voucher Profile" style="color: #475569; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: #F8FAFC; border: 1px solid #E2E8F0;">
                                        <i class="fa-solid fa-eye" style="font-size: 0.82rem;"></i>
                                    </a>
                                    <a href="{{ route('admin.transactions.receipt-voucher.edit', $voucher) }}" class="btn btn-sm btn-icon" title="Edit Voucher" style="color: #5B841E; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: rgba(91, 132, 30, 0.08); border: 1px solid rgba(91, 132, 30, 0.2);">
                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.82rem;"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-icon" title="Print Receipt Slip" onclick="window.open('{{ route('admin.transactions.receipt-voucher.show', $voucher) }}?print=1', '_blank')" style="color: #2563EB; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: rgba(37, 99, 235, 0.08); border: 1px solid rgba(37, 99, 235, 0.2);">
                                        <i class="fa-solid fa-print" style="font-size: 0.82rem;"></i>
                                    </button>
                                    <form action="{{ route('admin.transactions.receipt-voucher.destroy', $voucher) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete Voucher {{ $voucher->voucher_no }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon" title="Delete Voucher" style="color: #DC2626; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); cursor: pointer;">
                                            <i class="fa-solid fa-trash-can" style="font-size: 0.82rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3.5rem 1rem; color: #64748B;">
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                    <div style="width: 68px; height: 68px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; color: #94A3B8; margin-bottom: 1rem;">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem;">No Receipt Vouchers Found</h4>
                                    <p style="font-size: 0.875rem; color: #64748B; max-width: 420px; margin-bottom: 1.25rem;">
                                        No receipt entries matching your filter criteria. Click below to record a new receipt collection.
                                    </p>
                                    <a href="{{ route('admin.transactions.receipt-voucher.create') }}" class="btn btn-primary">
                                        <i class="fa-solid fa-plus"></i> Record First Receipt Voucher
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
            <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; background: #FFFFFF; display: flex; justify-content: flex-end;">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
