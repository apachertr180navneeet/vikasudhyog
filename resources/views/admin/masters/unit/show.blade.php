@extends('admin.layouts.app')

@section('title', $unit->name . ' (' . $unit->code . ') - Unit Dossier - VIKAS UDHYOG ERP')
@section('page_code', 'master-unit')

@section('content')
<section class="view-section active" id="view-unit-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.unit') }}">Unit Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $unit->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-scale-balanced text-primary"></i> Unit Dossier &amp; Conversion Relation
            </h1>
            <p class="erp-page-subtitle">
                Complete specifications, GST UQC classification &amp; conversion ratio breakdown (e.g. 100 cm = 1 m).
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Unit Dossier">
                <i class="fa-solid fa-print"></i> Print Dossier
            </button>
            <a href="{{ route('admin.masters.unit.edit', $unit->id) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Unit
            </a>
            <a href="{{ route('admin.masters.unit') }}" class="btn btn-outline">
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
                    {{ $unit->initials }}
                </div>
                <div>
                    <h2 class="erp-profile-hero-name">
                        {{ $unit->name }}
                    </h2>
                    <div class="erp-profile-hero-meta-row">
                        <!-- Symbol Pill -->
                        <span class="erp-profile-username-pill font-monospace" title="Unit Symbol">
                            <i class="fa-solid fa-signature me-1"></i>Symbol: {{ $unit->code }}
                        </span>

                        <!-- GST UQC Pill -->
                        @if($unit->uqc_code)
                            <span class="erp-profile-role-pill font-monospace" title="GST Reporting UQC">
                                <i class="fa-solid fa-file-invoice me-1"></i>UQC: {{ $unit->uqc_code }}
                            </span>
                        @endif

                        <!-- Classification Pill -->
                        @if($unit->is_base_unit || empty($unit->base_unit_id))
                            <span class="erp-profile-role-pill" title="Primary Base Standard">
                                <i class="fa-solid fa-cube me-1"></i>Primary Base Unit
                            </span>
                        @else
                            <span class="erp-profile-role-pill" title="Derived Sub-Unit with Relation">
                                <i class="fa-solid fa-arrows-split-up-and-left me-1"></i>Derived Relation Unit
                            </span>
                        @endif

                        <!-- Relation Formula Pill -->
                        @if(!$unit->is_base_unit && $unit->baseUnit)
                            <span class="erp-profile-username-pill font-monospace" style="background: rgba(16, 185, 129, 0.25); border-color: rgba(16, 185, 129, 0.4); color: #FFFFFF;" title="Conversion Formula">
                                <i class="fa-solid fa-link me-1"></i>{{ $unit->relation_formula }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Hero Action & Status Column -->
            <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end">
                <!-- Status Toggle Button Form -->
                <form action="{{ route('admin.masters.unit.toggle-status', $unit->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    @method('PATCH')
                    @if($unit->status === 'active')
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-active" title="Click to Deactivate Unit">
                            <span class="erp-profile-status-dot-active"></span> Status: Active
                        </button>
                    @else
                        <button type="submit" class="erp-profile-status-btn erp-profile-status-btn-inactive" title="Click to Activate Unit">
                            <span class="erp-profile-status-dot-inactive"></span> Status: Inactive
                        </button>
                    @endif
                </form>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.masters.unit.edit', $unit->id) }}" class="btn" style="background: rgba(255,255,255,0.18); color: #FFFFFF; border: 1px solid rgba(255,255,255,0.3); border-radius: 8px; font-size: 0.82rem; padding: 0.45rem 0.85rem; font-weight: 600; backdrop-filter: blur(6px);" title="Modify Unit Record">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Specifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat KPI 4-Card Ribbon -->
    <div class="erp-kpi-grid mb-4">
        <!-- Hierarchy Classification -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Unit Hierarchy</div>
                <div class="erp-kpi-val" style="font-size: 1.15rem;">
                    {{ $unit->is_base_unit ? 'Primary Base Unit' : 'Derived Sub-Unit' }}
                </div>
            </div>
        </div>

        <!-- Conversion Relation -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-link"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Active Relation Formula</div>
                <div class="erp-kpi-val font-monospace erp-kpi-val-success" style="font-size: 1.05rem;">
                    {{ $unit->relation_formula }}
                </div>
            </div>
        </div>

        <!-- Decimal Places -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-hashtag"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Decimal Precision</div>
                <div class="erp-kpi-val font-monospace">
                    {{ $unit->decimal_places }} <span style="font-size: 0.8rem; color: #64748B;">Decimals</span>
                </div>
            </div>
        </div>

        <!-- Sub-Units Count -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-network-wired"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Linked Sub-Units</div>
                <div class="erp-kpi-val" style="color: #2563EB;">
                    {{ $unit->subUnits->count() }} <span style="font-size: 0.8rem; color: #64748B;">Units</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2-Column Profile Details Breakdown Grid -->
    <div class="erp-profile-details-grid">
        <!-- Main Column (Left) -->
        <div class="erp-form-main-col">

            <!-- 1. Unit Taxonomy & GST Standard Codes -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-scale-balanced text-primary"></i> Unit Taxonomy &amp; Official Identifiers
                </div>
                <div class="erp-profile-detail-body">
                    <div>
                        <div class="erp-profile-detail-label">Full Unit Name</div>
                        <div class="erp-profile-detail-val">
                            <span class="fw-bold text-dark">{{ $unit->name }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Symbol / Short Code</div>
                        <div class="erp-profile-detail-val">
                            <span class="font-monospace fw-bold text-dark">{{ $unit->code }}</span>
                            <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $unit->code }}', 'Unit code copied!')" title="Copy Code">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">GST UQC Code</div>
                        <div class="erp-profile-detail-val">
                            @if($unit->uqc_code)
                                <span class="font-monospace fw-bold text-dark">{{ $unit->uqc_code }}</span>
                                <button type="button" class="erp-copy-btn" onclick="copyToClipboard('{{ $unit->uqc_code }}', 'GST UQC code copied!')" title="Copy UQC">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            @else
                                <span class="text-muted fw-normal">Not Specified</span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Permitted Precision</div>
                        <div class="erp-profile-detail-val">
                            <span class="badge font-monospace" style="background: #F1F5F9; color: #334155; font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #CBD5E1;">
                                {{ $unit->decimal_places }} Decimal Places
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Classification Type</div>
                        <div class="erp-profile-detail-val">
                            @if($unit->is_base_unit)
                                <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                    <i class="fa-solid fa-cube me-1"></i> Primary Base Unit
                                </span>
                            @else
                                <span class="badge" style="background: rgba(59, 130, 246, 0.12); color: #2563EB; font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                    <i class="fa-solid fa-arrows-split-up-and-left me-1"></i> Derived Sub-Unit
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="erp-profile-detail-label">Status</div>
                        <div class="erp-profile-detail-val">
                            @if($unit->status === 'active')
                                <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-weight: 700; font-size: 0.8rem;">
                                    Active Standard
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

            <!-- 2. Conversion & Mathematical Relation Breakdown -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-calculator text-primary"></i> Conversion Formula &amp; Mathematical Relation
                </div>
                <div class="erp-profile-detail-body">
                    <div style="grid-column: 1 / -1;">
                        <div class="erp-profile-detail-label">Primary Relation Formula</div>
                        <div class="erp-profile-detail-val">
                            <span class="badge font-monospace" style="background: #ECFDF5; color: #047857; font-size: 1.05rem; font-weight: 800; padding: 6px 14px; border-radius: 8px; border: 1.5px solid #A7F3D0;">
                                <i class="fa-solid fa-link me-2"></i>{{ $unit->relation_formula }}
                            </span>
                        </div>
                    </div>

                    @if(!$unit->is_base_unit && $unit->baseUnit)
                        <div>
                            <div class="erp-profile-detail-label">Reciprocal Ratio</div>
                            <div class="erp-profile-detail-val">
                                <span class="font-monospace fw-bold text-dark">{{ $unit->equivalent_formula ?: '—' }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="erp-profile-detail-label">Base Root Reference</div>
                            <div class="erp-profile-detail-val">
                                <a href="{{ route('admin.masters.unit.show', $unit->baseUnit->id) }}" class="text-primary fw-bold" style="text-decoration: none;">
                                    <i class="fa-solid fa-cube me-1"></i>{{ $unit->baseUnit->name }} ({{ $unit->baseUnit->code }})
                                </a>
                            </div>
                        </div>

                        <div>
                            <div class="erp-profile-detail-label">Conversion Factor Number</div>
                            <div class="erp-profile-detail-val">
                                <span class="font-monospace fw-bold text-dark">{{ (float)$unit->conversion_factor }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="erp-profile-detail-label">Conversion Direction</div>
                            <div class="erp-profile-detail-val">
                                <span class="font-monospace fw-bold text-dark">
                                    {{ $unit->operator === '/' ? '[Factor] ' . $unit->code . ' = 1 ' . $unit->baseUnit->code : '1 ' . $unit->code . ' = [Factor] ' . $unit->baseUnit->code }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div style="grid-column: 1 / -1;">
                            <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.88rem; color: #64748B;">
                                This is a <strong>Primary Base Unit</strong>. Transactions recorded in this unit or its linked sub-units reconcile to this standard metric.
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Specifications & Operational Notes -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clipboard-list text-primary"></i> Specifications &amp; Operational Notes
                </div>
                <div class="p-3">
                    @if($unit->description)
                        <div class="p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0; font-size: 0.9rem; line-height: 1.6; color: #334155;">
                            {{ $unit->description }}
                        </div>
                    @else
                        <div class="text-muted p-2" style="font-size: 0.88rem; font-style: italic;">
                            No special handling notes or operational packaging instructions recorded for this measurement unit.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Right) -->
        <div class="erp-form-side-col">
            <!-- 1. Live Interactive Conversion Calculator -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-calculator text-primary"></i> Live Conversion Calculator
                </div>
                <div class="p-3">
                    <label style="font-size: 0.78rem; font-weight: 700; color: #475569; display: block; margin-bottom: 0.35rem;">
                        Convert from {{ $unit->code }}:
                    </label>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.85rem;">
                        <input type="number" id="calc-input" value="100" class="form-control font-monospace" style="font-size: 0.95rem; font-weight: 700;" oninput="recalculateConversion()">
                        <span class="font-monospace" style="font-weight: 700; font-size: 0.85rem; color: #1E293B;">{{ $unit->code }}</span>
                    </div>

                    <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 10px; padding: 0.85rem; text-align: center;">
                        <div style="font-size: 0.72rem; color: #64748B; text-transform: uppercase; font-weight: 700; letter-spacing: 0.04em;">
                            Converted Output
                        </div>
                        <div class="font-monospace" id="calc-output" style="font-size: 1.25rem; font-weight: 800; color: #047857; margin-top: 0.25rem;">
                            @if(!$unit->is_base_unit && $unit->baseUnit)
                                {{ number_format($unit->toBase(100), 2) }} {{ $unit->baseUnit->code }}
                            @else
                                100.00 {{ $unit->code }}
                            @endif
                        </div>
                        <div style="font-size: 0.72rem; color: #64748B; margin-top: 0.2rem;" id="calc-label">
                            @if(!$unit->is_base_unit && $unit->baseUnit)
                                In Base Unit: {{ $unit->baseUnit->name }}
                            @else
                                Base Standard Value
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Dependent Linked Sub-Units -->
            @if($unit->subUnits->count() > 0)
                <div class="card erp-profile-detail-card mb-4">
                    <div class="erp-profile-detail-header">
                        <i class="fa-solid fa-network-wired text-primary"></i> Linked Sub-Units ({{ $unit->subUnits->count() }})
                    </div>
                    <div class="p-3">
                        <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                            @foreach($unit->subUnits as $sub)
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.85rem; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px;">
                                    <div>
                                        <a href="{{ route('admin.masters.unit.show', $sub->id) }}" class="fw-bold text-dark text-decoration-none" style="font-size: 0.85rem;">
                                            {{ $sub->name }} ({{ $sub->code }})
                                        </a>
                                        <div class="font-monospace" style="font-size: 0.72rem; color: #047857; font-weight: 700; margin-top: 0.1rem;">
                                            {{ $sub->relation_formula }}
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.masters.unit.show', $sub->id) }}" class="erp-table-action-icon" title="View Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- 3. System Record Audit Trail -->
            <div class="card erp-profile-detail-card mb-4">
                <div class="erp-profile-detail-header">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i> Ledger Security &amp; Audit Trail
                </div>
                <div class="erp-profile-detail-body-single">
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">System Record ID</span>
                        <strong class="font-monospace text-dark">#{{ $unit->id }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Registration Date</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $unit->created_at ? $unit->created_at->format('d M Y, h:i A') : 'System Initial' }}</strong>
                    </div>
                    <div class="erp-profile-detail-row">
                        <span class="erp-profile-detail-label">Last Modified</span>
                        <strong class="text-dark" style="font-size: 0.82rem;">{{ $unit->updated_at ? $unit->updated_at->format('d M Y, h:i A') : 'Never' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Action Links -->
            <div class="card" style="padding: 1.15rem; background: #FAFBFD; border: 1px solid #E2E8F0; border-radius: 12px;">
                <h5 style="margin: 0 0 0.85rem 0; font-size: 0.88rem; font-weight: 700; color: #1E293B;">
                    <i class="fa-solid fa-bolt text-primary me-1"></i> Quick Master Actions
                </h5>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.masters.unit.edit', $unit->id) }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i> Edit Specifications &amp; Formula
                    </a>
                    <a href="{{ route('admin.masters.unit.create') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-plus me-2 text-primary"></i> Add Another Measurement Unit
                    </a>
                    <a href="{{ route('admin.masters.unit') }}" class="btn btn-outline" style="font-size: 0.82rem; text-align: left; justify-content: flex-start;">
                        <i class="fa-solid fa-list me-2 text-primary"></i> Return to Unit Directory
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function recalculateConversion() {
        const inputVal = parseFloat(document.getElementById('calc-input').value) || 0;
        const isBase = {{ $unit->is_base_unit ? 'true' : 'false' }};
        const hasBase = {{ $unit->baseUnit ? 'true' : 'false' }};
        const factor = {{ (float)$unit->conversion_factor }};
        const operator = '{{ $unit->operator }}';
        const baseCode = '{{ $unit->baseUnit ? $unit->baseUnit->code : $unit->code }}';

        if (!isBase && hasBase && factor > 0) {
            let res = 0;
            if (operator === '/') {
                res = inputVal / factor;
            } else {
                res = inputVal * factor;
            }
            document.getElementById('calc-output').textContent = res.toFixed(4).replace(/\.?0+$/, '') + ' ' + baseCode;
        } else {
            document.getElementById('calc-output').textContent = inputVal.toFixed(2) + ' {{ $unit->code }}';
        }
    }

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
