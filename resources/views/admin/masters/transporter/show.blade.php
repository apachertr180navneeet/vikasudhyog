@extends('admin.layouts.app')

@section('title', 'Transporter Profile - ' . $transporter->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-transporter')

@section('content')
<section class="view-section active" id="view-transporter-show">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.transporter') }}">Transporters</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">{{ $transporter->name }}</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-fast text-primary"></i> Transporter 360° Profile
            </h1>
            <p class="erp-page-subtitle">
                Comprehensive overview of logistics provider, registration identifiers, and contact details.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.transporter.edit', $transporter) }}" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Transporter
            </a>
            <a href="{{ route('admin.masters.transporter') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Hero Header Profile Banner -->
    <div class="card erp-main-card mb-4 p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="erp-hero-avatar">
                    {{ strtoupper(substr($transporter->name, 0, 2)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h2 class="h4 fw-bold mb-0 text-dark">{{ $transporter->name }}</h2>
                        <span class="badge {{ $transporter->is_active ? 'bg-success' : 'bg-secondary' }} px-3 py-1">
                            {{ $transporter->is_active ? 'Active Carrier' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="d-flex flex-wrap gap-3 text-muted small">
                        @if($transporter->transporter_id_gstin)
                            <span><i class="fa-solid fa-barcode me-1 text-primary"></i> TRANSIN/GSTIN: <strong class="font-monospace text-dark">{{ $transporter->transporter_id_gstin }}</strong></span>
                        @endif
                        @if($transporter->city)
                            <span><i class="fa-solid fa-location-dot me-1 text-danger"></i> {{ $transporter->city }}</span>
                        @endif
                        @if($transporter->phone)
                            <span><i class="fa-solid fa-phone me-1 text-success"></i> <a href="tel:{{ $transporter->phone }}" class="text-decoration-none text-dark font-monospace">{{ $transporter->phone }}</a></span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <form action="{{ route('admin.masters.transporter.toggle-status', $transporter) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm {{ $transporter->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                        <i class="fa-solid {{ $transporter->is_active ? 'fa-ban' : 'fa-check' }} me-1"></i>
                        {{ $transporter->is_active ? 'Deactivate Carrier' : 'Activate Carrier' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 2-Column Details Breakdown -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card erp-form-section-card mb-4">
                <div class="erp-section-header">
                    <div class="erp-section-icon"><i class="fa-solid fa-circle-info"></i></div>
                    <div>
                        <h3 class="erp-section-title">Registration & Contact Information</h3>
                        <p class="erp-section-desc">Logistics identification and contact coordinates.</p>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless erp-details-table mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 220px;" class="text-muted">Transporter Name:</th>
                                <td class="fw-bold">{{ $transporter->name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Transporter ID / GSTIN:</th>
                                <td>
                                    @if($transporter->transporter_id_gstin)
                                        <span class="erp-code-pill font-monospace fw-bold">{{ $transporter->transporter_id_gstin }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Contact Phone:</th>
                                <td>
                                    @if($transporter->phone)
                                        <a href="tel:{{ $transporter->phone }}" class="text-decoration-none font-monospace fw-bold text-dark">
                                            <i class="fa-solid fa-phone text-muted me-1"></i>{{ $transporter->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Operating Hub / City:</th>
                                <td class="fw-semibold">{{ $transporter->city ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Associated Plant/Company:</th>
                                <td>{{ $transporter->company?->company_name ?? 'Default Plant' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Notes & Routing:</th>
                                <td>{{ $transporter->notes ?? '—' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <!-- System Timestamps Card -->
            <div class="card erp-form-section-card">
                <div class="erp-section-header">
                    <div class="erp-section-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div>
                        <h3 class="erp-section-title">Audit Trail</h3>
                        <p class="erp-section-desc">Record creation and history.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Record ID:</span>
                        <span class="font-monospace fw-bold">#{{ $transporter->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Created Date:</span>
                        <span class="font-monospace">{{ $transporter->created_at?->format('d M Y, h:i A') ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Last Modified:</span>
                        <span class="font-monospace">{{ $transporter->updated_at?->format('d M Y, h:i A') ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
