<!-- Top Navbar -->
<header class="top-navbar">
    <div class="navbar-left">
        <button class="btn-icon" id="sidebar-toggle"><i class="fa-solid fa-bars"></i></button>

        <div class="company-selector">
            <i class="fa-solid fa-building"></i>
            <select id="company-selector-dropdown">
                <option value="Vikas Udhyog">Vikas Udhyog (Sojat)</option>
                <option value="Vikas Herbal Products">Vikas Herbal Products</option>
                <option value="Vikas Trading">Vikas Trading</option>
            </select>
        </div>

        <div class="search-box-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="global-search-input" placeholder="Search anything (Mehndi, Invoices, Customers)..." autocomplete="off">
            <div class="search-results-dropdown" id="global-search-dropdown"></div>
        </div>
    </div>

    <div class="navbar-right">
        <div class="notification-btn" id="notification-btn">
            <button class="btn-icon"><i class="fa-solid fa-bell"></i></button>
            <span class="notification-badge">3</span>

            <div class="notification-drawer" id="notification-drawer">
                <div class="notification-header">
                    <span class="notification-title">Notifications</span>
                    <span style="font-size: 0.75rem; color: var(--primary); cursor: pointer;">Mark all as read</span>
                </div>
                <div class="notification-list">
                    <div class="notification-item">
                        <div class="notification-icon-wrap"><i class="fa-solid fa-triangle-exclamation" style="color: var(--status-warning);"></i></div>
                        <div>
                            <div class="notification-text"><strong>Neem Powder</strong> is below minimum stock level (18 KG).</div>
                            <div class="notification-time">10 mins ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon-wrap"><i class="fa-solid fa-circle-check" style="color: var(--status-success);"></i></div>
                        <div>
                            <div class="notification-text">₹24,500 receipt recorded from <strong>Raj Traders</strong>.</div>
                            <div class="notification-time">1 hour ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon-wrap"><i class="fa-solid fa-truck-fast"></i></div>
                        <div>
                            <div class="notification-text">Order <strong>SO-1024</strong> dispatched via Vikas Logistics.</div>
                            <div class="notification-time">3 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $currentUser = Auth::user();
            $userName = $currentUser->name ?? session('vu_user_name', 'Administrator');
            $userRole = $currentUser->role ?? session('vu_user_role', 'Super Administrator');
            $userEmail = $currentUser->email ?? session('vu_user_email', 'admin@vikasudhyog.com');
            $userInitial = $currentUser ? $currentUser->initials : strtoupper(substr($userName, 0, 1));
        @endphp
        <div class="user-profile-menu" id="user-profile-btn">
            <div class="avatar" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: #fff; font-weight: 700; display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 10px; font-size: 0.9rem;">
                {{ $userInitial }}
            </div>
            <div class="user-info">
                <span class="user-name" style="font-weight: 600;">{{ $userName }}</span>
                <span class="user-role" style="font-size: 0.75rem; color: var(--text-muted);">{{ $userRole }}</span>
            </div>
            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem; color: var(--text-muted);"></i>

            <div class="user-dropdown" id="user-dropdown">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--bg-hover);">
                    <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-primary);">{{ $userName }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">{{ $userEmail }}</div>
                    <span style="display: inline-block; font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; background: rgba(91, 132, 30, 0.12); color: var(--primary);">
                        {{ $userRole }}
                    </span>
                </div>
                <a href="{{ route('admin.settings.company') }}" class="user-dropdown-item"><i class="fa-solid fa-user-gear"></i> Profile & Settings</a>
                <a href="{{ route('admin.masters.access-level') }}" class="user-dropdown-item"><i class="fa-solid fa-shield-halved"></i> Access Levels</a>
                <div class="user-dropdown-divider"></div>
                <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <div class="user-dropdown-item" style="cursor: pointer; color: var(--status-danger);" onclick="sessionStorage.removeItem('vu_logged_in'); document.getElementById('admin-logout-form').submit();">
                    <i class="fa-solid fa-right-from-bracket" style="color: var(--status-danger);"></i> Secure Logout
                </div>
            </div>
        </div>
    </div>
</header>
