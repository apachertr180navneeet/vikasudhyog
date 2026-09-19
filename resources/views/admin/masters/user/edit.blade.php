@extends('admin.layouts.app')

@section('title', 'Edit ' . $user->name . ' - VIKAS UDHYOG ERP')
@section('page_code', 'master-user-edit')

@section('content')
<section class="view-section active" id="view-master-user-edit">
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
                <span class="erp-breadcrumb-active">Edit User</span>
            </div>
            <h1 class="erp-page-title">
                <span class="erp-page-title-icon-box">
                    <i class="fa-solid fa-user-pen"></i>
                </span>
                Edit User: {{ $user->name }}
            </h1>
            <p class="erp-page-subtitle">
                Update operator credentials, access roles, status, and plant associations.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.masters.user.show', $user) }}" class="btn btn-outline erp-btn-header-back">
                <i class="fa-regular fa-eye"></i> View Profile
            </a>
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

    <form action="{{ route('admin.masters.user.update', $user) }}" method="POST" id="user-edit-form">
        @csrf
        @method('PUT')

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
                                <input type="text" name="name" id="field-name" class="form-control erp-field-input-iconified" placeholder="e.g. Navneet Sharma" value="{{ old('name', $user->name) }}" required oninput="updateLivePreview()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="erp-field-label">
                                Email Address <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-regular fa-envelope erp-field-icon"></i>
                                <input type="email" name="email" id="field-email" class="form-control erp-field-input-iconified" placeholder="e.g. navneet@vikasudhyog.com" value="{{ old('email', $user->email) }}" required oninput="updateLivePreview()">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="erp-field-label">
                                Mobile Number
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-phone erp-field-icon"></i>
                                <input type="text" name="phone" id="field-phone" class="form-control erp-field-input-iconified" placeholder="e.g. +91 98290 12345" value="{{ old('phone', $user->phone) }}" oninput="updateLivePreview()">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Login Credentials & Security (Optional Password Reset) -->
                <div class="card erp-form-section-card">
                    <div class="erp-form-section-header">
                        <div class="erp-form-section-header-left">
                            <div class="erp-form-section-icon-box erp-form-icon-blue">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div>
                                <h3 class="erp-form-section-title">2. Login Credentials &amp; Password Update</h3>
                                <p class="erp-form-section-desc">Username handle and optional password reset</p>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline erp-btn-gen-pwd" onclick="generateRandomPassword()" title="Generate Random Strong Password">
                            <i class="fa-solid fa-dice"></i> Generate New Password
                        </button>
                    </div>

                    <div class="erp-form-section-body">
                        <div class="form-group erp-form-col-full">
                            <label class="erp-field-label">
                                Username <span class="erp-req-star">*</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-at erp-field-icon"></i>
                                <input type="text" name="username" id="field-username" class="form-control erp-field-input-iconified erp-field-input-mono" placeholder="e.g. navneet" value="{{ old('username', $user->username) }}" required oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_.-]/g, ''); updateLivePreview();">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="erp-field-label-flex">
                                <span>New Password</span>
                                <span class="erp-field-hint mt-0">(Leave empty to keep current)</span>
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-lock erp-field-icon"></i>
                                <input type="password" name="password" id="field-password" class="form-control erp-field-input-iconified-pwd" placeholder="Leave blank to keep unchanged" oninput="checkPasswordStrength(this.value)">
                                <button type="button" onclick="togglePasswordVisibility('field-password', this)" class="erp-pwd-toggle-btn" title="Show/Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div>
                                <div class="erp-pwd-strength-bar-wrap">
                                    <div id="pwd-strength-bar" class="erp-pwd-strength-bar"></div>
                                </div>
                                <span id="pwd-strength-text" class="erp-pwd-strength-text">Only fill if changing password</span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label class="erp-field-label">
                                Confirm New Password
                            </label>
                            <div class="erp-field-icon-wrap">
                                <i class="fa-solid fa-shield-check erp-field-icon"></i>
                                <input type="password" name="password_confirmation" id="field-password-confirm" class="form-control erp-field-input-iconified-pwd" placeholder="Re-enter new password">
                                <button type="button" onclick="togglePasswordVisibility('field-password-confirm', this)" class="erp-pwd-toggle-btn" title="Show/Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
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
                                        <option value="{{ $roleKey }}" {{ old('role', $user->role) === $roleKey ? 'selected' : '' }} data-desc="{{ $roleDesc }}">
                                            {{ $roleKey }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="role-description-box" class="erp-profile-mod-badge w-100 mt-2">
                                System role privileges
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
                                        <option value="{{ $comp->id }}" {{ old('company_id', $user->company_id) == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->name }} ({{ $comp->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="erp-form-side-col">

                <!-- Live Preview Card -->
                <div class="card erp-preview-card">
                    <div class="erp-preview-header">
                        Live Card Preview
                    </div>
                    <div class="erp-preview-body">
                        <div id="preview-avatar" class="erp-preview-avatar-circle">
                            {{ $user->initials }}
                        </div>
                        <h4 id="preview-name" class="erp-preview-name-text">
                            {{ $user->name }}
                        </h4>
                        <div id="preview-username" class="erp-user-username mb-2">
                            @{{ $user->username }}
                        </div>

                        <div>
                            <span id="preview-role" class="erp-preview-role-pill">
                                {{ $user->role }}
                            </span>
                        </div>

                        <div id="preview-company" class="erp-preview-meta-list">
                            @if($user->company)
                                <div class="erp-preview-meta-item">
                                    <i class="fa-solid fa-building"></i>
                                    <span>{{ $user->company->name }}</span>
                                </div>
                            @else
                                <div class="erp-preview-meta-item">
                                    <i class="fa-solid fa-globe"></i>
                                    <span>All Plants / Global</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Account Metadata Card -->
                <div class="card erp-profile-detail-card p-3">
                    <div class="erp-profile-audit-title mb-2">
                        <i class="fa-solid fa-clock-rotate-left"></i> Audit Trail
                    </div>
                    <div class="d-flex flex-column gap-2 text-muted">
                        <div class="d-flex justify-content-between">
                            <span>Account ID:</span>
                            <strong class="text-dark">#{{ $user->id }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Created:</span>
                            <strong class="text-dark">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Last Login:</span>
                            <strong class="text-dark">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : 'Never' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Last IP Address:</span>
                            <strong class="text-dark font-monospace">{{ $user->last_login_ip ?: 'None' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="card erp-form-section-card p-3 d-flex flex-column gap-2">
                    <button type="submit" class="btn btn-primary erp-btn-header-primary w-100 justify-content-center">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.masters.user') }}" class="btn btn-outline erp-btn-header-back w-100 justify-content-center">
                        Cancel
                    </a>
                </div>

                <!-- Danger Zone (Delete Account) -->
                @if(auth()->id() !== $user->id)
                    <div class="erp-alert-error-list-card p-3">
                        <div class="erp-alert-error-header mb-1">
                            <i class="fa-solid fa-triangle-exclamation"></i> Danger Zone
                        </div>
                        <p class="erp-field-hint mt-0 mb-2">Soft deletes this user account and immediately prevents login.</p>
                        <button type="button" class="btn btn-danger erp-btn-header-back w-100 justify-content-center" onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->username }}')">
                            <i class="fa-solid fa-trash-can"></i> Delete User Account
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </form>
</section>

<!-- Delete User Modal Form -->
<form id="delete-user-form" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

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
    if (!password) {
        bar.style.width = '0%';
        text.innerText = 'Only fill if changing password';
        text.style.color = 'var(--text-muted)';
        return;
    }

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

function confirmDeleteUser(userId, userName, username) {
    Swal.fire({
        title: 'Delete User Account?',
        html: `Are you sure you want to remove user <strong>"${userName}"</strong> (@${username})?<br><small class="text-muted">This action soft-deletes the user and revokes ERP access immediately.</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-solid fa-trash-can"></i> Yes, Delete User',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'swal2-border-radius'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-user-form');
            form.action = `/admin/masters/user/${userId}`;
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateLivePreview();
});
</script>
@endpush
@endsection
