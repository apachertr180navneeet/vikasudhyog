@extends('admin.layouts.app')

@section('title', 'Edit Transporter - ' . $transporter->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-transporter')

@section('content')
<section class="view-section active" id="view-transporter-edit">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.transporter') }}">Transporters</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Edit Transporter</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-pen-to-square text-primary"></i> Edit Transporter: {{ $transporter->name }}
            </h1>
            <p class="erp-page-subtitle">
                Update logistics partner contact details, city hubs, and registration.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.transporter.show', $transporter) }}" class="btn btn-outline">
                <i class="fa-solid fa-eye"></i> View Profile
            </a>
            <a href="{{ route('admin.masters.transporter') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('admin.masters.transporter.update', $transporter) }}" method="POST" id="transporterEditForm">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Left Column: Form Sections -->
            <div class="col-lg-8">
                <!-- Section 1: Transporter Details -->
                <div class="card erp-form-section-card mb-4">
                    <div class="erp-section-header">
                        <div class="erp-section-icon"><i class="fa-solid fa-id-card"></i></div>
                        <div>
                            <h3 class="erp-section-title">Carrier Profile & Identification</h3>
                            <p class="erp-section-desc">Basic information and government transporter registration.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label erp-form-label required">Transporter Name</label>
                                <div class="erp-input-icon-wrap">
                                    <i class="fa-solid fa-truck erp-input-icon"></i>
                                    <input type="text"
                                           class="form-control erp-input @error('name') is-invalid @enderror"
                                           id="name"
                                           name="name"
                                           value="{{ old('name', $transporter->name) }}"
                                           placeholder="Enter Transporter Name"
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="transporter_id_gstin" class="form-label erp-form-label">Transporter ID / GSTIN</label>
                                <div class="erp-input-icon-wrap">
                                    <i class="fa-solid fa-barcode erp-input-icon"></i>
                                    <input type="text"
                                           class="form-control erp-input text-uppercase @error('transporter_id_gstin') is-invalid @enderror"
                                           id="transporter_id_gstin"
                                           name="transporter_id_gstin"
                                           value="{{ old('transporter_id_gstin', $transporter->transporter_id_gstin) }}"
                                           placeholder="Enter Transporter ID / GSTIN"
                                           maxlength="20">
                                </div>
                                <div class="form-text small text-muted">15-digit GSTIN or government Transporter ID (TRANSIN)</div>
                                @error('transporter_id_gstin')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label erp-form-label">Contact Phone / Mobile</label>
                                <div class="erp-input-icon-wrap">
                                    <i class="fa-solid fa-phone erp-input-icon"></i>
                                    <input type="text"
                                           class="form-control erp-input @error('phone') is-invalid @enderror"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone', $transporter->phone) }}"
                                           placeholder="Enter Contact Phone">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label erp-form-label">Operating City / Hub</label>
                                <div class="erp-input-icon-wrap">
                                    <i class="fa-solid fa-location-dot erp-input-icon"></i>
                                    <input type="text"
                                           class="form-control erp-input @error('city') is-invalid @enderror"
                                           id="city"
                                           name="city"
                                           value="{{ old('city', $transporter->city) }}"
                                           placeholder="Enter Operating City">
                                </div>
                                @error('city')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="notes" class="form-label erp-form-label">Notes & Booking Branches</label>
                                <textarea class="form-control erp-input @error('notes') is-invalid @enderror"
                                          id="notes"
                                          name="notes"
                                          rows="3"
                                          placeholder="Enter Notes & Booking Branches">{{ old('notes', $transporter->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone Card -->
                <div class="card erp-form-section-card border-danger-subtle">
                    <div class="erp-section-header bg-danger-subtle text-danger">
                        <div class="erp-section-icon bg-danger text-white"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div>
                            <h3 class="erp-section-title text-danger">Danger Zone</h3>
                            <p class="erp-section-desc text-danger">Archive or permanently remove this transporter record.</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1">Archive Transporter</h6>
                                <p class="text-muted small mb-0">Soft-deletes the carrier. Existing transaction history remains preserved.</p>
                            </div>
                            <button type="button"
                                    class="btn btn-outline-danger"
                                    onclick="if(confirm('Are you sure you want to archive this transporter?')) document.getElementById('archiveForm').submit();">
                                <i class="fa-solid fa-box-archive me-1"></i> Archive Carrier
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Preview & Action Card -->
            <div class="col-lg-4">
                <!-- Action Card -->
                <div class="card erp-sidebar-actions-card mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Update Changes</h5>
                        <p class="text-muted small mb-4">Save modifications to carrier contact information and routing hubs.</p>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary erp-btn-submit">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Update Transporter
                            </button>
                            <a href="{{ route('admin.masters.transporter') }}" class="btn btn-outline text-muted">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Audit Trail Card -->
                <div class="card erp-form-section-card">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Audit Trail</h6>
                        <div class="small">
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Status:</span>
                                <span class="badge {{ $transporter->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $transporter->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Created:</span>
                                <span class="font-monospace">{{ $transporter->created_at?->format('d M Y, h:i A') ?? '—' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted">Last Updated:</span>
                                <span class="font-monospace">{{ $transporter->updated_at?->format('d M Y, h:i A') ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form action="{{ route('admin.masters.transporter.destroy', $transporter) }}" method="POST" id="archiveForm" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</section>
@endsection
