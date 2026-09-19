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
            <button type="button" class="btn btn-primary" onclick="openCreateCompanyModal()">
                <i class="fa-solid fa-plus"></i> Add New Company
            </button>
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
                        <tr style="{{ $company->is_default ? 'background: rgba(91, 132, 30, 0.03);' : '' }}">
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
                                            <strong style="font-size: 0.95rem; color: var(--text-primary);">{{ $company->name }}</strong>
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
                                <form action="{{ route('admin.masters.company.toggle-status', $company->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="badge {{ $company->status === 'active' ? 'badge-success' : 'badge-danger' }}" style="border: none; cursor: pointer; padding: 4px 10px; font-size: 0.75rem; border-radius: 20px; transition: all 0.2s ease;" title="Click to Toggle Status">
                                        <i class="fa-solid {{ $company->status === 'active' ? 'fa-check' : 'fa-ban' }}" style="font-size: 0.65rem; margin-right: 3px;"></i>
                                        {{ ucfirst($company->status) }}
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end; gap: 0.35rem;">
                                    <!-- View Modal Button -->
                                    <button type="button" class="btn-action" style="color: var(--primary);" title="View Company Profile" onclick="viewCompanyDetails({{ $company->id }})">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" class="btn-action edit" title="Edit Company" onclick="editCompanyProfile({{ $company->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <!-- Make Primary/Default Button -->
                                    @if(!$company->is_default)
                                        <form action="{{ route('admin.masters.company.set-default', $company->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-action" style="color: #D4A017;" title="Set as Primary Default Entity">
                                                <i class="fa-regular fa-star"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete Button -->
                                    <button type="button" class="btn-action delete" title="Delete Company" onclick="confirmDeleteCompany({{ $company->id }}, '{{ addslashes($company->name) }}')">
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
                                    No company records matched your search filters. Try adjusting your query or register a new business entity.
                                </p>
                                <button type="button" class="btn btn-primary" onclick="openCreateCompanyModal()">
                                    <i class="fa-solid fa-plus"></i> Add New Company
                                </button>
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

<!-- =========================================================================
     MODAL 1: Create & Edit Company Modal
     ========================================================================= -->
<div class="modal-overlay" id="modal-company-form" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; padding: 1.5rem; backdrop-filter: blur(4px);">
    <div class="modal-card" style="width: 100%; max-width: 820px; max-height: 90vh; overflow-y: auto; background: #FFFFFF; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div class="modal-header" style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: var(--bg-hover); border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(91, 132, 30, 0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                    <i class="fa-solid fa-building" id="modal-company-icon"></i>
                </div>
                <span class="modal-title" id="modal-company-title" style="font-weight: 800; font-size: 1.15rem; color: var(--text-primary);">Add New Company</span>
            </div>
            <button type="button" class="modal-close" onclick="closeCompanyFormModal()" style="background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <div class="modal-body" style="padding: 1.75rem;">
            <form id="company-master-form" method="POST" action="{{ route('admin.masters.company.store') }}">
                @csrf
                <input type="hidden" name="_method" id="company-form-method" value="POST">
                <input type="hidden" id="company-edit-id" value="">

                <!-- Section 1: Business Profile -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-id-card"></i> 1. General Business Profile
                    </div>
                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Company Legal Name <span style="color: red;">*</span></label>
                            <input type="text" name="name" id="comp-name" class="form-control" placeholder="e.g. Vikas Udhyog" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Entity Short Code</label>
                            <input type="text" name="code" id="comp-code" class="form-control" placeholder="e.g. VU-SOJAT">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Status <span style="color: red;">*</span></label>
                            <select name="status" id="comp-status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Tagline / Nature of Business</label>
                            <input type="text" name="tagline" id="comp-tagline" class="form-control" placeholder="e.g. Premium Sojat Henna Powder & Herbal Formulations Manufacturer">
                        </div>
                    </div>
                </div>

                <!-- Section 2: GSTIN, PAN & Financial Year -->
                <div style="margin-bottom: 1.5rem; padding-top: 1.25rem; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-file-invoice"></i> 2. Tax Registrations &amp; Fiscal Year
                    </div>
                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">GSTIN Number</label>
                            <input type="text" name="gstin" id="comp-gstin" class="form-control" placeholder="08AABCV1234F1Z5" maxlength="20" style="text-transform: uppercase;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Permanent Account Number (PAN)</label>
                            <input type="text" name="pan" id="comp-pan" class="form-control" placeholder="AABCV1234F" maxlength="15" style="text-transform: uppercase;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Active Financial Year</label>
                            <input type="text" name="financial_year" id="comp-financial-year" class="form-control" value="2026-2027" placeholder="2026-2027">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Contact & Factory Address -->
                <div style="margin-bottom: 1.5rem; padding-top: 1.25rem; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-map-location-dot"></i> 3. Address &amp; Communication Details
                    </div>
                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Primary Phone</label>
                            <input type="text" name="phone" id="comp-phone" class="form-control" placeholder="+91 94140 12345">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Email Address</label>
                            <input type="email" name="email" id="comp-email" class="form-control" placeholder="info@vikasudhyog.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Website</label>
                            <input type="text" name="website" id="comp-website" class="form-control" placeholder="https://vikasudhyog.com">
                        </div>
                        <div class="form-group" style="grid-column: span 3;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Factory / Registered Address</label>
                            <textarea name="address" id="comp-address" rows="2" class="form-control" placeholder="Plot No. 12-15, Industrial Area, Near Krishi Mandi"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">City <span style="color: red;">*</span></label>
                            <input type="text" name="city" id="comp-city" class="form-control" value="Sojat City" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">State <span style="color: red;">*</span></label>
                            <input type="text" name="state" id="comp-state" class="form-control" value="Rajasthan" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Pincode</label>
                            <input type="text" name="pincode" id="comp-pincode" class="form-control" placeholder="306104">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Bank Settlement Credentials -->
                <div style="margin-bottom: 1.5rem; padding-top: 1.25rem; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-building-columns"></i> 4. Banking &amp; Settlement Details
                    </div>
                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Bank Name</label>
                            <input type="text" name="bank_name" id="comp-bank-name" class="form-control" placeholder="State Bank of India">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Account Number</label>
                            <input type="text" name="bank_account_no" id="comp-bank-account" class="form-control" placeholder="38491029384">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">IFSC Code</label>
                            <input type="text" name="bank_ifsc" id="comp-bank-ifsc" class="form-control" placeholder="SBIN0031204" style="text-transform: uppercase;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.35rem; display: block;">Branch</label>
                            <input type="text" name="bank_branch" id="comp-bank-branch" class="form-control" placeholder="Main Branch Sojat City">
                        </div>
                    </div>
                </div>

                <!-- Section 5: Primary Checkbox -->
                <div style="padding: 1rem; background: var(--bg-hover); border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <input type="checkbox" name="is_default" id="comp-is-default" value="1" style="width: 18px; height: 18px; accent-color: var(--primary); cursor: pointer;">
                    <label for="comp-is-default" style="font-size: 0.88rem; font-weight: 600; color: var(--text-primary); cursor: pointer;">
                        Set as Default Primary Operating Entity (Auto-selected in invoices and vouchers)
                    </label>
                </div>

                <div class="modal-footer" style="padding-top: 1rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="closeCompanyFormModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-company">
                        <i class="fa-solid fa-floppy-disk"></i> Save Company Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL 2: View Company Details Modal
     ========================================================================= -->
<div class="modal-overlay" id="modal-company-view" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; padding: 1.5rem; backdrop-filter: blur(4px);">
    <div class="modal-card" style="width: 100%; max-width: 680px; max-height: 90vh; overflow-y: auto; background: #FFFFFF; border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div class="modal-header" style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #1B4D25 0%, #0F2813 100%); color: #FFFFFF; border-top-left-radius: 20px; border-top-right-radius: 20px;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #BEF264;">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <h3 id="view-comp-name" style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #FFFFFF;">Company Details</h3>
                    <div id="view-comp-code" style="font-size: 0.78rem; color: #A3E635;"></div>
                </div>
            </div>
            <button type="button" onclick="closeCompanyViewModal()" style="background: none; border: none; font-size: 1.5rem; color: #FFFFFF; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <div class="modal-body" style="padding: 1.75rem;" id="view-comp-body">
            <!-- Dynamic view content populated by JS -->
        </div>

        <div class="modal-footer" style="padding: 1rem 1.75rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; background: var(--bg-hover); border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
            <button type="button" class="btn btn-outline" onclick="closeCompanyViewModal()">Close</button>
            <button type="button" class="btn btn-primary" id="btn-view-to-edit">
                <i class="fa-solid fa-pen"></i> Edit Profile
            </button>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL 3: Delete Confirmation Dialog
     ========================================================================= -->
<form id="delete-company-form" method="POST" action="" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    // Open Create Modal
    window.openCreateCompanyModal = function() {
        const form = document.getElementById('company-master-form');
        if (form) form.reset();
        
        form.action = "{{ route('admin.masters.company.store') }}";
        document.getElementById('company-form-method').value = 'POST';
        document.getElementById('company-edit-id').value = '';
        document.getElementById('modal-company-title').innerText = 'Add New Company';
        
        const icon = document.getElementById('modal-company-icon');
        if (icon) icon.className = 'fa-solid fa-building-circle-check';
        
        document.getElementById('comp-city').value = 'Sojat City';
        document.getElementById('comp-state').value = 'Rajasthan';
        document.getElementById('comp-financial-year').value = '2026-2027';
        document.getElementById('comp-status').value = 'active';
        
        const modal = document.getElementById('modal-company-form');
        if (modal) {
            modal.style.display = 'flex';
            modal.style.opacity = '1';
            modal.style.pointerEvents = 'auto';
            modal.classList.add('active');
        }
    };

    // Close Form Modal
    window.closeCompanyFormModal = function() {
        const modal = document.getElementById('modal-company-form');
        if (modal) {
            modal.style.display = 'none';
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            modal.classList.remove('active');
        }
    };

    // Close View Modal
    window.closeCompanyViewModal = function() {
        const modal = document.getElementById('modal-company-view');
        if (modal) {
            modal.style.display = 'none';
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            modal.classList.remove('active');
        }
    };

    // Edit Company Profile
    window.editCompanyProfile = function(id) {
        fetch(`/admin/masters/company/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.company) {
                alert('Unable to load company details.');
                return;
            }
            const c = data.company;
            const form = document.getElementById('company-master-form');
            form.action = `/admin/masters/company/${c.id}`;
            document.getElementById('company-form-method').value = 'PUT';
            document.getElementById('company-edit-id').value = c.id;
            document.getElementById('modal-company-title').innerText = 'Edit Company: ' + c.name;

            document.getElementById('comp-name').value = c.name || '';
            document.getElementById('comp-code').value = c.code || '';
            document.getElementById('comp-status').value = c.status || 'active';
            document.getElementById('comp-tagline').value = c.tagline || '';
            document.getElementById('comp-gstin').value = c.gstin || '';
            document.getElementById('comp-pan').value = c.pan || '';
            document.getElementById('comp-financial-year').value = c.financial_year || '2026-2027';
            document.getElementById('comp-phone').value = c.phone || '';
            document.getElementById('comp-email').value = c.email || '';
            document.getElementById('comp-website').value = c.website || '';
            document.getElementById('comp-address').value = c.address || '';
            document.getElementById('comp-city').value = c.city || 'Sojat City';
            document.getElementById('comp-state').value = c.state || 'Rajasthan';
            document.getElementById('comp-pincode').value = c.pincode || '';
            document.getElementById('comp-bank-name').value = c.bank_name || '';
            document.getElementById('comp-bank-account').value = c.bank_account_no || '';
            document.getElementById('comp-bank-ifsc').value = c.bank_ifsc || '';
            document.getElementById('comp-bank-branch').value = c.bank_branch || '';
            document.getElementById('comp-is-default').checked = Boolean(c.is_default);

            window.closeCompanyViewModal();
            const modal = document.getElementById('modal-company-form');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.opacity = '1';
                modal.style.pointerEvents = 'auto';
                modal.classList.add('active');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to connect to server.');
        });
    };

    // View Company Details
    window.viewCompanyDetails = function(id) {
        fetch(`/admin/masters/company/${id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.company) {
                alert('Company details not found.');
                return;
            }
            const c = data.company;
            document.getElementById('view-comp-name').innerText = c.name;
            document.getElementById('view-comp-code').innerText = c.code ? `Code: ${c.code}` : 'Vikas Udhyog Entity';

            let html = `
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="padding: 0.85rem; background: var(--bg-hover); border-radius: 10px;">
                        <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">GSTIN</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">${c.gstin || 'N/A'}</div>
                    </div>
                    <div style="padding: 0.85rem; background: var(--bg-hover); border-radius: 10px;">
                        <div style="font-size: 0.72rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">PAN</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary);">${c.pan || 'N/A'}</div>
                    </div>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 0.4rem;">
                        <i class="fa-solid fa-location-dot"></i> Registered Plant &amp; Address
                    </div>
                    <div style="font-size: 0.88rem; color: var(--text-primary); line-height: 1.5; background: #FAFAFA; padding: 0.85rem; border-radius: 10px; border: 1px solid var(--border-color);">
                        ${c.address || 'Address not registered'}<br>
                        <strong>${c.city || ''}</strong>, ${c.state || ''} ${c.pincode ? '- ' + c.pincode : ''}
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Phone Number</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: var(--text-primary);">${c.phone || 'N/A'}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Official Email</div>
                        <div style="font-size: 0.88rem; font-weight: 600; color: var(--text-primary);">${c.email || 'N/A'}</div>
                    </div>
                </div>

                <div style="padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <div style="font-size: 0.78rem; font-weight: 700; color: var(--primary); text-transform: uppercase; margin-bottom: 0.4rem;">
                        <i class="fa-solid fa-building-columns"></i> Bank Account Information
                    </div>
                    <div style="font-size: 0.85rem; background: var(--bg-hover); padding: 0.85rem; border-radius: 10px;">
                        <div><strong>Bank:</strong> ${c.bank_name || 'N/A'}</div>
                        <div><strong>Account No:</strong> ${c.bank_account_no || 'N/A'}</div>
                        <div><strong>IFSC Code:</strong> ${c.bank_ifsc || 'N/A'}</div>
                        <div><strong>Branch:</strong> ${c.bank_branch || 'N/A'}</div>
                    </div>
                </div>
            `;

            document.getElementById('view-comp-body').innerHTML = html;
            document.getElementById('btn-view-to-edit').onclick = function() {
                window.editCompanyProfile(c.id);
            };
            const modal = document.getElementById('modal-company-view');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.opacity = '1';
                modal.style.pointerEvents = 'auto';
                modal.classList.add('active');
            }
        });
    };

    // Delete confirmation
    window.confirmDeleteCompany = function(id, name) {
        if (confirm(`Are you sure you want to delete company profile "${name}"?\nThis action cannot be undone.`)) {
            const form = document.getElementById('delete-company-form');
            form.action = `/admin/masters/company/${id}`;
            form.submit();
        }
    };

    // Close modals on clicking overlay backdrop
    window.addEventListener('click', function(e) {
        const formModal = document.getElementById('modal-company-form');
        const viewModal = document.getElementById('modal-company-view');
        if (e.target === formModal) window.closeCompanyFormModal();
        if (e.target === viewModal) window.closeCompanyViewModal();
    });
</script>
@endpush
@endsection
