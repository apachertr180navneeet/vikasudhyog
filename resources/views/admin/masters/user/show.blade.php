@extends('admin.layouts.app')

@section('title', $user->name . ' - User Profile - VIKAS UDHYOG ERP')
@section('page_code', 'master-user')

@section('content')
<section class="view-section active" id="view-master-user-show">
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
                <span style="color: var(--primary); font-weight: 600;">{{ $user->name }}</span>
            </div>
            <h1 class="page-title" style="margin: 0; font-size: 1.65rem; font-weight: 800; color: var(--text-primary); display: flex; align-items: center; gap: 0.65rem;">
                <span style="width: 40px; height: 40px; border-radius: 12px; background: rgba(91, 132, 30, 0.12); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                    <i class="fa-solid fa-user-shield"></i>
                </span>
                User Profile
            </h1>
            <p class="page-subtitle" style="margin: 0.25rem 0 0; color: var(--text-muted); font-size: 0.88rem;">
                Comprehensive overview of account status, ERP module access, and plant bindings.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.masters.user.edit', $user) }}" class="btn btn-primary" style="border-radius: 10px; height: 42px; padding: 0 1.25rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-pen-to-square"></i> Edit Profile
            </a>
            <a href="{{ route('admin.masters.user') }}" class="btn btn-outline" style="border-radius: 10px; height: 42px; padding: 0 1.25rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-arrow-left"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success" style="background: #F0FDF4; border: 1px solid #BBF7D0; border-left: 4px solid #16A34A; padding: 0.9rem 1.2rem; border-radius: 10px; color: #166534; font-size: 0.88rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-check" style="font-size: 1.1rem; color: #16A34A;"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #166534; cursor: pointer; font-size: 1.1rem;">&times;</button>
        </div>
    @endif

    <!-- Profile Hero Card -->
    <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); margin-bottom: 1.5rem;">
        <div style="background: linear-gradient(135deg, #2D4010 0%, #5B841E 100%); padding: 2.25rem 2rem; color: #FFFFFF; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px); border: 2px solid rgba(255, 255, 255, 0.3); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 2rem; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);">
                    {{ $user->initials }}
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.6rem; font-weight: 800; color: #FFFFFF; display: flex; align-items: center; gap: 0.6rem;">
                        {{ $user->name }}
                        @if(auth()->id() === $user->id)
                            <span style="font-size: 0.72rem; background: rgba(255, 255, 255, 0.25); color: #FFFFFF; padding: 0.2rem 0.6rem; border-radius: 6px; font-weight: 700;">Active Session</span>
                        @endif
                    </h2>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.4rem; flex-wrap: wrap;">
                        <span style="font-family: monospace; font-size: 0.85rem; background: rgba(255, 255, 255, 0.15); color: #FFFFFF; padding: 0.2rem 0.6rem; border-radius: 6px;">
                            <i class="fa-solid fa-at" style="font-size: 0.75rem; opacity: 0.8;"></i>{{ $user->username }}
                        </span>
                        <span style="font-size: 0.82rem; background: rgba(255, 255, 255, 0.2); color: #FFFFFF; padding: 0.2rem 0.65rem; border-radius: 6px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-shield-halved"></i> {{ $user->role }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Status Form Action -->
            <form action="{{ route('admin.masters.user.toggle-status', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                @if($user->status === 'active')
                    <button type="submit" style="background: rgba(220, 252, 231, 0.95); color: #166534; border: 1px solid #86EFAC; padding: 0.5rem 1.1rem; border-radius: 30px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.2s;" title="Click to Deactivate">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: #16A34A;"></span> Status: Active
                    </button>
                @elseif($user->status === 'suspended')
                    <button type="submit" style="background: rgba(254, 243, 199, 0.95); color: #92400E; border: 1px solid #FCD34D; padding: 0.5rem 1.1rem; border-radius: 30px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.2s;" title="Click to Activate">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: #D97706;"></span> Status: Suspended
                    </button>
                @else
                    <button type="submit" style="background: rgba(254, 226, 226, 0.95); color: #991B1B; border: 1px solid #FCA5A5; padding: 0.5rem 1.1rem; border-radius: 30px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.2s;" title="Click to Activate">
                        <span style="width: 9px; height: 9px; border-radius: 50%; background: #DC2626;"></span> Status: Inactive
                    </button>
                @endif
            </form>
        </div>
    </div>

    <!-- Detailed Attributes Grid -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
        <!-- Left Column: Details -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Contact & Corporate Assignment -->
            <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
                <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; font-weight: 700; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-address-card" style="color: var(--primary);"></i> Contact &amp; Organization Binding
                </div>
                <div style="padding: 1.5rem; display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Official Email</div>
                        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-regular fa-envelope" style="color: var(--primary);"></i>
                            <a href="mailto:{{ $user->email }}" style="color: inherit; text-decoration: none;">{{ $user->email }}</a>
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Mobile Number</div>
                        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-phone" style="color: var(--primary);"></i>
                            <span>{{ $user->phone ?: 'Not provided' }}</span>
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Assigned Plant Entity</div>
                        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-top: 0.35rem;">
                            @if($user->company)
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fa-solid fa-building" style="color: var(--primary);"></i>
                                    <a href="{{ route('admin.masters.company.show', $user->company) }}" style="color: var(--primary); text-decoration: none;">
                                        {{ $user->company->name }} ({{ $user->company->code }})
                                    </a>
                                </div>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.82rem; font-weight: 600; color: #0369A1; background: #F0F9FF; padding: 0.25rem 0.6rem; border-radius: 6px; border: 1px solid #BAE6FD;">
                                    <i class="fa-solid fa-globe"></i> All Plants / Global Multi-Unit
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">ERP Access Level</div>
                        <div style="font-size: 0.95rem; font-weight: 600; color: var(--text-primary); margin-top: 0.35rem;">
                            <span style="font-size: 0.85rem; color: #475569; background: #F1F5F9; padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 700;">
                                {{ $user->role }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allowed Module Capabilities -->
            <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color);">
                <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background: #FCFDFB; font-weight: 700; font-size: 0.95rem; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-cubes" style="color: var(--primary);"></i> Operating Module Scope
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                        <div style="padding: 0.85rem; border-radius: 10px; background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-folder-tree" style="color: var(--primary); font-size: 1.2rem;"></i>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">Masters</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">Company, Items, Accounts</div>
                            </div>
                        </div>

                        <div style="padding: 0.85rem; border-radius: 10px; background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-right-left" style="color: #2563EB; font-size: 1.2rem;"></i>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">Transactions</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">Sales, Purchases, Vouchers</div>
                            </div>
                        </div>

                        <div style="padding: 0.85rem; border-radius: 10px; background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-boxes-stacked" style="color: #D97706; font-size: 1.2rem;"></i>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">Inventory</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">Stock ledger &amp; alerts</div>
                            </div>
                        </div>

                        <div style="padding: 0.85rem; border-radius: 10px; background: #F8FAFC; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-file-contract" style="color: #059669; font-size: 1.2rem;"></i>
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">Reports</div>
                                <div style="font-size: 0.72rem; color: var(--text-muted);">GST &amp; Financial audit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Audit & Actions -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Audit Trail -->
            <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); padding: 1.25rem;">
                <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.85rem; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Security &amp; Activity
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.85rem; color: #64748B;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                        <span>System ID:</span>
                        <strong style="color: var(--text-primary);">#{{ $user->id }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                        <span>Created Date:</span>
                        <strong style="color: var(--text-primary);">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : 'N/A' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                        <span>Last Active:</span>
                        <strong style="color: var(--text-primary);">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never logged in' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Last Login IP:</span>
                        <strong style="color: var(--text-primary); font-family: monospace;">{{ $user->last_login_ip ?: 'None' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Action Quick Card -->
            <div class="card" style="box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04); border-radius: 16px; overflow: hidden; border: 1px solid var(--border-color); padding: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">
                <a href="{{ route('admin.masters.user.edit', $user) }}" class="btn btn-primary" style="height: 42px; font-weight: 700; font-size: 0.9rem; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i class="fa-solid fa-pen-to-square"></i> Edit User Profile
                </a>
                <a href="{{ route('admin.masters.user') }}" class="btn btn-outline" style="height: 42px; font-weight: 600; font-size: 0.9rem; border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    Back to Directory
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
