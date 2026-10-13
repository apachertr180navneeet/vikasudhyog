@extends('admin.layouts.app')

@section('title', 'Add Transporter - Logistics Directory - VIKAS UDHYOG ERP')
@section('page_code', 'master-transporter')

@section('content')
<section class="view-section active" id="view-transporter-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.transporter') }}">Transporters</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">New Transporter</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-truck-fast text-primary"></i> Add New Transporter
            </h1>
            <p class="erp-page-subtitle">
                Register a new freight carrier, transport agency, or logistics provider.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.transporter') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('admin.masters.transporter.store') }}" method="POST" id="transporterForm">
        @csrf
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
                                           value="{{ old('name') }}"
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
                                           value="{{ old('transporter_id_gstin') }}"
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
                                           value="{{ old('phone') }}"
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
                                           value="{{ old('city') }}"
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
                                          placeholder="Enter Notes & Booking Branches">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Preview & Action Card -->
            <div class="col-lg-4">
                <!-- Live Preview Card -->
                <div class="card erp-preview-card mb-4">
                    <div class="erp-preview-header">
                        <i class="fa-solid fa-eye text-primary me-2"></i> Real-time Preview
                    </div>
                    <div class="card-body text-center p-4">
                        <div class="erp-preview-avatar mx-auto mb-3" id="previewAvatar">
                            TR
                        </div>
                        <h4 class="fw-bold mb-1" id="previewName">Transporter Name</h4>
                        <div class="text-muted small mb-3" id="previewGstin">ID: —</div>

                        <div class="erp-preview-details text-start">
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">City / Hub:</span>
                                <span class="fw-semibold" id="previewCity">—</span>
                            </div>
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Contact Phone:</span>
                                <span class="fw-semibold font-monospace" id="previewPhone">—</span>
                            </div>
                            <div class="d-flex justify-content-between py-2">
                                <span class="text-muted">Initial Status:</span>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Card -->
                <div class="card erp-sidebar-actions-card">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Save Transporter</h5>
                        <p class="text-muted small mb-4">The transporter will immediately become available in Sales and Purchase vouchers.</p>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary erp-btn-submit">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Transporter
                            </button>
                            <a href="{{ route('admin.masters.transporter') }}" class="btn btn-outline text-muted">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('name');
    const gstinInput = document.getElementById('transporter_id_gstin');
    const phoneInput = document.getElementById('phone');
    const cityInput = document.getElementById('city');

    const previewName = document.getElementById('previewName');
    const previewAvatar = document.getElementById('previewAvatar');
    const previewGstin = document.getElementById('previewGstin');
    const previewPhone = document.getElementById('previewPhone');
    const previewCity = document.getElementById('previewCity');

    function updatePreview() {
        const nameVal = nameInput.value.trim() || 'Transporter Name';
        previewName.textContent = nameVal;

        const words = nameVal.split(' ').filter(w => w.length > 0);
        let initials = 'TR';
        if (words.length >= 2) {
            initials = (words[0][0] + words[1][0]).toUpperCase();
        } else if (words.length === 1 && words[0].length >= 2) {
            initials = words[0].substring(0, 2).toUpperCase();
        }
        previewAvatar.textContent = initials;

        previewGstin.textContent = gstinInput.value.trim() ? 'ID: ' + gstinInput.value.trim().toUpperCase() : 'ID: —';
        previewPhone.textContent = phoneInput.value.trim() || '—';
        previewCity.textContent = cityInput.value.trim() || '—';
    }

    [nameInput, gstinInput, phoneInput, cityInput].forEach(inp => {
        if (inp) inp.addEventListener('input', updatePreview);
    });

    updatePreview();
});
</script>
@endpush
@endsection
