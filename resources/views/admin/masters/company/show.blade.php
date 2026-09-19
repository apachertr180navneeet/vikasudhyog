@extends('admin.layouts.app')

@section('title', $company->name . ' - Company Profile')
@section('page_code', 'master-company-show')

@section('content')
<section class="view-section active" id="view-master-company-show">
    <!-- Breadcrumb & Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <a href="{{ route('admin.masters.company') }}" style="color: var(--text-muted); text-decoration: none;">Company Master</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span style="color: var(--primary); font-weight: 600;">{{ $company->name }}</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.6rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-building" style="color: var(--primary);"></i> {{ $company->name }}
            </h1>
            <p class="page-subtitle" style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                {{ $company->tagline ?? 'Registered Business Profile & Taxation Details' }}
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.masters.company.edit', $company->id) }}" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> Edit Company
            </a>
            <a href="{{ route('admin.masters.company') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Company Details Card -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
        <!-- Left Summary Card -->
        <div class="card" style="padding: 1.75rem; border-radius: 18px; text-align: center; border-top: 4px solid var(--primary);">
            <div style="width: 72px; height: 72px; border-radius: 18px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 1rem; box-shadow: 0 8px 20px rgba(91, 132, 30, 0.25);">
                {{ strtoupper(substr($company->name, 0, 1)) }}
            </div>
            <h2 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.35rem;">
                {{ $company->name }}
            </h2>
            @if($company->code)
                <div style="display: inline-block; font-size: 0.78rem; font-weight: 700; color: var(--primary); background: rgba(91, 132, 30, 0.12); padding: 3px 10px; border-radius: 20px; margin-bottom: 0.75rem;">
                    Code: {{ $company->code }}
                </div>
            @endif

            <div style="margin-top: 0.5rem;">
                <span class="badge {{ $company->status === 'active' ? 'badge-success' : 'badge-danger' }}" style="padding: 5px 12px; font-size: 0.78rem; border-radius: 20px;">
                    {{ ucfirst($company->status) }}
                </span>
                @if($company->is_default)
                    <span class="badge" style="background: rgba(212, 160, 23, 0.15); color: #B45309; border: 1px solid rgba(212, 160, 23, 0.3); padding: 5px 12px; font-size: 0.78rem; border-radius: 20px; margin-left: 0.35rem;">
                        <i class="fa-solid fa-star"></i> Primary Unit
                    </span>
                @endif
            </div>

            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color); text-align: left; font-size: 0.85rem;">
                <div style="margin-bottom: 0.75rem;">
                    <div style="color: var(--text-muted); font-size: 0.75rem;">Financial Year</div>
                    <div style="font-weight: 700; color: var(--text-primary);">{{ $company->financial_year ?? '2026-2027' }}</div>
                </div>
                <div style="margin-bottom: 0.75rem;">
                    <div style="color: var(--text-muted); font-size: 0.75rem;">Phone</div>
                    <div style="font-weight: 600; color: var(--text-primary);">{{ $company->phone ?? 'Not provided' }}</div>
                </div>
                <div>
                    <div style="color: var(--text-muted); font-size: 0.75rem;">Email</div>
                    <div style="font-weight: 600; color: var(--text-primary);">{{ $company->email ?? 'Not provided' }}</div>
                </div>
            </div>
        </div>

        <!-- Right Detailed Information Cards -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Tax & Legal -->
            <div class="card" style="padding: 1.75rem; border-radius: 18px;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-file-invoice" style="color: var(--primary);"></i> Tax &amp; Government Registrations
                </h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div style="padding: 1rem; background: var(--bg-hover); border-radius: 12px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">GSTIN Registration</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: var(--primary); margin-top: 0.25rem;">{{ $company->gstin ?? 'N/A' }}</div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-hover); border-radius: 12px;">
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Permanent Account Number (PAN)</div>
                        <div style="font-size: 1.1rem; font-weight: 800; color: var(--text-primary); margin-top: 0.25rem;">{{ $company->pan ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Factory & Location -->
            <div class="card" style="padding: 1.75rem; border-radius: 18px;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-map-location-dot" style="color: var(--primary);"></i> Plant &amp; Registered Address
                </h3>
                <div style="font-size: 0.95rem; line-height: 1.6; color: var(--text-primary); background: var(--bg-hover); padding: 1.25rem; border-radius: 12px;">
                    <div>{{ $company->address ?? 'Address not specified' }}</div>
                    <div style="margin-top: 0.35rem; font-weight: 700;">
                        {{ $company->city }}, {{ $company->state }} {{ $company->pincode ? '- ' . $company->pincode : '' }}
                    </div>
                    @if($company->website)
                        <div style="margin-top: 0.5rem; font-size: 0.85rem;">
                            <i class="fa-solid fa-globe" style="color: var(--primary); margin-right: 4px;"></i>
                            <a href="{{ $company->website }}" target="_blank" style="color: var(--primary); font-weight: 600;">{{ $company->website }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Banking -->
            <div class="card" style="padding: 1.75rem; border-radius: 18px;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-building-columns" style="color: var(--primary);"></i> Bank Settlement Account
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background: var(--bg-hover); padding: 1.25rem; border-radius: 12px;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Bank Name</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary);">{{ $company->bank_name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Account Number</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary);">{{ $company->bank_account_no ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">IFSC Code</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary);">{{ $company->bank_ifsc ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Branch</div>
                        <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary);">{{ $company->bank_branch ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
