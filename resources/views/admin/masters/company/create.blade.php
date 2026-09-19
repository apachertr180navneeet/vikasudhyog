@extends('admin.layouts.app')

@section('title', 'Add New Company - VIKAS UDHYOG ERP')
@section('page_code', 'master-company-create')

@section('content')
<section class="view-section active" id="view-master-company-create">
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
                <span style="color: var(--primary); font-weight: 600;">Add New Company</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.6rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-building-circle-check" style="color: var(--primary);"></i> Add New Company Profile
            </h1>
            <p class="page-subtitle" style="margin: 0.2rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Register a new manufacturing plant, corporate entity, or trading division.
            </p>
        </div>

        <div>
            <a href="{{ route('admin.masters.company') }}" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Companies
            </a>
        </div>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 4px solid #EF4444; padding: 1rem 1.25rem; border-radius: 12px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.5rem;">
            <div style="font-weight: 700; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-circle-exclamation"></i> Please correct the errors below:
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card" style="border-radius: 18px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); overflow: hidden;">
        <form action="{{ route('admin.masters.company.store') }}" method="POST">
            @csrf

            <div style="padding: 2rem;">
                <!-- Section 1: General Business Information -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 0.82rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(91, 132, 30, 0.15);">
                        <i class="fa-solid fa-id-card"></i> 1. General Business Profile
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                        <div style="grid-column: span 2;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Company Legal Name <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Vikas Udhyog" value="{{ old('name') }}" required autofocus>
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Entity Short Code
                            </label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. VU-SOJAT" value="{{ old('code') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Status <span style="color: red;">*</span>
                            </label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div style="grid-column: span 2;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Tagline / Scope of Operations
                            </label>
                            <input type="text" name="tagline" class="form-control" placeholder="e.g. Premium Sojat Henna Powder & Herbal Formulations Manufacturer" value="{{ old('tagline') }}">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Tax Registrations & Financial Year -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 0.82rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(91, 132, 30, 0.15);">
                        <i class="fa-solid fa-file-invoice"></i> 2. Tax Registrations &amp; Fiscal Year
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                GSTIN Number
                            </label>
                            <input type="text" name="gstin" class="form-control" placeholder="08AABCV1234F1Z5" maxlength="20" style="text-transform: uppercase;" value="{{ old('gstin') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Permanent Account Number (PAN)
                            </label>
                            <input type="text" name="pan" class="form-control" placeholder="AABCV1234F" maxlength="15" style="text-transform: uppercase;" value="{{ old('pan') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Current Financial Year
                            </label>
                            <input type="text" name="financial_year" class="form-control" placeholder="2026-2027" value="{{ old('financial_year', '2026-2027') }}">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Contact & Address -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 0.82rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(91, 132, 30, 0.15);">
                        <i class="fa-solid fa-map-location-dot"></i> 3. Address &amp; Factory Location
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Primary Phone
                            </label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 94140 12345" value="{{ old('phone') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Official Email Address
                            </label>
                            <input type="email" name="email" class="form-control" placeholder="info@vikasudhyog.com" value="{{ old('email') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Website URL
                            </label>
                            <input type="text" name="website" class="form-control" placeholder="https://vikasudhyog.com" value="{{ old('website') }}">
                        </div>

                        <div style="grid-column: span 3;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Factory / Registered Office Address
                            </label>
                            <textarea name="address" rows="2" class="form-control" placeholder="Plot No. 12-15, Industrial Area, Near Krishi Mandi">{{ old('address') }}</textarea>
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                City <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="city" class="form-control" placeholder="Sojat City" value="{{ old('city', 'Sojat City') }}" required>
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                State <span style="color: red;">*</span>
                            </label>
                            <input type="text" name="state" class="form-control" placeholder="Rajasthan" value="{{ old('state', 'Rajasthan') }}" required>
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Pincode
                            </label>
                            <input type="text" name="pincode" class="form-control" placeholder="306104" value="{{ old('pincode') }}">
                        </div>
                    </div>
                </div>

                <!-- Section 4: Bank Settlement Credentials -->
                <div style="margin-bottom: 2rem;">
                    <div style="font-size: 0.82rem; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid rgba(91, 132, 30, 0.15);">
                        <i class="fa-solid fa-building-columns"></i> 4. Banking &amp; Settlement Details
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Bank Name
                            </label>
                            <input type="text" name="bank_name" class="form-control" placeholder="State Bank of India" value="{{ old('bank_name') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Account Number
                            </label>
                            <input type="text" name="bank_account_no" class="form-control" placeholder="38491029384" value="{{ old('bank_account_no') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                IFSC Code
                            </label>
                            <input type="text" name="bank_ifsc" class="form-control" placeholder="SBIN0031204" style="text-transform: uppercase;" value="{{ old('bank_ifsc') }}">
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Branch
                            </label>
                            <input type="text" name="bank_branch" class="form-control" placeholder="Main Branch Sojat City" value="{{ old('bank_branch') }}">
                        </div>
                    </div>
                </div>

                <!-- Section 5: Primary Default Unit Checkbox -->
                <div style="padding: 1.25rem; background: var(--bg-hover); border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.85rem; border: 1px solid var(--border-color);">
                    <input type="checkbox" name="is_default" id="comp-is-default" value="1" {{ old('is_default') ? 'checked' : '' }} style="width: 20px; height: 20px; accent-color: var(--primary); cursor: pointer;">
                    <div>
                        <label for="comp-is-default" style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary); cursor: pointer; display: block;">
                            Set as Default Primary Operating Entity
                        </label>
                        <span style="font-size: 0.78rem; color: var(--text-muted);">
                            This company will be pre-selected in invoices, bills, and voucher entries across the ERP.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Footer / Actions -->
            <div style="padding: 1.25rem 2rem; background: var(--bg-hover); border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: flex-end; gap: 1rem;">
                <a href="{{ route('admin.masters.company') }}" class="btn btn-outline" style="padding: 0.65rem 1.25rem;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.75rem; font-weight: 700;">
                    <i class="fa-solid fa-floppy-disk"></i> Save Company Profile
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
