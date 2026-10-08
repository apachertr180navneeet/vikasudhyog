@extends('admin.layouts.app')

@section('title', 'Cash & Bank Register - VIKAS UDHYOG ERP')
@section('page_code', 'rpt-cash-reg')

@section('content')
<section class="view-section active" id="view-rpt-cash-reg">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Reports</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Cash &amp; Bank Register</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-wallet text-primary"></i> Cash &amp; Bank Register &amp; Daybook
            </h1>
            <p class="erp-page-subtitle">
                Unified real-time financial daybook tracking liquid cash &amp; bank inflows (Dr), outflows (Cr), fund transfers &amp; closing balances.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Daybook Register">
                <i class="fa-solid fa-print"></i> Print Register
            </button>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline" title="Export Filtered Register to CSV">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.transactions.receipt-voucher.create') }}" class="btn btn-outline" style="border-color: #059669; color: #059669;" title="Record Inward Cash/Bank Receipt">
                <i class="fa-solid fa-arrow-down me-1"></i> New Receipt
            </a>
            <a href="{{ route('admin.transactions.payment-voucher.create') }}" class="btn btn-primary erp-btn-header-primary" title="Record Outward Cash/Bank Payment">
                <i class="fa-solid fa-arrow-up me-1"></i> New Payment
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success" style="margin-bottom: 1.25rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- 4-Card KPI Analytics Grid -->
    <div class="erp-kpi-grid">
        <!-- 1. Total Inflows (Receipts Dr) -->
        <div class="card erp-kpi-card erp-kpi-success" style="border-left: 4px solid #059669;">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-arrow-down-left"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Inflow (Debit / Dr)</div>
                <div class="erp-kpi-val font-monospace" style="color: #059669;">
                    ₹{{ number_format($stats['total_receipts_amount'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    {{ $stats['total_receipts_count'] ?? 0 }} Receipts Recorded
                </div>
            </div>
        </div>

        <!-- 2. Total Outflows (Payments Cr) -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #DC2626;">
            <div class="erp-kpi-icon-box" style="background: rgba(220, 38, 38, 0.12); color: #DC2626;">
                <i class="fa-solid fa-arrow-up-right"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Total Outflow (Credit / Cr)</div>
                <div class="erp-kpi-val font-monospace" style="color: #DC2626;">
                    ₹{{ number_format($stats['total_payments_amount'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    {{ $stats['total_payments_count'] ?? 0 }} Payments Disbursed
                </div>
            </div>
        </div>

        <!-- 3. Net Cash Movement -->
        <div class="card erp-kpi-card" style="border-left: 4px solid {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
            <div class="erp-kpi-icon-box" style="background: {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? 'rgba(5, 150, 105, 0.12)' : 'rgba(220, 38, 38, 0.12)' }}; color: {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Net Movement (Period)</div>
                <div class="erp-kpi-val font-monospace" style="color: {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                    {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? '+' : '' }}₹{{ number_format($stats['net_cash_movement'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? 'Net Surplus' : 'Net Deficit' }}
                </div>
            </div>
        </div>

        <!-- 4. Total Available Liquid Balance -->
        <div class="card erp-kpi-card erp-kpi-primary" style="border-left: 4px solid #5B841E;">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Closing Liquid Balance</div>
                <div class="erp-kpi-val font-monospace" style="color: #5B841E;">
                    ₹{{ number_format($stats['total_liquid_balance'] ?? 0, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Available across all Bank &amp; Cash Accounts
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Card -->
    <div class="card erp-table-filter-header" style="margin-bottom: 1.25rem; padding: 1.15rem; background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0;">
        
        <!-- Date Preset Shortcut Pills & Mode Switcher -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.85rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em; margin-right: 4px;">
                    <i class="fa-regular fa-calendar-check me-1"></i> Quick Presets:
                </span>
                @php
                    $presets = [
                        'all'         => 'All Past',
                        'today'       => 'Today',
                        'this_week'   => 'This Week',
                        'this_month'  => 'This Month',
                        'last_month'  => 'Last Month',
                        'this_quarter'=> 'This Quarter',
                        'this_fy'     => 'This FY',
                    ];
                    $currentPreset = $filters['date_preset'] ?? 'all';
                @endphp
                @foreach($presets as $pKey => $pLabel)
                    <a href="{{ request()->fullUrlWithQuery(['date_preset' => $pKey, 'from_date' => null, 'to_date' => null]) }}" 
                       class="btn btn-sm {{ $currentPreset === $pKey ? 'btn-primary' : 'btn-outline' }}" 
                       style="padding: 3px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 9999px;">
                        {{ $pLabel }}
                    </a>
                @endforeach
            </div>

            <!-- View Mode Tabs (Pill Switcher) -->
            <div style="display: inline-flex; background: #F1F5F9; padding: 3px; border-radius: 10px; border: 1px solid #E2E8F0;">
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'daybook']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'daybook' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-book-open me-1"></i> Daybook Register
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'receipts']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'receipts' ? 'background: #FFFFFF; color: #059669; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-arrow-down-left me-1"></i> Receipts (Dr)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'payments']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'payments' ? 'background: #FFFFFF; color: #DC2626; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-arrow-up-right me-1"></i> Payments (Cr)
                </a>
                <a href="{{ request()->fullUrlWithQuery(['view_mode' => 'accounts']) }}" 
                   class="btn btn-sm" 
                   style="padding: 4px 14px; font-size: 0.78rem; font-weight: 700; border-radius: 8px; border: none; transition: all 0.2s ease; {{ $viewMode === 'accounts' ? 'background: #FFFFFF; color: #0F172A; box-shadow: 0 2px 6px rgba(0,0,0,0.08);' : 'background: transparent; color: #64748B;' }}">
                    <i class="fa-solid fa-landmark me-1"></i> Accounts Summary
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.reports.cash-bank-register') }}" method="GET" class="erp-filter-form" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; width: 100%;">
            <input type="hidden" name="view_mode" value="{{ $viewMode }}">
            <input type="hidden" name="date_preset" value="{{ $currentPreset === 'custom' ? 'custom' : $currentPreset }}">

            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; flex: 1;">
                
                <!-- Keyword Search -->
                <div class="erp-search-wrap" style="min-width: 220px; flex: 1; max-width: 320px;">
                    <i class="fa-solid fa-magnifying-glass erp-search-icon"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search Voucher, Party, Ref No..." class="form-control erp-search-input">
                </div>

                <!-- Custom Dates -->
                <div style="display: flex; align-items: center; gap: 4px;">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">FROM:</span>
                    <input type="date" name="from_date" class="form-control erp-filter-select" value="{{ $filters['from_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748B;">TO:</span>
                    <input type="date" name="to_date" class="form-control erp-filter-select" value="{{ $filters['to_date'] ?? '' }}" style="width: 135px; padding: 0.35rem 0.5rem;" onchange="document.querySelector('input[name=date_preset]').value='custom';">
                </div>

                <!-- Account Selector -->
                <select name="account_id" class="form-control erp-filter-select" style="min-width: 180px;">
                    <option value="">All Cash &amp; Bank Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ ($filters['account_id'] ?? '') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }} ({{ $acc->code }})
                        </option>
                    @endforeach
                </select>

                <!-- Payment Mode Selector -->
                <select name="payment_mode" class="form-control erp-filter-select" style="min-width: 140px;">
                    <option value="all">All Payment Modes</option>
                    <option value="Cash" {{ ($filters['payment_mode'] ?? '') === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="Bank Transfer" {{ ($filters['payment_mode'] ?? '') === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="UPI" {{ ($filters['payment_mode'] ?? '') === 'UPI' ? 'selected' : '' }}>UPI / QR</option>
                    <option value="NEFT" {{ ($filters['payment_mode'] ?? '') === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                    <option value="RTGS" {{ ($filters['payment_mode'] ?? '') === 'RTGS' ? 'selected' : '' }}>RTGS</option>
                    <option value="Cheque" {{ ($filters['payment_mode'] ?? '') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                </select>

            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; margin-left: auto;">
                <button type="submit" class="btn btn-primary btn-sm erp-btn-filter" style="padding: 0.45rem 1rem;">
                    <i class="fa-solid fa-filter me-1"></i> Apply Filter
                </button>
                <a href="{{ route('admin.reports.cash-bank-register', ['view_mode' => $viewMode]) }}" class="btn btn-outline btn-sm erp-btn-filter-clear" style="padding: 0.45rem 0.85rem;" title="Reset Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

    </div>

    <!-- Edge-to-Edge Data Card -->
    <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); background: #FFFFFF;">

        <!-- MODE 1: DAYBOOK REGISTER TABLE -->
        @if($viewMode === 'daybook')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Date &amp; Voucher</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Type</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Account Head</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Particulars / Party</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Mode &amp; Ref</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #059669; text-align: right;">Debit (Dr) ₹</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #DC2626; text-align: right;">Credit (Cr) ₹</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;" class="erp-actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($daybookData as $item)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Date & Voucher -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.88rem; color: #0F172A;">
                                        {{ $item->voucher_date ? $item->voucher_date->format('d M Y') : '—' }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.78rem; font-weight: 700; color: {{ $item->type === 'Receipt' ? '#059669' : '#DC2626' }}; margin-top: 2px;">
                                        {{ $item->voucher_no }}
                                    </div>
                                </td>

                                <!-- Type -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    @if($item->type === 'Receipt')
                                        <span class="badge" style="background: rgba(5, 150, 105, 0.12); color: #059669; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(5, 150, 105, 0.25);">
                                            <i class="fa-solid fa-arrow-down-left me-1"></i> Receipt (Dr)
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(220, 38, 38, 0.12); color: #DC2626; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px; border: 1px solid rgba(220, 38, 38, 0.25);">
                                            <i class="fa-solid fa-arrow-up-right me-1"></i> Payment (Cr)
                                        </span>
                                    @endif
                                </td>

                                <!-- Account Head -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #1E293B; font-size: 0.88rem;">
                                        {{ $item->account_name }}
                                    </div>
                                </td>

                                <!-- Particulars / Party -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                        {{ $item->party_name }}
                                    </div>
                                    @if($item->against_invoice && $item->against_invoice !== '—')
                                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 2px;">
                                            Ref Bill: <span class="font-monospace" style="font-weight: 600;">{{ $item->against_invoice }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Mode & Ref -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge" style="background: #EEF2F6; color: #475569; font-weight: 600; font-size: 0.72rem; padding: 2px 6px;">
                                        {{ $item->payment_mode }}
                                    </span>
                                    @if($item->reference_no && $item->reference_no !== '—')
                                        <div class="font-monospace" style="font-size: 0.7rem; color: #64748B; margin-top: 2px;">
                                            {{ $item->reference_no }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Debit (Dr) -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    @if($item->debit > 0)
                                        <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: #059669;">
                                            ₹{{ number_format($item->debit, 2) }}
                                        </div>
                                    @else
                                        <span style="color: #CBD5E1;">—</span>
                                    @endif
                                </td>

                                <!-- Credit (Cr) -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    @if($item->credit > 0)
                                        <div class="font-monospace" style="font-weight: 800; font-size: 0.98rem; color: #DC2626;">
                                            ₹{{ number_format($item->credit, 2) }}
                                        </div>
                                    @else
                                        <span style="color: #CBD5E1;">—</span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;" class="erp-actions-cell">
                                    <a href="{{ $item->view_url }}" 
                                       class="btn btn-sm btn-icon" 
                                       style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #5B841E; display: inline-flex; align-items: center; justify-content: center;" 
                                       title="View Voucher Record">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #94A3B8; margin: 0 auto 0.85rem;">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <h4 style="font-weight: 700; color: #1E293B; margin-bottom: 0.35rem; font-size: 1.15rem;">No Daybook Entries Found</h4>
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No cash or bank transactions recorded for selected criteria.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($daybookData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                            <tr>
                                <td colspan="5" style="padding: 0.95rem 1.15rem; color: #1E293B; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.04em;">
                                    Filtered Totals ({{ $daybookData->total() }} Transactions):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #059669; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($stats['total_receipts_amount'] ?? 0, 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #DC2626; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($stats['total_payments_amount'] ?? 0, 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: center; color: #64748B; font-size: 0.8rem;">
                                    Net: <strong class="font-monospace" style="color: {{ ($stats['net_cash_movement'] ?? 0) >= 0 ? '#059669' : '#DC2626' }};">{{ ($stats['net_cash_movement'] ?? 0) >= 0 ? '+' : '' }}₹{{ number_format($stats['net_cash_movement'] ?? 0, 2) }}</strong>
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($daybookData->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FFFFFF; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="font-size: 0.82rem; color: #64748B;">
                        Showing {{ $daybookData->firstItem() ?? 0 }} to {{ $daybookData->lastItem() ?? 0 }} of {{ $daybookData->total() }} daybook entries
                    </div>
                    <div>
                        {{ $daybookData->links() }}
                    </div>
                </div>
            @endif

        <!-- MODE 2: RECEIPTS REGISTER (DR) -->
        @elseif($viewMode === 'receipts')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Voucher No &amp; Date</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Customer / Source</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Receiving Account</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Mode &amp; Ref</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Against Invoice</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #059669; text-align: right;">Amount (Dr ₹)</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;" class="erp-actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receiptsData as $rcv)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Voucher No & Date -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.88rem; color: #0F172A;">
                                        {{ $rcv->voucher_date ? $rcv->voucher_date->format('d M Y') : '—' }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.78rem; font-weight: 700; color: #059669; margin-top: 2px;">
                                        {{ $rcv->voucher_no }}
                                    </div>
                                </td>

                                <!-- Customer / Source -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                        {{ $rcv->receipt_type === 'Customer' && $rcv->customer ? $rcv->customer->name : ($rcv->income_source ?: 'Direct Inflow') }}
                                    </div>
                                    @if($rcv->customer)
                                        <div style="font-size: 0.74rem; color: #64748B;">
                                            <span class="font-monospace">{{ $rcv->customer->code }}</span>
                                            @if($rcv->customer->city)
                                                &bull; {{ $rcv->customer->city }}
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Receiving Account -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #1E293B; font-size: 0.88rem;">
                                        {{ $rcv->account ? $rcv->account->name : 'Cash/Bank' }}
                                    </div>
                                </td>

                                <!-- Mode & Ref -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge" style="background: #EEF2F6; color: #475569; font-weight: 600; font-size: 0.72rem; padding: 2px 6px;">
                                        {{ $rcv->payment_mode }}
                                    </span>
                                    @if($rcv->reference_no)
                                        <div class="font-monospace" style="font-size: 0.7rem; color: #64748B; margin-top: 2px;">
                                            {{ $rcv->reference_no }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Against Invoice -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="font-monospace" style="font-size: 0.82rem; color: #334155;">
                                        {{ $rcv->against_invoice ?: 'On Account' }}
                                    </span>
                                </td>

                                <!-- Amount Received -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #059669;">
                                        ₹{{ number_format((float)$rcv->amount, 2) }}
                                    </div>
                                </td>

                                <!-- Action -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;" class="erp-actions-cell">
                                    <a href="{{ route('admin.transactions.receipt-voucher.show', $rcv->id) }}" 
                                       class="btn btn-sm btn-icon" 
                                       style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #059669; display: inline-flex; align-items: center; justify-content: center;" 
                                       title="View Receipt Voucher">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No receipt records match your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($receiptsData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                            <tr>
                                <td colspan="5" style="padding: 0.95rem 1.15rem; color: #1E293B; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.04em;">
                                    Total Receipts ({{ $receiptsData->total() }} Vouchers):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #059669; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($stats['total_receipts_amount'] ?? 0, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($receiptsData->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FFFFFF; flex-wrap: gap: 0.75rem;">
                    <div style="font-size: 0.82rem; color: #64748B;">
                        Showing {{ $receiptsData->firstItem() ?? 0 }} to {{ $receiptsData->lastItem() ?? 0 }} of {{ $receiptsData->total() }} receipts
                    </div>
                    <div>
                        {{ $receiptsData->links() }}
                    </div>
                </div>
            @endif

        <!-- MODE 3: PAYMENTS REGISTER (CR) -->
        @elseif($viewMode === 'payments')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Voucher No &amp; Date</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Vendor / Expense Head</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Disbursed From Account</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Mode &amp; Ref</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Against Bill</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #DC2626; text-align: right;">Amount (Cr ₹)</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;" class="erp-actions-cell">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentsData as $pay)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Voucher No & Date -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.88rem; color: #0F172A;">
                                        {{ $pay->voucher_date ? $pay->voucher_date->format('d M Y') : '—' }}
                                    </div>
                                    <div class="font-monospace" style="font-size: 0.78rem; font-weight: 700; color: #DC2626; margin-top: 2px;">
                                        {{ $pay->voucher_no }}
                                    </div>
                                </td>

                                <!-- Vendor / Expense Head -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                        {{ $pay->payment_type === 'Vendor' && $pay->vendor ? $pay->vendor->name : ($pay->expense_head ?: 'Direct Expense') }}
                                    </div>
                                    @if($pay->vendor)
                                        <div style="font-size: 0.74rem; color: #64748B;">
                                            <span class="font-monospace">{{ $pay->vendor->code }}</span>
                                            @if($pay->vendor->city)
                                                &bull; {{ $pay->vendor->city }}
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <!-- Paid From Account -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 600; color: #1E293B; font-size: 0.88rem;">
                                        {{ $pay->account ? $pay->account->name : 'Cash/Bank' }}
                                    </div>
                                </td>

                                <!-- Mode & Ref -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge" style="background: #EEF2F6; color: #475569; font-weight: 600; font-size: 0.72rem; padding: 2px 6px;">
                                        {{ $pay->payment_mode }}
                                    </span>
                                    @if($pay->reference_no)
                                        <div class="font-monospace" style="font-size: 0.7rem; color: #64748B; margin-top: 2px;">
                                            {{ $pay->reference_no }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Against Bill -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="font-monospace" style="font-size: 0.82rem; color: #334155;">
                                        {{ $pay->against_invoice ?: 'Advance / Expense' }}
                                    </span>
                                </td>

                                <!-- Amount Paid -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #DC2626;">
                                        ₹{{ number_format((float)$pay->amount, 2) }}
                                    </div>
                                </td>

                                <!-- Action -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: center;" class="erp-actions-cell">
                                    <a href="{{ route('admin.transactions.payment-voucher.show', $pay->id) }}" 
                                       class="btn btn-sm btn-icon" 
                                       style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #DC2626; display: inline-flex; align-items: center; justify-content: center;" 
                                       title="View Payment Voucher">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No payment records match your filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($paymentsData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                            <tr>
                                <td colspan="5" style="padding: 0.95rem 1.15rem; color: #1E293B; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.04em;">
                                    Total Payments ({{ $paymentsData->total() }} Vouchers):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #DC2626; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($stats['total_payments_amount'] ?? 0, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            @if($paymentsData->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; background: #FFFFFF; flex-wrap: gap: 0.75rem;">
                    <div style="font-size: 0.82rem; color: #64748B;">
                        Showing {{ $paymentsData->firstItem() ?? 0 }} to {{ $paymentsData->lastItem() ?? 0 }} of {{ $paymentsData->total() }} payments
                    </div>
                    <div>
                        {{ $paymentsData->links() }}
                    </div>
                </div>
            @endif

        <!-- MODE 4: ACCOUNTS SUMMARY TABLE -->
        @elseif($viewMode === 'accounts')
            <div class="table-responsive">
                <table class="custom-table" style="width: 100%; margin-bottom: 0; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Account Code</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Account Name &amp; Details</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Group</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Opening Balance</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #059669; text-align: right;">Period Inflow (Dr)</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #DC2626; text-align: right;">Period Outflow (Cr)</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Net Movement</th>
                            <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #0F172A; text-align: right;">Current Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accountsData as $accRow)
                            <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                <!-- Code -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge font-monospace" style="background: #F1F5F9; color: #0F172A; font-size: 0.78rem; font-weight: 700; padding: 4px 8px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                        {{ $accRow->code }}
                                    </span>
                                </td>

                                <!-- Name & Details -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #1E293B; font-size: 0.92rem;">
                                        {{ $accRow->name }}
                                    </div>
                                    @if($accRow->bank_name !== '—')
                                        <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                                            {{ $accRow->bank_name }} &bull; A/c: <span class="font-monospace">{{ $accRow->account_number }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- Group -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle;">
                                    <span class="badge" style="background: {{ $accRow->group === 'Cash in Hand' ? 'rgba(91, 132, 30, 0.12)' : 'rgba(2, 132, 199, 0.12)' }}; color: {{ $accRow->group === 'Cash in Hand' ? '#5B841E' : '#0284C7' }}; font-weight: 700; font-size: 0.74rem; padding: 3px 8px; border-radius: 6px;">
                                        {{ $accRow->group }}
                                    </span>
                                </td>

                                <!-- Opening Balance -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.9rem; color: #475569;">
                                        ₹{{ number_format($accRow->opening_balance, 2) }}
                                    </div>
                                </td>

                                <!-- Inflow -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #059669;">
                                        ₹{{ number_format($accRow->inflow, 2) }}
                                    </div>
                                </td>

                                <!-- Outflow -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 700; font-size: 0.92rem; color: #DC2626;">
                                        ₹{{ number_format($accRow->outflow, 2) }}
                                    </div>
                                </td>

                                <!-- Net Movement -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 0.92rem; color: {{ $accRow->net_change >= 0 ? '#059669' : '#DC2626' }};">
                                        {{ $accRow->net_change >= 0 ? '+' : '' }}₹{{ number_format($accRow->net_change, 2) }}
                                    </div>
                                </td>

                                <!-- Current Balance -->
                                <td style="padding: 0.95rem 1.15rem; vertical-align: middle; text-align: right;">
                                    <div class="font-monospace" style="font-weight: 800; font-size: 1rem; color: #0F172A;">
                                        ₹{{ number_format($accRow->current_balance, 2) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 4rem 1rem; color: #64748B;">
                                    <p style="font-size: 0.88rem; color: #64748B; margin: 0;">No active cash or bank accounts found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($accountsData && $accountsData->count() > 0)
                        <tfoot style="background: #F8FAFC; border-top: 2px solid #E2E8F0; font-weight: 700;">
                            <tr>
                                <td colspan="3" style="padding: 0.95rem 1.15rem; color: #1E293B; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.04em;">
                                    Total Liquid Holdings ({{ $accountsData->count() }} Accounts):
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #475569;" class="font-monospace">
                                    ₹{{ number_format($accountsData->sum('opening_balance'), 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #059669;" class="font-monospace">
                                    ₹{{ number_format($accountsData->sum('inflow'), 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #DC2626;" class="font-monospace">
                                    ₹{{ number_format($accountsData->sum('outflow'), 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #0F172A;" class="font-monospace">
                                    {{ $accountsData->sum('net_change') >= 0 ? '+' : '' }}₹{{ number_format($accountsData->sum('net_change'), 2) }}
                                </td>
                                <td style="padding: 0.95rem 1.15rem; text-align: right; color: #5B841E; font-size: 1.05rem;" class="font-monospace">
                                    ₹{{ number_format($accountsData->sum('current_balance'), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        @endif

    </div>

</section>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #view-rpt-cash-reg, #view-rpt-cash-reg * {
        visibility: visible;
    }
    #view-rpt-cash-reg {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .sidebar, .navbar, .erp-page-top-bar .erp-header-actions, .erp-table-filter-header, .erp-actions-cell, .pagination {
        display: none !important;
    }
    .erp-main-card {
        box-shadow: none !important;
        border: 1px solid #CBD5E1 !important;
    }
}
</style>
@endsection
