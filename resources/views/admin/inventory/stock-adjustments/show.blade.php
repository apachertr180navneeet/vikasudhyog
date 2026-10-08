@extends('admin.layouts.app')

@section('title', 'Audit ' . $adjustment->adjustment_no . ' Profile - VIKAS UDHYOG ERP')
@section('page_code', 'inv-adjustment')

@section('content')
<section class="view-section active" id="view-adj-show">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Inventory</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.inventory.stock-adjustment') }}">Stock Adjustment</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $adjustment->adjustment_no }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-clipboard-check text-primary"></i> Stock Adjustment {{ $adjustment->adjustment_no }}
            </h1>
            <p class="erp-page-subtitle">
                Complete reconciliation dossier, balance impacts, variance justification, and auditor inspection logs.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Audit Certificate">
                <i class="fa-solid fa-print"></i> Print Dossier
            </button>
            <a href="{{ route('admin.inventory.stock-adjustment.edit', $adjustment->id) }}" class="btn btn-primary" title="Edit Adjustment">
                <i class="fa-solid fa-pen-to-square"></i> Edit Adjustment
            </a>
            <a href="{{ route('admin.inventory.stock-adjustment') }}" class="btn btn-outline" title="Back to Audit List">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    @php
        $isAdd = $adjustment->type === 'add';
        $item = $adjustment->item;
    @endphp

    <!-- Hero Identity Profile Banner -->
    <div class="card" style="background: #FFFFFF; border-radius: 16px; border: 1px solid #E2E8F0; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 1.15rem;">
                <div style="width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, {{ $isAdd ? '#10B981, #047857' : '#DC2626, #991B1B' }}); color: #FFFFFF; font-weight: 800; font-size: 1.35rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
                    <i class="fa-solid {{ $isAdd ? 'fa-plus' : 'fa-minus' }}"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <h2 style="margin: 0; font-size: 1.45rem; font-weight: 800; color: #0F172A;">
                            {{ $adjustment->adjustment_no }}
                        </h2>
                        <span class="badge" style="background: {{ $isAdd ? 'rgba(16, 185, 129, 0.12)' : 'rgba(220, 38, 38, 0.12)' }}; color: {{ $isAdd ? '#059669' : '#DC2626' }}; border: 1px solid {{ $isAdd ? 'rgba(16, 185, 129, 0.25)' : 'rgba(220, 38, 38, 0.25)' }}; font-size: 0.76rem; font-weight: 700; padding: 3px 10px; border-radius: 9999px;">
                            {{ $isAdd ? '+ Surplus Inflow' : '- Physical Reduction' }}
                        </span>
                        <span class="badge font-monospace" style="background: #F1F5F9; color: #475569; font-size: 0.76rem; padding: 3px 8px; border: 1px solid #E2E8F0;">
                            Audited: {{ $adjustment->adjustment_date ? $adjustment->adjustment_date->format('d M Y') : '-' }}
                        </span>
                    </div>
                    <div style="margin-top: 0.35rem; font-size: 0.92rem; color: #475569;">
                        Reconciled Product: <strong>{{ $item ? $item->name : 'N/A' }}</strong>
                        @if($item)
                            <span class="badge font-monospace" style="background: #F8FAFC; color: #64748B; font-size: 0.75rem; border: 1px solid #E2E8F0; margin-left: 4px;">{{ $item->code }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('admin.inventory.item-ledger', ['item_id' => $adjustment->item_id]) }}" class="btn btn-outline" style="font-size: 0.82rem;" title="View Complete Stock Ledger">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> View Item Stock Ledger
                </a>
            </div>
        </div>
    </div>

    <!-- 4-Stat KPI Ribbon -->
    <div class="erp-kpi-grid" style="margin-bottom: 1.5rem;">
        <!-- 1. Variance Quantity -->
        <div class="card erp-kpi-card {{ $isAdd ? 'erp-kpi-success' : 'erp-kpi-danger' }}">
            <div class="erp-kpi-icon-box {{ $isAdd ? 'erp-kpi-icon-success' : 'erp-kpi-icon-danger' }}">
                <i class="fa-solid {{ $isAdd ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Reconciled Variance Qty</div>
                <div class="erp-kpi-val" style="color: {{ $isAdd ? '#059669' : '#DC2626' }};">
                    {{ $isAdd ? '+' : '-' }}{{ number_format($adjustment->quantity, 2) }} {{ $adjustment->unit }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    {{ $isAdd ? 'Physical Count Gain' : 'Physical Reduction' }}
                </div>
            </div>
        </div>

        <!-- 2. System Stock Before Audit -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Digital Stock Prior to Audit</div>
                <div class="erp-kpi-val">
                    {{ number_format($adjustment->previous_stock, 2) }} {{ $adjustment->unit }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Ledger Balance Before Variance
                </div>
            </div>
        </div>

        <!-- 3. Reconciled Balance After Audit -->
        <div class="card erp-kpi-card erp-kpi-info">
            <div class="erp-kpi-icon-box erp-kpi-icon-info">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Reconciled Stock Balance</div>
                <div class="erp-kpi-val" style="color: #0284C7;">
                    {{ number_format($adjustment->new_stock, 2) }} {{ $adjustment->unit }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;">
                    Physical Floor Count Balance
                </div>
            </div>
        </div>

        <!-- 4. Valuation Impact -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-indian-rupee-sign"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Valuation Delta Impact</div>
                <div class="erp-kpi-val" style="color: {{ $isAdd ? '#059669' : '#DC2626' }};">
                    {{ $isAdd ? '+' : '-' }}₹{{ number_format($adjustment->total_value, 2) }}
                </div>
                <div style="font-size: 0.74rem; color: #64748B; margin-top: 2px;" class="font-monospace">
                    Rate: ₹{{ number_format($adjustment->rate, 2) }}/{{ $adjustment->unit }}
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Detailed Breakdown -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        
        <!-- Column 1: Inventory & Product Specifications -->
        <div class="card" style="background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.65rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(91, 132, 30, 0.1); color: #5B841E; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #0F172A;">Product Specifications</h3>
                    <p style="margin: 0; font-size: 0.76rem; color: #64748B;">Reconciled herbal catalog item identity</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Herbal Product Name:</span>
                    <strong style="color: #0F172A;">{{ $item ? $item->name : 'N/A' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Product SKU Code:</span>
                    <span class="font-monospace fw-bold">{{ $item ? $item->code : 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Category:</span>
                    <span class="badge" style="background: #F1F5F9; color: #475569;">{{ $item ? $item->category : 'General' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Measurement Unit:</span>
                    <strong>{{ $adjustment->unit }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Standard Unit Rate:</span>
                    <span class="font-monospace fw-bold">₹{{ number_format($adjustment->rate, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Current Live Catalog Stock:</span>
                    <span class="font-monospace fw-bold" style="color: #059669;">
                        {{ $item ? number_format($item->current_stock, 2) : '0.00' }} {{ $adjustment->unit }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Column 2: Audit Logs & Justification -->
        <div class="card" style="background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.65rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #0F172A;">Audit Justification &amp; Control</h3>
                    <p style="margin: 0; font-size: 0.76rem; color: #64748B;">Internal compliance, reason and timestamps</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.85rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Audit Reason Category:</span>
                    <strong style="color: #0F172A;">{{ $adjustment->reason }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Auditor / Operator:</span>
                    <strong>{{ $adjustment->audited_by ?: ($adjustment->user ? $adjustment->user->name : 'System') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Audit Date:</span>
                    <span class="fw-bold">{{ $adjustment->adjustment_date ? $adjustment->adjustment_date->format('d M Y') : '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Logged On:</span>
                    <span>{{ $adjustment->created_at ? $adjustment->created_at->format('d M Y, h:i A') : '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F8FAFC; padding-bottom: 0.5rem;">
                    <span style="color: #64748B;">Last Modified:</span>
                    <span>{{ $adjustment->updated_at ? $adjustment->updated_at->format('d M Y, h:i A') : '-' }}</span>
                </div>
                <div>
                    <span style="color: #64748B; display: block; margin-bottom: 4px;">Auditor Remarks / Notes:</span>
                    <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem; color: #334155; font-style: italic; min-height: 48px;">
                        {{ $adjustment->notes ?: 'No notes or special remarks logged.' }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Danger Zone Rollback Card -->
    <div class="card" style="background: #FFF5F5; border-radius: 14px; border: 1px solid #FED7D7; padding: 1.25rem 1.5rem; margin-top: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-weight: 700; color: #C53030; font-size: 0.95rem;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Revert &amp; Rollback Adjustment
            </div>
            <div style="font-size: 0.8rem; color: #9B2C2C; margin-top: 2px;">
                Permanently cancel this audit entry and restore {{ number_format($adjustment->quantity, 2) }} {{ $adjustment->unit }} back into item inventory.
            </div>
        </div>
        <div>
            <form action="{{ route('admin.inventory.stock-adjustment.destroy', $adjustment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel and reverse this stock adjustment?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="font-size: 0.84rem;">
                    <i class="fa-solid fa-trash-can me-1"></i> Rollback Adjustment
                </button>
            </form>
        </div>
    </div>

</section>
@endsection
