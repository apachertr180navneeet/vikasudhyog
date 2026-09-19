@extends('admin.layouts.app')

@section('title', 'Company Master - VIKAS UDHYOG ERP')
@section('page_code', 'master-company')

@section('content')
<section class="view-section active" id="view-master-company">
    <!-- Breadcrumb & Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span style="color: var(--primary); font-weight: 600;">Company Master</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.6rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-building" style="color: var(--primary);"></i> Company Master
            </h1>
            <p class="page-subtitle" style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Manage multi-company corporate profiles, GSTIN tax registrations, bank accounts &amp; default plant settings.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <button type="button" class="btn btn-outline" onclick="window.print()" title="Print Company List">
                <i class="fa-solid fa-print"></i> Print List
            </button>
            <a href="{{ route('admin.masters.company.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add New Company
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--primary);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(91, 132, 30, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-building-flag"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Total Companies</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--text-primary); line-height: 1.2;">{{ $stats['total'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--status-success);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.12); color: var(--status-success); display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Active Profiles</div>
                <div style="font-size: 1.6rem; font-weight: 800; color: var(--status-success); line-height: 1.2;">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #D4A017;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(212, 160, 23, 0.12); color: #B45309; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Primary Active Unit</div>
                <div style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px;" title="{{ $stats['default']->name ?? 'None' }}">
                    {{ $stats['default']->name ?? 'Not Configured' }}
                </div>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #3B82F6;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.12); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Base Hub</div>
                <div style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary);">Sojat City, RJ</div>
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
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #166534; cursor: pointer; font-size: 1rem;">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; padding: 0.9rem 1.2rem; border-radius: 10px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem; color: #EF4444;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #991B1B; cursor: pointer; font-size: 1rem;">&times;</button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px;">
        <!-- Filter and Search Header -->
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <form action="{{ route('admin.masters.company') }}" method="GET" style="display: flex; align-items: center; gap: 0.75rem; flex: 1; max-width: 600px; flex-wrap: wrap;">
                <div style="position: relative; flex: 1; min-width: 220px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search company name, GSTIN, PAN, city..." class="form-control" style="padding-left: 2.25rem; height: 40px; border-radius: 8px;">
                </div>

                <select name="status" class="form-control" style="width: 140px; height: 40px; border-radius: 8px;" onchange="this.form.submit()">
                    <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <button type="submit" class="btn btn-outline" style="height: 40px; padding: 0 1rem; border-radius: 8px;">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                @if(!empty($filters['search']) || ($filters['status'] ?? 'all') !== 'all')
                    <a href="{{ route('admin.masters.company') }}" class="btn btn-outline" style="height: 40px; padding: 0 0.85rem; border-radius: 8px; color: var(--status-danger);" title="Clear Filters">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>

            <div style="font-size: 0.82rem; color: var(--text-muted);">
                Showing <strong>{{ $companies->count() }}</strong> of <strong>{{ $companies->total() }}</strong> companies
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="custom-table" style="margin-bottom: 0;">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Company Details</th>
                        <th>GSTIN &amp; PAN</th>
                        <th>Contact &amp; Web</th>
                        <th>Location</th>
                        <th>Bank Settlement</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $index => $company)
                        <tr id="company-row-{{ $company->id }}" style="{{ $company->is_default ? 'background: rgba(91, 132, 30, 0.03);' : '' }}">
                            <td style="font-weight: 600; color: var(--text-muted);">
                                {{ $companies->firstItem() + $index }}
                            </td>
                            <td>
                                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(91, 132, 30, 0.25);">
                                        {{ strtoupper(substr($company->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                            <a href="{{ route('admin.masters.company.show', $company->id) }}" style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); text-decoration: none;">
                                                {{ $company->name }}
                                            </a>
                                            @if($company->is_default)
                                                <span class="badge" style="background: rgba(212, 160, 23, 0.15); color: #B45309; border: 1px solid rgba(212, 160, 23, 0.3); font-size: 0.7rem; font-weight: 700; padding: 2px 6px; border-radius: 6px;">
                                                    <i class="fa-solid fa-star" style="font-size: 0.65rem;"></i> PRIMARY
                                                </span>
                                            @endif
                                        </div>
                                        @if($company->code)
                                            <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                                Code: <span style="color: var(--primary);">{{ $company->code }}</span>
                                            </div>
                                        @endif
                                        @if($company->tagline)
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $company->tagline }}">
                                                {{ $company->tagline }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem;">
                                    @if($company->gstin)
                                        <div><span style="color: var(--text-muted); font-size: 0.75rem;">GSTIN:</span> <strong>{{ $company->gstin }}</strong></div>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.78rem;">GSTIN: N/A</span>
                                    @endif

                                    @if($company->pan)
                                        <div style="margin-top: 2px;"><span style="color: var(--text-muted); font-size: 0.75rem;">PAN:</span> <code style="font-size: 0.78rem; background: #F3F4F6; padding: 1px 4px; border-radius: 4px;">{{ $company->pan }}</code></div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem;">
                                    @if($company->phone)
                                        <div><i class="fa-solid fa-phone" style="font-size: 0.7rem; color: var(--primary); width: 14px;"></i> <a href="tel:{{ $company->phone }}" style="color: inherit; text-decoration: none;">{{ $company->phone }}</a></div>
                                    @endif
                                    @if($company->email)
                                        <div style="margin-top: 2px;"><i class="fa-solid fa-envelope" style="font-size: 0.7rem; color: var(--primary); width: 14px;"></i> <a href="mailto:{{ $company->email }}" style="color: inherit; text-decoration: none;">{{ $company->email }}</a></div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.82rem;">
                                    <strong>{{ $company->city }}</strong>, {{ $company->state }}
                                    @if($company->pincode)
                                        <span style="color: var(--text-muted); font-size: 0.75rem;">- {{ $company->pincode }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.8rem;">
                                    @if($company->bank_name)
                                        <div style="font-weight: 600; color: var(--text-primary);">{{ $company->bank_name }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            A/c: {{ $company->bank_account_no ?? 'N/A' }}
                                        </div>
                                        @if($company->bank_ifsc)
                                            <div style="font-size: 0.72rem; color: var(--text-muted);">IFSC: {{ $company->bank_ifsc }}</div>
                                        @endif
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.78rem;">Not configured</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <button type="button" class="badge {{ $company->status === 'active' ? 'badge-success' : 'badge-danger' }} btn-toggle-status" data-id="{{ $company->id }}" data-name="{{ $company->name }}" data-url="{{ route('admin.masters.company.toggle-status', $company->id) }}" data-status="{{ $company->status }}" style="border: none; cursor: pointer; padding: 4px 10px; font-size: 0.75rem; border-radius: 20px; transition: all 0.2s ease;" title="Click to Toggle Status">
                                    <i class="fa-solid {{ $company->status === 'active' ? 'fa-check' : 'fa-ban' }}" style="font-size: 0.65rem; margin-right: 3px;"></i>
                                    {{ ucfirst($company->status) }}
                                </button>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end; gap: 0.35rem;">
                                    <!-- View Page Link -->
                                    <a href="{{ route('admin.masters.company.show', $company->id) }}" class="btn-action" style="color: var(--primary);" title="View Company Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <!-- Edit Page Link -->
                                    <a href="{{ route('admin.masters.company.edit', $company->id) }}" class="btn-action edit" title="Edit Company">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    <!-- Make Primary/Default Button -->
                                    @if(!$company->is_default)
                                        <button type="button" class="btn-action btn-set-default" data-id="{{ $company->id }}" data-name="{{ $company->name }}" data-url="{{ route('admin.masters.company.set-default', $company->id) }}" style="color: #D4A017; border: none; background: none; cursor: pointer;" title="Set as Primary Default Entity">
                                            <i class="fa-regular fa-star"></i>
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <button type="button" class="btn-action delete btn-delete-company" data-id="{{ $company->id }}" data-name="{{ $company->name }}" data-url="{{ route('admin.masters.company.destroy', $company->id) }}" style="border: none; background: none; cursor: pointer;" title="Delete Company">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3.5rem 1.5rem; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; color: #D1D5DB; margin-bottom: 0.75rem;">
                                    <i class="fa-solid fa-building-circle-xmark"></i>
                                </div>
                                <h3 style="font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.35rem;">No Companies Found</h3>
                                <p style="font-size: 0.85rem; max-width: 400px; margin: 0 auto 1.25rem;">
                                    No company records exist yet. Click below to add your first company profile.
                                </p>
                                <a href="{{ route('admin.masters.company.create') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus"></i> Add New Company
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($companies->hasPages())
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.82rem; color: var(--text-muted);">
                    Showing {{ $companies->firstItem() }} to {{ $companies->lastItem() }} of {{ $companies->total() }} entries
                </div>
                <div>
                    {{ $companies->links() }}
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // 1. AJAX Status Toggle with SweetAlert Confirmation
        $(document).on('click', '.btn-toggle-status', function(e) {
            e.preventDefault();
            const btn = $(this);
            const name = btn.data('name');
            const url = btn.data('url');
            const currentStatus = btn.data('status');
            const nextStatus = currentStatus === 'active' ? 'Inactive' : 'Active';

            Swal.fire({
                title: 'Change Company Status?',
                text: `Are you sure you want to mark "${name}" as ${nextStatus}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: nextStatus === 'Active' ? '#10B981' : '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: `Yes, make ${nextStatus}`,
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true).css('opacity', '0.6');

                    $.ajax({
                        url: url,
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        success: function(res) {
                            btn.prop('disabled', false).css('opacity', '1');
                            if (res.success) {
                                toastr.success(res.message || 'Status updated successfully!');
                                const newStatus = res.status;
                                btn.data('status', newStatus);
                                if (newStatus === 'active') {
                                    btn.removeClass('badge-danger').addClass('badge-success');
                                    btn.html('<i class="fa-solid fa-check" style="font-size: 0.65rem; margin-right: 3px;"></i> Active');
                                } else {
                                    btn.removeClass('badge-success').addClass('badge-danger');
                                    btn.html('<i class="fa-solid fa-ban" style="font-size: 0.65rem; margin-right: 3px;"></i> Inactive');
                                }
                            } else {
                                toastr.error(res.message || 'Failed to update status.');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).css('opacity', '1');
                            const errorMsg = xhr.responseJSON?.message || 'Error occurred while updating status.';
                            toastr.error(errorMsg);
                        }
                    });
                }
            });
        });

        // 2. AJAX Delete with SweetAlert Confirmation
        $(document).on('click', '.btn-delete-company', function(e) {
            e.preventDefault();
            const btn = $(this);
            const name = btn.data('name');
            const url = btn.data('url');
            const id = btn.data('id');

            Swal.fire({
                title: 'Delete Company Profile?',
                html: `Are you sure you want to permanently delete <strong>"${name}"</strong>?<br><span style="font-size:0.85rem; color:#EF4444; margin-top: 4px; display: inline-block;">This action cannot be undone.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message || 'Company profile deleted successfully!');
                                $(`#company-row-${id}`).fadeOut(350, function() {
                                    $(this).remove();
                                    if ($('tbody tr').length === 0) {
                                        location.reload();
                                    }
                                });
                            } else {
                                btn.prop('disabled', false);
                                toastr.error(res.message || 'Failed to delete company.');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false);
                            const errorMsg = xhr.responseJSON?.message || 'Error occurred while deleting company.';
                            toastr.error(errorMsg);
                        }
                    });
                }
            });
        });

        // 3. AJAX Set Default Primary with SweetAlert Confirmation
        $(document).on('click', '.btn-set-default', function(e) {
            e.preventDefault();
            const btn = $(this);
            const name = btn.data('name');
            const url = btn.data('url');

            Swal.fire({
                title: 'Set as Primary Entity?',
                text: `Do you want to set "${name}" as the primary default company profile?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#D4A017',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, Set Primary',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.prop('disabled', true);

                    $.ajax({
                        url: url,
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message || 'Default primary entity updated!');
                                setTimeout(() => location.reload(), 600);
                            } else {
                                btn.prop('disabled', false);
                                toastr.error(res.message || 'Failed to update default company.');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false);
                            const errorMsg = xhr.responseJSON?.message || 'Error updating default company.';
                            toastr.error(errorMsg);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
