@extends('admin.layouts.app')

@section('title', 'Add New User - VIKAS UDHYOG ERP')
@section('page_code', 'master-user-create')

@section('content')
<section class="view-section active" id="view-master-user-create">
    <!-- Breadcrumb & Top Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                <a href="{{ route('admin.dashboard') }}" style="color: var(--text-muted); text-decoration: none;">Dashboard</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span>Masters</span>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <a href="{{ route('admin.masters.user') }}" style="color: var(--text-muted); text-decoration: none;">User Master</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.65rem;"></i>
                <span style="color: var(--primary); font-weight: 600;">Add New User</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.65rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.65rem;">
                <span style="width: 40px; height: 40px; border-radius: 12px; background: rgba(91, 132, 30, 0.12); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                Create User Account
            </h1>
            <p class="page-subtitle" style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Provision login credentials, ERP access roles and multi-plant permissions for an operator.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.masters.user') }}" class="btn btn-outline" style="border-radius: 10px; height: 42px; padding: 0 1.25rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Error Alerts -->
    @if($errors->any())
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 5px solid #EF4444; padding: 1.1rem 1.35rem; border-radius: 14px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.75rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);">
            <div style="font-weight: 700; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem;"></i> Please correct the following errors:
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.masters.user.store') }}" method="POST" id="user-create-form">
        @csrf

        <div class="form-grid-layout" style="display: grid; grid-template-columns: 1fr 340px; gap: 1.5rem; align-items: start;">
            <!-- Left Main Form Column -->
            <div class="form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">

                <!-- 1. Personal & Contact Details -->
                <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(91, 132, 30, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">1. Personal & Contact Details</h3>
                            <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Operator's legal full name, communication phone and email</p>
                        </div>
                    </div>

                    <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Full Name <span style="color: #EF4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-user" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <input type="text" name="name" id="field-name" class="form-control" placeholder="e.g. Navneet Sharma" value="{{ old('name') }}" required autofocus style="padding-left: 2.5rem; height: 42px; border-radius: 8px;" oninput="updateLivePreview()">
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Enter the employee's official full name</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Email Address <span style="color: #EF4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <i class="fa-regular fa-envelope" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <input type="email" name="email" id="field-email" class="form-control" placeholder="e.g. navneet@vikasudhyog.com" value="{{ old('email') }}" required style="padding-left: 2.5rem; height: 42px; border-radius: 8px;" oninput="updateLivePreview()">
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Used for system notifications &amp; password recovery</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Mobile Number
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-phone" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <input type="text" name="phone" id="field-phone" class="form-control" placeholder="e.g. +91 98290 12345" value="{{ old('phone') }}" style="padding-left: 2.5rem; height: 42px; border-radius: 8px;" oninput="updateLivePreview()">
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Optional mobile contact number</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Login Credentials & Security -->
                <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59, 130, 246, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div>
                                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">2. Login Credentials &amp; Security</h3>
                                <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Unique login handle and authentication password</p>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline" style="font-size: 0.78rem; padding: 0.35rem 0.75rem; border-radius: 6px; color: #2563EB;" onclick="generateRandomPassword()" title="Generate Random Strong Password">
                            <i class="fa-solid fa-dice"></i> Generate Password
                        </button>
                    </div>

                    <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem;">
                                <span>Username <span style="color: #EF4444;">*</span></span>
                                <button type="button" onclick="suggestUsername()" style="background: none; border: none; color: var(--primary); font-size: 0.75rem; cursor: pointer; font-weight: 600; padding: 0;">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-suggest from Name
                                </button>
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-at" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <input type="text" name="username" id="field-username" class="form-control" placeholder="e.g. navneet" value="{{ old('username') }}" required style="padding-left: 2.5rem; height: 42px; border-radius: 8px; font-family: monospace;" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9_.-]/g, ''); updateLivePreview();">
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Must be unique. Allowed characters: lowercase letters, numbers, dot, dash, underscore.</span>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Account Password <span style="color: #EF4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <input type="password" name="password" id="field-password" class="form-control" placeholder="Minimum 6 characters" required style="padding-left: 2.5rem; padding-right: 2.5rem; height: 42px; border-radius: 8px;" oninput="checkPasswordStrength(this.value)">
                                <button type="button" onclick="togglePasswordVisibility('field-password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 5px;" title="Show/Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <div style="margin-top: 0.4rem;">
                                <div style="height: 4px; border-radius: 2px; background: #E2E8F0; overflow: hidden;">
                                    <div id="pwd-strength-bar" style="height: 100%; width: 0%; background: #EF4444; transition: all 0.3s;"></div>
                                </div>
                                <span id="pwd-strength-text" style="font-size: 0.72rem; color: var(--text-muted); margin-top: 0.2rem; display: block;">Password strength</span>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Confirm Password <span style="color: #EF4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-shield-check" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <input type="password" name="password_confirmation" id="field-password-confirm" class="form-control" placeholder="Re-enter password" required style="padding-left: 2.5rem; padding-right: 2.5rem; height: 42px; border-radius: 8px;">
                                <button type="button" onclick="togglePasswordVisibility('field-password-confirm', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 5px;" title="Show/Hide Password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Both passwords must match exactly</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Role & Company Assignment -->
                <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
                    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(139, 92, 246, 0.1); color: #8B5CF6; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div>
                            <h3 style="margin: 0; font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">3. Role &amp; Plant Assignment</h3>
                            <p style="margin: 0.15rem 0 0; font-size: 0.8rem; color: var(--text-muted);">Assign module access permissions and manufacturing plant mapping</p>
                        </div>
                    </div>

                    <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem;">
                        <!-- Role -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                System Role <span style="color: #EF4444;">*</span>
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-user-tag" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <select name="role" id="field-role" class="form-control" required style="padding-left: 2.5rem; height: 42px; border-radius: 8px;" onchange="updateLivePreview()">
                                    @foreach($roles as $roleKey => $roleDesc)
                                        <option value="{{ $roleKey }}" {{ old('role', 'Staff') === $roleKey ? 'selected' : '' }} data-desc="{{ $roleDesc }}">
                                            {{ $roleKey }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="role-description-box" style="margin-top: 0.4rem; font-size: 0.75rem; color: #64748B; background: #F8FAFC; padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid #E2E8F0;">
                                Full data entry and operational privileges.
                            </div>
                        </div>

                        <!-- Company Assignment -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; display: block;">
                                Company / Plant Assignment
                            </label>
                            <div style="position: relative;">
                                <i class="fa-solid fa-building" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.9rem;"></i>
                                <select name="company_id" id="field-company" class="form-control" style="padding-left: 2.5rem; height: 42px; border-radius: 8px;" onchange="updateLivePreview()">
                                    <option value="">-- All Companies / Multi-Plant Global --</option>
                                    @foreach($companies as $comp)
                                        <option value="{{ $comp->id }}" {{ old('company_id') == $comp->id ? 'selected' : '' }}>
                                            {{ $comp->name }} ({{ $comp->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">Leave blank to allow access across all registered company entities</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column -->
            <div class="form-sidebar-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
                <input type="hidden" name="status" value="active">

                <!-- Live Preview Card -->
                <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); text-align: center;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; font-weight: 700; font-size: 0.88rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">
                        Live Card Preview
                    </div>
                    <div style="padding: 1.5rem 1.25rem;">
                        <div id="preview-avatar" style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, var(--primary) 0%, #3D5A14 100%); color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.5rem; margin-bottom: 0.85rem; box-shadow: 0 4px 12px rgba(91,132,30,0.3);">
                            VU
                        </div>
                        <h4 id="preview-name" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">
                            Full Name
                        </h4>
                        <div id="preview-username" style="font-family: monospace; font-size: 0.82rem; color: #64748B; margin: 0.25rem 0 0.75rem;">
                            @username
                        </div>

                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span id="preview-role" style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px; background: #FAF5FF; color: #7E22CE; border: 1px solid #E9D5FF;">
                                Staff
                            </span>
                        </div>

                        <div id="preview-company" style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px dashed var(--border-color); display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                            <i class="fa-solid fa-globe"></i> All Plants / Global
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
                    <button type="submit" class="btn btn-primary" style="height: 44px; font-weight: 700; font-size: 0.95rem; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="fa-solid fa-user-check"></i> Create User Account
                    </button>
                    <a href="{{ route('admin.masters.user') }}" class="btn btn-outline" style="height: 42px; font-weight: 600; font-size: 0.9rem; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        Cancel
                    </a>
                </div>

                <!-- Info Box -->
                <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 1rem; font-size: 0.78rem; color: #64748B;">
                    <div style="font-weight: 700; color: #334155; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                        <i class="fa-solid fa-circle-info" style="color: #3B82F6;"></i> Security Policy
                    </div>
                    <span>Users will use their <strong>Username</strong> or <strong>Email</strong> along with their password to access the ERP portal. Role permissions apply immediately upon account creation.</span>
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
    const statusRadio = document.querySelector('input[name="status"]:checked');
    const status = statusRadio ? statusRadio.value : 'active';

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
        previewCompany.innerHTML = `<i class="fa-solid fa-building" style="color: var(--primary);"></i> ${companyText}`;
    } else {
        previewCompany.innerHTML = `<i class="fa-solid fa-globe"></i> All Plants / Global`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    updateLivePreview();
});
</script>
@endpush
@endsection
