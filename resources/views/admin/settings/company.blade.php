@extends('admin.layouts.app')

@section('title', 'Company Settings - VIKAS UDHYOG ERP')
@section('page_code', 'set-company')

@section('content')
<section class="view-section active" id="view-set-company">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Settings</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Company Information</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-building-circle-gear text-primary"></i> Company Settings &amp; Corporate Profile
            </h1>
            <p class="erp-page-subtitle">
                Configure official operating business entity, statutory tax credentials, factory premises, invoice letterheads &amp; default settlement bank.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.company') }}" class="btn btn-outline" title="View all registered company entities">
                <i class="fa-solid fa-building me-1"></i> Company Directory
            </a>
            <button type="submit" form="company-settings-form" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success" style="margin-bottom: 1.5rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 5px solid #EF4444; padding: 1.1rem 1.35rem; border-radius: 14px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);">
            <div style="font-weight: 700; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem;"></i> Validation Errors Found
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Operating Entity Switcher Ribbon (If Multiple Companies Exist) -->
    @if(isset($allCompanies) && $allCompanies->count() > 1)
        <div class="card" style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #FFFFFF; border-radius: 14px; border: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 0.78rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.04em;">
                    <i class="fa-solid fa-industry me-1"></i> Configure Operating Plant / Entity:
                </span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                @foreach($allCompanies as $compItem)
                    <a href="{{ route('admin.settings.company', ['company_id' => $compItem->id]) }}" 
                       class="btn btn-sm {{ $company->id === $compItem->id ? 'btn-primary' : 'btn-outline' }}" 
                       style="padding: 5px 14px; font-size: 0.8rem; font-weight: 700; border-radius: 8px;">
                        <i class="fa-solid fa-building me-1"></i> {{ $compItem->name }}
                        @if($compItem->is_default)
                            <span class="badge" style="background: rgba(255,255,255,0.25); color: inherit; font-size: 0.65rem; margin-left: 4px;">Primary</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('admin.settings.company.update') }}" method="POST" id="company-settings-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="company_id" value="{{ $company->id }}">

        <div class="form-grid-layout" style="display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; align-items: start;">
            
            <!-- Left Main Form Column -->
            <div class="form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- 1. Business Identity & Operating Profile -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(91, 132, 30, 0.1); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">1. Business Identity &amp; Corporate Header</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Official company legal name and operating classification</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.15rem;">
                        <!-- Legal Company Name -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Company Legal Name <span style="color: #DC2626;">*</span>
                                <span style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-left: 6px;">(Printed as primary title on tax invoices &amp; bills)</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-building" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <input type="text" name="name" id="field-name" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="Enter legal company name" value="{{ old('name', $company->name) }}" required autofocus>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.15rem;">
                            <!-- Short Code -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                    Short Code / Prefix
                                    <span style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-left: 4px;">(Used in invoice numbering)</span>
                                </label>
                                <div class="erp-field-icon-wrap" style="position: relative;">
                                    <i class="fa-solid fa-hashtag" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                    <input type="text" name="code" id="field-code" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px; text-transform: uppercase;" placeholder="Enter company code" value="{{ old('code', $company->code) }}">
                                </div>
                            </div>

                            <!-- Financial Year -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                    Financial Accounting Year
                                    <span style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-left: 4px;">(Active FY)</span>
                                </label>
                                <div class="erp-field-icon-wrap" style="position: relative;">
                                    <i class="fa-solid fa-calendar-days" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                    <input type="text" name="financial_year" id="field-fy" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="e.g. 2026-2027" value="{{ old('financial_year', $company->financial_year ?? '2026-2027') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Tagline -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Corporate Tagline / Brand Subtitle
                                <span style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-left: 6px;">(Appears below firm title on official stationery)</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-quote-left" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <input type="text" name="tagline" id="field-tagline" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="e.g. Pure Herbal & Henna Manufacturing" value="{{ old('tagline', $company->tagline) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Tax & Compliance Registrations -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(2, 132, 199, 0.1); color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">2. Tax Registrations &amp; Statutory Identifiers</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">GSTIN, Permanent Account Number (PAN) and compliance codes</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.15rem;">
                        <!-- GSTIN -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                GSTIN Registration Number
                                <span style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-left: 4px;">(15-digit GST Code)</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-receipt" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <input type="text" name="gstin" id="field-gstin" class="form-control font-monospace" style="padding-left: 2.75rem; height: 44px; border-radius: 10px; text-transform: uppercase;" placeholder="e.g. 08ABCDE1234F1Z5" maxlength="20" value="{{ old('gstin', $company->gstin) }}">
                            </div>
                        </div>

                        <!-- PAN -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Income Tax PAN Number
                                <span style="font-size: 0.74rem; font-weight: 500; color: #64748B; margin-left: 4px;">(10-digit PAN)</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-id-card" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <input type="text" name="pan" id="field-pan" class="form-control font-monospace" style="padding-left: 2.75rem; height: 44px; border-radius: 10px; text-transform: uppercase;" placeholder="e.g. ABCDE1234F" maxlength="20" value="{{ old('pan', $company->pan) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Factory / Plant Location & Contact -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(217, 119, 6, 0.1); color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">3. Factory Premises &amp; Communication Channels</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Physical address, dispatch location and contact points</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 1.15rem;">
                        <!-- Factory Address -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Factory / Plant Address
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-map-location-dot" style="position: absolute; left: 14px; top: 16px; color: #94A3B8; font-size: 0.95rem;"></i>
                                <textarea name="address" id="field-address" class="form-control" rows="2" style="padding-left: 2.75rem; border-radius: 10px; resize: vertical;" placeholder="Enter complete factory address">{{ old('address', $company->address) }}</textarea>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.15rem;">
                            <!-- City -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">City</label>
                                <input type="text" name="city" id="field-city" class="form-control" style="height: 44px; border-radius: 10px;" placeholder="e.g. Sojat City" value="{{ old('city', $company->city) }}">
                            </div>

                            <!-- State -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">State</label>
                                <input type="text" name="state" id="field-state" class="form-control" style="height: 44px; border-radius: 10px;" placeholder="e.g. Rajasthan" value="{{ old('state', $company->state ?? 'Rajasthan') }}">
                            </div>

                            <!-- Pincode -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Pincode</label>
                                <input type="text" name="pincode" id="field-pincode" class="form-control font-monospace" style="height: 44px; border-radius: 10px;" placeholder="e.g. 306104" maxlength="10" value="{{ old('pincode', $company->pincode) }}">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.15rem;">
                            <!-- Phone -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Phone / Contact</label>
                                <div class="erp-field-icon-wrap" style="position: relative;">
                                    <i class="fa-solid fa-phone" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.9rem;"></i>
                                    <input type="text" name="phone" id="field-phone" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="e.g. +91 98290 12345" value="{{ old('phone', $company->phone) }}">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Official Email</label>
                                <div class="erp-field-icon-wrap" style="position: relative;">
                                    <i class="fa-solid fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.9rem;"></i>
                                    <input type="email" name="email" id="field-email" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="e.g. info@vikasudhyog.com" value="{{ old('email', $company->email) }}">
                                </div>
                            </div>

                            <!-- Website -->
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Corporate Website</label>
                                <div class="erp-field-icon-wrap" style="position: relative;">
                                    <i class="fa-solid fa-globe" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.9rem;"></i>
                                    <input type="url" name="website" id="field-website" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="https://..." value="{{ old('website', $company->website) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Bank Settlement Account (For Invoices) -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(5, 150, 105, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-landmark"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">4. Default Settlement Bank (Printed on Invoices)</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Bank account where customer payments and wire transfers are received</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.15rem;">
                        <!-- Bank Name -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Bank Name</label>
                            <input type="text" name="bank_name" id="field-bank-name" class="form-control" style="height: 44px; border-radius: 10px;" placeholder="e.g. State Bank of India" value="{{ old('bank_name', $company->bank_name) }}">
                        </div>

                        <!-- Account Number -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Account Number</label>
                            <input type="text" name="bank_account_no" id="field-account-no" class="form-control font-monospace" style="height: 44px; border-radius: 10px;" placeholder="e.g. 39201928472" value="{{ old('bank_account_no', $company->bank_account_no) }}">
                        </div>

                        <!-- IFSC Code -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">IFSC Code</label>
                            <input type="text" name="bank_ifsc" id="field-ifsc" class="form-control font-monospace" style="height: 44px; border-radius: 10px; text-transform: uppercase;" placeholder="e.g. SBIN0031245" value="{{ old('bank_ifsc', $company->bank_ifsc) }}">
                        </div>

                        <!-- Branch Name -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">Branch Name</label>
                            <input type="text" name="bank_branch" id="field-branch" class="form-control" style="height: 44px; border-radius: 10px;" placeholder="e.g. Sojat Main Branch" value="{{ old('bank_branch', $company->bank_branch) }}">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="form-side-col" style="display: flex; flex-direction: column; gap: 1.5rem; position: sticky; top: 1.5rem;">
                
                <!-- Action Submission Card -->
                <div class="card erp-sidebar-actions-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <button type="submit" class="btn btn-primary" style="width: 100%; height: 46px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: #5B841E; border: none; box-shadow: 0 4px 12px rgba(91, 132, 30, 0.3);">
                        <i class="fa-solid fa-floppy-disk"></i> Save Settings
                    </button>
                    
                    <label style="display: flex; align-items: center; gap: 0.65rem; margin-top: 0.5rem; cursor: pointer; user-select: none;">
                        <input type="checkbox" name="is_default" value="1" id="is_default" {{ old('is_default', $company->is_default) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #5B841E;">
                        <span style="font-size: 0.84rem; font-weight: 600; color: #1E293B;">Set as Primary Default Entity</span>
                    </label>

                    <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #F1F5F9; font-size: 0.76rem; color: #64748B;">
                        <div>Status: <strong style="color: #059669; text-transform: uppercase;">Active</strong></div>
                        <div style="margin-top: 3px;">Created: <span class="font-monospace">{{ $company->created_at ? $company->created_at->format('d M Y') : '—' }}</span></div>
                        <div style="margin-top: 3px;">Last Updated: <span class="font-monospace">{{ $company->updated_at ? $company->updated_at->format('d M Y, h:i A') : '—' }}</span></div>
                    </div>
                </div>

                <!-- Live Dynamic Letterhead / Invoice Preview Card -->
                <div class="card erp-preview-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #CBD5E1; background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%); box-shadow: 0 4px 20px -2px rgba(0,0,0,0.06);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #5B841E;">
                            <i class="fa-solid fa-file-invoice me-1"></i> Live Invoice Header Preview
                        </span>
                        <span class="badge" style="background: rgba(91, 132, 30, 0.12); color: #5B841E; font-size: 0.68rem; font-weight: 700; padding: 2px 6px;">Tax Letterhead</span>
                    </div>

                    <!-- Mini Letterhead Box -->
                    <div style="background: #FFFFFF; border: 1px dashed #CBD5E1; border-radius: 12px; padding: 1.25rem; text-align: center;">
                        <div id="preview-avatar" style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; font-weight: 800; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.65rem; box-shadow: 0 2px 8px rgba(91, 132, 30, 0.3);">
                            {{ strtoupper(substr($company->name, 0, 2)) }}
                        </div>

                        <h4 id="preview-name" style="font-weight: 800; font-size: 1.05rem; color: #0F172A; margin: 0 0 2px 0;">
                            {{ $company->name }}
                        </h4>
                        
                        <p id="preview-tagline" style="font-size: 0.74rem; color: #5B841E; font-weight: 600; margin: 0 0 0.5rem 0;">
                            {{ $company->tagline ?: 'Pure Herbal & Henna Manufacturing' }}
                        </p>

                        <div style="font-size: 0.72rem; color: #64748B; line-height: 1.4; border-top: 1px solid #F1F5F9; padding-top: 0.5rem; margin-top: 0.5rem;">
                            <div id="preview-address">{{ $company->address ?: 'Industrial Area, Mandi Road' }}</div>
                            <div>
                                <span id="preview-city">{{ $company->city ?: 'Sojat City' }}</span>, 
                                <span id="preview-state">{{ $company->state ?: 'Rajasthan' }}</span> - 
                                <span id="preview-pincode" class="font-monospace">{{ $company->pincode ?: '306104' }}</span>
                            </div>
                        </div>

                        <div style="margin-top: 0.65rem; padding-top: 0.65rem; border-top: 1px dashed #E2E8F0; display: flex; flex-direction: column; gap: 3px; font-size: 0.72rem;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748B;">GSTIN:</span>
                                <strong id="preview-gstin" class="font-monospace" style="color: #0F172A;">{{ $company->gstin ?: '08ABCDE1234F1Z5' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748B;">PAN:</span>
                                <strong id="preview-pan" class="font-monospace" style="color: #0F172A;">{{ $company->pan ?: 'ABCDE1234F' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748B;">Bank Settlement:</span>
                                <strong id="preview-bank" style="color: #059669;">{{ $company->bank_name ?: 'State Bank of India' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div style="font-size: 0.72rem; color: #64748B; text-align: center; margin-top: 0.75rem;">
                        <i class="fa-solid fa-circle-check text-success me-1"></i> Dynamically appears on all invoices, gate passes, and delivery challans.
                    </div>
                </div>

                <!-- Guidance Notes -->
                <div class="card" style="padding: 1.25rem; border-radius: 14px; border: 1px solid #E2E8F0; background: #FFFFFF;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #1E293B; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-circle-info text-primary"></i> Operating Notes
                    </div>
                    <ul style="font-size: 0.74rem; color: #64748B; margin: 0; padding-left: 1.2rem; line-height: 1.5;">
                        <li>Ensure <strong>GSTIN</strong> matches your government GST portal registration.</li>
                        <li>The <strong>Bank Details</strong> entered here will be printed at the bottom of customer invoices for direct NEFT/RTGS payments.</li>
                        <li>You can maintain multiple sister plants and switch active view via the top navbar.</li>
                    </ul>
                </div>

            </div>

        </div>
    </form>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('field-name');
    const taglineInput = document.getElementById('field-tagline');
    const gstinInput = document.getElementById('field-gstin');
    const panInput = document.getElementById('field-pan');
    const addressInput = document.getElementById('field-address');
    const cityInput = document.getElementById('field-city');
    const stateInput = document.getElementById('field-state');
    const pincodeInput = document.getElementById('field-pincode');
    const bankInput = document.getElementById('field-bank-name');

    const prevAvatar = document.getElementById('preview-avatar');
    const prevName = document.getElementById('preview-name');
    const prevTagline = document.getElementById('preview-tagline');
    const prevGstin = document.getElementById('preview-gstin');
    const prevPan = document.getElementById('preview-pan');
    const prevAddress = document.getElementById('preview-address');
    const prevCity = document.getElementById('preview-city');
    const prevState = document.getElementById('preview-state');
    const prevPincode = document.getElementById('preview-pincode');
    const prevBank = document.getElementById('preview-bank');

    function updatePreview() {
        const nameVal = nameInput ? nameInput.value.trim() : '';
        if (prevName) prevName.textContent = nameVal || 'Company Name';
        if (prevAvatar) {
            const initials = nameVal ? nameVal.substring(0, 2).toUpperCase() : 'VU';
            prevAvatar.textContent = initials;
        }

        if (prevTagline && taglineInput) {
            prevTagline.textContent = taglineInput.value.trim() || 'Pure Herbal & Henna Manufacturing';
        }
        if (prevGstin && gstinInput) {
            prevGstin.textContent = gstinInput.value.trim().toUpperCase() || 'Not Set';
        }
        if (prevPan && panInput) {
            prevPan.textContent = panInput.value.trim().toUpperCase() || 'Not Set';
        }
        if (prevAddress && addressInput) {
            prevAddress.textContent = addressInput.value.trim() || 'Factory Address';
        }
        if (prevCity && cityInput) {
            prevCity.textContent = cityInput.value.trim() || 'Sojat City';
        }
        if (prevState && stateInput) {
            prevState.textContent = stateInput.value.trim() || 'Rajasthan';
        }
        if (prevPincode && pincodeInput) {
            prevPincode.textContent = pincodeInput.value.trim() || '306104';
        }
        if (prevBank && bankInput) {
            prevBank.textContent = bankInput.value.trim() || 'Bank Name';
        }
    }

    [nameInput, taglineInput, gstinInput, panInput, addressInput, cityInput, stateInput, pincodeInput, bankInput].forEach(inp => {
        if (inp) {
            inp.addEventListener('input', updatePreview);
        }
    });
});
</script>
@endpush
@endsection
