@extends('admin.layouts.app')

@section('title', 'Add New User - VIKAS UDHYOG ERP')
@section('page_code', 'master-user-create')

@section('content')
<section class="view-section active" id="view-master-user-create">
    <!-- Breadcrumb & Top Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.masters.user') }}">User Master</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Add New User</span>
            </div>
            <h1 class="erp-page-title">
                <span class="erp-page-title-icon-box">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                Create User Account
            </h1>
            <p class="erp-page-subtitle">
                Provision login credentials, ERP access roles and multi-plant permissions for an operator.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.user') }}" class="btn btn-outline erp-btn-header-back">
                <i class="fa-solid fa-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="alert erp-alert-error-list-card">
            <div class="erp-alert-error-header">
                <i class="fa-solid fa-triangle-exclamation"></i> Please correct the following errors:
            </div>
            <ul class="erp-alert-error-ul">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.masters.user.store') }}" method="POST" id="user-create-form">
        @csrf

        <div class="erp-form-layout-2col">
            <!-- Left Main Form Column -->
            <div class="erp-form-main-col">

                <!-- 1. Personal & Contact Details -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-primary">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">1. Personal &amp; Contact Details</h3>
                                <p class="erp-form-section-desc">Operator's legal full name, communication phone and email</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Full Name <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user erp-field-icon"></i>
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="e.g. Navneet Sharma" value="{{ old('name') }}" required autofocus oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Enter the employee's official full name</span>
                        </div>

                        <div class="form-group">
                            <label class="erp-field-label">
                                Email Address <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-envelope erp-field-icon"></i>
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="e.g. navneet@vikasudhyog.com" value="{{ old('email') }}" required oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Used for system notifications &amp; password recovery</span>
                        </div>

                        <div class="form-group">
                            <label class="erp-field-label">
                                Mobile Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-phone erp-field-icon"></i>
                                <input type="text" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="e.g. +91 98290 12345" value="{{ old('phone') }}" oninput="updateLivePreview()">
                            </div>
                            <span class="erp-field-hint">Optional mobile contact number</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Login Credentials & Security -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-blue">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Login Credentials &amp; Security</h3>
                                <p class="erp-form-section-desc">Unique login handle and authentication password</p>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline erp-btn-gen-pwd" onclick="generateRandomPassword()" title="Generate Random Strong Password">
                            <i class="fa-solid fa-dice"></i> Generate Password
                        </button>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label-flex">
                                <span>Username <span class="erp-req-star">*</span></span>
                                <button type="button" onclick="suggestUsername()" class="erp-btn-suggest">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-suggest from Name
                                </button>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-at erp-field-icon"></i>
                                <input type="text" name="username" id="field-username" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="e.g. navneet" value="{{ old('username') }}" required oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_.-]/g, ''); updateLivePreview();">
                            </div>
                            <span class="erp-field-hint">Must be unique. Allowed characters: lowercase letters, numbers, dot, dash, underscore.</span>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Account Password <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-lock erp-field-icon"></i>
                                <input type="password" name="password" id="field-password" class="form-control erp-field-input-iconified-pwd" placeholder="Minimum 6 characters" required oninput="checkPasswordStrength(this.value)">
                                <button type="button" onclick="togglePasswordVisibility('field-password', this)" class="erp-pwd-toggle-btn" title="Show/Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div>
                                <div class="erp-pwd-strength-bar-wrap">
                                    <div id="pwd-strength-bar" class="erp-pwd-strength-bar"></div>
                                </div>
                                <span id="pwd-strength-text" class="erp-pwd-strength-text">Password strength</span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Confirm Password <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-shield-check erp-field-icon"></i>
                                <input type="password" name="password_confirmation" id="field-password-confirm" class="form-control erp-field-input-iconified-pwd" placeholder="Re-enter password" required>
                                <button type="button" onclick="togglePasswordVisibility('field-password-confirm', this)" class="erp-pwd-toggle-btn" title="Show/Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <span class="erp-field-hint">Both passwords must match exactly</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Role & Company Assignment -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-amber">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">3. Role &amp; Plant Assignment</h3>
                                <p class="erp-form-section-desc">Assign module access permissions and manufacturing plant mapping</p>
                            </div>
                        </div>
                    </div>

                    <div class="erp-form-section-body">
                        <!-- Role -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                System Role <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-user-tag erp-field-icon"></i>
                                <select name="role" id="field-role" class="form-control erp-field-input-iconified" required onchange="updateLivePreview()">
                                    @foreach($roles as $roleKey => $roleDesc)
                                        <option value="{{ $roleKey }}" {{ old('role', 'Staff') === $roleKey ? 'selected' : '' }} data-desc="{{ $roleDesc }}">
                                            {{ $roleKey }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="role-description-box" class="erp-profile-mod-badge w-100 mt-2">
                                Full data entry and operational privileges.
                            </div>
                        </div>

                        <!-- Company Assignment -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Company / Plant Assignment
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-building erp-field-icon"></i>
                                <select name="company_id" id="field-company" class="form-control erp-field-input-iconified" onchange="updateLivePreview()">
                                    <option value="">-- All Companies / Multi-Plant Global --</option>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->name }} ({{ $comp->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span class="erp-field-hint">Leave blank to allow access across all registered company entities</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-side-col">
                <input type="hidden" name="status" value="active">

                <!-- Live Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        Live Card Preview
                    </div>
                    <div class="erp-preview-body">
                        <div id="preview-avatar" class="erp-preview-avatar-circle">
                            VU
                        </div>
                        <h4 id="preview-name" class="erp-preview-name-text">
                            Full Name
                        </h4>
                        <div id="preview-username" class="erp-user-username mb-2">
                            @username
                        </div>

                        <div>
                            <span id="preview-role" class="erp-preview-role-pill">
                                Staff
                            </span>
                        </div>

                        <div id="preview-company" class="erp-preview-meta-list">
                            <div class="erp-preview-meta-item">
                                <i class="fa-solid fa-globe"></i>
                                <span>All Plants / Global</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="card erp-form-section-card p-3 d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary erp-btn-header-primary w-100 justify-content-center">
                        <i class="fa-solid fa-user-check"></i> Create User Account
                    </button>
                    <a href="{{ route('admin.masters.user') }}" class="btn btn-outline erp-btn-header-back w-100 justify-content-center">
                        Cancel
                    </a>
                </div>

                <!-- Info Box -->
                <div class="erp-profile-mod-badge flex-column align-items-start p-3">
                    <div class="erp-alert-error-header mb-1 text-primary">
                        <i class="fa-solid fa-circle-info"></i> Security Policy
                    </div>
                    <span class="erp-field-hint mt-0">Users will use their <strong>Username</strong> or <strong>Email</strong> along with their password to access the ERP portal. Role permissions apply immediately upon account creation.</span>
                </div>

            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function suggestUsername() {
    const name = document.getElementById('field-name').value.trim();
    if (!name) {
        toastr.info('Please type a Full Name first.');
        return;
    }
    const clean = name.toLowerCase().replace(/[^a-z0-9\s]/g, '').split(/\s+/);
    let username = '';
    if (clean.length === 1) {
        username = clean[0];
    } else {
        username = clean[0] + '.' + clean[clean.length - 1];
    }
    document.getElementById('field-username').value = username;
    updateLivePreview();
}

function generateRandomPassword() {
    const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*';
    let password = '';
    for (let i = 0; i < 10; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('field-password').value = password;
    document.getElementById('field-password-confirm').value = password;
    document.getElementById('field-password').type = 'text';
    document.getElementById('field-password-confirm').type = 'text';
    checkPasswordStrength(password);
    toastr.success('Generated random strong password!');
}

function checkPasswordStrength(password) {
    const bar = document.getElementById('pwd-strength-bar');
    const text = document.getElementById('pwd-strength-text');
    let strength = 0;
    if (password.length >= 6) strength += 25;
    if (password.length >= 9) strength += 25;
    if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;

    bar.style.width = strength + '%';
    if (strength <= 25) {
        bar.style.background = '#EF4444';
        text.innerText = 'Weak password';
        text.style.color = '#EF4444';
    } else if (strength <= 50) {
        bar.style.background = '#F59E0B';
        text.innerText = 'Fair password';
        text.style.color = '#F59E0B';
    } else if (strength <= 75) {
        bar.style.background = '#3B82F6';
        text.innerText = 'Good password';
        text.style.color = '#3B82F6';
    } else {
        bar.style.background = '#10B981';
        text.innerText = 'Strong password';
        text.style.color = '#10B981';
    }
}

function updateLivePreview() {
    const name = document.getElementById('field-name').value.trim() || 'Full Name';
    const username = document.getElementById('field-username').value.trim() || 'username';
    const roleSelect = document.getElementById('field-role');
    const role = roleSelect.value;
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleDesc = selectedOption ? selectedOption.getAttribute('data-desc') : '';
    const companySelect = document.getElementById('field-company');
    const companyText = companySelect.options[companySelect.selectedIndex].text;

    // Initials
    const words = name.split(/\s+/);
    let initials = 'VU';
    if (words.length >= 2 && words[0] && words[1]) {
        initials = (words[0][0] + words[1][0]).toUpperCase();
    } else if (words.length >= 1 && words[0]) {
        initials = words[0].substring(0, 2).toUpperCase();
    }

    document.getElementById('preview-avatar').innerText = initials;
    document.getElementById('preview-name').innerText = name;
    document.getElementById('preview-username').innerText = '@' + username;
    document.getElementById('preview-role').innerText = role;

    if (roleDesc) {
        document.getElementById('role-description-box').innerText = roleDesc;
    }

    const previewCompany = document.getElementById('preview-company');
    if (companySelect.value) {
        previewCompany.innerHTML = `<div class="erp-preview-meta-item"><i class="fa-solid fa-building"></i> <span>${companyText}</span></div>`;
    } else {
        previewCompany.innerHTML = `<div class="erp-preview-meta-item"><i class="fa-solid fa-globe"></i> <span>All Plants / Global</span></div>`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateLivePreview();
});
</script>
@endpush
@endsection
